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

    public function Save(){
        if(empty ($this->props['NumeroTicket']) && empty($this->props['Numero'])){
            //On recupere un numero depuis la base de la gestion

            $l = $this->getTicketLetter();
            $req = 'SELECT MAX(NumeroTicket) FROM taches WHERE NumeroTicket LIKE \''.$l.'%\';';
            $q = $this->con_handle->query($req);
            $res = $q->fetchColumn();
            $ord = (int)substr($res,1);
            $new = $l.sprintf('%05d',$ord+1);

            if(!$this->getOrigin()){
                $this->props['NumeroTicket'] = $new;
            }else{
                $this->props['Numero'] = $new;
            }
        }

        return parent::Save();
    }


    private function getTicketLetter(){
        $base = ord('A');
        $new = $base + ((int) date('Y')) - 2010;
        $let = chr($new);
        return $let;
    }
}
