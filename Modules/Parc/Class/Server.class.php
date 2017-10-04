<?php

class Server extends genericClass {

	// Connexion LDAP
	static $_LDAP;

	// Date de dernière MAJ
	static $_DATELASTUPDATE;

	// Guide de l'association de données dans Kob-Eye (Nouvel objet)
	var $_assocClient = array('NomLDAP' => 'cn', 'LdapGid' => 'gidnumber');
	var $_assocHost = array('Nom' => 'cn', 'LdapGid' => 'gidnumber', 'LdapUid' => 'uidnumber','PHPVersion' => 'preferredLanguage');
	var $_assocApache = array('DocumentRoot' => 'apachedocumentroot', 'ApacheServerName' => 'apacheservername', 'ApacheServerAlias' => 'apacheserveralias');
	var $_assocFtpuser = array('Identifiant' => 'uid', 'Password' => 'userpassword', 'DocumentRoot' => 'homedirectory');
	var $_assocDomain = array('DNSSerial' => 'dnsserial', 'Url' => 'cn');
	var $_assocSubdomain = array('IP' => 'dnsipaddr', 'Url' => 'cn');
	var $_assocMX = array('Nom' => 'cn', 'Dnscname' => 'dnscname');
	var $_assocNS = array('Nom' => 'cn', 'Dnscname' => 'dnscname');
	var $_assocCNAME = array('Nom' => 'cn', 'Dnscname' => 'dnscname', 'Dnsdomainname' => 'dnsdomainname');
	var $_assocTXT = array('Nom' => 'cn', 'Dnsdomainname' => 'dnsdomainname', 'Dnstxt' => 'dnstxt');

    var $_isVerified = false;

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
        }
        return true;
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
                $dn = 'ou='.$this->LDAPNom.',ou=servers,'.PARC_LDAP_BASE;
                $dn2 = 'cn='.$this->LDAPNom.','.PARC_LDAP_BASE;

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
                        $entry2 = $this->buildUserEntry(false);
                        $res2 = Server::ldapModify($this->LdapUserID, $entry2);
                        if($res['OK']&&$res2['OK']) {
                            // Tout s'est passé correctement
                            $this->LdapDN = $dn;
                            $this->LdapTms = $res['LdapTms'];
                            $this->LdapUserDN = $dn2;
                            $this->LdapUserTms = $res['LdapTms'];
                        }
                        else {
                            // Erreur
                            if (!$res['OK'])
                                $this->AddError($res);
                            if (!$res2['OK'])
                                $this->AddError($res2);
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
                    $entry2 = $this->buildUserEntry();
                    $res2 = Server::ldapAdd($dn2, $entry2);
                    if($res['OK']&&$res2['OK']) {
                        $this->LdapDN = $dn;
                        $this->LdapID = $res['LdapID'];
                        $this->LdapTms = $res['LdapTms'];
                        $this->LdapUserDN = $dn2;
                        $this->LdapUserID = $res2['LdapID'];
                        $this->LdapUserTms = $res2['LdapTms'];
                    }
                    else {
                        if (!$res['OK'])
                            $this->AddError($res);
                        else $this->Delete();
                        if (!$res2['OK'])
                            $this->AddError($res2);
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
     *
     * dn: ou=ws2.enguer.com,ou=servers,dc=enguer,dc=com
        objectclass: organizationalUnit
        objectclass: top
        ou: ws2.enguer.com
     *
     * dn: cn=ws2.enguer.com,dc=enguer,dc=com
        cn: ws2.enguer.com
        displayname: ws2.enguer.com read only
        objectclass: inetOrgPerson
        objectclass: top
        sn: ws2.enguer.com
        uid: cn=ws2.enguer.com,dc=enguer,dc=com
        userpassword: {SSHA}QT7YK+30GU7cAS/IeWX+xVNimqvPWDpD
     *
     * @param	boolean		Si FALSE c'est simplement une mise à jour$dn
     * @return	Array
     */
    private function buildUserEntry( $new = true ) {
        $entry = array();
        $entry['cn'] = $this->LDAPNom;
        $entry['sn'] = $this->LDAPNom;
        $entry['uid'] = 'cn='.$this->LDAPNom.PARC_LDAP_BASE;
        $entry['displayname'] = '' . $this->LDAPNom . ' read only';
        $entry['userpassword'] = '{SSHA}QT7YK+30GU7cAS/IeWX+xVNimqvPWDpD';
        if($new) {
            $entry['objectclass'][0] = 'inetOrgPerson';
            $entry['objectclass'][1] = 'top';
        }
        return $entry;
    }
    private function buildEntry( $new = true ) {
        $entry = array();
        $entry['ou'] = $this->LDAPNom;
        if($new) {
            $entry['objectclass'][0] = 'organizationalUnit';
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
     * Récupérer les mails presents sur le serveur et les créer/updater objets CompteMail si besoin
     * @param boolean $dryrun : Si true on affiche seulement les mails
     * @return	String rapport
     */
    public function getMails($dryrun=false) {
        //TODO: Vérifier que les comptes admin sont bien actifs avant !!!
        $report = '';

        if(!isset($this->IP) || $this->IP =='' || !isset($this->mailAdminPort) || $this->mailAdminPort == '' || !isset($this->mailAdminUser) || $this->mailAdminUser == '' || !isset($this->mailAdminPassword) || $this->mailAdminPassword == ''){
            $report = 'Veuillez vérifier la configuration du serveur. En l\'état il nous est impossible de nous connecter a l\'administration du serveur de mail';
            return $report;
        }

        // Create a new Admin class and authenticate
        $zimbra = new \Zimbra\ZCS\Admin($this->IP, $this->mailAdminPort);
        $zimbra->auth($this->mailAdminUser, $this->mailAdminPassword);

        try{
            $domaines = $zimbra->getDomains();
            $quotas = $zimbra->getQuotas(array());
            //echo '<pre>';
            //print_r($quotas);
            //echo '</pre>';
            $cosesTemp = $zimbra->getAllCos();
            $coses = array();
            foreach ($cosesTemp as $cosTemp){
                $coses[$cosTemp->get('id')]=$cosTemp;
            }


            foreach($domaines as $domain){
                //echo '<pre>';
                //print_r($domain);
                //echo '</pre>';

                $dname = $domain->get('name');
                //print_r($dname.'<br/>');
                $kDom = Sys::getOneData('Parc','Domain/Url='.$dname);
                if(!is_object($kDom)){
                    $report .= '<b>Domaine</b> "'.$dname.'" absent du Parc. Les adresses appartenant à ce domaine seront ignorées car impossible à relier à un client.<br>'.PHP_EOL;
                    continue;
                }
                $kCli = $kDom->getOneParent('Client');
                if(!is_object($kCli)){
                    $report .= '<b>Client</b> introuvable pour le domaine "'.$dname.'". Les adresses appartenant à ce domaine seront ignorées car impossible à relier à un client.<br>'.PHP_EOL;
                    continue;
                }

                $report .= '<b>Domaine</b> "'.$dname.': .<br>'.PHP_EOL;

                $accList = $zimbra->getAllAccounts($dname);
                foreach($accList as $account){
                    //echo '<pre>';
                    //print_r($account);
                    //echo '</pre>';
                    //exit;


                    $accHost = $account->get('zimbraMailHost');
                    if($accHost != $this->DNSNom){
                        continue;
                    }
                    $accId = $account->get('id');
                    $accName = $account->get('name');
                    $userNom = $account->get('sn');
                    $userPrenom = $account->get('givenName');
                    //print_r($accId.' : '.$accName.'<br>');
                    //print_r($quotas[$accId]['limit'].' / '.$quotas[$accId]['used'] .'<br>');
                    $userQuota = $quotas[$accId]['limit'];
                    $userUsed = $quotas[$accId]['used'];
                    $accStatus = $account->get('zimbraMailStatus');
                    $cosId = $account->get('zimbraCOSId');
                    $cos ='NULL';
                    if(isset($cosId) && $cosId != '')
                        $cos = $coses[$cosId];

                    $o = Sys::getOneData('Parc','CompteMail/Adresse='.$accName);
                    if(!is_object($o)){
                        $o = genericClass::createInstance('Parc','CompteMail');
                        $o->IdMail = $accId;
                        $o->Adresse = $accName;
                        $report .= '<b>Nouvelle adresse trouvée</b> : '.$accName.'.<br>'.PHP_EOL;
                    }
                    $o->COS = $cos;
                    $o->Nom = $userNom;
                    $o->Prenom = $userPrenom;
                    $o->Quota = floor($userQuota/1048576); //En Mo
                    $o->EspaceUtilise =floor($userUsed/1048576); //En Mo
                    $o->Status = $accStatus;


                    $o->addParent($this);
                    $o->addParent($kCli);

                    if(!$dryrun){
                        $o->Save();
                    }
                }
            }
        } catch (Exception $e){
            $report .= print_r ($e,true);
        }

         return $report;
    }


    /*******************************************************************************************************************************

	 SYNCHRONISATION

	 ********************************************************************************************************************************/

	/**
	 * Mise à jour des éléements depuis le serveur LDAP
	 * @return	void
	 */
	public function Synchroniser() {

		// On détermine la dernière date de mise à jour
		$this -> getLastUpdate(true);

		// Connexion à la base LDAP
		if ($this -> ldapConnect())
			$this -> debug("Connexion LDAP établie");
		else
			$this -> error("Impossible d'établie une connexion à la base");

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

		// Synchro des éléments indépendants
		$this -> synchroPartielle('clients');
		$this -> synchroPartielle('domains');
		$this -> synchroPartielle('servers');

		// Fin
		$this -> debug('Synchronisation terminée');

	}


    /**
     * Mise à jour des éléements depuis le serveur LDAP
     * @return	void
     */
    public function SynchServ() {

        // On détermine la dernière date de mise à jour
        $this -> getLastUpdate(true);

        // Connexion à la base LDAP
        if ($this -> ldapConnect())
            $this -> debug("Connexion LDAP établie");
        else
            $this -> error("Impossible d'établie une connexion à la base");

        Server::ldapConnect();

        $this -> synchroPartielle('servers');

        // Fin
        $this -> debug('Synchronisation terminée');

    }

	/**
	 * Récupère les données nouvelles pour un groupe de données et met à jour la base KE
	 * -> Effectue la requete
	 * -> Selon le type de l'élément action specifique
	 * @param	string	Noeud dans l'arborescence LDAP
	 * @return	void
	 */
	private function synchroPartielle($group) {

		// Requete
		$req = ldap_search(Server::$_LDAP, 'ou=' . $group . ',' . PARC_LDAP_BASE, '(&(objectClass=*)(modifytimestamp>=' . $this -> _DATELASTUPDATE . ')(!(modifytimestamp=' . $this -> _DATELASTUPDATE . ')))', array('*', 'modifytimestamp', 'entryuuid'));
		$data = ldap_get_entries(Server::$_LDAP, $req);

		// On parcours les données
		for ($i = 0; $i < $data['count']; $i++) {

			// On détermine le type
			$type = $this -> findDataType($data[$i]);

			if (in_array($type, array('Client', 'Host', 'Apache', 'Ftpuser', 'Domain', 'Subdomain', 'MX', 'NS', 'CNAME', 'TXT'))) {

				// On instancie un objet KE
				$assocVar = '_assoc' . $type;
				$KEObj = $this -> getKEObject($type, $data[$i], $this -> {$assocVar});

				// Action spécifique selon le type
				switch($type) {
					case 'Client' :
						$text = ($KEObj -> Id ? 'Client mis à jour' : 'Nouveau Client') . ' : ' . $KEObj -> NomLDAP;
						break;
					case 'Host' :
						$text = ($KEObj -> Id ? 'Hébergement mis à jour' : 'Nouvel Hébergement') . ' : ' . $KEObj -> Nom;
						$KEObj -> FindParents();
						break;
					case 'Apache' :
						$text = ($KEObj -> Id ? 'Config Apache mis à jour' : 'Nouvelle Config Apache') . ' : ' . $KEObj -> ApacheServerName;
						$KEObj -> FindParents();
						break;
					case 'Ftpuser' :
						$text = ($KEObj -> Id ? 'Utilisateur FTP mis à jour' : 'Nouvel Utilisateur FTP') . ' : ' . $KEObj -> Identifiant;
						$KEObj -> FindParents();
						break;
					case 'Domain' :
						$text = ($KEObj -> Id ? 'Domaine mis à jour' : 'Nouveau Domaine') . ' : ' . $KEObj -> Url;
						$KEObj -> FindParents();
						break;
					case 'Subdomain' :
						$text = ($KEObj -> Id ? 'Sous domaine mis à jour' : 'Nouveau Sous domaine') . ' : ' . $KEObj -> Url;
						$KEObj -> FindParents();
						break;
					case 'MX' :
						$text = ($KEObj -> Id ? 'Configuration MX mise à jour' : 'Nouvelle Configuration MX') . ' : ' . $KEObj -> Nom;
						$KEObj -> FindParents();
						break;
					case 'NS' :
						$text = ($KEObj -> Id ? 'Configuration NS mise à jour' : 'Nouvelle Configuration NS') . ' : ' . $KEObj -> Nom;
						$KEObj -> FindParents();
						break;
					case 'CNAME' :
						$text = ($KEObj -> Id ? 'Configuration CNAME mise à jour' : 'Nouvelle Configuration CNAME') . ' : ' . $KEObj -> Nom;
						$KEObj -> FindParents();
						break;
					case 'TXT' :
						$text = ($KEObj -> Id ? 'Configuration SPF mise à jour' : 'Nouvelle Configuration TXT') . ' : ' . $KEObj -> Nom;
						$KEObj -> FindParents();
						break;
				}

				// Enregistrement en BDD
				$KEObj -> Save(false);
				$this -> info('<a href="/Parc/' . $type . '/' . $KEObj -> Id . '">' . $text . '</a>');

				// Post traitement
				if ($type == 'Apache')
					$this -> info($KEObj -> findSubDomains() . ' sous domaines associés');

			} else {
				$this -> debug('Entrée non traitée : ' . $data[$i]['dn']);
			}

		}

	}

	/**
	 * Retrouve le type de données selon le dn
	 * @param	string	DN de référence
	 * @return	Le type Kob-Eye dans le module Parc
	 */
	private function findDataType($leaf) {
		$tab = array();
		$arbo = explode(',', $leaf['dn']);
		foreach ($arbo as $k => $entry) {
			$detail = explode('=', $entry);
			$tab[] = array('key' => $detail[0], 'val' => $detail[1]);
		}
		switch(@$tab[0]['key']) {
			case 'apacheServerName' :
			// ex : apacheServerName=www.abtel.fr,cn=abtel,ou=ws10.abtel.fr,ou=servers,dc=abtel,dc=fr
				if (@$tab[1]['key'] == 'cn' and @$tab[2]['key'] == 'ou' and @$tab[3]['val'] == 'servers')
					return 'Apache';
				break;
			case 'uid' :
			// ex : uid=admin@abtel.fr,ou=users,cn=ws10.abtel.fr,ou=ws10.abtel.fr,ou=servers,dc=abtel,dc=fr
				if (@$tab[1]['key'] == 'ou' and @$tab[1]['val'] == 'users' and @$tab[4]['val'] == 'servers')
					return 'Ftpuser';
				break;
			case 'cn' :
				switch(@$tab[1]['key']) {
					case 'ou' :
						switch(@$tab[1]['val']) {
							case 'clients' :
							// ex : cn=abtel,ou=clients,dc=abtel,dc=fr
								return 'Client';
								break;
							case 'domains' :
							// ex : cn=abtel.fr,ou=domains,dc=abtel,dc=fr
								return 'Domain';
								break;
							default :
							// ex : cn=abtel,ou=ws10.abtel.fr,ou=servers,dc=abtel,dc=fr
								if (@$tab[2]['key'] == 'ou' and @$tab[2]['val'] == 'servers')
									return 'Host';
								break;
						}
						break;
					case 'cn' :
						switch($leaf['dnstype'][0]) {
							case 'A' :
							// ex : cn=A:www,cn=abtel.fr,ou=domains,dc=abtel,dc=fr
								return 'Subdomain';
								break;
							case 'MX' :
							// ex : cn=MX1:,cn=abtel.fr,ou=domains,dc=abtel,dc=fr
								return 'MX';
								break;
							case 'NS' :
							// ex : cn=NS1:.fr,ou=domains,dc=abtel,dc=fr
								return 'NS';
								break;
							case 'CNAME' :
							// ex : cn=CNAME1:support,cn=abtel.fr,ou=domains,dc=abtel,dc=fr
								return 'CNAME';
								break;
							case 'TXT' :
							// ex : cn=SPF,cn=abtel.fr,ou=domains,dc=abtel,dc=fr
								return 'TXT';
								break;
						}
						break;
				}
				break;
		}
	}

	/**
	 * Détermine la date de la donnée la plus à jour
	 * @param	boolean	Afficher la date trouvée
	 * @return	void
	 */
	private function getLastUpdate($verbose = false) {
		// Date minimale
		$tms = "19700101000000Z";
		// On vérifie dans tous les objets
		$Tab = Sys::$Modules["Parc"] -> callData("Parc/Client", "", 0, 1, "DESC", "LdapTms");
		if (!empty($Tab) && $Tab[0]["LdapTms"] > $tms)
			$tms = $Tab[0]["LdapTms"];
		$Tab = Sys::$Modules["Parc"] -> callData("Parc/Host", "", 0, 1, "DESC", "LdapTms");
		if (!empty($Tab) && $Tab[0]["LdapTms"] > $tms)
			$tms = $Tab[0]["LdapTms"];
		$Tab = Sys::$Modules["Parc"] -> callData("Parc/Apache", "", 0, 1, "DESC", "LdapTms");
		if (!empty($Tab) && $Tab[0]["LdapTms"] > $tms)
			$tms = $Tab[0]["LdapTms"];
		$Tab = Sys::$Modules["Parc"] -> callData("Parc/Ftpuser", "", 0, 1, "DESC", "LdapTms");
		if (!empty($Tab) && $Tab[0]["LdapTms"] > $tms)
			$tms = $Tab[0]["LdapTms"];
		$Tab = Sys::$Modules["Parc"] -> callData("Parc/Domain", "", 0, 1, "DESC", "LdapTms");
		if (!empty($Tab) && $Tab[0]["LdapTms"] > $tms)
			$tms = $Tab[0]["LdapTms"];
		$Tab = Sys::$Modules["Parc"] -> callData("Parc/Subdomain", "", 0, 1, "DESC", "LdapTms");
		if (!empty($Tab) && $Tab[0]["LdapTms"] > $tms)
			$tms = $Tab[0]["LdapTms"];
		$Tab = Sys::$Modules["Parc"] -> callData("Parc/MX", "", 0, 1, "DESC", "LdapTms");
		if (!empty($Tab) && $Tab[0]["LdapTms"] > $tms)
			$tms = $Tab[0]["LdapTms"];
		$Tab = Sys::$Modules["Parc"] -> callData("Parc/NS", "", 0, 1, "DESC", "LdapTms");
		if (!empty($Tab) && $Tab[0]["LdapTms"] > $tms)
			$tms = $Tab[0]["LdapTms"];
		$Tab = Sys::$Modules["Parc"] -> callData("Parc/CNAME", "", 0, 1, "DESC", "LdapTms");
		if (!empty($Tab) && $Tab[0]["LdapTms"] > $tms)
			$tms = $Tab[0]["LdapTms"];
		$Tab = Sys::$Modules["Parc"] -> callData("Parc/TXT", "", 0, 1, "DESC", "LdapTms");
		if (!empty($Tab) && $Tab[0]["LdapTms"] > $tms)
			$tms = $Tab[0]["LdapTms"];
		// Message de lOG
		if ($verbose) {
			$y = substr($tms, 0, 4);
			$m = substr($tms, 4, 2);
			$d = substr($tms, 6, 2);
			$h = substr($tms, 8, 2);
			$i = substr($tms, 10, 2);
			$s = substr($tms, 12, 2);
			$this -> verbose('Date du dernier update : ' . date('d F Y à H:i:s', mktime($h, $i, $s, $m, $d, $y)));
		}
		$this -> _DATELASTUPDATE = $tms;
	}

	/**
	 * Récupérer un objet Kob-Eye existant ou initialiser un nouveau
	 * @param	string	Type d'objet (Server, FtpUser, Host, Client, ...)
	 * @param	array	Donnée LDAP
	 * @param	string	Valeurs à conserver de LDAP vers KE
	 * @return	Objet KE
	 */
	public function getKEObject($type, $entry, $assoc = array()) {

		// Nouvel objet ou objet existant ?
		$Tab = Sys::$Modules["Parc"] -> callData("Parc/" . $type . "/LdapID=" . $entry['entryuuid'][0], "", 0, 1);
		if (empty($Tab))
			$obj = genericClass::createInstance('Parc', $type);
		else
			$obj = genericClass::createInstance('Parc', $Tab[0]);

		// Données à affecter
		$obj -> LdapDN = $entry['dn'];
		$obj -> LdapID = $entry['entryuuid'][0];
		$obj -> LdapTms = $entry['modifytimestamp'][0];
		foreach ($assoc as $key => $field) {
			unset($entry[$field]['count']);
			$obj -> {$key} = @implode("\r", $entry[$field]);
		}
		return $obj;

	}

	/*******************************************************************************************************************************

	 LOG

	 ********************************************************************************************************************************/

	/**
	 * Fonctions pour le suivi de la synchro
	 * @param	string	Texte à afficher
	 * @param	boolean	Mettre en gras
	 * @return	void
	 */
	public function error($str, $bold = false) {
		if ($bold)
			$str = '<strong>' . $str . '</strong>';
		echo '<div style="color:red">' . $str . '</div>';
	}

	public function debug($str, $bold = false) {
		if ($bold)
			$str = '<strong>' . $str . '</strong>';
		echo '<div style="color:green" class="debug">' . $str . '</div>';
	}

	public function info($str, $bold = false) {
		if ($bold)
			$str = '<strong>' . $str . '</strong>';
		echo '<div>' . $str . '</div>';
	}

	public function verbose($str, $bold = false) {
		if ($bold)
			$str = '<strong>' . $str . '</strong>';
		echo '<div style="color:blue" class="verbose">' . $str . '</div>';
	}

	public function dump($var, $bold = false) {
		if ($bold)
			echo '<strong>';
		echo '<pre>';
		print_r($var);
		echo '</pre>';
		if ($bold)
			echo '</strong>';
	}

	/*******************************************************************************************************************************

	 FONCTIONS LDAP

	 ********************************************************************************************************************************/

	/**
	 * Connexion à LDAP
	 * Utilise les données su serveur courant
	 * Stocke la connexion dans un attribut privé
	 * @return 	Liaison
	 */
	static function ldapConnect() {
		if (!is_null(Server::$_LDAP))
			return Server::$_LDAP;
		Server::$_LDAP = ldap_connect(PARC_LDAP_IP);
		if (Server::$_LDAP) {
			ldap_set_option(Server::$_LDAP, LDAP_OPT_PROTOCOL_VERSION, 3);
			$bind = ldap_bind(Server::$_LDAP, PARC_LDAP_LOGIN, PARC_LDAP_PASSWORD);
			if ($bind)
				return Server::$_LDAP;
		}
		return Server::$_LDAP;
	}

	/**
	 * Retourne tous les champs d'un objet
	 * @param	string	ID sous LDAP
	 * @return	Array
	 */
	static function ldapGet($ldapID) {
		$connect = Server::ldapConnect();
		$search = ldap_search(Server::$_LDAP, PARC_LDAP_BASE, 'entryuuid=' . $ldapID, array('*', 'modifytimestamp', 'entryuuid'));
		$res = ldap_get_entries(Server::$_LDAP, $search);
		return $res;
	}

	/**
	 * Ajoute un élément dans l'arbre
	 * @param	string	chemin complet de l'élément à insérer
	 * @param	array	configuration complète de l'entrée
	 * @return 	Tableau de debug
	 */
	static function ldapAdd($dn, $entry) {
		$e = array();
		$connect = Server::ldapConnect();
		$req = @ldap_add(Server::$_LDAP, $dn, $entry);
		if ($connect and $req) {
			// L'enregistrement a réussi - on récupère l'id et le tms LDAP
			$search = ldap_search(Server::$_LDAP, $dn, 'objectClass=*', array('modifytimestamp', 'entryuuid'));
			$res = ldap_get_entries(Server::$_LDAP, $search);
			$e['OK'] = true;
			$e['LdapTms'] = $res[0]['modifytimestamp'][0];
			$e['LdapID'] = $res[0]['entryuuid'][0];
		} else {
			// L'enregistrement a échoué - on récupère l'erreur
			$e['OK'] = false;
			$e['Message'] = "Erreur LDAP lors de l'ajout - " . @ldap_error(Server::$_LDAP).' DN: '.$dn;
			$e['Prop'] = '';
		}
		return $e;
	}

	/**
	 * Renomme un élément dans l'arbre
	 * @param	string	ancien chemin de l'élément
	 * @param	string	nouveau nom de l'élément
	 * @param	string	nouveau parent de l'élément
	 * @return 	"LDAP TMS" de l'objet après sa modification
	 */
	static function ldapRename($olddn, $newrdn, $parent) {
		$e = array();
		$connect = Server::ldapConnect();
		//echo "ldaprename ".Server::$_LDAP." - ".$olddn." - ".$newrdn." - ".$parent." <br />\r\n";
		$req = @ldap_rename(Server::$_LDAP, $olddn, $newrdn, $parent, true);
		if ($connect and $req) {
			// Le déplacement a réussi - on récupère le tms LDAP
			$search = ldap_search(Server::$_LDAP, $newrdn . ',' . $parent, 'objectClass=*', array('modifytimestamp', 'entryuuid'));
			$res = ldap_get_entries(Server::$_LDAP, $search);
			$e['OK'] = true;
			$e['LdapTms'] = $res[0]['modifytimestamp'][0];
			$e['LdapID'] = $res[0]['entryuuid'][0];
		} else {
			// L'enregistrement a échoué - on récupère l'erreur
			$e['OK'] = false;
			$e['Message'] = "Erreur LDAP lors du déplacement - " . @ldap_error(Server::$_LDAP).' OLDDN: '.$olddn.' NEWDN: '.$newrdn;
			$e['Prop'] = '';
		}
		return $e;
	}

	/**
	 * Modifie un élément dans l'arbre
	 * @param	string	ID de l'élément à modifier
	 * @param	array	données à modifier
	 * @return 	void
	 */
	static function ldapModify($ldapID, $entry) {
		$connect = Server::ldapConnect();
		$res = Server::ldapGet($ldapID);
		$req = @ldap_modify(Server::$_LDAP, $res[0]['dn'], $entry);
		if ($connect and $req) {
			// La modif a réussi - on récupère le tms LDAP
			$res = Server::ldapGet($ldapID);
			$e['OK'] = true;
			$e['LdapTms'] = $res[0]['modifytimestamp'][0];
			$e['LdapID'] = $res[0]['entryuuid'][0];
		} else {
			// L'enregistrement a échoué - on récupère l'erreur
			$e['OK'] = false;
			$e['Message'] = "Erreur LDAP lors de la modification - " . @ldap_error(Server::$_LDAP);
			$e['Prop'] = '';
		}
		return $e;
	}

	/**
	 * Supprime un élément dans l'arbre
	 * @param	string	ID de l'élément à supprimer
	 * @return 	void
	 */
	static function ldapDelete($ldapID,$recursive=false) {
		if(empty($ldapID)) return;
		$res = Server::ldapGet($ldapID);
		$GLOBALS["Systeme"]->Log->log("DELETE HOST: $ldapID RECURSIVE: $recursive");
		if ($recursive == false) {
			$req = ldap_delete(Server::$_LDAP, $res[0]['dn']);
		} else {
			//searching for sub entries
			$sr = ldap_list(Server::$_LDAP, $res[0]['dn'], "ObjectClass=*", array('modifytimestamp', 'entryuuid'));
			$GLOBALS["Systeme"]->Log->log("DELETE HOST: LDAP GET LIST ",$sr);
			$info = ldap_get_entries(Server::$_LDAP, $sr);
			$GLOBALS["Systeme"]->Log->log("DELETE HOST: LDAP GET ENTRY ",$info);
			for ($i = 0; $i < $info['count']; $i++) {
				//deleting recursively sub entries
				$result = Server::ldapDelete($info[$i]['entryuuid'][0], $recursive);
				if (!$result) {
					$GLOBALS["Systeme"]->Log->log("DELETE HOST: RESULT $result");
					//return result code, if delete fails
					return ($result);
				}
			}
			return (ldap_delete(Server::$_LDAP, $res[0]['dn']));
		}
	}
	/**
	 * checkTms
	 * Verifie la date de modification de l'objet
	 * @param Object Kob-eye
	 */
	static function checkTms($KEObj) {

        $e = array('exists' => true, 'OK' => true);
		Server::ldapConnect();
		if (empty($KEObj -> LdapID)) {
		    switch (get_class($KEObj)) {
                case "Server":
                    $search = ldap_search(Server::$_LDAP, 'ou=servers,'.PARC_LDAP_BASE, 'ou=' . $KEObj->LDAPNom, array('modifytimestamp', 'entryuuid'));
                    $res = ldap_get_entries(Server::$_LDAP, $search);
                    //cette entrée existe bien dans ldap mais les informations ne sont pas correcte en bdd
                    $KEObj->LdapTms = intval($res[0]['modifytimestamp'][0])-10000;
                    $KEObj->LdapID = $res[0]['entryuuid'][0];
                    $KEObj->LdapDN = $res[0]['dn'];
                    $search2 = ldap_search(Server::$_LDAP, PARC_LDAP_BASE, 'cn=' . $KEObj->LDAPNom, array('modifytimestamp', 'entryuuid'));
                    $res2 = ldap_get_entries(Server::$_LDAP, $search2);
                    $KEObj->LdapUserTms = intval($res2[0]['modifytimestamp'][0])-10000;
                    $KEObj->LdapUserID = $res2[0]['entryuuid'][0];
                    $KEObj->LdapUserDN = $res2[0]['dn'];
                    if (!$res['count']) {
                        $e['exists'] = false;
                        return $e;
                    }
                break;
                case "Parc_Technicien":
                    $search = ldap_search(Server::$_LDAP, 'ou=users,'.PARC_LDAP_BASE, 'cn=' . $KEObj->AccesUser, array('modifytimestamp', 'entryuuid'));
                    $res = ldap_get_entries(Server::$_LDAP, $search);
                    //cette entrée existe bien dans ldap mais les informations ne sont pas correcte en bdd
                    $KEObj->LdapTms = intval($res[0]['modifytimestamp'][0])-10000;
                    $KEObj->LdapID = $res[0]['entryuuid'][0];
                    $KEObj->LdapDN = $res[0]['dn'];
                    if (!$res['count']) {
                        $e['exists'] = false;
                        return $e;
                    }
                    break;
                case "Client":
                    $search = ldap_search(Server::$_LDAP, 'ou=users,'.PARC_LDAP_BASE, 'cn=' . $KEObj->AccesUser, array('modifytimestamp', 'entryuuid'));
                    $res = ldap_get_entries(Server::$_LDAP, $search);
                    //cette entrée existe bien dans ldap mais les informations ne sont pas correcte en bdd
                    $KEObj->LdapTms = intval($res[0]['modifytimestamp'][0])-10000;
                    $KEObj->LdapID = $res[0]['entryuuid'][0];
                    $KEObj->LdapDN = $res[0]['dn'];
                    if (!$res['count']) {
                        $e['exists'] = false;
                        return $e;
                    }
                    break;
                case "Contact":
                    $search = ldap_search(Server::$_LDAP, 'ou=users,'.PARC_LDAP_BASE, 'cn=' . $KEObj->AccesUser, array('modifytimestamp', 'entryuuid'));
                    $res = ldap_get_entries(Server::$_LDAP, $search);
                    //cette entrée existe bien dans ldap mais les informations ne sont pas correcte en bdd
                    $KEObj->LdapTms = intval($res[0]['modifytimestamp'][0])-10000;
                    $KEObj->LdapID = $res[0]['entryuuid'][0];
                    $KEObj->LdapDN = $res[0]['dn'];
                    if (!$res['count']) {
                        $e['exists'] = false;
                        return $e;
                    }
                    break;
            }
        }else {
            $search = ldap_search(Server::$_LDAP, PARC_LDAP_BASE, 'entryuuid=' . $KEObj->LdapID, array('modifytimestamp'));
            $res = ldap_get_entries(Server::$_LDAP, $search);
            if (!$res['count']) {
                $e['exists'] = false;
                return $e;
            }
        }
		/*if (!empty($KEObj -> LdapTms) && intval($res[0]['modifytimestamp'][0])-10000 > intval($KEObj -> LdapTms )) {
			$e['OK'] = false;
			$e['Message'] = "Cette entrée est obsolète. Il faut faire une synchronisation avant de pouvoir la modifier.";
			$e['Prop'] = '';
		}else*/if (empty($KEObj -> LdapTms)) {
            $e['exists'] = false;
			$e['OK'] = false;
			$e['Message'] = "Cette entrée n'est pas publiée, elle doit être incomplète. Vérifiez la cohérence de l'élément.";
			$e['Prop'] = '';
            $e['OK'] = true;
		}else {
			$e['OK'] = true;
		}
		return $e;
	}

	/*******************************************************************************************************************************

	 FONCTIONS UTILES

	 ********************************************************************************************************************************/

	/**
	 * Retourne le max uid + 1
	 * @return	Prochain uid
	 */
	static function getNextUid() {
		Server::ldapConnect();
		$search = ldap_search(Server::$_LDAP, PARC_LDAP_BASE, 'objectClass=posixAccount', array('uidnumber'));
		$sort = ldap_sort(Server::$_LDAP, $search, 'uidnumber');
		$res = ldap_get_entries(Server::$_LDAP, $search);
		$uid = $res[sizeof($res) - 2]['uidnumber'][0] + 1;
		return ($uid > 1000) ? $uid : 1000;
	}

	/**
	 * Retourne le max gid + 1
	 * @return	Prochain gid
	 */
	static function getNextGid() {
		Server::ldapConnect();
		$search = ldap_search(Server::$_LDAP, PARC_LDAP_BASE, '(|(objectClass=posixAccount)(objectClass=posixGroup))', array('gidnumber'));
		$sort = ldap_sort(Server::$_LDAP, $search, 'gidnumber');
		$res = ldap_get_entries(Server::$_LDAP, $search);
		$gid = $res[sizeof($res) - 2]['gidnumber'][0] + 1;
		return ($gid > 1000) ? $gid : 1000;
	}

}
