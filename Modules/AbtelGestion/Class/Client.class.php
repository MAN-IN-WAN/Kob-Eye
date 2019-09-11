<?php

class Client extends AbtelGestionBase {
    protected $entity = 'clients';
    protected $identifier = 'Code';

    public function Set($prop, $newValue){
        file_put_contents('/tmp/debugCli',$prop.' : '.$newValue.PHP_EOL,8 );
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

        if($prop == 'EstProspect' || $prop == 'EstClient' || $prop == 'EstConstructeur' || $prop == 'EstFournisseur'){
            //file_put_contents('/tmp/debugCli',$prop.' : '.$newValue.PHP_EOL,8 );
            if(!empty($newValue)) return true;
            //file_put_contents('/tmp/debugCli','------------------ OOOKKKK'.PHP_EOL,8 );
        }

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

