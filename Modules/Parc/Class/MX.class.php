<?php

class MX extends genericClass {
	var $_isVerified = false;
	var $_KEDomain = false;
	var $_KEServer = false;

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
		if($this->_isVerified){
			parent::Save();
			if($synchro) $this->_KEDomain->updateDnsSerial();
		}
		return true;
	}

    /**
     * getLdapID
     * récupère le ldapId d'une entrée pour un serveur spécifique
     */
    public function getLdapID() {
        return $this->LdapID;
    }
    /**
     * setLdapID
     * défniit le ldapId d'une entrée pour un serveur spécifique
     */
    public function setLdapID($ldapId) {
        $this->LdapID = $ldapId;
    }
    /**
     * getLdapDN
     * récupère le ldapDN d'une entrée pour un serveur spécifique
     */
    public function getLdapDN() {
        if (empty($this->LdapDN)) {
            //on construit le dn si il n'existe pas.
            $KEDomain = $this->getKEDomain();
            $this->LdapDN = 'cn='.$this->Nom.',cn='.$KEDomain->Url.',ou=domains,'.PARC_LDAP_BASE;
        }
        return $this->LdapDN;
    }
    /**
     * getLdapDN
     * récupère le ldapDN d'une entrée pour un serveur spécifique
     */
    public function getLdapBaseDN() {
        $KEDomain = $this->getKEDomain();
        return 'cn='.$KEDomain->Url.',ou=domains,'.PARC_LDAP_BASE;

    }
    /**
     * setLdapDN
     * définit le ldapDN d'une entrée pour un serveur spécifique
     */
    public function setLdapDN($ldapDn) {
        $this->LdapDN = $ldapDn;
    }
    /**
     * getLdapTms
     * récupère le ldapTms d'une entrée pour un serveur spécifique
     */
    public function getLdapTms() {
        return $this->LdapTms;
    }
    /**
     * setLdapTms
     * définit le ldapTms d'une entrée pour un serveur spécifique
     */
    public function setLdapTms($ldapTms) {
        $this->LdapTms = $ldapTms;
    }
	/**
	 * Verification des erreurs possibles
	 * @param	boolean	Verifie aussi sur LDAP
	 * @return	Verification OK ou NON
	 */
	public function Verify( $synchro = false ) {
        // Outils
        $KEDomain = $this->getKEDomain();
        $KEServer = $this->getKEServer();
		if (!$this->Poids) $this->Poids = 10;
        //création du nom
        if (empty($this->Nom)){
            //génératio automatique du nom
            for ($i=1;$i<100;$i++){
                $nb = Sys::getCount('Parc','Domain/'.$KEDomain->Id.'/MX/Nom=MX:'.$i);
                if (!$nb){
                    $this->Nom = 'MX:'.$i;
                    break;
                }
            }
        }

		$sibs = false;
        $updateSibsTTL = false;
        $l = Server::ldapGet($this->LdapID);
        if(is_array($l) && isset($l[0]['dnsttl'])){
            $ttl = $l[0]['dnsttl'][0];
            if($ttl != $this->TTL){
                $updateSibsTTL = true;
            }
        }

		if(parent::Verify()) {

			$this->_isVerified = true;

			if($synchro) {

				$dn = 'cn='.$this->Nom.',cn='.$KEDomain->Url.',ou=domains,'.PARC_LDAP_BASE;
				// Verification à jour
				$res = Server::checkTms($this,$this->getKEServer(),$dn);
				if($res['exists']) {
					if(!$res['OK']) {
						$this->AddError($res);
						$this->_isVerified = false;
					}
					else {
						// Déplacement
						$res = Server::ldapRename($this->LdapDN, 'cn='.$this->Nom, 'cn='.$KEDomain->Url.',ou=domains,'.PARC_LDAP_BASE);
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

			if($updateSibsTTL){
			    $pad = $this->getOneParent('Domain');
			    $siblings = $pad->getChildren('MX');
			    foreach ($siblings as $sib){
			        $sib->TTL = $this->TTL;
			        $sib->Save();
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
	private function buildEntry( $new = true ) {
		$entry = array();
		if ($new){
			//recherche du numéro
			$dom = $this->getKEDomain();
			$nb = 1;
            $alr = Sys::getData('Parc','Domain/'.$dom->Id.'/MX');
            $ok=0;
            while (!$ok) {
                $ok=1;
                foreach ($alr as $a){
                    if($a->Nom == 'MX:'.$nb){
                        $ok=0;
                        $nb++;
                        break;
                    }
                }
            }
			$this->Nom='MX:'.$nb;
		}
		$entry['cn'] = $this->Nom;

        //Recup le parent par defaut
        $pa = $this->getOneParent('Domain');
        $default = $pa->Url.'.';

        $entry['dnscname'] = $this->Dnscname ? $this->Dnscname: $default;
		$entry['dnsdomainname'] = $this->Dnsdomainname ? $this->Dnsdomainname : $default;

        $entry['dnsttl'] = $this->TTL ?  $this->TTL : 86400;
        $entry['dnspreference'] = $this->Poids;

		if($new) {
			$entry['objectclass'][0] = 'dnsrrset';
			$entry['objectclass'][1] = 'top';
			$entry['dnsclass'] = 'IN';
			$entry['dnstype'] = 'MX';
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
			// Parent MXServer
			if(!empty($this->Dnscname)) {
				$Tab = Sys::$Modules["Parc"]->callData("Parc/Domain/".$obj->Id."/Subdomain/Url=A:mail", "", 0, 1);
				if(!empty($Tab)) {
					$objSD = genericClass::createInstance('Parc', $Tab[0]);
					$Tab = Sys::$Modules["Parc"]->callData("Parc/MXServer/IP=".$objSD->IP, "", 0, 1);
					if(!empty($Tab)) {
						$objMXS = genericClass::createInstance('Parc', $Tab[0]);
						$this->AddParent($objMXS);
					}
				}
			}
		}
	}


}