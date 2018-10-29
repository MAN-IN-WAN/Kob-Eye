<?php
include_once 'Class/Utils/BashColors.class.php';
class Activity extends genericClass{

    function addDetails($det,$color = 'cyan',$mute = false) {
        $this->Details .= date('d/m/Y H:i:s').' > '.$det."\n";
        if(!$mute){
            $b = new BashColors();
            //echo $b->getColoredString($det."\n",$color);
        }
        $this->Save();
    }
    function addProgression($prog) {
        if (!intval($prog)) return;
        $this->Progression += intval($prog);
        $this->Save();
        if ($this->Progression>=100){
            $this->Progression = 100;
            $this->Terminate();
        }

        //Traitement de la progression du Job
        $this->setJobProgress();
    }
    function setProgression($prog) {
        if ($this->Progression==intval($prog)) return;
        $this->Progression = intval($prog);
        $this->Save();
        if ($this->Progression>=100){
            $this->Progression = 100;
            $this->Terminate();
        }

        //Traitement de la progression du Job
        $this->setJobProgress();
    }
    function Terminate($success = true) {
        $this->Terminated = true;
        $this->Success = $success;
        $this->Errors = !$success;
        $this->Save();
    }
    function setJobProgress(){
        $task = $this->getOneParent('Tache');
        if(!$task) return false;
        $job = $task;

        $prog = intval($this->ProgressStart + $this->ProgressSpan*$this->Progression/100);
        if($prog != $job->Progression){
            $job->Progression = $prog;
            $job->Save();
        }


        return true;
    }
}