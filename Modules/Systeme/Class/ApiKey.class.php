<?php


class ApiKey extends genericClass {

    public function Save(){
        if(!$this->Key || $this->Key == ''){
            $pref = bin2hex(random_bytes(5));
            $this->Key = uniqid($pref.'-');
        }
        parent::Save();
    }

}