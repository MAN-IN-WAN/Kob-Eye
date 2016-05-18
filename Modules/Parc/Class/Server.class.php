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

		// Synchro des éléments indépendants
		$this -> synchroPartielle('clients');
		$this -> synchroPartielle('domains');
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
				$KEObj = $this -> getKEObject($type, $data[$i], $this -> $assocVar);

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
			$obj -> $key = @implode("\r", $entry[$field]);
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
			$e['Message'] = "Erreur LDAP lors de l'ajout - " . @ldap_error(Server::$_LDAP);
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
			$e['Message'] = "Erreur LDAP lors du déplacement - " . @ldap_error(Server::$_LDAP);
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
		$connect = Server::ldapConnect();
		$search = ldap_search(Server::$_LDAP, PARC_LDAP_BASE, 'entryuuid=' . $KEObj -> LdapID, array('modifytimestamp'));
		$res = ldap_get_entries(Server::$_LDAP, $search);
		if (!$res['count']) {
			$e['exists'] = false;
			return $e;
		}
		/*if (!empty($KEObj -> LdapTms) && intval($res[0]['modifytimestamp'][0])-10000 > intval($KEObj -> LdapTms )) {
			$e['OK'] = false;
			$e['Message'] = "Cette entrée est obsolète. Il faut faire une synchronisation avant de pouvoir la modifier.";
			$e['Prop'] = '';
		}else*/if (empty($KEObj -> LdapTms)) {
			$e['OK'] = false;
			$e['Message'] = "Cette entrée n'est pas publiée, elle doit être incomplète. Vérifiez la cohérence de l'élément.";
			$e['Prop'] = '';
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
