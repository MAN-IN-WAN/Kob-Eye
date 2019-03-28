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
        //gestion depuis le plugin
        $version = VersionLogiciel::getLastVersion('Prestashop',$this->_obj->Type);
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Installation de la version '.$version->Version.' de Prestashop sur l\'instance ' . $this->_obj->Nom;
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
            $act = $task->createActivity('Suppression du dossier www', 'Info', $task);
            $out = $apachesrv->remoteExec('rm -Rf /home/' . $host->NomLDAP . '/www');
            $act->addDetails($out);
            $act->Terminate(true);
            //Installation des fichiers
            $act = $task->createActivity('Initialisation de la synchronisation', 'Info', $task);

            $modele = Sys::getOneData('Parc','Host/Nom=modele-prestashop');
            $srv = $modele->getOneParent('Server');
            $url = $srv->DNSNom;

            $cmd = 'cd /home/' . $host->NomLDAP . '/ && rsync -avz root@'.$url.':/home/modele-prestashop/www/ www';
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
            //Dump de la base
            $act = $task->createActivity('Dump de la base Mysql', 'Info', $task);
            $cmd = 'mysqldump -h db.maninwan.fr -u '.$modele->Nom.' -p'.$modele->Password.' '.$modele->Nom.' | mysql -u '.$host->NomLDAP.' -h db.maninwan.fr -p'.$host->Password.' '.$bdd->Nom;
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $act->Terminate(true);

            //Conf en base
            $act = $task->createActivity('Configuration magasin', 'Info');
            $cmd = 'mysql -u '.$host->NomLDAP.' -h db.maninwan.fr -p'.$host->Password.' '.$bdd->Nom.' -e "UPDATE ps_configuration SET value =\''.$this->_obj->FullDomain.'\' WHERE name=\'PS_SHOP_DOMAIN\'"';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $cmd = 'mysql -u '.$host->NomLDAP.' -h db.maninwan.fr -p'.$host->Password.' '.$bdd->Nom.' -e "UPDATE ps_configuration SET value =\''.$this->_obj->FullDomain.'\' WHERE name=\'PS_SHOP_DOMAIN_SSL\'"';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $cmd = 'mysql -u '.$host->NomLDAP.' -h db.maninwan.fr -p'.$host->Password.' '.$bdd->Nom.' -e "UPDATE ps_configuration SET value =\''.$this->_obj->Nom.'\' WHERE name=\'PS_SHOP_NAME\'"';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $cmd = 'mysql -u '.$host->NomLDAP.' -h db.maninwan.fr -p'.$host->Password.' '.$bdd->Nom.' -e "UPDATE ps_shop SET name =\''.$this->_obj->Nom.'\' WHERE id_shop=1"';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $cmd = 'mysql -u '.$host->NomLDAP.' -h db.maninwan.fr -p'.$host->Password.' '.$bdd->Nom.' -e "UPDATE ps_shop_url SET domain =\''.$this->_obj->FullDomain.'\', domain_ssl =\''.$this->_obj->FullDomain.'\' WHERE id_shop=1"';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            $act = $task->createActivity('Mot de passe administrateur', 'Info');
            /*$sets = $conf = $srv->getFileContent('/home/'.$host->NomLDAP.'/www/app/config/parameters.php');
            $salt = array();
            $temp = preg_match('#\'cookie_key\' => \'(.*)\',#',$sets,$salt);
            $salt = $salt[1];
            $act->addDetails('Salt : '.$salt);*/
            $pass = addcslashes(password_hash($host->Password, PASSWORD_BCRYPT),'$');
            $cmd = 'mysql -u '.$host->NomLDAP.' -h db.maninwan.fr -p'.$host->Password.' '.$bdd->Nom.' -e "UPDATE ps_employee SET passwd =\''.$pass.'\',email=\'admin@'.$this->_obj->FullDomain.'\' WHERE Id_employee=1"';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $act->Terminate(true);

            /* clear cache*/
            $act = $task->createActivity('Nettoyage des caches', 'Info');
            $cmd = 'rm -rf /home/' . $host->NomLDAP . '/www/var/cache/prod/smarty/cache/*';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $cmd = 'rm -rf /home/' . $host->NomLDAP . '/www/var/cache/prod/smarty/compile/*';
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
    public function createUpdateTask($orig = null){
        //gestion depuis le plugin
        $version = VersionLogiciel::getLastVersion('Prestashop',$this->_obj->Type);
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Mise à jour en version '.$version->Version.' de Prestashop sur l\'instance ' . $this->_obj->Nom;
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
            $modele = Sys::getOneData('Parc','Host/Nom=modele-prestashop');
            $srv = $modele->getOneParent('Server');
            $url = $srv->DNSNom;

            $act = $task->createActivity('Initialisation de la synchronisation', 'Info', $task);
            $cmd = 'cd /home/' . $host->NomLDAP . '/ && rsync -avz root@'.$url.':/home/modele-prestashop/www/ www';
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
        $conf = $srv->getFileContent('/home/'.$hos->NomLDAP.'/www/app/config/parameters.php');
        if (!empty($conf)){
            $conf = preg_replace('#\'database_name\' => \'(.*)\',#','\'database_name\' => \''.$hos->NomLDAP.'\',',$conf);
            $conf = preg_replace('#\'database_user\' => \'(.*)\',#','\'database_user\' => \''.$hos->NomLDAP.'\',',$conf);
            $conf = preg_replace('#\'database_password\' => \'(.*)\',#','\'database_password\' => \''.$hos->Password.'\',',$conf);
            $conf = preg_replace('#\'database_host\' => \'(.*)\',#','\'database_host\' => \'db.maninwan.fr'.'\',',$conf);
            $srv->putFileContent('/home/'.$hos->NomLDAP.'/www/app/config/parameters.php',$conf);
        }
        return true;
    }
}
