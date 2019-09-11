<?php
class Bash extends Beacon {
    function Generate() {
        Beacon::Generate();
        $Vars = explode("|",$this->Vars);
        $this->Attributes = $Vars;
        switch($this->Beacon) {
            case "BASH":
                switch ($this->Attributes[0]){
                    case "COLOR":
                        include_once (ROOT_DIR.'Class/Utils/BashColors.class.php');
                        $c = new BashColors();
                        $color = isset($this->Attributes[1])?$this->Attributes[1]:"green";
                        echo $c->getColoredString($this->getString(), $color) . "\n";
                    break;
                }
            break;
        }
    }
    function getString() {
        $tmp = Parser::getContent($this->ChildObjects);
        $tmp = Process::processingVars($tmp);
        $this->Data = Parser::PostProcessing($tmp);
        return $this->Data;
    }
    function Affich(){
        return '';
    }
}

