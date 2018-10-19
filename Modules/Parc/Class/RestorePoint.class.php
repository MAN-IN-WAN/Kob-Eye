<?php
class RestorePoint extends genericClass{
    /**
     * createRestoreTask
     * Création d'une tache de restauration
     */
    public function createRestoreTask($orig=null) {
        $host = $this->getOneParent('Host');
        $task = genericClass::createInstance('Parc', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Restauration de l\'hébergement ' . $host->Nom.' du point de restauration '.$this->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'RestorePoint';
        $task->TaskId = $this->Id;
        $task->TaskFunction = 'restore';
        $task->addParent($host);
        $inst = $host->getOneChild('Instance');
        if ($inst)
            $task->addParent($inst);
        $task->addParent($host->getOneParent('Server'));
        if (is_object($orig)) $task->addParent($orig);
        $task->Save();
        return true;
    }
    /**
     * backup
     * Fonction de sauvegarde
     * @param Object Tache
     */
    public function restore($task = null){
        $host = $this->getOneParent('Host');
        $bdds = $host->getChildren('Bdd');
        $apachesrv = $host->getOneParent('Server');
        $inst = $host->getOneChild('Instance');
        try {
            //Préparation du backup
            $act = $this->createActivity('Préparation et nettoyage de la restauration ', 'Info', $task);
            //suppression des dossiers
            $cmd = 'cd /home/' . $host->NomLDAP . ' && ls';
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($cmd);
            $act->addDetails($out);
            /*$cmd = 'if [ ! -d /home/' . $host->NomLDAP . '/backup ]; then mkdir /home/' . $host->NomLDAP . '/backup;borg init --encryption=none /home/' . $host->NomLDAP . '/backup; fi';
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
                $act = $this->createActivity('Sauvegarde base de donnée '.$bdd->Nom, 'Info', $task);
                $cmd = 'cd /home/' . $host->NomLDAP . '/ && mysqldump -h db.maninwan.fr -u ' . $host->NomLDAP . ' -p' . $host->Password . ' ' . $bdd->Nom . ' > sql/'.$bdd->Nom.'-'.date('YmdHis').'.sql';
                $out = $apachesrv->remoteExec($cmd);
                $act->addDetails($cmd);
                $act->addDetails($out);
                $act->Terminate(true);
            }
            $restopoint = date('YmdHis');
            $restodate = date('d/m/Y à H:i:s');
            $act = $this->createActivity('Backup fichier', 'Info', $task);
            $cmd = 'cd /home/' . $host->NomLDAP . ' && borg create backup::'.$restopoint.' * --exclude "backup" --exclude "cgi-bin" --exclude "logs"';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            //modification des droits
            $act = $this->createActivity('Modification des droits', 'Info', $task);
            $cmd = 'chown ' . $host->NomLDAP . ':users /home/' . $host->NomLDAP . ' -R';
            $act->addDetails($cmd);
            $out = $apachesrv->remoteExec($cmd);
            $act->addDetails($out);
            $act->Terminate(true);
            //création du point de restauration
            $rp = genericClass::createInstance('Parc','RestorePoint');
            $rp->Titre = 'Sauvegarde date: '.$restodate;
            $rp->Identifiant = $restopoint;
            $rp->addParent($host);
            if ($inst)
                $rp->addParent($inst);
            $rp->Save();
            return true;*/
        }catch (Exception $e){
            $act->addDetails('Erreur: '.$e->getMessage());
            $act->Terminate(false);
            throw new Exception($e->getMessage());
        }
    }
}