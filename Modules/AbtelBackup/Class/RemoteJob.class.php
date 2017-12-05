<?php
class RemoteJob extends genericClass {
    private $TotalProgression = 100;
    public static function execute() {
        //intialisation des dates
        $d = time();
        $week = array('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche');
        $weekday = $week[date('w',$d)];
        $hour = date('H',$d);
        $minute = intval(date('i',$d));
        $month = intval(date('m',$d));
        $monthday = date('j',$d);
        $jobs = Sys::getData('AbtelBackup','RemoteJob/Enabled=1&(!Minute=*+Minute='.$minute.'!)&(!Heure=*+Heure='.$hour.'!)&(!Jour=*+Jour='.$monthday.'!)&(!Mois=*+Mois='.$month.'!)&(!(!Lundi=0&Mardi=0&Mercredi=0&Jeudi=0&Vendredi=0&Samedi=0&Dimanche=0!)+(!'.$weekday.'=1!)!)');

        foreach ($jobs as $j) {
            $j->run();
        }
    }
    public function createActivity($title) {

        $act = genericClass::createInstance('AbtelBackup','Activity');
        $act->addParent($this);
        $act->Titre = '[REMOTEJOB] '.date('d/m/Y H:i:s').' > '.$this->Titre.' > '.$title;
        $act->Started = true;
        $act->Progression = 0;
        $act->Save();
        return $act;
    }
    public function runNow() {
        if ($this->Running){
            $this->addError(Array('Message'=>'Impossible de démarrer le job. Il est déjà en cours.'));
            return false;
        }
        $cmd = 'bash -c "exec nohup setsid php cron.php backup.abtel.local AbtelBackup/RemoteJob/'.$this->Id.'/run.cron > /dev/null 2>&1 &"';
        exec($cmd);
        return true;
    }
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
                    $act = $this->createActivity($ss->Titre.' > Initialisation du dépôt distant');
                    $this->initJob($ss,$dev,$act);
                }

                //montage
                if (intval($this->Step)<=2){
                    unset($act);
                    $act = $this->createActivity($ss->Titre.' > Synchronisation',$ss);
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
     * setStep
     * Définie l'étape en cours.
     * Permet une reprise en repartant de l'étape en erreur
     */
    private function setStep($step){
        $this->Step = $step;
        parent::Save();
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
        $this->addProgression(10);
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

    private function clearAct($full = true){
        $acts = $this->getChildren('Activity/Started=1&Errors=0&Success=0');
        foreach($acts as $act){
            //print_r($act);
            if($full)
                $act->Errors = 1;

            $act->addDetails(' ---> Arrêt Utilisateur','cyan',true);
            //print_r($act);

        }
    }
    private function addProgression($nb){
        $this->Progression += ($nb/$this->TotalProgression)*100;
    }
}