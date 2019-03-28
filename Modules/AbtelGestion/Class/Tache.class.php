<?php

class AbtelTache extends AbtelGestionBase {
    protected $entity = 'taches';


    public function Set($prop, $newValue){
        if($prop == 'Id'){
            if(!$this->getOrigin()){
                $this->props['NumeroTicket'] = $newValue;
            }else{
                $this->props['Numero'] = $newValue;
            }
            return true;
        }

        return parent::Set($prop, $newValue);
    }

    /**
     * @param $prop
     * @return bool|mixed
     */
    public function Get($prop){
        if($prop == 'Id'){
            if(!$this->getOrigin()){
                return $this->props['NumeroTicket'];
            }else{
                return $this->props['Numero'];
            }
        }

        return parent::Get($prop);
    }
}
