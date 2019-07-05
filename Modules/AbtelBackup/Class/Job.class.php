<?php
class Job extends genericClass {
    protected $tag = '[BASEJOB]';
    protected static $KEObj = 'BaseJob';
    protected static $desc = 'Job de base';

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
            $task = genericClass::createInstance('Systeme', 'Tache');
            $task->Type = 'Fonction';
            $task->Nom = static::$desc.' :' . $j->Titre;
            $task->TaskModule = 'AbtelBackup';
            $task->TaskObject = static::$KEObj;
            $task->TaskId = $j->Id;
            $task->TaskFunction = 'run';
            $task->TaskType = 'backup';
            $task->addParent($j);
            $task->Save();
        }
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
    /**
     * saveStats
     * Sauvegarde les statistiques dans la tache
     */
    public function saveStats($task) {
        $act = $task->createActivity('Enregistrement des statistiques');
        $stats = Sys::getData('AbtelBackup','State/tmsCreate>='.$task->DateDebut.'&tmsCreate<='.time(),0,1000000);
        $out = array(
            "RX"=> array(),
            "TX"=> array(),
            "Io"=> array(),
            "Cpu"=> array(),
            "Ram"=> array()
        );
        foreach ($stats as $s){
            array_push($out['RX'],array($s->tmsCreate*1000,$s->RX));
            array_push($out['TX'],array($s->tmsCreate*1000,$s->TX));
            array_push($out['Io'],array($s->tmsCreate*1000,$s->IOUsage));
            array_push($out['Cpu'],array($s->tmsCreate*1000,$s->CpuUsage));
            array_push($out['Ram'],array($s->tmsCreate*1000,$s->RamUsage));
        }
        $task->Graph = json_encode($out);
        //$act->addDetails($task->Graph);
        $act->Terminate(true);
        $task->Save();
    }
}