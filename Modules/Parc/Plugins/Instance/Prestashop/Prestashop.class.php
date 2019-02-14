<?php

/*********************************************
*
* Module de paiement
* Crédit Mutuel
* Abtel
* 
*********************************************/

require_once( dirname(dirname(__FILE__)).'/Instance.interface.php' );

class ParcInstancePrestashop extends Plugin implements ParcInstancePlugin {

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
        $hos = $this->_obj->getOneParent('Host');
        $srv = $hos->getOneParent('Server');
        $conf = $srv->getFileContent('/home/'.$hos->NomLDAP.'/www/config/settings.inc.php');
        if (!empty($conf)){
            $conf = preg_replace('#define\(\'_DB_USER_\', \'(.*)\'\);#','define(\'_DB_USER_\', \''.$hos->NomLDAP.'\');',$conf);
            $conf = preg_replace('#define\(\'_DB_PASSWD_\', \'(.*)\'\);#','define(\'_DB_PASSWD_\', \''.$hos->Password.'\');',$conf);
            $conf = preg_replace('#define\(\'_DB_SERVER_\', \'(.*)\'\);#','define(\'_DB_SERVER_\', \'db.maninwan.fr'.'\');',$conf);
            $srv->putFileContent('/home/'.$hos->NomLDAP.'/www/config/settings.inc.php',$conf);
        }
        return true;
    }
}