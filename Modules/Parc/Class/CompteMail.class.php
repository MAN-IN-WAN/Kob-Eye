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
		parent::Save();
		// Forcer la vérification
		//if(!$this->_isVerified) $this->Verify( $synchro );
		// Enregistrement si pas d'erreur
		//if($this->_isVerified) {
		//	parent::Save();
		//}

		if((!isset($this->IdMail) || $this->IdMail=='') && $this->getKEServer()){
		    $this->createMail();
        }
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
            if($e->getMessage() == ''){
                //TODO : Creation du domaine
            }
        }


        try{

            $cosesTemp = $zimbra->getAllCos();
            $coses = array();
            foreach ($cosesTemp as $cosTemp){
                $coses[$cosTemp->get('name')]=$cosTemp;
            }
            $cos = $coses[$this->COS];

            $values = array();

            $values['name'] = $this->Adresse;
            $values['password'] = $this->Pass;
            $values['zimbraCOSId'] = $cos->get('id');
            $values['sn'] = $this->Nom;
            $values['givenName'] = $this->Prenom;
            $values['displayName'] = ucfirst($this->Prenom) .' '. ucfirst($this->Nom);
            $values['zimbraMailHost'] = $srv->DNSNom;

            $res = $zimbra->createAccount($values);

            $this->IdMail = $res->get('id');

            $this->save();

        } catch (Exception $e){
           print_r ($e);
        }


	    return false;
    }
}
