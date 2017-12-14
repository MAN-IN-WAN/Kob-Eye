<?php
require_once 'Modules/AbtelBackup/Class/Job.class.php';

class RemoteJob extends Job {
    protected $tag = '[REMOTEJOB]';
    protected static $KEObj = 'RemoteJob';
    protected $pStarts = array(
        0,
        10,
        100
    );
    /**
     * stop
     * Stoppe un job de backup
     */
    public function stop()
    {
        $vms = Sys::getData('AbtelBackup', 'RemoteJob/' . $this->Id . '/SambaShare');
        foreach ($vms as $v) {
            $act = $this->createActivity($v->Titre . ' > Arret Utilisateur: Step ' . $this->Step, $v);
            $act->addDetails($v->Titre . "Arret Utilisateur", 'red', true);
            $act->setProgression(100);
            $act->Success = true;
            $act->Save();
        }
        switch ($this->Step) {
            case 1:
                $this->addError(Array('Message' => 'Impossible de stopper le job pendant l\'initialisation.'));
                return false;
                break;
            case 2:
                if ($pid = AbtelBackup::getPid('rsync')) {
                    $this->clearAct(false);
                    AbtelBackup::localExec('sudo kill -9 '.$pid);
                    $this->addSuccess(Array('Message' => 'Synchronisation stoppée avec succès.'));
                } else {
                    $this->clearAct(true);
                    $this->addWarning(Array('Message' => 'Le processus n\'a pas été trouvé.'));
                }
                $this->Running = false;
                parent::Save();
                return true;
                break;
            default:
                $this->clearAct(true);
                $this->resetState();
                parent::Save();
                $this->addSuccess(Array('Message' => 'Job stoppé avec succès.'));
                return true;
                break;
        }
    }
    /**
     * run
     * Demarre ou resume un job de backup de vm
     */
    public function run() {
        //test running
        if ($this->Running) {
            $act = $this->createActivity(' > Impossible de démarrer, le job est déjà en cours d\'éxécution');
            $act->Terminate();
            return;
        }
        $this->resetState();
        $this->Running = true;
        parent::Save();
        //init
        Klog::l('DEBUG demarrage remote job');
        $GLOBALS['Systeme']->Db[0]->query("SET AUTOCOMMIT=1");
        //pour chaque partage
        $sss = Sys::getData('AbtelBackup','RemoteJob/'.$this->Id.'/BorgRepo');
        $this->TotalProgression =  Sys::getCount('AbtelBackup','RemoteJob/'.$this->Id.'/BorgRepo')*100;

        //calcul des progress span
        $pSpan = array();
        for($n =0; $n < count($this->pStarts);$n++){
            $pSpan[] = ($this->pStarts[$n+1] - $this->pStarts[$n])/count($sss);
        }

        foreach ($sss as $ss){
            Klog::l('DEBUG remote ==> '.$ss->Id.' STEP: '.$this->Step);
            //définition de la vm en cours
            $this->setStep(1);
            $this->setCurrentBorgRepo($ss->Id);
            $dev = $this->getOneParent('RemoteServer');
            try {
                //initialisation
                if (intval($this->Step)<=1){
                    unset($act);
                    $act = $this->createActivity($ss->Titre.' > Initialisation du dépôt distant',$ss,$pSpan[0]);
                    $this->initJob($ss,$dev,$act);
                }

                //montage
                if (intval($this->Step)<=2){
                    unset($act);
                    $act = $this->createActivity($ss->Titre.' > Synchronisation',$ss,$pSpan[1]);
                    $act = $this->syncJob($ss,$dev,$act);
                }

            }catch (Exception $e){
                if(!$act) $act = $this->createActivity($ss->Titre.' > Exception: Step '.$this->Step);
                $act->addDetails($ss->Titre." ERROR => ".$e->getMessage(),'red');
                $act->Terminated = true;
                $act->Errors = true;
                $act->Save();
                //opération terminée
                $this->Running = false;
                $this->Errors = true;
                parent::Save();
                return;
            }
        }
        //opération terminée
        $this->resetState();
    }

    /**
     * setCurrentVm
     * Déinfition de la vm en cours de traitement
     */
    private function setCurrentBorgRepo($ss){
        $this->CurrentBorgRepo = $ss;
        parent::Save();
    }
    /**
     * Nettoyage de l'esx et du stor elocal
     */
    private function initJob($ss,$dev,$act) {
        Klog::l('DEBUG Test INIT JOB');
        $this->setStep(1); //Initialisation
        $act->addDetails('Création des dossier','yellow');
        $dev->remoteExec("if [ ! -d '~/".$ss->getName()."' ]; then mkdir -p '~/".$ss->getName()."'; fi");
        $act->addProgression(100);
        parent::Save();
        return $act;
    }
    /**
     * syncJob
     * Syncrhonisation du dépôt
     */
    private function syncJob($ss,$dev,$act){
        $this->setStep(2); //Montage
        $act->addDetails('-> Synchronisation  '.$ss->Titre,'yellow');
        AbtelBackup::sync($ss->Path,$ss->getName(),$dev->Login,$dev->IP,$this->BandePassante*1000,$act);
        $act->addProgression(100);
        parent::Save();
        return $act;
    }
    /**
     * resetState
     * Reinitialisation du job
     */
    private function resetState(){
        $this->setStep(0); //'Attente'
        $this->setCurrentBorgRepo(0);
        $this->Running = false;
        $this->Progression = 0;
        parent::Save();
    }

}