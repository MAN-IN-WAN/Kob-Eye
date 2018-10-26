<?php

/*********************************************
*
* Module de paiement
* Crédit Mutuel
* Abtel
* 
*********************************************/

require_once( dirname(dirname(__FILE__)).'/Instance.interface.php' );

class ParcInstancePydio extends Plugin implements ParcInstancePlugin {

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
        $version = VersionLogiciel::getLastVersion('Pydio',$this->_obj->Type);
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Installation de la version '.$version->Version.' de Pydio sur l\'instance ' . $this->_obj->Nom;
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
        return true;
    }
    /**
     * installSecibWeb
     * Fonction d'installation ou de mise à jour de secib web
     * @param Object Tache
     */
    public function installSoftware($task){
        $host = $this->_obj->getOneParent('Host');
        $bdd = $host->getOneChild('Bdd');
        $mysqlsrv = $bdd->getOneParent('Server');
        $apachesrv = $host->getOneParent('Server');
        $version = VersionLogiciel::getLastVersion('Pydio',$this->_obj->Type);
        if (!is_object($version))throw new Exception('Pas de version disponible pour l\'app Pydio Type '.$this->_obj->Type);
        try {
            //Installation des fichiers
            $act = $task->createActivity('Suppression du dossier www', 'Info');
            $out = $apachesrv->remoteExec('rm -Rf /home/' . $host->NomLDAP . '/www');
            $act->addDetails($out);
            $act->Terminate(true);
            //Installation des fichiers
            $act = $task->createActivity('Initialisation de la synchronisation', 'Info');
            $cmd = 'cd /home/' . $host->NomLDAP . '/ && rsync -avz root@ws1.maninwan.fr:/home/modele-pydio/www/ www';
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
            $act = $task->createActivity('Dump de la base Mysql', 'Info');
            $cmd = 'mysqldump -h db.maninwan.fr -u modele-pydio -p02e532ba74a03544ea7208b8 modele-pydio | mysql -u '.$host->NomLDAP.' -h db.maninwan.fr -p'.$host->Password.' '.$bdd->Nom;
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            //changement du statut de l'instance
            $this->_obj->setStatus(2);
            $this->_obj->CurrentVersion = $version->Version;
            $this->_obj->Save();
        }catch (Exception $e){
            $act->addDetails('Erreur: '.$e->getMessage());
            $act->Terminate(false);
            throw new Exception($e->getMessage());
        }
        //execution de la configuration
        $act = $task->createActivity('Création de la config', 'Info');
        $act->Terminate($this->rewriteConfig());
        return true;
    }
    /**
     * createUpdateTask
     * Creation de la tache de mise à jour du logiciel
     */
    public function createUpdateTask($orig = null){
//gestion depuis le plugin
        $version = VersionLogiciel::getLastVersion('Pydio',$this->_obj->Type);
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Mise à jour en version '.$version->Version.' d\'Pydio sur l\'instance ' . $this->_obj->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Instance';
        $task->TaskId = $this->_obj->Id;
        $task->TaskFunction = 'updateSoftware';
        $task->TaskType = 'update';
        $task->addParent($this->_obj);
        $host = $this->_obj->getOneParent('Host');
        $task->addParent($host);
        $task->addParent($host->getOneParent('Server'));
        if (is_object($orig)) $task->addParent($orig);
        $task->Save();
        //changement du statut de l'instance
        $this->_obj->setStatus(3);
    }
    /**
     * updateSoftware
     * Fonction de mise à jour de l'applicatif
     * @param Object Tache
     */
    public function updateSoftware($task){
        $host = $this->_obj->getOneParent('Host');
        $bdd = $host->getOneChild('Bdd');
        $mysqlsrv = $bdd->getOneParent('Server');
        $apachesrv = $host->getOneParent('Server');
        $version = VersionLogiciel::getLastVersion('Pydio',$this->_obj->Type);
        if (!is_object($version))throw new Exception('Pas de version disponible pour l\'app Pydio Type '.$this->_obj->Type);
        try {
            //Installation des fichiers
            $act = $task->createActivity('Initialisation de la synchronisation', 'Info');
            $cmd = 'cd /home/' . $host->NomLDAP . '/ && rsync -avz root@ws1.maninwan.fr:/home/modele-pydio/www/ www';
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
            $this->_obj->CurrentVersion = $version->Version;
            $this->_obj->Save();
            return true;
        }catch (Exception $e){
            $act->addDetails('Erreur: '.$e->getMessage());
            $act->Terminate(false);
            throw new Exception($e->getMessage());
        }
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
        $hos = $this->_obj->getOneParent('Host');
        $bdd = $hos->getOneChild('Bdd');
        $ap = $hos->getOneChild('Apache');
        $srv = $hos->getOneParent('Server');
        $conf = $srv->getFileContent('/home/'.$hos->NomLDAP.'/www/data/plugins/boot.conf/bootstrap.json');
        if (!empty($conf)){
            $conf = preg_replace('#"mysql_username":"(.*)",#','"mysql_username":"'.$hos->NomLDAP.'",',$conf);
            $conf = preg_replace('#"mysql_password":"(.*)",#','"mysql_password":"'.$hos->Password.'",',$conf);
            $conf = preg_replace('#"mysql_host":"(.*)",#','"mysql_host":"db.maninwan.fr",',$conf);
            $conf = preg_replace('#"mysql_database":"(.*)",#','"mysql_database":"'.$bdd->Nom.'",',$conf);
            $srv->putFileContent('/home/'.$hos->NomLDAP.'/www/data/plugins/boot.conf/bootstrap.json',$conf);

            //suppression du cache
            $srv->remoteExec('rm /home/'.$hos->NomLDAP.'/www/data/cache/plugins_*.ser -f');
            //modification des droits
            $srv->remoteExec('chown '.$hos->NomLDAP.':users /home/'.$hos->NomLDAP.'/www -R');
        }
        return true;
    }
}