<?php

/*********************************************
*
* Module de paiement
* Crédit Mutuel
* Abtel
* 
*********************************************/

require_once( dirname(dirname(__FILE__)).'/Instance.interface.php' );

class ParcInstanceVide extends Plugin implements ParcInstancePlugin {

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
    /**
     * installSecibWeb
     * Fonction d'installation ou de mise à jour de secib web
     * @param Object Tache
     */
    public function installSoftware($task = null){
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

}