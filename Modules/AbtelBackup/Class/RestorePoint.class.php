<?php
class RestorePoint extends genericClass {
    public function restoreNow() {
        $cmd = 'bash -c "exec nohup setsid php cron.php backup.abtel.local AbtelBackup/RestorePoint/'.$this->Id.'/restore.cron > /dev/null 2>&1 &"';
        exec($cmd);
        return true;
    }
    public function createActivity($title) {
        $v =  $this->getOneParent('EsxVm');
        $act = genericClass::createInstance('AbtelBackup','Activity');
        $act->addParent($v);
        $act->Titre = '[VMJOB] '.date('d/m/Y H:i:s').' > '.$title;
        $act->Started = true;
        $act->Progression = 0;
        $act->Save();
        return $act;
    }

    public function restore(){
        //init
        $GLOBALS['Systeme']->Db[0]->query("SET AUTOCOMMIT=1");
        //pour chaque vm
        $v =  $this->getOneParent('EsxVm');
        $borg = $this->getOneParent('BorgRepo');
        try{
            $act = $this->createActivity(' > Restauration de la vm '.$v->Titre.' au point '.$this->Name.'');
            $act->addDetails('Création du chemin de restauration /backup/nfs/Restore/'.$v->Titre.'::'.$this->Name.'','yellow');
            AbtelBackup::localExec("if [ ! -d '/backup/nfs/Restore/".$v->Titre."::".$this->Name."' ]; then sudo mkdir -p '/backup/nfs/Restore/'; fi");
            $act->setProgression(15);
            $act->addDetails('Extraction borg '.$v->Titre.'::'.$this->Name,'yellow');
            $det = AbtelBackup::localExec("export BORG_PASSPHRASE='".BORG_SECRET."' && cd /backup/restore && borg extract ".$borg->Path."::".$this->Name."",$act);
            $act->setProgression(65);
            $act->addDetails('Décompression du fichier','yellow');
            AbtelBackup::localExec("cd /backup/restore && tar xvf '/backup/restore/backup/nfs/".$v->Titre.".tar'");
            $act->setProgression(70);
            $act->addDetails('Déplacement de la restauration','yellow');
            AbtelBackup::localExec("sudo mv '/backup/restore/backup/nfs/".$v->Titre."/".$v->Titre."-A' '/backup/nfs/Restore/".$v->Titre."::".$this->Name."'");
            $act->setProgression(75);
            $act->addDetails('Suppression de l\'archive','yellow');
            AbtelBackup::localExec("sudo rm -Rf '/backup/restore/backup'");
            $act->addDetails('Modification des droits','yellow');
            AbtelBackup::localExec("sudo chown nfsnobody:nfsnobody -R '/backup/nfs/Restore'");
            $act->setProgression(100);
            $act->addDetails('Vm disponible','yellow');
            $act->Terminate();
        }catch (Exception $e){
            $act->addDetails($v->Titre." ERROR => ".$e->getMessage(),'red');
            $act->Terminated = true;
            $act->Errors = true;
            $act->Save();
        }
        return true;
    }
}