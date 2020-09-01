<?php

/*********************************************
*
* Module de paiement
* Crédit Mutuel
* Abtel
* 
*********************************************/

require_once( dirname(dirname(__FILE__)).'/Instance.interface.php' );

class ParcInstanceNextCloud extends Plugin implements ParcInstancePlugin {

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
    }
    /**
     * createInstallTask
     * Creation de la tach d'installation du secib web
     */
    public function createInstallTask(){
//gestion depuis le plugin
        $version = VersionLogiciel::getLastVersion('NextCloud',$this->_obj->Type);
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Installation de la version '.$version->Version.' de NextCloud sur l\'instance ' . $this->_obj->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskType = 'install';
        $task->TaskObject = 'Instance';
        $task->TaskId = $this->_obj->Id;
        $task->TaskFunction = 'installSoftware';
        $task->addParent($this->_obj);
        $host = $this->_obj->getOneChild('Host');
        $task->addParent($host);
        $task->addParent($host->getOneParent('Server'));
        $task->Save();
        return array('task'=>$task);
    }
    /**
     * installNextCloud
     * Fonction d'installation ou de mise à jour de NextCloud
     * @param Object Tache
     */
    public function installSoftware($task){
        $host = $this->_obj->getOneChild('Host');
        $bdd = $host->getOneChild('Bdd');
        $mysqlsrv = $bdd->getOneParent('Server');
        $apachesrv = $host->getOneParent('Server');
        try {
            //Installation des fichiers
            $act = $task->createActivity('Suppression du dossier www', 'Info');
            $out = $apachesrv->remoteExec('rm -Rf /home/' . $host->NomLDAP . '/www');
            $act->addDetails($out);
            $act->Terminate(true);
            //Installation des fichiers
            $modele = Sys::getOneData('Parc','Host/Nom=modele-nextcloud');
            $srv = $modele->getOneParent('Server');
            $url = $srv->IP;

            $act = $task->createActivity('Initialisation de la synchronisation', 'Info');
            $cmd = 'cd /home/' . $host->NomLDAP . '/ && rsync -avz root@'.$url.':/home/modele-nextcloud/www/ www';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            $act = $task->createActivity('Modification des droits', 'Info');
            $cmd = 'chown ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . '/www -R';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);

            //Dump de la base
            $act = $task->createActivity('Dump de la base Mysql', 'Info', $task);
            $mysqlhostModel = "185.87.64.121";
            $mysqlhost =  $mysqlsrv->IP;
            $cmd = 'mysqldump -h '.$mysqlhostModel.' -u modele-nextcloud -p3c0207175b7ea9ffc9e94200 modele-nextcloud | mysql -u '.$host->NomLDAP.' -h '.$mysqlhost.' -p'.$host->Password.' '.$bdd->Nom;
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            $act = $task->createActivity('Regen config', 'Info');
            $this->rewriteConfig();
            $act->addDetails('OK');
            $act->Terminate(true);

            $act = $task->createActivity('Mot de passe administrateur', 'Info');
            $cmd = 'su - ' . $host->NomLDAP . ' -c "export OC_PASS='.$host->Password.' && /home/' . $host->NomLDAP . '/www/occ user:resetpassword --password-from-env admin"';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $act->Terminate(true);

            //changement du statut de l'instance
            $this->_obj->setStatus(2);
            $this->_obj->Save();

            $task->Terminate(true);
            return true;
        }catch (Exception $e){
            $act->addDetails('Erreur: '.$e->getMessage());
            $act->Terminate(false);
            $task->Terminate(false);
            throw new Exception($e->getMessage());
        }
    }
    /**
     * createUpdateTask
     * Creation de la tache de mise à jour du logiciel
     */
    public function createUpdateTask($orig = null){
//gestion depuis le plugin
        /*$version = VersionLogiciel::getLastVersion('NextCloud',$this->_obj->Type);
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Mise à jour en version '.$version->Version.' d\'NextCloud sur l\'instance ' . $this->_obj->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Instance';
        $task->TaskType = 'update';
        $task->TaskId = $this->_obj->Id;
        $task->TaskFunction = 'updateSoftware';
        $task->addParent($this->_obj);
        $host = $this->_obj->getOneChild('Host');
        $task->addParent($host);
        $task->addParent($host->getOneParent('Server'));
        if (is_object($orig)) $task->addParent($orig);
        $task->Save();
        //changement du statut de l'instance
        $this->_obj->setStatus(3);
        return array('task'=>$task);*/
    }
    /**
     * updateSoftware
     * Fonction de mise à jour de l'applicatif
     * @param Object Tache
     */
    public function updateSoftware($task){
        /*$host = $this->_obj->getOneChild('Host');
        $bdd = $host->getOneChild('Bdd');
        $mysqlsrv = $bdd->getOneParent('Server');
        $apachesrv = $host->getOneParent('Server');
        try {
            //Installation des fichiers
            $modele = Sys::getOneData('Parc','Host/Nom=modele-nextcloud');
            $srv = $modele->getOneParent('Server');
            $url = $srv->IP;

            $act = $task->createActivity('Initialisation de la synchronisation', 'Info');
            $cmd = 'cd /home/' . $host->NomLDAP . '/ && rsync -avz root@'.$url.':/home/modele-nextcloud/www/ www';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            $act = $task->createActivity('Modification des droits', 'Info');
            $cmd = 'chown ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . '/www -R';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            //changement du statut de l'instance
            $this->_obj->setStatus(2);
            $this->_obj->Save();
            return true;
        }catch (Exception $e){
            $act->addDetails('Erreur: '.$e->getMessage());
            $act->Terminate(false);
            throw new Exception($e->getMessage());
        }*/
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
        $hos = $this->_obj->getOneChild('Host');
        $srv = $hos->getOneParent('Server');
        $bdd = $hos->getOneChild('Bdd');
        $mysqlsrv = $bdd->getOneParent('Server');
        $mysqlhost =  $mysqlsrv->IP;
        if (!$bdd){
            $this->_obj->addError(array('Message'=>'Base de donnée introuvable'));
            //return false;
        }else $mysqlsrv = $bdd->getOneParent('Server');
        $conf = $srv->getFileContent('/home/'.$hos->NomLDAP.'/www/config/config.php');

        if (!empty($conf)){
            $conf = preg_replace('#\'dbname\' => \'modele-nextcloud\',#','\'dbname\' => \''.$bdd->Nom.'\',',$conf);
            $conf = preg_replace('#\'dbuser\' => \'modele-nextcloud\',#','\'dbuser\' => \''.$hos->NomLDAP.'\',',$conf);
            $conf = preg_replace('#\'dbhost\' => \'db.abtel.fr\',#','\'dbhost\' => \''.$mysqlhost.'\',',$conf);
            $conf = preg_replace('#\'dbpassword\' => \'3c0207175b7ea9ffc9e94200\',#','\'dbpassword\' => \''.$hos->Password.'\',',$conf);
            $conf = preg_replace('#\'datadirectory\' => \'/home/modele-nextcloud/www/data\',#',"'datadirectory' => '/home/".$hos->NomLDAP."/www/data',",$conf);
            $conf = preg_replace('#0 => \'modele-nextcloud.abtel.fr\',#','0 => \''.$hos->NomLDAP.'.abtel.fr\',',$conf);
            $conf = preg_replace('#\'overwrite.cli.url\' => \'http://modele-nextcloud.abtel.fr\',#','\'overwrite.cli.url\' => \'http://'.$hos->NomLDAP.'.abtel.fr\',',$conf);
            $srv->putFileContent('/home/'.$hos->NomLDAP.'/www/config/config.php',$conf);
        }
        return true;
    }
}
