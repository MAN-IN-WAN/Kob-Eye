<?php
class Job extends genericClass {
    protected $tag = '[BASEJOB]';
    protected static $KEObj = 'BaseJob';

    public static function execute() {
        //intialisation des dates
        $d = time();
        $week = array('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche');
        $weekday = $week[date('w',$d)];
        $hour = date('H',$d);
        $minute = intval(date('i',$d));
        $month = intval(date('m',$d));
        $monthday = date('j',$d);
        $jobs = Sys::getData('AbtelBackup',static::$KEObj.'/Enabled=1&(!Minute=*+Minute='.$minute.'!)&(!Heure=*+Heure='.$hour.'!)&(!Jour=*+Jour='.$monthday.'!)&(!Mois=*+Mois='.$month.'!)&(!(!Lundi=0&Mardi=0&Mercredi=0&Jeudi=0&Vendredi=0&Samedi=0&Dimanche=0!)+(!'.$weekday.'=1!)!)');

        foreach ($jobs as $j) {
            $j->run();
        }
    }

    public function createActivity($title,$obj=null,$jPSpan=0,$Type='Exec') {
        $act = genericClass::createInstance('AbtelBackup','Activity');
        $act->addParent($this);
        if($obj)
        $act->addParent($obj);
        $act->Titre = $this->tag.date('d/m/Y H:i:s').' > '.$this->Titre.' > '.$title;
        $act->Started = true;
        $act->Type= $Type;
        $act->Progression = 0;
        $act->ProgressStart = $this->Progression;
        $act->ProgressSpan = $jPSpan;
        $act->PJob = $this;
        $act->Save();

        return $act;
    }

    public function runNow() {

        if ($this->Running){
            $this->addError(Array('Message'=>'Impossible de démarrer le job. Il est déjà en cours.'));
            return false;
        }
        $cmd = 'bash -c "exec nohup setsid php cron.php backup.abtel.local AbtelBackup/'.static::$KEObj.'/'.$this->Id.'/run.cron > /dev/null 2>&1 &"';

        exec($cmd);
        return true;
    }


    /**
     * setStep
     * Définie l'étape en cours.
     * Permet une reprise en repartant de l'étape en erreur
     */
    protected function setStep($step){
        $this->Step = $step;
        parent::Save();
    }


    protected function clearAct($full = true){
        $acts = $this->getChildren('Activity/Started=1&Errors=0&Success=0&Type=Exec');
        foreach($acts as $act){
            print_r($act);
            if($full)
                $act->Errors = 1;

            $act->Started=0;
            $act->addDetails(' ---> Arrêt Utilisateur','cyan',true);
            $act->Save();
            //print_r($act);

        }
    }

    protected function addProgression($nb){
        $this->Progression += ($nb/$this->TotalProgression)*100;
    }
}