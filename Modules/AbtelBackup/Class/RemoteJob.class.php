<?php
require_once 'Modules/AbtelBackup/Class/Job.class.php';

class RemoteJob extends Job {
    protected $tag = '[REMOTEJOB]';
    protected static $KEObj = 'RemoteJob';
    protected static $desc = 'Job externalisation';
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
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Collecteur';
        $task->Nom = 'Stop :' . $this->Titre;
        $task->addParent($this);
        $task->Save();

        $v = Sys::getOneData('AbtelBackup','BorgRepo/'.$this->CurrentBorgRepo);
        if($v){
            $act = $task->createActivity($v->Titre . ' > Arret Utilisateur: Step ' . $this->Step,'Info');
            $act->addDetails($v->Titre . "Arret Utilisateur", 'red', true);
        } else{
            $act = $task->createActivity('Borg non défini > Arret Utilisateur: Step ' . $this->Step,'Info');
            $act->addDetails("Arret Utilisateur", 'red', true);
        }



        if ($this->Running){
            switch ($this->Step) {
                case 1:
                    $this->addError(Array('Message' => 'Impossible de stopper le job pendant l\'initialisation.'));
                    $act->addDetails("Impossible de stopper le job pendant l'initialisation.", 'red', true);
                    $act->Terminate(false);

                    return false;
                    break;
                case 2:
                    if ($pid = AbtelBackup::getPid('rsync')) {
                        $this->clearAct(false);
                        AbtelBackup::localExec('sudo kill -9 '.$pid);
                        $this->addSuccess(Array('Message' => 'Synchronisation stoppée avec succès.'));
                        $act->Terminate();

                    } else {
                        $this->clearAct(true);
                        $this->addWarning(Array('Message' => 'Le processus n\'a pas été trouvé.'));
                        $act->addDetails("Le processus n'a pas été trouvé.", 'red', true);
                        $act->Terminate(false);

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
                    $act->Terminate();

                    return true;
                    break;
            }
        }else{

            $act->addDetails("Arret Utilisateur : Impossible de stopper le job $this->Titre. Il n'est pas démarré.", 'red',true);
            $act->Terminate(false);

            $this->addError(Array('Message'=>'Impossible de stopper le job. Il n\'est pas démarré.'));
            return false;
        }
    }
    /**
     * run
     * Demarre ou resume un job de backup de vm
     */
    public function run($task) {
        //test running
        if ($this->Running) {
            $act = $task->createActivity(' > Impossible de démarrer, le job est déjà en cours d\'éxécution','Info');
            $act->Terminate(false);
            return;
        }
        $this->resetState();
        $this->Running = true;
        parent::Save();
        //init
        Klog::l('DEBUG demarrage remote job');
        $act = $task->createActivity(' > Demarrage du Job Remote : '.$this->Titre.' ('.$this->Id.')','Info');
        $act->Terminate();

        $GLOBALS['Systeme']->Db[0]->query("SET AUTOCOMMIT=1");
        //pour chaque partage
        $sss = Sys::getData('AbtelBackup','RemoteJob/'.$this->Id.'/BorgRepo');

        //calcul des progress span
        $pSpan = array();
        for($n =0; $n < count($this->pStarts);$n++){
            $pSpan[] = ($this->pStarts[$n+1] - $this->pStarts[$n])/count($sss);
        }

        foreach ($sss as $ss){
            Klog::l('DEBUG remote ==> '.$ss->Id.' STEP: '.$this->Step);
            $act = $task->createActivity(' > Demarrage du Depot : '.$ss->Titre.' ('.$ss->Id.')','Info');
            $act->Terminate();
            //définition de la vm en cours
            $this->setStep(1);
            $this->setCurrentBorgRepo($ss->Id);
            $dev = $this->getOneParent('RemoteServer');
            try {
                //initialisation
                if (intval($this->Step)<=1){
                    unset($act);
                    $act = $task->createActivity($ss->Titre.' > Initialisation du dépôt distant','Exec',$pSpan[0]);
                    $this->initJob($ss,$dev,$act);
                }

                //montage
                if (intval($this->Step)<=2){
                    unset($act);
                    $act = $task->createActivity($ss->Titre.' > Synchronisation','Exec',$pSpan[1]);
                    $act = $this->syncJob($ss,$dev,$act);
                }

                $act = $task->createActivity(' > Fin du Depot : '.$ss->Titre.' ('.$ss->Id.')','Info');
                $act->Terminate();

            }catch (Exception $e){
                if(!$act) $act = $task->createActivity($ss->Titre.' > Exception: Etape '.$this->Step,'Info');
                $act->addDetails($ss->Titre." ERROR => ".$e->getMessage(),'red');
                $act->Terminate(false);
                //opération terminée
                $this->Running = false;
                $this->Errors = true;
                parent::Save();
                return;
            }
        }
        //opération terminée
        $this->resetState();
        return true;
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