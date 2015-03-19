<?php

class Host extends genericClass {
	var $_isVerified = false;
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
		if(!$this->_isVerified) $this->Verify( $synchro );
		// Enregistrement si pas d'erreur + Récupération GID CLIENT
		if($this->_isVerified) {
			$this->getGidFromClient( $synchro );
			parent::Save();
		}
		// Maj Quotas niveau serveur
		if($this->Id) {
			$T1 = Sys::$Modules["Parc"] -> callData("Parc/Server/Host/{$this->Id}", "", 0, 1);
			if(!empty($T1)) {
				$Server = genericClass::createInstance('Parc', $T1[0]);
				$Server->EspaceProvisionne = 0;
				$Tab = Sys::$Modules["Parc"] -> callData("Parc/Server/{$Server->Id}/Host", "", 0, 1000);
				if (!empty($Tab)) foreach($Tab as $H) $Server->EspaceProvisionne += $H["Quota"];
				$Server->Save();
			}
		}
	}

	/**
	 * Verification des erreurs possibles
	 * @param	boolean	Verifie aussi sur LDAP
	 * @return	Verification OK ou NON
	 */
	public function Verify( $synchro = true ) {

		if(parent::Verify()) {
			//Verification du client
			if (!$this->getKEClient()) return true;
			//Verification du server
			if (!$this->getKEServer()) return true;

			$this->_isVerified = true;

			if($synchro) {

				// Outils
				$KEServer = $this->getKEServer();
				$dn = 'cn='.$this->Nom.',ou='.$KEServer->LDAPNom.',ou=servers,'.PARC_LDAP_BASE;
				// Verification à jour
				$res = Server::checkTms($this);
				if($res['exists']) {
					if(!$res['OK']) {
						$this->AddError($res);
						$this->_isVerified = false;
					}
					else {
						// Déplacement
						if($this->LdapDN != 'cn='.$this->Nom.',ou='.$KEServer->LDAPNom.',ou=servers,'.PARC_LDAP_BASE) $res = Server::ldapRename($this->LdapDN, 'cn='.$this->Nom, 'ou='.$KEServer->LDAPNom.',ou=servers,'.PARC_LDAP_BASE);
						else $res = array('OK' => true);
						if($res['OK']) {
							// Modification
							$entry = $this->buildEntry(false);
							$res = Server::ldapModify($this->LdapID, $entry);
							if($res['OK']) {
								// Tout s'est passé correctement
								$this->LdapDN = $dn;
								$this->LdapTms = $res['LdapTms'];
							}
							else {
								// Erreur
								$this->AddError($res);
								$this->_isVerified = false;
								// Rollback du déplacement
								$tab = explode(',', $this->LdapDN);
								$leaf = array_shift($tab);
								$rest = implode(',', $tab);
								Server::ldapRename($dn, $leaf, $rest);
							}
						}
						else {
							$this->AddError($res);
							$this->_isVerified = false;
						}
					}
	
				}
				else {
					////////// Nouvel élément
					if($KEServer) {
						$entry = $this->buildEntry();
						$res = Server::ldapAdd($dn, $entry);
						if($res['OK']) {
							$res2 = Server::ldapAdd('ou=users,'.$dn, array('objectclass' => array('organizationalUnit', 'top'), 'ou' => 'users'));
							$this->LdapDN = $dn;
							$this->LdapUid = $entry['uidnumber'];
							$this->LdapGid = $entry['gidnumber'];
							$this->LdapID = $res['LdapID'];
							$this->LdapTms = $res2['LdapTms'];
						}
						else {
							$this->AddError($res);
							$this->_isVerified = false;
						}
					}
					else {
						$this->AddError(array('Message' => "Un hébergement doit obligatoirement être créé dans un serveur donné.", 'Prop' => ''));
						$this->_isVerified = false;
					}
				}

			}

		}
		else {

			$this->_isVerified = false;

		}

		return $this->_isVerified;

	}

	/**
	 * Configuration d'une nouvelle entrée type
	 * Utilisé lors du test dans Verify
	 * puis lors du vrai ajout dans Save
	 * @param	boolean		Si FALSE c'est simplement une mise à jour
	 * @return	Array
	 */
	private function buildEntry( $new = true ) {
		$entry = array();
		$entry['cn'] = $this->Nom;
		$entry['givenname'] = $this->Nom;
		$entry['homedirectory'] = '/home/' . $this->Nom;
		$entry['sn'] = $this->Nom;
		$entry['uid'] = $this->Nom;
		$entry['description'] = json_encode(array("Quota" => $this->Quota));
		$entry['preferredLanguage'] = $this->PHPVersion;
		if($new) {
			$entry['uidnumber'] = $this->_KEServer->getNextUid();
			$entry['gidnumber'] = $this->_KEClient->LdapGid;
			$entry['loginshell'] = '/bin/bash';
			$entry['objectclass'][0] = 'inetOrgPerson';
			$entry['objectclass'][1] = 'posixAccount';
			$entry['objectclass'][2] = 'top';
			$entry['userpassword'] = '{MD5}Xr4ilOzQ4PCOq3aQ0qbuaQ==';
		}
		return $entry;
	}

	/**
	 * Récupère le Gid du Client Parent s'il existe
	 * @param	boolean		Synchroniser aussi sur LDAP
	 * @return	void
	 */
	private function getGidFromClient( $synchro = true ) {
		$tab = $this->getParents('Client');
		if(!empty($tab)) {
			$this->LdapGid = $tab[0]->LdapGid;
			if($synchro) {
				$entry = array('gidnumber' => $this->LdapGid);
				$KEServer = $this->getKEServer();
				Server::ldapModify($this->LdapID, $entry);
			}
		}
	}


	/**
	 * Suppression de la BDD
	 * Relai de cette suppression à LDAP
	 * On utilise aussi la fonction de la superclasse
	 * @return	void
	 */
	public function Delete() {
		if (!empty($this->LdapGid)) Server::ldapDelete($this->LdapID,true);
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

}
