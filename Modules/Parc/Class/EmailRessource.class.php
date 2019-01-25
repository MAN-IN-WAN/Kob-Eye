<?php

class EmailRessource extends genericClass {
	//var $_isVerified = false;
	var $_KEServer = false;
	var $_KEClient = false;
    private $delaiSuppression = 28*84600;  //(4 semaines)

	/**
	 * Force la vérification avant enregistrement
	 * @param	boolean	Enregistrer aussi sur LDAP
	 * @return	boolean
	 */
	public function Save( $synchro = true ) {
		// Forcer la vérification
		//if(!$this->_isVerified) $this->Verify( $synchro );
		// Enregistrement si pas d'erreur
		//if($this->_isVerified) {
		//	parent::Save();
		//}
        //vérificatio du client


        if($this->Suppression > 0  && $this->Suppression < time()){
            $this->finalDelete();
            return true;
        }


        $client = $this->getKEClient();
        if (!$client){
            $this->addError(array('Message'=>'Compte client introuvable.'));
            return false;
        }

        if(!$synchro){
            parent::Save();
            return true;
        }



		if((!isset($this->IdMail) || $this->IdMail=='') && $this->getKEServer()){
		    if(!$this->createRessource()){
		        //$this->Delete();
                $this->addError(array('Message'=>'Impossible de créer la ressource...'));
                return false;
            }
        } elseif ($this->getKEServer()){
            if(!$this->updateRessource()) {
                return false;
            }

        } else{
            $this->addError(array('Message'=>'Impossible de trouver le serveur.'));
            //$this->Delete();
            return false;
        }

        parent::Save();

        return true;
	}





	/**
    /**
     * Suppression de la BDD apres un temps donné. Jusque la boite sera simplement fermée
     * @return	void
     */
    public function Delete() {
        $this->Suppression = time() + $this->delaiSuppression;
        $this->Status = 'closed';
        $this->Save();
    }

    public function unDelete() {
        $this->Suppression = NULL;
        $this->Status = 'active';
        $this->Save();
    }


	/**
	 * Récupère une référence vers l'objet KE "Server"
	 * pour effectuer des requetes LDAP
	 * On conserve une référence vers le serveur
	 * pour le cas d'une utilisation ultérieure
	 * @return	L'objet Kob-Eye
	 */
	public function getKEServer() {
        if(empty($this->Id)){
            $pars = array();
            foreach ($this->Parents as $p){
                if($p['Titre'] == 'Server'){
                    $pa = Sys::getOneData('Parc','Server/'.$p['Id'],0,1,null,null,null,null,true);
                    $pars[] = $pa;
                }
            }
            $this->_KEServer = $pars;
        }
		if(!is_object($this->_KEServer)) {
			$this->_KEServer = Sys::getOneData('Parc','Server/EmailRessource/'.$this->Id,0,1,'','','','',true);
		}
		if (!is_object($this->_KEServer)){
		    //retroune le serveur de mail par defaut
            $this->_KEServer = Sys::getOneData('Parc','Server/defaultMailServer=1',0,1,'','','','',true);
        }
        if (!is_object($this->_KEServer)){
		    return false;
        }
		return $this->_KEServer;
	}

	/**
	 * Récupère une référence vers l'objet KE "Client"
	 * pour effectuer des requetes LDAP
	 * On conserve une référence vers le client
	 * pour le cas d'une utilisation ultérieure
	 * @return	L'objet Kob-Eye
	 */
	public function getKEClient() {
		if(!is_object($this->_KEClient)) {
			$this->_KEClient = $this->getOneParent('Client');
		}
        if(!is_object($this->_KEClient)&&Sys::$User->hasRole('PARC_CLIENT')) {
		    //donc i ls'agit d'un client connecté, on récupère l'objet client
            $this->_KEClient = Sys::$User->getOneChild('Client');
        }
        if(!is_object($this->_KEClient)) {
		    return false;
        }
		return $this->_KEClient;
	}


    /**
     * Met à jour une adresse mail sur le serveur
     * @return	false
     */
    public function updateRessource(){
        $srv = $this->getKEServer();

        if(!is_object($srv) || $srv->ObjectType != 'Server'){
            $this->AddError(array('Message'=>'Un compte mail doit être lié a un serveur.'));
            return false;
        }

        $zimbra = new \Zimbra\ZCS\Admin($srv->IP, $srv->mailAdminPort);
        $zimbra->auth($srv->mailAdminUser, $srv->mailAdminPassword);


        $dom = explode('@',$this->Adresse);
        $dom = $dom[1];


        try{
            $domaine = $zimbra->getDomain($dom);
        } catch (Exception $e){
            //if($e->getMessage() == 'no such domain'){
            $this->AddError(array('Message' => 'Erreur, le domaine renseigné n\'existe pas', 'Object' => $e));
            return false;
            //TODO : Creation du domaine ?
            //}
        }

        //Check que la ressource existe déjà
        try{

            $temp = $zimbra->getRessource($this->IdMail,'id' );
            $actuName = $temp->get('name');
        } catch (Exception $e) {
            $this->AddError(array('Message' => 'Erreur, cette ressource n\'existe plus', 'Object' => $e));
            return false;
        }

        //Check que l'adresse ne soit pas déjà prise par un autre compte/Alias/Liste de diffusion
        try{
            $temp = $zimbra->getAccount($dom, 'name', $this->Adresse);
            if($temp->id != $this->IdMail){
                $this->AddError(array('Message' => 'Erreur, cette adresse mail est liée à un autre compte', 'Object' => $temp));
                return false;
            }
        } catch (Exception $e) {
            //print_r($e);
        }
        try{
            $temp = $zimbra->getDistributionList( $this->Adresse,'name');

            $this->AddError(array('Message' => 'Erreur, cette adresse mail correspond à une liste de diffusion', 'Object' => $temp));
            return false;
        } catch (Exception $e) {
            //print_r($e);
        }


        try{
            $values = array();

//            if($this->COS && $this->COS != 'NULL'){
//
//                $cosesTemp = $zimbra->getAllCos();
//                $coses = array();
//                foreach ($cosesTemp as $cosTemp){
//                    $coses[$cosTemp->get('name')]=$cosTemp;
//                }
//
//                $cos = $coses[$this->COS];
//                $values['zimbraCOSId'] = $cos->get('id');
//            }

            if($actuName != $this->Adresse)
                $resName = $zimbra->renameRessource($this->IdMail, $this->Adresse);

            if( isset($this->Pass) &&  $this->Pass != ''){
                //    $values['password'] = $this->Pass;
                $resPass = $zimbra->setPassword($this->IdMail, $this->Pass);
            }
            if( isset($this->Status) &&  $this->Status != ''){
                $values['zimbraAccountStatus'] = $this->Status;
            }

            //print_r('UPDATE --------'.$this->Adresse.PHP_EOL);

            $values['displayName'] = ucfirst($this->Nom);
            $values['zimbraMailHost'] = $srv->DNSNom;
            $values['id'] = $this->IdMail;
            $values['zimbraCalResType'] = $this->Type;
            $values['zimbraCalResAutoAcceptDecline'] = 'TRUE';
            $values['zimbraCalResAutoDeclineIfBusy'] = 'TRUE';
            $res = $zimbra->modifyRessource($values);


        } catch (Exception $e){
            $this->AddError(array('Message'=>'Erreur lors l\'enregistrement : '.$e->getErrorCode(),'Object'=>''));
            //print_r($e);
            return false;
        }

        return true;

    }

    /**
     * Crée une adresse sur le serveur mail a pres l'avoir enregistré en base
     * @return	false
     */
	public function createRessource(){

	    $srv = $this->getKEServer();
	    if(!is_object($srv) || $srv->ObjectType != 'Server'){
            $this->AddError(array('Message'=>'Un compte mail doit être lié a un serveur.'));
	        return false;
        }


        // Create a new Admin class and authenticate
        $zimbra = new \Zimbra\ZCS\Admin($srv->IP, $srv->mailAdminPort);
        $zimbra->auth($srv->mailAdminUser, $srv->mailAdminPassword);


        $dom = explode('@',$this->Adresse);
        $dom = $dom[1];


        try{
            $domaine = $zimbra->getDomain($dom);
        } catch (Exception $e){
            //if($e->getMessage() == 'no such domain'){
                $this->AddError(array('Message' => 'Erreur lors de la liaison avec le serveur de mail, le domaine renseigné n\'existe pas', 'Object' => $e));
                return false;
                //TODO : Creation du domaine ?
            //}
        }

        //Check que l'adresse ne soit pas déjà prise par un autre compte/Alias/Liste de diffusion
        try{
            $temp = $zimbra->getAccount($dom, 'name', $this->Adresse);
            $this->AddError(array('Message' => 'Erreur lors de la liaison avec le serveur de mail, l\'adresse mail existe déjà', 'Object' => $temp));
            return false;
        } catch (Exception $e) {
            //On souhaite l'excpetion no_such_account pour confirmer que l'adresse n'exsite pas.
        }
        try{
            $temp = $zimbra->getDistributionList( $this->Adresse,'name');

            $this->AddError(array('Message' => 'Erreur lors de la liaison avec le serveur de mail, cette adresse mail correspond à une liste de diffusion', 'Object' => $temp));
            return false;
        } catch (Exception $e) {
            //print_r($e);
        }


        try{
            $values = array();

//            if($this->COS && $this->COS != 'NULL'){
//                $cosesTemp = $zimbra->getAllCos();
//                $coses = array();
//                foreach ($cosesTemp as $cosTemp){
//                    $coses[$cosTemp->get('name')]=$cosTemp;
//                }
//                $cos = $coses[$this->COS];
//
//                $cos = $coses[$this->COS];
//                $values['zimbraCOSId'] = $cos->get('id');
//            }


            //print_r('INSERT --------'.$this->Adresse.PHP_EOL);

            $values['name'] = $this->Adresse;
            $values['password'] = $this->Pass;
            $values['displayName'] = ucfirst($this->Nom);
            $values['zimbraMailHost'] = $srv->DNSNom;
            $values['zimbraCalResType'] = $this->Type;
            $values['zimbraCalResAutoAcceptDecline'] = 'TRUE';
            $values['zimbraCalResAutoDeclineIfBusy'] = 'TRUE';

            $res = $zimbra->createRessource($values);

            $this->IdMail = $res->get('id');

        } catch (Exception $e){
            $this->AddError(array('Message'=>'Erreur lors l\'enregistrement : '.$e->getErrorCode(),'Object'=>''));
           return false;
        }

	    return true;
    }

    public function deletegateAccess() {
        $srv = $this->getKEServer();

        if(!is_object($srv) || $srv->ObjectType != 'Server'){
            $this->AddError(array('Message'=>'Un compte mail doit être lié a un serveur.'));
            return false;
        }
        $zimbra = new \Zimbra\ZCS\Admin($srv->IP, $srv->mailAdminPort);
        $zimbra->auth($srv->mailAdminUser, $srv->mailAdminPassword);
        $datoken = $zimbra->delegateAuth($this->Adresse);
        //$token = "f6bb13efeafaa432f1f05991f7d55a56f60940f825f2b258b40f813065a37f4d";
        //$timestamp = (time()*1000);
        //$expires = 0;
        //$preauth = hash_hmac('sha1',$this->Adresse.'|name|0|'.$timestamp,$token);
        //$url = 'https://'.$srv->DNSNom.'/service/preauth?account='.$this->Adresse.'&by=name&timestamp='.$timestamp.'&expires='.$expires.'&preauth='.$preauth;
        //$url = 'https://'.$srv->DNSNom.'/mail?auth=qp&zauthtoken='.$datoken;
        $url = 'https://mx1.abtel.link/mail?auth=qp&zauthtoken='.$datoken;
        return $url;
    }


    public function finalDelete(){
        $srv = $this->getKEServer();

        if(!is_object($srv) || $srv->ObjectType != 'Server'){
            $this->AddError(array('Message'=>'Un compte mail doit être lié a un serveur.'));
            return false;
        }

        $zimbra = new \Zimbra\ZCS\Admin($srv->IP, $srv->mailAdminPort);
        $zimbra->auth($srv->mailAdminUser, $srv->mailAdminPassword);

        try{
            $zimbra->deleteRessource($this->IdMail);
        } catch (Exception $e){
            $this->AddError(array('Message'=>'Erreur lors l\'effacement : '.$e->getErrorCode(),'Object'=>''));
            return false;
        }

        parent::Delete();
    }


    public function forceDelete(){
	    parent::Delete();
    }
}
