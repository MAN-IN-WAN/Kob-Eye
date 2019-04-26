<?php

class Client extends AbtelGestionBase {
    protected $entity = 'clients';
    protected $identifier = 'Code';

    public function Set($prop, $newValue){
        //Gestion du rtf
        if(!empty($newValue) && is_string($newValue) && strpos($newValue,'{\rtf1\ansi') !== false){
            $reader = new RtfReader();
            $reader->Parse($newValue);
            $formatter = new RtfHtml();
            $desc=$formatter->Format($reader->root);
            $desc=strip_tags($desc);
            $newValue = $desc;
        }

        /*if($prop == "Fichier"){
            if(!empty($newValue)){
                $this->props['ACNOTE'] .= PHP_EOL.$newValue.PHP_EOL;
            }
            return true;
        }
        if($prop == "Note"){
            if(!empty($newValue)){
                $this->props['ACNOTE'] .= $newValue;
            }
            return true;
        }

        if($prop == "Duree"){
            if(!empty($newValue)){
                $this->props['ACDUREE'] = gmdate('Hi',$newValue);
            }
            return true;
        }

        $sqlDate = array('DateCrea');
        if(in_array($prop,$sqlDate)){
            $newValue = date('Y-m-d',$newValue);
        }

        $sqlHeure = array('DateCloture','DateTermine'); //format 20190416101700
        if(in_array($prop,$sqlHeure)){
            $newValue = date('H:i:s',$newValue);
        }
        */

        return parent::Set($prop, $newValue);
    }


    /*public function Save(){
        $ok = parent::Save();

        if(!$this->getOrigin() && empty ($this->props['IdGestion']) && empty($this->props['Id'])){
            $this->props['IdGestion'] = $this->con_handle->lastInsertId();
        }

        return $ok;
    }*/

}
