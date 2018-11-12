<?php

/*********************************************
*
* Module de paiement
* Crédit Mutuel
* Abtel
* 
*********************************************/

require_once( dirname(dirname(__FILE__)).'/Instance.interface.php' );

class ParcInstanceAzkoBack extends Plugin implements ParcInstancePlugin {

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
     * Creation de la tach d'installation de l'applicatif
     */
    public function createInstallTask(){
    }
    /**
     * installAzkoBack
     * Fonction d'installation ou de mise à jour de secib web
     * @param Object Tache
     */
    public function installSoftware($task = null){
    }
    /**
     * createUpdateTask
     * Creation de la tache de mise à jour
     */
    public function createUpdateTask($orig=null){
        //gestion depuis le plugin
        $version = VersionLogiciel::getLastVersion('AzkoBack',$this->_obj->Type);
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Mise à jour en version '.$version->Version.' d\'AzkoBack sur l\'instance ' . $this->_obj->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Instance';
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
     * @return bool
     */
    public function updateSoftware($task){
        $apachesrv = Sys::getOneData('Parc', 'Server/Web=1&defaultWebServer=1');
        $mysqlsrv = Sys::getOneData('Parc', 'Server/Sql=1&defaultSqlServer=1');
        $host = $this->_obj->getOneParent('Host');
        $bdd = $host->getOneChild('Bdd');
        $apache = $host->getOneChild('Apache');
        $version = VersionLogiciel::getLastVersion('AzkoBack',$this->_obj->Type);
        if (!is_object($version))throw new Exception('Pas de version disponible pour l\'app AzkoBack Type '.$this->_obj->Type);
        try {
            $act = $task->createActivity('Initialisation du git clone', 'Info');
            $cmd = 'cd /home/' . $host->NomLDAP . '/www && git remote -v';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            if (!empty($out)) {
                $cmd = 'cd /home/' . $host->NomLDAP . '/www && git remote rm origin';
                $act->addDetails($cmd);
                $out = $apachesrv->remoteExec($cmd);
                $act->addDetails($out);
            }
            $cmd = 'cd /home/' . $host->NomLDAP . '/www && git remote add origin '.$version->GitUrl;
            $act->addDetails($cmd);
            $out .= $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $cmd = 'cd /home/' . $host->NomLDAP . '/www && git fetch';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            if (!empty($version->GitBranche))
                $cmd = 'cd /home/' . $host->NomLDAP . '/www && git reset --hard origin/'.$version->GitBranche;
            else $cmd = 'cd /home/' . $host->NomLDAP . '/www && git reset --hard origin/master';
            $act->addDetails($cmd);
            $out .= $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            $act = $task->createActivity('Modification des droits', 'Info');
            $cmd = 'chown ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . '/www -R';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            //changement du statut de l'instance
            $this->_obj->Status = 2;
            $this->_obj->CurrentVersion = $version;
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
        return true;

    }

}