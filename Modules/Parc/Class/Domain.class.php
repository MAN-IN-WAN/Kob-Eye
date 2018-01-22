<?php

class Domain extends genericClass {
	var $_isVerified = false;
	var $UpdateOnSave;

	/**
	 * Force la vérification avant enregistrement
	 * @param	boolean	Enregistrer aussi sur LDAP
	 * @return	void
	 */
	public function Save( $synchro = true ) {
		$first = ($this->Id == 0);
		parent::Save();
		// Forcer la vérification
		if(!$this->_isVerified) $this->Verify( $synchro );
		// Enregistrement si pas d'erreur
		if($this->_isVerified) {
			parent::Save();
			if ($this->updateOnSave)
				$this->AutoGenSubDomains();
		}
		return true;
	}


	public function Synchroniser() {
		Server::ldapConnect();
		$req = ldap_search(Server::$_LDAP, $this->LdapDN, '(objectClass=*)', array('*', 'modifytimestamp', 'entryuuid'));
		$res = ldap_get_entries(Server::$_LDAP, $req);
		foreach($res as $k => $r) :
			if($k == 'count' or !isset($r['dnstype']) or $r['dnstype'][0] != 'A' or !isset($r['cn']) or !isset($r['dnsipaddr'])) continue;
			$url = $r['cn'][0];
			$ip = $r['dnsipaddr'][0];
			$e = Sys::$Modules['Parc']->callData('Domain/'.$this->Id.'/Subdomain/Url='.$url,false,0,1,'DESC','Id','COUNT(*)');
			if (!$e[0]['COUNT(*)']){
				$KEObj = genericClass::createInstance('Parc', 'Subdomain');
				$KEObj->Url = $url;
				$KEObj->IP = $ip;
				$KEObj->LdapDN = 'cn='.$url.','.$this->LdapDN;
				$KEObj->LdapID = $r['entryuuid'][0];
				$KEObj->LdapTms = $r['modifytimestamp'][0];
				$KEObj->AddParent($this);
				$KEObj->Save();
				echo "Sous domaine <strong>$url</strong> ($ip) ajouté.<br />";
			}
		endforeach;
		echo '<br /><a href="/Parc/Domain/'.$this->Id.'">Retour au domaine</a>';
	}

	/**
	 * Verification des erreurs possibles
	 * @param	boolean	Verifie aussi sur LDAP
	 * @return	Verification OK ou NON
	 */
	public function Verify( $synchro = true ) {

		if(parent::Verify()) {

			$this->_isVerified = true;

			if($synchro) {

				// Outils
				$dn = 'cn='.$this->Url.',ou=domains,'.PARC_LDAP_BASE;
		
				// Verification à jour
				$res = Server::checkTms($this);
				if($res['exists']) {
					if(!$res['OK']) {
						$this->AddError($res);
						$this->_isVerified = false;
					}
					else {
						// Déplacement
						//$res = Server::ldapRename($this->LdapDN, 'cn='.$this->Url, 'ou=domains,'.PARC_LDAP_BASE);
						//if($res['OK']) {
							// Modification
							$entry = $this->buildEntry(false);
							$res = Server::ldapModify($this->LdapID, $entry);
							if($res['OK']) {
								// Tout s'est passé correctement
								$this->LdapDN = $dn;
								$this->LdapTms = $res['LdapTms'];
								$this->updateDnsSerial();
							}
							else {
								// Erreur
								$this->AddError($res);
								$this->_isVerified = false;
								// Rollback du déplacement
								/*$tab = explode(',', $this->LdapDN);
								$leaf = array_shift($tab);
								$rest = implode(',', $tab);
								Server::ldapRename($dn, $leaf, $rest);*/
							}
						/*}
						else {
							$this->AddError($res);
							$this->_isVerified = false;
						}*/
					}
	
				}
				else {
					////////// Nouvel élément
					$entry = $this->buildEntry();
					$res = Server::ldapAdd($dn, $entry);
					if($res['OK']) {
						$this->LdapDN = $dn;
						$this->DNSSerial = $entry['dnsserial'];
						$this->LdapID = $res['LdapID'];
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
	 * Mise à jour du DNS Serial
	 * Après modification Domaine ou Sous Domaine
	 * Notation : YYYYMMDDVV (annee-mois-jour-version)
	 * @return	void
	 */
	public function updateDnsSerial() {
		// Update jour ou version ?
		if(substr($this->DNSSerial, 0, 8) == date('Ymd')) $serialTms = $this->DNSSerial+1;
		else $serialTms = date('Ymd01');
		// Mise à jour de l'entrée dnsserial		
		$res = Server::ldapModify($this->LdapID, array('dnsserial' => $serialTms));
		// On enregistre si cela s'est bien passé
		if($res['OK']) {
			$this->DNSSerial = $serialTms;
			$this->LdapTms = $res['LdapTms'];
			parent::Save();
		}
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
		$entry['cn'] = $this->Url;
		$entry['dnsadminmailbox'] = 'postmaster.' . $this->Url . '.';
		if (!$new){
			$sd = Sys::$Modules["Parc"]->callData("Domain/".$this->Id."/NS/Nom=NS:1",false,0,1);
			if (is_array($sd[0])){
				$sd[0] = genericClass::createInstance("Parc",$sd[0]);
				$ns = $sd[0]->getParents("Server");
				if (isset($ns[0])&&is_object($ns[0]))
					$entry['dnszonemaster'] = $ns[0]->DNSNom;
				else
					$entry['dnszonemaster'] = DNS_ZONE_MASTER;
			}else	$entry['dnszonemaster'] = DNS_ZONE_MASTER;
		}else	$entry['dnszonemaster'] = DNS_ZONE_MASTER;
		$entry['dnszonename'] = $this->Url;

        $entry['dnsminimum'] = $this->TTLMin ?  $this->TTLMin : 60;
        $entry['dnsttl'] =  $this->TTL ?  $this->TTL : 86400;

		if($new) {
			$entry['dnsclass'] = 'IN';
			$entry['dnsexpire'] = 604800;
			$entry['dnsrefresh'] = 21600;
			$entry['dnsretry'] = 3600;
			$entry['dnsserial'] = date('Ymd01');
			$entry['dnstype'] = 'SOA';
			$entry['objectclass'][0] = 'dnszone';
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
		$KEServer = $this->getKEServer();
		//suppression des sous domaines
		$sd = $this->getChilds('Subdomain');
		if (is_array($sd))foreach ($sd as $s) $s->Delete();
		//suppression des alias sous domaines
		$sd = $this->getChilds('CNAME');
		if (is_array($sd))foreach ($sd as $s) $s->Delete();
		//suppression des servers de noms
		$sd = $this->getChilds('NS');
		if (is_array($sd))foreach ($sd as $s) $s->Delete();
		//suppression des servers de mails
		$sd = $this->getChilds('MX');
		if (is_array($sd))foreach ($sd as $s) $s->Delete();
		//suppression des champs de textes
		$sd = $this->getChilds('TXT');
		if (is_array($sd))foreach ($sd as $s) $s->Delete();
		//suppression des utilisateurs ftp 
		$sd = $this->getChilds('Ftpuser');
		if (is_array($sd))foreach ($sd as $s) $s->Delete();
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
		if(!isset($this->_KEServer)||!is_object($this->_KEServer)) {
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
	}

	/**
	 * Génère automatiquement des sous domains si besoin
	 * @return	void
	 */
	private function AutoGenSubDomains($fromsave=true) {
		$out= '<ul><li><div style="color:green;font-weight:bold" class="debug">Auto gen  '.$this->Url.'</div><ul>';
		//vérification de l'existence d'une template
		$dt = Sys::getOneData('Parc','DomainTemplate/Domain/'.$this->Id);
		if ($dt){
			$Obj = new xml2array($dt->Contenu);
			$TabXml[0] = $Obj->Tableau["TEMPLATE"];
			//Traitement des donn�es du tableau
			$conf = Conf::parseOnly($TabXml);
		}else {
			$obj = empty($GLOBALS) ? Sys::$Conf : $GLOBALS["Systeme"]->Conf;
			$conf = $obj->get("MODULE::PARC::AUTO_DOMAIN");
		}
		//sous domaines
		$out.= "<li>Check <strong>subdomains</strong><ul>\r\n";
		foreach($conf['SOUS_DOMAINE'] as $sub) {
			$KEObj = genericClass::createInstance('Parc', $sub['TYPE']);
			switch($sub['TYPE']) {
				case "Subdomain":
					//test existence
					$e = Sys::$Modules['Parc']->callData('Domain/'.$this->Id.'/Subdomain/Url='.$sub['CN'],false,0,1,'DESC','Id','COUNT(*)');
					if (!$e[0]['COUNT(*)']){
						$e = Sys::$Modules['Parc']->callData('Domain/'.$this->Id.'/CNAME/Dnsdomainname='.substr($sub['CN'],2),false,0,1,'DESC','Id','COUNT(*)');
						if (!$e[0]['COUNT(*)']){
							$KEObj->Url = $sub['CN'];
							$KEObj->IP = $sub['IP'];
							$KEObj->AddParent($this);
							$KEObj->Save();
							$out.= '<li><div style="color:red" class="debug">Add A '.$KEObj->Url.'</div> </li>';
						}
					}
				break;
				case "CNAME":
					//test existence
					$e = Sys::$Modules['Parc']->callData('Domain/'.$this->Id.'/Subdomain/Url=A:'.$sub['DNSDOMAINNAME'],false,0,1,'DESC','Id','COUNT(*)');
					if (!$e[0]['COUNT(*)']){
						$e = Sys::$Modules['Parc']->callData('Domain/'.$this->Id.'/CNAME/Dnsdomainname='.$sub['DNSDOMAINNAME'],false,0,1,'DESC','Id','COUNT(*)');
						if (!$e[0]['COUNT(*)']){
							$KEObj->Nom = $sub['CN'];
							$KEObj->Dnscname = $sub['DNSCNAME'];
							$KEObj->Dnsdomainname = $sub['DNSDOMAINNAME'];
							$KEObj->AddParent($this);
							$KEObj->Save();
							$out.= '<li><div style="color:red" class="debug">Add CNAME '.$KEObj->Nom.'</div> </li>';
						}
					}
				break;
			}
		}
		$out.= "</ul></li>\r\n";

		$out.= "<li>Check <strong>nameservers</strong><ul>\r\n";
		//name server
		foreach($conf['NAME_SERVER'] as $sub) {
			//test existence
			$e = Sys::$Modules['Parc']->callData('Domain/'.$this->Id.'/'.$sub['TYPE'].'/Nom='.$sub['CN'],false,0,1,'DESC','Id','COUNT(*)');
			if (!$e[0]['COUNT(*)']){
				$KEObj = genericClass::createInstance('Parc', $sub['TYPE']);
				$KEObj->Nom = $sub['CN'];
                $KEObj->Dnscname = $sub['DNSCNAME'];
                $KEObj->Dnsdomainname = $this->Url.'.';
				$KEObj->AddParent($this);
				//recherche du serveur de nom associé
//				$Sn = Sys::$Modules['Parc']->callData('Server/DNSNom='.$sub['DNSCNAME']);
//				$Sn = genericClass::createInstance('Parc',$Sn[0]);
//				$KEObj->AddParent($Sn);

				$KEObj->Save();
				$out.= '<li><div style="color:red" class="debug">Add Nameserver '.$KEObj->Nom.'</div> </li>';
			}
		}
		$out.= "</ul></li>\r\n";

		$out.= "<li>Check <strong>mailservers</strong><ul>\r\n";
		//mail server
		if (is_array($conf['MAIL_SERVER'])) {
		    if(isset($conf['MAIL_SERVER']['TYPE'])){
                //test existence
                $e = Sys::$Modules['Parc']->callData('Domain/'.$this->Id.'/'.$conf['MAIL_SERVER']['TYPE'].'/Nom='.$conf['MAIL_SERVER']['CN'],false,0,1,'DESC','Id','COUNT(*)');
                if (!$e[0]['COUNT(*)']){
                    $KEObj = genericClass::createInstance('Parc', $conf['MAIL_SERVER']['TYPE']);
                    $KEObj->Nom = $conf['MAIL_SERVER']['CN'];
                    $KEObj->Dnscname = $conf['MAIL_SERVER']['DNSCNAME'];
                    $KEObj->Poids = (isset($conf['MAIL_SERVER']['WEIGHT'])&&$conf['MAIL_SERVER']['WEIGHT']>0)?$conf['MAIL_SERVER']['WEIGHT']:'10';
                    $KEObj->AddParent($this);
                    $KEObj->Save();
                    $out.= '<li><div style="color:red" class="debug">Add Mailserver '.$KEObj->Nom.'</div> </li>';
                }
            } else{
		        foreach($conf['MAIL_SERVER'] as $ms){
                    $e = Sys::$Modules['Parc']->callData('Domain/'.$this->Id.'/'.$ms['TYPE'].'/Nom='.$ms['CN'],false,0,1,'DESC','Id','COUNT(*)');
                    if (!$e[0]['COUNT(*)']){
                        $KEObj = genericClass::createInstance('Parc', $ms['TYPE']);
                        $KEObj->Nom = $ms['CN'];
                        $KEObj->Dnscname = $ms['DNSCNAME'];
                        $KEObj->Poids = (isset($ms['WEIGHT'])&&$ms['WEIGHT']>0)?$ms['WEIGHT']:'10';
                        $KEObj->AddParent($this);
                        $KEObj->Save();
                        $out.= '<li><div style="color:red" class="debug">Add Mailserver '.$KEObj->Nom.'</div> </li>';
                    }
                }
            }
		}
		$out.= "</ul></li>\r\n";
		$out.= "</ul></li></ul>";
		if (!$fromsave) echo $out;
	}

	function checkIntegrity() {
		$error = 0;
		echo '<ul><li><div style="color:green;font-weight:bold" class="debug">Check domain  '.$this->Url.'</div><ul>';
		//Verification des données erronées
		$f = Sys::$Modules['Parc']->callData('Domain/'.$this->Id.'/CNAME',false,0,100);
		if (is_array($f)) foreach ($f as $cn){
			$cn = genericClass::createInstance('Parc',$cn);
			if ($cn->Nom==""&&$cn->Dnscname==""){
				echo '<li><div style="color:red" class="debug">Delete bad entry</div> </li>';
				$error++;
				$cn->Delete();
			}
		}
				
		echo "<li>Search <strong>doublons</strong><ul>\r\n";
		//on recense chaque adresse A et on verifie qu'il n'y est pas un doublon en CNAME
		$e = Sys::$Modules['Parc']->callData('Domain/'.$this->Id.'/Subdomain',false,0,1000);
		if (is_array($e)) foreach ($e as $d){
			if (substr($d['Url'],2)!=""){
				$f = Sys::$Modules['Parc']->callData('Domain/'.$this->Id.'/CNAME/Dnsdomainname='.substr($d['Url'],2),false,0,2);
				if (is_array($f)) foreach ($f as $cn){
					$cn = genericClass::createInstance('Parc',$cn);
					echo '<li><div style="color:red" class="debug">Delete doublon CNAME '.$cn->Nom.'</div> </li>';
					$error++;
					$cn->Delete();
				}
			}
			$f = Sys::$Modules['Parc']->callData('Domain/'.$this->Id.'/Subdomain/Url='.$d['Url'],false,0,20);
			if (is_array($f)&&sizeof($f)>1) foreach ($f as $k=>$cn){
				if ($k>0){
					$cn = genericClass::createInstance('Parc',$cn);
					$cn->Delete();
					$error++;
					echo '<li><div style="color:red" class="debug">Delete doublon A '.$cn->Nom.'</div> </li>';
				}
			}
		}
		echo "</ul></li>\r\n";
		
		echo "<li>Check <strong>ldap</strong><ul>\r\n";
		//Verification de la liaison ldap pour chacun des éléments
		$e = Sys::$Modules['Parc']->callData('Domain/'.$this->Id.'/Subdomain',false,0,1000);
		if (is_array($e)) foreach ($e as $d){
			$cn = genericClass::createInstance('Parc',$d);
			if (!$cn->checkIntegrity()){
				echo '<li><div style="color:red" class="debug">Ajout '.$cn->Url.'</div></li>';
				$error++;
			 	$cn->Save();	
			}
		}
		echo "</ul></li>\r\n";
		
		if ($error){
			echo '<li><div style="color:red" class="debug">'.$error.' errors found</div></li>';
			//sauvegarde du domaine et incrementation du numero de serie
			$this->Save();
		}else{
			echo '<li><div style="color:green" class="debug">no error found</div></li>';
		}
		
		echo "</ul></li></ul>";
		if ($error){
			$this->updateOnSave = false;
			$this->Save(false);
		}
		
		$this->AutoGenSubDomains(false);
	}

}