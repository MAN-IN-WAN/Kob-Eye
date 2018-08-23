<?php

/*********************************************
*
* Module de paiement
* Crédit Mutuel
* Abtel
* 
*********************************************/

require_once( dirname(dirname(__FILE__)).'/Instance.interface.php' );

class ParcInstanceAzkoFront extends Plugin implements ParcInstancePlugin {

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
        $apache->DocumentRoot = '/home/'.$host->NomLDAP.'/www/azkocms/front/';
        $apache->ApacheConfig = 'Alias /skins /home/'.$host->NomLDAP.'/azkocms_skins/
        <Directory /home/'.$host->NomLDAP.'/azkocms_skins/>
            require all granted
        </Directory>
        Alias /medias /home/'.$host->NomLDAP.'/azkocms_medias/
        <Directory /home/'.$host->NomLDAP.'/azkocms_medias/>
            require all granted
        </Directory>';
        $apache->Save();
    }

    /**
     * createInstallTask
     * Creation de la tach d'installation de l'applicatif
     */
    public function createInstallTask(){
        //gestion depuis le plugin
        $version = VersionLogiciel::getLastVersion('AzkoFront',$this->_obj->Type);
        $task = genericClass::createInstance('Parc', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Installation de la version '.$version->Version.' d\'AzkoFront sur l\'instance ' . $this->_obj->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Instance';
        $task->TaskId = $this->_obj->Id;
        $task->TaskFunction = 'installSoftware';
        $task->addParent($this->_obj);
        $host = $this->_obj->getOneParent('Host');
        $task->addParent($host);
        $task->addParent($host->getOneParent('Server'));
        $task->Save();
    }
    /**
     * installAzkoBack
     * Fonction d'installation ou de mise à jour de secib web
     * @param Object Tache
     */
    public function installSoftware($task = null){
        $apachesrv = Sys::getOneData('Parc', 'Server/Web=1&defaultWebServer=1');
        $mysqlsrv = Sys::getOneData('Parc', 'Server/Sql=1&defaultSqlServer=1');
        $host = $this->_obj->getOneParent('Host');
        $bdd = $host->getOneChild('Bdd');
        $apache = $host->getOneChild('Apache');
        $version = VersionLogiciel::getLastVersion('AzkoFront',$this->_obj->Type);
        if (!is_object($version))throw new Exception('Pas de version disponible pour l\'app AzkoFront Type '.$this->_obj->Type);
        try {
            //Installation des fichiers
            $act = $this->_obj->createActivity('Suppression du dossier www', 'Info', $task);
            $out = $apachesrv->remoteExec('rm -Rf /home/' . $host->NomLDAP . '/www');
            $act->addDetails($out);
            $act->Terminate(true);
            $act = $this->_obj->createActivity('Initialisation du git clone', 'Info', $task);
            $cmd = 'cd /home/' . $host->NomLDAP . '/ && git clone '.$version->GitUrl.' www';
            if (!empty($version->GitBranche))
                $cmd.=' -b '.$version->GitBranche;
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails('cd /home/' . $host->NomLDAP . '/ && git clone '.$version->GitUrl.' www');
            $act->addDetails($out);
            $act->Terminate(true);
            $act = $this->_obj->createActivity('Création du fichier de config', 'Info', $task);
            $cmd = 'echo "[' . $host->NomLDAP . ']" > /home/' . $host->NomLDAP . '/www/config.ini';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $cmd = 'echo "DB_ORGA_LOGIN = \'' . $host->NomLDAP . '\'" >> /home/' . $host->NomLDAP . '/www/config.ini';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $cmd = 'echo "DB_ORGA_PASS = \'' . $host->Password . '\'" >> /home/' . $host->NomLDAP . '/www/config.ini';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            $act = $this->_obj->createActivity('Modification des droits', 'Info', $task);
            $cmd = 'chown ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . '/www -R';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            $act = $this->_obj->createActivity('Creation des dossiers skins et media et montage nfs', 'Info', $task);
            if (!$apachesrv->folderExists('/home/'.$host->NomLDAP.'/azkocms_medias')) {
                $cmd = 'mkdir /home/' . $host->NomLDAP . '/azkocms_medias';
                $act->addDetails($cmd);
                $out = $apachesrv->remoteExec($cmd);
                $act->addDetails($out);
                $cmd = 'chown ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . '/azkocms_medias';
                $act->addDetails($cmd);
                $out = $apachesrv->remoteExec($cmd);
                $act->addDetails($out);
                $act->Terminate(true);
            }else $apachesrv->remoteExec('for file in $(ls /home/' . $host->NomLDAP . '/); do mountpoint -q /home/' . $this->NomLDAP . '/$file && umount -l /home/' . $this->NomLDAP . '/$file; done ');
            if (!$apachesrv->folderExists('/home/'.$host->NomLDAP.'/azkocms_skins')) {
                $cmd = 'mkdir /home/' . $host->NomLDAP . '/azkocms_skins';
                $act->addDetails($cmd);
                $out = $apachesrv->remoteExec($cmd);
                $act->addDetails($out);
                $act->Terminate(true);
                $cmd = 'chown ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . '/azkocms_skins';
                $act->addDetails($cmd);
                $out = $apachesrv->remoteExec($cmd);
                $act->addDetails($out);
                $act->Terminate(true);
            }else $apachesrv->remoteExec('for file in $(ls /home/' . $host->NomLDAP . '/); do mountpoint -q /home/' . $this->NomLDAP . '/$file && umount -l /home/' . $this->NomLDAP . '/$file; done ');
            $cmd = 'mount -t nfs 192.168.200.4:/home/azkoback/azkocms_skins /home/' . $host->NomLDAP . '/azkocms_skins';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            $cmd = 'mount -t nfs 192.168.200.4:/home/azkoback/azkocms_medias /home/' . $host->NomLDAP . '/azkocms_medias';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            //changement du statut de l'instance
            $this->_obj->setStatus(2);
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
     * createUpdateTask
     * Creation de la tache de mise à jour
     */
    public function createUpdateTask($orig=null){
        //gestion depuis le plugin
        $version = VersionLogiciel::getLastVersion('AzkoFront',$this->_obj->Type);
        $task = genericClass::createInstance('Parc', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Mise à jour en version '.$version->Version.' d\'AzkoFront sur l\'instance ' . $this->_obj->Nom;
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
    }
    /**
     * updateSoftware
     * Fonction de mise à jour de l'applicatif
     * @param Object Tache
     * @return bool
     */
    public function updateSoftware($task = null){
        $apachesrv = Sys::getOneData('Parc', 'Server/Web=1&defaultWebServer=1');
        $mysqlsrv = Sys::getOneData('Parc', 'Server/Sql=1&defaultSqlServer=1');
        $host = $this->_obj->getOneParent('Host');
        $bdd = $host->getOneChild('Bdd');
        $apache = $host->getOneChild('Apache');
        $version = VersionLogiciel::getLastVersion('AzkoFront',$this->_obj->Type);
        if (!is_object($version))throw new Exception('Pas de version disponible pour l\'app AzkoFront Type '.$this->_obj->Type);
        try {
            $act = $this->_obj->createActivity('Initialisation du git clone', 'Info', $task);
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
            $act = $this->_obj->createActivity('Modification des droits', 'Info', $task);
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

}