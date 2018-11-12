<?php

/*********************************************
*
* Module de paiement
* Crédit Mutuel
* Abtel
* 
*********************************************/

require_once( dirname(dirname(__FILE__)).'/Instance.interface.php' );

class ParcInstanceDolibarr extends Plugin implements ParcInstancePlugin {

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
        //modification du apache
        $host = $this->_obj->getOneParent('Host');
        $apache = $host->getOneChild('Apache');
        $apache->DocumentRoot = 'www/htdocs';
        $apache->Save();
    }
    /**
     * createInstallTask
     * Creation de la tach d'installation du secib web
     */
    public function createInstallTask(){
//gestion depuis le plugin
        $version = VersionLogiciel::getLastVersion('Dolibarr',$this->_obj->Type);
        $task = genericClass::createInstance('Parc', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Installation de Dolibarr sur l\'instance ' . $this->_obj->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskType = 'install';
        $task->TaskObject = 'Instance';
        $task->TaskId = $this->_obj->Id;
        $task->TaskFunction = 'installSoftware';
        $task->addParent($this->_obj);
        $host = $this->_obj->getOneParent('Host');
        $task->addParent($host);
        $task->addParent($host->getOneParent('Server'));
        $task->Save();
        return array('task'=>$task);
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
        try {
            //Installation des fichiers
            $act = $task->createActivity('Suppression du dossier www', 'Info');
            $out = $apachesrv->remoteExec('rm -Rf /home/' . $host->NomLDAP . '/www');
            $act->addDetails($out);
            $act->Terminate(true);
            //Installation des fichiers
            $act = $task->createActivity('Initialisation de la synchronisation', 'Info');
            $cmd = 'cd /home/' . $host->NomLDAP . '/ && rsync -avz root@ws1.maninwan.fr:/home/modele-dolibarr/www/ www';
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
            $cmd = 'mysqldump -h db.maninwan.fr -u modele-dolibarr -pD4nsT0n208 modele-dolibarr | mysql -u '.$host->NomLDAP.' -h db.maninwan.fr -p'.$host->Password.' '.$bdd->Nom;
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            $act = $task->createActivity('Mot de passe administrateur', 'Info');
            $cmd = 'mysql -u '.$host->NomLDAP.' -h db.maninwan.fr -p'.$host->Password.' '.$bdd->Nom.' -e "UPDATE llx_user SET pass_crypted=\'\', pass=\''.$host->Password.'\' WHERE rowid=1"';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            //configuration
            $act = $task->createActivity('Configuration', 'Info');
            $act->Terminate($this->rewriteConfig());
            //changement du statut de l'instance
            $this->_obj->setStatus(2);
            $this->_obj->Save();
            return true;
        }catch (Exception $e){
            $act->addDetails('Erreur: '.$e->getMessage());
            $act->Terminate(false);
            throw new Exception($e->getMessage());
        }catch (Error $e){
            $act->addDetails('Erreur: '.$e->getMessage());
            $act->Terminate(false);
            throw new Exception($e->getMessage());
        }
    }
    /**
     * createUpdateTask
     * Creation de la tache de mise à jour du logiciel
     */
    public function createUpdateTask($orig = null){
//gestion depuis le plugin
        $task = genericClass::createInstance('Parc', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Mise à jour  de Dolibarr sur l\'instance ' . $this->_obj->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Instance';
        $task->TaskType = 'update';
        $task->TaskId = $this->_obj->Id;
        $task->TaskFunction = 'updateSoftware';
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
        $host = $this->_obj->getOneParent('Host');
        $bdd = $host->getOneChild('Bdd');
        $mysqlsrv = $bdd->getOneParent('Server');
        $apachesrv = $host->getOneParent('Server');
        try {
            //Installation des fichiers
            $act = $task->createActivity('Initialisation de la synchronisation', 'Info');
            $cmd = 'cd /home/' . $host->NomLDAP . '/ && rsync -avz root@ws1.maninwan.fr:/home/modele-dolibarr/www/ www';
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
        }
    }
    /**
     * checkState
     */
    public function checkState(){

    }
    /**
     * rewriteConfig
     */
    public function rewriteConfig() {
        $hos = $this->_obj->getOneParent('Host');
        $srv = $hos->getOneParent('Server');
        $conf = $srv->getFileContent('/home/'.$hos->NomLDAP.'/www/htdocs/conf/conf.php');
        if (!empty($conf)){
            $conf = preg_replace('#\$dolibarr_main_url_root=.*#','$dolibarr_main_url_root=\'http://'.$this->_obj->FullDomain.'\';',$conf);
            $conf = preg_replace('#\$dolibarr_main_document_root=.*#','$dolibarr_main_document_root=\'/home/'.$hos->NomLDAP.'/www/htdocs\';',$conf);
            $conf = preg_replace('#\$dolibarr_main_document_root_alt=.*#','$dolibarr_main_document_root_alt=\'/home/'.$hos->NomLDAP.'/www/htdocs/custom\';',$conf);
            $conf = preg_replace('#\$dolibarr_main_data_root=.*#','$dolibarr_main_data_root=\'/home/'.$hos->NomLDAP.'/www/documents\';',$conf);
            $conf = preg_replace('#\$dolibarr_main_db_name=.*#','$dolibarr_main_db_name=\''.$hos->NomLDAP.'\';',$conf);
            $conf = preg_replace('#\$dolibarr_main_db_user=.*#','$dolibarr_main_db_user=\''.$hos->NomLDAP.'\';',$conf);
            $conf = preg_replace('#\$dolibarr_main_db_pass=.*#','$dolibarr_main_db_pass=\''.$hos->Password.'\';',$conf);
            $srv->putFileContent('/home/'.$hos->NomLDAP.'/www/htdocs/conf/conf.php',$conf);
        }
        return true;
    }
}