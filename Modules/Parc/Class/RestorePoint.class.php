<?php
class RestorePoint extends genericClass{
    /**
     * createRestoreTask
     * Création d'une tache de restauration
     */
    public function createRestoreTask($orig=null) {
        $host = $this->getOneParent('Host');
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Restauration de l\'hébergement ' . $host->Nom.' du point de restauration '.$this->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'RestorePoint';
        $task->TaskId = $this->Id;
        $task->TaskFunction = 'restore';
        $task->TaskType = 'edit';
        $task->TaskCode = 'HOST_RESTORE';
        $task->addParent($host);
        $inst = $host->getOneChild('Instance');
        if ($inst)
            $task->addParent($inst);
        $task->addParent($host->getOneParent('Server'));
        if (is_object($orig)) $task->addParent($orig);
        $task->Save();
        return array("task" => $task);
    }
    /**
     * restore
     * Fonction de restauration
     * @param Object Tache
     */
    public function restore($task){
        $host = $this->getOneParent('Host');
        $bdds = $host->getChildren('Bdd');
        $apachesrv = $host->getOneParent('Server');
        $inst = $host->getOneChild('Instance');
        try {
            //Préparation du backup
            $act = $task->createActivity('Préparation et nettoyage de la restauration ', 'Info', $task);
            //suppression des dossiers
            $cmd = 'cd /home/' . $host->NomLDAP . ' && ls';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $folders = explode("\n",$out);
            array_pop($folders);
            $act->addDetails(print_r($folders,true));
            $act->Terminate(true);

            $act = $task->createActivity('Suppression des dossiers', 'Info', $task);
            //analyse des dossiers à supprimer
            foreach ($folders as $folder) {
                if (!in_array($folder,array('cgi-bin','backup','logs','stats','conf',''))){
                    $out = $apachesrv->remoteExec('rm -Rf /home/'.$host->NomLDAP.'/'.$folder);
                    $act->addDetails('Suppression du dossier '.$folder);
                    $act->addDetails($out);
                }
            }
            $act->Terminate(true);

            $act = $task->createActivity('Restauration '.$this->Titre, 'Info', $task);
            $cmd = 'cd /home/' . $host->NomLDAP . '/ && borg extract backup::'.$this->Identifiant.'';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $act->Terminate(true);

            $act = $task->createActivity('Modification des droits', 'Info', $task);
            $cmd = 'chown ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . ' -R';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);

            //Restauration des bases des données
            foreach ($bdds as $bdd) {
                $act = $task->createActivity('Restauration base de donnée '.$bdd->Nom, 'Info', $task);
                $cmd = 'cd /home/' . $host->NomLDAP . '/ && mysql -h '.PARC_BDD_DOMAIN.' -u ' . $host->NomLDAP . ' -p' . $host->Password . ' -e "DROP DATABASE \`' . $bdd->Nom . '\`;" && mysql -h '.PARC_BDD_DOMAIN.' -u ' . $host->NomLDAP . ' -p' . $host->Password . ' -e "CREATE DATABASE \`' . $bdd->Nom . '\`;" && mysql -h '.PARC_BDD_DOMAIN.' -u ' . $host->NomLDAP . ' -p' . $host->Password . ' ' . $bdd->Nom . ' < sql/'.$bdd->Nom.'-'.$this->Identifiant.'.sql';
                $act->addDetails($cmd);
                $out = $apachesrv->remoteExec($cmd);
                $act->addDetails($out);
                $act->Terminate(true);
            }
            return true;
        }catch (Exception $e){
            $act->addDetails('Erreur: '.$e->getMessage());
            $act->Terminate(false);
            throw new Exception($e->getMessage());
        }catch (Error $e){
            $act->addDetails('Erreur: '.$e->getMessage());
            $act->Terminate(false);
            throw new Exception($e->getMessage());
        }
    }
    /**
     * createMountTask
     * Création d'une tache de montage
     */
    public function createMountTask($orig=null) {
        $host = $this->getOneParent('Host');
        $task = genericClass::createInstance('Parc', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Montage de la sauvegarde de l\'hébergement ' . $host->Nom.' au point de restauration '.$this->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'RestorePoint';
        $task->TaskId = $this->Id;
        $task->TaskFunction = 'mount';
        $task->TaskType = 'edit';
        $task->TaskCode = 'BACKUP_MOUNT';
        $task->addParent($host);
        $inst = $host->getOneChild('Instance');
        if ($inst)
            $task->addParent($inst);
        $task->addParent($host->getOneParent('Server'));
        if (is_object($orig)) $task->addParent($orig);
        $task->Save();
        return array("task" => $task);
    }

    /**
     * mount
     * Créé un point de monateg instantanné pour récupéré un fichier ou deux
     * @param Object Tache
     */
    public function mount($task){
        $host = $this->getOneParent('Host');
        $bdds = $host->getChildren('Bdd');
        $apachesrv = $host->getOneParent('Server');
        $inst = $host->getOneChild('Instance');
        try {
            //Préparation du backup
            $act = $task->createActivity('Création du dossier backup-'.$this->Identifiant, 'Info');
            //creation du dossier
            $cmd = 'mkdir /home/' . $host->NomLDAP . '/backup-'.$this->Identifiant;
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails(print_r($out,true));
            $act->Terminate(true);

            $act = $task->createActivity('Modification des droits', 'Info');
            $cmd = 'chown ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . ' -R';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);

            $act = $task->createActivity('Montage du point de restauration en lecture seule', 'Info');
            $cmd = 'cd /home/' . $host->NomLDAP . '/ && sudo -u '.$host->NomLDAP.' borg mount backup::'.$this->Identifiant.' backup-'.$this->Identifiant;
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);

            //definition en point de montage
            $this->Mounted = true;
            parent::Save();
            return true;
        }catch (Exception $e){
            $act->addDetails('Erreur: '.$e->getMessage());
            $act->Terminate(false);
            throw new Exception($e->getMessage());
        }catch (Error $e){
            $act->addDetails('Erreur: '.$e->getMessage());
            $act->Terminate(false);
            throw new Exception($e->getMessage());
        }
    }
    /**
     * umountAllBackup
     * Démonter tous les backups en point de montage
     */
    public static function umountAllBackup() {

    }
    /**
     * backup
     * creation d'une sauvegarde d'un hébergement
     * @param Object Tache
     */
    public function backup($task = null) {
        $host = $this->getOneParent('Host');
        $bdds = $host->getChildren('Bdd');
        $apachesrv = $host->getOneParent('Server');
        $restopoint = $this->Identifiant;
        try {
            //Préparation du backup
            $act = $task->createActivity('Préparation et nettoyage backup ', 'Info');
            //test des dossiers
            $cmd = 'if [ ! -d /home/' . $host->NomLDAP . '/sql ]; then mkdir /home/' . $host->NomLDAP . '/sql; fi';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            $cmd = 'if [ ! -d /home/' . $host->NomLDAP . '/backup ]; then mkdir /home/' . $host->NomLDAP . '/backup;borg init --encryption=none /home/' . $host->NomLDAP . '/backup; fi';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            //test du dépot
            $cmd = 'if [ $(ls /home/' . $host->NomLDAP . '/backup | wc -l) == 0 ]; then borg init --encryption=none /home/' . $host->NomLDAP . '/backup; fi';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            //suppression des dump précédents
            $cmd = 'rm /home/' . $host->NomLDAP . '/sql/* -f';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            //Sauvegarde base des donnée
            foreach ($bdds as $bdd) {
                $bddsrv = $bdd->getOneParent('Server');
                $act = $task->createActivity('Sauvegarde base de donnée '.$bdd->Nom, 'Info', $task);
                $cmd = 'cd /home/' . $host->NomLDAP . '/ && mysqldump -h '.$bddsrv->InternalIP.' -u ' . $host->NomLDAP . ' -p' . $host->Password . ' ' . $bdd->Nom . ' > sql/'.$bdd->Nom.'-'.$restopoint.'.sql';
                $out = $apachesrv->remoteExec($cmd);
                $act->addDetails($cmd);
                $act->addDetails($out);
                $act->Terminate(true);
            }
            $act = $task->createActivity('Backup fichier', 'Info', $task);
            $cmd = 'cd /home/' . $host->NomLDAP . ' && borg create backup::'.$restopoint.' * --exclude "backup" --exclude "cgi-bin" --exclude "logs" --exclude "azkocms_medias" --exclude "azkocms_skins"';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            //modification des droits
            $act = $task->createActivity('Modification des droits', 'Info', $task);
            $cmd = 'chown ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . '/backup -R';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            $this->Success = true;
            parent::Save();
            return true;
        }catch (Exception $e){
            $act->addDetails(print_r($this,true));
            $act->addDetails('Erreur: '.$e->getMessage());
            $act->Terminate(false);
            $this->Success = false;
            parent::Save();
            throw new Exception($e->getMessage());
        }
    }
}