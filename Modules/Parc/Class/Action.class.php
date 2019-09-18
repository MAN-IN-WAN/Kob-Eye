<?php


class Parc_Action extends genericClass{
    protected $con_handle = null;
    protected $api_token = null;
    //TODO : Passer en CONF ?
    const GESTIONURL = 'http://api.gestion.abtel.fr/gestion/';
    const APIKEY = 'd5d485cbd6-5c4590da81d05';
    const APIUSER = 'api_parc';
    const APIPASS = '21wyisey';


    public function Save($syncGestion = false){
        if(!$this->Titre){
            $tempnote = strip_tags($this->Note);
            if(strlen($tempnote) < 80) {
                $this->Titre = $tempnote;
                $this->Note = '';
            } else{
                $this->Titre = substr($tempnote,0,70).' [...]';
            }
            /*if($this->UserCrea == "ZZ"){
                $this->Titre = 'Communication Client';
            }else{
                $this->Titre = 'Communication Abtel';
            }*/
        }
        if(!$this->Etat)
            $this->Etat = 2;

        $tick = $this->getOneParent('Ticket');
        $contrat = $this->getOneParent('Contrat');

        if(!$contrat) {
            $ct = $tick->getOneParent('Contrat');
            if ($ct)
                $this->addParent($ct);
        }

        /*if(!empty($this->pj)){
            $this->Note .= PHP_EOL.PHP_EOL.'<hr>';
            $this->Titre .= '( Ajout d\'une pièce jointe )';
            $ext = pathinfo($this->pj,PATHINFO_EXTENSION);
            $img = array('jpg','jpeg','png','gif','JPG','JPEG','PNG','GIF');
            if(in_array($ext,$img)){
                $this->Note .= '<div class="row uploadItem"><div class="col-md-5 uploadItemThumb"><img src="'.$this->pj.'.limit.250x40.'.$ext.'"></div><div class="col-md-7 uploadItemLink"><a href="'.$this->pj.'" target="_blank" title="Voir l\'image">Voir l\'image</a></div></div>';
            } else{
                $this->Note .= '<div class="row uploadItem"><div class="col-md-5 uploadItemThumb"><i class="icmn-file-empty2"></i></div><div class="col-md-7 uploadItemLink"><a href="'.$this->pj.'" target="_blank" title="Voir le fichier">Voir le fichier</a></div></div>';
            }
        }*/


        if(($syncGestion || !$this->kb_api) && array_key_exists('Abtel',Sys::$Modules)){ // Si ca ne viens pas de l'api on synchro gestion
            $props = $this->getElementsByAttribute('gestion',1);
            $params = array("data"=>array());
            foreach($props as $cat){
                foreach ($cat['elements'] as $p){
                    $params["data"][$p['name']] = $this->{$p['name']};
                }
            }

            $url = self::GESTIONURL.'action';
            $method = "POST";
            if(!empty($this->IdGestion)){
                $url .= '/'.$this->IdGestion;
                $method = "PATCH";
            }

            $res = $this->requestGestion($url,$params,$method);
            $gok = !!$res['success'];
            if(!$gok) return false;

            $propsRet = $res['data']['props'];

            if(empty($this->IdGestion)){
                $this->IdGestion = $propsRet['IdGestion'];
            }

        }


        $ok = parent::Save();

        if( $ok ){
            $tck = $this->getOneParent('Ticket');
            AlertUser::addAlert('Ticket mis à jour : '.$tck->Titre,"Ticket : ".$tck->Numero,'Parc','Ticket',$tck->Id,[],'PARC_TECHNICIEN','icmn-user3');
        }

        return $ok;
    }

    public function Delete($syncGestion = false){
        if(($syncGestion || !$this->kb_api) && array_key_exists('Abtel',Sys::$Modules)){ // Si ca ne viens pas de l'api on synchro gestion
            $url = self::GESTIONURL.'action/'.$this->IdGestion;
            $method = "DELETE";
            $res = $this->requestGestion($url,array(),$method);
            if(!$res['success']){
                $this->addError(array('Message'=>'Erreur lors de la suppression dans la base gestion '));
                return false;
            }
        }

        return parent::Delete();
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

        if(!empty($this->NumeroTicket)){
            $tick = Sys::getOneData('Parc','Ticket/Numero='.$this->NumeroTicket);
            if(!$tick) {
                $this->addError(array("Message"=>"Ticket introuvable dans la base du Parc"));
                return false;
            }

            $par = $this->getOneParent('Ticket');
            if($par && $par->Id != $tick->Id){
                $this->delParent($par);
            }
            $this->addParent($tick);
        } else{
            $paTi = $this->getOneParent('Ticket');
            if($paTi)
                $this->NumeroTicket = $paTi->Numero;
        }

        if(empty($this->DateCrea))
            $this->DateCrea = time();

        if(empty($this->HeureFin))
            $this->HeureFin = date('H:i:s',$this->DateCrea);

        if(empty($this->Duree))
            $this->Duree = 60;

        if(empty($this->HeureDebut))
            $this->HeureDebut = date('H:i:s',$this->DateCrea - $this->Duree);

        return parent::Verify();
    }

    private function requestGestion($url,$params,$method){

        $this->authGestion();

        curl_setopt($this->con_handle,CURLOPT_URL,$url);
        curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, $method);
        $data =json_encode(array('API_KEY'=>self::APIKEY,'AUTH_TOKEN'=>$this->api_token,"params"=>$params));
        curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $temp = curl_exec($this->con_handle);
        $ret = json_decode($temp,true);

        if(!$ret['success']) {
            if(!empty($ret['error_description'] )) {
                if(!is_array($ret['error_description']))
                    $ret['error_description'] = array($ret['error_description']);

                foreach ($ret['error_description'] as $err) {
                    $this->addError(array('Message' => $err));
                }
            } elseif (!empty(curl_error($this->con_handle))){
                $this->addError(array('Message' => curl_error($this->con_handle)));
            } else {
                $code = curl_getinfo($this->con_handle, CURLINFO_HTTP_CODE);
                if($code == 500) {
                    $this->addError(array('Message' => 'Erreur mystère, une erreur 500 coté Catalina'));
                } elseif ($code == 204) {
                    $ret['success'] = true;
                } else {
                    $this->addError(array('Message' => 'Erreur mystère'));
                }
            }
        }

        return $ret;
    }

    private function authGestion(){
        //Ouverture connection curl si besoin
        if(empty($this->con_handle)){
            $this->con_handle = curl_init(self::GESTIONURL);

            curl_setopt($this->con_handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, "POST");
            $data =json_encode(array('API_KEY'=>self::APIKEY,'login'=>self::APIUSER,'pass'=>self::APIPASS));
            curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
            curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );
            curl_setopt($this->con_handle, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($this->con_handle, CURLOPT_SSL_VERIFYPEER, 0);

            $ret = json_decode(curl_exec($this->con_handle),true);
            $this->api_token = $ret['auth_token'];
        }

        return true;
    }


}