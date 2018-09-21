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
	public function Save( $synchro = true , $force = false) {
		if ($this->Ssl&$this->Id){
			//test de l'activation ssl
			$old = Sys::getOneData('Parc','Apache/'.$this->Id);
			if (!$old->Ssl&&!$force) {
			    if (!$this->enableSsl()) return false;
            }
		}elseif($this->Ssl){
		    parent::Save();
            $this->enableSsl();
        }
		// Forcer la vérification
		if(!$this->_isVerified) $this->Verify( $synchro );
		// Enregistrement si pas d'erreur
		if($this->_isVerified) parent::Save();

		//mise à jour des serveurs
        try {
            $srvs = $this->getKEServer();
            foreach ($srvs as $srv)
                $srv->callLdap2Service();
            $pxs = Sys::getData('Parc','Server/Proxy=1');
            foreach ($pxs as $px) {
                $px->callLdap2Service();
                $px->createRestartProxyTask();
            }

        }catch (Exception $e){
            $this->addError(array("Message"=>"Impossible de mettre le serveur à jour. Serveur injoignable.".$e->getMessage()));
            return false;
        }
		return true;
	}

    /**
     * softSave
     * @return bool
     */
    public function softSave() {
        return parent::Save();
    }

    /**
     * enableSsl
     * @param bool $force
     * @param null $instance
     * @return bool
     */
	public function enableSsl($force = false) {
		if (empty($this->SslMethod))$this->SslMethod = "Letsencrypt";
		//check already exists
		if (!$force&&$this->Ssl&&!empty($this->SslCertificate)&&!empty($this->SslCertificateKey)&&$this->SslExpiration>time()+86400){
			$this->addError(array("Message"=>"Le certificat est déjà généré et valide. Son expiration n'interviendra pas dans les prochaines 24 heures."));
			return false;
		}
		//on vérifie qu'il n'y ait pas déjà une tache
        if (Sys::getCount('Parc','Apache/'.$this->Id.'/Tache/Termine=0')){
            $this->addError(array("Message"=>"Il y a déjà une tache à venir pour l'activation SSL."));
            return false;
        }
        $this->Ssl = false;
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
                $this->Ssl = true;
                //recherche du serveur proxy
                $serv = Sys::getOneData('Parc','Server/Proxy=1',0,1,'ASC','Id');
                if (!sizeof($serv)) {
                    $serv = $this->getKEServer();
                    $serv = $serv[0];
                }
                $sa = explode(" ",$this->getDomains());

                //test des entrées dns
                $resolver = new Net_DNS2_Resolver( array('nameservers' => array('8.8.8.8')) );
                try {
                    $t = array($resolver->query($this->ApacheServerName, 'A'));
                }catch (Exception $e){
                    $this->addWarning(array("Message"=>"Timeout DNS : ".$e->getMessage()));
                }
                foreach ($sa as $a){
                    $a = trim($a);
                    if (!empty($a)) {
                        try {
                            $t = array_merge($t, array($resolver->query($a, 'A')));
                        }catch (Exception $e){
                            $this->addWarning(array("Message"=>"Erreur DNS : ".$e->getMessage()));
                        }
                    }
                }
                //test des erreurs
                $err = false;
                foreach ($t as $dns){
                    if (!sizeof($dns->answer[sizeof($dns->answer)-1])){
                        $err = true;
                        $this->addError(array("Message"=>"Le domaine : '".$dns->question[sizeof($dns->question)-1]->qname."' ne pointe pas sur l'adresse ip ".$serv->IP." (actuellement il n'est pas configuré)"));
                    }/*elseif (trim($dns->answer[sizeof($dns->answer)-1]->address)!=trim($serv->IP)){
                        $err = true;
                        $this->addError(array("Message"=>"Le domaine : '".$dns->question[sizeof($dns->question)-1]->qname."' ne pointe pas sur l'adresse ip ".$serv->IP." (actuellement il pointe vers ".$dns->answer[sizeof($dns->answer)-1]->address."), ou sa propagation se terminera dans ".$dns->answer[sizeof($dns->answer)-1]->ttl." secondes"));
                    }*/
                }
                if ($err)return false;

                //pour activer ssl il faut déclencher une tache
                $task  = genericClass::createInstance('Parc','Tache');
                $task->Nom = "Activation SSL pour la configuration Apache ".$this->getDomains()." ( ".$this->Id." )";
                $task->Type = "Fonction";
                $task->TaskModule = "Parc";
                $task->TaskObject = "Apache";
                $task->TaskFunction = "executeLetsencrypt";
                $task->TaskId = $this->Id;
                $task->addParent($this);
                $task->addParent($serv);
                //on va charcher l'hébergement
                $host = $this->getOneParent('Host');
                $task->addParent($host);
                //on va chercher l'instance
                $instance = $host->getOneChild('Instance');
                $task->addParent($instance);
                //recherch de la prochaine d'execution pour eviter les collision de letsencrypt
                $nb = Sys::getCount('Parc','Tache/DateDebut>'.time());
                $task->DateDebut = time()+(60*($nb+1));
                $task->Save();
                parent::Save();
			break;
			default:
			break;
		}
		return true;
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
        if (!empty($this->LdapID)) {
            if (!$en = json_decode($this->LdapID, true))
                $en = array($KEServer->Id => $this->LdapID);
        }else $en=array();
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
        if (!empty($this->LdapDN)) {
            if (!$en = json_decode($this->LdapDN, true))
                $en = array($KEServer->Id => $this->LdapDN);
        }else $en=array();
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
        if (!empty($this->LdapTms)) {
            if (!$en = json_decode($this->LdapTms, true))
                $en = array($KEServer->Id => $this->LdapTms);
        }else $en=array();
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
        if ($this->Ssl&&$this->SslMethod=='Letsencrypt') {
            $this->addWarning(array("Message" => "Veuillez bien vérifier que les ServerName soit bien configuré et pointe bien vers le serveur. Les ServerAlias doivent également bien pointer sur le serveur, sinon l'activation SSL échouera."));
        }

		//check documentRoot
		if (substr($this->DocumentRoot,strlen($this->DocumentRoot)-1,1)=='/') $this->DocumentRoot = substr($this->DocumentRoot,0,-1);
        //test du documentroot
        $host = $this->getKEHost();
        $this->DocumentRoot = str_replace('/home/'.$host->NomLDAP.'/','',$this->DocumentRoot);
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
                if (empty($KEHost->NomLDAP)) {
                    $this->addWarning(array("Message" => "L'hébergement n'est pas à jour... Enregistrement forcé..."));
                    $KEHost->Save();
                }
                foreach ($KEServers as $KEServer) {
                    $dn = 'apacheServerName=' . $this->ApacheServerName.',cn=' . $KEHost->NomLDAP . ',ou=' . $KEServer->LDAPNom . ',ou=servers,' . PARC_LDAP_BASE;
                    // Verification à jour
                    $res = Server::checkTms($this,$KEServer,'cn=' . $KEHost->NomLDAP . ',ou=' . $KEServer->LDAPNom . ',ou=servers,' . PARC_LDAP_BASE,'apacheServerName=' . $this->ApacheServerName);
                    if ($res['exists']) {
                        if (!$res['OK']) {
                            $this->AddError($res);
                            $this->_isVerified = false;
                        } else {
                            // Déplacement
                            $res = Server::ldapRename($this->getLdapDN($KEServer), 'apacheServerName=' . $this->ApacheServerName, 'cn=' . $KEHost->NomLDAP . ',ou=' . $KEServer->LDAPNom . ',ou=servers,' . PARC_LDAP_BASE);
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

		$entry['apachesuexecuid'] = $this->_KEHost->NomLDAP;
		$Client = $this->_KEHost->getKEClient();
		$entry['apachesuexecgid'] = $Client->NomLDAP;
		$entry['apacheservername'] = $this->ApacheServerName;
		$entry['apachescriptalias'] = '/cgi-bin/ /home/'.$this->_KEHost->NomLDAP.'/cgi-bin/';
		$entry['apachedocumentroot'] = '/home/'.$this->_KEHost->NomLDAP.'/'.$this->DocumentRoot;
		if($new) {
			$entry['objectclass'][0] = 'apacheConfig';
			$entry['objectclass'][1] = 'top';
		}
		$entry['apachevhostenabled'] = $this->Actif?'yes':'no';
		$entry['apacheHtPasswordEnabled'] = $this->PasswordProtected?'yes':'no';
		if ($this->PasswordProtected) {
			$entry['apacheOptions'][] = 'AuthType Basic';
			$entry['apacheOptions'][] = 'AuthName "Authentication Required"';
			$entry['apacheOptions'][] = 'AuthUserFile "'.'/home/'.$this->_KEHost->NomLDAP.'/'.$this->DocumentRoot.'/.htpasswd"';
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

		//ALias Config
        if (!empty($this->ApacheConfig))
            $entry['apacheconfigalias'] = $this->ApacheConfig;
        else if (!$new) $entry['apacheconfigalias'] = Array();

        //Proxy config
		if ($this->ProxyCache){
            $entry['apacheProxyCacheConfig'] = "proxy_cache            STATIC;\n    proxy_cache_valid      200  1h;\n    proxy_cache_use_stale  error timeout invalid_header updating http_500 http_502 http_503 http_504;\n";
            $entry['apacheProxyCacheConfigSsl'] = "    proxy_cache STATIC;\n    proxy_cache_valid      200  1h;\n    proxy_cache_use_stale  error timeout invalid_header updating http_500 http_502 http_503 http_504;\n";
        }else if (!$new) {
		    $entry['apacheProxyCacheConfig'] = Array();
            $entry['apacheProxyCacheConfigSsl'] = Array();
        }
    	foreach ($webs as $web){
    	    if (!empty($web->InternalIP))
              $entry['apacheProxy'] .= 'server '.$web->InternalIP.";\n";
        }
        if($entry['apacheProxy'] == '') unset($entry['apacheProxy']);
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
                $this->addError(Array("Message" => "Impossible d'effectuer la commande de suppression sur le serveur ".$KEServer->Nom));
            }
            Server::ldapDelete($this->LdapID);
        }
        //suppresion de la config sur les serveurs proxy
        $pxs = Sys::getData('Parc','Proxy=1');
        foreach ($pxs as $px){
            try {
                $KEServer->remoteExec('rm /etc/nginx/conf.d/' . $this->ApacheServerName . '* -f && systemctl reload nginx');
            } catch (Exception $e) {
                $this->addError(Array("Message" => "Impossible d'effectuer la commande de suppression sur le serveur ".$px->Nom));
            }
        }

        parent::Delete();
        return true;
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

	/**
     * getDomains
     * renvoie la slite séparée pâr des esapces de tous les domaines
     *
     */
	public function getDomains() {
	    return $this->ApacheServerName.' '.(implode(" ",explode("\n",str_replace("\r","",$this->ApacheServerAlias))));
    }
    /**
     * getDomains
     * renvoie la slite séparée pâr des esapces de tous les domaines
     *
     */
    public function getDomainsLink() {{}
        $out = '<a href="http://'.$this->ApacheServerName.'">'.$this->ApacheServerName.'</a><br />';
        $oth = explode("\n",$this->ApacheServerAlias);
        foreach ($oth as $o){
            $out.='<a href="http://'.$o.'">'.$o.'</a><br />';
        }
        return $out;
    }
    /**
     * executeLetsencrypt
     * Execution de letsencrypt sur le serveur
     */
    public function executeLetsencrypt($task) {
        $first=$this->SslMainDomain;
        //récupératio ndu serveur
        $srv = $task->getOneParent('Server');

        //Vérification du dépot letsencrypt
        $act = $task->createActivity('Vérification du dépot letsencrypt');
        $err = false;
        $cert = $srv->getFileContent("/etc/letsencrypt/live/".$first."/fullchain.pem");
        if (!empty($cert)) {
            $certinfo = openssl_x509_parse($cert);
            //on vérifie qu'on a la bonne date d'expiration et qu'il est différent de celui actif
            if ($certinfo['validTo_time_t'] > time() + (86400 * 30) && $this->SslCertificate != $cert) {
                //alors on utilise ce certificat
                $act = $task->createActivity('Récupération des certificats');
                //récupération des certificats
                $this->SslCertificate = $srv->getFileContent("/etc/letsencrypt/live/" . $first . "/fullchain.pem");
                $act->addDetails($this->SslCertificate);
                $this->SslCertificateKey = $srv->getFileContent("/etc/letsencrypt/live/" . $first . "/privkey.pem");
                $act->addDetails($this->SslCertificateKey);
                $this->SslExpiration = $certinfo['validTo_time_t'];
                $act->addDetails('Date d\'Expiration: ' . $this->SslExpiration);
                $act->Terminate(true);
                $this->Save();
                return true;
                //on compare la liste des domaines à certifier et les domaines dans le certificat
                /*$domains=explode(' ',$this->getDomains());
                $certdomains = array();
                preg_match_all('#DNS:([^\ ,]*)#',$certinfo['extensions']['subjectAltName'],$othersdomains);
                $certdomains=array_merge($certdomains,$othersdomains[1]);
                foreach ($domains as $d){
                    if (!in_array($d,$certdomains)){
                        $this->addError(array('Message'=>'Le domaine '.$d.' n\' est pas compris dans le certificat en production. Il serait nécessaire de le regénérer.'));
                    }
                }*/

            }
        }

        //execution de la commande
        $prefixe = "/usr/src/certbot/certbot-auto --renew-by-default --webroot certonly --webroot-path /var/www/letsencrypt ";
        $cmd = '';
        // ajout des server alias
        $sa = explode(' ',$this->getDomains());
        foreach ($sa as $s ){
            $s = trim($s);
            if (!preg_match("#azko.site#",$s)&&!empty($s)) {
                if (empty($first)) $first=$s;
                $cmd .= " -d " . $s;
            }
        }
        if (empty($cmd)){
            $act = $task->createActivity('Aucun domaine à certifier.');
            $act->Terminate(true);
            return true;
        }
        $cmd = $prefixe.$cmd;
        $act = $task->createActivity('Execution de la commande certbot');
        $act->addDetails($cmd);
        try {
            $out = $srv->remoteExec($cmd);
        }catch (Exception $e) {
            $act->addDetails($e->getMessage());
            $act->Terminate(false);
            $task->Erreur = 1;
            $task->Save();
            return false;
        }
        $act->addDetails($out);
        if (preg_match('#/etc/letsencrypt/live/(.*?)/fullchain.pem#',$out,$path)) {
            //analyse du retour et récupération du path
            $first = $this->SslMainDomain = $path[1];
            parent::Save();
            $act->addDetails('Définition du domaine par défaut du certificat: '.$this->SslMainDomain);
        }

        $act = $task->createActivity('Récupération des certificats');
        //récupération des certificats
        $this->SslCertificate = $srv->getFileContent("/etc/letsencrypt/live/".$first."/fullchain.pem");
        $certinfo = openssl_x509_parse($this->SslCertificate);
        $act->addDetails($this->SslCertificate);
        $this->SslCertificateKey = $srv->getFileContent("/etc/letsencrypt/live/".$first."/privkey.pem");
        $act->addDetails($this->SslCertificateKey);
        $this->SslExpiration = $certinfo['validTo_time_t'];
        $act->addDetails('Date d\'Expiration: '.$this->SslExpiration);
        $act->Terminate(true);
        $this->Save();
        return true;
    }
    /**
     * checkCertificate
     * Vérifie la validité du certificat et récupère sa date d'expiration
     */
    public function checkCertificate($task = null) {
        if ($this->Ssl) {
            $certinfo = openssl_x509_parse($this->SslCertificate);
            //on vérifie qu'on a la bonne date d'expiration
            if ($this->SslExpiration!=$certinfo['validTo_time_t']){
                $this->SslExpiration = $certinfo['validTo_time_t'];
            }

            //on compare la liste des domaines à certifier et les domaines dans le certificat
            $domains=explode(' ',$this->getDomains());
            $certdomains = array();
            preg_match_all('#DNS:([^\ ,]*)#',$certinfo['extensions']['subjectAltName'],$othersdomains);
            $certdomains=array_merge($certdomains,$othersdomains[1]);
            foreach ($domains as $d){
                if (!in_array($d,$certdomains)){
                    $this->addError(array('Message'=>'Le domaine '.$d.' n\' est pas compris dans le certificat en production. Il serait nécessaire de le regénérer.'));
                }
            }
            //on sauvegarde
            $this->softSave();

            if ($task){
                foreach ($this->Error as $err){
                    $task->addRetour($err['Message']."\r\n");
                }
            }

            if ($this->SslExpiration>time()){
                return true;
            }else{
                $this->addError(array('Message'=>'Le certificat a expiré le '.date('d/m/Y à H:i:s',$certinfo['validTo_time_t']).'. Il serait nécessaire de le regénérer.'));
                return false;
            }
        }
        return true;
    }

}