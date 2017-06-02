<?php

class Parc_Client extends genericClass {
	var $Role = 'PARC_CLIENT';
	var $_isVerified = false;
	/**
	 * Force la vérification avant enregistrement
	 * @param	boolean	Enregistrer aussi sur LDAP
	 * @return	void
	 */
	public function Save( $synchro = true ) {
		parent::Save();
		// Forcer la vérification
		if(!$this->_isVerified) $this->Verify( $synchro );
		//Si le revendeur connecté modifie ou ajoute un client
		//on l'ajoute
		if (Sys::$User->isRole('PARC_REVENDEUR')){
			$rev = Process::getRegVars('ParcClient');
			$this->AddParent($rev);
		}
		// Enregistrement si pas d'erreur
		if($this->_isVerified){
			parent::Save();
			$this->setUser();
		}
	}

	/**
	 * Verification des erreurs possibles
	 * @param	boolean	Verifie aussi sur LDAP
	 * @return	Verification OK ou NON
	 */
	public function Verify( $synchro = true ) {

		if(empty($this->Nom)) $this->Nom = $this->NomLDAP;

		if(parent::Verify()) {

			$this->_isVerified = true;

			if($synchro) {

				// Outils
				$dn = 'cn='.$this->NomLDAP.',ou=clients,'.PARC_LDAP_BASE;
		
				// Verification à jour
				$res = Server::checkTms($this);
				if($res['exists']) {
					if(!$res['OK']) {
						$this->AddError($res);
						$this->_isVerified = false;
					}
					else {
						// Déplacement
						$res = Server::ldapRename($this->LdapDN, 'cn='.$this->NomLDAP, 'ou=clients,'.PARC_LDAP_BASE);
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
					$entry = $this->buildEntry();
					$res = Server::ldapAdd($dn, $entry);
					if($res['OK']) {
						$this->LdapDN = $dn;
						$this->LdapID = $res['LdapID'];
						$this->LdapGid = $entry['gidnumber'];
						$this->LdapTms = $res['LdapTms'];
					}
					else {
						$this->AddError($res);
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
		$entry['cn'] = $this->NomLDAP;
		if($new) {
			$entry['gidnumber'] = Server::getNextGid();
			$entry['objectclass'][0] = 'posixGroup';
			$entry['objectclass'][1] = 'top';
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
		Server::ldapDelete($this->LdapID);
		parent::Delete();
	}

	/**
	 * Creation de l'utilisateur pour ce client
	 */
	public function setUser() {
		//récupération du groupe de stockage des accès clients
		$grp = Group::getGroupFromRole($this->Role);
		if (!sizeof($grp)){
			//Erreur
			$this->AddError(Array("Message"=>"Veuillez mettre le module à jour, les roles ne sont pas définis"));
			return;
		}else $grp = $grp[0];

		//vérification de l'existence de l'utilisateur
		$u = $this->getParents('User');
		if ($this->AccesActif){

            //Création d'un subGroup dédié au clilent
            $sGrp = $grp->getOneChild('Group/Nom='.strtoupper(Utils::KEAddSlashes($this->Nom)));
            if($sGrp){
                $grp = $sGrp;
            }else{
                $sGrp = genericClass::createInstance('Systeme','Group');
                $sGrp->Nom = strtoupper(Utils::KEAddSlashes($this->Nom));
                $sGrp->addParent($grp);
                $sGrp->Save();

                $grp = $sGrp;
            }

			//Vérification des propriétées
			if (!empty($this->AccesUser)&&!empty($this->AccesPass)){
				if (!sizeof($u)){
					//creation de l'utilisateur
					$u = genericClass::createInstance('Systeme','User');
					$u->Login = $this->AccesUser;
					$u->Pass = md5($this->AccesPass);
					$u->Mail = $this->Email;
					$u->Actif = true;
					$u->AddParent($grp);
					$u->Save();
					$this->AddParent($u);
					parent::Save();
				}else{
					//mise à jour utilisateur
					$u = $u[0];
					$u->Login = $this->AccesUser;
					$u->Pass = md5($this->AccesPass);
					$u->Mail = $this->Email;
					$u->Actif = true;
					$u->AddParent($grp);
					$u->Save();
				}
			}else{
				//Erreur
				$this->AddError(Array("Message"=>"Veuillez saisir toutes les informations d'accès web sur la fiche client"));
				
			}
		}else{
			if (sizeof($u)){
				//Si utilisateur alors on désasctive son accès
				$u = $u[0];
				$u->Actif = false;
				$u->Save();
			}
		}
	}
	public function getDomain() {
		return $this->getChildren('Domain');
	}
	public function getHost() {
		return $this->getChildren('Host');
	}
	public function getDomainQuery() {
		return 'Parc/Client/'.$this->Id.'/Domain';
	}
	public function getHostQuery() {
		return 'Parc/Client/'.$this->Id.'/Host';
	}

}