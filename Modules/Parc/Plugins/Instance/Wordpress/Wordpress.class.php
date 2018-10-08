<?php

/*********************************************
*
* Module de paiement
* Crédit Mutuel
* Abtel
* 
*********************************************/

require_once( dirname(dirname(__FILE__)).'/Instance.interface.php' );

class ParcInstanceWordpress extends Plugin implements ParcInstancePlugin {

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
    public function checkState(){

    }
    /**
     * rewriteConfig
     */
    public function rewriteConfig() {
        $hos = $this->_obj->getOneParent('Host');
        $srv = $hos->getOneParent('Server');
        $conf = $srv->getFileContent('/home/'.$hos->NomLDAP.'/www/wp-config.php');
        if (!empty($conf)){
            $conf = preg_replace('#define\(\'DB_USER\', \'(.*)\'\);#','define(\'DB_USER\', \''.$hos->NomLDAP.'\');',$conf);
            $conf = preg_replace('#define\(\'DB_PASSWORD\', \'(.*)\'\);#','define(\'DB_PASSWORD\', \''.$hos->Password.'\');',$conf);
            $conf = preg_replace('#define\(\'DB_HOST\', \'(.*)\'\);#','define(\'DB_HOST\', \'db.maninwan.fr'.'\');',$conf);
            $srv->putFileContent('/home/'.$hos->NomLDAP.'/www/wp-config.php',$conf);

            //récupération de l'index
            $index = $srv->getFileContent('/home/'.$hos->NomLDAP.'/www/index.php');
            $index = preg_replace('#define\(\'WP_USE_THEMES\', true\);#',"define('WP_USE_THEMES', true);\nif(\$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){\n \$_SERVER['HTTPS'] = 'on';\n\$_SERVER['SERVER_PORT'] = 443;\n}\n",$index);
            $srv->putFileContent('/home/'.$hos->NomLDAP.'/www/index.php',$index);

            $htaccess = $srv->getFileContent('/home/'.$hos->NomLDAP.'/www/.htaccess');
            $htaccess = preg_replace('#RewriteCond %\{HTTPS\} off#','RewriteCond %{HTTP:X-Forwarded-Proto} !https',$htaccess);
            $srv->putFileContent('/home/'.$hos->NomLDAP.'/www/.htaccess',$htaccess);
        }
    }
}