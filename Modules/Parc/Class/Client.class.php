<?php

class Parc_Client extends genericClass {
	var $Role = 'PARC_CLIENT';
	var $_isVerified = false;
	/**
	 * Force la vérification avant enregistrement
	 * @param	boolean	Enregistrer aussi sur LDAP
	 * @return	bool
	 */
	public function Save( $synchro = true ) {
        //ajout auto du revendeur
        if (Sys::$User->isRole('PARC_REVENDEUR')){
            $rev = Process::getRegVars('ParcRevendeur');
            $this->AddParent($rev);
        }

		// Forcer la vérification
		$this->Verify( $synchro );
        parent::Save();

		// Enregistrement si pas d'erreur
		if($this->_isVerified){
            //Calcul montant mensuel
            $cs = $this->getChildren('Contrat');
            $amount = 0;
            foreach ($cs as $c){
                $amount += $c->MontantMensu;
            }
            $this->MontantMensuel = $amount;

			parent::Save();
			$this->setUser();
		}
        return true;
	}

	/**
	 * Verification des erreurs possibles
	 * @param	boolean	Verifie aussi sur LDAP
	 * @return	Verification OK ou NON
	 */
	public function Verify( $synchro = false ) {
        if(!empty($this->CodeGestion)){
            $yets = Sys::getData('Parc','Client/CodeGestion='.Utils::KEAddSlashes($this->CodeGestion));
            foreach($yets as $yet){
                if($yet && $yet->Id != $this->Id){
                    $this->AddError(
                        Array(
                            "Message"=>"__LA_VALEUR_DU_CHAMP__ CodeGestion __ALREADY_EXISTS__",
                            "Prop"=> "CodeGestion"
                        )
                    );
                    $this->_isVerified = false;
                    return false;
                }
            }
            if(empty($this->Nom)){
                $this->Nom = $this->CodeGestion; // Gestion des tiers sans nom dans la gestion
            }
        }

        if (empty($this->NomLDAP)) {
            $this->NomLDAP = Utils::CheckSyntaxe($this->Nom);
            $cpt = 0;
            $base = $this->NomLDAP;
            while ($Res = Sys::getCount($this -> Module,  $this -> ObjectType . "/NomLDAP=" . $this->NomLDAP)){
                $this->NomLDAP = $base.'_'.$cpt;
            }
        }
        $this->NomLDAP = strtolower($this->NomLDAP);
        $this->NomLDAP = Utils::CheckSyntaxe($this->NomLDAP);
        if (empty($this->AccesUser)) {
            $this->AccesUser = $this->NomLDAP;
        }
        $this->AccesUser = strtolower($this->AccesUser);
        $this->AccesUser = Utils::CheckSyntaxe($this->AccesUser);

		if(parent::Verify()) {

            $this->_isVerified = true;

            //si acces web alors il faut vérifier identifiant / moty de passe et email
            if ($this->AccesActif){
                if (empty($this->AccesUser)){
                    $this->AddError(Array("Message"=>"Veuillez renseigner l'identifiant pour l'accès web"));
                    $this->_isVerified = false;
                }
                if (empty($this->AccesPass)){
                    $this->AddError(Array("Message"=>"Veuillez renseigner le mot de passe pour l'accès web"));
                    $this->_isVerified = false;
                }
                if (empty($this->Email)){
                    $this->AddError(Array("Message"=>"Veuillez renseigner l'adresse mail pour l'accès web"));
                    $this->_isVerified = false;
                }
                if (!$this->_isVerified)
                    return false;
            }

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
        //$entry['sn'] = $this->NomLDAP;
        if ($this->AccesActif) {
            $entry['uid'] = $this->AccesUser;
            $entry['userPassword'] = '{MD5}' . base64_encode(pack("H*", md5($this->AccesPass)));
        }else{
            $entry['uid'] = $this->NomLDAP;
            $entry['userPassword'] = '{MD5}' . base64_encode(pack("H*", md5('ZOBzobzoboizojhfdslhj')));
        }
        //$entry['displayname'] = $this->Nom.' '.$this->Prenom;
//        if($new) {
            $entry['homedirectory'] = '/home/'.$this->AccesUser;
            $entry['objectclass'][0] = 'posixGroup';
            $entry['objectclass'][1] = 'top';
            $entry['objectclass'][2] = 'posixAccount';
            $entry['objectclass'][3] = 'shadowAccount';
            $entry['uidnumber'] = Server::getNextUid();
            $entry['gidnumber'] = Server::getNextGid();
//        }
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
		$this->deleteGuacamoleUser();
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
                $sGrp->Nom = strtoupper($this->Nom);
                $sGrp->addParent($grp);
                $sGrp->Save();

                $grp = $sGrp;
            }

			//Vérification des propriétées
			if (!empty($this->AccesUser)&&!empty($this->AccesPass)){
				if (!sizeof($u)){
					//creation de l'utilisateur
					$u = genericClass::createInstance('Systeme','User');
                    $u->Nom = $this->Nom;
                    $u->Prenom = $this->Prenom;
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
                    $u->Nom = $this->Nom;
                    $u->Prenom = $this->Prenom;
					$u->Login = $this->AccesUser;
					$u->Pass = md5($this->AccesPass);
					$u->Mail = $this->Email;
					$u->Actif = true;
					$u->AddParent($grp);
					$u->Save();
				}
                //mise à jour de l'utilisateur guacamole
                $this->updateGuacamoleUser();
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
    public function getRevendeur() {
        return $this->getOneParent('Revendeur');
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
    private function updateGuacamoleUser(){
	    //test des serveurs guacamole
        $servs = Sys::getData('Parc','Server/Guacamole=1');
        foreach ($servs as $serv) {
            $dbGuac = new PDO('mysql:host=' . $serv->IP . ';dbname=guacamole', $serv->guacAdminUser, $serv->guacAdminPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $dbGuac->query("SET AUTOCOMMIT=1");
            $dbGuac->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (isset($this->AccesUser) && $this->AccesUser != '' && $this->AccesUser != null && isset($this->AccesPass) && $this->AccesPass != '' && $this->AccesPass != null) {
                $query = "SELECT * FROM `guacamole_user` WHERE username = '" . $this->AccesUser . "'";
                $q = $dbGuac->query($query);
                $result = $q->fetchALL(PDO::FETCH_ASSOC);
                if (sizeof($result) > 0) {
                    $usr = $result;
                    $query = "UPDATE `guacamole_user` SET password_hash=UNHEX(UPPER(SHA2('" . $this->AccesPass . "',256))),password_date='" . date("Y-m-d H:i:s") . "',password_salt=NULL  WHERE username='" . $this->AccesUser . "'";
                    $q = $dbGuac->query($query);
                } else {
                    $query = "INSERT INTO `guacamole_user` (username,password_hash,password_date) VALUES ('" . $this->AccesUser . "',UNHEX(UPPER(SHA2('" . $this->AccesPass . "',256))),'" . date("Y-m-d H:i:s") . "')";
                    $q = $dbGuac->query($query);

                }
                $query = "SELECT * FROM `guacamole_user` WHERE username = '" . $this->AccesUser . "'";
                $q = $dbGuac->query($query);
                $result = $q->fetchALL(PDO::FETCH_ASSOC);
                $usr = $result;
                //creation des droits
//                $query = "INSERT IGNORE INTO `guacamole_system_permission` (user_id,permission) VALUES ('" . $usr[0]['user_id'] . "','ADMINISTER')";
//                $q = $dbGuac->query($query);

                //Maj de ses connexions
                $query = "";
                $devices = $this->getChildren('Device');
                if(count($devices)){
                    foreach ($devices as $dev) {
                        $cons = $dev->getChildren('DeviceConnexion');
                        foreach ($cons as $con) {
                            $query .= "INSERT IGNORE INTO `guacamole_connection_permission` (user_id,connection_id,permission) VALUES ('" . $usr[0]['user_id'] . "','" . $con->GuacamoleId . "','READ');";
                        }
                    }
                    $q = $dbGuac->query($query);
                }

            } else if (isset($this->AccesUser) && $this->AccesUser != '' && $this->AccesUser != null && (!isset($this->AccesPass) || $this->AccesPass == '' || $this->AccesPass == null)) {
                //$this->addError(array('Message' => 'La valeur du champ AccesPass est nulle ou non définie alors que le champ AccesUser est défini.', "Prop" => 'AccesPass'));
            }
        }
        return true;
    }

    /**CurrentVersion
     * deleteGuacamole
     *
     */
    private function deleteGuacamoleUser() {
        $servs = Sys::getData('Parc','Server/Guacamole=1');
        foreach ($servs as $serv) {

            $dbGuac = new PDO('mysql:host='.$serv->IP.';dbname=guacamole',  $serv->guacAdminUser, $serv->guacAdminPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $dbGuac->query("SET AUTOCOMMIT=1");
            $dbGuac->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if (isset($this->AccesUser) && $this->AccesUser != '' && $this->AccesUser != null && isset($this->AccesPass) && $this->AccesPass != '' && $this->AccesPass != null) {
                $query = "DELETE FROM `guacamole_user` WHERE username = '" . $this->AccesUser . "'";
                $q = $dbGuac->query($query);
            }
        }
    }
    /**
     * importEmails
     * Function permettant d'importer et de créer des comptes clients en masse.
     * Au format: email;pass;COS;quota
     */
    public function importEmails($emails){
        $srv = Sys::getOneData('Parc','Server/defaultMailServer=1');

        if(!is_object($srv) || $srv->ObjectType != 'Server'){
            $this->AddError(array('Message'=>'Un compte mail doit être lié a un serveur.'));
            return false;
        }
        $zimbra = new \Zimbra\ZCS\Admin($srv->IP, $srv->mailAdminPort);
        $zimbra->auth($srv->mailAdminUser, $srv->mailAdminPassword);

        //traitement du csv
        $emails = explode("\n",$emails);
        $error = false;
        $domainToCreate = array();
        //verification du fichier
        foreach ($emails as $e){
            $e = explode(';',$e);
            $email = $e[0];
            $pass = $e[1];
            $cos = $e[2];
            $quota = ($e[3]>1000)?$e[3]*1024:1024;
            if (empty($email)||empty($pass)||empty($cos)) {
                $error = true;
                $this->addError(Array('Message' => 'Compte email incomplet ' . $email . ' ' . $pass . ' ' . $cos . ' ' . $quota));
            }
            $domain = explode('@',$email);
            //verification du domaine
            $domainToCreate[] = $domain[1];
        }
        if ($error)
            return false;
        //creation des domaines
        foreach ($domainToCreate as $dom){
            try {
                $zimbra->createDomain(Array('name' =>$dom));
            }catch (Exception $e){
                //$this->addError(Array('Message' => 'Erreur lors de la création du domaine '.$dom));
            }
        }

        //création des compte emails
        foreach ($emails as $e) {
            $e = explode(';', $e);
            $email = $e[0];
            $pass = $e[1];
            $cos = $e[2];
            $quota = ($e[3])?$e[3]*1024:1024;
            if (Sys::getCount('Parc','CompteMail/Adresse='.$email)){
                $compte = Sys::getOneData('Parc','CompteMail/Adresse='.$email);
            }else {
                $compte = genericClass::createInstance('Parc', 'CompteMail');
                $compte->Adresse = $email;
            }
            $compte->Pass = $pass;
            $compte->COS = $cos;
            $compte->Quota = $quota;
            $compte->addParent($this);
            $compte->addParent($srv);
            if ($compte->Verify()){
                if (!$compte->Save()){
                    $this->Error = array_merge($this->Error,$compte->Error);
                    return false;
                }
            }else{
                $this->Error = array_merge($this->Error,$compte->Error);
                return false;
            }
        }

        return true;
    }

    public function Test($params){
        sleep(1);
        return 'toto'. rand(500 , 7985321);



        if($params['MAILSEND']){
            require_once ("Class/Lib/Mail.class.php");
            $Mail = new Mail();
            $Mail -> Subject($params['SUBJECT']);
            $Mail -> From( 'no-reply@abtel.fr');
            $Mail -> ReplyTo('contact@abtel.fr');
            //$Mail -> To($this -> Email);
            $Mail -> To('gcandella@abtel.fr');

            //$Mail -> To('enguer@enguer.com');
            $Mail -> Bcc('gcandella@abtel.fr');
            //$Mail -> Cc($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
            $bloc = new Bloc();

            $mailContent = $params['CONTENT'];



            $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
            $Pr = new Process();
            $bloc -> init($Pr);
            $bloc -> generate($Pr);
            $Mail -> Body($bloc -> Affich());

            //klog::l('$Mail',print_r($Mail,true));

            $Mail -> Send();
            return 'Mail envoyé avec succès';
        } else{
            return array(
                'template'=>'SendRecap',
                'callNext' => array(
                    'nom'=>'Test',
                    'title' => 'Envoi du mail',
                    'needConfirm' => false
                )
            );
        }

    }

    /**
     * getClient
     * @param String Code du client
     */
    public static function getClientFromCode($code,$name='') {
        if (empty($name))$name = $code;
        //on vérifie que le client n'existe pas déjà
        $client = Sys::getOneData('Parc','Client/NomLDAP='.$code,0,1,'','','','',true);
        if (!$client) {
            $client = genericClass::createInstance('Parc', 'Client');
            $client->Nom = $name;
            $client->NomLDAP = $code;
            $client->Save();
        }
        return $client;
    }
    /**
     * doMigrationMail
     * Migration de boite mail entre serveur MBX
     */
    public function doMigrationMail ($params=null) {
        if (!$params) $params =array('step'=>0);
        if (!isset($params['step'])) $params['step']=0;
        switch($params['step']) {
            case 1:
                $srv = Sys::getOneData('Parc', 'Server/' . $params['selectedServer'], 0, 1, '', '', '', '', true);
                if (!$srv) return false;
                $task = genericClass::createInstance('Systeme', 'Tache');
                $task->Type = 'Fonction';
                $task->Nom = 'Inventaire des comptes à déplacer du client '. $this->Nom . ' vers le serveur "' . $srv->Nom . '"';
                $task->TaskModule = 'Parc';
                $task->TaskObject = 'Client';
                $task->TaskId = $this->Id;
                $task->TaskFunction = 'findMailAccounts';
                $task->TaskType = 'install';
                $task->TaskCode = 'CLIENT_MAIL_ACCOUNTS_SERVER_MOVE';
                $task->TaskArgs = serialize($params);
                $task->addParent($this);
                $task->Save();
                return array('task' => $task, 'title' => 'Progression de l\'inventaire');
                break;
            default:
                return array('template' => "listSrv", 'step' => 1, 'callNext' => array('nom' => 'doMigrationMail', 'title' => 'Progression'),'errors' => $this->Error);
        }
    }
    /**
     * moveMailAccounts
     * Migration de boites mail du client entre serveur MBX
     * @param object task
     */
    public function findMailAccounts ($task) {
        $params=unserialize($task->TaskArgs);
        $account=$this->getChildren('CompteMail');
        foreach ($account as $a) {
            //Exécution de la commande
            $act=$task->createActivity('Création de la tache pour le compte '.$a->Adresse);
            $a->createMailboxMoveTask($params);
            $act->Terminate(true);
        }
    }
}