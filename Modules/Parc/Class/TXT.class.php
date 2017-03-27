<?php

class TXT extends genericClass {
	var $_isVerified = false;
	var $_KEDomain;
	var $_KEServer;

	/**
	 * Force la vérification avant enregistrement
	 * @param	boolean	Enregistrer aussi sur LDAP
	 * @return	void
	 */
	public function Save( $synchro = true ) {
		parent::Save();
		// Forcer la vérification
		if(!$this->_isVerified) $this->Verify( $synchro );
		// Enregistrement si pas d'erreur
		if($this->_isVerified) {
		    parent::Save();
            if($synchro) $this->_KEDomain->updateDnsSerial();
        }
	}

	/**
	 * Verification des erreurs possibles
	 * @param	boolean	Verifie aussi sur LDAP
	 * @return	Verification OK ou NON
	 */
	public function Verify( $synchro = true ) {
        if (!$this->Type) {
            $this->Type = 'TXT';
        }
		if(parent::Verify()) {

			$this->_isVerified = true;

			if($synchro) {

				// Outils
				$KEDomain = $this->getKEDomain();
				$KEServer = $this->getKEServer();
				$dn = 'cn='.$this->Nom.',cn='.$KEDomain->Url.',ou=domains,'.PARC_LDAP_BASE;
				// Verification à jour
				$res = Server::checkTms($this);
				if($res['exists']) {
					if(!$res['OK']) {
						$this->AddError($res);
						$this->_isVerified = false;
					}
					else {
                        //Compatibilité avec ancien systeme
                        if (preg_match('#^(TXT|TXT2|TXT3|SPF|SPF2|SPF3)$#',$this->Nom,$out)){
                            Server::ldapDelete($this->LdapID);
                            $entry = $this->buildEntry(true,true);
                            $dn = 'cn='.$this->Nom.',cn='.$KEDomain->Url.',ou=domains,'.PARC_LDAP_BASE;
                            $res = Server::ldapAdd($dn, $entry);
                            if($res['OK']) {
                                $this->LdapDN = $dn;
                                $this->LdapID = $res['LdapID'];
                                $this->LdapTms = $res['LdapTms'];
                            }
                            else {
                                $this->AddError($res);
                                $this->_isVerified = false;
                            }
                        }else{
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
					}
	
				}
				else {
					////////// Nouvel élément
					if($KEDomain) {
					    $entry = $this->buildEntry();
						$dn = 'cn='.$this->Nom.',cn='.$KEDomain->Url.',ou=domains,'.PARC_LDAP_BASE;
						$res = Server::ldapAdd($dn, $entry);
						if($res['OK']) {
							$this->LdapDN = $dn;
							$this->LdapID = $res['LdapID'];
							$this->LdapTms = $res['LdapTms'];
						}
						else {
							$this->AddError($res);
							$this->_isVerified = false;
						}
					}
					else {
						$this->AddError(array('Message' => "Une entrée de ce type doit être créée dans un domaine donné.", 'Prop' => ''));
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
	 * Récupère une référence vers l'objet KE "Domain" parent
	 * On conserve une référence vers le host
	 * pour le cas d'une utilisation ultérieure
	 * @return	L'objet Kob-Eye
	 */
	private function getKEDomain() {
		if(!is_object($this->_KEDomain)) {
			$tab = $this->getParents('Domain');
			if(empty($tab)) return false;
			else $this->_KEDomain = $tab[0];
		}
		return $this->_KEDomain;
	}

	/**
	 * Configuration d'une nouvelle entrée type
	 * Utilisé lors du test dans Verify
	 * puis lors du vrai ajout dans Save
	 * @param	boolean		Si FALSE c'est simplement une mise à jour
	 * @return	Array
	 */
	private function buildEntry( $new = true , $retrocompat = false) {
		$entry = array();
		if ($new){
			//recherche du numéro
			$dom = $this->getKEDomain();
			$nb = 1;

            $alr = Sys::getData('Parc','Domain/'.$dom->Id.'/TXT/Nom~'.$this->Type);
            $ok=0;
            while (!$ok) {
                $ok=1;
                foreach ($alr as $a){
                    if($a->Nom == $this->Type.':'.$nb){
                        $ok=0;
                        $nb++;
                        break;
                    }
                }
            }
			$this->Nom=$this->Type.':'.$nb;
			parent::Save();
		}
		$entry['cn'] = $this->Nom;
		$entry['dnsdomainname'] = $this->Dnsdomainname;
		if ($retrocompat&&!strpos($this->Dnstxt,'"')) {
            $this->Dnstxt = '"' . $this->Dnstxt . '"';
            parent::Save();
        }
        $this->Dnstxt = str_replace('""','"',$this->Dnstxt);
        $entry['dnstxt'] = $this->Dnstxt;
		if($new) {
			$entry['objectclass'][0] = 'dnsrrset';
			$entry['objectclass'][1] = 'top';
			$entry['dnsclass'] = 'IN';
			$entry['dnstype'] = 'TXT';
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
	 * Récupère une référence vers l'objet KE "Server"
	 * pour effectuer des requetes LDAP
	 * On conserve une référence vers le serveur
	 * pour le cas d'une utilisation ultérieure
	 * @return	L'objet Kob-Eye
	 */
	private function getKEServer() {
		if(!is_object($this->_KEServer)) {
			$Tab = Sys::$Modules["Parc"]->callData('Parc/Server/1', "", 0, 1);
			$this->_KEServer = genericClass::createInstance('Parc', $Tab[0]);
		}
		return $this->_KEServer;
	}

	/**
	 * Retrouve les parents lors d'une synchronisation
	 * @return	void
	 */
	public function findParents() {
		$Parts = explode(',', $this->LdapDN);
		foreach($Parts as $i => $P) $Parts[$i] = explode('=', $P);
		// Parent Domain
		$Tab = Sys::$Modules["Parc"]->callData("Parc/Domain/Url=".$Parts[1][1], "", 0, 1);
		if(!empty($Tab)) {
			$obj = genericClass::createInstance('Parc', $Tab[0]);
			$this->AddParent($obj);
		}
	}

}
