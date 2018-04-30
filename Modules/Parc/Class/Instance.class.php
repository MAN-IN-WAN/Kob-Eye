<?php

class Instance extends genericClass{

    public function Save(){
        $old = Sys::getOneData('Parc', 'Instance/' . $this->Id);
        if (!$old) $new = true; else $new = false;
        if (!$new&&$old->SousDomaine!=$this->SousDomaine){
            $this->addError(Array("Message" => 'Impossible de modifier le sous domaine d\'une instance. Veuillez dupliquer ou créer une nouvelle instance.'));
            return false;
        }
        //serveurs par défaut
        $apachesrv = Sys::getOneData('Parc', 'Server/Web=1&defaultWebServer=1');
        $mysqlsrv = Sys::getOneData('Parc', 'Server/Sql=1&defaultSqlServer=1');
        $dom = Sys::getOneData('Parc','Domain/defaultDomain=1',0,1,'','','','',true);

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
        if ($this->SousDomaine != Subdomain::checkName($this->SousDomaine)) {
            $GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Le sous-domaine de l\'instance ne doit pas contenir de caractère spéciaux.'));
            return false;
        }

        parent::Save();
        //Check du client
        $client = $this->getOneParent('Client');
        if (!$client) {
            /*$GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Une instance doit être liée à un client. Si il n\'existe pas , veuillez le créer au préalable.'));
            return false;*/
            //création d'un client par défaut
            $client = genericClass::createInstance('Parc','Client');
            $client->Nom = $this->Nom;
            $client->Save();
            $this->addParent($client);
            parent::Save();
        }

        //Vérification du mot de passe
        if (empty($this->Password)){
            $this->Password = str_shuffle(bin2hex(openssl_random_pseudo_bytes(12)));
        }

        //creation du nom temporaire
        $tmpname = 'instance-'.$client->NomLDAP;

        //vérification de l'existence
        $as = Sys::getOneData('Parc','Domain/'.$dom->Id.'/Subdomain/Url='.$tmpname);
        if (!$as){
            $as = genericClass::createInstance('Parc','Subdomain');
            $as->Url = $tmpname;
            $as->IP = $apachesrv->IP;
            $as->addParent($dom);
            $as->Save();
        }
        $this->FullDomain = $as->Url.'.'.$dom->Url;
        parent::Save();

        //Check des domaine
        $dom = $this->getParents('Domain');
        if (!is_array($dom) || !sizeof($dom)) {
            /*$GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Une instance doit être liée à un ou plusieurs domaines'));
            return false;*/
        } else {
            $defaulturl = false;
            $otherurls = array();
            foreach ($dom as $d) {
                //vérification des sous domaines
                $www = $d->getOneChild('Subdomain/Url=A:' . $this->Nom);
                if (!$www) {
                    //création du A
                    $www = genericClass::createInstance('Parc', 'Subdomain');
                    $www->Url = 'A:' . $this->SousDomaine;
                    $www->IP = $apachesrv->IP;
                } else {
                    $www->IP = $apachesrv->IP;
                }
                $www->addParent($d);
                $www->Save();
                if (!$defaulturl) $defaulturl = $this->SousDomaine . '.' . $d->Url;
                else $otherurls[] = $this->SousDomaine . '.' . $d->Url;
            }
        }

        //Check de l'hébergement
        $heb = $this->getOneParent('Host');
        if (!$heb) {
            //alors création de l'hébergement
            $heb = genericClass::createInstance('Parc', 'Host');
            $heb->Nom = $tmpname;
            $heb->Production = true;
            $heb->PHPVersion = $this->PHPVersion;
            $heb->addParent($apachesrv);
            $heb->addParent($client);
            $heb->Save();
            $this->addParent($heb);
        } else {
            $heb->Production = true;
            $heb->PHPVersion = $this->PHPVersion;
            $heb->addParent($apachesrv);
            $heb->addParent($client);
            $heb->Save();
        }

        //check apache
        $apache = $heb->getOneChild('Apache');
        if (!$apache) {
            //alors création du apache
            $apache = genericClass::createInstance('Parc', 'Apache');
            $apache->DocumentRoot = $this->SousDomaine;
            $apache->ApacheServerName = $this->FullDomain;
            $apache->ApacheServerAlias = implode(' ', $otherurls);
            $apache->Actif = true;
            $apache->addParent($heb);
            $apache->Save();
        } else {
            $apache->ApacheServerName = $this->FullDomain;
            $apache->ApacheServerAlias = $defaulturl.' '.implode(' ', $otherurls);
            $apache->Actif = true;
            $apache->addParent($heb);
            $apache->Save();
        }

        //check bdd
        $bdd = $heb->getOneChild('Bdd');
        if (!$bdd) {
            //alors création du apache
            $bdd = genericClass::createInstance('Parc', 'Bdd');
            $bdd->Nom = $tmpname;
            $bdd->addParent($heb);
            $bdd->addParent($mysqlsrv);
            $bdd->Save();
        } else {
            $bdd->addParent($heb);
            $bdd->Save();
        }
        $this->addParent($bdd);

        //check ftp
        $bdd = $heb->getOneChild('Ftpuser');
        if (!$bdd) {
            //alors création du apache
            $bdd = genericClass::createInstance('Parc', 'Ftpuser');
            $bdd->Nom = $tmpname;
            $bdd->addParent($heb);
            $bdd->addParent($mysqlsrv);
            $bdd->Save();
        } else {
            $bdd->addParent($heb);
            $bdd->Save();
        }
        $this->addParent($bdd);
        parent::Save();
        //if (!$this->Enabled||$old->VersionId!=$this->VersionId||$old->Type!=$this->Type){
        //$this->Enabled = false;
        $apachesrv->callLdap2Service();
        $this->createInstallTask();
        parent::Save();
        //}
        if ($this->EnableSsl)
            $apache->enableSsl(false,$this);
        $this->Error = $apache->Error;
        return true;
    }
    /**
     * Retourne un plugin Boutique / Instance
     * @return	Implémentation d'interface
     */
    public function getPlugin() {
        $plugin = Plugin::createInstance('Boutique','TypePaiement', $this->Plugin);
        $plugin->setConfig( $this->PluginConfig );
        return $plugin;
    }


    /**
     * createInstallTask
     * Creation de la tach d'installation du secib web
     */
    public function createInstallTask(){
        //gestion depuis le plugin
        //TODO
        /*$task = genericClass::createInstance('Parc', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Installation du logiciel Secib Web sur l\'instance ' . $this->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Instance';
        $task->TaskId = $this->Id;
        $task->TaskFunction = 'installSecibWeb';
        $task->addParent($this);
        $host = $this->getOneParent('Host');
        $task->addParent($host);
        $task->addParent($host->getOneParent('Server'));
        $task->Save();*/
    }

    public function Delete(){
        //suppression hébergement
        $host = $this->getOneParent('Host');
        if ($host)
            $host->Delete();
        parent::Delete();
    }

    /**
     * createActivity
     * créé une activité en liaison avec l'esx
     * @param $title
     * @param null $obj
     * @param int $jPSpan
     * @param string $Type
     * @return genericClass
     */
    public function createActivity($title, $Type = 'Exec', $Task = null)
    {
        $act = genericClass::createInstance('Parc', 'Activity');
        $host = $this->getOneParent('Host');
        $srv = $host->getOneParent('Server');
        $act->addParent($this);
        $act->addParent($host);
        $act->addParent($srv);
        if ($Task) $act->addParent($Task);
        $act->Titre = $this->tag . date('d/m/Y H:i:s') . ' > ' . $this->Titre . ' > ' . $title;
        $act->Started = true;
        $act->Type = $Type;
        $act->Progression = 0;
        $act->Save();
        return $act;
    }

    /**
     * installSecibWeb
     * Fonction d'installation ou de mise à jour de secib web
     * @param Object Tache
     */
    public function installSecibWeb($task = null){
        //gestion depuis le plugin
        //TODO
        /*$apachesrv = Sys::getOneData('Parc', 'Server/Web=1&defaultWebServer=1');
        $mysqlsrv = Sys::getOneData('Parc', 'Server/Sql=1&defaultSqlServer=1');
        $host = $this->getOneParent('Host');
        $version = $this->getOneParent('VersionLogiciel');
        if (!$version) {
            $act = $this->createActivity('Erreur pas de version correpsondante', 'Info');
            $act->Terminate(false);
            return false;
        }
        $bdd = $host->getOneChild('Bdd');
        $apache = $host->getOneChild('Apache');

        //Connexion à la base de donnée
        $act = $this->createActivity('Initialisation de la base de donnée', 'Info', $task);
        $db = new PDO('mysql:host=' . $mysqlsrv->InternalIP . ';dbname=' . $bdd->Nom, $host->Nom, $host->Password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $db->query("SET AUTOCOMMIT=1");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //mise à jour de la base de donnée des domaines
        $domains = $apache->ApacheServerName . ' ' . $apache->ApacheServerAlias;
        $domains = explode(' ', $domains);
        $act->addDetails($version->SQLInit);
        $db->query($version->SQLInit);
        $db->query('TRUNCATE secibweb_domaine');
        $db->query('TRUNCATE secibweb_webservice');
        $sql = 'INSERT INTO `secibweb_domaine` (`dom_domaine`, `dom_ws_id`, `dom_lg`, `dom_actif`, `dom_crea_date`, `dom_crea_user_id`, `dom_modif_date`, `dom_modif_user_id`) VALUES';
        $flag = false;
        foreach ($domains as $d) {
            if ($flag) $sql .= ',';
            $sql .= '(\'' . $d . '\', 1, \'fr\', 1, \'' . date('Y-m-d') . ' 10:58:00\', 0, \'' . date('Y-m-d') . ' 10:58:00\', 0)';
            $flag = true;
        }
        $act->addDetails($sql);
        $db->query($sql);
        $sql = 'INSERT INTO `secibweb_webservice` (`ws_id`,`ws_nom`, `ws_url`, `ws_guid`, `ws_crea_date`, `ws_crea_user_id`, `ws_modif_date`, `ws_modif_user_id`) VALUES
(1,\'Intégration\', \'' . $this->WebService . '\', \'' . $this->Guid . '\', \'' . date('Y-m-d') . ' 09:40:00\', 0, \'' . date('Y-m-d') . ' 09:40:00\', 0);';
        $act->addDetails($sql);
        $db->query($sql);
        $act->Terminate(true);
        //Installation des fichiers
        $act = $this->createActivity('Téléchargement des fichiers', 'Info', $task);
        $out = $apachesrv->remoteExec('wget -v http://management.secib.fr/' . $version->Fichier . ' -O /home/' . $host->Nom . '/version.zip');
        $act->addDetails('wget -v http://management.secib.fr/' . $version->Fichier . ' -O /home/' . $host->Nom . '/version.zip');
        $act->Terminate(true);
        $act = $this->createActivity('Extraction des fichiers', 'Info', $task);
        $out = $apachesrv->remoteExec('cd /home/' . $host->Nom . '/ && unzip -o version.zip && chown ' . $this->Nom . ':users * -R');
        $act->addDetails($out);
        $act->Terminate(true);
        //Saisie du fichier de configuration
        $act = $this->createActivity('Modification du fichier de config', 'Info', $task);
        //db host
        $cmd = 'cat /home/' . $host->Nom . '/www/lib/init.php | sed -e \'s/oConfig->DB_PARAMS_ONLINE.*$/oConfig->DB_PARAMS_ONLINE = array("db_host" => "' . $mysqlsrv->IP . '" , "db_user" => "' . $host->Nom . '", "db_pass" => "' . $host->Password . '"  , "db_name" => "' . $bdd->Nom . '"); /\' > /home/' . $host->Nom . '/www/lib/init.php.tmp';
        $act->addDetails($cmd);
        $out = $apachesrv->remoteExec($cmd);
        $apachesrv->remoteExec('rm /home/' . $host->Nom . '/www/lib/init.php && mv /home/' . $host->Nom . '/www/lib/init.php.tmp /home/' . $host->Nom . '/www/lib/init.php && chown ' . $host->Nom . ':users /home/' . $host->Nom . '/www/lib/init.php');
        $out = $apachesrv->remoteExec('cat /home/' . $host->Nom . '/www/lib/init.php');
        $apachesrv->remoteExec('chmod 705 /home/' . $host->Nom . '/* -R');
        $act->addDetails($out);
        $act->Terminate(true);
        $act = $this->createActivity('Installation terminée', 'Info', $task);
        $act->Terminate(true);
        $this->Enabled = true;
        parent::Save();
        return true;*/
    }

    public function enableSsl($force = false){
        //recherche du apache correspondant
        $host = $this->getOneParent('Host');
        $apache = $host->getOneChild('Apache');
        $out =  $apache->enableSsl($force,$this);
        $this->Error = $apache->Error;
    }
}