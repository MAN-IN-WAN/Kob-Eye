<?php

class Sys extends Root{
	//Requete
	var $type;
	var $Lien;
	var $Query;
	var $PageDefaut;
	static $link;
	static $domain;
	static $remote_addr;
	static $port;
	static $user_agent;
    static $allMenus;
	//Initialisation Coeur
	static $Modules;
	static $BlocLoaded;
	var $Conf;
	var $Header;
	static $Skin;
	static $DefaultSkin;

	//properties
	var $Connection;
	static $Session;
	static $User;
	var $Menus;
	var $Driver;
	//Entete
	var $Description;
	var $Titre;
	var $MotsCles;
	var $Url;
	var $ReplyTo;
	var $Classification;
	var $Process;
	//Variables
	var $GetVars;
	var $PostVars;
	var $RegVars;
	var $FilesVars;
	//Ccaches
	var $menuId;
	var $Db;
	var $Error;
	var $Language;
	var $DefaultLanguage;
	var $CurrentLanguage;
	var $isLogged = 0;
	//Menus
	static $CurrentMenu;
	static $DefaultMenu;
	static $MenusFromUrl;
	//Config
	static $keywordsProcessing = true;
	//Keywords
    static $keywords = array();

    static $FORCE_INSERT = false;
    static $NO_TEMPLATE = false;
    static $REMOVE_COMMENT = true;

//*************//
//***ETAPE 1***//
//*************//
	//***********************************
	//	CONSTRUCTEUR
	//***********************************
	function __construct($link,$domain) {
		Sys::$link = $link;
		Sys::$domain = $domain;
		Sys::$remote_addr = (isset($_SERVER["REMOTE_ADDR"]))?$_SERVER["REMOTE_ADDR"]:"127.0.0.1";
		Sys::$port = (isset($_SERVER["SERVER_PORT"]))?$_SERVER["SERVER_PORT"]:"80";
		Sys::$user_agent = (isset($_SERVER["HTTP_USER_AGENT"]))?$_SERVER["HTTP_USER_AGENT"]:"Unknown browser type";
		date_default_timezone_set('Europe/Paris');
		//Generation de la configuration
 		$this->initConf();
		//Initialisation du systeme de log
		$this->Log = new Klog("Log/Systeme.log");
		$this->Log->log("---------------------------------------- DEPART----------------------------------------");
		$this->Log->log("GET -> ".$link);
		$this->Error = new KError();
		Sys::$DefaultSkin = SHARED_SKIN;
		//Recuperation du tableau des variables POST
		$this->PostVarsToPhp();
		$this->GetVarsToPhp();
		$this->FilesVarsToPhp();
		define('DEBUG_INTERFACE',false);
		define('OBJECTCLASS_CATEGORY_DEFAULT',"none");
	}

//*************//
//***ETAPE 2***//
//*************//
	/***********************************
	* Initialisation et identification
	***********************************/

	function Connect() {
		if (isset($_GET["ACTION"])&&$_GET["ACTION"]=="UPDATE"){
			//$this->connectSQL();
			//$this->connectSQLITE();
			//Intialisation des modules
			$this->initModules();
			//Intialisation des langages
			$this->initLanguages();
			foreach (Sys::$Modules as $K=>$M){
				$M->loadSchema();
				$M->Check();
			}
			die('UPDATE OK');
		}
		//Intialisation des langages
  		$this->initLanguages();
		//Intialisation des modules
  		$this->initModules();
		//Crï¿œtion de la connexion
		$GLOBALS["Chrono"]->start("Connexion");
  		$this->Connection =new Connection();
		$GLOBALS["Chrono"]->stop("Connexion");
  		$this->registerVar("DefaultUser",MAIN_USER_NUM);
  		if (isset(Sys::$User->Skin)) Sys::$Skin=Sys::$User->Skin;
  		if (isset(Sys::$User->Menus)) $this->Menus=Sys::$User->Menus;
  		if (isset(Sys::$User->Access)) $this->Access=Sys::$User->Access;
		//Configuration du Lien
 		$this->configLien();
		if(strtolower($this->Lien) == 'sitemap' && strtolower($this->type) == 'xml') $this->displaySitemap();
		if(strtolower($this->Lien) == 'robots' && strtolower($this->type) == 'txt') $this->displayRobots();
		//gestion des entetes specifiques
		if (isset($_GET["Ke-Url"])){
			header('Ke-Url: '.$this->Lien);
		}
		//post initialisation des modules
		$this->postInitModules();
	}

	//***********************************
	//	Fonction D Initialisation
	//***********************************
	function initConf(){
		$this->Conf = new Conf("Conf/General.conf");
	}

	function connectSQL() {
		if (!isset($this->Db[0])||!is_object($this->Db[0])){
			preg_match('#^(.*?)\:\/\/(.*?)\:(.*)\@(.*?)/(.*)$#',$this->Conf->get("GENERAL::BDD::MYSQL_DSN"),$Out);
			try {
				$this->Db[0] = new PDO($Out[1].':host='.$Out[4].';dbname='.$Out[5],$Out[2],$Out[3],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				$this->Db[0]->query("SET AUTOCOMMIT=0");
				$this->Db[0]->query("START TRANSACTION");
			} catch (PDOException $e) {
				echo 'impossible de se connecter à la abase de donnée';
				print_r($e);
				KError::fatalError('Impossible de se connecter à la base de données !');
			}
		}
	}
	function version($min){
		$version = $this->Db[0]->query('select version()')->fetchColumn();
		$version = mb_substr($version, 0, 6);
		return (version_compare($version, $min) >= 0);
	}
	function connectSQLITE(){
		if ((!isset($this->Db[1])||!is_object($this->Db[1]))&&$this->Conf->get("GENERAL::BDD::SQLITE_FILE")!=""){
			$f = $this->Conf->get("GENERAL::BDD::SQLITE_FILE");
			try {
				$this->Db[1] = new PDO("sqlite:".ROOT_DIR.$f);
				$this->Db[1]->query("PRAGMA synchronous = OFF;");
			}catch(PDOException $e){
				//foreach(PDO::getAvailableDrivers() as $driver){
				//	echo $driver.'<br />';
				//}
				//die($e->getMessage());
				die("Probleme de connexion SQLITE");
			}
		}
	}


	function initModules() {
		//Definition des Modules
		$Temp = $this->Conf->get("MODULE");
		//Recuperation de la configuration des modules par defaut
		if (file_exists('Conf/Schema.default')){
			$schemaDefaut = new xml2array('Conf/Schema.default');
			if (isset($schemaDefaut->Tableau['SCHEMA']['#']['OBJECTCLASS']))$schemaDefaut = $schemaDefaut->Tableau['SCHEMA']['#']['OBJECTCLASS'];
			else $schemaDefaut = Array();
		}else $schemaDefaut = Array();
		foreach ($Temp as $Mod) {
			//Fusion des objectclass
			$T = array_merge_recursive($Mod['SCHEMA']['SCHEMA']['#']['OBJECTCLASS'],$schemaDefaut);
			$Mod['SCHEMA']['SCHEMA']['#']['OBJECTCLASS'] = $T;
			//Configuration du Module
			Sys::$Modules[$Mod["NAME"]] = Module::createInstance($Mod);
			Sys::$Modules[$Mod["NAME"]]->loadSchema();
		}
		foreach ($Temp as $Mod) {
			Sys::$Modules[$Mod["NAME"]]->init();
		}
/*		foreach ($Temp as $Mod) {
			if (SCHEMA_CACHE)Sys::$Modules[$Mod["NAME"]]->saveCache();
		}*/
	}
	/**
	 * postInitModules
	 * Post initilisation des modules après authentification de l'utilisateur.
	 */
	function postInitModules() {
		//Definition des Modules
		foreach (Sys::$Modules as $K=>$M){
			$M->postInit();
		}
	}

	function initLanguages() {
		$Temp = $this->Conf->get("GENERAL::LANGUAGE");
		foreach ($Temp as $Tit=>$Lang) {
			if (isset($Lang["DEFAULT"])&&$Lang["DEFAULT"]){
				 $this->DefaultLanguage = $Lang["TITLE"];
				$this->LangageDefaut = $Lang["TITLE"];
				$this->CurrentLanguage = $Lang["TITLE"];
			}
			//Configuration des langues
			$this->Language[$Lang["TITLE"]] = $Tit;
		}
	}

	/***********************************
	* Configuration des urls par defaut
	***********************************/
	function configLien() {
		//Extraction de la partie significative de l'url
 		$LienOrig = explode("?",Sys::$link);
 		$LienOrig = explode("#",$LienOrig[0]);
		$LienOrig = $LienOrig[0];
		//Suppression des slashs en debut de chaine
		while (substr($LienOrig,0,1)=='/'){
			$LienOrig = explode("/",$LienOrig,2);
			$LienOrig = $LienOrig[1];
		}
		$this->setLink($LienOrig);        
        $proto = (Sys::$port == '443')?"https":"http";
        $this->registerVar("DomaineHttp","http://".Sys::$domain);
	$this->registerVar("DomaineHttps","https://".Sys::$domain);
	$this->registerVar("Domaine",$proto."://".Sys::$domain);

		$this->setDefault($this->Conf->get("GENERAL::SERVER::DEFAULT_LINK"));
		//Test si l url definit un fichier ou un dossier
		if (preg_match("#([A-z\/\-0-9\@\*]+)\.([A-z0-9]+)$#",$LienOrig,$out)) {
			//C un fichier qui est defini
			$this->type=strtolower($out[2]);
		}elseif(preg_match("#([A-z\/\-0-9\@\*\.]+)\.([A-z]+)\.([A-z]+)\.([0-9]+)x([0-9]+)$#",$LienOrig,$out)){
			$this->type=strtolower($out[2]);
			//echo $this->type;
		}else{
			//C un dossier
			$this->type="html";
		}
            
        if ($this->type == 'download') {
            $pos = strrpos($this->Lien, '.');
            if ($pos !== FALSE) {
                $this->type = substr($this->Lien, $pos+1);
                header("Content-Disposition: attachment; filename=\"" . basename($this->Lien) . "\"");
                $this->Lien = substr($this->Lien, 0, $pos);
            }
        }   
        
		//-------------------------------------------------------------------//
		//	SI LIEN VIDE !!!!!!!					     //
		//   ICI GESTION DES CAS PAR DEFAUT !!!!!!!			     //
		//   SI USER A MENU ALORS MENU PAR DEFAUT			     //
		//   SINON URL SKIN PAR DEFAUT					     //
		//		DONC INIT SKIN ICI + LECTURE CONF		     //
		//   SINON PAGE KOB-EYE DEFAUT DANS LA CONF			     //
		//-------------------------------------------------------------------//
		if ($this->Lien=="") {
			$DefaultMenu = $this->getDefaultUserMenu();
			if ($DefaultMenu!=NULL) {
				//Alors on afiche le menu par defaut de l utilisateur
				$this->setQuery($DefaultMenu->Alias);
			}else{
				//Alors il faut definir le lien par defaut de la skin

			}
		}
	}

	function setDefault($Temp) {
		$this->PageDefaut = $Temp;
	}

	//Definie l'url à utiliser
	function setLink($Temp) {
		$Temp=preg_replace("#(.*)(\..*)$#","$1",$Temp);
		$this->Lien = $Temp;
		$this->registerVar("Lien",$Temp);
	}

	//Definie la requete à utiliser
	function setQuery($Temp) {
		$this->Query = $Temp;
		$this->registerVar("Query",$Temp);
	}

	//Recherche le menu par defaut de l'utilisateur
	function getDefaultUserMenu(){
		//Le menu par defaut le l'utilisateur est un menu du premier niveau ou le champ url est nul
		if (isset($this->Menus)&&is_array($this->Menus))foreach ($this->Menus as $M) if ($M->Url=="") return $M;
	}

//*************//
//***ETAPE 3***//
//*************//
	//Declenche l'affichage
	function Affich() {
		$Data= $this->getContent();
		return $Data;
	}
	function setNavCache($t=false){
		header("Last-Modified: " . gmdate('D, d M Y H:i:s', time()-3600) . ' GMT+1');
		header("Expires: " . gmdate('D, d M Y H:i:s', time()+86400) . " GMT+1");
		header("Cache-Control: max-age=3600, must-revalidate");
		header("Cache-Control: public");
		header("Pragma: cache");
	}

	//Selectionne le type de rendu en fonction du type
	function getContent() {
		$detectmime = true;
		switch($this->type){
			case "jpg":if ($this->type=="jpg")header("Content-type:  image/jpg");$detectmime=false;
			case "jpeg":if ($this->type=="jpeg")header("Content-type:  image/jpeg");$detectmime=false;
			case "png":if ($this->type=="png")header("Content-type:  image/png");$detectmime=false;
			case "gif":if ($this->type=="gif")header("Content-type:  image/gif");$detectmime=false;
				$this->setNavCache();
				$file = $this->Lien.'.'.$this->type;
				if (file_exists($file) ) {
					//si un fichier existe
					$Temp = explode("/",$file);
					$name = $Temp[sizeof($Temp)-1];
					$this->output_file($file,$name,false,$detectmime);
				}else{
					if (preg_match("#([A-z\/\-0-9\@\*\.]+)\.([A-z0-9]+)\.([A-z]+)\.([0-9]+)x([0-9]+)(.*)#",$this->Lien,$o)){
						$SplitChem = explode("/",$this->Lien.'.'.$this->type);
						$Query = "_Dossier";
						for ($i=0;$i<count($SplitChem)-1;$i++){
							$Query .= "/".$SplitChem[$i];
						}
						$Query .= "/_Fichier/".$SplitChem[count($SplitChem)-1];
						$data = $Query;
						$res = Sys::$Modules["Explorateur"]->callData($data);
						header('Content-Length: '.$res[0]["Size"]);
						$data = $res[0]["Contenu"];
						print($data);
					}
					else {
						$this->FileNotFound($file);
					}
				}
			break;
			case "mp3":if ($this->type=="mp3")header("Content-type: audio/mpeg");$detectmime=false;
			case "wav":if ($this->type=="wav")header("Content-type: audio/x-wav");$detectmime=false;
			case "mpeg":if ($this->type=="mpeg")header("Content-type: video/mpeg");$detectmime=false;
			case "mov":if ($this->type=="mov")header("Content-type: video/quicktime");$detectmime=false;
			case "mpg":if ($this->type=="mpg")header("Content-type: video/mpeg");$detectmime=false;
			case "f4v":if ($this->type=="f4v")header("Content-type: video/mp4");$detectmime=false;
			case "mp4":if ($this->type=="mp4")header("Content-type: video/mp4");$detectmime=false;
			case "webm":if ($this->type=="webm")header("Content-type: video/webm");$detectmime=false;
            case "wmv":if ($this->type=="wmv")header("Content-type: video/x-ms-wmv");$detectmime=false;
			case "flv":if ($this->type=="flv")header("Content-type: video/x-flv");$detectmime=false;
			case "css":if ($this->type=="css")header("Content-type: text/css");$detectmime=false;
			case "swf"://if ($this->type=="mp3")header("Content-type:  image/jpg");$detectmime=false;
			case "ico":if ($this->type=="ico")header("Content-type:  image/ico");$detectmime=false;
			case "js":if ($this->type=="js")header("Content-type:  text/js");$detectmime=false;
				//Fichiers intégrés dans le navigateur
				$this->setNavCache();
				$file = $this->Lien.'.'.$this->type;
				$Temp = explode("/",$file);
				$name = $Temp[sizeof($Temp)-1];
				$this->output_file($file,$name,false,$detectmime);
			break;
            case "b64":
            	header("Content-type:application/octet-stream");
            	$detectmime=false;
            	echo base64_encode( file_get_contents($this->Lien));
            break;
			case "ods": if ($this->type=="ods") { header("Content-type:application/vnd.oasis.opendocument.spreadsheet");$detectmime=false;  }
			case "ppt": if ($this->type=="ppt") { header("Content-type:application/vnd.ms-powerpoint");$detectmime=false; }
			case "ppz": if ($this->type=="ppz") { header("Content-type:application/vnd.ms-powerpoint");$detectmime=false;  }
			case "pps": if ($this->type=="pps") { header("Content-type:application/vnd.ms-powerpoint");$detectmime=false;  }
			case "pot": if ($this->type=="pot") { header("Content-type:application/vnd.ms-powerpoint");$detectmime=false;  }
			case "xmn":
			case "lnn":
            case "exe": if ($this->type=="exe") { header("Content-type:application/octet-stream");$detectmime=false;  }
            case "dll": if ($this->type=="dll") { header("Content-type:application/octet-stream");$detectmime=false;  }
			case "doc": if ($this->type=="doc") { header("Content-type:application/msword");$detectmime=false;  }
			case "odt": if ($this->type=="odt") { header("Content-type:application/vnd.oasis.opendocument.text");$detectmime=false;  }
			case "xlsx": if ($this->type=="xlsx") { header("Content-type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");$detectmime=false;  }
			case "conf":
				//Fichiers qui déclenchent un téléchargement
				$this->setNavCache();
				$file = $this->Lien.'.'.$this->type;
				$Temp = explode("/",$file);
				$name = $Temp[sizeof($Temp)-1];
				$this->output_file($file,$name);
			break;
			case "htrc":
			case "json":
				header("Content-type: text/json; charset=".CHARSET_CODE."");
				header("Accept-Ranges:bytes");
				header("Access-Control-Allow-Headers:Origin, Accept, Content-Type, X-Requested-With, X-CSRF-Token");
				header("Access-Control-Allow-Methods:GET, POST, PUT, DELETE");
				header("Access-Control-Allow-Origin: *");
				$data = "";
				$this->AnalyseVars();
				Parser::Init();
				$Skin = new Skin();
				$this->CurrentSkin=$Skin; 
				$this->CurrentSkin->Template = false;  
				$data .= $this->getContenu();
				$data = $Skin->ProcessLang($data);
				print($data);
			break;

			case "xml":
				header("Content-type: text/xml; charset=".CHARSET_CODE."");
				$data = "";
				$this->AnalyseVars();
				Parser::Init();
				$Skin = new Skin();
				$this->CurrentSkin=$Skin; 
				$this->CurrentSkin->Template = false;  
				$data .= $this->getContenu();
				$data = $Skin->ProcessLang($data);
				print($data);
			break;
			case "soap":
				if($this->Lien=="Systeme"){
					if(isset($_GET["wsdl"])) {
						header("Content-type: text/xml; charset=".CHARSET_CODE."");
						$data = file_get_contents("Class/Rpc/appaloosa.xml");
						print($data);
						break;
					}
					$this->AnalyseVars();
					try { 
						$server = new SoapServer("Class/Rpc/appaloosa.xml",  array('trace' => 1, 'uri' => $this->Lien,'encoding'=>'UTF-8', 'features' => SOAP_SINGLE_ELEMENT_ARRAYS));
						// On d�finit la classe qui va g�rer les requ�tes SOAP
						$server -> setclass('WebService');
					} catch (Exception $e) {
						echo $e;
					}
					// La m�thode POST a �t� utilis�e pour appeller cette page.
					// On suppose donc qu'une requ�te a �t� envoy�e, on la g�re
					if ($_SERVER['REQUEST_METHOD'] == 'POST') {
						$server -> handle();
					}
				}else{
					//Dans le cas d'un webservice pour un module en particulier
					$filename="Modules/".$this->Lien."/Class/Webservice_".$this->Lien.".class.php";
					$classname = "Webservice_".$this->Lien;
					if (!file_exists($filename)) die();
					require_once $filename;
					$wrpc = new $classname();
					if(isset($_GET["wsdl"])) {
						header("Content-type: text/xml; charset=".CHARSET_CODE."");
						$data = $wrpc->getwsdl($this);
						print($data);
						break;
					}
					$this->AnalyseVars();
					$wrpc->soapServer($this);
				}
			break;
			case "indd":if ($this->type=="indd")header('Content-type: application/pdf');
			case "bin":if ($this->type=="bin")header("Content-type: binary/octet-stream;");
			case "htm":if ($this->type=="htm")header("Content-type: text/html; charset=".CHARSET_CODE."");
			case "pac":if ($this->type=="pac")header("Content-type: text/plain; charset=".CHARSET_CODE."");
			case "cron":
				$file = $this->Lien.'.'.$this->type;
				if (@fopen(ROOT_DIR.$file,'r') ) {
					//si un fichier existe
					$file = $this->Lien.'.'.$this->type;
					$Temp = explode("/",$file);
					$name = $Temp[sizeof($Temp)-1];
					$this->output_file($file,$name,false,$detectmime);
				}else{
					//ON definie les variables d environnements
					$Skin = new Skin();
					$this->CurrentSkin=$Skin; 
					Sys::$Session->LastUrl = '/'.$this->Lien.'.'.$this->type;
					$data = "";
					$this->AnalyseVars();
					Parser::Init();
					$data .= $this->getContenu();
					$data = $Skin->ProcessLang($data);
					if (DEBUG_DISPLAY) $data.=KError::displayErrors();
					if($this->type == "htm" && defined('HTML_CACHE') && HTML_CACHE) $data = str_replace(array("\r", "\n", "\t"),"",$data);
					print($data);
				}
			break;
            case "raw":
                $file = $this->Lien.'.'.$this->type;
                if (@fopen(ROOT_DIR.$file,'r') ) {
                    //si un fichier existe
                    $file = $this->Lien.'.'.$this->type;
                    $Temp = explode("/",$file);
                    $name = $Temp[sizeof($Temp)-1];
                    $this->output_file($file,$name,false,$detectmime);
                }else{
                    //ON definie les variables d environnements
                    Sys::$NO_TEMPLATE = true;
                    Sys::$REMOVE_COMMENT = false;
                    $Skin = new Skin();
                    $this->CurrentSkin=$Skin;
                    Sys::$Session->LastUrl = '/'.$this->Lien.'.'.$this->type;
                    $data = "";
                    $this->AnalyseVars();
                    Parser::Init();
                    $data .= $this->getContenu();
                    $data = $Skin->ProcessLang($data);
                    if (DEBUG_DISPLAY) $data.=KError::displayErrors();
                    if($this->type == "htm" && defined('HTML_CACHE') && HTML_CACHE) $data = str_replace(array("\r", "\n", "\t"),"",$data);
                    print($data);
                }
                break;
			case "pdf":
				if ($this->type=="pdf"){
					header('Content-type: application/pdf');
					$this->Log->log("PDF -> ".$this->Lien);
				}
			case "xls": 
				if ($this->type=="xls") { 
					header("Content-type:application/vnd.ms-excel");$detectmime=false;  
					$this->Log->log("XLS -> ".$this->Lien);
				}
			case "csv": 
				if ($this->type=="csv") {
					header("Content-type: application/vnd.ms-excel; charset=".CHARSET_CODE."");
					$this->Log->log("CSV -> ".$this->Lien);
				}
				if (preg_match("#(Skins|Home)/#",$this->Lien)){
					$file = $this->Lien.'.'.$this->type;
					$Temp = explode("/",$file);
					$name = $Temp[sizeof($Temp)-1];
					header("Content-disposition: attachment; filename=\"$name.".$this->type."\"");
					$this->output_file($file,$name);
				}else{
					//ON definie les variables d environnements
					$data = "";
					$this->AnalyseVars();
					Parser::Init();
					$data .= $this->getContenu();
					print($data);
				}
			break;
			case "print":
				$this->AnalyseVars();
				Parser::Init();
				$this->Header = new Header($this->type);
				$data="<script>print()</script>";
				header("Content-type: text/html; charset=".CHARSET_CODE."");
				$Skin = new Skin();
				$data .= $Skin->ProcessLang($this->getContenu());
				$data=$this->getHeader().$data.$this->getFooter();
				$this->Log->Log($GLOBALS["Chrono"]->total());
				print($data);
			break;
			case "htms":
				//ON definie les variables d environnements
				$this->AnalyseVars();
				Parser::Init();
				$this->Header = new Header();
				header("Content-type: text/html; charset=".CHARSET_CODE."");
				$Skin = new Skin();
				$data = $Skin->ProcessLang($this->getContenu());
				$data=$this->getHeader().$data.$this->getFooter();
				$this->Log->Log($GLOBALS["Chrono"]->total());
				print($data);
			break;

			case "skin":
				$this->Header = new Header();
				header("Content-type: text/html; charset=".CHARSET_CODE."");
				$Skin = new Skin();
				Parser::Init('Skin');
				$Skin->Generate();
				$data=$Skin->Affich();
				$data = $Skin->ProcessLang($data);
				$data=$this->getHeader().$data.$this->getFooter();
				print($data);
			break;
			case "html":
			default:
// 				$this->Log->log("INIT START -> ".$this->Lien);
				if (file_exists("stats.php"))include_once("stats.php");
				elseif (file_exists("../stats/stats.php"))include_once("../stats/stats.php");
				header("Content-type: text/html; charset=".CHARSET_CODE."");
				$this->Header = new Header();
				//Sauf le cas d un fichier html dans la skin ou Home
				if (preg_match("#(Skins|Home)/#",$this->Lien)){
					$file = $this->Lien;
					$Temp = explode("/",$file);
					$name = $Temp[sizeof($Temp)-1];
					$this->output_file($file,$name);
				}else{
					if ($this->Lien!='')Sys::$Session->LastUrl = '/'.$this->Lien;
					Parser::Init();
					$Skin = new Skin();
					$this->CurrentSkin=$Skin; 
					//ON definie les variables d environnements
					$this->AnalyseVars();
					$Skin->Generate();
					$data=$Skin->Affich();
 					$Contenu =$this->getContenu();
 					$data=Parser::ProcessData($data,$Contenu);
 					$data = $Skin->ProcessLang($data);
					//On ajoute les erreurs
					if (DEBUG_DISPLAY) $data.=KError::displayHtml();
 					//On ajoute l entete
					$data=$this->getHeader().$data.$this->getFooter();
				}
				if($this->type == "html" && defined('HTML_CACHE') && HTML_CACHE) $data = str_replace(array("\r", "\n", "\t"),"",$data);
				print($data);
			break;
		}
		if (isset($data))return $data;
	}
	private function FileNotFound($file) {
		header("Content-type: text/html; charset=".CHARSET_CODE."");
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
		die('Fichier ' .$file. ' non trouvé.');
	}
	/***********************************
	* Analyse Url requete
	***********************************/
	function AnalyseVars(){
		//Ici on definit les variables globales necessaires.
		$this->Query=$this->AnalyseMenu($this->Lien);
		//Définition du men par défaut
		$Results = $this->searchMenu('');
		Sys::$DefaultMenu = $Results;
		//Recuperation des Donnï¿œes du Module
		$Out = explode("/",$this->Query);
		$this->CurrentModule = ($Out[0]!="")?$Out[0]:"Systeme";
		$this->setQuery($this->Query);
	}

	function searchMenu($Men,$Tab=0,$Niv=0) {
		//Sinon on continue a analyser le lien
		if ($Tab==0&&$Niv==0)$Tab=Sys::$User->Menus;
		$Menus = explode("/",$Men);
		$Result=false;
		$T = $Menus[$Niv];
		if (is_array($Tab))for ($i=0;$i<sizeof($Tab);$i++){
			if (is_array($Tab[$i])) die("ERREUR: trop de niveaux de recursion dans les menus".var_dump($Tab[$i]));
			$Tab[$i]->Niveau = $Niv;
			$Url = $Tab[$i]->Url;
			if ($Url==$T&&$Niv<sizeof($Menus)-1) {
				$this->MenusFromUrl[]= $Tab[$i];
				$Result = $this->searchMenu($Men,$Tab[$i]->Menus,$Niv+1);
				if (!$Result) {
					//Peut etre Alias ?
					$Tab[$i]->Niveau = $Niv;
					return $Tab[$i];
				}else{
					return $Result;
				}
			}elseif ($Url==$T){
				$this->MenusFromUrl[]= $Tab[$i];
				return $Tab[$i];
			}
		}
		return $Result;
	}

	function AnalyseMenu($Lien,$nb=0){
		//Sinon on continue a analyser le lien
		$Menus = explode("/",$Lien);
		$Results = $this->searchMenu($Lien);
		if (is_object($Results)){
			//Modification de l'url du menu en cours
			$U = $Menus[0];
			for ($i=1;$i<sizeof($this->MenusFromUrl);$i++)$U.='/'.$this->MenusFromUrl[$i]->Url;
			Sys::$CurrentMenu = $Results->getClone(true);
			Sys::$CurrentMenu->Url=$U;
		}
//		if (isset($Results)&&is_object($Results)&&$Results->Alias!=""&&$Results->Niveau<sizeof($Menus)){
// Correctio prob de menu sans alias
		if (isset($Results)&&is_object($Results)&&$Results->Niveau<sizeof($Menus)){
			$LienResult = $Results->Alias;
			for($j=$Results->Niveau+1;$j<sizeof($Menus);$j++) {
				if ($Menus[$j]!="")$LienResult.="/".$Menus[$j];
			}
 			//echo "LIENRESULT--> ".$LienResult." NIVEAU ".$Results->Niveau;
			return $LienResult;
		}
		//TEST REQUETE DIRECTE
		$Out = explode("/",$Lien);
		$Module = $Out[0];
		//Si c est une requete alors on sort avec la reponse
		if ($this->isModule($Module)) {
			$this->CurrentModule =$Module;
			return $Lien;
		}

		//---------------------------------------------------------//
		// MESSAGE ERREUR PAGE INTROUVABLE + RAPPORT ERREUR	   //
		//---------------------------------------------------------//
		if (!is_object($Results)&&!$nb) {
			//On renvoie le menu par defaut
			return $this->AnalyseMenu($this->PageDefaut,1);
		}elseif($nb) $this->redirectionErreur404();
		if (($Results->Lien!="")&&($Lien!=$Results->Lien)){
			return $this->AnalyseMenu($Results->Lien,0,$Lien);
		}
		return $Results->Alias;
	}
	/**
	 * redirectionErreur404
	 * Redirection 404
	 */
	function redirectionErreur404(){
		header('HTTP/1.0 404 Not Found');
		die('<h1>404 Error</h1><h2>File not found</h2><a href="/">Back home</a>');
	}
	//***********************************
	//	Manipulation des Variables
	//***********************************
	function registerVar($Name,$Value){
		$this->RegVars[$Name] = $Value;
		Process::RegisterTempVar($Name,$Value);
	}

	function PostVarsToPhp() {
		$this->PostVars = $_POST;
		//Resoplution des problemes d encodages dues a l AJAX
		if (is_array($this->PostVars))foreach ($this->PostVars as $K=>$P) {
//			if (!is_array($P)&&!is_object($P))$this->PostVars[$K] = stripslashes($P);
			if (!is_array($P)&&!is_object($P))$this->PostVars[$K] = $P;
			else $this->PostVars[$K] = $P;
		}
		//Detection des variables JSON (AJAX)
		if (isset($_POST["data"])){
			$this->Log->log("JSON DECODE : ".$_POST["data"]."\r\n",json_decode($_POST["data"]));
			$this->RegVars["JSON"] = json_decode($_POST["data"]);
		}
	}
	
	function GetVarsToPhp() {
		$this->GetVars = $_GET;
	}

	function GetVarsLink() {
		foreach ($_GET as $k=>$g) {if ($r!="")$r.="&";$r.=$k."=".$g;}
		return $r;
	}
	function FilesVarsToPhp() {
		$this->FilesVars = $_FILES;
	}

	function getPostVars($Data) {
		if (isset($this->PostVars[$Data]))return $this->PostVars[$Data];
	}
	function getGetVars($Data) {
		if (isset($this->GetVars[$Data]))return $this->GetVars[$Data];
	}

	function getRegVars($Data) {
		if (isset($this->RegVars[$Data]))return $this->RegVars[$Data];
	}
	function getFilesVars($Data) {
		if (isset($this->FilesVars[$Data]))return $this->FilesVars[$Data];
	}


	//***********************************
	//	Methodes GETTER
	//***********************************
	//	Methodes PRIVES

	function isModule($Temp) {
		$ModTemp = array_keys(Sys::$Modules);
		for ($i=0;$i<count(Sys::$Modules);$i++) {
			if ($ModTemp[$i]==$Temp) {
				return true;
			}
		}
		return false;
	}

	function getBlockList(){
		//On passe dans le tableau pour retirer les eventuels doublons
		$NewTab=Array();
		for ($i=0;$i<count($this->BlockList);$i++){
			for ($j=0;$j<count($NewTab);$j++){
				if($this->BlockList[$i]==$NewTab[$j]){
					$flag=true;
				}
			}
			if (!$flag) $NewTab[]=$this->BlockList[$i];
		}
		return $NewTab;
	}

	function getContenu() {
        if (!empty($this->Query)) {
            $Tab = Module::splitQuery($this->Query);
            if (isset($Tab[0]["Query"])) Process::RegisterTempVar('Query', $Tab[0]["Query"]);
        }
		//Recuperation des Donnï¿œes du Module
		$Data = Sys::$Modules[$this->CurrentModule]->Affich($this->Query); //echo "ERREUR FICHEIR NON TROUVE";//$this->FileNotFound($this->Query);
		return $Data;
	}

	function output_file($file,$name,$attach=true,$detectmime=true){
		//do something on download abort/finish
		//register_shutdown_function( 'function_name'  );
		if(!file_exists($file))
			$this->redirectionErreur404();
		$size = filesize($file);
		$name = rawurldecode($name);

		if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
		$UserBrowser = "Opera";
		elseif (ereg('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
		$UserBrowser = "IE";
		else
		$UserBrowser = '';

		/// important for download im most browser
/*		$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ?
		'application/octetstream' : 'application/octet-stream';*/
		/*if ($detectmime)header('Content-Type: ' . mime_content_type($file));
		header('Content-Length: ' . filesize($file));
		header("Last-Modified: " . gmdate('D, d M Y H:i:s', time()-3600) . ' GMT');
		if ($attach){
			header('Content-disposition: attachment; filename='. $name);
			header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
			header('Expires: 0');
			header("Cache-control: private");
			header('Pragma: no-cache');
			header('Accept-Ranges: bytes');
		}
		/////  multipart-download and resume-download
		if(isset($_SERVER['HTTP_RANGE'])){
			list($a, $range) = explode("=",$_SERVER['HTTP_RANGE']);
			str_replace($range, "-", $range);
			$size2 = $size-1;
			$new_length = $size-$range;
			header("HTTP/1.1 206 Partial Content");
			header("Content-Length: $new_length");
			header("Content-Range: bytes $range$size2/$size");
		}else{
			$size2=$size-1;
			header("Content-Length: ".$size);
		}*/
		$chunksize = 1*(1024*1024);
		$this->bytes_send = 0;
		//if (file_exists($file)){// = fopen($file, 'r')){
			/*if(isset($_SERVER['HTTP_RANGE']))fseek($file, $range);
			while(!feof($file) and (connection_status()==0)){
				$buffer = fread($file, $chunksize);
				print($buffer);//echo($buffer); // is also possible
				ob_flush();
				$this->bytes_send += strlen($buffer);
				//sleep(1);//// decrease download speed
			}
			fclose($file);
			ob_end_clean();*/
			flush();
			ob_clean();
			readfile($file);
		//}else	die('error can not open file');
		//if(isset($new_length))$size = $new_length;
	}

	function saveGroupData($id){
		$ModulesInv = array_keys(Sys::$Modules);
		$Modules["Systeme"] = "Rien";
 		for ($i=0;$i<count(Sys::$Modules);$i++){
		//	echo "OK";
			if (method_exists(Sys::$Modules[$ModulesInv[$i]],"loadSchema")){
				Sys::$Modules[$ModulesInv[$i]]->loadSchema();
				if (method_exists(Sys::$Modules[$ModulesInv[$i]]->Db,"saveGroupData")){
					$Resultats .= Sys::$Modules[$ModulesInv[$i]]->Db->saveGroupData($id);
				}
			}
		}
		return htmlentities($Resultats);
	}


	function getHeader() {
		return $this->Header->Affich();
	}
	function getFooter() {
		return $this->Header->getFooter();
	}

	//****************************************
	//	Methodes de configuration Externe
	//****************************************
	function setModules($Temp) {
		Sys::$Modules = $Temp;
	}
	//Definition du driver de base de donnï¿œ
	function setDriver($Temp) {
		$this->Driver = $Temp;
	}

	static function setSkin($Temp) {
		Sys::$Skin = $Temp;
		Sys::$User->Skin = $Temp;
	}
	function isLogged() {
		return true;
	}
	/**
	* searchInMenus Permet de retrouver le menu a partir d'un champ
	* Recherche recursivement 
	* @param Field Champ concerné par la recherche
	* @param Value Valeur de la recherche
	* @return Array
	*/
	static public function searchInMenus($Field,$Value,$Url=Array(),$Tab=Array(),$Niv=0,$all=false){
		//echo "Search $Field $Value $Niv \r\n";
		//Sinon on continue a analyser le lien
		if (!sizeof($Tab)&&$Niv==0)$Tab=Sys::$User->Menus;
		$Result=false;
		if (is_array($Tab)){
			for ($i=0;$i<sizeof($Tab);$i++){
				//echo "--> Test ".$Tab[$i]->Alias." | ".$Tab[$i]->Titre."\r\n";
				//Test de resultat
				if ($Tab[$i]->{$Field}!=$Value&&isset($Tab[$i]->Menus)&&sizeof($Tab[$i]->Menus)) {
					$Url = Sys::searchInMenus($Field,$Value,$Url,$Tab[$i]->Menus,$Niv+1,$all);
					if (!$all&&sizeof($Url)) return $Url;
				}elseif ($Tab[$i]->{$Field}==$Value&&$Tab[$i]->Url!=""){
					//echo "Found ".$Tab[$i]->Alias." | ".$Tab[$i]->Titre." \r\n";
					array_push($Url,$Tab[$i]);
					if (!$all)return $Url;
				}
			}
		}
		return $Url;
	}
	/**
	* GetMenu Permet de retrouver le menu a partir d'une requete sur les menus de l'utilisateur courant
	* @param query Requete
	* @param all permet une recherche sur la globalité des menus
	* @return Array[String]
	*/
	static public function getMenus($Query,$all = false,$strict=true){
		//On analyse la requete
		$Infos = Info::getInfos($Query);
		if (!isset($Infos['Module'])||!is_object(Sys::$Modules[$Infos['Module']]))return $Query;
		//Cas différent de child
		if (isset($Infos["TypeSearch"])&&($Infos["NbHisto"]>1||$Infos["TypeSearch"]!="Child")){
			//Liste des noeuds parents
			$P = Array();
			//On recupere les détails de l'objet demandé dans le cas d'une recherche directe
			if ($Infos["TypeSearch"]=="Direct"){
				$Q = $Query;
                if (!$strict) {
                    //si pas strict alors on recherche
                    $C = Sys::$Modules[$Infos['Module']]->callData($Q, false, 0, 1);
                    if (isset($C) && is_array($C) && sizeof($C)) {
                        $C = genericClass::createInstance($Infos['Module'], $C[0]);
                        //Cas ou il s'agit d'un objet
                        $Ap = Array();
                        if ($Infos['Module'] . '/' . $Infos['ObjectType'] . '/' . $C->Id != $Q)
                            $Ap[] = Array($Infos['Module'] . '/' . $Infos['ObjectType'] . '/' . $C->Id, "");
                        if (isset($C->Url) && !empty($C->Url)) $Ap[] = Array($Infos['Module'] . '/' . $Infos['ObjectType'] . '/' . $C->Url, "");
                        $up = (isset($C->Url)) ? $C->Url : $C->Id;
                    }
                }
			}
			if (isset($Infos['Historique'][0]['DataSource'])&&(!$strict||$Infos["TypeSearch"]=="Interface")){
				$rest = explode($Infos['Module'].'/'.$Infos['Historique'][0]['DataSource'].'/',$Query);
				$up = (empty($up))?$rest[1]:$up;
				$Ap[] = Array($Infos['Module'].'/'.$Infos['Historique'][0]['DataSource'],$up);
			}
		}
		//cas child
		if (isset($Infos["TypeSearch"])&&($Infos["NbHisto"]==1&&$Infos["TypeSearch"]=="Child")&&!$strict){
			//dans ce cas la on extrait le module et la cible pour comparer avec les menus
            
		}

		$Ap[] = Array($Query,"");
		if ($all){
			//récupération de la liste de l'ensemble des menus
            if (!sizeof(Sys::$allMenus)) {
                Sys::$allMenus = Sys::getData('Systeme', 'Menu/*', 0, 10000);
                __autoload("Storproc");
                Sys::$allMenus = StorProc::sortRecursivResult(Sys::$allMenus, "Menus");
                Sys::$allMenus = Root::quickSort(Sys::$allMenus, "Ordre");
            }
            $Menus = Sys::$allMenus;
			$out=Array();
		}else $Menus = NULL;
		
		foreach ($Ap as $a){
			$m = Sys::searchInMenus('Alias',$a[0],Array(),$Menus,0,$all);
			if ($m&&is_array($m)){
				if (!empty($a[1])&&!$all){
					$b = new stdClass();
					$b->Url = $a[1];
					$m[] = $b;
				}
				if (!$all)return $m;
				else $out = array_merge($out,$m);
			}
		}
		if ($all) return $out;
		else return Array();
	}
	/**
	* GetMenu Permet de retrouver le menu a partir d'une requete sur les menus de l'utilisateur courant
	* @param query Requete 
	* @return String 
	*/
	static public function getMenu($Query){
		$m = Sys::getMenus($Query,false,false);
		$out='';
		if ($m){
			foreach ($m as $a) {
                $out .= ((!empty($out)) ? '/' : '') . $a->Url;
                if (isset($a->MenuParent)&&sizeof($a->MenuParent)){
                	$out = $a->MenuParent[0]->Url.'/'.$out;
				}
            }
		}else{
			$out = $Query;
		}
		return $out;
	}
	/**
	* Get module
	*/
	static function getModule($N){
		return isset(Sys::$Modules[$N]) ? Sys::$Modules[$N] : false;
	}
	
	/**
	 * getOneData
	 * Static call of callData with only one result
	 * @return Object or null
	 */
	 static function getOneData($Module, $Query, $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ){
	 	$o= Sys::getData($Module, $Query, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy );
		if (is_array($o)&&sizeof($o))foreach ($o as $k=>$t)
			return $o[0];
		else return null;
	 }

	/**
	 * getData
	 * Static call of callData
	 */
	 static function getData($Module, $Query, $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ){
	 	$o= Sys::$Modules[$Module]->callData($Query, false, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy );
		if (is_array($o)&&sizeof($o))foreach ($o as $k=>$t)
			$o[$k] = genericClass::createInstance($Module,$t);
		else $o = Array();
		return $o;
	 }

	/**
	 * getCount
	 * Static call of callData
	 */
	 static function getCount($Module, $Query){
	 	$o= Sys::$Modules[$Module]->callData($Query, false, 0, 1000000, '', '', 'COUNT(DISTINCT(m.Id))', '' );
		return isset($o[0]) ? $o[0]['COUNT(DISTINCT(m.Id))'] : 0;
	 }

	/**
	 * Affiche le sitemap et termine la page
	 * @return	void
	 */
	function displaySitemap() {
		ob_clean();
		header("Content-type: text/xml; charset=".CHARSET_CODE."");
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n";
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\r\n";
		$domain =Sys::$domain;
		$pages = Sys::$Modules['Systeme']->callData("Systeme/Site/Domaine=$domain/Page", '', 0, 100000);
		if(is_array($pages)) foreach($pages as $page) {
			echo "<url>";
			echo "<loc>".$page['Url']."</loc>";
			echo "<lastmod>".$page['LastMod']."</lastmod>";
			echo "<changefreq>".$page['ChangeFreq']."</changefreq>";
			echo "<priority>".$page['Priority']."</priority>";
			echo "</url>";
		}
		echo "</urlset>";
		exit();
	}

	/**
	 * Affiche le fichier robots.txt et termine la page
	 * @return	void
	 */
	function displayRobots() {
		$domain =Sys::$domain;
		$sites = Sys::$Modules['Systeme']->callData("Systeme/Site/Domaine=$domain", '', 0, 1000);
		if(is_array($sites)) echo $sites[0]['Robots'];
		exit();
	}

	/**********************************
	* ETAPE 4
	***********************************/
    /**
     * Cloture transaction
     */
    function CommitTransaction() {
        if (is_object($this->Db[0])) $this->Db[0]->query("COMMIT");
        if (is_object($this->Db[0])) $this->Db[0]->query("START TRANSACTION");
    }
	/**
	* Fermeture des connexions
	*/
	function Close() {
		foreach (Sys::$Modules as $Key=>$Mod){
			if ($Mod->SchemaLoaded) {Sys::$Modules[$Key]->Db->close();}
		}
		//Mise a jour des connexions
		if (is_object($this->Connection))$this->Connection->close();
		if (is_object($this->Db[0])) $this->Db[0]->query("COMMIT");

        $Pages = array();

        if (is_object($this->Db[0])) $this->Db[0]->query("START TRANSACTION");
        //Gestion des mots clefs
        foreach (Sys::$keywords as $k=>$m){
            $t = Sys::getOneData('Systeme','Tag/Canonic='.$k);
            $tmpPages = $m->Pages;
            $tmpPoids = $m->Poids;
            if (is_object($t)) {
                $m=$t;
            }
            $m->Save();
            if (is_array($tmpPages)){
                foreach ($tmpPages as $p) {
                    if (!isset($Pages[$p])) {
                        $Pages[$p] = Sys::getOneData('Systeme', 'Page/' . $p);
                    }
                    $Pages[$p]->AddParent($m);
                }
            }
        }

        //Gestion des pages
        foreach ($Pages as $page) $page->Save();

        if (is_object($this->Db[0])) $this->Db[0]->query("COMMIT");

        $this->Log->log("---------------------------------------- CLOSE----------------------------------------");
		flush();
        session_write_close();
	}

	function restartTransaction(){
		if (is_object($this->Db[0])){
			$this->Db[0]->query("COMMIT");
		} 
		$this->connectSQL();
	}
	/**********************************
	* TEMPLATES
	***********************************/
	/**
	* getTemplates
	* @return renvoie la liste des templates disponibles
	*/
	public function getTemplates() {
		$dir = "Templates";
		$out=Array();
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && is_dir($dir.'/'.$file) && !preg_match("#^\..*#",$file)) {
					$out[] = $file;
				}
			}
			closedir($handle);
		}
		return $out;	
	}
	/**********************************
	* SEARCH TAGS
	***********************************/
	/**
	* getSearch
	* @return renvoie la liste des resultats de recherches
	*/
	public function getSearch($Search) {
		$S = Utils::Canonic($Search);
		$domain =Sys::$domain;
		$Mc = Sys::getData('Systeme','Tag/Canonic~'.$S.'/Page/Page.SiteId(Domaine='.$domain.')',0,10,null,null,null,'m.Id');
		return $Mc;
	}
	/**********************************
	 * SEARCH TAGS
	 ***********************************/
	public static function getKeywordsProcessing() {
		return KEYWORDS_PROCESSING;
		//return Sys::$keywordsProcessing;
	}
	public static function disableKeywordsProcessing() {
		Sys::$keywordsProcessing = false;
	}
	public static function enableKeywordsProcessing() {
		Sys::$keywordsProcessing = true;
	}
}
?>
