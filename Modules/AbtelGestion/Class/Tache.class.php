<?php

require_once '/var/www/html/Class/Lib/rtf2html.php';

class AbtelTache extends AbtelGestionBase {
    protected $entity = 'taches';
    protected $identifier = 'NumeroTicket';

    public function Set($prop, $newValue){
        if($prop == 'Id'){
            if(!$this->getOrigin()){
                $this->props['NumeroTicket'] = $newValue;
            }else{
                $this->props['Numero'] = $newValue;
            }
            return true;
        }

        $sqlDate = array('DateCrea','DateEcheance');
        if(in_array($prop,$sqlDate)){
            $newValue = date('Y-m-d',$newValue);
        }

        $fausseDate = array('DateCloture','DateTermine'); //format 20190416101700
        if(in_array($prop,$fausseDate)){
            if(!empty($newValue)){
                $newValue = date('YmdHis',$newValue);
                $newValue .='00';
            } else {
                $newValue = '';
            }
        }

        if( $prop == 'CodeClient' && $newValue == 'ABT_0'){
            $newValue = '0';
        }
        if( $prop == 'TACLIENT' && $newValue == '0'){
            $newValue = 'ABT_0';
        }

        if($prop == "CodeContrat" && !$this->getOrigin()){
            if(empty($newValue)){
                $this->props['TACADRE'] = 2;
            } else {
                $this->props['TACADRE'] = 1;
            }
        }

        //Gestion du rtf
        if(!empty($newValue) && is_string($newValue) && strpos($newValue,'{\rtf1\ansi') !== false){
            $reader = new RtfReader();
            $reader->Parse($newValue);
            $formatter = new RtfHtml();
            $desc=$formatter->Format($reader->root);
            $newValue = $desc;
        }


        return parent::Set($prop, $newValue);
    }

    /**
     * @param $prop
     * @return bool|mixed
     */
    public function Get($prop ,$Nom = false){
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
