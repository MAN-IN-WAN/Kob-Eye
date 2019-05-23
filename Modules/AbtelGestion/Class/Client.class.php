<?php

class Client extends AbtelGestionBase {
    protected $entity = 'clients';
    protected $identifier = 'Code';

    public function Set($prop, $newValue){
        if($prop == 'Code'){
            if($newValue === 0 || $newValue === '0'){
                $newValue = 'ABT_0';
            } elseif ($newValue == 'ABT_0'){
                $newValue = '0';
            }
        }

        if($prop == 'Id'){
            $this->props['IdGestion'] = $newValue;
            return true;
        }
        if($prop == 'Historique') $prop = '__z__'.$prop;

        //Gestion du rtf
        if(!empty($newValue) && is_string($newValue) && strpos($newValue,'{\rtf1\ansi') !== false){
            $reader = new RtfReader();
            $reader->Parse($newValue);
            $formatter = new RtfHtml();
            $desc=$formatter->Format($reader->root);
            $desc=strip_tags($desc);
            $newValue = $desc;
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
