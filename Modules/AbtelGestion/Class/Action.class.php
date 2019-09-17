<?php

class Action extends AbtelGestionBase {
    protected $entity = 'actions';
    protected $identifier = 'Id';

    public function Set($prop, $newValue){
        //Gestion du rtf
        if(!empty($newValue) && is_string($newValue) && strpos($newValue,'{\rtf1\ansi') !== false){
            $reader = new RtfReader();
            $reader->Parse($newValue);
            $formatter = new RtfHtml();
            $desc=$formatter->Format($reader->root);
            $newValue = utf8_encode($desc);
        }

        if($prop == "Fichier" && !$this->getOrigin()){
            if(!empty($newValue)){
                if(!empty($this->NOTEADDED)) {
                    $this->props['ACNOTE'] .= PHP_EOL . $newValue . PHP_EOL;
                } else {
                    $this->props['ACNOTE'] =  $newValue . PHP_EOL;
                }
                $this->FILEADDED = 1;
            }
            return true;
        }
        if($prop == "Note" && !$this->getOrigin()){
            if(!empty($newValue)){
                if(!empty($this->FILEADDED)) {
                    $this->props['ACNOTE'] .= $newValue;
                } else {
                    $this->props['ACNOTE'] = $newValue;
                }
                $this->NOTEADDED = 1;
            }
            return true;
        }

        if($prop == "Duree" && !$this->getOrigin()){
            if(!empty($newValue)){
                $this->props['ACDUREE'] = gmdate('Hi',$newValue);
            }
            return true;
        }

        if($prop == "CodeContrat" && !$this->getOrigin()){
            if(empty($newValue)){
                $this->props['ACCADRE'] = 2;
            } else {
                $this->props['ACCADRE'] = 1;
            }
        }

        $sqlDate = array('DateCrea');
        if(in_array($prop,$sqlDate)){
            $newValue = date('Y-m-d',$newValue);
        }

        if($prop == 'Id' && $this->getOrigin()){
            $this->props['IdGestion'] = $newValue;
            return true;
        }

        $sqlHeure = array('DateCloture','DateTermine'); //format 20190416101700
        if(in_array($prop,$sqlHeure)){
            $newValue = date('H:i:s',$newValue);
        }

        return parent::Set($prop, $newValue);
    }


    public function Save(){
        $ok = parent::Save();

        if(!$this->getOrigin() && empty ($this->props['IdGestion']) && empty($this->props['Id'])){
            $this->props['IdGestion'] = $this->con_handle->lastInsertId();
        }

        return $ok;
    }
}