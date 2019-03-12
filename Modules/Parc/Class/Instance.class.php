<?php

class Instance extends genericClass{
    private $_plugin = null;
    public function Save($force=false){
        Sys::startTransaction();
        if ($this->Id)
            $old = Sys::getOneData('Parc', 'Instance/' . $this->Id);
        else $old=false;
        if (!$old) $new = true; else $new = false;
        if (!$new&&$old->SousDomaine!=$this->SousDomaine){
            $this->addError(Array("Message" => 'Impossible de modifier le sous domaine d\'une instance. Veuillez créer une nouvelle instance.'));
            return false;
        }
        if (!$new&&$old->InstanceNom!=$this->InstanceNom){
            $this->addError(Array("Message" => 'Impossible de modifier le nom technique d\'une instance. Veuillez créer une nouvelle instance.'));
            return false;
        }


        $pref='';
        $infra = $this->getInfra();
        if($infra){
            $pref= 'Infra/'.$infra->Id.'/';
        }

        //serveurs par défaut
        $apachesrv = Sys::getOneData('Parc', $pref.'Server/Web=1&defaultWebServer=1', null, null, null, null, null, null, true);
        $proxysrv = Sys::getOneData('Parc', $pref.'Server/Proxy=1', null, null, null, null, null, null, true);
        $mysqlsrv = Sys::getOneData('Parc', $pref.'Server/Sql=1&defaultSqlServer=1', null, null, null, null, null, null, true);
        $dom = Sys::getOneData('Parc', 'Domain/defaultDomain=1', 0, 100, '', '', '', '', true);

        if (!$apachesrv) {
            $GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Il n\'y a pas de serveur apache par défaut. Veuillez en définir un.'));
            return false;
        }
        if (!$mysqlsrv) {
            $GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Il n\'y a pas de serveur mysql par défaut. Veuillez contacter votre administrateur.'));
            return false;
        }
        if (!$dom) {
            $GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Il n\'y a pas de domaine par défaut. Veuillez contacter votre administrateur.'));
            return false;
        }

        //Verification du nom
        if ($this->SousDomaine != Instance::checkName($this->SousDomaine)) {
            $GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Le sous-domaine de l\'instance ne doit pas contenir de caractère spéciaux. '.$this->SousDomaine.'!='.Instance::checkName($this->SousDomaine)));
            return false;
        }

        //creation du nom temporaire
        if (empty($this->InstanceNom))
            $this->InstanceNom = substr('inst-'.Instance::checkName($this->Nom),0,32);
        else $this->InstanceNom = substr($this->InstanceNom,0,32);

        //Vérification du mot de passe
        if (empty($this->Password)){
            $this->Password = str_shuffle(bin2hex(openssl_random_pseudo_bytes(12)));
        }

        //Check du client
        $client = $this->getOneParent('Client');
        if (!$client) {
            $client = Parc_Client::getClientFromCode(Instance::checkName($this->Nom),$this->Nom);
            $this->addParent($client);
        }
        parent::Save();

        $tmpname = $this->InstanceNom;

        //vérification de l'existence du sous domaine par défaut
        if ($dom) {
            $as = Sys::getOneData('Parc', 'Domain/' . $dom->Id . '/Subdomain/Url=' . $tmpname);
            if (!$as) {
                $as = genericClass::createInstance('Parc', 'Subdomain');
                $as->Url = $tmpname;
                $as->IP = $proxysrv->IP;
                $as->addParent($dom);
                $as->Save();
            } else {
                $as->IP = $proxysrv->IP;
                $as->addParent($dom);
                $as->Save();
            }
            $this->FullDomain = $as->Url . '.' . $dom->Url;
            parent::Save();
        }

        //Check des domaine (CAS SECIB)
        $dom = $this->getParents('Domain');
        if (is_array($dom) && sizeof($dom)) {
            $defaulturl = false;
            $otherurls = array();
            foreach ($dom as $d) {
                //vérification des sous domaines
                $www = $d->getOneChild('Subdomain/Url=' . $this->InstanceNom);
                if (!$www) {
                    //création du A
                    $www = genericClass::createInstance('Parc', 'Subdomain');
                    $www->Url = $this->SousDomaine;
                    $www->IP = $proxysrv->IP;
                } else {
                    $www->IP = $proxysrv->IP;
                }
                $www->addParent($d);
                $www->Save();
                if (!$defaulturl) $defaulturl = $this->SousDomaine . '.' . $d->Url;
                else $otherurls[] = $this->SousDomaine . '.' . $d->Url;
            }
        }

        //Check de l'hébergement
        try {
            $heb = $this->getOneParent('Host');
            if (!$heb) {
                //alors création de l'hébergement
                $heb = genericClass::createInstance('Parc', 'Host');
                $heb->Nom = $tmpname;
                $heb->Password = $this->Password;
                $heb->Production = true;

                $heb->PHPVersion = $this->PHPVersion;
                $heb->BackupEnabled = $this->BackupEnabled;
                $heb->addParent($apachesrv);
                $heb->addParent($client);
                $heb->addParent($infra);
                if (!$heb->Save()) {
                    $GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
                    $this->Error = array_merge($this->Error, $heb->Error);
                    return false;
                }
                $this->addParent($heb);
            } else {
                $heb->Production = true;
                $heb->Password = $this->Password;
                $heb->PHPVersion = $this->PHPVersion;
                $heb->BackupEnabled = $this->BackupEnabled;
                $heb->addParent($infra);
                $heb->addParent($client);
                $heb->Save();
            }
        }catch (Exception $e){
            //impossible de creéer l'hébergement
            $GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Impossible de créer l\'hébergement. raison: '.$e->getMessage()));
            return false;
        }

        //installation logicielle
        if (intval($this->Status) <= 1)
            $this->createInstallTask();

        parent::Save();

        //execution postinit plugin
        $plugin = $this->getPlugin();
        $plugin->postInit();

        //redemarrages de proxys
        Server::createRestartProxyTask($infra);
        return true;
    }
    public function softSave(){
        return parent::Save();
    }

    /**
     * Retourne l'infra à laquelle est attachée l'instance
     * @return	mixed Object Infra - false
     */
    public function getInfra()
    {
        if(empty($this->Id)){
            $infra = false;
            foreach ($this->Parents as $p){
                if($p['Titre'] == 'Infra'){
                    $infra = Sys::getOneData('Parc','Infra/'.$p['Id'],0,1,null,null,null,null,true);
                    break;
                }
            }
            if (!$infra){
                //affectation à l'infra par défaut
                $infra = Sys::getOneData('Parc','Infra/Default=1',0,100,null,null,null,null,true);
                $this->addParent($infra);
            }
            return $infra;
        }

        $infra = Sys::getOneData('Parc','Infra/Instance/'.$this->Id,0,100,null,null,null,null,true);
        if (!$infra){
            //affectation à l'infra par défaut
            $infra = Sys::getOneData('Parc','Infra/Default=1');
            $this->addParent($infra);
            return $infra;
        }
        else return $infra;
    }


    /**
     * Retourne un plugin Parc / Instance
     * @return	Implémentation d'interface
     */
    public function getPlugin() {
        if (!$this->_plugin) {
            $this->_plugin = Plugin::createInstance('Parc', 'Instance', $this->Plugin);
            $this->_plugin->setConfig($this->PluginConfig, $this);
        }
        return $this->_plugin;
    }


    /**
     * createInstallTask
     * Creation de la tache d'installation de l'applicatif
     */
    public function createInstallTask(){
        //on vérifie que la tache n'est pas déjà crée
        $nb = Sys::getCount('Parc','Instance/'.$this->Id.'/Tache/Termine=0&Erreur=0&TaskType=install');
        if ($nb){
            $this->addError(array('Message'=>'Une tache d\'installation est déjà en cours'));
            return true;
        }
        $plugin = $this->getPlugin();
        $this->Status=1;
        parent::Save();
        return $plugin->createInstallTask();
    }
    /**
     * createUpdateTask
     * Creation de la tache d'installation de l'applicatif
     */
    public function createUpdateTask($orig = null){
        $plugin = $this->getPlugin();
        return $plugin->createUpdateTask($orig);
    }
    /**
     * createCheckStateTask
     * Creation de la tache de vérification
     */
    public function createCheckStateTask($orig=null){
        //on vérifie que la tache n'est pas déjà crée
        $nb = Sys::getCount('Parc','Instance/'.$this->Id.'/Tache/Termine=0&Erreur=0&TaskCode=INSTANCE_CHECKSTATE&TaskFunction=checkState');
        if ($nb){
            $this->addError(array('Message'=>'Une tache de vérification est déjà en cours'));
            return true;
        }
        //gestion depuis le plugin
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Vérification de l\'instance ' . $this->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Instance';
        $task->TaskType = 'check';
        $task->TaskCode = 'INSTANCE_CHECKSTATE';
        $task->TaskId = $this->Id;
        $task->TaskFunction = 'checkState';
        $task->addParent($this);
        $host = $this->getOneParent('Host');
        if (!$host) return false;
        $task->addParent($host);
        $task->addParent($host->getOneParent('Server'));
        if (is_object($orig)) $task->addParent($orig);
        $task->Save();
        return $task;
    }

    /**
     * Delete
     * Supprime une instance
     * @return bool
     */
    public function Delete(){
        //suppression hébergement
        $host = $this->getOneParent('Host');
        if ($host)
            $host->Delete();
        parent::Delete();
        return true;
    }

    /**
     * installSoftware
     * Fonction d'installation ou de mise à jour de l'applicatif
     * @param Object Tache
     */
    public function installSoftware($task = null){
        $plugin = $this->getPlugin();
        return $plugin->installSoftware($task);
    }

    /**
     * updateSoftware
     * Fonction de mise à jour de l'applicatif
     * @param Object Tache
     */
    public function updateSoftware($task = null){
        $plugin = $this->getPlugin();
        return $plugin->updateSoftware($task);
    }

    /**
     * setStatus
     * Définit le status de l'instance
     * @param bool $force
     *
     */
    public function setStatus($nb){
        $this->Status = $nb;
        parent::Save();
    }

    /**
     * enableSsl
     * Active le ssl pour l'ensemble des hôtes virtuels
     * @param bool $force
     */
    public function enableSsl($force = false){
        //recherche du apache correspondant
        $host = $this->getOneParent('Host');
        $apache = $host->getOneChild('Apache');
        $out =  $apache->enableSsl($force,$this);
        $this->Error = $apache->Error;
    }
    /**
     * getHttpCode
     * Retourne le code http pour un domaine en particulier.
     */
    public static function getHttpCode($url,$https=false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,$https);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $rt = curl_exec($ch);
        $info = curl_getinfo($ch);
        return $info;
        //return $info["http_code"];
    }
    /**
     * checkName
     * Vérifie le nom de l'instance
     * @param $chaine
     * @return mixed|string
     */
    static function checkName($chaine) {
        $chaine = strtolower($chaine);
        $chaine=utf8_decode($chaine);
        $chaine=stripslashes($chaine);
        $chaine = preg_replace('`\s+`', '-', trim($chaine));
        $chaine = str_replace("'", "-", $chaine);
        $chaine = str_replace("&", "et", $chaine);
        $chaine = str_replace('"', "-", $chaine);
        $chaine = str_replace("?", "", $chaine);
        $chaine = str_replace("!", "", $chaine);
        $chaine = str_replace(".", "", $chaine);
        $chaine = preg_replace('`[\,\ \(\)\+\'\/\:_\;]`', '-', trim($chaine));
        $chaine=strtr($chaine,utf8_decode("ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ?"),"aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn-");
        $chaine = preg_replace('`[-]+`', '-', trim($chaine));
        $chaine =  utf8_encode($chaine);
        $chaine = preg_replace('`[\/]`', '-', trim($chaine));

        return $chaine;
    }
    /**
     * checkSssl
     * @param $url
     */
    public static function checkSsl($url){
        $orignal_parse = parse_url($url, PHP_URL_HOST);
        $get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
        $read = stream_socket_client("ssl://".$orignal_parse.":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
        $cert = stream_context_get_params($read);
        $certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);
        return $certinfo;
    }
    /**
     * checkState
     * Vérifie l'état d'une instance
     */
    public function checkState($task=null) {
        $task->DateDebut =time();
        $host = $this->getOneParent('Host');
        $aps = $host->getChildren('Apache/Actif=1');
        $start = microtime(true);

        //TODO Delete this dirty workaround
        $toCheck = false;

        //vérifie le retour http sur page accueil
        foreach ($aps as $ap){


            $domains = explode(' ',$ap->getDomainsToCheck());
            foreach ($domains as $domain){
                $domain = trim($domain);
                if (empty($domain)) continue;
                //test http
                try {
                    $act = $task->createActivity('Vérification du domaine '.$domain);
                    $time = microtime(true);
                    $code = self::getHttpCode('http://' . $domain);
                    $act->addDetails('HTTP: '.print_r($code,true));
                    if (!in_array(intval($code["http_code"]), array(200, 301, 302, 303, 0))) {
                        //alors incident
                        Incident::createIncident('Le domaine ' . $domain . ' ne répond pas correctement en http.', 'Le code de retour est ' . print_r($code, true), $this, 'HTTP_CODE', $domain, 4, false);
                    } else {
                        Incident::createIncident('Le domaine ' . $domain . ' ne répond pas correctement en http.', 'Le code de retour est ' . print_r($code, true), $this, 'HTTP_CODE', $domain, 4, true);
                    }

                    if (in_array(intval($code["http_code"]), array(200))&&intval($code["size_download"]) == 0) {
                        //teste la taille de la page si =0 alors incident page blanche
                        //alors incident
                        Incident::createIncident('Le domaine ' . $domain . ' retourne une page vide.', 'Le détail du retour: ' . print_r($code, true), $this, 'HTTP_EMPTY', $domain, 4, false);

                        //TODO DELETE THIS DIRTY WORKAROUND
                        if (!$toCheck) {
                            $this->emptyProxyCacheTask();
                            $toCheck = true;
                        }

                    } else Incident::createIncident('Le domaine ' . $domain . ' retourne une page vide.', 'Le détail du retour: ' . print_r($code, true), $this, 'HTTP_EMPTY', $domain, 4, true);

                    //si ssl vérifie l'état du certificat et le code retour
                    if ($ap->Ssl) {
                        $code = self::getHttpCode('https://' . $domain, true);
                        $act->addDetails('HTTPS: '.print_r($code,true));
                        if (!in_array(intval($code["http_code"]), array(200, 301, 302, 0))) {
                            //alors incident
                            Incident::createIncident('Le domaine ' . $domain . ' ne répond pas correctement en https.', 'Le code de retour est ' . print_r($code, true), $this, 'HTTPS_CODE', $domain, 4, false);
                        } else{
                            Incident::createIncident('Le domaine ' . $domain . ' ne répond pas correctement en https.', 'Le code de retour est ' . print_r($code, true), $this, 'HTTPS_CODE', $domain, 4, true);
                        }
                        if (in_array(intval($code["http_code"]), array(200))&&intval($code["size_download"]) == 0) {
                            //teste la taille de la page si =0 alors incident page blanche
                            Incident::createIncident('Le domaine ' . $domain . ' retourne une page vide.', 'Le détail du retour: ' . print_r($code, true), $this, 'HTTPS_EMPTY', $domain, 4, false);

                            //TODO DELETE THIS DIRTY WORKAROUND
                            if (!$toCheck) {
                                $this->emptyProxyCacheTask();
                                $toCheck = true;
                            }

                        } else Incident::createIncident('Le domaine ' . $domain . ' retourne une page vide.', 'Le détail du retour: ' . print_r($code, true), $this, 'HTTPS_EMPTY', $domain, 4, true);

                        //vérification du certificat
                        $certinfo = Instance::checkSsl('https://' . $domain);
                        if (!$certinfo) continue;
                        //test de la date d'expiration
                        if ($certinfo['validTo_time_t'] < time()) {
                            Incident::createIncident('Le certificat du domaine ' . $domain . ' a expirté le ' . date('d/m/Y H:i:s', $certinfo['validTo_time_t']), 'Le code de retour est ' . print_r($certinfo, true), $this, 'SSL_ERROR', $domain, 4, false);
                        } else Incident::createIncident('Le certificat du domaine ' . $domain . ' a expirté le ' . date('d/m/Y H:i:s', $certinfo['validTo_time_t']), 'Le code de retour est ' . print_r($certinfo, true), $this, 'SSL_ERROR', $domain, 4, true);

                        //on compare la liste des domaines à certifier et les domaines dans le certificat
                        $certdomains = array();
                        preg_match_all('#DNS:([^\ ,]*)#', $certinfo['extensions']['subjectAltName'], $othersdomains);
                        $certdomains = array_merge($certdomains, $othersdomains[1]);
                        if (!in_array($domain, $certdomains)) {
                            Incident::createIncident('Le certificat ne gère pas le domaine ' . $domain . '.', 'Le code de retour est ' . print_r($certinfo, true), $this, 'SSL_ERROR', $domain, 4, false);
                        } else Incident::createIncident('Le certificat ne gère pas le domaine ' . $domain . '.', 'Le code de retour est ' . print_r($certinfo, true), $this, 'SSL_ERROR', $domain, 4, true);
                    }
                    $act->addDetails('Exécution en '.floatval((microtime(true)-$time)).' secondes');
                    $act->Terminate(true);
                }catch (Exception $e){
                    $act->Terminate(false);
                }
            }
        }
        $act = $task->createActivity('Execution de la tache plugin');
        $time = microtime(true);
        $plugin = $this->getPlugin();
        //appel checkState du plugin
        $plugin->checkState($task);
        $act->addDetails('Exécution en '.floatval((microtime(true)-$time)).' secondes');
        $act->Terminate(true);
        $act = $task->createActivity('Vérification terminée en '.floatval(microtime(true)-$start).' secondes');
        $act->Terminate(true);
        return true;
    }
    /**
     * rewriteConfig
     * Réécrire Configuration
     */
    public function rewriteConfig(){
        $plugin = $this->getPlugin();
        return $plugin->rewriteConfig();
    }
    /**
     * createBackupTask
     * création d'un point de restauration
     */
    public function createBackupTask() {
        $host = $this->getOneParent('Host');
        return $host->createBackupTask();
    }

    public function createApache($ssl=0,$proxycache=0){
        $host = $this->getOneParent('Host');
        if(!$host){
            $pars = array();
            foreach ($this->Parents as $p){
                if($p['Titre'] == 'Host'){
                    $pa = Sys::getOneData('Parc','Host/'.$p['Id'],0,1,null,null,null,null,true);
                    $pars[] = $pa;
                }

            }
            $host = $pars[0];
        }


        $apache = genericClass::createInstance('Parc','Apache');
        $apache->Ssl = $ssl;
        $apache->Proxycache = $proxycache;

        $pref = $ssl ? ( $proxycache ? 'ssl-cache-' : 'ssl-') : ( $proxycache ? 'cache-' : '' );

        $apache->ApacheServerName = $pref.$this->FullDomain;
        $apache->DocumentRoot = $this->SousDomaine;
        $apache->Actif = true;
        $apache->addParent($host);
        $apache->Save();

        return $apache;
    }
    /**
     * emptyProxyCacheTask
     * Supprime le cache des serveurs proxy pour cet hébergement
     */
    public function emptyProxyCacheTask(){
        $hosts = $this->getParents('Host');
        foreach ($hosts as $host)
            $host->emptyProxyCacheTask();

        //vérifier l'instance après
        //TODO DElete this dirty workaround
        $this->createCheckStateTask();
        return true;
    }

}