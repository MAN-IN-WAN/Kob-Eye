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
//gestion depuis le plugin
        $version = VersionLogiciel::getLastVersion('Wordpress',$this->_obj->Type);
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Installation de la version '.$version->Version.' de Wordpress sur l\'instance ' . $this->_obj->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Instance';
        $task->TaskId = $this->_obj->Id;
        $task->TaskType = 'install';
        $task->TaskFunction = 'installSoftware';
        $task->addParent($this->_obj);
        $host = $this->_obj->getOneChild('Host');
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
        $host = $this->_obj->getOneChild('Host');
        $bdd = $host->getOneChild('Bdd');
        $mysqlsrv = $bdd->getOneParent('Server');
        $apachesrv = $host->getOneParent('Server');
//        $version = VersionLogiciel::getLastVersion('Wordpress',$this->_obj->Type);
//        if (!is_object($version))throw new Exception('Pas de version disponible pour l\'app Wordpress Type '.$this->_obj->Type);
        try {
            //Installation des fichiers
            $act = $task->createActivity('Suppression du dossier www', 'Info', $task);
            $out = $apachesrv->remoteExec('rm -Rf /home/' . $host->NomLDAP . '/www');
            $act->addDetails($out);
            $act->Terminate(true);
            //Installation des fichiers
            $act = $task->createActivity('Initialisation de la synchronisation', 'Info', $task);

            $modele = Sys::getOneData('Parc','Host/Nom=modele-wordpress');
            $srv = $modele->getOneParent('Server');
            $url = $srv->IP;

            $cmd = 'cd /home/' . $host->NomLDAP . '/ && rsync -avz root@'.$url.':/home/modele-wordpress/www/ www';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            $act = $task->createActivity('Modification des droits', 'Info', $task);
            $cmd = 'chown ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . '/www -R';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            //Dump de la base
            $act = $task->createActivity('Dump de la base Mysql', 'Info', $task);
            $mysqlhostModel = "185.87.64.121";
            $mysqlhost =  $mysqlsrv->IP;
            $cmd = 'mysqldump -h '.$mysqlhostModel.' -u modele-wordpress -p4e1969065bb99da1da40f2a2 modele-wordpress | mysql -u '.$host->NomLDAP.' -h '.$mysqlhost.' -p'.$host->Password.' '.$bdd->Nom;
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            $act = $task->createActivity('Mot de passe administrateur', 'Info');
            $cmd = 'mysql -u '.$host->NomLDAP.' -h '.$mysqlhost.' -p'.$host->Password.' '.$bdd->Nom.' -e "UPDATE wp_users SET user_pass=\''.md5($host->Password).'\' WHERE ID=1"';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $act->Terminate(true);

            //execution de la configuration
            $act = $task->createActivity('Création de la config', 'Info', $task);
            $retcfg = $this->rewriteConfig();
            if($retcfg){
                $act->Terminate(true);
                $task->Terminate(true);
            } else {
                $act->Terminate(false);
                $task->Terminate(false);
            }
            
            //changement du statut de l'instance
            $this->_obj->setStatus(2);
            $this->_obj->CurrentVersion = date('Ymd');
            $this->_obj->Save();

        }catch (Exception $e){
            $act->addDetails('Erreur: '.$e->getMessage());
            $act->Terminate(false);
            throw new Exception($e->getMessage());
        }

        return true;
    }
    /**
     * createUpdateTask
     * Creation de la tache de mise à jour du logiciel
     */
    public function createUpdateTask($orig = null){
//gestion depuis le plugin
/*        $version = VersionLogiciel::getLastVersion('Wordpress',$this->_obj->Type);
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Mise à jour en version '.$version->Version.' d\'Wordpress sur l\'instance ' . $this->_obj->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Instance';
        $task->TaskId = $this->_obj->Id;
        $task->TaskFunction = 'updateSoftware';
        $task->TaskType = 'update';
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
        //$version = VersionLogiciel::getLastVersion('Wordpress',$this->_obj->Type);
        //if (!is_object($version))throw new Exception('Pas de version disponible pour l\'app Wordpress Type '.$this->_obj->Type);
        try {
            //Installation des fichiers
            $modele = Sys::getOneData('Parc','Host/Nom=modele-wordpress');
            $srv = $modele->getOneParent('Server');
            $url = $srv->IP;

            $act = $task->createActivity('Initialisation de la synchronisation', 'Info', $task);
            $cmd = 'cd /home/' . $host->NomLDAP . '/ && rsync -avz root@'.$url.':/home/modele-wordpress/www/ www';
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
            //changement du statut de l'instance
            $this->_obj->setStatus(2);
            $this->_obj->CurrentVersion = date('Ymd');
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
        $mysqlhost = 'db.abtel.fr';
        if (!$bdd){
            $this->_obj->addError(array('Message'=>'Base de donnée introuvable'));
            //return false;
        }else {
            $mysqlsrv = $bdd->getOneParent('Server');
            $mysqlhost =  $mysqlsrv->IP;
        }
        $conf = $srv->getFileContent('/home/'.$hos->NomLDAP.'/www/wp-config.php');
        if (!empty($conf)){
            $conf = preg_replace('#define\( *\'DB_NAME\', *\'(.*)\' *\);#','define(\'DB_NAME\', \''.$bdd->Nom.'\');',$conf);
            $conf = preg_replace('#define\( *\'DB_USER\', *\'(.*)\' *\);#','define(\'DB_USER\', \''.$hos->NomLDAP.'\');',$conf);
            $conf = preg_replace('#define\( *\'DB_PASSWORD\', *\'(.*)\' *\);#','define(\'DB_PASSWORD\', \''.$hos->Password.'\');',$conf);
            $conf = preg_replace('#define\( *\'DB_HOST\', *\'(.*)\' *\);#',"define('DB_HOST', '".$mysqlhost."' );\nif(\$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){\n \$_SERVER['HTTPS'] = 'on';\n\$_SERVER['SERVER_PORT'] = 443;\n}\nif (isset(\$_SERVER['HTTP_X_FORWARDED_HOST'])) {\n\$_SERVER['HTTP_HOST'] = \$_SERVER['HTTP_X_FORWARDED_HOST'];\n}\n",$conf);
            $srv->putFileContent('/home/'.$hos->NomLDAP.'/www/wp-config.php',$conf);

            //reconfiguration de la base de donnée
            if ($bdd) {
                $cmd = 'mysqldump -h '.$mysqlhost.' -u ' . $hos->NomLDAP . ' -p' . $hos->Password . ' ' . $bdd->Nom . ' | sed -e "s/modele-wordpress.abtel.fr/' . $hos->NomLDAP . '.abtel.fr/g" > /home/' . $hos->NomLDAP . '/' . $bdd->Nom . '-rewiteconfig.sql && cat /home/' . $hos->NomLDAP . '/' . $bdd->Nom . '-rewiteconfig.sql | mysql -u ' . $hos->NomLDAP . ' -h '.$mysqlhost.' -p' . $hos->Password . ' ' . $bdd->Nom;
                //echo $cmd;
                $srv->remoteExec($cmd);
            }

            //correction du .htaccess
            $conf = $srv->getFileContent('/home/'.$hos->NomLDAP.'/www/.htaccess');
            $conf = str_replace('RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]','#RewriteEngine On
#RewriteCond %{HTTPS} off
#RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]',$conf);
            $srv->putFileContent('/home/'.$hos->NomLDAP.'/www/.htaccess',$conf);


        }
        return true;
    }
}