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
        $host = $this->_obj->getOneChild('Host');
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
        //suppression des users ftp
        $usrFtps = $host->getChildren('Ftpuser');
        foreach ($usrFtps as $usr) $usr->Delete();
    }

    /**
     * createInstallTask
     * Creation de la tach d'installation de l'applicatif
     */
    public function createInstallTask(){
        //gestion depuis le plugin
        $version = VersionLogiciel::getLastVersion('AzkoFront',$this->_obj->Type);
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Installation de la version '.$version->Version.' d\'AzkoFront sur l\'instance ' . $this->_obj->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Instance';
        $task->TaskType = 'install';
        $task->TaskId = $this->_obj->Id;
        $task->TaskFunction = 'installSoftware';
        $task->addParent($this->_obj);
        $host = $this->_obj->getOneChild('Host');
        $task->addParent($host);
        $task->addParent($host->getOneParent('Server'));
        $task->Save();
        return array('task'=>$task);
    }
    /**
     * installAzkoBack
     * Fonction d'installation ou de mise à jour de secib web
     * @param Object Tache
     */
    public function installSoftware($task){
        $host = $this->_obj->getOneChild('Host');
        $bdd = $host->getOneChild('Bdd');
        $mysqlsrv = $bdd->getOneParent('Server');
        $apachesrv = $host->getOneParent('Server');
        $version = VersionLogiciel::getLastVersion('AzkoFront',$this->_obj->Type);
        if (!is_object($version))throw new Exception('Pas de version disponible pour l\'app AzkoFront Type '.$this->_obj->Type);
        try {
            //définition des droits sur la base
            $act = $task->createActivity('Définition des droits spécifiques sql', 'Info');
            $db = new PDO('mysql:host=' . $mysqlsrv->InternalIP . ';dbname=' . $bdd->Nom, $mysqlsrv->SshUser, $mysqlsrv->SshPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $db->query("SET AUTOCOMMIT=1");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->query("GRANT SELECT ON `azkocms_common`.* TO `".$host->Nom."` @'%';");
            $act->Terminate(true);
            //Installation des fichiers
            $act = $task->createActivity('Suppression du dossier www', 'Info');
            $out = $apachesrv->remoteExec('rm -Rf /home/' . $host->NomLDAP . '/www');
            $act->addDetails($out);
            $act->Terminate(true);
            //configuration du git
            $this->configGit($task,$apachesrv,$host,$version);
            //execution du git clone
            $act = $task->createActivity('Initialisation du git clone', 'Info');
            $cmd = 'cd /home/' . $host->NomLDAP . '/ && sudo -u ' . $host->NomLDAP . ' git clone '.$version->GitUrl.' www';
            if (!empty($version->GitBranche))
                $cmd.=' -b '.$version->GitBranche;
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails('cd /home/' . $host->NomLDAP . '/ && sudo -u ' . $host->NomLDAP . ' git clone '.$version->GitUrl.' www');
            $act->addDetails($out);
            $act->Terminate(true);
            $act = $task->createActivity('Création du fichier de config', 'Info');
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
            $act = $task->createActivity('Modification des droits', 'Info');
            $cmd = 'chown ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . '/www -R';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            //creation et montage des dossier
            $this->createAndMountFolders($apachesrv,$host,$task);
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
     * configGit
     * Configuration du Git
     */
    private function configGit($task,$apachesrv,$host,$version) {
        //Configuratio du git clone
        $act = $task->createActivity('Configuration du git', 'Info');
        try {
            $apachesrv->remoteExec('sudo -u ' . $host->NomLDAP . ' ssh -T '.$version->GitUrl);
        }catch (Throwable $e){
            //erreur normales
        }
        $apachesrv->remoteExec('if [ ! -d  /home/' . $host->NomLDAP . '/.ssh ]; then mkdir /home/' . $host->NomLDAP . '/.ssh; fi');
        $cmd = 'echo "'.base64_encode($version->SshKey).'" | base64 -d > /home/' . $host->NomLDAP . '/.ssh/azko_app.key';
        $out = $apachesrv->remoteExec($cmd);
        $act->addDetails($cmd);
        $act->addDetails($out);
        $cmd = 'echo "'.base64_encode($version->SshConfig).'" | base64 -d > /home/' . $host->NomLDAP . '/.ssh/config';
        $out = $apachesrv->remoteExec($cmd);
        $act->addDetails($cmd);
        $act->addDetails($out);
        $cmd = 'chmod 400 /home/' . $host->NomLDAP . '/.ssh/azko_app.key && chmod 400 /home/' . $host->NomLDAP . '/.ssh/config && chown -R ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . '/.ssh';
        $act->addDetails($cmd);
        $apachesrv->remoteExec($cmd);
        $act->Terminate(true);
    }
    /**
     * createAndMountFolders
     * creation et montage des dossiers skins et medias
     */
    public function createAndMountFolders($apachesrv,$host,$task) {
        try {
            $act = $task->createActivity('Creation des dossiers skins et media et montage nfs sur '.$apachesrv->Nom, 'Info');
            if (!$apachesrv->folderExists('/home/' . $host->NomLDAP . '/azkocms_medias')) {
                $cmd = 'mkdir /home/' . $host->NomLDAP . '/azkocms_medias';
                $act->addDetails($cmd);
                $out = $apachesrv->remoteExec($cmd);
                $act->addDetails($out);
                $cmd = 'chown ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . '/azkocms_medias';
                $act->addDetails($cmd);
                $out = $apachesrv->remoteExec($cmd);
                $act->addDetails($out);
                $act->Terminate(true);
            }//else $apachesrv->remoteExec('for file in $(ls /home/' . $host->NomLDAP . '/); do mountpoint -q /home/' . $host->NomLDAP . '/$file && umount -l /home/' . $host->NomLDAP . '/$file; done ');
            if (!$apachesrv->folderExists('/home/' . $host->NomLDAP . '/azkocms_skins')) {
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
            }// else $apachesrv->remoteExec('for file in $(ls /home/' . $host->NomLDAP . '/); do mountpoint -q /home/' . $host->NomLDAP . '/$file && umount -l /home/' . $host->NomLDAP . '/$file; done ');
            $cmd = 'mountpoint -q /home/'.$host->NomLDAP.'/azkocms_skins';
            $act->addDetails($cmd);
            try {
                $apachesrv->remoteExec($cmd);
            }catch (Exception $e){
                $cmd = 'mount -t nfs 192.168.200.4:/home/azkoback/azkocms_skins /home/' . $host->NomLDAP . '/azkocms_skins';
                $act->addDetails($cmd);
                $out = $apachesrv->remoteExec($cmd);
                $act->addDetails($out);
                $act->Terminate(true);
            }
            $cmd = 'mountpoint -q /home/'.$host->NomLDAP.'/azkocms_medias && echo 1';
            $act->addDetails($cmd);
            try {
                $apachesrv->remoteExec($cmd);
            }catch (Exception $e){
                $cmd = 'mount -t nfs 192.168.200.4:/home/azkoback/azkocms_medias /home/' . $host->NomLDAP . '/azkocms_medias';
                $act->addDetails($cmd);
                $out = $apachesrv->remoteExec($cmd);
                $act->addDetails($out);
                $act->Terminate(true);
            }
            return true;
        }catch (Exception $e){
            $act->addDetails('ERREUR: '.$e->getMessage());
            $act->Terminate(false);
            return false;
        }
    }
    /**
     * createUpdateTask
     * Creation de la tache de mise à jour
     */
    public function createUpdateTask($orig=null){
        //gestion depuis le plugin
        $version = VersionLogiciel::getLastVersion('AzkoFront',$this->_obj->Type);
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Mise à jour en version '.$version->Version.' d\'AzkoFront sur l\'instance ' . $this->_obj->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Instance';
        $task->TaskType = 'install';
        $task->TaskId = $this->_obj->Id;
        $task->TaskFunction = 'updateSoftware';
//        $task->TaskFunction = 'installSoftware';
        $task->addParent($this->_obj);
        $host = $this->_obj->getOneChild('Host');
        if ($host) {
            $task->addParent($host);
            $task->addParent($host->getOneParent('Server'));
            if (is_object($orig)) $task->addParent($orig);
            $task->Save();
            //changement du statut de l'instance
            $this->_obj->setStatus(3);
            return array('task'=>$task);
        }else{
            return true;
        }
    }

    /**
     * updateSoftware
     * Fonction de mise à jour de l'applicatif
     * @param Object Tache
     * @return bool
     */
    public function updateSoftware($task){
        $host = $this->_obj->getOneChild('Host');
        //$host = $this->_obj->getOneChild('Host');
        $apachesrvs = $host->getParents('Server');
        $version = VersionLogiciel::getLastVersion('AzkoFront',$this->_obj->Type);
        if (!is_object($version))throw new Exception('Pas de version disponible pour l\'app AzkoFront Type '.$this->_obj->Type);
        foreach ($apachesrvs as $apachesrv) {
            try {
                //configuration du git
                if ($version->SshReset)
                    $this->configGit($task, $apachesrv, $host, $version);

                if ($version->GitReset) {
                    $GLOBALS['Chrono']->start('AZKOFRONT: git init');
                    $act = $task->createActivity('Initialisation du git clone et connexion à ' . $apachesrv->Nom, 'Info');
                    $cmd = 'cd /home/' . $host->NomLDAP . '/www && sudo -u ' . $host->NomLDAP . ' git remote -v';
                    $act->addDetails($cmd);
                    $out = $apachesrv->remoteExec($cmd);
                    $act->addDetails($out);
                    if (!empty($out)) {
                        $cmd = 'cd /home/' . $host->NomLDAP . '/www && sudo -u ' . $host->NomLDAP . ' git remote rm origin';
                        $act->addDetails($cmd);
                        $out = $apachesrv->remoteExec($cmd);
                        $act->addDetails($out);
                    }
                    $cmd = 'cd /home/' . $host->NomLDAP . '/www && sudo -u ' . $host->NomLDAP . ' git remote add origin ' . $version->GitUrl;
                    $act->addDetails($cmd);
                    $out .= $apachesrv->remoteExec($cmd);
                    $act->addDetails($out);
                    $GLOBALS['Chrono']->stop('AZKOFRONT: git init');
                    $GLOBALS['Chrono']->start('AZKOFRONT: git fetch');
                    $cmd = 'cd /home/' . $host->NomLDAP . '/www && sudo -u ' . $host->NomLDAP . ' git fetch';
                    $act->addDetails($cmd);
                    $out = $apachesrv->remoteExec($cmd);
                    $act->addDetails($out);
                    if (!empty($version->GitBranche))
                        $cmd = 'cd /home/' . $host->NomLDAP . '/www && sudo -u ' . $host->NomLDAP . ' git reset --hard origin/' . $version->GitBranche;
                    else $cmd = 'cd /home/' . $host->NomLDAP . '/www && sudo -u ' . $host->NomLDAP . ' git reset --hard origin/master';
                    $act->addDetails($cmd);
                    $out .= $apachesrv->remoteExec($cmd);
                    $act->addDetails($out);
                    $act->Terminate(true);
                    $GLOBALS['Chrono']->stop('AZKOFRONT: git fetch');
                    $GLOBALS['Chrono']->start('AZKOFRONT: edit rights');
                    $act = $task->createActivity('Modification des droits', 'Info');
                    $cmd = 'chown ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . '/www -R';
                    $act->addDetails($cmd);
                    $out = $apachesrv->remoteExec($cmd);
                    $act->addDetails($out);
                    $act->Terminate(true);
                    $GLOBALS['Chrono']->stop('AZKOFRONT: edit rights');
                    $GLOBALS['Chrono']->start('AZKOFRONT: save instance changes');
                    //changement du statut de l'instance
                    $this->_obj->CurrentVersion = $version->Version;
                    $this->_obj->setStatus(2);
                    if ($task) {
                        $task->addRetour($GLOBALS['Chrono']->total());
                    }
                    $GLOBALS['Chrono']->stop('AZKOFRONT: save instance changes');
                    return true;
                } else {
                    $GLOBALS['Chrono']->start('AZKOFRONT: git pull');
                    $act = $task->createActivity('Execution du git pull et connexion à ' . $apachesrv->Nom, 'Info');
                    $cmd = 'cd /home/' . $host->NomLDAP . '/www && sudo -u ' . $host->NomLDAP . ' git pull ' . $version->GitUrl . ' ' . $version->GitBranche;
                    $act->addDetails($cmd);
                    $out = $apachesrv->remoteExec($cmd);
                    $act->addDetails($out);
                    $act->Terminate(true);
                    $GLOBALS['Chrono']->stop('AZKOFRONT: git pull');
                    //changement du statut de l'instance
                    $this->_obj->CurrentVersion = $version->Version;
                    $this->_obj->setStatus(2);
                    if ($task) {
                        $task->addRetour($GLOBALS['Chrono']->total());
                    }
                    $act = $task->createActivity('Fin de la mise à jour', 'Info');
                    $act->Terminate(true);
                }
            } catch (Exception $e) {
                $act->addDetails('Erreur: ' . $e->getMessage());
                $act->Terminate(false);
                //throw new Exception($e->getMessage());
            }
        }
        return true;
    }
    /**
     * checkState
     */
    public function checkState($task){
        $host = $this->_obj->getOneChild('Host');
        $apachesrvs = $host->getParents('Server');
        foreach ($apachesrvs as $apachesrv) {
            try {
                //demontage forcé
                try {
                    $act = $task->createActivity('Démontage des points de montage ' . $apachesrv->Nom . ' PID:' . getmypid(), 'Info');
                    $cmd = 'umount /home/' . $host->NomLDAP . '/azkocms_medias && umount /home/' . $host->NomLDAP . '/azkocms_skins';
                    $act->addDetails($cmd);
                    $out = $apachesrv->remoteExec($cmd);
                }catch (Throwable $e){

                }
                //check mount
                $GLOBALS['Chrono']->start('AZKOFRONT: checkState check mount');
                $act = $task->createActivity('Vérification des points de montage ' . $apachesrv->Nom . ' PID:' . getmypid(), 'Info');
                $cmd = 'mountpoint -q /home/' . $host->NomLDAP . '/azkocms_medias && mountpoint -q /home/' . $host->NomLDAP . '/azkocms_skins && echo 2';
                $act->addDetails($cmd);
                try {
                    $out = $apachesrv->remoteExec($cmd);
                    $act->addDetails($out);
                    $act->Terminate(true);
                } catch (Exception $e) {
                    $act->addDetails($out);
                    $act->Terminate(false);
                    //montage des dossiers
                    $out = 0;
                }
                if (intval($out) < 2) {
                    $act = $task->createActivity('Montage des dossiers skins et médias sur le serveur ' . $apachesrv->Nom . ' PID:' . getmypid(), 'Info');
                    try {
                        $incident = Incident::createIncident('Les dossiers médias et skins de l\'instance ' . $this->_obj->Nom . ' ne sont pas montés.', 'Le code de retour est ', $this->_obj, 'FOLDER_MOUNT', $this->_obj->NomInstance, 3, false);
                        if ($this->createAndMountFolders($apachesrv, $host, $task)) {
                            $incident = Incident::createIncident('Les dossiers médias et skins de l\'instance ' . $this->_obj->Nom . ' ne sont pas montés.', 'Le code de retour est ', $this->_obj, 'FOLDER_MOUNT', $this->_obj->NomInstance, 3, true);
                        }
                    } catch (Exception $e) {
                        $act->addDetails('ERREUR DE MONTAGE : ' . $e->getMessage());
                    }
                } else $incident = Incident::createIncident('Les dossiers médias et skins de l\'instance ' . $this->_obj->Nom . ' ne sont pas montés.', 'Le code de retour est ', $this->_obj, 'FOLDER_MOUNT', $this->_obj->InstanceNom, 3, true);


                $GLOBALS['Chrono']->stop('AZKOFRONT: checkState check mount');
                $GLOBALS['Chrono']->start('AZKOFRONT: checkState check software');
                $act = $task->createActivity('Vérification de l\'installation du logciel', 'Info');
                $cmd = 'ls -lah /home/' . $host->NomLDAP . '/www/azkocms | wc -l';
                $act->addDetails($cmd);
                $out = $apachesrv->remoteExec($cmd);
                $act->addDetails($out);
                $act->Terminate(true);
                if (!intval($out)) {
                    //Lancement de l'installation du logiciel
                    //$this->createInstallTask();
                    $act->addDetails('Logiciel mal installé!');
                    $act->Terminate(false);
                    $this->_obj->setStatus(1);
                } else $this->_obj->setStatus(2);
                $GLOBALS['Chrono']->stop('AZKOFRONT: checkState check software');
                if ($task) {
                    $task->addRetour($GLOBALS['Chrono']->total());
                }
            } catch (Exception $e) {
                $act->addDetails('Erreur: ' . $e->getMessage());
                $act->Terminate(false);
                //throw new Exception($e->getMessage());
            }
        }
        return true;
    }
    /**
     * rewriteConfig
     */
    public function rewriteConfig() {
        return true;

    }

}