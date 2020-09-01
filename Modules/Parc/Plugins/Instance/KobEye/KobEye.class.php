<?php

/*********************************************
*
* Module de paiement
* Crédit Mutuel
* Abtel
* 
*********************************************/

require_once( dirname(dirname(__FILE__)).'/Instance.interface.php' );

class ParcInstanceKobEye extends Plugin implements ParcInstancePlugin {

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
    }
    /**
     * installSecibWeb
     * Fonction d'installation ou de mise à jour de secib web
     * @param Object Tache
     */
    public function installSoftware($task){
        $host = $this->_obj->getOneChild('Host');
        $bdd = $host->getOneChild('Bdd');
        $mysqlsrv = $bdd->getOneParent('Server');
        $apachesrv = $host->getOneParent('Server');

        try {
            //Installation des fichiers
            $act = $task->createActivity('Suppression du dossier www', 'Info', $task);
            $out = $apachesrv->remoteExec('rm -Rf /home/' . $host->NomLDAP . '/www');
            $act->addDetails($out);
            $act->Terminate(true);
            //Installation des fichiers
            $act = $task->createActivity('Initialisation de la synchronisation', 'Info', $task);

            $cmd = 'cd /home/' . $host->NomLDAP . '/www && git clone https://github.com/MAN-IN-WAN/Kob-Eye.git';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            $act = $task->createActivity('Modification des droits', 'Info', $task);
            $cmd = 'chown ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . '/www -R';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);

            //Generation de la conf
            $act = $task->createActivity('Génération fichier Conf', 'Info', $task);
            $cmd = 'cp -p /home/' . $host->NomLDAP . '/www/Conf/General.conf.tpl  /home/' . $host->NomLDAP . '/www/Conf/General.conf';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            if($this->rewriteConfig()){
                $act->Terminate(true);
            } else{
                $act->Terminate(false);
            }
            
            //Creation de la base
            $act = $task->createActivity('ACTION UPDATE', 'Info', $task);
            $cmd = 'curl -kv '.$this->_obj->FullDomain.'?ACTION=UPDATE';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $act->Terminate(true);



            //changement du statut de l'instance
            $this->_obj->setStatus(2);
            $this->_obj->CurrentVersion = date('Ymd');
            $this->_obj->Save();
        }catch (Exception $e){
            $act->addDetails('Erreur: '.$e->getMessage());
            $act->Terminate(false);
            throw new Exception($e->getMessage());
        }
        //execution de la configuration
        $act = $task->createActivity('Création de la config', 'Info', $task);
        $act->Terminate($this->rewriteConfig());
        return true;
    }
    /**
     * createUpdateTask
     * Creation de la tache de mise à jour du logiciel
     */
    public function createUpdateTask(){

    }
    /**
     * updateSoftware
     * Fonction de mise à jour de l'applicatif
     * @param Object Tache
     */
    public function updateSoftware($task = null){

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
        $host = $this->_obj->getOneChild('Host');
        $bdd = $host->getOneChild('Bdd');
        $mysqlsrv = $bdd->getOneParent('Server');
        $apachesrv = $host->getOneParent('Server');
        $conf = $apachesrv->getFileContent('/home/'.$host->NomLDAP.'/www/Conf/General.conf');
        if (!empty($conf)){
            $conf = preg_replace('#<BDD_DSN>.*</BDD_DSN>#','<MYSQL_DSN>mysql://'.$host->NomLDAP.':'.$host->Password.'@'.$mysqlsrv->InternalIP.'/'.$host->NomLDAP.'</MYSQL_DSN>',$conf);
            $conf = str_replace('<SQL_MAX_LIMIT type="const">200</SQL_MAX_LIMIT>','<SQL_MAX_LIMIT type="const">2000</SQL_MAX_LIMIT>',$conf);
            $conf = str_replace('Login','LoginBootstrap',$conf);
            $apachesrv->putFileContent('/home/'.$host->NomLDAP.'/www/Conf/General.conf',$conf);
        }
        return true;
    }
}