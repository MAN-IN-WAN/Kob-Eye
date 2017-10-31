<?php

class Apache extends genericClass {
	var $_KEHost;
	var $_KEServer;
	var $_isVerified = false;
	/**
	 * Force la vérification avant enregistrement
	 * @param	boolean	Enregistrer aussi sur LDAP
	 * @return	void
	 */
	public function Save( $synchro = true ) {
		if ($this->Ssl){
			//test de l'activation ssl
			$old = Sys::getOneData('Parc','Apache/'.$this->Id);
			if (!$old->Ssl) {
			    if (!$this->enableSsl()) return false;
            }
		}
		// Forcer la vérification
		if(!$this->_isVerified) $this->Verify( $synchro );
		// Enregistrement si pas d'erreur
		if($this->_isVerified) parent::Save();
		return true;
	}

	public function enableSsl($force = false) {
	    $this->Ssl = false;
		if (empty($this->SslMethod))$this->SslMethod = "Letsencrypt";
		//check already exists
		if (!$force&&$this->Ssl&&!empty($this->SslCertificate)&&!empty($this->SslCertificateKey)&&$this->SslExpiration>time()+2592000){
			$this->addError(array("Message"=>"Le certificat est déjà généré et valide."));
			return false;
		}
		//on vérifie qu'il n'y ait pas déjà une tache
        if (Sys::getCount('Parc','Apache/'.$this->Id.'/Tache/Termine=0')) return false;

		switch($this->SslMethod){
            case "Manuel":
                if (!$force&&$this->Ssl&&(empty($this->SslCertificate)||empty($this->SslCertificateKey))){
                    $this->addError(array("Message"=>"Pour activer SSL il faut que la clef et le certificat soient renseignés"));
                    return false;
                }
                //check validity before enabling
                $key  = openssl_x509_check_private_key($this->SslCertificate,$this->SslCertificateKey);
                if (!$key){
                    $this->addError(array("Message"=>"Le certificat et la clef ne correspondent pas."));
                    return false;
                }
                $this->Ssl = true;
                break;
			case "Letsencrypt":
                require_once 'Net/DNS2.php';
                if (!class_exists('Net_DNS2_Resolver')){
                    $this->addError(array("Message"=>"La librairie Net_DNS2 n'est pas disponible. Veuillez l'installer avec la commande suivante: 'pear install NET/DNS2'"));
                    return false;
                }
                //définition de la date d'expiration
                $this->SslExpiration=time()+(86400*90);
                $this->Ssl = true;
                $serv = $this->getKEServer();
                $sa = explode("\n",$this->ApacheServerAlias);

                //test des entrées dns
                $resolver = new Net_DNS2_Resolver( array('nameservers' => array('8.8.8.8')) );
                $t = array($resolver->query($this->ApacheServerName, 'A'));
                foreach ($sa as $a){
                    $a = trim($a);
                    if (!empty($a))
                        $t = array_merge($t,array($resolver->query($a, 'A')));
                }
                //test des erreurs
                $err = false;
                foreach ($t as $dns){
                    if (!sizeof($dns->answer[sizeof($dns->answer)-1])){
                        $err = true;
                        $this->addError(array("Message"=>"Le domaine : '".$dns->question[sizeof($dns->question)-1]->qname."' ne pointe pas sur l'adresse ip ".$serv->IP." (actuellement il n'est pas configuré)"));
                    }elseif ($dns->answer[sizeof($dns->answer)-1]->address!=$serv->IP){
                        $err = true;
                        $this->addError(array("Message"=>"Le domaine : '".$dns->question[sizeof($dns->question)-1]->qname."' ne pointe pas sur l'adresse ip ".$serv->IP." (actuellement il pointe vers ".$dns->answer[sizeof($dns->answer)-1]->address."), ou sa propagation se terminera dans ".$dns->answer[sizeof($dns->answer)-1]->ttl." secondes"));
                    }
                }
                if ($err)return false;

                //pour activer ssl il faut déclencher une tache
                $task  = genericClass::createInstance('Parc','Tache');
                $task->Nom = "Activation SSL pour la configuration Apache ".$this->ApacheServerName." ( ".$this->Id." )";
                $task->Type = "Ssh";
                $task->Contenu = "/usr/src/certbot/certbot-auto --renew-by-default --webroot certonly --webroot-path /var/www/letsencrypt --quiet -d ".$this->ApacheServerName;
                // ajout des server alias
                $sa = explode("\n",$this->ApacheServerAlias);
                if (!empty($sa[0]))foreach ($sa as $s ){
                    $task->Contenu .= " -d ".trim($s);
                }
                $task->Contenu .= "\n cat /etc/letsencrypt/live/".$this->ApacheServerName."/fullchain.pem";
                $task->Contenu .= "\n cat /etc/letsencrypt/live/".$this->ApacheServerName."/privkey.pem";
                $task->addParent($this);
                $task->addParent($serv);
                $task->Save();
                parent::Save();
			break;
			default:
			break;
		}
		return;
	}

	public function getRootPath() {
		if (!$this->Id){
			//recherche de l'hote dans le parent
			foreach ($this->Parents as $p){
				if ($p['Titre']=='Host'){
					$ho = Sys::getOneData('Parc','Host/'.$p['Id']);
					return '/home/'.$ho->Nom.'/www';
				}
			}
			return 'Tout neuf';
		}else return $this->DocumentRoot;
	}
    /**
     * getLdapID
     * récupère le ldapId d'une entrée pour un serveur spécifique
     */
    public function getLdapID($KEServer) {
        if (!empty($this->LdapID))
            $en = json_decode($this->LdapID,true);
        else $en=array();
        return $en[$KEServer->Id];
    }
    /**
     * setLdapID
     * défniit le ldapId d'une entrée pour un serveur spécifique
     */
    public function setLdapID($KEServer,$ldapId) {
        if (!empty($this->LdapDN))
            $en = json_decode($this->LdapID,true);
        else $en = Array();
        if (!is_array($en))$en = array();
        $en[$KEServer->Id] = $ldapId;
        $this->LdapID = json_encode($en);
    }
    /**
     * getLdapDN
     * récupère le ldapDN d'une entrée pour un serveur spécifique
     */
    public function getLdapDN($KEServer) {
        if (!empty($this->LdapDN))
            $en = json_decode($this->LdapDN,true);
        else $en=array();
        return $en[$KEServer->Id];
    }
    /**
     * setLdapDN
     * définit le ldapDN d'une entrée pour un serveur spécifique
     */
    public function setLdapDN($KEServer,$ldapDn) {
        if (!empty($this->LdapDN))
            $en = json_decode($this->LdapDN,true);
        else $en = Array();
        if (!is_array($en))$en = array();
        $en[$KEServer->Id] = $ldapDn;
        $this->LdapDN = json_encode($en);
    }
    /**
     * getLdapTms
     * récupère le ldapTms d'une entrée pour un serveur spécifique
     */
    public function getLdapTms($KEServer) {
        if (!empty($this->LdapTms))
            $en = json_decode($this->LdapTms,true);
        else $en=array();
        return $en[$KEServer->Id];
    }
    /**
     * setLdapTms
     * définit le ldapTms d'une entrée pour un serveur spécifique
     */
    public function setLdapTms($KEServer,$ldapTms) {
        if (!empty($this->LdapTms))
            $en = json_decode($this->LdapTms,true);
        else $en = Array();
        if (!is_array($en))$en = array();
        $en[$KEServer->Id] = $ldapTms;
        $this->LdapTms = json_encode($en);
    }
	/**
	 * Verification des erreurs possibles
	 * @param	boolean	Verifie aussi sur LDAP
	 * @return	Verification OK ou NON
	 */
	public function Verify( $synchro = true ) {
		$this->addWarning(array("Message"=>"Veuillez bien vérifier que les ServerName soit bien configuré et pointe bien vers le serveur. Les ServerAlias doivent également bien pointer sur le serveur, sinon l'activation SSL échouera."));

		//check documentRoot
		if (substr($this->DocumentRoot,strlen($this->DocumentRoot)-1,1)=='/') $this->DocumentRoot = substr($this->DocumentRoot,0,-1);

		if(parent::Verify()) {
            //check ssl
            if ($this->Ssl&&$this->SslMethod=='Manuel'&&(empty($this->SslCertificate)||empty($this->SslCertificateKey))){
                $this->addError(array("Message"=>"Pour activer SSL il faut que la clef et le certificat soient renseignés"));
                $this->_isVerified = false;
                return false;
            }
            if ($this->Ssl&&$this->SslMethod=='Manuel') {
                //check validity before enabling
                $key = openssl_x509_check_private_key($this->SslCertificate, $this->SslCertificateKey);
                if (!$key) {
                    $this->addError(array("Message" => "Le certificat et la clef ne correspondent pas."));
                    $this->_isVerified = false;
                    return false;
                }
            }
			$this->_isVerified = true;

			if($synchro) {

				// Outils
				$KEHost = $this->getKEHost();
				$KEServers = $this->getKEServer();
				foreach ($KEServers as $KEServer) {
                    $dn = 'apacheServerName=' . $this->ApacheServerName . ',cn=' . $KEHost->Nom . ',ou=' . $KEServer->LDAPNom . ',ou=servers,' . PARC_LDAP_BASE;

                    // Verification à jour
                    $res = Server::checkTms($this,$KEServer);
                    if ($res['exists']) {
                        if (!$res['OK']) {
                            $this->AddError($res);
                            $this->_isVerified = false;
                        } else {
                            // Déplacement
                            $res = Server::ldapRename($this->getLdapDN($KEServer), 'apacheServerName=' . $this->ApacheServerName, 'cn=' . $KEHost->Nom . ',ou=' . $KEServer->LDAPNom . ',ou=servers,' . PARC_LDAP_BASE);
                            if ($res['OK']) {
                                // Modification
                                $entry = $this->buildEntry($KEServer,false);
                                $res = Server::ldapModify($this->getLdapID($KEServer), $entry);
                                if ($res['OK']) {
                                    // Tout s'est passé correctement
                                    $this->setLdapDN($KEServer,$dn);
                                    $this->setLdapTms($KEServer,$res['LdapTms']);
                                } else {
                                    // Erreur
                                    $this->AddError($res);
                                    $this->_isVerified = false;
                                    // Rollback du déplacement
                                    $tab = explode(',', $this->getLdapDN($KEServer));
                                    $leaf = array_shift($tab);
                                    $rest = implode(',', $tab);
                                    Server::ldapRename($dn, $leaf, $rest);
                                }
                            } else {
                                $this->AddError($res);
                                $this->_isVerified = false;
                            }
                        }

                    } else {
                        ////////// Nouvel élément
                        if ($KEHost) {
                            $entry = $this->buildEntry($KEServer);
                            $res = Server::ldapAdd($dn, $entry);
                            if ($res['OK']) {
                                $this->setLdapDN($KEServer,$dn);
                                $this->setLdapID($KEServer,$res['LdapID']);
                                $this->setLdapTms($KEServer,$res['LdapTms']);
                            } else {
                                $this->AddError($res);
                                $this->_isVerified = false;
                            }
                        } else {
                            $this->AddError(array('Message' => "Une configuration Apache doit obligatoirement être créé dans un hébergement donné.", 'Prop' => ''));
                            $this->_isVerified = false;
                        }
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
	 * Récupère une référence vers l'objet KE "Host" parent
	 * On conserve une référence vers le host
	 * pour le cas d'une utilisation ultérieure
	 * @return	L'objet Kob-Eye
	 */
	private function getKEHost() {
		if(!is_object($this->_KEHost)) {
			$tab = $this->getParents('Host');
			if(empty($tab)) return false;
			else $this->_KEHost = $tab[0];
		}
		return $this->_KEHost;
	}

	/**
	 * Configuration d'une nouvelle entrée type
	 * Utilisé lors du test dans Verify
	 * puis lors du vrai ajout dans Save
	 * @param	boolean		Si FALSE c'est simplement une mise à jour
	 * @return	Array
	 */
	private function buildEntry($KEServer, $new = true ) {
	    //recherche multiple web servers
        $host= $this->getOneParent('Host');
        $webs= $host->getParents('Server');

		$entry = array();
		if(!empty($this->ApacheServerAlias)) {
			$alias = explode("\n", $this->ApacheServerAlias);
			$entry['apacheserveralias'] = array();
			foreach($alias as $k => $a)	$entry['apacheserveralias'][$k] = $a;
		}elseif (!$new) $entry['apacheserveralias'] = array();

		$entry['apachesuexecuid'] = $this->_KEHost->Nom;
		$Client = $this->_KEHost->getKEClient();
		$entry['apachesuexecgid'] = $Client->NomLDAP;
		$entry['apacheservername'] = $this->ApacheServerName;
		$entry['apachescriptalias'] = '/cgi-bin/ /home/'.$this->_KEHost->Nom.'/cgi-bin/';
		$entry['apachedocumentroot'] = '/home/'.$this->_KEHost->Nom.'/'.$this->DocumentRoot;
		if($new) {
			$entry['objectclass'][0] = 'apacheConfig';
			$entry['objectclass'][1] = 'top';
		}
		$entry['apachevhostenabled'] = $this->Actif?'yes':'no';
		$entry['apacheHtPasswordEnabled'] = $this->PasswordProtected?'yes':'no';
		if ($this->PasswordProtected) {
			$entry['apacheOptions'][] = 'AuthType Basic';
			$entry['apacheOptions'][] = 'AuthName "Authentication Required"';
			$entry['apacheOptions'][] = 'AuthUserFile "'.'/home/'.$this->_KEHost->Nom.'/'.$this->DocumentRoot.'/.htpasswd"';
			$entry['apacheOptions'][] = 'Require valid-user';
			$entry['apacheHtPasswordUser'] = $this->HtaccessUser;
			$entry['apacheHtPasswordPassword'] = $this->HtaccessPassword;
		}elseif (!$new){
			$entry['apacheOptions'] = Array();
		}
		if ($this->Ssl&&!empty($this->SslCertificate)&&!empty($this->SslCertificateKey)){
			$entry['apacheSslEnabled'] = 'yes';
			$entry['apacheCertificate'] = base64_encode($this->SslCertificate);
			$entry['apacheCertificateKey'] = base64_encode($this->SslCertificateKey);
			$entry['apacheCertificateExpiration'] = $this->SslExpiration;
		}else{
			$entry['apacheSslEnabled'] = 'no';
		}
        $entry['apacheProxy'] = '';
    	foreach ($webs as $web){
    	    if (!empty($web->InternalIP))
              $entry['apacheProxy'] .= 'server '.$web->InternalIP.";\n";
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
		$KEServers = $this->getKEServer();
		foreach ($KEServers as $KEServer) {
            try {
                $KEServer->remoteExec('rm /etc/httpd/sites-enabled/' . $this->ApacheServerName . '* -f && systemctl reload httpd');
            } catch (Exception $e) {
                $this->addError(Array("Message" => "Impossible d'effectuer la commande de suppression sur le serveur"));
                return false;
            }
            Server::ldapDelete($this->LdapID);
        }
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
			$KEHost = $this->getKEHost();
			if($KEHost)	$this->_KEServer = $KEHost->getKEServer();
			else return false;
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
		// Parent Host
		$Tab = Sys::$Modules["Parc"]->callData("Parc/Host/Nom=".$Parts[1][1], "", 0, 1);
		if(!empty($Tab)) {
			$obj = genericClass::createInstance('Parc', $Tab[0]);
			$this->AddParent($obj);
		}
	}

	/**
	 * Retrouve les sous domaines qui correspondent
	 * ( Nécessité d'avoir ça dans les 2 sens )
	 * @return	void
	 */
	public function findSubDomains() {
		// Liste des domaines
		$Domains = array();
		if(!empty($this->ApacheServerName)) $Domains[] = $this->ApacheServerName;
		if(!empty($this->ApacheServerAlias)) {
			$Tab = explode("\r", $this->ApacheServerAlias);
			foreach($Tab as $url) if(!empty($url)) $Domains[] = $url;
		}
		$result = 0;
		// Recherche
		foreach($Domains as $url) {
			$Parts = explode('.', $url);
			$domain = "";
			$domain = array_pop($Parts);
			$domain = array_pop($Parts).'.'.$domain;
			$subdomain = implode('.', $Parts);
			$Tab = Sys::$Modules["Parc"]->callData("Parc/Domain/Url=$domain/Subdomain/Url=$subdomain", "", 0, 100);
			if(!empty($Tab)) {
				foreach($Tab as $o) {
					$result++;
					$obj = genericClass::createInstance('Parc', $Tab[0]);
					$obj->AddParent($this);
					$obj->Save(false);
				}
			}
		}
		return $result;
	}

	/**
	 * callBackTask
	 * function callback pour le retour de la tache
	 */
	public function callBackTask($msg){
		if (preg_match("#-----BEGIN CERTIFICATE-----#", $msg,$out)){
            $this->SslExpiration=time()+(86400*90);
			//enregistrement du fullchain
			$this->SslCertificate = $msg;
			parent::Save();
		}
		if (preg_match("#-----BEGIN PRIVATE KEY-----#", $msg,$out)){
			$this->SslCertificateKey = $msg;
			parent::Save();
		}
	}
}