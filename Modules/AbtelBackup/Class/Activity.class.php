<?php
class Activity extends genericClass{

    function addDetails($det,$color = 'cyan',$mute = false) {
        $this->Details .= date('d/m/Y H:i:s').' > '.$det."\n";
        if(!$mute){
            $b = new BashColors();
            echo $b->getColoredString($det."\n",$color);
        }
        $this->Save();
    }
    function addProgression($prog) {
        if(!$prog) return false;
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
        if(intval($prog) == $this->Progression) return false;
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
        $this->Error = !$success;
        $this->Save();
    }
    function setJobProgress(){
//        if($job = $this->getOneParent('VmJob')){
//
//        }elseif ($job = $this->getOneParent('SambaJob')){
//
//        }elseif ($job = $this->getOneParent('RemoteJob')){
//
//        }else{
//            $this->addError(Array('Message'=>'Activité sans job parent'));
//            return false;
//        }
        if(!$this->PJob) return false;
        $job = $this->PJob;

        $prog = intval($this->ProgressStart + $this->ProgressSpan*$this->Progression/100);
        if($prog != $job->Progression){
            $job->Progression = $prog;
            $job->Save();
        }


        return true;
    }
}