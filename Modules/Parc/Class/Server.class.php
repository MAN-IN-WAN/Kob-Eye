<?php

class Server extends genericClass {
    //connexion ssh
    private $_connection;

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
        //installation de la clef
        if (!$this->Status&&!$first){
            //if (!$this->installSshKey()) return false;
            parent::Save();
        }
        // Forcer la vérification
        $this->Verify( $synchro );
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
    public function Verify( $synchro = false ) {
        $this->Connect();
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
                $coses[$cosTemp->get('id')]=$cosTemp->get('name');
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
                    $accStatus = $account->get('zimbraAccountStatus');
                    $cosId = $account->get('zimbraCOSId');
                    $externe = $account->get('zimbraIsExternalVirtualAccount');
                    $cos ='NULL';
                    if(isset($cosId) && $cosId != '')
                        $cos = $coses[$cosId];

                    $o = Sys::getOneData('Parc','CompteMail/IdMail='.$accId);
                    if(!is_object($o)){
                        $o = genericClass::createInstance('Parc','CompteMail');
                        $o->IdMail = $accId;
                        $report .= '<b>Nouvelle adresse trouvée</b> : '.$accName.'.<br>'.PHP_EOL;
                    }
                    $o->Adresse = $accName;
                    $o->COS = $cos;
                    $o->Nom = $userNom;
                    $o->Prenom = $userPrenom;
                    $o->Quota = floor($userQuota/1048576); //En Mo
                    $o->EspaceUtilise =floor($userUsed/1048576); //En Mo
                    $o->Status = $accStatus;
                    $o->Externe = $externe;


                    $o->addParent($this);
                    $o->addParent($kCli);

                    if(!$dryrun){
                        $o->Save(false);
                        $mAliases = $account->get('zimbraMailAlias');
                        if(!is_array($mAliases))
                            $mAliases = array($mAliases);

                        $pAliases = $o->getChildren('EmailAlias');

                        foreach($pAliases as $pAlias){
                            if(!in_array($pAlias->TargetMail,$mAliases)){
                                echo '**************************************** Suppression Alias :'.$pAlias->TargetMail.PHP_EOL;
                                $pAlias->Delete(false);
                            }
                        }
                        foreach($mAliases as $mAlias){
                            foreach($pAliases as $pAlias){
                                if($mAlias == $pAlias->TargetMail){
                                    continue 2;
                                }
                            }
                            echo '**************************************** Ajout Alias :'.$mAlias.PHP_EOL;
                            $a = genericClass::createInstance('Parc','EmailAlias');
                            $a->TargetMail = $mAlias;
                            $a->addParent($o);
                            $a->Save(false);
                        }
                    }
                }
            }
        } catch (Exception $e){
            $report .= print_r ($e,true);
        }

         return $report;
    }

    public function getDiffs($dryrun=false) {
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

                $diffList = $zimbra->getDistributionLists($dname);
                foreach($diffList as $diff){


                    $diffId = $diff->get('id');
                    $diffName = $diff->get('name');

                    $o = Sys::getOneData('Parc','ListeDiffusion/IdDiffusion='.$diffId);
                    if(!is_object($o)){
                        $o = genericClass::createInstance('Parc','ListeDiffusion');
                        $o->IdDiffusion = $diffId;
                        $report .= '<b>Nouvelle liste de diffusion trouvée</b> : '.$diffName.'.<br>'.PHP_EOL;
                    }

                    $o->Nom = $diffName;

                    $o->addParent($this);
                    $o->addParent($kCli);

                    if(!$dryrun){
                        $o->Save(false);

                        foreach($diff->getMembers() as $memb) {
                            $report .= '------- synchronisation du membre : "'.$memb.': .<br>'.PHP_EOL;
                            try {
                                $temp = $zimbra->getAccount($dname, 'name', $memb);
                                $id = $temp->get('id');
                                $compte = Sys::getOneData('Parc', 'CompteMail/IdMail=' . $id);
                                if ($compte) {
                                    $compte->addParent($o);
                                    $compte->Save(false);
                                } else{
                                    $report .= 'ERREUR :  Compte introuvable sur le proxy ( Tentez un synchronisation des comptes mails et réessayez. )'.PHP_EOL;
                                }
                            }catch (Exception $e){
                                $report .= 'ERREUR :  Compte introuvable sur le serveur de mail ( Probablement une liste de diffusion. )'.PHP_EOL;
                                //$report .= print_r ($e,true);
                            }
                        }
                    }

                    if(count($o->Error)){
                        $report .= '************************* ERRORS ********************************';
                        $report .= print_r($o->Error, true);
                        $report .= '*****************************************************************';
                    }


                }
            }
        } catch (Exception $e){
            $report .= print_r ($e,true);
        }

        return $report;
    }

    public function getRessources($dryrun=false) {
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

//            $cosesTemp = $zimbra->getAllCos();
//            $coses = array();
//            foreach ($cosesTemp as $cosTemp){
//                $coses[$cosTemp->get('id')]=$cosTemp->get('name');
//            }



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

                $ressList = $zimbra->getAllRessources($dname);
                foreach($ressList as $ress){


                    $accHost = $ress->get('zimbraMailHost');
                    if($accHost != $this->DNSNom){
                        continue;
                    }
                    $accId = $ress->get('id');
                    $accName = $ress->get('name');
                    $accStatus = $ress->get('zimbraAccountStatus');
                    $accType = $ress->get('zimbraCalResType');
                    $accDisplay = $ress->get('displayName');
//                    $cosId = $ress->get('zimbraCOSId');
//                    $cos ='NULL';
//                    if(isset($cosId) && $cosId != '')
//                        $cos = $coses[$cosId];

                    $o = Sys::getOneData('Parc','EmailRessource/IdMail='.$accId);
                    if(!is_object($o)){
                        $o = genericClass::createInstance('Parc','EmailRessource');
                        $o->IdMail = $accId;
                        $report .= '<b>Nouvelle ressource trouvée</b> : '.$accName.'.<br>'.PHP_EOL;
                    }
                    $o->Adresse = $accName;
                    //$o->COS = $cos;
                    $o->Status = $accStatus;
                    $o->Type = $accType;
                    $o->Nom = $accDisplay;


                    $o->addParent($this);
                    $o->addParent($kCli);

                    if(!$dryrun){
                        $o->Save(false);
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
		/*if (!is_null(Server::$_LDAP))
			return Server::$_LDAP;*/
		Server::$_LDAP = ldap_connect(PARC_LDAP_IP);
		if (Server::$_LDAP) {
			ldap_set_option(Server::$_LDAP, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option(Server::$_LDAP, LDAP_OPT_SIZELIMIT,10000);
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
        try {
    		$connect = Server::ldapConnect();
            $req = ldap_add(Server::$_LDAP, $dn, $entry);
        }catch (Throwable $e){
		    return array('Message' => $e->getMessage());
        }
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
			@ldap_get_option(Server::$_LDAP, LDAP_OPT_DIAGNOSTIC_MESSAGE, $err);
			$e['Message'] = "Erreur LDAP lors de la modification - " . @ldap_error(Server::$_LDAP) .' - '.$err;
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
		    if (!isset($res[0]))return false;
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
	static function checkTms($KEObj,$KEServer=false,$dn='',$filter='') {

        $e = array('exists' => true, 'OK' => true);
		Server::ldapConnect();
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
            case "Host":
                $search = ldap_search(Server::$_LDAP, $dn,$filter, array('modifytimestamp', 'entryuuid'));
                $res = ldap_get_entries(Server::$_LDAP, $search);
                //cette entrée existe bien dans ldap mais les informations ne sont pas correcte en bdd
                //$KEObj->LdapTms = intval($res[0]['modifytimestamp'][0])-10000;
                $KEObj->setLdapTms($KEServer,intval($res[0]['modifytimestamp'][0])-10000);
                //$KEObj->LdapID = $res[0]['entryuuid'][0];
                $KEObj->setLdapID($KEServer,$res[0]['entryuuid'][0]);
                //$KEObj->LdapDN = $res[0]['dn'];
                $KEObj->setLdapDN($KEServer,$res[0]['dn']);
                if (!$res['count']) {
                    $e['exists'] = false;
                    return $e;
                }else{
                    $e['exists'] = true;
                }
                break;
            case "Apache":
                $search = ldap_search(Server::$_LDAP, $dn,$filter, array('modifytimestamp', 'entryuuid'));
                $res = ldap_get_entries(Server::$_LDAP, $search);
                //cette entrée existe bien dans ldap mais les informations ne sont pas correcte en bdd
                //$KEObj->LdapTms = intval($res[0]['modifytimestamp'][0])-10000;
                $KEObj->setLdapTms($KEServer,intval($res[0]['modifytimestamp'][0])-10000);
                //$KEObj->LdapID = $res[0]['entryuuid'][0];
                $KEObj->setLdapID($KEServer,$res[0]['entryuuid'][0]);
                //$KEObj->LdapDN = $res[0]['dn'];
                $KEObj->setLdapDN($KEServer,$res[0]['dn']);
                if (!$res['count']) {
                    $e['exists'] = false;
                    return $e;
                }else{
                    $e['exists'] = true;
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
                if (!$res['count']) {
                    $e['exists'] = false;
                    return $e;
                }else{
                    $KEObj->LdapTms = intval($res[0]['modifytimestamp'][0])-10000;
                    $KEObj->LdapID = $res[0]['entryuuid'][0];
                    $KEObj->LdapDN = $res[0]['dn'];
                }
                break;
            case "Contact":
                $search = ldap_search(Server::$_LDAP, 'ou=users,'.PARC_LDAP_BASE, 'cn=' . $KEObj->AccesUser, array('modifytimestamp', 'entryuuid'));
                $res = ldap_get_entries(Server::$_LDAP, $search);
                //cette entrée existe bien dans ldap mais les informations ne sont pas correcte en bdd
                if (!$res['count']) {
                    $e['exists'] = false;
                    return $e;
                }else{
                    $KEObj->LdapTms = intval($res[0]['modifytimestamp'][0])-10000;
                    $KEObj->LdapID = $res[0]['entryuuid'][0];
                    $KEObj->LdapDN = $res[0]['dn'];
                }
                break;
            case "Domain":
                $search = ldap_search(Server::$_LDAP, 'ou=domains,'.PARC_LDAP_BASE, 'cn=' . $KEObj->Url, array('modifytimestamp', 'entryuuid'));
                $res = ldap_get_entries(Server::$_LDAP, $search);
                //cette entrée existe bien dans ldap mais les informations ne sont pas correcte en bdd
                if (!$res['count']) {
                    $e['exists'] = false;
                    return $e;
                }else{
                    $KEObj->LdapTms = intval($res[0]['modifytimestamp'][0])-10000;
                    $KEObj->LdapID = $res[0]['entryuuid'][0];
                    $KEObj->LdapDN = $res[0]['dn'];
                }
                break;
            case "Subdomain":
                $search = ldap_search(Server::$_LDAP, $dn, 'cn=' . $KEObj->Nom, array('modifytimestamp', 'entryuuid'));
                $res = ldap_get_entries(Server::$_LDAP, $search);
                //cette entrée existe bien dans ldap mais les informations ne sont pas correcte en bdd
                if (!$res['count']) {
                    $e['exists'] = false;
                    return $e;
                }else{
                    $KEObj->LdapTms = intval($res[0]['modifytimestamp'][0])-10000;
                    $KEObj->LdapID = $res[0]['entryuuid'][0];
                    $KEObj->LdapDN = $res[0]['dn'];
                }
                break;
            case "MX":
                $search = ldap_search(Server::$_LDAP, $dn, 'cn=' . $KEObj->Nom, array('modifytimestamp', 'entryuuid'));
                $res = ldap_get_entries(Server::$_LDAP, $search);
                //cette entrée existe bien dans ldap mais les informations ne sont pas correcte en bdd
                if (!$res['count']) {
                    $e['exists'] = false;
                    return $e;
                }else{
                    $KEObj->LdapTms = intval($res[0]['modifytimestamp'][0])-10000;
                    $KEObj->LdapID = $res[0]['entryuuid'][0];
                    $KEObj->LdapDN = $res[0]['dn'];
                }
                break;
            case "TXT":
                $search = ldap_search(Server::$_LDAP, $dn, 'cn=' . $KEObj->Nom, array('modifytimestamp', 'entryuuid'));
                $res = ldap_get_entries(Server::$_LDAP, $search);
                //cette entrée existe bien dans ldap mais les informations ne sont pas correcte en bdd
                if (!$res['count']) {
                    $e['exists'] = false;
                    return $e;
                }else{
                    $KEObj->LdapTms = intval($res[0]['modifytimestamp'][0])-10000;
                    $KEObj->LdapID = $res[0]['entryuuid'][0];
                    $KEObj->LdapDN = $res[0]['dn'];
                }
                break;
            default:
                if ($KEServer){
                    $search = ldap_search(Server::$_LDAP, PARC_LDAP_BASE, 'entryuuid=' . $KEObj->getLdapID($KEServer), array('modifytimestamp'));
                }else {
                    $search = ldap_search(Server::$_LDAP, PARC_LDAP_BASE, 'entryuuid=' . $KEObj->LdapID, array('modifytimestamp'));
                }
                $res = ldap_get_entries(Server::$_LDAP, $search);
                if (!$res['count']) {
                    $e = array('exists' => false, 'OK' => true);
                    return $e;
                }
                break;
        }
		/*if (!empty($KEObj -> LdapTms) && intval($res[0]['modifytimestamp'][0])-10000 > intval($KEObj -> LdapTms )) {
			$e['OK'] = false;
			$e['Message'] = "Cette entrée est obsolète. Il faut faire une synchronisation avant de pouvoir la modifier.";
			$e['Prop'] = '';
		}else*/
        if ($KEServer) {
            if (!$KEObj->getLdapTms($KEServer)) {
                $e['exists'] = false;
                $e['OK'] = false;
                $e['Message'] = "Cette entrée n'est pas publiée, elle doit être incomplète. Vérifiez la cohérence de l'élément.";
                $e['Prop'] = '';
                $e['OK'] = true;
            } else {
                $e['OK'] = true;
            }
        }else {
            if (empty($KEObj->LdapTms)) {
                $e['exists'] = false;
                $e['OK'] = false;
                $e['Message'] = "Cette entrée n'est pas publiée, elle doit être incomplète. Vérifiez la cohérence de l'élément.";
                $e['Prop'] = '';
                $e['OK'] = true;
            } else {
                $e['OK'] = true;
            }
        }
		return $e;
	}

	/*******************************************************************************************************************************

	 FONCTIONS UTILES

	 ********************************************************************************************************************************/
    /**
     * sort_ldap_entries
     * Fonction de tri des résultats ldap
     * @param $e results
     * @param $fld field
     * @param $order A or D
     * @param $as_int traiter en tant qu'entier
     * @return mixed
     */
    static function sort_ldap_entries($e, $fld, $order,$as_int=false){
        for ($i = 0; $i < $e['count']; $i++) {
            for ($j = $i; $j < $e['count']; $j++) {
                if (!$as_int)
                    $d = strcasecmp($e[$i][$fld][0], $e[$j][$fld][0]);
                else
                    $d = intval($e[$i][$fld][0])>intval($e[$j][$fld][0]);
                switch ($order) {
                    case 'A':
                        if ($d > 0)
                            Server::swap($e, $i, $j);
                        break;
                    case 'D':
                        if ($d < 0)
                            Server::swap($e, $i, $j);
                        break;
                }
            }
        }
        return ($e);
    }

    /**
     * swap
     * fonction accessoire de tri ldap
     * @param $ary
     * @param $i
     * @param $j
     */
    static function swap(&$ary, $i, $j){
        $temp = $ary[$i];
        $ary[$i] = $ary[$j];
        $ary[$j] = $temp;
    }
    /**
	 * Retourne le max uid + 1
	 * @return	Prochain uid
	 */
	static function getNextUid() {
		Server::ldapConnect();
		$search = ldap_search(Server::$_LDAP, PARC_LDAP_BASE, 'objectClass=posixAccount', array('uidnumber'));
		$res = ldap_get_entries(Server::$_LDAP, $search);
		$res = Server::sort_ldap_entries($res,'uidnumber','A',true);
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
		$res = ldap_get_entries(Server::$_LDAP, $search);
        $res = Server::sort_ldap_entries($res,'gidnumber','A',true);
		$gid = $res[sizeof($res) - 2]['gidnumber'][0] + 1;
		return ($gid > 1000) ? $gid : 1000;
	}

    /**
     * Fonction de connexion
     * @return bool|resource
     */
    public function Connect() {
        if (!function_exists('ssh2_connect')){
            $this->AddError(array("Message"=>"Librairie PECL/SSH2 non disponible... Veuillez l'sintaller avec la commande 'yum install libssh2-devel && pecl install ssh2'."));
            return false;
        }
        //test connectivite ssh
        try {
            //Test de connectivité pour ne pas bloquer l'appli
            $connection = false;
            if(!empty($this->InternalIP)) {
                $tc = @fsockopen($this->InternalIP, $this->Port, $errno, $errstr, 0.5);
                if ($tc !== false) {
                    fclose($tc);
                    $connection = ssh2_connect($this->InternalIP, $this->Port);
                }
            }
            if(!$connection && !empty($this->IP)) {
                $tc = @fsockopen($this->IP, $this->Port, $errno, $errstr, 0.5);
                if ($tc !== false) {
                    fclose($tc);
                    $connection = ssh2_connect($this->IP, $this->Port);
                }
            }

            if (!$connection){
                $this->addError(array("Message"=>"Impossible de contacter l'hôte ".$this->InternalIP));
                $this->Status = false;
                parent::Save();
                return false;
            }
            if (!$this->Status) {
                if (!ssh2_auth_password($connection, $this->SshUser, $this->SshPassword)) {
                    $this->addError(array("Message" => "Impossible de s'authentifier sur l'hôte " . $this->InternalIP . ". Veuillez vérifier vos identifiants."));
                    $this->Status = false;
                    parent::Save();
                    return false;
                }else{
                    $this->addWarning(array("Message" => "Connexion avec identifiant /mot de passe. Veuillez générer les clefs publique /privées."));
                }
            }else{
                //connexion avec clef ssh
//                if (!ssh2_auth_pubkey_file($connection,$this->Login,$this->PublicKey,$this->PrivateKey)){
                if (!ssh2_auth_pubkey_file($connection,$this->SshUser,'.ssh/id_'.$this->InternalIP.'.pub','.ssh/id_'.$this->InternalIP)){
                    $this->Status = false;
                    parent::Save();
                    $this->addError(array("Message" => "Impossible de s'authentifier sur l'hôte " . $this->InternalIP . ". Veuillez vérifier vos clefs publique / privée ou les regénérer."));
                    return false;
                }else{
                    if (!$this->Status) {
                        $this->Status = true;
                        parent::Save();
                    }
                    $this->addSuccess(array("Message" => "Connexion réussie avec les clefs publique / privée ."));
                }
            }
            /*$stream1= ssh2_exec($connection, trim("hostname")."\n");*/
            //stream_set_blocking($stream1, true);
            $this->_connection= $connection;
            return $connection;
        }catch (Exception $e){
            $this->Status = false;
            parent::Save();
            $this->addError(array("Message"=>"Une erreur interne s'est produite lors de la tentative de connexion à l'hôte ".$this->InternalIP));
            return false;
        }
        return true;
    }

    /**
     * Focntion de déconnexion
     * @return bool
     */
    public function Disconnect() {
        if ($this->_connection) {
            ssh2_exec($this->_connection, 'exit');
            $this->_connection = null;
        }
        return true;
    }
    /**
     * Insttalation des clefs ssh
     * @return bool
     */
    public function installSshKey() {
        //connexion par login/pass
        if (!$this->_connection)$this->Connect();
        //génération des clefs publiques / privées
        try {
            Parc::localExec("if [ ! -d '.ssh' ]; then mkdir .ssh; fi && cd .ssh && rm -f id_". $this->InternalIP."* && /usr/bin/ssh-keygen  -N \"\" -f id_" . $this->InternalIP);
            //récupération et stockage des clefs
            $stream2 =  Parc::localExec("cd .ssh && cat id_" . $this->InternalIP);
            $this->PrivateKey = $stream2;
            $stream2 =  Parc::localExec("cd .ssh && cat id_" . $this->InternalIP . ".pub");
            $this->PublicKey = $stream2;
            //publication de la clef
            $stream3 = $this->remoteExec("[ -d /root/.ssh ] || mkdir /root/.ssh");
            $stream3 = $this->remoteExec("echo '".$this->PublicKey."' >>/root/.ssh/authorized_keys");
        }catch (Exception $e){
            $this->addError(array("Message"=>"Une erreur interne s'est produite lors de la tentative de création des clefs SSH. Détails: ".$e->getMessage()));
            $this->Status = false;
            parent::Save();
            return false;
        }
        //tout initialisé
        $this->Status = true;
        parent::Save();
        return true;
    }

    /**
     * execution distante sur le serveur
     * @param $command
     * @param null $activity
     * @param bool $noerror
     * @return mixed
     */
    public function remoteExec( $command ,$activity = null,$noerror=false){
        if (!$this->_connection)$this->Connect();
        $result = $this->rawExec( $command.';echo -en "\n$?"', $activity);
        if(!$noerror&& ! preg_match( "/^(0|-?[1-9][0-9]*)$/s", $result[2], $matches ) ) {
            throw new RuntimeException( "Le retour de la commande ne contenait pas le status. commande : ".$command );
        }
        if( !$noerror&&$matches[1] !== "0" ) {
            throw new RuntimeException( $result[1].$result[0], (int)$matches[1] );
        }
        //return $result[0].$result[3];
        return print_r($result,true);
    }

    /**
     * Copie de fichiers
     * @param $file
     * @return bool
     */
    public function copyFile( $file ){
        if ($this->_connection){
            $this->Disconnect();
        }
        if (!$this->_connection)$this->Connect();
        //$result = ssh2_scp_send($this->_connection,$file,'/'.$file, 0644);
        // Create SFTP session
        $sftp = ssh2_sftp($this->_connection);
        $sftpStream = @fopen('ssh2.sftp://'.$sftp.'/'.$file, 'w');
        try {
            if (!$sftpStream) {
                throw new Exception("Could not open remote file: /$file");
            }
            $data_to_send = @file_get_contents($file);
            if ($data_to_send === false) {
                throw new Exception("Could not open local file: $file.");
            }
            if (@fwrite($sftpStream, $data_to_send) === false) {
                throw new Exception("Could not send data from file: $file.");
            }
            fclose($sftpStream);

        } catch (Exception $e) {
            error_log('Exception: ' . $e->getMessage());
            fclose($sftpStream);
        }
        $this->Disconnect();
        return true;//$result;
    }

    /**
     * Fonction complementaire de l'execution ssh.
     * @param $command
     * @param null $activity
     * @return array
     */
    private function rawExec( $command,$activity=null ){
        $stream = ssh2_exec( $this->_connection, $command );
        $error_stream = ssh2_fetch_stream( $stream, SSH2_STREAM_STDERR );
        $dio_stream = ssh2_fetch_stream($stream, SSH2_STREAM_STDDIO);
        stream_set_blocking( $stream, TRUE );
        stream_set_blocking( $error_stream, TRUE );
        stream_set_blocking( $dio_stream, TRUE );
        $data='';
        while ($buf = fread($stream, 4096)) {
            //tentative de récupération de la progression
            if (preg_match('# ([0-9]{1,2})% #',$buf,$out)&&$activity) {
                $progress = $out[1];
                $activity->setProgression($progress);
            }
            $data.=$buf;
        }
        $output = $data;//substr($data,strlen($data)-1,1);//stream_get_contents( $stream );
        $error_output = '';
        while ($buf = fread($error_stream, 4096)) {
            $error_output.=$buf;
        }
        $dio_output = '';
        while ($buf = fread($dio_stream, 4096)) {
            $dio_output.=$buf;
        }
        /*$error_output = stream_get_contents( $error_stream );
        $dio_output = stream_get_contents( $dio_stream );*/
        //alors récupération sur le dernier caractère
        $exit_output = 0;
        if (preg_match('/^(.*)\n(0|-?[1-9][0-9]*)$/s',$output,$out)) {
            $output = $out[1];
            $exit_output = $out[2];
        }
        fclose( $stream );
        fclose( $error_stream );
        fclose( $dio_stream );
        return array( $output, $error_output,$exit_output,$dio_output);
    }
    /**
     * createTaskLdap2Service
     * create task for async execution
     */
    public function createTaskLdap2Service() {
        $nb = Sys::getOneData('Systeme','Tache/TaskModule=Parc&TaskObject=Server&TaskId='.$this->Id.'&TaskFunction=callLdap2service&Termine=0&Erreur=0');
        if ($nb) {
            $nb->DateDebut = time()+5;
            $nb->Save();
            return true;

        }

        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Raifraichissement des configuration (ldap2service) ' . $this->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Server';
        $task->TaskId = $this->Id;
        $task->TaskFunction = 'callLdap2service';
        $task->DateDebut = time()+10;
        $task->Save();
        return true;
    }

    /**
     * callLdap2Service
     * execute remotely ldap2service
     */
    public function callLdap2Service($task=null,$retry=false) {
        if (!$task) {
            $task = genericClass::createInstance('Systeme', 'Tache');
            $task->Type = 'Fonction';
            $task->Nom = 'Raifraichissement des configuration (ldap2service) ' . $this->Nom;
            $task->TaskModule = 'Parc';
            $task->TaskObject = 'Server';
            $task->TaskId = $this->Id;
            $task->TaskFunction = 'callLdap2service';
            $task->Demarre = true;
            $task->Save();
        }
        $act = $task->createActivity('Execution de synchronisation');
        try {
            $out = $this->remoteExec('/usr/bin/ldap2service', $act);
        }catch (Exception $e){
            $act->addDetails($e->getMessage());
            //si erreur il faut vérfiier le fichier de date
            $act->Terminate(false);
            return false;
        }
        //on vérifie quand mêmle la date
        $ct = $this->getFileContent('/etc/ldap2service/ldap2service.time');
        $ct = trim($ct);
        if (empty($ct)&&!$retry){
            //alors on pousse une valeur
            $this->putFileContent('/etc/ldap2service/ldap2service.time',date('YmdHis',time()-60));
            $this->callLdap2Service($task,true);
        }

        $act->addDetails($out);
        $act->Terminate($out);
        $task->Termine = true;
        $task->Save();
        return true;
    }
    /**
     * clearCache
     * clear Nginx cache
     */
    public function clearCache($task) {
        $act = $task->createActivity('Vidage du cache');
        try {
            $out = $this->remoteExec('rm /tmp/nginx/* -Rf', $act);
        }catch (Exception $e){
            $act->addDetails($e->getMessage());
            $act->Terminate(false);
            return;
        }
        $act->addDetails($out);
        $act->Terminate(true);
        $this->restartServiceNginx($task);
        return true;
    }
    /**
     * clearCache
     * clear Nginx cache
     */
    public function restartServiceNginx($task) {
        $act = $task->createActivity('Redemarrage du service Proxy (NGINX)');
        try {
            $out = $this->remoteExec('/usr/sbin/nginx -s reload', $act);
        }catch (Exception $e){
            $act->addDetails($e->getMessage());
            $act->Terminate(false);
            return;
        }
        $act->addDetails($out);
        $act->Terminate(true);
        return true;
    }
    /**
     * folderExists
     * Check if a folder exists
     */
    public function folderExists($path){
        try {
            $out = $this->remoteExec('if [ -d '.$path.' ]; then echo 1; else echo 0; fi');
            if (intval($out)>0){
                return true;
            }else{
                return false;
            }
        }catch (Exception $e){
            return false;
        }
    }
    /**
     * fileExists
     * Check if a file exists
     */
    public function fileExists($path){
        try {
            $out = $this->remoteExec('if [ -f '.$path.' ]; then echo 1; else echo 0; fi');
            if (intval($out)>0){
                return true;
            }else{
                return false;
            }
        }catch (Exception $e){
            return false;
        }
    }
    /**
     * createFolder
     * create a folder
     */
    public function createFolder($path,$usr = null,$rights='705'){
        try {
            $cmd = 'mkdir -p '.$path.' && chmod '.$rights.' '.$path;
            if ($usr){
                $cmd.=' && chown '.$usr.':users '.$path;
            }
            $out = $this->remoteExec($cmd);
            if (intval($out)>0){
                return true;
            }else{
                return false;
            }
        }catch (Exception $e){
            return false;
        }
    }
    /**
     * getFileContent
     * Return file content
     */
    public function getFileContent($path){
        try {
//            $out = $this->remoteExec('cat '.$path.' ');
            if (!$this->_connection)$this->Connect();
            //créatio nd'un fichier temporaire
            $tmpfile = 'Data/'.microtime(true).'.tmp';
            //$cmd = 'echo  \''.base64_encode($content).'\' > | base64 --decode '.$path;
            //$out = $this->remoteExec($cmd);
            $out = ssh2_scp_recv($this->_connection,$path,$tmpfile);
            $out = file_get_contents($tmpfile);
            unlink($tmpfile);
            if (!empty($out)){
                return $out;
            }else{
                return false;
            }
        }catch (Exception $e){
            $this->addError(array('Message'=>'ERROR: '.$e->getMessage()));
            return false;
        }
    }
    /**
     * putFileContent
     * Inject file content
     */
    public function putFileContent($path,$content){
        try {
            if (!$this->_connection)$this->Connect();
            //créatio nd'un fichier temporaire
            $tmpfile = 'Data/'.microtime().'.tmp';
            file_put_contents($tmpfile,$content);
            //$cmd = 'echo  \''.base64_encode($content).'\' > | base64 --decode '.$path;
            //$out = $this->remoteExec($cmd);
            $out = ssh2_scp_send($this->_connection,$tmpfile,$path);
            unlink($tmpfile);
            if (!empty($out)){
                return $out;
            }else{
                return false;
            }
        }catch (Exception $e){
            return false;
        }
    }
    /**
     * createRestartTask
     * Creation de la tache de redemarrage des services
     */
    public static function createRestartProxyTask($infra = null) {
        $pref = '';
        if($infra)
            $pref = 'Infra/'.$infra->Id.'/';

        $pxs = Sys::getData('Parc',$pref.'Server/Proxy=1');
        foreach ($pxs as $px){
            //on vérifie d'abord qu'il n'y en a pas une à venir.
            $nb = Sys::getCount('Systeme','Tache/TaskModule=Parc&TaskObject=Server&TaskId='.$px->Id.'&TaskFunction=restartServiceNginx&Demarre=0');
            if (!$nb) {
                $task = genericClass::createInstance('Systeme', 'Tache');
                $task->Type = 'Fonction';
                $task->Nom = 'Redemarrage des service du proxy ' . $px->Nom;
                $task->TaskModule = 'Parc';
                $task->TaskObject = 'Server';
                $task->TaskId = $px->Id;
                $task->TaskFunction = 'restartServiceNginx';
                $task->DateDebut = time()+60;
                $task->Save();
            }
        }
    }
    /**
     * emptyProxyCacheTask
     * Creation de la tache pour vider le cache d'un hote virtuel
     * @params $apache
     * @params $infra
     */
    public static function emptyProxyCacheTask($apache, $infra = null) {
        $pref = '';
        if($infra)
            $pref = 'Infra/'.$infra->Id.'/';

        $pxs = Sys::getData('Parc',$pref.'Server/Proxy=1',0,100,'','','','',true);
        foreach ($pxs as $px){
            $task = genericClass::createInstance('Systeme', 'Tache');
            $task->Type = 'Fonction';
            $task->Nom = 'Suppression du cache proxy ' . $px->Nom.' pour l\'hote virtuel ' . $apache->ApacheServerName;
            $task->TaskModule = 'Parc';
            $task->TaskObject = 'Server';
            $task->TaskId = $px->Id;
            $task->TaskType = 'maintenance';
            $task->TaskArgs = serialize(array('Apache'=>$apache->ApacheServerName,'ApacheSsl'=>$apache->Ssl));
            $task->TaskFunction = 'emptyProxyCache';
            $task->addParent($apache);
            if ($host = $apache->getOneParent('Host')) {
                $task->addParent($host);
                if ($inst = $host->getOneChild('Instance')){
                    $task->addParent($inst);
                }
            }
            $task->DateDebut = time();
            $task->Save();
        }
    }

    /**
     * emptyProxyCache
     * Vider le cache d'un hote virtuel
     * @params $apache
     * @params $infra
     */
    public function emptyProxyCache($task) {
        $params = unserialize($task->TaskArgs);
        $act = $task->createActivity('Suppression du dossier cache du proxy '.$this->Nom.' pour le\'hote virtuel '.$params['Apache']);
        if (!isset($params['Apache'])||empty($params['Apache'])){
            $act->addDetails('Paramètre Apache introuvable.');
            $act->Terminate(false);
            return false;
        }
        try {
            $act->addDetails('rm -Rf /tmp/nginx/'.$params['Apache']);
            $out = $this->remoteExec('rm -Rf /tmp/nginx/'.$params['Apache'], $act);
            $act->addDetails($out);
            $act->Terminate(true);
            if ($params['ApacheSsl']){
                $act = $task->createActivity('Suppression du dossier cache du proxy '.$this->Nom.' pour le\'hote virtuel '.$params['Apache'].'.ssl');
                $act->addDetails('rm -Rf /tmp/nginx/'.$params['Apache'].'.ssl');
                $out = $this->remoteExec('rm -Rf /tmp/nginx/'.$params['Apache'].'.ssl', $act);
                $act->addDetails($out);
                $act->Terminate(true);
            }
        }catch (Exception $e){
            $act->addDetails($e->getMessage());
            $act->Terminate(false);
            return false;
        }
        return true;
    }
    /**
     * getServer
     * Retourne un serveur depuis son id
     * @param Id
     */
    public static function getServer($id){
        return Sys::getOneData('Parc','Server/'.$id,0,1,'','','','',true);
    }
    /**
     * createConfigTask
     * Création d'une tache de configuration du serveur
     */
    public function createConfigTask(){
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Configuratio du serveur ' . $this->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'Server';
        $task->TaskId = $this->Id;
        $task->TaskType = 'installation';
        $task->TaskFunction = 'config';
        $task->addParent($this);
        $task->DateDebut = time();
        $task->Save();
        return array('task'=> $task);
    }
    /**
     * config
     * Configure un serveur
     */
    public function config($task) {
        //récupératio du modèle
        $modele = $this->getOneParent('ServerProfile');
        $act = $task->createActivity('Lancement de la configuratio du serveur à partir du modele '.$modele->Nom);
        $act->Terminate(true);
        //chargement des variables twig
        $vars = array();
        $vars['server'] = $this;
        $twig = clone KeTwig::$Twig;
        $twig->setLoader(new \Twig_Loader_String());
        try{
            //Execution de chaque tache du profil
            $servertasks = $modele->getChildren('ServerTask');
            foreach ($servertasks as $st){
                $act = $task->createActivity('Exécution de la commande:  '.$st->Nom);
                $command = $st->Command;
                $act->addDetails($command);
                //execution twig
                $rendered = $twig->render($command,$vars);
                $act->addDetails($command);
                $out = $this->remoteExec($command);
                $act->addDetails($out);
                $act->Terminate(true);
            }
        }catch (Exception $e){
            $act->addDetails($e->getMessage());
            $act->Terminate(false);
            throw new Exception ($e->getMessage());
            return false;
        }
        return true;
    }
}
