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
//        $version = VersionLogiciel::getLastVersion('Wordpress',$this->_obj->Type);
//        if (!is_object($version))throw new Exception('Pas de version disponible pour l\'app Wordpress Type '.$this->_obj->Type);
        try {
            //Installation des fichiers
            $act = $task->createActivity('Suppression du dossier www', 'Info');
            $out = $apachesrv->remoteExec('rm -Rf /home/' . $host->NomLDAP . '/www');
            $act->addDetails($out);
            $act->Terminate(true);
            //Installation des fichiers
            $act = $task->createActivity('Initialisation de la synchronisation', 'Info');
            $cmd = 'cd /home/' . $host->NomLDAP . '/ && rsync -avz root@ws1.maninwan.fr:/home/modele-wordpress/www/ www';
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
            $cmd = 'mysqldump -h db.maninwan.fr -u modele-wordpress -pb57f9fda5b3d748ec61b3687 modele-wordpress | mysql -u '.$host->NomLDAP.' -h db.maninwan.fr -p'.$host->Password.' '.$bdd->Nom;
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
        $version = VersionLogiciel::getLastVersion('Wordpress',$this->_obj->Type);
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Mise à jour en version '.$version->Version.' d\'Wordpress sur l\'instance ' . $this->_obj->Nom;
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
        //$version = VersionLogiciel::getLastVersion('Wordpress',$this->_obj->Type);
        //if (!is_object($version))throw new Exception('Pas de version disponible pour l\'app Wordpress Type '.$this->_obj->Type);
        try {
            //Installation des fichiers
            $act = $task->createActivity('Initialisation de la synchronisation', 'Info');
            $cmd = 'cd /home/' . $host->NomLDAP . '/ && rsync -avz root@ws1.maninwan.fr:/home/modele-wordpress/www/ www';
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
            $this->_obj->CurrentVersion = date('Ymd');
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
        $srv = $hos->getOneParent('Server');
        $bdd = $hos->getOneChild('Bdd');
        $mysqlsrv = $bdd->getOneParent('Server');
        $conf = $srv->getFileContent('/home/'.$hos->NomLDAP.'/www/wp-config.php');
        if (!empty($conf)){
            $conf = preg_replace('#define\(\'DB_NAME\', \'(.*)\'\);#','define(\'DB_NAME\', \''.$bdd->Nom.'\');',$conf);
            $conf = preg_replace('#define\(\'DB_USER\', \'(.*)\'\);#','define(\'DB_USER\', \''.$hos->NomLDAP.'\');',$conf);
            $conf = preg_replace('#define\(\'DB_PASSWORD\', \'(.*)\'\);#','define(\'DB_PASSWORD\', \''.$hos->Password.'\');',$conf);
            $conf = preg_replace('#define\(\'DB_HOST\', \'(.*)\'\);#','define(\'DB_HOST\', \'db.maninwan.fr'.'\');',$conf);
            $srv->putFileContent('/home/'.$hos->NomLDAP.'/www/wp-config.php',$conf);

            //récupération de l'index
            $index = $srv->getFileContent('/home/'.$hos->NomLDAP.'/www/index.php');
            $index = preg_replace('#define\(\'WP_USE_THEMES\', true\);#',"define('WP_USE_THEMES', true);\nif(\$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){\n \$_SERVER['HTTPS'] = 'on';\n\$_SERVER['SERVER_PORT'] = 443;\n}\n",$index);
            $srv->putFileContent('/home/'.$hos->NomLDAP.'/www/index.php',$index);

            //récupération de wp-login
            $index = $srv->getFileContent('/home/'.$hos->NomLDAP.'/www/wp-login.php');
            $index = preg_replace('#<\?php#',"<?php\nif(\$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){\n \$_SERVER['HTTPS'] = 'on';\n\$_SERVER['SERVER_PORT'] = 443;\n}\n",$index);
            $srv->putFileContent('/home/'.$hos->NomLDAP.'/www/wp-login.php',$index);

            //récupération de l'admin
            $index = $srv->getFileContent('/home/'.$hos->NomLDAP.'/www/wp-admin/admin.php');
            $index = preg_replace('#nocache_headers\(\);#',"nocache_headers();\nif(\$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){\n \$_SERVER['HTTPS'] = 'on';\n\$_SERVER['SERVER_PORT'] = 443;\n}\n",$index);
            $srv->putFileContent('/home/'.$hos->NomLDAP.'/www/wp-admin/admin.php',$index);

            $htaccess = $srv->getFileContent('/home/'.$hos->NomLDAP.'/www/.htaccess');
            $htaccess = preg_replace('#RewriteCond %\{HTTPS\} off#','RewriteCond %{HTTP:X-Forwarded-Proto} !https',$htaccess);
            $srv->putFileContent('/home/'.$hos->NomLDAP.'/www/.htaccess',$htaccess);

            //reconfiguration de la base de donnée
            $cmd = 'mysqldump -h db.maninwan.fr -u '.$hos->NomLDAP.' -p'.$hos->Password.' '.$bdd->Nom.' | sed -e "s/modele-wordpress.maninwan.fr/'.$hos->NomLDAP.'.maninwan.fr/g" > /home/'.$hos->NomLDAP.'/'.$bdd->Nom.'-rewiteconfig.sql && cat /home/'.$hos->NomLDAP.'/'.$bdd->Nom.'-rewiteconfig.sql | mysql -u '.$hos->NomLDAP.' -h db.maninwan.fr -p'.$hos->Password.' '.$bdd->Nom;
            //echo $cmd;
            $srv->remoteExec($cmd);

            //modification du mot de passe
            $db = new PDO('mysql:host=' . $mysqlsrv->InternalIP . ';dbname=' . $bdd->Nom, $mysqlsrv->SshUser, $mysqlsrv->SshPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $db->query("SET AUTOCOMMIT=1");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->query("GRANT SELECT ON `azkocms_common`.* TO `".$hos->Nom."` @'%';");
        }
        return true;
    }
}