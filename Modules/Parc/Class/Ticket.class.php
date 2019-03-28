<?php

class Ticket extends genericClass
{

    public function Save(){

        return parent::Save();
    }

    public function Set($Prop, $newValue) {

        if (empty($Prop)) return false;

        $Props = $this -> Proprietes(false, true);
        if(!$Props) $Props = array();
        for ($i = 0; $i < sizeof($Props); $i++) {
            if ($Props[$i]["Nom"] == $Prop) {
                if ($Props[$i]["Type"] == "date") {
                    if(is_numeric($newValue)) {
                        $newValue = intval($newValue);
                    }else{
                        $newValue = strtotime($newValue);
                    }
                    $this -> {$Prop} = $newValue;
                    return true;
                }
            }
        }

        return parent::Set($Prop, $newValue);
    }


    public function Verify(){

        if(!empty($this->CodeClient)){
            $cli = Sys::getOneData('Parc','Client/CodeGestion='.$this->CodeClient);
            if(!$cli) {
                $this->addError(array("Message"=>"Client introuvable dans la base du Parc"));
                return false;
            }

            $par = $this->getOneParent('Client');
            if($par){
                if($par->Id != $cli->Id){
                    $this->delParent($par);
                }
                $this->addParent($cli);
            } else{
                $this->addParent($cli);
            }
        }

        return parent::Verify();
    }

}