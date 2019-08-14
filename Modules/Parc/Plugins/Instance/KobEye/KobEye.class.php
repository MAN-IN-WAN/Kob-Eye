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
    public function installSoftware($task = null){
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
        $hos = $this->_obj->getOneChild('Host');
        $srv = $hos->getOneParent('Server');
        $conf = $srv->getFileContent('/home/'.$hos->NomLDAP.'/www/Conf/General.conf');
        if (!empty($conf)){
            $conf = preg_replace('#<MYSQL_DSN>mysql\:\/\/(.*):(.*)@(.*)\/(.*)</MYSQL_DSN>#','<MYSQL_DSN>mysql://'.$hos->NomLDAP.':'.$hos->Password.'@db.maninwan.fr/\4</MYSQL_DSN>',$conf);
            $srv->putFileContent('/home/'.$hos->NomLDAP.'/www/Conf/General.conf',$conf);
        }
        return true;
    }
}