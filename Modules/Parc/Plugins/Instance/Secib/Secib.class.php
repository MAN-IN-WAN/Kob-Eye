<?php

/*********************************************
*
* Module de paiement
* Crédit Mutuel
* Abtel
* 
*********************************************/

require_once( dirname(dirname(__FILE__)).'/Instance.interface.php' );

class ParcInstanceSecib extends Plugin implements ParcInstancePlugin {

    /**
     * Delete
     * Suppression des éléments spécifiques
     */
    public function Delete(){
    }
    /**
     * postInit
     * Initialisation du plugin
     */
    public function postInit(){
        $pref='';
        $infra = $this->_obj->getInfra();
        if($infra){
            $pref= 'Infra/'.$infra->Id.'/';
        }

        $proxysrv = Sys::getOneData('Parc', $pref.'Server/Proxy=1', null, null, null, null, null, null, true);

        $host = $this->_obj->getOneParent('Host');
        $host->PHPVersion = '7.0.10';
        $host->Save();

        $infra = Sys::getOneData('Parc', 'Infra/Nom=Secib', null, null, null, null, null, null, true);
        if($infra)
            $this->_obj->addParent($infra);

        $this->_obj->PHPVersion = '7.0.10';
        $this->_obj->softSave();

        //Check des domaine (CAS SECIB)
        $dom = $this->_obj->getParents('Domain');
        if (is_array($dom) && sizeof($dom)) {
            $defaulturl = false;
            $otherurls = array();
            foreach ($dom as $d) {
                //vérification des sous domaines
                $www = $d->getOneChild('Subdomain/Url=' . $this->_obj->InstanceNom);
                if (!$www) {
                    //création du A
                    $www = genericClass::createInstance('Parc', 'Subdomain');
                    $www->Url = $this->_obj->InstanceNom;
                    $www->IP = $proxysrv->IP;
                } else {
                    $www->IP = $proxysrv->IP;
                }
                $www->addParent($d);
                $www->Save();
                $otherurls[] = $this->_obj->InstanceNom . '.' . $d->Url;
            }
        }

        $domains = '';
        $apaches  = $host->getChildren('Apache');
        foreach ($apaches as $apache) {
            //mise à jour de la base de donnée des domaines
            $domains .= $apache->ApacheServerName . ' ' . $apache->ApacheServerAlias;
        }
        $domains = explode(' ', $domains);

        //vérification des configuration apaches
        $dirty = false;
        foreach ($otherurls as $ou){
            $exists = false;
            foreach ($domains as $d){
                if ($d==$ou) $exists = true;
            }
            if (!$exists){
                //il faut jouater le domaine en serverAlias d'un des apaches
                $apache->ApacheServerAlias.="\n".$ou;
                $dirty = true;
            }
        }
        if ($dirty){
            //apache modifié
            $apache->ApacheServerAlias = trim($apache->ApacheServerAlias);
            $apache->Save();
            //lancement d'une reinstallation
            $this->createUpdateTask();
        }
    }
    /**
     * createInstallTask
     * Creation de la tach d'installation du secib web
     */
    public function createInstallTask(){
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Installation du logiciel Secib Web sur l\'instance ' . $this->_obj->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Instance';
        $task->TaskId = $this->_obj->Id;
        $task->TaskType = 'install';
        $task->TaskFunction = 'installSoftware';
        $task->addParent($this->_obj);
        $host = $this->_obj->getOneParent('Host');
        $task->addParent($host);
        $task->addParent($host->getOneParent('Server'));
        $task->Save();
    }
    /**
     * installSecibWeb
     * Fonction d'installation ou de mise à jour de secib web
     * @param Object Tache
     */
    public function installSoftware($task){
        $apachesrv = Sys::getOneData('Parc', 'Server/Web=1&defaultWebServer=1');
        $mysqlsrv = Sys::getOneData('Parc', 'Server/Sql=1&defaultSqlServer=1');
        $host = $this->_obj->getOneParent('Host');
        $version = VersionLogiciel::getLastVersion('Secib',$this->_obj->Type);
        if (!$version) {
            $act = $task->createActivity('Erreur pas de version correpsondante', 'Info');
            $act->Terminate(false);
            return false;
        }

        $bdd = $host->getOneChild('Bdd');
        $apaches = $host->getChildren('Apache');

        $db = new PDO('mysql:host=' . $mysqlsrv->InternalIP . ';dbname=' . $bdd->Nom, $mysqlsrv->SshUser, $mysqlsrv->SshPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $db->query("SET AUTOCOMMIT=1");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->query("GRANT USAGE ON *.* TO `".$host->Nom."` @'%';");
        $db->query("GRANT ALL PRIVILEGES ON `Analytics`.* TO `".$host->Nom."` @'%';");

        $act = $task->createActivity('Modification du dump', 'Info');
        try {
            //test de l'existence de certains champs
            $SQLInit = preg_replace_callback('/#IF_COLUMN_NOT_EXISTS\|(.*?)\|(.*?)#(.*?)#END_IF_COLUMN_NOT_EXISTS#/', function ($matches) use ($db, $bdd,$act) {
                $column = $matches[1];
                $table = $matches[2];
                $alter = $matches[3];
                $sql2 = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='" . $table . "' AND column_name='" . $column . "' AND TABLE_SCHEMA='" . $bdd->Nom . "'";
                $act->addDetails($sql2);
                $res =$db->query($sql2);
                $re1 = $res->fetchALL(PDO::FETCH_ASSOC);
                $act->addDetails(print_r($re1,true));
                if (!$re1[0]['COUNT(*)'])
                    return $alter;
                else return '';
            }, $version->SQLInit);
        }catch (Exception $e){
            $act->addDetails('Erreur :'.$e->getMessage());
            $act->Terminate(false);
            return;
        }
        $act->addDetails(print_r($SQLInit,true));
        $act->Terminate(true);

        //Connexion à la base de donnée
        $act = $task->createActivity('Initialisation de la base de donnée', 'Info');
        $db = new PDO('mysql:host=' . $mysqlsrv->InternalIP . ';dbname=' . $bdd->Nom, $host->Nom, $host->Password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $db->query("SET AUTOCOMMIT=1");
        $db->query("SET FOREIGN_KEY_CHECKS=0");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $domains = '';
        foreach ($apaches as $apache) {
            //mise à jour de la base de donnée des domaines
            $domains .= $apache->ApacheServerName . ' ' . $apache->ApacheServerAlias;
        }
        $domains = explode(' ', $domains);

        $act->addDetails($SQLInit);
        $db->query($SQLInit);
        $act->addDetails(print_r($db->errorInfo(),true));

        $db->query('TRUNCATE secibweb_webservice');
        $act->addDetails('TRUNCATE secibweb_webservice');
        $db->query('TRUNCATE secibweb_domaine');
        $act->addDetails('TRUNCATE secibweb_domaine');
        $sql = 'INSERT INTO `secibweb_domaine` (`dom_domaine`, `dom_ws_id`, `dom_lg`, `dom_actif`, `dom_crea_date`, `dom_crea_user_id`, `dom_modif_date`, `dom_modif_user_id`, `dom_ta_id`, `dom_url_wopi`, `dom_url_ebarreau`, `dom_url_merlin`) VALUES';
        $flag = false;
        foreach ($domains as $d) {
            if ($flag) $sql .= ',';
            $sql .= '(\'' . $d . '\', 1, \''.$this->_obj->Langue.'\', 1, \'' . date('Y-m-d') . ' 10:58:00\', 0, \'' . date('Y-m-d') . ' 10:58:00\', 0, '.$this->_obj->TypeApplication.',\''.$this->_obj->UrlWopi.'\',\''.$this->_obj->URLEBarreau.'\',\''.$this->_obj->URLMerlin.'\')';
            $flag = true;
        }
        $act->addDetails($sql);
        $db->query($sql);
        $sql = 'INSERT INTO `secibweb_webservice` (`ws_id`,`ws_nom`, `ws_url`, `ws_guid`, `ws_crea_date`, `ws_crea_user_id`, `ws_modif_date`, `ws_modif_user_id`) VALUES
(1,\'Intégration\', \'' . $this->_obj->WebService . '\', \'' . $this->_obj->Guid . '\', \'' . date('Y-m-d') . ' 09:40:00\', 0, \'' . date('Y-m-d') . ' 09:40:00\', 0);';
        $act->addDetails($sql);
        $db->query($sql);
        $act->Terminate(true);
        //Installation des fichiers
        $act = $task->createActivity('Téléchargement des fichiers', 'Info');
        $out = $apachesrv->remoteExec('wget -v http://management.secib.fr/' . $version->Fichier . ' -O /home/' . $host->Nom . '/version.zip');
        $act->addDetails('wget -v http://management.secib.fr/' . $version->Fichier . ' -O /home/' . $host->Nom . '/version.zip');
        $act->Terminate(true);
        $act = $task->createActivity('Extraction des fichiers', 'Info');
        $out = $apachesrv->remoteExec('cd /home/' . $host->Nom . '/ && unzip -o version.zip && chown ' . $this->_obj->InstanceNom . ':users * -R');
        $act->addDetails($out);
        $act->Terminate(true);
        //Saisie du fichier de configuration
        $act = $task->createActivity('Modification du fichier de config','Info');
        //db host
        $cmd = 'cat /home/' . $host->Nom . '/www/lib/init.php | sed -e \'s/oConfig->DB_PARAMS_ONLINE.*$/oConfig->DB_PARAMS_ONLINE = array("db_host" => "' . $mysqlsrv->IP . '" , "db_user" => "' . $host->Nom . '", "db_pass" => "' . $host->Password . '"  , "db_name" => "' . $bdd->Nom . '"); /\' > /home/' . $host->Nom . '/www/lib/init.php.tmp';
        $act->addDetails($cmd);
        $out = $apachesrv->remoteExec($cmd);
        $apachesrv->remoteExec('rm /home/' . $host->Nom . '/www/lib/init.php && mv /home/' . $host->Nom . '/www/lib/init.php.tmp /home/' . $host->Nom . '/www/lib/init.php && chown ' . $host->Nom . ':users /home/' . $host->Nom . '/www/lib/init.php');
        $out = $apachesrv->remoteExec('cat /home/' . $host->Nom . '/www/lib/init.php');
        $apachesrv->remoteExec('chmod 705 /home/' . $host->Nom . '/* -R');
        $act->addDetails($out);
        $act->Terminate(true);
        $act = $task->createActivity('Installation terminée', 'Info');
        $act->Terminate(true);
        $this->_obj->Enabled = true;
        $this->_obj->setStatus(2);
        $this->_obj->CurrentVersion = $version->Version;
        $this->_obj->Save();


        return true;
    }
    /**
     * createUpdateTask
     * Creation de la tache de mise à jour du logiciel
     */
    public function createUpdateTask($orig=null){
        //gestion depuis le plugin
        $version = VersionLogiciel::getLastVersion('Secib',$this->_obj->Type);
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Mise à jour en version '.$version->Version.' du logiciel \'Secib Web sur l\'instance ' . $this->_obj->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Instance';
        $task->TaskId = $this->_obj->Id;
        $task->TaskFunction = 'installSoftware';
        $task->addParent($this->_obj);
        $host = $this->_obj->getOneParent('Host');
        $task->addParent($host);
        $task->addParent($host->getOneParent('Server'));
        if (is_object($orig)) $task->addParent($orig);
        $task->Save();
        //changement du statut de l'instance
        $this->_obj->setStatus(3);
        return array('task'=>$task);
    }
    /**
     * updateSoftware
     * Fonction de mise à jour de l'applicatif
     * @param Object Tache
     */
    public function updateSoftware($task){

    }
    /**
     * checkState
     */
    public function checkState($task){

    }
    /**
     * rewriteConfig
     */
    public function rewriteConfig() {
        return true;
    }
}