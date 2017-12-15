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
        if (!intval($prog)) return;
        $this->Progression += intval($prog);
        $this->Save();
        if ($this->Progression>=100) $this->Terminate();
    }
    function setProgression($prog) {
        if ($this->Progression==intval($prog)) return;
        $this->Progression = intval($prog);
        $this->Save();
        if ($this->Progression>=100) $this->Terminate();
    }
    function Terminate() {
        $this->Terminated = true;
        $this->Success = true;
        $this->Save();
    }
}