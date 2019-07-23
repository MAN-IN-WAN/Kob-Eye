<?php

class Host extends genericClass
{
    var $_isVerified = false;
    var $_KEServer = false;
    var $_KEClient = false;
    var $_KEInfra = false;

    /**
     * Force la vérification avant enregistrement
     * @param    boolean    Enregistrer aussi sur LDAP
     * @return    void
     */
    public function Save($synchro = true)
    {
        $oldsrvs = array();
        if ($this->Id) {
            $old = Sys::getOneData('Parc', 'Host/' . $this->Id);
            $oldsrvs = $this->getKEServer();
        }
        else $old = false;
        //test de modification du ApacheServerName
        if ($old&&$old->Nom!=$this->Nom){
            $this->addError(array("Message"=>"Impossible de modifier le nom de l'hébergement. Si c'est nécessaire, veuillez supprimer et recréer cet hébergement en réimportant vos données."));
            return false;
        }
        if ($old&&!empty($old->NomLDAP)&&$old->NomLDAP!=$this->NomLDAP){
            $this->addError(array("Message"=>"Impossible de modifier le nom technique de l'hébergement. Si c'est nécessaire, veuillez supprimer et recréer cet hébergement en réimportant vos données."));
            return false;
        }
        parent::Save();
        $this->_KEServer = false;
        Sys::$Modules['Parc']->Db->clearLiteCache();
        $srvs = $this->getKEServer();
        // Forcer la vérification
        $this->Verify($synchro);
        if (!$this->_isVerified) {
            $this->addError(array("Message"=>"Impossible de valider l'enregistrement Contactez votre administrateur préféré."));
            return false;
        }
        Sys::$Modules['Parc']->Db->clearLiteCache();
        //Vérification du mot de passe
        if (empty($this->Password)){
            $this->Password = str_shuffle(bin2hex(openssl_random_pseudo_bytes(12)));
        }
        // Enregistrement si pas d'erreur + Récupération GID CLIENT
        if (!$this->_isVerified) return false;

        parent::Save();

        //creation apachedefault
        $aps = Sys::getCount('Parc','Host/'.$this->Id.'/Apache');

        if ($aps<4)
            $this->createDefaultApache();

        //creation defaultftp
        $ftps = Sys::getCount('Parc','Host/'.$this->Id.'/FtpUser');
        if (!$ftps)
            $this->createDefaultFtp();

        //creation default bdd
        $bdds = Sys::getCount('Parc','Host/'.$this->Id.'/Bdd');
        if (!$bdds)
            $this->createDefaultBdd();

        //application des configurations
        $this->createTaskConfigHost();

        //si le mot de passe a été modifié on répercute sur la base de donnée uniquement si le host existait déjà
        if ($this->Password!=$old->Password&&$old){
            $bdd = $this->getOneChild('Bdd');
            if ($bdd){
                $bdd->checkDatabase();
            }
        }

        //vérification des changements serveurs
        if (sizeof($oldsrvs)&&(sizeof($srvs)!=sizeof($oldsrvs)||$srvs[0]->Id!=$oldsrvs[0]->Id)){
            if (sizeof($srvs)>1&&sizeof($srvs)>sizeof($oldsrvs)) {
                $mainsrv = $oldsrvs[0];
                $this->HA = true;
                if ($this->HAMode=='standard')
                    $this->HAMode = 'loadbalancing';
                parent::Save();
                $this->MasterServer = $mainsrv->Id;
            }elseif (sizeof($srvs)==1&&sizeof($srvs)<sizeof($oldsrvs)){
                $mainsrv = array_pop($srvs);
                $this->HA = false;
                if ($this->HAMode=='loadbalancing'||$this->HAMode=='master')
                    $this->HAMode = 'standard';
                $this->MasterServer = $mainsrv->Id;
                parent::Save();
            }
        }
        if ($this->MasterServer<1&&sizeof($srvs)==1){
            $this->MasterServer = $srvs[0]->Id;
            parent::Save();
        }

        return true;
    }

    /**
     * @return bool
     * softSave
     */
    public function softSave(){
        return parent::Save();
    }
    /**
     * getMasterServer
     * Recupere le serveur maître
     */
    public function getMasterServer(){
        if (!$this->MasterServer){
            $srvs = $this->getKEServer();
            $this->MasterServer = $srvs[0]->Id;
        }
        return $this->MasterServer;
    }

    /**
     * moveHostTask
     * Deplace un ehébergement d'un serveur à l'autre
     */
    public function moveHostTask($params=null){
        if (!$params) $params =array('step'=>0);
        if (!isset($params['step'])) $params['step']=0;
        switch($params['step']){
            case 1:
                $srv=Sys::getOneData('Parc','Server/'.$params['selectedServer'],0,1,'','','','',true);
                if (!$srv) return false;
                $task = genericClass::createInstance('Systeme','Tache');
                $task->Type = 'Fonction';
                $task->Nom = 'Déplacement de l\'hébergement ' . $this->Nom.' vers le serveur "'.$srv->Nom.'"';
                $task->TaskModule = 'Parc';
                $task->TaskObject = 'Host';
                $task->TaskId = $this->Id;
                $task->TaskFunction = 'moveHost';
                $task->TaskType = 'install';
                $task->TaskCode = 'HOST_SERVER_MOVE';
                $task->TaskArgs = serialize($params);
                $task->addParent($this);
                $task->Save();
                return array('task'=>$task,'title'=>'Progression du déplacement de l\'hébergement');
                break;
            default:
                return array('template'=>"listSrv",'step'=>1,'callNext'=>array('nom'=>'moveHostTask','title'=>'Progression'));
        }
    }
    /**
     * moveHost
     * Déplace un hébergement
     */
    public function moveHost($task){
        $srv = $this->getMasterServer();
        $task->createActivity('Ajout du serveur ');
        //ajoute le serveur
        $this->addServerHost($task);
        $act = $task->createActivity('Test d\'intégrité');
        $srvs = $this->getKEServer();
        $out=array();
        $cmd = 'du -sh /home/'.$this->NomLDAP;
        foreach ($srvs as $k=>$srvt){
            $act->addDetails($cmd);
            $out[$k] = $srvt->remoteExec($cmd);
        }
        $task->createActivity('Suppression du serveur ID:'.$srv);
        $params = unserialize($task->TaskArgs);
        $params['selectedServer'] = $srv;
        $task->TaskArgs = serialize($params);
        $this->delServerHost($task);
        return true;
    }
    /**
     * addServerHostTask
     * Ajoute un serveur
     * @params $param array()
     */
    public function addServerHostTask($params=null){
        if (!$params) $params =array('step'=>0);
        if (!isset($params['step'])) $params['step']=0;
        switch($params['step']){
            case 1:
                $srv=Sys::getOneData('Parc','Server/'.$params['selectedServer'],0,1,'','','','',true);
                if (!$srv) return false;
                $task = genericClass::createInstance('Systeme','Tache');
                $task->Type = 'Fonction';
                $task->Nom = 'Ajout du serveur "'.$srv->Nom.'" à l\'hébergement ' . $this->Nom;
                $task->TaskModule = 'Parc';
                $task->TaskObject = 'Host';
                $task->TaskId = $this->Id;
                $task->TaskFunction = 'addServerHost';
                $task->TaskType = 'install';
                $task->TaskCode = 'HOST_SERVER_ADD';
                $task->TaskArgs = serialize($params);
                $task->addParent($this);
                $task->Save();
                return array('task'=>$task,'title'=>'Progression de l\'ajout de server à l\'hébergement');
                break;
            default:
                return array('template'=>"listSrv",'step'=>1,'callNext'=>array('nom'=>'addServerHostTask','title'=>'Progression'));
        }
    }
    /**
     * delServerHost
     * Fonction de suppression d'un serveur d'un hébergement
     */
    public function addServerHost($task){
        //on teste les paramètres
        $params = unserialize($task->TaskArgs);
        if (!is_array($params)||!isset($params['selectedServer'])){
            $act = $task->createActivity('Le paramètre selectedServer est introuvable');
            $act->Terminate(false);
            return false;
        }
        $addsrv = Sys::getOneData('Parc','Server/'.$params['selectedServer'],0,1,'','','','',true);
        $act = $task->createActivity('Reconfiguration de l\'hébergement '.$this->Nom.' du serveur '.$addsrv->Nom);
        $this->addParent($addsrv);
        $this->Save();
        $act->Terminate(true);
        foreach ($this->Success as $err){
            $act = $task->createActivity('Success: '.$err['Message']);
            $act->Terminate(true);
        }
        foreach ($this->Warning as $err){
            $act = $task->createActivity('Warning: '.$err['Message']);
        }
        foreach ($this->Error as $err){
            $act = $task->createActivity('Erreur: '.$err['Message']);
            $act->Terminate(false);
        }
        return $this->syncHostSelf($task);
    }
    /**
     * delServerHostTask
     * Supprime u nserveur du mode HA
     * @params $param array()
     */
    public function delServerHostTask($params=null){
        if (!$params) $params =array('step'=>0);
        if (!isset($params['step'])) $params['step']=0;
        switch($params['step']){
            case 1:
                $srv=Sys::getOneData('Parc','Server/'.$params['selectedServer'],0,1,'','','','',true);
                if (!$srv) return false;
                $task = genericClass::createInstance('Systeme','Tache');
                $task->Type = 'Fonction';
                $task->Nom = 'Suppression du serveur "'.$srv->Nom.'"" de l\'hébergement ' . $this->Nom;
                $task->TaskModule = 'Parc';
                $task->TaskObject = 'Host';
                $task->TaskId = $this->Id;
                $task->TaskFunction = 'delServerHost';
                $task->TaskType = 'install';
                $task->TaskCode = 'HOST_SERVER_DEL';
                $task->TaskArgs = serialize($params);
                $task->addParent($this);
                $task->Save();
                return array('task'=>$task,'title'=>'Progression de la suppression du serveur à l\'hébergement');
                break;
            default:
                return array('template'=>"listSrv",'step'=>1,'callNext'=>array('nom'=>'delServerHostTask','title'=>'Progression'));
        }
    }
    /**
     * delServerHost
     * Fonction de suppression d'un serveur d'un hébergement
     */
    public function delServerHost($task){
        //on teste les paramètres
        $params = unserialize($task->TaskArgs);
        if (!is_array($params)||!isset($params['selectedServer'])){
            $act = $task->createActivity('Le paramètre selectedServer est introuvable');
            $act->Terminate(false);
            return false;
        }
        $delsrv = Sys::getOneData('Parc','Server/'.$params['selectedServer'],0,1,'','','','',true);
        $act = $task->createActivity('Reconfiguration de l\'hébergement '.$this->Nom.' du serveur '.$delsrv->Nom);
        $this->delParent($delsrv);
        $this->Save();
        $act->Terminate(true);

        $act = $task->createActivity('Suppression de l\'hébergement '.$this->Nom.' du serveur '.$delsrv->Nom);
        $this->deleteFromServer($delsrv,$act);
        $act->Terminate(true);

        //suppression des configuration apache
        $aps = $this->getChildren('Apache');
        $act = $task->createActivity('Suppression des hôtes virtuels de l\'hébergement '.$this->Nom.' du serveur '.$delsrv->Nom);
        foreach ($aps as $ap){
            if ($ap->deleteFromServer($delsrv)) {
                $act->addDetails('-> Suppression de de l\'hôte virtuel '.$ap->getFirstSearchOrder().' OK');
            }else  $act->addDetails('-> Suppression de de l\'hôte virtuel '.$ap->getFirstSearchOrder().' NOK');
        }
        $act->Terminate(true);


        $act = $task->createActivity('Suppression de l\'hébergement '.$this->Nom.' du serveur '.$delsrv->Nom.' terminé');
        $act->Terminate(true);

        //on relance une configuration serveur
        $this->createTaskConfigHost();
        return true;
    }
    /**
     * syncHostTask
     * Synchroniser les hébergements
     * @params Task
     */
    public function syncHostTask(){
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Synchronisation des occurences de l \'hébergement ' . $this->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Host';
        $task->TaskId = $this->Id;
        $task->TaskFunction = 'syncHostSelf';
        $task->TaskType = 'installation';
        $task->TaskCode = 'HOST_SYNC';
        $task->addParent($this);
        $srvs = $this->getKEServer();
        foreach ($srvs as $srv){
            $task->addParent($srv);
        }
        $task->Save();
        return array('task' => $task);
    }
    /**
     * syncHostSelf
     * Syncrhoniser les occurence d'un meme host
     */
    public function syncHostSelf($task) {
        $srvs = $this->getKEServer();
        //recherche du erveur prioncipal
        $mainsrv=false;
        foreach ($srvs as $k=>$srv){
            if ($srv->Id==$this->getMasterServer()){
                $mainsrv = $srv;
                break;
            }
        }
        if (!$mainsrv)
            $mainsrv = array_pop($srvs);
        $host = $this;
        try {
            foreach ($srvs as $dstsrv) {
                if ($dstsrv->Id!=$mainsrv->Id) {
                    //Installation des fichiers
                    $act = $task->createActivity('Initialisation de la synchronisation sur le serveur ' . $dstsrv->Nom, 'Info');
                    $cmd = 'rsync -e "ssh -o StrictHostKeyChecking=no" -avz root@' . $mainsrv->InternalIP . ':/home/' . $host->NomLDAP . ' /home/' ;
                    $act->addDetails($cmd);
                    $out = $dstsrv->remoteExec($cmd);
                    $act->addDetails($out);
                    $act->Terminate(true);
                    $act = $task->createActivity('Modification des droit ssur le serveur ' . $dstsrv->Nom, 'Info');
                    $cmd = 'chown ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . '/ -R';
                    $act->addDetails($cmd);
                    $out = $dstsrv->remoteExec($cmd);
                    $act->addDetails($out);
                    $act->Terminate(true);
                }
            }
        }catch (Exception $e){
            $act->addDetails('Erreur: '.$e->getMessage());
            $act->Terminate(false);
            throw new Exception($e->getMessage());
        }
        $act = $task->createActivity('Synchronisation terminée.', 'Info');
        $act->Terminate(true);
        return true;
    }
    /**
     * createTaskConfigHost
     * Creation de la tache pour configurer l'host
     */
    private function createTaskConfigHost(){
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Configuration de l\'hébergement ' . $this->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Host';
        $task->TaskId = $this->Id;
        $task->TaskFunction = 'configHost';
        $task->TaskType = 'install';
        $task->TaskCode = 'HOST_CONFIG';
        $task->addParent($this);
        $inst = $this->getOneChild('Instance');
        if ($inst)
            $task->addParent($inst);
        $task->addParent($this->getOneParent('Server'));
        $task->Save();
        return array('task' => $task);
    }
    /**
     * configHost
     * Configuration supplémentairte de l'hébergemen t
     */
    public function configHost($task) {
        //execution du ldap2service
        $servs = $this->getKEServer();
        foreach ($servs as $serv) {
            $act = $task->createActivity('Configuration de l\'hébergement '.$this->Nom.' sur le serveur '.$serv->Nom);
            $serv->callLdap2Service();
            //création du fichier .bashrc
            $f = '# .bashrc

# Source global definitions
if [ -f /etc/bashrc ]; then
        . /etc/bashrc
fi

PS1=\'\[\033[32m\]\u\[\e[1;33m\] PROD :\[\033[34m\]\w\[\033[31m\]$(__git_ps1)\[\033[00m\]\$ \'

source ~/.bash_git
# User specific aliases and functions
alias php="/usr/local/php-'.$this->PHPVersion.'/bin/php"
export PATH=/usr/local/php-'.$this->PHPVersion.'/bin:$PATH
';
            $serv->putFileContent('/home/'.$this->NomLDAP.'/.bashrc',$f);
            $serv->remoteExec('chown ' . $this->NomLDAP . ':users /home/' . $this->NomLDAP . '/.bashrc');
            $act->Terminate(true);

        }
        //affectation des clefs ssh
        $this->sshKeysCheck();

        //configuration des clefs
        $this->refreshSshKeys();

        //on force le renouvellement des hôtes virtuels
        $aps = $this->getChildren('Apache');
        foreach ($aps as $ap){
            $act = $task->createActivity('Enregistrement forcé de l\'hôte virtuel :'.$ap->ApacheServerName.' et domaines '.$ap->ApacheServerAlias);
            $act->Terminate($ap->Save());
        }
        $pxs = Sys::getData('Parc','Server/Proxy=1',0,100,'','','','',true);
        foreach ($pxs as $px){
            $act = $task->createActivity('Appel des configuration proxys '.$px->Nom);
            $px->callLdap2Service();
            $act->Terminate();
        }
        return true;
    }
    /**
     * sshKeyCheck
     * Affectation automatique des clefs ssh
     */
    private function sshKeysCheck() {
        //clef technicien
        $techs = Sys::getData('Parc','SshKeys/Type=technicien');
        foreach ($techs as $tech){
            $tech->addParent($this);
            $tech->Save();
        }
        //clef revendeur
        $cli = $this->getKEClient();
        if ($rev = $cli->getRevendeur()){
            $keys = Sys::getData('Parc','Revendeur/'.$rev->Id.'/SshKeys/Type=revendeur');
            foreach ($keys as $key){
                $key->addParent($this);
                $key->Save();
            }
        }
        //clef client
        $keys = Sys::getData('Parc','Client/'.$cli->Id.'/SshKeys/Type=client');
        foreach ($keys as $key){
            $key->addParent($this);
            $key->Save();
        }
    }
    /**
     * refreshSshKeys
     * regénère les clefs ssh de l'hébergement
     */
    public function refreshSshKeys(){
        //generation du fichier authorized_keys à pousser sur l'hébergement du client.
        $keys = $this->getChildren('SshKeys');
        $f = '';
        foreach ($keys as $key){
            $f.= $key->Clef."\n";
        }
        $servs = $this->getKEServer();
        foreach ($servs as $serv){
            $cmd = 'if [ ! -d /home/' . $this->NomLDAP . '/.ssh ]; then mkdir /home/' . $this->NomLDAP . '/.ssh; fi';
            $serv->remoteExec($cmd);
            $serv->putFileContent('/home/'.$this->NomLDAP.'/.ssh/authorized_keys',$f);
            $serv->remoteExec('chown ' . $this->NomLDAP . ':users /home/' . $this->NomLDAP . '/.ssh -R');
            $serv->remoteExec('chmod 700 /home/' . $this->NomLDAP . '/.ssh -R');
        }
        return true;
    }
    /*****************************
     * INIT
     ****************************/
    /**
     * createDefaultApache
     * Crée une configuratio apache par défaut avec u sous domaine
     */
    public function createDefaultApache($force_domain = '') {
        //on vérifie l'existence
        $dom = Sys::getOneData('Parc', 'Domain/defaultDomain=1', 0, 1, '', '', '', '', true);
//        for ($i=0;$i<4;$i++){
            $apache = genericClass::createInstance('Parc','Apache');
            $ssl = false;//($i % 2 == 0) ? true : false;
            $proxycache = false;//($i<2)? true : false;
            $apache->Ssl = $ssl;
            $apache->ProxyCache = $proxycache;

            $pref = $ssl ? ( $proxycache ? 'ssl-cache-' : 'ssl-') : ( $proxycache ? 'cache-' : '' );

            if (empty($force_domain))
                $domain = $this->NomLDAP;
            else $domain = $force_domain;

            //test existence
            $exists = Sys::getCount('Parc','Host/'.$this->Id.'/Apache/apacheServerName='.$pref.$this->NomLDAP.'.'.$dom->Url);
            //if ($exists) continue;
            if ($exists) return false;

            $apache->ApacheServerName = $pref.$this->NomLDAP.'.'.$dom->Url;
            $apache->DocumentRoot = 'www';
            $apache->Actif = true;
            $apache->addParent($this);
            $apache->Save();
//        }
        return true;
    }
    /**
     * createDefaultFtp
     * Crée une configuratio apache par défaut avec u sous domaine
     */
    public function createDefaultFtp() {
        //check ftp
        $ftp = $this->getOneChild('Ftpuser');
        if (!$ftp) {
            //alors création du apache
            $ftp = genericClass::createInstance('Parc', 'Ftpuser');
            $ftp->Identifiant = 'admin@'.$this->NomLDAP;
            $ftp->Password = $this->Password;
            $ftp->addParent($this);
            $ftp->Save();
        } else {
            $ftp->addParent($this);
            $ftp->Save();
        }
        return $ftp;
    }
    /**
     * createDefaultBdd
     * Crée la base de donnée par défaut
     */
    public function createDefaultBdd() {
        $bdd = $this->getOneChild('Bdd');
        if (!$bdd) {
            //alors création du apache
            $bdd = genericClass::createInstance('Parc', 'Bdd');
            $bdd->Nom = $this->NomLDAP;
            $bdd->addParent($this);
            $bdd->Save();
        } else {
            $bdd->addParent($this);
            $bdd->Save();
        }
        $this->addParent($bdd);
        return $bdd;
    }
    /*******************************
     * LDAP
     ******************************/
    /**
     * getLdapID
     * récupère le ldapId d'une entrée pour un serveur spécifique
     */
    public function getLdapID($KEServer) {
        if (!empty($this->LdapID)) {
            $en = json_decode($this->LdapID, true);
            if (!is_array($en))
                $en = array($KEServer->Id => $this->LdapID);
        }else $en=array();
        return $en[$KEServer->Id];
    }
    /**
     * setLdapID
     * défniit le ldapId d'une entrée pour un serveur spécifique
     */
    public function setLdapID($KEServer,$ldapId) {
        if (!empty($this->LdapID)) {
            $en = json_decode($this->LdapID, true);
            if (!is_array($en))
                $en = array($KEServer->Id => $this->LdapID);
        }else $en = Array();
        if (!is_array($en))$en = array();
        $en[$KEServer->Id] = $ldapId;
        $this->LdapID = json_encode($en);
    }
    /**
     * getLdapDN
     * récupère le ldapDN d'une entrée pour un serveur spécifique
     */
    public function getLdapDN($KEServer) {
        if (!empty($this->LdapDN)) {
            $en = json_decode($this->LdapDN, true);
            if (!is_array($en))
                $en = array($KEServer->Id => $this->LdapDN);
        }else $en=array();
        return $en[$KEServer->Id];
    }
    /**
     * setLdapDN
     * définit le ldapDN d'une entrée pour un serveur spécifique
     */
    public function setLdapDN($KEServer,$ldapDn) {
        if (!empty($this->LdapDN)) {
            $en = json_decode($this->LdapDN, true);
            if (!is_array($en))
                $en = array($KEServer->Id => $this->LdapDN);
        } else $en = Array();
        if (!is_array($en))$en = array();
        $en[$KEServer->Id] = $ldapDn;
        $this->LdapDN = json_encode($en);
    }
    /**
     * getLdapTms
     * récupère le ldapTms d'une entrée pour un serveur spécifique
     */
    public function getLdapTms($KEServer) {
        if (!empty($this->LdapTms)) {
            $en = json_decode($this->LdapTms, true);
            if (!is_array($en))
                $en = array($KEServer->Id => $this->LdapTms);
        }else $en=array();
        return $en[$KEServer->Id];
    }
    /**
     * setLdapTms
     * définit le ldapTms d'une entrée pour un serveur spécifique
     */
    public function setLdapTms($KEServer,$ldapTms) {
        if (!empty($this->LdapTms)) {
            $en = json_decode($this->LdapTms, true);
            if (!is_array($en))
                $en = array($KEServer->Id => $this->LdapTms);
        }else $en = Array();
        if (!is_array($en))$en = array();
        $en[$KEServer->Id] = $ldapTms;
        $this->LdapTms = json_encode($en);
    }
    /**
     * getLdapUid
     * récupère le ldapUid d'une entrée pour un serveur spécifique
     */
    public function getLdapUid($KEServer)
    {

        if (!empty($this->LdapUid)){
            $en = json_decode($this->LdapUid, true);
            if (!is_array($en))
                $en = array($KEServer->Id => $this->LdapUid);
        }else $en=array();
        if (!isset($en[$KEServer->Id])){
            $en[$KEServer->Id] = Server::getNextUid();
            $this->setLdapUid($KEServer,$en[$KEServer->Id]);
        }
        return $en[$KEServer->Id];
    }
    /**
     * setLdapUid
     * définit le ldapUid d'une entrée pour un serveur spécifique
     */
    public function setLdapUid($KEServer,$ldapUid) {
        if (!empty($this->LdapUid)){
            $en = json_decode($this->LdapUid, true);
            if (!is_array($en))
                $en = array($KEServer->Id => $this->LdapUid);
        }else $en = Array();
        if (!is_array($en))$en = array();
        $en[$KEServer->Id] = $ldapUid;
        $this->LdapUid = json_encode($en);
    }

    /**
     * Verification des erreurs possibles
     * @param    boolean    Verifie aussi sur LDAP
     * @return    Verification OK ou NON
     */
    public function Verify($synchro = false)
    {
        //test du nom
        if (empty($this->NomLDAP)) {
            $this->NomLDAP = Utils::CheckSyntaxe($this->Nom);
        }
        $this->NomLDAP = strtolower($this->NomLDAP);
        $this->NomLDAP = Utils::CheckSyntaxe($this->NomLDAP);
        $this->NomLDAP = substr($this->NomLDAP,0,32);
        $old = Sys::getOneData('Parc','Host/'.$this->Id);
        //test de modification du ApacheServerName
        if ($this->Id&&$old->Nom!=$this->Nom){
            $this->addError(array("Message"=>"Impossible de modifier le nom de l'hébergement. Si c'est nécessaire, veuillez supprimer et recréer cet hébergement en réimportant vos données."));
            return false;
        }
        if ($this->Id&&!empty($old->NomLDAP)&&$old->NomLDAP!=$this->NomLDAP){
            $this->addError(array("Message"=>"Impossible de modifier le nom technique de l'hébergement. Si c'est nécessaire, veuillez supprimer et recréer cet hébergement en réimportant vos données."));
            return false;
        }
        if (strlen($this->Nom)>50||strlen($this->Nom)<2){
            $this->addError(array("Prop"=>"Nom","Message"=>"Le nom doit comporter de 2 à 50 caractères"));
            return false;
        }
        if (parent::Verify()) {
            //Verification du client
            if (!$this->getKEClient()){
                $this->addError(array("Prop"=>"Nom","Message"=>"Client introuvable."));
                return true;
            }
            //Verification des server
            if (!$this->getKEServer()){

                //Gestion des infra si existante
                $infra = $this->getInfra();
                $pref = '';
                if($infra)
                    $pref='Infra/'.$infra->Id.'/';

                //si pas de serveur alors on affecte le serveur Web par défaut
                $defserv = Sys::getOneData('Parc',$pref.'Server/defaultWebServer=1');
                if (!$defserv){
                    $this->addError(array('Message'=>'Aucun serveur Web par défaut n\'est définie. Veuillez contacter votre administrateur.'));
                    return false;
                }
                $this->addParent($defserv);
            }

            $this->_isVerified = true;

            if ($synchro) {

                // On boucle sur tous les serveurs
                $KEServers = $this->getKEServer();
                foreach ($KEServers as $KEServer) {
                    $this->addSuccess(array('Message'=>'Enregistrement de la configuration du serveur '.$KEServer->Nom));
                    $dn = 'cn=' . $this->NomLDAP . ',ou=' . $KEServer->LDAPNom . ',ou=servers,' . PARC_LDAP_BASE;
                    $base = 'ou=' . $KEServer->LDAPNom . ',ou=servers,' . PARC_LDAP_BASE;
                    $filter = 'cn=' . $this->NomLDAP;
                    // Verification à jour
                    $res = Server::checkTms($this,$KEServer,$base,$filter);
                    $this->addSuccess(array('Message'=>'Test existence ldap du serveur '.$KEServer->Nom.' '.print_r($res,true)));
                    if ($res['exists']) {
                        if (!$res['OK']) {
                            $this->AddError($res);
                            $this->_isVerified = false;
                        } else {
                            // Déplacement
                            if ($this->getLdapDN($KEServer) != 'cn=' . $this->NomLDAP . ',ou=' . $KEServer->LDAPNom . ',ou=servers,' . PARC_LDAP_BASE)
                                $res = Server::ldapRename($this->getLdapDN($KEServer), 'cn=' . $this->NomLDAP, 'ou=' . $KEServer->LDAPNom . ',ou=servers,' . PARC_LDAP_BASE);
                            else $res = array('OK' => true);
                            if ($res['OK']) {
                                // Modification
                                $entry = $this->buildEntry($KEServer,false);
                                $this->addSuccess(array('Message'=>'Modificatio netréée ldap '.$dn.' '.print_r($entry,true)));
                                $res = Server::ldapModify($this->getLdapID($KEServer), $entry);
                                if ($res['OK']) {
                                    // Tout s'est passé correctement
                                    $this->setLdapDN($KEServer,$dn);
                                    $this->setLdapTms($KEServer,$res['LdapTms']);
                                } else {
                                    // Erreur
                                    $this->AddError($res);
                                    $this->_isVerified = false;
                                    // Rollback du déplacement
                                    $tab = explode(',', $this->getLdapDN($KEServer));
                                    $leaf = array_shift($tab);
                                    $rest = implode(',', $tab);
                                    Server::ldapRename($dn, $leaf, $rest);
                                }
                            } else {
                                $this->AddError($res);
                                $this->_isVerified = false;
                            }
                        }

                    } else {
                        ////////// Nouvel élément
                        if ($KEServer) {
                            $entry = $this->buildEntry($KEServer);
                            $this->addSuccess(array('Message'=>'Ajout entrée ldap '.$dn.' '.print_r($entry,true)));
                            $res = Server::ldapAdd($dn, $entry);
                            if ($res['OK']) {
                                $res2 = Server::ldapAdd('ou=users,' . $dn, array('objectclass' => array('organizationalUnit', 'top'), 'ou' => 'users'));
                                $this->setLdapDN($KEServer,$dn);
                                $this->setLdapUid($KEServer,$entry['uidnumber']);
                                $this->LdapGid = $entry['gidnumber'];
                                $this->setLdapID($KEServer,$res['LdapID']);
                                $this->setLdapTms($KEServer,$res2['LdapTms']);
                            } else {
                                $this->AddError($res);
                                $this->_isVerified = false;
                            }
                        } else {
                            $this->AddError(array('Message' => "Un hébergement doit obligatoirement être créé dans un serveur donné.", 'Prop' => ''));
                            $this->_isVerified = false;
                        }
                    }
                }
            }

        } else {

            $this->_isVerified = false;

        }

        return $this->_isVerified;

    }

    /**
     * Configuration d'une nouvelle entrée type
     * Utilisé lors du test dans Verify
     * puis lors du vrai ajout dans Save
     * @param    boolean        Si FALSE c'est simplement une mise à jour
     * @return    Array
     */
    private function buildEntry($KEServer,$new = true)
    {
        $entry = array();
        $entry['cn'] = $this->NomLDAP;
        $entry['givenname'] = $this->Nom;
        $entry['homedirectory'] = '/home/' . $this->NomLDAP;
        $entry['sn'] = $this->NomLDAP;
        $entry['uid'] = $this->NomLDAP;
        $entry['description'] = json_encode(array("Quota" => $this->Quota));
        $entry['preferredLanguage'] = $this->PHPVersion;
        if ($new) {
            $entry['uidnumber'] = $this->getLdapUid($KEServer);
            $entry['gidnumber'] = "100";//$this->_KEClient->LdapGid;
            $entry['loginshell'] = '/bin/bash';
            $entry['objectclass'][0] = 'inetOrgPerson';
            $entry['objectclass'][1] = 'posixAccount';
            $entry['objectclass'][2] = 'shadowAccount';
            $entry['objectclass'][3] = 'top';
        }
        $entry['userpassword'] = "{MD5}".base64_encode(pack("H*",md5($this->Password)));
        return $entry;
    }

    /**
     * Récupère le Gid du Client Parent s'il existe
     * @param    boolean        Synchroniser aussi sur LDAP
     * @return    void
     */
    private function getGidFromClient($synchro = true)
    {
        $tab = $this->getParents('Client');
        if (!empty($tab)) {
            $this->LdapGid = $tab[0]->LdapGid;
            if ($synchro) {
                $entry = array('gidnumber' => $this->LdapGid);
                $KEServer = $this->getKEServer();
                Server::ldapModify($this->LdapID, $entry);
            }
        }
    }


    /**
     * Suppression de la BDD
     * Relai de cette suppression à LDAP
     * On utilise aussi la fonction de la superclasse
     * @return    void
     */
    public function Delete($task = null){
        if(!$task){
            //creatio nde la tache
            $task = genericClass::createInstance('Systeme', 'Tache');
            $task->Type = 'Manuel';
            $task->Nom = 'Suppression de l\'instance '.$this->Nom;
            $task->TaskModule = 'Parc';
            $task->TaskObject = 'Instance';
            $task->TaskType = 'update';
            $task->TaskCode = 'INSTANCE_DELETE';
            $task->Demarre = true;
            $task->TaskFunction = '';
            $task->Save();

        }
        $act = $task->createActivity('Suppression de l\'hébergement '.$this->getFirstSearchOrder());
        //suppression des apaches
        $aps = $this->getChildren('Apache');
        foreach ($aps as $ap){
            if ($ap->Delete()) {
                $act->addDetails('-> Suppression de de l\'hôte virtuel '.$ap->getFirstSearchOrder().' OK');
            }else  $act->addDetails('-> Suppression de de l\'hôte virtuel '.$ap->getFirstSearchOrder().' NOK');
        }
        //suppression des ftp
        $ftps = $this->getChildren('Ftpuser');
        foreach ($ftps as $ftp){
            if ($ftp->Delete()) {
                $act->addDetails('-> Suppression de de l\'utilisateur ftp '.$ftp->getFirstSearchOrder().' OK');
            }else  $act->addDetails('-> Suppression de de l\'utilisateur ftp '.$ftp->getFirstSearchOrder().' NOK');
        }
        //suppression des apaches
        $bdds = $this->getChildren('Bdd');
        foreach ($bdds as $bdd){
            if ($bdd->Delete()) {
                $act->addDetails('-> Suppression de la base de donnée '.$bdd->getFirstSearchOrder().' OK');
            }else  $act->addDetails('-> Suppression de la base de donnée '.$bdd->getFirstSearchOrder().' NOK');
        }
        //suppression ldap
        $KEServers = $this->getKEServer();
        foreach ($KEServers as $KEServer) {
            $this->deleteFromServer($KEServer,$act);
        }
        parent::Delete();
        $act->addDetails('Suppression terminée avec succès');
        $act->Terminate(true);
        $task->Termine = true;
        $task->Save();
        return true;
    }
    /**
     * deleteFromServer
     * @params Server
     */
    private function deleteFromServer($KEServer,$act){
        try {
            if (!empty($this->NomLDAP)) {
                if ($KEServer->folderExists('/home/'.$this->NomLDAP)) {
                    $cmd = 'for file in $(ls /home/' . $this->NomLDAP . '/); do mountpoint -q /home/' . $this->NomLDAP . '/$file && umount /home/' . $this->NomLDAP . '/$file; done';
                    $act->addDetails($cmd);
                    try{
                        $KEServer->remoteExec($cmd);
                    }catch (Exception $e){}
                    //$KEServer->remoteExec('rm -Rf /home/'.$this->NomLDAP);
                    $KEServer->remoteExec('mv /home/'.$this->NomLDAP.' /home/ws/'.$this->NomLDAP);
                    $act->addDetails('-> Suppression des fichiers  OK');
                }else $act->addDetails('-> Le dossier '."/home/".$this->NomLDAP.' n\'existe pas.'.$KEServer->folderExists('/home/'.$this->NomLDAP));
            }else $act->addDetails('-> Suppression des fichiers  NOK, pas de nom LDAP');
        } catch (Exception $e) {
            $act->addDetails('-> Suppression des fichiers  NOK. Détails: '.$e->getMessage());
            $act->Terminate(false);
            $this->addError(Array("Message" => "Impossible d'effectuer la commande de suppression sur le serveur"));
            return false;
        }
        //suppression définitive
        if ($this->getLdapID($KEServer)) Server::ldapDelete($this->getLdapID($KEServer), true);
        else $act->addDetails('-> Suppression des entrées LDAP NOK');
        return true;
    }


    /**
     * Récupère une référence vers l'objet KE "Server"
     * pour effectuer des requetes LDAP
     * On conserve une référence vers le serveur
     * pour le cas d'une utilisation ultérieure
     * @return    L'objet Kob-Eye
     */
    public function getKEServer()
    {
        if(empty($this->Id)){
            $pars = array();
            foreach ($this->Parents as $p){
                if($p['Titre'] == 'Server'){
                    $pa = Sys::getOneData('Parc','Server/'.$p['Id'],0,1,null,null,null,null,true);
                    $pars[] = $pa;
                }

            }
            $this->_KEServer = $pars;
        }
        if (!is_array($this->_KEServer)) {
            //$tab = $this->getParents('Server');
            $tab = Sys::getData('Parc','Server/Host/'.$this->Id,0,100,null,null,null,null,true);
            if (empty($tab)) return false;
            else $this->_KEServer = $tab;
        }
        return $this->_KEServer;
    }

    /**
     * Récupère une référence vers l'objet KE "Client"
     * pour effectuer des requetes LDAP
     * On conserve une référence vers le client
     * pour le cas d'une utilisation ultérieure
     * @return    L'objet Kob-Eye
     */
    public function getKEClient(){
        foreach ($this->Parents as $p){
            if($p['Titre'] == 'Client'){
                $this->_KEClient = Sys::getOneData('Parc','Client/'.$p['Id'],0,1,null,null,null,null,true);
            }
        }
        if (!is_object($this->_KEClient)) {
            $tab = $this->getParents('Client');
            if (empty($tab)) return false;
            else $this->_KEClient = $tab[0];
        }
        return $this->_KEClient;
    }

    /**
     * Récupère une référence vers l'objet KE "Infra"
     * pour effectuer des requetes LDAP
     * On conserve une référence vers le serveur
     * pour le cas d'une utilisation ultérieure
     * @return	L'objet Kob-Eye
     */
    public function getInfra() {
        if(!is_object($this->_KEInfra)) {
            $this->_KEInfra = $this->getOneParent('Infra');
            if(!is_object($this->_KEInfra)) {
                //si definit dans l'instance
                if($inst = $this->getOneChild('Instance')){
                    $this->_KEInfra = $inst->getInfra();
                    $this->addParent($this->_KEInfra);
                }else{
                    $infra = Sys::getOneData('Parc','Infra/Default=1');
                    $this->_KEInfra = $infra;
                    if (!is_object($infra)){
                        $this->addError(array('Message'=> 'Aucune infrastructure n\'est définie par défaut. Veuillez contacter votre administrateur'));
                    }
                }
            }
        }
        return $this->_KEInfra;
    }

    /**
     * Retrouve les parents lors d'une synchronisation
     * @return    void
     */
    public function findParents()
    {
        $Parts = explode(',', $this->LdapDN);
        foreach ($Parts as $i => $P) $Parts[$i] = explode('=', $P);
        // Parent Client
        $Tab = Sys::$Modules["Parc"]->callData("Parc/Client/NomLDAP=" . $Parts[0][1], "", 0, 1);
        if (!empty($Tab)) {
            $obj = genericClass::createInstance('Parc', $Tab[0]);
            $this->AddParent($obj);
        }
        // Parent Server
        $Tab = Sys::$Modules["Parc"]->callData("Parc/Server/LDAPNom=" . $Parts[1][1], "", 0, 1);
        if (!empty($Tab)) {
            $obj = genericClass::createInstance('Parc', $Tab[0]);
            $this->AddParent($obj);
        }
    }

    public function Terminal()
    {
        /**
         * The command comes from ajax-post.
         */
        if(isset($_POST['stdin'])){
            Terminal::postCommand($_POST['stdin']);
            exit;
        }

        /**
         * The authentication.
         * You can change this method.
         * I've used Auth Basic as example.
         */
        /*if (!isset($_SERVER['PHP_AUTH_USER']) ||
            !Terminal::autenticate($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'])) {
            header('WWW-Authenticate: Basic realm="xwiterm (use your linux login)"');
            header('HTTP/1.0 401 Unauthorized');
            echo "Authentication failure :)";
            exit;
        }*/

//        return Terminal::run($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
        return Terminal::run('enguer', '21wyisey');
    }

    /**
     * createBackupTask
     * Creation de la tache de backup
     */
    public function createBackupTask($orig=null){
        $nbt = Sys::getCount('Parc','Host/'.$this->Id.'/Tache/TaskCode=BACKUP_CREATE&Termine=0&Erreur=0');
        if ($nbt){
            $this->addError(array('Message'=>'Une tache de sauvegarde est déjà en cours.'));
            return false;
        }
        if (!$this->BackupEnabled) return false;
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Sauvegarde de l\'hébergement ' . $this->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Host';
        $task->TaskId = $this->Id;
        $task->TaskFunction = 'backup';
        $task->TaskType = 'maintenance';
        $task->TaskCode = 'BACKUP_CREATE';
        $task->addParent($this);
        $inst = $this->getOneChild('Instance');
        if ($inst)
            $task->addParent($inst);
        $task->addParent($this->getOneParent('Server'));
        if (is_object($orig)) $task->addParent($orig);
        $task->Save();
        return array('task' => $task);
    }
    /**
     * backup
     * Fonction de sauvegarde
     * @param Object Tache
     * @throws Exception
     *
     * @return Boolean
     */
    public function backup($task ){
        $host = $this;
        $inst = $host->getOneChild('Instance');
        $restopoint = date('YmdHis');
        $restodate = date('d/m/Y à H:i:s');
        $task->DateDebut = time();
        $task->Save();
        //création du point de restauration
        $rp = genericClass::createInstance('Parc','RestorePoint');
        $rp->Titre = 'Sauvegarde date: '.$restodate;
        $rp->Identifiant = $restopoint;
        $rp->addParent($host);
        if ($inst)
            $rp->addParent($inst);
        $rp->Save();
        try {
            $result = $rp->backup($task);
            //reinitialisation des incidents de backup
            $incidents = Sys::getData('Parc','Host/'.$this->Id.'/Incident/Code=BACKUP_ERROR');
            foreach ($incidents as $incident){
                $incident->Solved = true;
                $incident->Save();
            }
            return $result;
        }catch (Exception $e){
            //création d'un incident
            $incident = genericClass::createInstance('Parc','Incident');
            $incident->Titre = 'Erreur sur la sauvegarde de l\'hébergement '.$host->Nom;
            $incident->Code = 'BACKUP_ERROR';
            $incident->Severity = 'Warning';
            $incident->addParent($this);
            if ($inst)
                $incident->addParent($inst);
            $incident->Details = $e->getMessage();
            $incident->Save();
            return false;
        }
    }

    /**
     * cloneHost
     * Fonction de creation de la tache de clonage
     * @param Array params
     *
     * @return Mixed
     */
    public function cloneHost($params = null){
        if (!$params) $params =array('step'=>0);
        if (!isset($params['step'])) $params['step']=0;
        switch($params['step']){
            case 1:
                $task = genericClass::createInstance('Systeme','Tache');
                $task->Type = 'Fonction';
                $task->Nom = 'Clonage de l\'hébergement ' . $this->Nom.' vers l\'hébergement '. $params['targetHost'];
                $task->TaskModule = 'Parc';
                $task->TaskObject = 'Host';
                $task->TaskId = $this->Id;
                $task->TaskFunction = 'exeClone';
                $task->TaskType = 'install';
                $task->TaskCode = 'HOST_CLONE';
                $task->TaskArgs = serialize($params);
                $task->addParent($this);
                $task->Save();
                return array('task'=>$task,'title'=>'Progression du clonage');
                break;
            default:
                return array('template'=>"Clone",'step'=>1,'callNext'=>array('nom'=>'cloneHost','title'=>'Progression'));
        }

    }

    /**
     * clone
     * Fonction de clonage d'hébergement
     * @param task Task Object
     */
    public function exeClone($task){
        //desérialisation des paramètres
        $params = unserialize($task->TaskArgs);
        if (!isset($params['fromHost'])) {
            //création de l'hébergement
            $host = Sys::getOneData('Parc', 'Host/' . $this->Id);
            $infra = Sys::getOneData('Parc', 'Infra/Host/' . $this->Id);
            $name = (isset($params['targetHost']) && !empty($params['targetHost'])) ? $params['targetHost'] : $host->Nom . ' (Copie)';
            $client = $host->getOneParent('Client');
            $server = (isset($params['targetServer']) && $params['targetServer'] > 0) ? Sys::getOneData('Parc', 'Server/' . $params['targetServer']) : $host->getOneParent('Server');
            $act = $task->createActivity('Création de l\'hébergement ' . $params['targetHost'] . ' sur le serveur ' . $server->Nom);
            //suppression des champs indesirables
            unset($host->Id);
            unset($host->tmsCreate);
            unset($host->userCreate);
            unset($host->tmsEdit);
            unset($host->userEdit);
            unset($host->LdapID);
            unset($host->LdapTms);
            unset($host->LdapGid);
            unset($host->LdapUid);
            unset($host->NomLDAP);
            $host->addParent($client);
            $host->addParent($server);
            if ($infra)$host->addParent($infra);
            $host->Nom = $name;
            try {
                if (!$host->Save()) {
                    foreach ($host->Error as $err) {
                        $actErr = $task->createActivity('Erreur lors de la création de l\'hébergement: ' . $err['Message']);
                        $actErr->Terminate(false);
                    }
                    throw new Exception('Impossible de créer l\'hébergement');
                }
            } catch (Exception $e) {
                $act->addDetails($e->getMessage());
                $act->addDetails(print_r($host, true));
                $act->Terminate(false);
                return false;
            }

            $act->Terminate(true);
            $params['fromHost'] = $this->Id;
            $params['toHost'] = $host->Id;
            $task->TaskArgs = serialize($params);
            $task->Save();
        }
        //lancement de la synchronisation
        return $this->syncHost($task);;
    }
    /**
     * syncHost
     * Fonction de synchronisartion des hébergements
     * @param task Task Object
     */
    public function syncHost($task){
        $params = unserialize($task->TaskArgs);
        if (!isset($params['fromHost'])||!isset($params['toHost'])){
            $act = $task->createActivity('Les paramètres fromHost toHost sont introuvables');
            $act->Terminate(false);
            return false;
        }
        //target
        $host = Sys::getOneData('Parc','Host/'.$params['toHost']);
        $bdd = $host->getOneChild('Bdd');
        $mysqlsrv = $bdd->getOneParent('Server');
        $apachesrv = $host->getOneParent('Server');
        //source
        $srchost = Sys::getOneData('Parc','Host/'.$params['fromHost']);
        $srcbdd = $srchost->getOneChild('Bdd');
        $srcmysqlsrv = $srcbdd->getOneParent('Server');
        $srcapachesrv = $srchost->getOneParent('Server');
        try {
            //Installation des fichiers
            $act = $task->createActivity('Suppression du dossier www', 'Info');
            $out = $apachesrv->remoteExec('rm -Rf /home/' . $host->NomLDAP . '/www');
            $act->addDetails($out);
            $act->Terminate(true);
            //Installation des fichiers
            $act = $task->createActivity('Initialisation de la synchronisation', 'Info');
            $cmd = 'cd /home/' . $host->NomLDAP . '/ && rsync -e "ssh -o StrictHostKeyChecking=no" -avz root@'.$srcapachesrv->InternalIP.':/home/'.$srchost->NomLDAP.'/www/ www';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            $act = $task->createActivity('Modification des droits', 'Info');
            $cmd = 'chown ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . '/www -R';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            //Dump de la base
            $act = $task->createActivity('Dump de la base Mysql', 'Info');
            $cmd = 'mysqldump -h '.$srcmysqlsrv->InternalIP.' -u '.$srchost->NomLDAP.' -p'.$srchost->Password.' '.$srcbdd->Nom.' | mysql -u '.$host->NomLDAP.' -h '.$mysqlsrv->InternalIP.' -p'.$host->Password.' '.$bdd->Nom;
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            return true;
        }catch (Exception $e){
            $act->addDetails('Erreur: '.$e->getMessage());
            $act->Terminate(false);
            throw new Exception($e->getMessage());
        }
    }
    /**
     * emptyProxyCacheTask
     * Supprime le cache des serveurs proxy pour cet hébergement
     */
    public function emptyProxyCacheTask(){
        $infra=$this->getInfra();
        $aps = $this->getChildren('Apache');
        foreach ($aps as $ap)
            $ap->emptyProxyCacheTask();
        return true;
    }

    /**
     * @return Object
     * getServer
     */
    public function getServer() {
        $serv = Sys::getOneData('Parc','Server/Host/'.$this->Id,0,1,'','','','',true);
        return $serv;
    }

    public function getSize(){
        $server = $this->getServer();
        $cmd='du -s /home/'.$this->NomLDAP.'  | cut -f1';
        $this->DiskSpace = $server->remoteExec($cmd);
        //mise à jour quota
        if ($this->Quota<=10000) $this->Quota=5*1024*1024;
        $this->DiskQuota = round(($this->DiskSpace / $this->Quota) *100);

        //mise à jour des tailles de bdd
        $bdds = $this->getChildren('Bdd');
        foreach ($bdds as $bdd){
            //echo $bdd->Nom.' size: '.$bdd->getSize();
            $bdd->getSize();
        }

        $this->softSave();
        return $this->DiskSpace;
    }

}
/**
 * Terminal
 * Class to interact with the user and the shell process.
 * @author Thiago Bocchile <tykoth@gmail.com>
 * @package xwiterm
 * @subpackage xwiterm-linux
 */

class Terminal {


    private $login;
    private $password;

    static $username;
    static $commandsFile = "/tmp/comandos.txt";
    static $totalCommands;
    static $process;
    static $status;
    static $meta;

    static $instance;

    /**
     * Static function to simply autenticate users.
     * Can be used for other purposes.
     * @param string $login the linux username
     * @param string $password password
     * @access public
     * @return bool - true if login and password is right
     */
    public static function autenticate($login, $password) {


        self::$username = $login;
        $process = new TerminalProcess("su " . escapeshellarg($login));
        usleep(500000);
        $process->put($password);
        $process->put(chr(13));
        usleep(500000);
        return (bool) !$process->close();
    }

    /**
     * Get the terminal title.
     * @return string
     */
    public static function getTitle(){
        if(!empty(self::$username)){
            $process = new TerminalProcess("uname -n");
            $title = self::$username."@".trim($process->get());
            $process->close();
            return $title;
        } else {
            return "not logged shell - how cold it be possible, uh?";
        }
    }
    /**
     * Static function to "run" the terminal.
     * It must be used at the end of all html output.
     * @param string $login
     * @param string $password
     * @return bool - true if runs
     */
    public static function run($login, $password){
        self::$instance = new self();
        return self::$instance->open($login, $password);
    }

    /**
     * Simple function to "post" a command in the commands file.
     * @todo Another way to catch the commands, file is ugly.
     * @param string $command
     */
    public static function postCommand($command){
        file_put_contents(self::$commandsFile, $command."\n", FILE_APPEND);
    }

    /**
     * Open the terminal process.
     * @param string $login
     * @param string $password
     * @return bool - true if runs
     */
    private function open($login, $password) {
        $this->login = $login;
        $this->password = $password;

        if(!is_writable(self::$commandsFile)){
            $this->output("\r\nNeed permission to write in ".self::$commandsFile."\r\n");
            return false;
        }

        // Clean commands
        file_put_contents(self::$commandsFile, "");
        $this->startProcess();

        do {
            $out = self::$process->get();

            // Detect "blocking" (wait for stdin)
            if(sizeof($out) == 1 && ord($out) == 0) {
                $this->listenCommand();
            } else {
                // Provisorio, meldels. (usuario www-data não tem controle de servico, dude!)
                if(preg_match('/-su: no job control in this shell/', $out)) continue;
                $this->output($out);
            }
            usleep(50000);
            self::$status = self::$process->getStatus();
            self::$meta = self::$process->metaData();
        } while(self::$meta['eof'] === false);
        return true;
    }

    /**
     * Starts the terminal process
     * Uses the class Process.
     * @return true if runs
     */
    private function startProcess() {
        $cmd = "sudo -S su - {$this->login}";
        self::$process = new TerminalProcess($cmd);
        //self::$process = new TerminalProcess("whoami");
//        self::$process = new TerminalProcess("vi");
        if(!self::$process->isResource()) {
            throw new Exception("RESOURCE NOT AVAIBLE");
            return false;
        }
        usleep(500000);
        self::$process->getStatus();
        self::$process->put($this->password);
        self::$process->put(chr(13));
        self::$process->get();
        usleep(500000);
        self::$status = self::$process->getStatus();
        self::$meta = self::$process->metaData();
    }

    /**
     * Simulates the terminal colors :)
     * Format the input and returns as html with styles
     * Function to be used with preg_replace.
     * @param string $code
     * @param string $value
     * @return string - the html tag with style
     */
    private function consoleTag($code, $value){
        $attrs = explode(";", $code);

        if(sizeof($attrs) == 2 && intval($attrs[0]) > 10){
            $attrs[2] = $attrs[1];
            $attrs[1] = $attrs[0];

        }

        if(sizeof($attrs) == 2 && intval($attrs[0]) == 0 && intval($attrs[1]) == 0){
            $attrs[0] = 0;
            $attrs[1] = 37;
        }
        $text = array(
            '0' => '',
            '1' => 'font-weight:bold',
            '3' => 'text-decoration:underline',
            '5' => 'blink'
        );
        $colors = array(
            '0' => 'black',
            '1' => 'red',
            '2' => '#89E234', // green
            '3' => 'yellow',
            '4' => '#729FCF', // blue
            '5' => 'magenta',
            '6' => 'cyan',
            '7' => 'white'
        );

        $text_decoration = (isset($attrs[0]) && array_key_exists(intval($attrs[0]), $text)) ? $text[intval($attrs[0])] : $text[0];
        $color = (isset($attrs[1]) && array_key_exists(intval($attrs[1])-30, $colors)) ? $colors[intval($attrs[1])-30] : $colors[0];
        $style = sprintf("%s;color:%s;", $text_decoration, $color);
        $style.= (isset($attrs[2]) && array_key_exists((intval($attrs[2])-40), $colors)) ? "background-color:".$colors[(intval($attrs[2])-40)] : '';
        return "<tt style=\\\"$style\\\">$value</tt>";
    }

    /**
     * "Hard" output.
     * It's not a good practice to echo from class methods, so it's a provisory
     * method.
     * @param string $output
     * @param bool $return - true to return the formatted output
     * @param bool $html - true to format html
     * @return mixed - if $return is true, returns the output
     */
    private function output($output, $return = false, $html = true) {

        if(preg_match('/\x08/',$output)) return false;

        $output = htmlentities($output);
        $output = addslashes($output);

        $output = explode("\n", $output);
        $output = implode("</span><span>", $output);
        $output = sprintf("<span>%s</span>", $output);
        $output = preg_replace( "/\r\n|\r|\n/", '\n', $output);

        // Removes the first occurrence (on ls)
        $output = preg_replace('/\x1B\[0m(\x1B)/', "\x1B", $output);
        // Add colors to default coloring sytem
        $output = preg_replace('/\x1B\[([^m]+)m([^\x1B]+)\x1B\[0m/e', '$this->consoleTag(\'\\1\',\'\\2\')', $output);
        $output = preg_replace('/\x1B\[([^m]+)m([^\x1B]+)\x1B\[m/e', '$this->consoleTag(\'\\1\',\'\\2\')', $output);
        // Add colors to grep color system
        $output = preg_replace('/\x1B\[([^m]+)m\x1B\[K([^\x1B]+)\x1B\[m\x1B\[K/e', '$this->consoleTag("\\1","\\2")', $output);

        // Removes some dumb chars
        $output = preg_replace('/\x1B\[m/', '', $output);
        $output = preg_replace('/\x07/', '', $output);


        if($return === false){
            echo "<script>recebe(\"{$output}\");</script>\n"; flush();
        } else {
            return $output;
        }
    }

    /**
     * Formats the output to be used as command suggest (pressing TAB)
     * @param string $output
     * @return string
     */
    private function commandSuggest($output){
        $output = preg_replace( "/\n|\r|\r\n/", '', $output);
        $output = preg_replace('/'.chr(7).'/', '', $output);
        return trim($output);
    }

    /**
     * Listener for incoming commands
     */
    private function listenCommand() {

        $commands = file(self::$commandsFile);

        if(sizeof($commands) > self::$totalCommands) {
            self::$totalCommands = sizeof($commands);
            $command = $commands[self::$totalCommands-1];
            $this->parse($command);
        }
    }

    /**
     * Parse the command
     * @param string $command - incomming command from terminal
     * @return void
     */
    private function parse($command) {


        switch(trim($command)) {
            case chr(3):
                // SIGTERM
                return $this->sendSigterm();
                break;
            case chr(4):
                self::$process->put("exit");
                self::$process->put(chr(13));
                break;

            case chr(26):
                //STOP - experimental
                return $this->sendSigstop();
                break;
            case 'fg':
                return $this->sendFg();
                break;

            default:
                // Checks for "TAB"
                if(ord(substr($command,-2,1)) == 9){
                    self::$process->put(trim($command).chr(9));
                    usleep(500000);

                    $out = self::$process->get();
                    // Check if is a "RE-TAB"
                    if(trim($command) == $this->commandSuggest($out)){
                        self::$process->put(trim($command).chr(9).chr(9));
                        self::$process->put(chr(21));
                        $this->output(self::$process->get());
                    } else {
                        echo "<script>recebe(null, '".$this->commandSuggest($out)."')</script>\n"; flush();
                    }
                    self::$process->put(chr(21));
                } else {
                    self::$process->put(chr(21));
                    self::$process->put(trim($command));
                    self::$process->put(chr(13));
                }

                usleep(500000);
                break;
        }
    }


    /**
     * Emulates the SIGTERM sending via CTRL-C
     * @return void
     */
    private function sendSigterm() {
        // SLAYER!!! GRRRRRRRRRR
        // http://www.youtube.com/watch?v=VSoh3c7QVyw
        $SLAYER = 'pid='.self::$status['pid'].
            '; supid=`ps -o pid --no-heading --ppid $pid`;'.
            'bashpid=`ps -o pid --no-heading --ppid $supid`;'.
            'childs=`ps -o pid --no-heading --ppid $bashpid`;'.
            'kill -9 $childs;';
        $process = new TerminalProcess("su -c '{$SLAYER}' -l {$this->login}");
        usleep(500000);
        $process->put($this->password);
        $process->put(chr(13));
        usleep(500000);
    }

    /**
     * Simulates the SIGSTOP sending via CTRL-Z
     * @return void
     */
    private function sendSigstop() {
        $SLAYER = 'pid='.self::$status['pid'].
            '; supid=`ps -o pid --no-heading --ppid $pid`;'.
            'bashpid=`ps -o pid --no-heading --ppid $supid`;'.
            'childs=`ps -o pid --no-heading --ppid $bashpid`;'.
            'kill -TSTP $childs;';
        $process = new TerminalProcess("su -c '{$SLAYER}' -l {$this->login}");
        usleep(500000);
        $process->put($this->password);
        $process->put(chr(13));
        self::$process->put(chr(13));
        usleep(500000);
    }

    /**
     * Simulates the SIGCONT sending via 'fg'
     */
    private function sendFg() {
        $SLAYER = 'pid='.self::$status['pid'].
            '; supid=`ps -o pid --no-heading --ppid $pid`;'.
            'bashpid=`ps -o pid --no-heading --ppid $supid`;'.
            'childs=`ps -o pid --no-heading --ppid $bashpid`;'.
            'kill -CONT $childs;';
        $process = new TerminalProcess("su -c '{$SLAYER}' -l {$this->login}");
        usleep(500000);
        $process->put($this->password);
        $process->put(chr(13));
        self::$process->put(chr(13));
        usleep(500000);
    }


}/**
 * Class to control the process.
 * @author Thiago Bocchile <tykoth@gmail.com>
 * @package xwiterm
 * @subpackage xwiterm-linux
 */
class TerminalProcess {
    public $pipes;
    public $process;

    public function __construct($command) {
        return $this->open($command);
    }

    public function __destruct() {
        return $this->close();
    }

    public function open($command) {
        /*        $spec = array(
                    array("pty"), // MAGIC - THE GATHERING!! MWAHAHAHAHA
                    array("pty"),
                    array("pty")
                );*/
        $spec = array(
            array("pipe","r"), // MAGIC - THE GATHERING!! MWAHAHAHAHA
            array("pipe","w"),
            array("pipe","w")
        );
        $this->process = proc_open($command, $spec, $this->pipes);
        $this->setBlocking(0);
    }

    public function isResource() {
        return is_resource($this->process);
    }
    public function setBlocking($blocking = 1) {
        return stream_set_blocking($this->pipes[1], $blocking);
    }
    public function getStatus() {
        return proc_get_status($this->process);
    }
    public function get() {
//		$out = fread($this->pipes[1], 128);
//		$out = fgets($this->pipes[1]);
        $out = stream_get_contents($this->pipes[1]);
        return $out;
    }

    public function put($data) {
//		fwrite($this->pipes[1], $data."\n");
        fwrite($this->pipes[1], $data);
//		fwrite($this->pipes[1], chr(13));
        fflush($this->pipes[1]);
//		return fwrite($this->pipes[1], $data);
    }

    public function close() {
        if(is_resource($this->process)) {
            fclose($this->pipes[0]);
            fclose($this->pipes[1]);
            fclose($this->pipes[2]);
            return proc_close($this->process);
        }
    }
    public function metaData() {
        return stream_get_meta_data($this->pipes[1]);
    }
}