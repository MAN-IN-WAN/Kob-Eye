<?php

class Ticket extends genericClass
{
    protected $con_handle = null;
    protected $api_token = null;
    const GESTIONURL = 'http://api.gestion.abtel.fr';
    const APIKEY = 'd5d485cbd6-5c4590da81d05';
    const APIUSER = 'api_parc';
    const APIPASS = '21wyisey';


    public function Save($syncGestion = false){
        if($syncGestion){
            //TODO
        }


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

        $ret = json_decode(curl_exec($this->con_handle),true);
        if(!$ret['success']) {
            foreach ($ret['error_description'] as $err){
                $this->addError(array('Message'=>$err));
            }
        }

        $res = $ret['pagination']['total'];

        return !!$ret['success'];
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


    public function createTicket($args){
        $info = array();

        if($args['step'] != 1) {
            return array(
                'template' => 'Create',
                'step' => 1,
                'callNext' => array(
                    'nom'=> 'createTicket',
                    'title'=> 'Creation d\'un Ticket',
                    'needConfirm' => false,
                    'item' => null
                )
            );
        } else{
            $client = null;
            $contrat = null;
            $contact = null;
            $cli = null;

            if(!empty($args['client'])){
                $cli = Sys::getOneData('Abtel','Contrat/'.$args['contrat']);
            }else {
                $cli = Process::GetTempVar('ParcClient');
            }
            if(!$cli)
                return array('errors'=>array(array('Message'=>'Impossible de créer un ticket sans client')));
            if(!empty($args['contrat']))
                $contrat = Sys::getOneData('Abtel','Contrat/'.$args['contrat']);
            if(!empty($args['contact'])) {
                $contact = Sys::getOneData('Parc', 'Contact/' . $args['contact']);
            }

            $tick = genericClass::createInstance('Parc','Ticket');
            $tick->addParent($cli);
            $tick->CodeClient = $cli->CodeGestion;
            if($contrat) {
                $tick->addParent($contrat);
                $tick->CodeContrat = $contrat->Code;
            }
            if($contact) {
                $tick->addParent($contact);
                $tick->IdContactGestion = $contact->IdGestion;
            }


            $tick->Source = 'Web';
            $tick->DateEcheance = Utils::getTodayEvening(null);
            $tick->Etat = 10;
            $tick->DateCrea = time();
            $tick->UserCrea = 'ZZ';
            $tick->Priorite = $args['urgence'];
            $tick->Note = $args['description'];

            switch($args['service']){
                case 'Commercial':
                    $tick->CodeEntite = 'AI';
                    $tick->Categorie = 'COM';
                    $tick->Titre = 'Demande client Commerciale';
                    $tick->UserNext = '00';
                    break;
                case 'Administratif':
                    $tick->CodeEntite = 'AI';
                    $tick->Categorie = 'ADM';
                    $tick->Titre = 'Demande client Administrative';
                    $tick->UserNext = '00';
                    break;
                case 'Téléphonie':
                    $tick->CodeEntite = 'AI';
                    $tick->Categorie = 'ADM';
                    $tick->Titre = 'Demande client Téléphonie';
                    $tick->UserNext = '00';
                    break;
                case 'Poste':
                    $tick->CodeEntite = 'AI';
                    $tick->Categorie = 'ADM';
                    $tick->Titre = 'Demande client Poste';
                    $tick->UserNext = '00';
                    break;
                case 'Serveur':
                    $tick->CodeEntite = 'AI';
                    $tick->Categorie = 'ADM';
                    $tick->Titre = 'Demande client Serveur';
                    $tick->UserNext = '00';
                    break;
                case 'Web':
                    $tick->CodeEntite = 'AW';
                    $tick->Categorie = 'OPE';
                    $tick->Titre = 'Demande client Web';
                    $tick->UserNext = '01';
                    break;
                case 'Autre':
                default:
                    $tick->CodeEntite = 'AI';
                    $tick->Categorie = 'OPE';
                    $tick->Titre = 'Demande client Autre';
                    $tick->UserNext = '00';
            }

            if(!empty($args['pj'])){
                $acts = array();
                foreach($args['pj'] as $pj){
                    $act = genericClass::createInstance('Parc','Action');
                    $act->Titre = 'Ajout d\'une pièce jointe';
                    $ext = pathinfo($pj,PATHINFO_EXTENSION);
                    $img = array('jpg','jpeg','png','gif','JPG','JPEG','PNG','GIF');
                    if(in_array($ext,$img)){
                        $act->Note = '<div class="row uploadItem"><div class="col-md-5 uploadItemThumb"><img src="'.$pj.'.limit.250x40.'.$ext.'"></div><div class="col-md-7 uploadItemLink"><a href="'.$pj.'" target="_blank" title="Voir l\'image">Voir l\'image</a></div></div>';
                    } else{
                        $act->Note = '<div class="row uploadItem"><div class="col-md-5 uploadItemThumb"><i class="icmn-file-empty2"></i></div><div class="col-md-7 uploadItemLink"><a href="'.$pj.'" target="_blank" title="Voir le fichier">Voir le fichier</a></div></div>';
                    }
                    $act->UserCrea = 'ZZ';

                    $acts[] = $act;

                }
            }

            if($tick->Verify() && $tick->Save()){
                foreach ($acts as $ac){
                    $ac->addParent($tick);
                    if($ac->Verify()){
                        $ac->Save();
                    } else {
                        $tick->Error = array_merge($tick->Error,$ac->Error);
                    }
                }

                return array(
                    'data' => 'Ticket créé avec succès !',
                    'errors' => $tick->Error,
                    'infos' => $info
                );
            } else{
                return array(
                    'data' => 'Oups, une erreur s\'est produite !',
                    'errors' => $tick->Error,
                    'infos' => $info
                );
            }



        }
    }

    public function closeTicket(){
        $info = array();

        $this->Etat = 40;
        $this->DateCloture = time();
        $this->UserCloture = 'ZZ';


        if($this->Verify() && $this->Save()){
            return array(
                'data' => 'Ticket créé avec succès !',
                'errors' => $this->Error,
                'infos' => $info
            );
        } else{
            return array(
                'data' => 'Oups, une erreur s\'est produite !',
                'errors' => $this->Error,
                'infos' => $info
            );
        }
    }

}