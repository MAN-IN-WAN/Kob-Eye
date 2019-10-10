<?php
class Connection extends Root{
	var $Session;		//Objet contenant la session PHP
	var $Num;		//Num de la connexion
	var $Date;		//Date de la connexion
	var $Ip;		//Ip de la connexio
	var $Host;		//Nom de l hote
	var $NavLang;		//Langue de l hote
	var $Navigateur;	//Type du navigateur
	var $Id;		//Id de connexion
	var $LastUrl;		//Derniere url html 
	var $FirstUrl;		//Premiere url html
	var $Utlisateur;	//id de l'utilisateur connecté
	var $Referent;		//Referent de la connexion
	var $SessId="";
	var $login;
	var $pass;
	var $clearpass;
	var $passmd5;
	var $codeverif;
	var $Disconnected = false;
	var $SessionStarted = false;
	var $_dirtyCache = false;
	var $kerberosAuth = false;
	var $Mobile=false;
	

	//------------------------------------------------------------//
	//			CONSTRUCTEUR			      //
	//------------------------------------------------------------//
	//C est ici que l on configure la connexion et que l on définit les processus d identification
	function Connection() {
	    $newCo = false;

        //On enregistre le nunmero de session
		$this->DetectSessions();

		//Recherche de la connexion ou creation si inexistante
 		$this->GetConn();

		//Detection de la langue (cas par défaut)
		$this->DetectLanguage();

        //On detecte l existence de cookies envoyés par le navigateur
		if (!empty($this->SessId)) {
		    $domain = Sys::$domain;

            $site = Sys::getOneData('Systeme', 'Site/Domaine=' . $domain);
            if ($site && $site->Api && $_SERVER['REQUEST_URI'] != '/Documentation') {
                header("Content-type: text/json; charset=".CHARSET_CODE."");
                header("Accept-Ranges:bytes");
                header("Access-Control-Allow-Headers:Origin, Accept, Content-Type, X-Requested-With, X-CSRF-Token");
                header("Access-Control-Allow-Methods:GET, POST, PUT, DELETE, PATCH");
                header("Access-Control-Allow-Origin: *");

                //Demarrage Session
                $this->DemarreAuthSession();
                //Chargement des variables
                if (!$this->ChargementAuthVars()) $this->LoadLoginVars();
                //Initialisation du usersession
                if (!$this->DetectUser()) {
                    $this->DestroyAuth();
                }

                if (!Sys::$User) {
                    $this->DestroyAuth();
                    setcookie(PHP_SESSION_NAME, '', time() - 42000, '/');
                    http_response_code(401);
                    die(json_encode(array('success'=>false,'error' => 'invalid_credentials', 'error_description' => 'Probleme d\'identification. Essayez de vous reconnecter.')));
                }
                $apiKey = isset($_COOKIE["API_KEY"]) ? $_COOKIE["API_KEY"] : (isset($_GET["API_KEY"]) ? $_GET["API_KEY"] : (isset($_POST["API_KEY"]) ? $_POST["API_KEY"] : false));
                if(!$apiKey){
                    $data = array();
                    $data = json_decode(file_get_contents("php://input"));
                    if(isset($data->API_KEY)) $apiKey = $data->API_KEY;
                }
                $uApi = Sys::$User->getOneParent('ApiKey');
                if ($uApi->Key != $apiKey) {
                    $this->DestroyAuth();
                    setcookie(PHP_SESSION_NAME, '', time() - 42000, '/');
                    http_response_code(401);
                    die(json_encode(array('success'=>false,'error' => 'invalid_credentials', 'error_description' => 'Clef Api invalide - 1')));
                    return false;
                }


                if ($this->DetectSess()) $this->DestroySess();
            }else{
                //Si l id de session est envoyé directement depuis un cookie alors on demarre la session
                //Sinon on charge les variables presentes dans le cookie
                if ($this->DetectAuth() && !$this->Disconnected) {
                    //Demarrage Session
                    $this->DemarreAuthSession();
                    //Chargement des variables
                    if (!$this->ChargementAuthVars()) $this->LoadLoginVars();
                    //Initialisation du usersession
                    if (!$this->DetectUser()) $this->DestroyAuth();
                    if ($this->DetectSess()) $this->DestroySess();
                } elseif ($this->DetectSess()) {
                    //Utilisateur public
                    $this->initPublicSession();
                    $this->LoadLoginVars();
                    //Verficiation de la connexion

                    if (!$this->DetectUser()) {
                        $this->initDefaultUser();
                    } else {
                        //Il faut basculer les informations de la session
                        $this->SessionPublicToPrivate();
                    }
                } else{
                    //PAS DE CAS
                }
            }
		}else{
			$this->LoadLoginVars();

            if ($this->DetectUser()) {
                $domain = Sys::$domain;
                $site = Sys::getOneData('Systeme','Site/Domaine='.$domain);
                if($site && $site->Api && $_SERVER['REQUEST_URI'] != '/Documentation'){
                    header("Content-type: text/json; charset=".CHARSET_CODE."");
                    header("Accept-Ranges:bytes");
                    header("Access-Control-Allow-Headers:Origin, Accept, Content-Type, X-Requested-With, X-CSRF-Token");
                    header("Access-Control-Allow-Methods:GET, POST, PUT, DELETE, PATCH");
                    header("Access-Control-Allow-Origin: *");
                    $apiKey = isset($_COOKIE["API_KEY"]) ? $_COOKIE["API_KEY"] : ( isset($_GET["API_KEY"]) ? $_GET["API_KEY"] : ( isset($_POST["API_KEY"]) ? $_POST["API_KEY"] : false));
                    if(!$apiKey){
                        $data = json_decode(file_get_contents("php://input"));
                        if(isset($data->API_KEY)) $apiKey = $data->API_KEY;
					}
                    $exists = false;
                    if($apiKey)
                        $exists = Sys::getOneData('Systeme','ApiKey/Key='.$apiKey);

                    if(!$exists) {
                        http_response_code (401);
                        die(json_encode(array('success'=>false,'error'=>'invalid_credentials','error_description'=>'Clef Api invalide - 2')));
                        return false;
                    }

                    $uApi = Sys::$User->getOneParent('ApiKey');
                    if($uApi->Key != $apiKey){
                        http_response_code (401);
                        die(json_encode(array('success'=>false,'error'=>'invalid_credentials','error_description'=>'Clef Api invalide - 3')));
                        return false;
					}
                }

				//On genere l id de session
				$this->SessId = $this->genSessId($this->login,$this->pass,Sys::$Session->Id);
				//Creation Session
				$this->DemarreAuthSession();
				//Enregistrement des variable
				$this->SaveSessionVars();
				//derniere conexion
				Sys::$User->LastConnection = time();
				Sys::$User->Save();

				$newCo = true;

			}else{
 				$domain = Sys::$domain;

                $site = Sys::getOneData('Systeme', 'Site/Domaine=' . $domain);
                if ($site && $site->Api && $_SERVER['REQUEST_URI'] != '/Documentation') {
                    http_response_code (401);
                    die(json_encode(array('success'=>false,'error'=>'invalid_credentials','error_description'=>'L\'utilisation de cette API nécéssite un utilisateur authentifié')));
                }

                $this->initDefaultUser();
			}
		}

        //Detection de la langue (cas ou l'utilisateur force une langue)
		$this->DetectLanguage();
		//Chargement des éléments en cache
		if (isset(Sys::$User->Cache)&&is_array(Sys::$User->Cache)){
			foreach (Sys::$User->Cache as $k=>$C){
				Process::$TempVar[$k] = $C;
			}
		}

        //On enregistre la connexion
		if (is_object(Sys::$Session)&&ADD_CONNECT){
			Sys::$Session->Set("Utilisateur",Sys::$User->Id);
			$GLOBALS["Systeme"]->Log->log("UTILISATEUR ".Sys::$User->Id);
			Sys::$Session->Save();
		}else $GLOBALS["Systeme"]->Log->log("ERROR CONNEXION");

        if($site && $site->Api && $newCo){
            setcookie(PHP_SESSION_NAME, '', time()-42000, '/');
            http_response_code (200);
            echo (json_encode(array('success'=>'authentication_success','error'=>false,'auth_token'=>$this->SessId)));
			$GLOBALS['Systeme']->Close();
			$GLOBALS["Chrono"]->stop("TOTAL CONNEXION");
			//$GLOBALS['Systeme']->Log->log($GLOBALS['Chrono']->total());
			die();
        }

    }

	//----------------------------------------
	// CLOSE SESSION FOR MULTITHREADING
	//----------------------------------------
	static function CloseSession() {
		session_write_close();
	}

	//------------------------------------------------------------//
	//			SESSIONS DETECTION				      //
	//------------------------------------------------------------//

	function DetectSessions() {
		$this->DetectSess();
		$this->DetectAuth();
		$domain = Sys::$domain;
		$site = Sys::getOneData('Systeme','Site/Domaine='.$domain);
		if($site && $site->Api)
			$this->DetectToken();
		if (!empty($this->SessId)) return true ;else  return false;
	}
	function DetectSess() {
		$t=false;
		if (isset($_COOKIE["PHPSESSID"])&& $_COOKIE["PHPSESSID"]!="" ) {
			$this->SessId  = $_COOKIE["PHPSESSID"];
			$t=true;
		}
		if (isset($_POST["PHPSESSID"])&& $_POST["PHPSESSID"]!="" ) {
			$this->SessId  = $_POST["PHPSESSID"];
			$t=true;
		}
		if (isset($_GET["PHPSESSID"])&& $_GET["PHPSESSID"]!="" ) {
			$this->SessId  = $_GET["PHPSESSID"];
			$t=true;
		}
		if ($t) return true ;else  return false;
	}
	function DetectAuth() {
		$t=false;
		if (isset($_COOKIE[PHP_SESSION_NAME])&& $_COOKIE[PHP_SESSION_NAME]!="" ) {
			$this->SessId  = $_COOKIE[PHP_SESSION_NAME];
			$t=true;
		}
		if (isset($_POST[PHP_SESSION_NAME])&& $_POST[PHP_SESSION_NAME]!="" ) {
			$this->SessId  = $_POST[PHP_SESSION_NAME];
			$t=true;
		}
		if (isset($_GET[PHP_SESSION_NAME])&& $_GET[PHP_SESSION_NAME]!="" ) {
			$this->SessId  = $_GET[PHP_SESSION_NAME];
			$t=true;
		}
		if (isset($_POST["logkey"])&& $_POST["logkey"]!="" ) {
			$this->SessId  = $_POST["logkey"];
			$this->Mobile = true;
			$t=true;
		}
		if ($t) return true ;else  return false;
	}
	public function DetectToken(){
	    $types= array(
	    	'application/x-www-form-urlencoded',
            'application/json',
            'application/x-javascript',
            'text/javascript',
            'text/x-javascript',
            'text/x-json',
            'text/json'
        );
	    if(!isset($_SERVER['CONTENT_TYPE']) || !in_array($_SERVER['CONTENT_TYPE'],$types)){
            if($_SERVER['REQUEST_URI'] != '/Documentation'){
                header('Location: /Documentation');
                die();
            }
             return true;
        }


		$apiKey = isset($_COOKIE["API_KEY"]) ? $_COOKIE["API_KEY"] : ( isset($_GET["API_KEY"]) ? $_GET["API_KEY"] : ( isset($_POST["API_KEY"]) ? $_POST["API_KEY"] : false));
		if(!$apiKey){
            $data = json_decode(file_get_contents("php://input"));
            if(isset($data->API_KEY)) $apiKey = $data->API_KEY;
        }
		$exists = false;
		if($apiKey)
            $exists = Sys::getOneData('Systeme','ApiKey/Key='.$apiKey);

		if(!$exists) {
            header("Content-type: text/json; charset=".CHARSET_CODE."");
            header("Accept-Ranges:bytes");
            header("Access-Control-Allow-Headers:Origin, Accept, Content-Type, X-Requested-With, X-CSRF-Token");
            header("Access-Control-Allow-Methods:GET, POST, PUT, DELETE, PATCH");
            header("Access-Control-Allow-Origin: *");
            http_response_code (401);
            die(json_encode(array('success'=>false,'error'=>'invalid_credentials','error_description'=>'Clef Api invalide')));
			return false;
        }

        $t=false;
        if (isset($_COOKIE["AUTH_TOKEN"])&& $_COOKIE["AUTH_TOKEN"]!="" ) {
            $this->SessId  = $_COOKIE["AUTH_TOKEN"];
            $t=true;
        }
        if (isset($_POST["AUTH_TOKEN"])&& $_POST["AUTH_TOKEN"]!="" ) {
            $this->SessId  = $_POST["AUTH_TOKEN"];
            $t=true;
        }
        if (isset($_GET["AUTH_TOKEN"])&& $_GET["AUTH_TOKEN"]!="" ) {
            $this->SessId  = $_GET["AUTH_TOKEN"];
            $t=true;
        }
        if(!$t){
                $data = array();
                parse_str(file_get_contents("php://input"),$data);
                if(isset($data['AUTH_TOKEN'])){
                    $this->SessId  = $data["AUTH_TOKEN"];
                    $t=true;
				}
		}
		if(!$t){
			$data = json_decode(file_get_contents("php://input"),true);
			if(is_array($data) && isset($data['AUTH_TOKEN'])){
				$this->SessId  = $data["AUTH_TOKEN"];
				$t=true;
			}
		}
        return $t;
	}

	//------------------------------------------------------------//
	//			SESSION AUTH			      //
	//------------------------------------------------------------//
	//Initialisation de la session prive
	function DemarreAuthSession() {
		//On recupere le numero de l'utilisateur
		$this->configAuthSession(Sys::$Session->Utilisateur);
		//On enregistre le numero de session
		Sys::$Session->Session = $this->SessId;
		session_id($this->SessId);
		if(empty($_SESSION))
			session_start();
	}
	//Chargement des variables de la session prive
	function ChargementAuthVars() {
		if (!is_null($_SESSION["UserCache"]) && USER_CACHE){
			Sys::$User = unserialize($_SESSION["UserCache"]);
		}
		foreach ($_SESSION as $Key=>$Value){

			if (is_string($Value)) {
			    $data = @unserialize($Value);
               		    if($data !== false)
                    		$GLOBALS["Systeme"]->RegisterVar($Key,unserialize($Value));
                	    else
                    		$GLOBALS["Systeme"]->RegisterVar($Key,$Value);
            		}
			if (is_object($Value)||is_array($Value)){
				$GLOBALS["Systeme"]->RegisterVar($Key,$Value);
			}
		}
		$this->login = $GLOBALS["Systeme"]->RegVars["login"];
		if (isset($GLOBALS["Systeme"]->RegVars["pass"]))$this->pass = $GLOBALS["Systeme"]->RegVars["pass"];
		if (isset($GLOBALS["Systeme"]->RegVars["kerberosAuth"]))$this->kerberosAuth = $GLOBALS["Systeme"]->RegVars["kerberosAuth"];
		$GLOBALS["Systeme"]->Log->log("USER KRB:".$this->kerberosAuth."=>  ".$GLOBALS["Systeme"]->RegVars["login"]);
		return true;
	}
	//Sauvegarde des variables de la session prive
	function SaveAuthVars() {
		$this->addSessionVar("login",$this->login);
		if (is_null($this->passmd5)) $this->addSessionVar("pass",$this->pass);
		else $this->addSessionVar("pass",$this->passmd5);
		$this->addSessionVar("UserCache",serialize(Sys::$User));
	}
	//Configuratino de la session prive
	function ConfigAuthSession($Id) {
		session_set_cookie_params(CONNECT_TIMEOUT);//  (864000000);
		//Configuration de la session
		if ($Id==NULL) return false;
		session_name(PHP_SESSION_NAME);
		//Emplacement de sauvegarde du fichier de session
		$Path = ROOT_DIR."/Home/".$Id."/.sessions";
		if (!file_exists($Path)) Root::mk_dir($Path,0755);
		session_save_path($Path);
	}
	//Generation du numero de session prive
	function genSessId($login,$pass,$id) {
		$temp = md5($login."-".$pass);
		$temp = "dddd".$id."dddd".$temp;
		$sessid = $temp;
		session_id($sessid);
		Sys::$Session->Set("Session",$sessid);
		$this->SessId = $sessid;
		return $sessid;
	}
	//Destruction de la session prive
	function DestroyAuth() {
		$GLOBALS["Systeme"]->isLogged=0;
		$_SESSION = array();
		if (isset($_COOKIE[PHP_SESSION_NAME])) {
			setcookie(PHP_SESSION_NAME, '', time()-42000, '/');
		}
		$this->SessId = "";
		$this->Utilisateur = "";
		return true;
	}

	//------------------------------------------------------------//
	//			SESSION PHP			      //
	//------------------------------------------------------------//
	//Demarrage de la session publique
	function initPublicSession() {
 		if (!$this->SessionStarted){
			session_start();
		}
		$this->SessId = session_id();
		$this->ChargementSessionVars();
		$this->SessionStarted = true;
	}
	//Configuration de la session publique
	function ConfigPublicSession() {
		session_set_cookie_params  (864000000);
		//Configuration de la session
		session_name("PHPSESSID");
		//Emplacement de sauvegarde du fichier de session
		$Path = ROOT_DIR."/Home/".Sys::$User->Id."/.sessions";
		if (!file_exists($Path)) Root::mk_dir($Path,0755);
		session_save_path($Path);
	}
	//Sauvegarde des variables de la session publique
	function SaveSessionVars() {
		$this->addSessionVar("login",$this->login);
		if ($this->kerberosAuth)$this->addSessionVar("kerberosAuth",true);
		else{
			if (is_null($this->passmd5)) $this->addSessionVar("pass",$this->pass);
			else $this->addSessionVar("pass",$this->passmd5);
		}
		$this->addSessionVar("UserCache",Sys::$User);
	}
	//Chargement des variables de la session publique
	function ChargementSessionVars() {
		//On met les variables de session en variable globale
		if (Session::is("UserCache") && USER_CACHE){
			Sys::$User = unserialize($_SESSION["UserCache"]);
		}
//		if ($_SESSION["login"]=="")return false;
		foreach ($_SESSION as $Key=>$Value){
			if (is_string($_SESSION[$Key])&&$Value!='')
			{
				$data = @unserialize($Value);
				if($data !== false)
					$GLOBALS["Systeme"]->RegisterVar($Key,unserialize($Value));
				else
					$GLOBALS["Systeme"]->RegisterVar($Key,$Value);
			}
		}
		if (isset($GLOBALS["Systeme"]->RegVars["kerberosAuth"]))$this->kerberosAuth =$GLOBALS["Systeme"]->RegVars["kerberosAuth"];
		if (isset($GLOBALS["Systeme"]->RegVars["login"]))$this->login =$GLOBALS["Systeme"]->RegVars["login"];
		if (isset($GLOBALS["Systeme"]->RegVars["pass"]))$this->pass = $GLOBALS["Systeme"]->RegVars["pass"];
		return true;
	}
	//Transfert des variables de la session publique vers la session prive
	function SessionPublicToPrivate() {
		@session_start();
		$OldSession = $_SESSION;
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-42000, '/');
		}
		session_destroy();
		setcookie ("PHPSESSID", "", time()-60000);
		$this->SessId = $this->genSessId($this->login,$this->pass,Sys::$Session->Id);
		//Creation Session
		$this->DemarreAuthSession();
		$_SESSION = $OldSession;
		$this->SaveSessionVars();
	}
	//Ajoute une variable a la session
	function addSessionVar($Nom,$Valeur){
		if (!$this->DetectSessions()&&Sys::$User->Public){
			//Creation de la session
			$this->initPublicSession();
			Sys::$Session->Session = $this->SessId;
			Sys::$Session->Save();
		}
		$_SESSION[$Nom] = serialize($Valeur);
		$GLOBALS["Systeme"]->RegisterVar($Nom,$Valeur);
	}
	//Supprime une variable de la session
	function rmSessionVar($Nom) {
		if (isset($_SESSION[$Nom]))unset($_SESSION[$Nom]);
	}
	//Methode publique avec balise pour la gestion avec keml
	function Connect($Login,$Pass){
		$this->login = $Login;
		$this->pass = md5($Pass);
		$this->clearpass = $Pass;
        $GLOBALS["Systeme"]->Log->log("CONNECT FROM CODE");
		if ($this->DetectUser() ){
			$this->SessionPublicToPrivate();
		}
		//On enregistre les modifications de la connexion
		Sys::$Session->Save();
	}
	//Destruction de la session prive
	function DestroySess() {
		if (isset($_COOKIE["PHPSESSID"])) {
			setcookie("PHPSESSID", '', time()-42000, '/');
		}
		return true;
	}
	function resetSessionVars() {
		$this->rmSessionVar("UserCache");
		//$this->addSessionVar("UserCache",Sys::$User);
	}
	//------------------------------------------------------------//
	//			VARIABLES KOB-EYE		      //
	//------------------------------------------------------------//
	//Recuperation des variables pouvant correspondre au login.
	function LoadLoginVars() {
		if (defined("EXTERNAL_AUTH_AD")&&EXTERNAL_AUTH_AD&&isset($_SERVER["PHP_AUTH_USER"])&&!isset($_SERVER["PHP_AUTH_PW"])){
			//CAS DU TICKET KERBEROS WINDOWS
			$l = explode("@",$_SERVER["PHP_AUTH_USER"]);
			$this->login = $l[0];
			$this->kerberosAuth = true;
			$GLOBALS["Systeme"]->Log->log("KERBEROS AUTH ".$_SERVER["PHP_AUTH_USER"]." => USER ".$l[0]);
		}elseif (defined("EXTERNAL_AUTH_AD")&&EXTERNAL_AUTH_AD&&isset($_SERVER["PHP_AUTH_USER"])&&isset($_SERVER["PHP_AUTH_PW"])){
			//CAS KERBEROS SANS TICKET WINDOWS
            $this->login = $_SERVER["PHP_AUTH_USER"];
            $this->pass = md5($_SERVER["PHP_AUTH_PW"]);
            $this->clearpass = $_SERVER["PHP_AUTH_PW"];
		}elseif ($GLOBALS["Systeme"]->getPostVars("login")!=""){
			//Ils sont dans les parametres Post
			$this->login = $GLOBALS["Systeme"]->getPostVars("login");
			$this->pass = md5($GLOBALS["Systeme"]->getPostVars("pass"));
			$this->clearpass = $GLOBALS["Systeme"]->getPostVars("pass");
			return true;
		}elseif (defined("ALLOW_GET_CONNECTION")&&ALLOW_GET_CONNECTION&&$GLOBALS["Systeme"]->getGetVars("login")!=""&&($GLOBALS["Systeme"]->getGetVars("pass")!=""||$GLOBALS["Systeme"]->getGetVars("passmd5")!="")){
			//Ils sont dans les parametres Get
			$this->login = $GLOBALS["Systeme"]->getGetVars("login");
			$this->pass = ($GLOBALS["Systeme"]->getGetVars("pass")=="") ? $GLOBALS["Systeme"]->getGetVars("passmd5") : md5($GLOBALS["Systeme"]->getGetVars("pass"));
			$this->clearpass = $GLOBALS["Systeme"]->getGetVars("pass");
			return true;
		}elseif ($GLOBALS["Systeme"]->getGetVars("login")!=""&&$GLOBALS["Systeme"]->getGetVars("passmd5")!=""&&$GLOBALS["Systeme"]->getGetVars("codeverif")!=""){
			$this->login = $GLOBALS["Systeme"]->getGetVars("login");
			$this->passmd5 = $GLOBALS["Systeme"]->getGetVars("passmd5");
			$this->codeverif = $GLOBALS["Systeme"]->getGetVars("codeverif");
			return true;
		}elseif (isset($_SESSION['login'])&&$_SESSION['login'] && $_SESSION['pass']){
        		$this->login = $_SESSION['login'];
		       	$this->pass = $_SESSION['pass'];
		}elseif (isset($_SERVER["PHP_AUTH_USER"])&&isset($_SERVER["PHP_AUTH_PW"])){
			$this->login = $_SERVER["PHP_AUTH_USER"];
			$this->pass = md5($_SERVER["PHP_AUTH_PW"]);
			$this->clearpass = $_SERVER["PHP_AUTH_PW"];
        }elseif (is_object(json_decode(file_get_contents('php://input')))){
			$data = json_decode(file_get_contents('php://input'));
			if (isset($data->login)) {
                $this->login = $data->login;
                $this->pass = md5($data->pass);
            }
        }else{
			if ($this->login==""||$this->pass=="")	{
                $GLOBALS["Systeme"]->Error->Set('Connexion','Veuillez vérifiez vos identifiants. Login et/ou mot de passe manquants ou érronés.',5);
                return false;
			}
		}

	}

	//------------------------------------------------------------//
	//			CONNEXION			      //
	//------------------------------------------------------------//
	//Detection automatique de la langue
	function DetectLanguage() {
		$LangueSite=Sys::$Session->LangueSite;
		//Detection automatique de la langue
		$Temp = $GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE");
		foreach ($Temp as $Tit=>$Lang){
			if (isset($Lang["DEFAULT"])&&$Lang["DEFAULT"]){
				$LangueDefault=$Lang["TITLE"];
			}elseif (@preg_match("#^".$Lang["NAV"].".*$#",$this->Langue,$out)){
				$LangueSite = $Lang["TITLE"];
			}
		}
		if (empty($LangueSite) && isset($LangueDefault)) {$LangueSite = $LangueDefault;}
		//Detection langue par defaut de l'utilisateur
		if (!empty(Sys::$User->Langue)){
			//Recherche de la langue
			$Temp = $GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE::".Sys::$User->Langue);
			$LangueSite = $Temp["TITLE"];
		}
		//Detection langue force
		if (isset($GLOBALS["Systeme"]->GetVars["SwitchLanguage"])) {
			//On verifie que l extension existe
			$Temp = $GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE");
			foreach ($Temp as $Tit=>$Lang) if ($Lang["TITLE"]==$GLOBALS["Systeme"]->GetVars["SwitchLanguage"]){
				$LangueSite = $GLOBALS["Systeme"]->GetVars["SwitchLanguage"];
			}
		}
		if (!empty($LangueSite)&&Sys::$Session->LangueSite!=$LangueSite) Sys::$Session->LangueSite = $LangueSite;

		//Verification de la définition dans la connexion
		$GLOBALS["Systeme"]->CurrentLanguage = Sys::$Session->LangueSite;
	}

	//Recherche connexion
	function GetConn() {
		$GLOBALS["Chrono"]->start("GET Connexion");
		//Recuperation de l adresse IP
		$this->Ip=Sys::$remote_addr;
		//Recuperation du nom de l hote
		//$this->Host=@gethostbyaddr(Sys::$remote_addr);
		//Langage de l hote
		$this->Navigateur=str_replace("/","",utf8_decode(Sys::$user_agent));
		$this->Navigateur=str_replace("'","",$this->Navigateur);
		$this->Navigateur=str_replace('"',"",$this->Navigateur);
		$this->Navigateur=str_replace("&","",$this->Navigateur);
		$this->Navigateur=str_replace(";","",$this->Navigateur);
		$this->Navigateur=str_replace("+","",$this->Navigateur);
		$this->Navigateur=str_replace("=","",$this->Navigateur);
		$this->Navigateur=str_replace("`","",$this->Navigateur);
		$this->Navigateur=str_replace("{","",$this->Navigateur);
		$this->Navigateur=str_replace("}","",$this->Navigateur);
		$this->Navigateur=str_replace("(","",$this->Navigateur);
		$this->Navigateur=str_replace(")","",$this->Navigateur);
		$this->Navigateur=str_replace("[","",$this->Navigateur);
		$this->Navigateur=str_replace("]","",$this->Navigateur);
		$this->Navigateur = htmlspecialchars($this->Navigateur);
		if (isset($_SERVER["HTTP_REFERER"]))$this->Ref = addslashes($_SERVER["HTTP_REFERER"]);
		//Langage de l hote
		$this->Langue=getenv("HTTP_ACCEPT_LANGUAGE");
		//Generation de la date
		$this->Date = $this->initDate();
		//Recherche de la connexion
		if (ADD_CONNECT){
			if ($this->SessId!=""){
				//Recuperation d'une connexion prive
				$query="Systeme/Connexion/Session=".$this->SessId;
                $Results = Sys::$Modules['Systeme']->callData($query,false,0,1);
				if (isset($Results[0])&&is_array($Results[0])){
					//Connexion existante
					$GLOBALS["Systeme"]->Log->log("RECUP CONNEXION ".$this->SessId);
					Sys::$Session = genericClass::createInstance("Systeme",$Results[0]);
					if (preg_match("#\/(.*)\.[^\.]*?$#",Sys::$Session->LastUrl,$Out))
						$this->LastUrl = $Out[1];
					else $this->LastUrl = Sys::$Session->LastUrl;
				}else{
					//Destruction de la session et deconnexion
					$GLOBALS["Systeme"]->Log->log("PERTE CONNEXION ",$this->SessId);
					$this->DisconnectInPlace();
					$this->SessId="";
				}
			}
			if (!isset(Sys::$Session)){
				//Recuperation d'une connexion publique
				$query="Systeme/Connexion/Langue=".$this->Langue."&Domaine=".Sys::$domain."&Navigateur=".addslashes($this->Navigateur)."&Ip=".$this->Ip."&Session=";
				$Results = Sys::$Modules['Systeme']->callData($query,false,0,1);
				if (isset($Results[0])&&is_array($Results[0])){
					//Connexion existante
					Sys::$Session = genericClass::createInstance("Systeme",$Results[0]);
				}
			}
			//Creation d'une nouvelle connexion
			if (!isset(Sys::$Session)){
				//Creation de la connexion
				Sys::$Session = genericClass::createInstance("Systeme","Connexion");
				if (isset($this->Ref))Sys::$Session->Set("Referent",$this->Ref);
				Sys::$Session->Set("FirstUrl",Sys::$link);
				Sys::$Session->Set("Domaine",Sys::$domain);
				Sys::$Session->Set("Ip",$this->Ip);
				Sys::$Session->Set("Langue",$this->Langue);
				Sys::$Session->Set("Host",$this->Host);
				Sys::$Session->Set("Mobile",$this->Mobile);
				Sys::$Session->Set("Navigateur",$this->Navigateur);
			}
			//Enregistrement de la session
			Sys::$Session->Save();
			//Configuration de la connexion
			Sys::$Session->Set("Session",$this->SessId);
			//Sys::$Session->Set("LastUrl",$_SERVER["REQUEST_URI"]);
		}
		$GLOBALS["Chrono"]->stop("GET Connexion");
		return Sys::$Session;
	}

	function close () {
		//On genere le cache
		if (USER_CACHE){
			if($this->_dirtyCache){
				$this->writeCacheFile(serialize(Sys::$User),"Home/".Sys::$User->Id."",".UserCache.".$GLOBALS["Systeme"]->CurrentLanguage);
			}elseif (!Sys::$User->Public){
				$this->addSessionVar("UserCache",Sys::$User);
			}
		}
		if (ADD_CONNECT){
			if(defined('LOG_CONNECTION')&&LOG_CONNECTION)Sys::$Session->Logs.=date('d/m/Y H:m:i',time())." :: ".Sys::$Session->LastUrl."\r\n";
			Sys::$Session->Save();
			//On supprime les connexions perimees sauf les session mobiles
			$query="Systeme/Connexion/tmsEdit<".(time()-CONNECT_TIMEOUT."&Mobile=0");
			$Results = Sys::$Modules['Systeme']->callData($query,false,0,5000);
			if ($Results)for ($i=0;$i<sizeof($Results);$i++) {
				$Connexion = genericClass::createInstance("Systeme",$Results[$i]);
				$Connexion->Delete();
			}
		}
	}

	//------------------------------------------------------------//
	//			USERS KOB-EYE			      //
	//------------------------------------------------------------//
	//Connexion d un utilisateur par un id ou par un tableau
	static function initUser($Tab) {
		//Initialisation du genericClass
		if (is_object($Tab)) {
			//C est un objet
			$User = $Tab;
		}else{
			//C est un int
            $User = Sys::getOneData('Systeme', 'User/'.$Tab);
			if (!$User){
                KError::fatalError("L'utilisateur par défaut est introuvable. Veuillez modifier la configuration.");
                die("L'utilisateur par défaut est introuvable. Veuillez modifier la configuration.");
                $User = genericClass::createInstance('Systeme','User');
            }
		}
		$User->Public=0;
		$User->initUserVars();

		//PGF Sys::$User = $User;
		return $User;
	}
	function DetectUser() {
		//Detection de l utilisateur
		if (!is_null($this->login)) {
			if (!is_null($this->passmd5)&&!is_null($this->codeverif)){
				//CAS LOGIN JETON MD5
				$Result = Sys::$Modules["Systeme"]->callData("User/Login=".$this->login."&Pass=".$this->passmd5."&CodeVerif=".$this->codeverif);
                if (!is_array($Result[0])) $Result = Sys::$Modules["Systeme"]->callData("User/Login=".$this->login."&Pass=[md5]".$this->passmd5."&CodeVerif=".$this->codeverif);
                if (!isset($Result[0])||!is_array($Result[0])) {
                    //Cas ou le mot de passe a été transformé alors que le champ en base comptait encore 32 caractères
                    $Result = Sys::$Modules["Systeme"]->callData("User/Login=" . $this->login . "&Pass=[md5]" . substr($this->pass, 0, -5));
                }
                $GLOBALS["Systeme"]->Log->log("DETECT USER >> PASSMD5 GET ".$Result[0]["Id"]);
				if (!is_array($Result[0])) {
					$GLOBALS["Systeme"]->Log->log('XXXXXXXXXXXXXX BAD PASSSWORD');
					return false;
				}
				$User = genericClass::createInstance("Systeme",$Result[0]);
				$User->Save();
			}elseif (is_object(Sys::$User) && (!isset(Sys::$User->Public)||!Sys::$User->Public)){
				$GLOBALS["Systeme"]->Log->log("DETECT USER >> SESSION CACHE ".Sys::$User->Id);
				$this->DetectLanguage();
				//CAS RECUP CACHE SESSION
				$User = Sys::$User;
				Sys::$Session->Utilisateur = $User->Id;
				//Detection langue force
				if (isset($GLOBALS["Systeme"]->GetVars["SwitchLanguage"])) {
					Sys::$User = $this->initUser($User);
					$L = $GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE");
					if (is_array($L))foreach ($L as $k=>$la)if ($la["TITLE"]==$GLOBALS["Systeme"]->CurrentLanguage){
						$User->Langue = $k;
						$User->Save();
					}
					$this->resetSessionVars();
					$this->SaveSessionVars();
				}
				return true;
			}else{
			  	$GLOBALS["Systeme"]->Log->log("DETECT USER KRB:".$this->kerberosAuth.">> POST Login=".$this->login);
				//CAS LOGIN PASS POST
				if ($this->kerberosAuth)
					$Result = Sys::$Modules["Systeme"]->callData("User/Login=".$this->login);
				else {
                    $Result = Sys::$Modules["Systeme"]->callData("User/Login=" . $this->login . "&Pass=" . $this->pass);
                    if (!isset($Result[0])||!is_array($Result[0])) {
                    	//Cas ou le mot de passe est deja trransformé
                        $Result = Sys::$Modules["Systeme"]->callData("User/Login=" . $this->login . "&Pass=[md5]" . $this->pass);
                        if (!isset($Result[0])||!is_array($Result[0])) {
                            //Cas ou le mot de passea été transformé alors que le champ en base comptait encore 32 caractères
                            $Result = Sys::$Modules["Systeme"]->callData("User/Login=" . $this->login . "&Pass=[md5]" . substr($this->pass, 0, -5));
                        }
                    }

                    klog::l('DETECT USER',"User/Login=" . $this->login . "&Pass=" . $this->pass);
                }
				if (!isset($Result[0])||!is_array($Result[0])){
					//CONNEXION AD EXTERNAL + AUTO PROVISIONNING
					if (!defined("EXTERNAL_AUTH_AD")||!EXTERNAL_AUTH_AD){
                        $GLOBALS["Systeme"]->Error->Set('Connexion','Veuillez vérifiez vos identifiants. Login et/ou mot de passe manquants ou érronés.',5);
						$GLOBALS["Systeme"]->Log->log('XXXXXXXXXXXXXX BAD IDENTIFIERS');
						return false;
                    }
					elseif (defined("EXTERNAL_AUTH_AD")){
						$adldap = $this->externalLdapConnect();
						//authenticate the user
						if ($this->kerberosAuth||$adldap->authenticate($this->login, $this->clearpass)){
							//SUCCESFULL LOGIN
							$Result = Sys::$Modules["Systeme"]->callData("User/Login=".$this->login);
							//AUTO PROVISIONNING
							if (!is_array($Result)||!sizeof($Result)){
								//recupération des informations de l'utilisateur
								$INFO = $adldap->user()->info($this->login);
								$t = explode(",",$INFO[0]["displayname"][0]);
								$prenom = trim($t[1]);
								$nom = trim($t[0]);
								$t = explode("/",$INFO[0]["physicaldeliveryofficename"][0]);
								$bureau = trim($t[1]);
								$pays = trim($t[0]);
								//test de l'existence du compte par l'email
								$ru = Sys::$Modules["Systeme"]->callData("User/Mail=".$INFO[0]["mail"][0]);
								if (is_array($ru)&&sizeof($ru)&&!empty($INFO[0]["mail"][0])){
									$u = genericClass::createInstance("Systeme",$ru[0]);
									$u->Commentaire .= date('d/m/Y')." - Account restored by email ".$INFO[0]["mail"][0]."\r\n";
								}else{
									$u = genericClass::createInstance("Systeme","User");
									$u->Commentaire .= date('d/m/Y')." - Account initialized by active directory ".$this->login."\r\n";
								} 
								
								$GLOBALS["Systeme"]->Log->log("CREATE USER",$INFO);
								$u->Set("Login",$this->login);
								$u->Set("Pass",$this->clearpass);
								$u->Set("ExternalAuth",1);
								$u->Set("Mail",$INFO[0]["mail"][0]);
								$u->Set("Nom",$nom);
								$u->Set("Prenom",$prenom);
								$u->Set("Style","rcv2.swf");
								$u->Set("Pays",$pays);
								$u->Set("Ville",$bureau);
								$u->Set("Langue","EN");
								$GLOBALS["Systeme"]->Log->log("CREATE USER",$u);
								//recherche du groupe associé
								$gr = Sys::$Modules["Systeme"]->callData("Group/Role.RoleId(Title=USER)");
								if (is_array($gr)&&sizeof($gr)){
									//recherche groupe des utilisateurs
									$gr = genericClass::createInstance("Systeme",$gr[0]);
									$GLOBALS["Systeme"]->Log->log("GROUPE USER",$gr);
									//recherche groupe du pays
									$grp = Sys::$Modules["Systeme"]->callData("Group/".$gr->Id."/Nom=".$pays);
									if (is_array($grp)&&sizeof($grp)){
										//recherche groupe des utilisateurs
										$grp = genericClass::createInstance("Systeme",$grp[0]);
										$GLOBALS["Systeme"]->Log->log("GROUPE USER $pays",$grp);
									}else{
										//le groupe pays n'existe pas
										//creation du groupe pays USER
										$grp = genericClass::createInstance("Systeme","Group");
										$grp->Set("Nom",$pays);
										$grp->AddParent($gr);
										$grp->Save();
									}
									//association de l'utilisateur au pays
									$u->addParent($grp);
								}
								$u->Save();
								$User=$u;
							}else{
								$User = genericClass::createInstance("Systeme",$Result[0]);
								//mise à jour du mot de passe
								$User->Set("Pass",$this->clearpass);
								$User->Save();
							} 
						}else{
							$GLOBALS["Systeme"]->Log->log("AD : USER ".$this->login." ".$this->clearpass." ".$this->pass." FAILED");
							$GLOBALS["Systeme"]->Log->log('XXXXXXXXXXXXXX AD FAIL');
							return false;
						} 
					}
				}else{
					$User = genericClass::createInstance("Systeme",$Result[0]);
					if (defined('EXTERNAL_AUTH_AD')&&EXTERNAL_AUTH_AD&&isset($User->ExternalAuth)&&$User->ExternalAuth&&isset($this->clearpass)&&!empty($this->clearpass)&&!$this->kerberosAuth){
						//vérification du mot de passe
						$adldap = $this->externalLdapConnect();
						if (!$adldap->authenticate($this->login, $this->clearpass)) {
							$GLOBALS["Systeme"]->Log->log('XXXXXXXXXXXXXX LDAP FAIL');
							return false;
						}
					}
				}	
			}
			//On verifie le mot de passe dans  la classe generique
			if (isset($User)&&is_object($User)&&$User->Actif&&(!$User->DateExpiration||$User->DateExpiration>=time())) {
				Sys::$User = $this->initUser($User);
				Sys::$Session->Utilisateur = $User->Id;
				if (isset($GLOBALS["Systeme"]->GetVars["SwitchLanguage"])) {
					$L = $GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE");
					if (is_array($L))foreach ($L as $k=>$la)if ($la["TITLE"]==$GLOBALS["Systeme"]->CurrentLanguage){
						$User->Langue = $k;
						$User->Save();
					}
					$this->resetSessionVars();
					$this->SaveSessionVars();
				}
				$GLOBALS["Systeme"]->Log->log("LOGIN USER ".$this->login." OK");
				return true;
			}else{
				if(!$User->Actif) $GLOBALS["Systeme"]->Error->Set('Connexion','Cet utilisateur n\'est pas activé. Si le problème persiste, n\'hésitez pas à contacter l\'administrateur du site.');
				//OK donc il existe une session mais les dientifiants de connexion sont erron�s donc il faut detruire la session
// 				$GLOBALS["Systeme"]->Error->sendErrorMsg(100);
				$this->destroyAuth();
			}
		}else 
			$GLOBALS["Systeme"]->Log->log('XXXXXXXXXXXXXX NO LOGIN PROVIDED');
		return false;
	}


	function externalLdapConnect(){
		include (dirname(__FILE__) . "/../Lib/adldap/adLDAP.php");
		try {
			$GLOBALS["Systeme"]->Log->log("TEST AD AUTH");
		    $adldap = new adLDAP(Array(
				"account_suffix"=>AD_ACCOUNT_SUFFIX,
				"domain_controllers"=>Array(AD_DOMAIN_CONTROLLER),
				"admin_username"=>AD_ADMIN_USERNAME,
				"admin_password"=>AD_ADMIN_PASSWORD,
				"use_ssl"=>AD_SSL,
				"use_tls"=>AD_TLS,
				"base_dn"=>AD_BASE_DN
			));
		}
		catch (adLDAPException $e) {
		   $GLOBALS["Systeme"]->Log->log("AD ERROR",$e);
		   echo $e;
		    exit();   
		}
		return $adldap;
	}

	function initDefaultUser() {
		Sys::$User = "";
		$GLOBALS["Chrono"]->start("INIT DEFAULT USER");
		//$Query,$recurs="",$Ofst="",$Limit="",$OrderType="",$OrderVar="",$Selection=""
		$UA = array(
			'Mobile' => '/Mobile|Mini/',
			'Desktop' => '/*/'
		);
		$defaultUserFound = false;
		$Results = Sys::$Modules["Systeme"]->callData("Site/Domaine=".Sys::$domain,false,0,100,"DESC","UserAgent","UserId,Id,UserAgent");
		if(is_array($Results)) foreach($Results as $Result) {
			if(!$defaultUserFound && (empty($Result['UserAgent']) || preg_match($UA[$Result['UserAgent']], Sys::$user_agent))) {
				$defaultUserFound = true;
				//On verifie quil nexiste pas deja un fichier cache pour lutilisateur
				if (USER_CACHE && $Result["UserId"]>0 && file_exists("Home/".$Result["UserId"]."/.UserCache.".$GLOBALS["Systeme"]->CurrentLanguage)){
					$GLOBALS["Systeme"]->Log->log("INIT DEFAULT USER >> LOAD CACHE DEFAULT USER DOMAINE ".Sys::$domain."  ".((isset($Result["UserId"]))?$Result["UserId"]:""));
					Sys::$User = unserialize(file_get_contents("Home/".$Result["UserId"]."/.UserCache.".$GLOBALS["Systeme"]->CurrentLanguage));
					//if ($Result[0]["CodeVerif"]!=Sys::$User->CodeVerif&&$Result[0]["CodeVerif"]!="")Sys::$User = "";
				}
				if (!is_object(Sys::$User)&&isset($Result["UserId"])){
					$GLOBALS["Systeme"]->Log->log("INIT DEFAULT USER >> INIT DEFAULT USER DOMAINE ".Sys::$domain."  ".((isset($Result["UserId"]))?$Result["UserId"]:""));
					Sys::$User = $this->initUser($Result["UserId"]);
					$this->_dirtyCache = true;
				}
			}
		}
		if(!$defaultUserFound) {
                    
			if (file_exists("Home/".MAIN_USER_NUM."/.UserCache.".$GLOBALS["Systeme"]->CurrentLanguage) && USER_CACHE){
				$GLOBALS["Systeme"]->Log->log("INIT DEFAULT USER >> LOAD CACHE DEFAULT USER MAIN_USER_NUM ".MAIN_USER_NUM);
				Sys::$User = unserialize(file_get_contents("Home/".MAIN_USER_NUM."/.UserCache.".$GLOBALS["Systeme"]->CurrentLanguage));
			}
			if (!is_object(Sys::$User)){
				$GLOBALS["Systeme"]->Log->log("INIT DEFAULT USER >> INIT DEFAULT USER MAIN_USER_NUM ".MAIN_USER_NUM);
				Sys::$User = $this->initUser(MAIN_USER_NUM);
				//On genere le cache
				$this->writeCacheFile(serialize(Sys::$User),"Home/".MAIN_USER_NUM."",".UserCache.".$GLOBALS["Systeme"]->CurrentLanguage);
			}
 		}
		$GLOBALS["Chrono"]->stop("INIT DEFAULT USER");
		Sys::$User->Public = 1;
		Sys::$Session->Utilisateur = Sys::$User->Id;

	}

	function writeCacheFile($Data,$Url,$Name) {
		$GLOBALS["Systeme"]->Log->log("CACHE >> WRITE CACHE  ".$Url."/".$Name);
		if (!$File=@fopen ($Url."/".$Name,"w")){
			Root::mk_dir($Url);
			$File=fopen ($Url."/".$Name,"w");
		}
		fwrite($File,$Data);
		fclose($File);
	}

	function isLogged() {
		if ($this->SessId!="") return true;
		else return false;
	}
	//Deconnexion sans redirection
	function DisconnectInPlace(){
		$GLOBALS["Systeme"]->isLogged=0;
		if ($this->DetectSess()) $this->DestroySess();
		if ($this->DetectAuth()) $this->DestroyAuth();
		$GLOBALS["Systeme"]->Log->log("*** DECONNEXION *** ");
		if (is_object(Sys::$Session))Sys::$Session->Delete();
		$this->Disconnected = true;
		return;
	}
	//Deconnexino et redirection vers l'accueil
	function Disconnect() {
		$GLOBALS["Systeme"]->isLogged=0;
		if ($this->DetectSess()) $this->DestroySess();
		if ($this->DetectAuth()) $this->DestroyAuth();
		$GLOBALS["Systeme"]->Log->log("*** DECONNEXION *** ");
		if (is_object(Sys::$Session))Sys::$Session->Delete();
		$this->Disconnected = true;
		header("Location: http://".Sys::$domain."/");
		die();
		//return;
	}

	//------------------------------------------------------------//
	//			UTILS				      //
	//------------------------------------------------------------//

	function initDate() {
		//Fonction d initialisation de la date
		$TempDate=date("YmdHis");
		return $TempDate;
	}
	static function recursivMenu($Menus,$Parent=null){
		$R = array();
		if (is_array($Menus))foreach ($Menus as $K=>$M) {
			$R[$K] = genericClass::createInstance("Systeme",$M);
			if ($Parent)
				$R[$K]->MenuParent = array($Parent);
			if (is_array($M["Menus"]))
				$R[$K]->Menus = Connection::recursivMenu($M["Menus"],$R[$K]);
		}
		return $R;
	}
	/**
	 * startConnexion
	 * initialize Connexio and User from connexion object
	 * @param Object Connexion
	 */
	static function startConnection($o) {
		$GLOBALS["Systeme"]->Connection->SessId = $o->Session;
		Sys::$Session = $o;
        $GLOBALS["Systeme"]->Connection->DemarreAuthSession();
		Sys::$User = $GLOBALS["Systeme"]->Connection->initUser($o->Utilisateur);
	}

}
?>
