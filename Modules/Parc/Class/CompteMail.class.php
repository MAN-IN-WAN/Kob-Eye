<?php

class CompteMail extends genericClass {
	//var $_isVerified = false;
	var $_KEServer = false;
	var $_KEClient = false;

	/**
	 * Force la vérification avant enregistrement
	 * @param	boolean	Enregistrer aussi sur LDAP
	 * @return	void
	 */
	public function Save( $synchro = true ) {
		// Forcer la vérification
		//if(!$this->_isVerified) $this->Verify( $synchro );
		// Enregistrement si pas d'erreur
		//if($this->_isVerified) {
		//	parent::Save();
		//}

        if(!$synchro){
            parent::Save();
            return true;
        }

		if((!isset($this->IdMail) || $this->IdMail=='') && $this->getKEServer()){
		    if(!$this->createMail()){
		        $this->Delete();
                return false;
            }
        } elseif ($this->getKEServer()){
            if(!$this->updateMail()) {
                return false;
            }

        } else{
            $this->Delete();
            return false;
        }

        parent::Save();
        return true;
	}

//	/**
//	 * Verification des erreurs possibles
//	 * @param	boolean	Verifie aussi sur LDAP
//	 * @return	Verification OK ou NON
//	 */
//	public function Verify( $synchro = true ) {
//    return true;
//		if(parent::Verify()) {
//			//Verification du client
//			if (!$this->getKEClient()) {
//                $this->AddError(array('Message'=>'Un compte mail doit être lié a un Client.'));
//			    return false;
//            }
//			//Verification du server
//			if (!$this->getKEServer()) {
//                $this->AddError(array('Message'=>'Un compte mail doit être lié a un Serveur.'));
//			    return false;
//            }
//
//			$this->_isVerified = true;
//
//			if($synchro) {
//
//				// Outils
//				$KEServer = $this->getKEServer();
//				$dn = 'cn='.$this->Adresse.',ou='.$KEServer->LDAPNom.',ou=servers,'.PARC_LDAP_BASE;
//				// Verification à jour
//				$res = Server::checkTms($this);
//				if($res['exists']) {
//					if(!$res['OK']) {
//						$this->AddError($res);
//						$this->_isVerified = false;
//					}
//					else {
//						// Déplacement
//						if($this->LdapDN != 'cn='.$this->Adresse.',ou='.$KEServer->LDAPNom.',ou=servers,'.PARC_LDAP_BASE) $res = Server::ldapRename($this->LdapDN, 'cn='.$this->Adresse, 'ou='.$KEServer->LDAPNom.',ou=servers,'.PARC_LDAP_BASE);
//						else $res = array('OK' => true);
//						if($res['OK']) {
//							// Modification
//							$entry = $this->buildEntry(false);
//							$res = Server::ldapModify($this->LdapID, $entry);
//							if($res['OK']) {
//								// Tout s'est passé correctement
//								$this->LdapDN = $dn;
//								$this->LdapTms = $res['LdapTms'];
//							}
//							else {
//								// Erreur
//								$this->AddError($res);
//								$this->_isVerified = false;
//								// Rollback du déplacement
//								$tab = explode(',', $this->LdapDN);
//								$leaf = array_shift($tab);
//								$rest = implode(',', $tab);
//								Server::ldapRename($dn, $leaf, $rest);
//							}
//						}
//						else {
//							$this->AddError($res);
//							$this->_isVerified = false;
//						}
//					}
//
//				}
//				else {
//					////////// Nouvel élément
//					if($KEServer) {
//						$entry = $this->buildEntry();
//
//						$res = Server::ldapAdd($dn, $entry);
//                        klog::l('$entry',$entry);
//						if($res['OK']) {
//							$this->LdapDN = $dn;
//							$this->LdapID = $res['LdapID'];
//							$this->LdapTms = $res['LdapTms'];
//						}
//						else {
//							$this->AddError($res);
//							$this->_isVerified = false;
//						}
//					}
//					else {
//						$this->AddError(array('Message' => "Un compte email doit obligatoirement être créé dans un serveur de mail donné.", 'Prop' => ''));
//						$this->_isVerified = false;
//					}
//				}
//
//			}
//
//		}
//		else {
//
//			$this->_isVerified = false;
//
//		}
//
//		return $this->_isVerified;
//
//	}

	/**
	 * Configuration d'une nouvelle entrée type
	 * Utilisé lors du test dans Verify
	 * puis lors du vrai ajout dans Save
	 * @param	boolean		Si FALSE c'est simplement une mise à jour
	 * @return	Array
	 */
	private function buildEntry( $new = true ) {
		$entry = array();
                //$entry['dn'] = 'cn='.$this->Adresse.',ou='.$this->_KEServer->LDAPNom.',ou=servers,dc=abtel,dc=fr';
                $entry['cn'] = $this->Adresse;
                $entry['description'] = json_encode(array("Quota" => $this->Quota));
                $entry['givenname'] = $this->Prenom;
                $entry['mail'] = $this->Adresse;
                $entry['mailaccess'] = $this->Status;
                $entry['sn'] =  $this->Nom;
                $entry['uid'] =  $this->Adresse;
                $entry['userpassword'] = $this->Pass;
                $entry['gidnumber'] = $this->_KEClient->LdapGid;
                $entry['uidnumber'] = $this->_KEServer->getNextUid();
                $entry['homedirectory'] = '/home/Parc2';
                if($new){
                        $entry["objectclass"] = Array("top","inetOrgPerson","twAccount","posixAccount");
                }
                
		return $entry;
	}



	/**
	 * Suppression de la BDD
	 * Relai de cette suppression à LDAP
	 * On utilise aussi la fonction de la superclasse
	 * @return	void
	 */
	public function Delete() {
		if (!empty($this->LdapID)) Server::ldapDelete($this->LdapID,true);
		parent::Delete();
	}


	/**
	 * Récupère une référence vers l'objet KE "Server"
	 * pour effectuer des requetes LDAP
	 * On conserve une référence vers le serveur
	 * pour le cas d'une utilisation ultérieure
	 * @return	L'objet Kob-Eye
	 */
	public function getKEServer() {
		if(!is_object($this->_KEServer)) {
			$tab = $this->getParents('Server');
			if(empty($tab)) return false;
			else $this->_KEServer = $tab[0];
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
			$tab = $this->getParents('Client');
			if(empty($tab)) return false;
			else $this->_KEClient = $tab[0];
		}
		return $this->_KEClient;
	}

	/**
	 * Retrouve les parents lors d'une synchronisation
	 * @return	void
	 */
	public function findParents() {
		$Parts = explode(',', $this->LdapDN);
		foreach($Parts as $i => $P) $Parts[$i] = explode('=', $P);
		// Parent Client
		$Tab = Sys::$Modules["Parc"]->callData("Parc/Client/NomLDAP=".$Parts[0][1], "", 0, 1);
		if(!empty($Tab)) {
			$obj = genericClass::createInstance('Parc', $Tab[0]);
			$this->AddParent($obj);
		}
		// Parent Server
		$Tab = Sys::$Modules["Parc"]->callData("Parc/Server/LDAPNom=".$Parts[1][1], "", 0, 1);
		if(!empty($Tab)) {
			$obj = genericClass::createInstance('Parc', $Tab[0]);
			$this->AddParent($obj);
		}
	}

    /**
     * Met à jour une adresse mail sur le serveur
     * @return	false
     */
    public function updateMail(){
        $srv = $this->getOneParent('Server');

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

        //Check que le compte existe déjà
        try{
            $temp = $zimbra->getAccount('abtel.fr', 'id', $this->IdMail);
            $actuName = $temp->get('name');
        } catch (Exception $e) {
            $this->AddError(array('Message' => 'Erreur, ce compte mail n\'existe plus', 'Object' => $e));
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

            if($this->COS && $this->COS != 'NULL'){

                $cosesTemp = $zimbra->getAllCos();
                $coses = array();
                foreach ($cosesTemp as $cosTemp){
                    $coses[$cosTemp->get('name')]=$cosTemp;
                }

                $cos = $coses[$this->COS];
                $values['zimbraCOSId'] = $cos->get('id');
            }

            if($actuName != $this->Adresse)
                $resName = $zimbra->renameAccount($this->IdMail, $this->Adresse);

            if( isset($this->Pass) &&  $this->Pass != ''){
                //    $values['password'] = $this->Pass;
                $resPass = $zimbra->setPassword($this->IdMail, $this->Pass);
            }
            if( isset($this->Status) &&  $this->Status != ''){
                $values['zimbraAccountStatus'] = $this->Status;
            }

            //print_r('UPDATE --------'.$this->Adresse.PHP_EOL);

            $values['sn'] = $this->Nom;
            $values['givenName'] = $this->Prenom;
            $values['displayName'] = ucfirst($this->Prenom) .' '. ucfirst($this->Nom);
            $values['zimbraMailHost'] = $srv->DNSNom;
            $values['id'] = $this->IdMail;
            $values['zimbraMailQuota'] = $this->Quota*1024*1024;

            $res = $zimbra->modifyAccount($values);


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
	public function createMail(){

	    $srv = $this->getOneParent('Server');

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

            $this->AddError(array('Message' => 'Erreur lors de la liaiison avec le serveur de mail, cette adresse mail correspond à une liste de diffusion', 'Object' => $temp));
            return false;
        } catch (Exception $e) {
            //print_r($e);
        }


        try{
            $values = array();

            if($this->COS && $this->COS != 'NULL'){
                $cosesTemp = $zimbra->getAllCos();
                $coses = array();
                foreach ($cosesTemp as $cosTemp){
                    $coses[$cosTemp->get('name')]=$cosTemp;
                }
                $cos = $coses[$this->COS];

                $cos = $coses[$this->COS];
                $values['zimbraCOSId'] = $cos->get('id');
            }


            //print_r('INSERT --------'.$this->Adresse.PHP_EOL);

            $values['name'] = $this->Adresse;
            $values['password'] = $this->Pass;
            $values['sn'] = $this->Nom;
            $values['givenName'] = $this->Prenom;
            $values['displayName'] = ucfirst($this->Prenom) .' '. ucfirst($this->Nom);
            $values['zimbraMailHost'] = $srv->DNSNom;
            $values['zimbraMailQuota'] = $this->Quota*1024*1024;

            $res = $zimbra->createAccount($values);

            $this->IdMail = $res->get('id');

        } catch (Exception $e){
            $this->AddError(array('Message'=>'Erreur lors l\'enregistrement : '.$e->getErrorCode(),'Object'=>''));
           return false;
        }


	    return true;
    }

    public function deletegateAccess() {
        $srv = $this->getOneParent('Server');

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
        $url = 'https://'.$srv->DNSNom.'/mail?auth=qp&zauthtoken='.$datoken;
        return $url;
    }
}
