<?php

class InstanceSecib extends genericClass
{

    public function Save()
    {
        $old = Sys::getOneData('Parc', 'InstanceSecib/' . $this->Id);
        parent::Save();
        //serveurs par défaut
        $apachesrv = Sys::getOneData('Parc', 'Server/Web=1&defaultWebServer=1');
        $mysqlsrv = Sys::getOneData('Parc', 'Server/Sql=1&defaultSqlServer=1');
        if (!$apachesrv) {
            $GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Il n\'y a pas de serveur apache par défaut. Veuillez en définir un.'));
            return false;
        }
        if (!$mysqlsrv) {
            $GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Il n\'y a pas de serveur mysql par défaut. Veuillez en définir un.'));
            return false;
        }


        //Verification du nom
        if ($this->Nom != Subdomain::checkName($this->Nom)) {
            $GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Le nom de l\'instance ne doit pas contenir de caractère spéciaux.'));
            return false;
        }

        //Check du client
        $client = $this->getOneParent('Client');
        if (!$client&&!$this->ClientCreateAuto) {
            $GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->Delete();
            $this->addError(Array("Message" => 'Une instance de Secib web doit être liée à un client'));
            return false;
        }elseif ($this->ClientCreateAuto){
            //test existence
            $client = Sys::getOneData('Parc','Client/NomLDAP='.$this->Nom);
            if (!$client){
                $client = genericClass::createInstance('Parc', 'Client');
                $client->NomLDAP = $client->Nom = $this->Nom;
                $client->Save();
            }
            $this->addParent($client);
            parent::Save();
        }

        //Check du domaine
        $dom = $this->getParents('Domain');
        if (!is_array($dom) || !sizeof($dom)) {

            $GLOBALS["Systeme"]->Db[0]->exec('ROLLBACK');
            $this->addError(Array("Message" => 'Une instance de Secib web doit être liée à un ou plusieurs domaines'));
            return false;
        } else {
            $defaulturl = false;
            $otherurls = array();
            foreach ($dom as $d) {
                //vérification des sous domaines
                $www = $d->getOneChild('Subdomain/Url=A:' . $this->Nom);
                if (!$www) {
                    //création du A
                    $www = genericClass::createInstance('Parc', 'Subdomain');
                    $www->Url = 'A:' . $this->Nom;
                    $www->IP = '95.143.73.50';
                } else {
                    $www->IP = '95.143.73.50';
                }
                $www->addParent($d);
                $www->Save();
                if (!$defaulturl) $defaulturl = $this->Nom . '.' . $d->Url;
                else $otherurls[] = $this->Nom . '.' . $d->Url;
            }
        }

        //Check de l'hébergement
        $heb = $this->getOneParent('Host');
        if (!$heb) {
            //alors création de l'hébergement
            $heb = genericClass::createInstance('Parc', 'Host');
            $heb->Nom = $this->Nom;
            $heb->Production = true;
            $heb->PHPVersion = '7.0.2';
            $heb->addParent($apachesrv);
            $heb->addParent($client);
            $heb->Save();
            $this->addParent($heb);
        } else {
            $heb->Production = true;
            $heb->PHPVersion = '7.0.2';
            $heb->addParent($apachesrv);
            $heb->addParent($client);
            $heb->Save();
        }

        //check apache
        $apache = $heb->getOneChild('Apache');
        if (!$apache) {
            //alors création du apache
            $apache = genericClass::createInstance('Parc', 'Apache');
            $apache->DocumentRoot = 'www';
            $apache->ApacheServerName = $defaulturl;
            $apache->ApacheServerAlias = implode(' ', $otherurls);
            $apache->Actif = true;
            $apache->addParent($heb);
            $apache->Save();
        } else {
            $apache->ApacheServerName = $defaulturl;
            $apache->ApacheServerAlias = implode(' ', $otherurls);
            $apache->Actif = true;
            $apache->addParent($heb);
            $apache->Save();
        }

        //check bdd
        $bdd = $heb->getOneChild('Bdd');
        if (!$bdd) {
            //alors création du apache
            $bdd = genericClass::createInstance('Parc', 'Bdd');
            $bdd->Nom = $this->Nom;
            $bdd->addParent($heb);
            $bdd->addParent($mysqlsrv);
            $bdd->Save();
        } else {
            $bdd->addParent($heb);
            $bdd->Save();
        }
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
     * createInstallTask
     * Creation de la tach d'installation du secib web
     */
    public function createInstallTask()
    {
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Installation du logiciel Secib Web sur l\'instance ' . $this->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'InstanceSecib';
        $task->TaskId = $this->Id;
        $task->TaskFunction = 'installSecibWeb';
        $task->addParent($this);
        $host = $this->getOneParent('Host');
        $task->addParent($host);
        $task->addParent($host->getOneParent('Server'));
        $task->Save();
    }

    public function Delete()
    {
        $host = $this->getOneParent('Host');
        if ($host)$host->Delete();
        parent::Delete();

    }


    /**
     * installSecibWeb
     * Fonction d'installation ou de mise à jour de secib web
     * @param Object Tache
     */
    public function installSecibWeb($task )
    {
        $apachesrv = Sys::getOneData('Parc', 'Server/Web=1&defaultWebServer=1');
        $mysqlsrv = Sys::getOneData('Parc', 'Server/Sql=1&defaultSqlServer=1');
        $host = $this->getOneParent('Host');
        $version = $this->getOneParent('VersionLogiciel');
        if (!$version) {
            $act = $task->createActivity('Erreur pas de version correpsondante', 'Info');
            $act->Terminate(false);
            return false;
        }
        $bdd = $host->getOneChild('Bdd');
        $apache = $host->getOneChild('Apache');

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
        //mise à jour de la base de donnée des domaines
        $domains = $apache->ApacheServerName . ' ' . $apache->ApacheServerAlias;
        $domains = explode(' ', $domains);

        $act->addDetails($SQLInit);
        $db->query($SQLInit);
        $db->query('TRUNCATE secibweb_webservice');
        $act->addDetails('TRUNCATE secibweb_webservice');
        $db->query('TRUNCATE secibweb_domaine');
        $act->addDetails('TRUNCATE secibweb_domaine');
        $sql = 'INSERT INTO `secibweb_domaine` (`dom_domaine`, `dom_ws_id`, `dom_lg`, `dom_actif`, `dom_crea_date`, `dom_crea_user_id`, `dom_modif_date`, `dom_modif_user_id`, `dom_ta_id`, `dom_url_wopi`) VALUES';
        $flag = false;
        foreach ($domains as $d) {
            if ($flag) $sql .= ',';
            $sql .= '(\'' . $d . '\', 1, \''.$this->Langue.'\', 1, \'' . date('Y-m-d') . ' 10:58:00\', 0, \'' . date('Y-m-d') . ' 10:58:00\', 0, '.$this->TypeApplication.',\''.$this->UrlWopi.'\')';
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
        $act = $task->createActivity('Téléchargement des fichiers', 'Info');
        $out = $apachesrv->remoteExec('wget -v http://management.secib.fr/' . $version->Fichier . ' -O /home/' . $host->Nom . '/version.zip');
        $act->addDetails('wget -v http://management.secib.fr/' . $version->Fichier . ' -O /home/' . $host->Nom . '/version.zip');
        $act->Terminate(true);
        $act = $task->createActivity('Extraction des fichiers', 'Info');
        $out = $apachesrv->remoteExec('cd /home/' . $host->Nom . '/ && unzip -o version.zip && chown ' . $this->Nom . ':users * -R');
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
        $this->Enabled = true;
        parent::Save();
        return true;
    }

    public function enableSsl($force = false){
        //recherche du apache correspondant
        $host = $this->getOneParent('Host');
        $apache = $host->getOneChild('Apache');
        $out =  $apache->enableSsl($force,$this);
        $this->Error = $apache->Error;
    }
}