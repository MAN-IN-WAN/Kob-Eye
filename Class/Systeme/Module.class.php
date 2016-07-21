<?php
class Module extends Root{
	private $_arrayQueryCache = array();

	var $Nom;												//Nom du Module
	var $Data;
	var $StorProc;
	var $Droits = Array();
	var $DataSource;
// 	var $Comment;
	var $Title;
	var $Schema;								//Le schema sous forme de tableau
	var $SchemaPath;							//Le chemin du schema
	var $ConfPath;							//Le chemin du schema
	var $SchemaLoaded = false;						//Savoir si le schema est charge ou pas
	var $Db;								//Objet contenant le schema charg�
	var $Cache=true;
	var $Bdd;
	var $TriggerFile = ""; //String
	var $Triggers;
	var $Functions = Array();

	//Derniere requete
	public static $LAST_QUERY;

	function Module ($Tab) {						//Constructeur du Module
		$this->Cache=true;
		//Configuration du module
		$this->Nom=$Tab["NAME"];
		$this->Title= $Tab["TITLE"];
		$this->Schema= $Tab["SCHEMA"]["SCHEMA"]["#"];
		$this->ConfPath = $Tab["@"]["file"];
		$this->SchemaPath = $Tab["SCHEMA"]["@"]["file"];
		$this->Description= $Tab["COMMENT"];
		if (isset($Tab["TRIGGER"]))$this->TriggerFile=$Tab["TRIGGER"];
		$this->Nom=$Tab["NAME"];
		if (isset($Tab["BDD"]))$this->Bdd=$Tab["BDD"];
		//Chargement des classes 
		if (is_array($this->Schema))foreach ($this->Schema["OBJECTCLASS"] as $o){
			if (isset($o["@"]["Class"])) include(ROOT_DIR.$o["@"]["Class"]);
		}
		if (isset($Tab["FUNCTIONS"]["FUNCTION"]))$Functions = $Tab["FUNCTIONS"]["FUNCTION"];
		//On place les fonctions dans lordre
		if ((!isset($this->Functions[0])||!is_array($this->Functions[0]))&&isset($Functions["TITRE"])&&$Functions["TITRE"]!=""){
			$this->Functions[0] = $Functions;
		}elseif (isset($Functions[0]["TITRE"])&&$Functions[0]["TITRE"]!=""){
			$this->Functions = $Functions;
		}
	}
	/**
	 * createInstance
	 * Renvoie une instance d'un module ou d'une class surchagre définie.
	 * @param Array|String Configuration du module
	 * @return Module
	 */
	static function createInstance($Conf) {
		if (!is_array($Conf))
			die("La configuration du module est vide");
		$Special = isset($Conf["CLASS"])?$Conf["CLASS"]:false;
		if ($Special) {
			//Le cas d une class etendue
			require_once (ROOT_DIR.$Conf["CLASS"]);
			$ClassName = $Conf["NAME"];
			$Class = new $ClassName($Conf);
		} else {
			$Class = new Module($Conf);
		}
		return $Class;
	}
	/**
	 * Fin de l'initialisation du module
	 * Toute les fonctionnalites ne sont pas encore disponibles
	 */
	function init() {
		//initilisation des views et associations
		$this->Db->initAssociations();
	}
	/**
	 * postInit est appelé après l'initialisation de l'utilisateur
	 * Cette fonction permet d'enregistrer des variables ou d'agir sur le systeme avant le traitement des requetes.
	 * @void 
	 */
	function postInit (){
	}
	/**
	 * Check Bdd
	 */
	function Check(){
		//Lance une verification de la bdd.
		echo "<li>CHECK MODULE ".$this->Nom."</li><ul>";
		$this->Db->Check();
		echo "</ul>";
		return true;
	}


	function getDataSource($Type,$Id) {
		//Construction de l arbres des donn�s necessaires
		$TempSource = new $Type();
		$TempSource->initFromId($Id);
		$TempSource->initChildTree();
		return $TempSource->getGroupe($Id);
	}

	function loadSchema() {
		//Si il n est pas charg�alors on le charge sinon retour
		if (!$this->SchemaLoaded) {

            /*Charge le cache des modules, ou si il n'est pas disponible, va chercher le xml*/
			/*if (SCHEMA_CACHE){
				$CacheO = "Modules/".$this->Nom."/.Db.cache";
				$Class = "Class/DataBase/DbAnalyzer.class.php";
				if (file_exists(ROOT_DIR.$CacheO) && (filemtime(ROOT_DIR.$CacheO) >= filemtime(ROOT_DIR.$Class))&& (filemtime(ROOT_DIR.$CacheO) >= filemtime(ROOT_DIR.$this->SchemaPath))&& (filemtime(ROOT_DIR.$CacheO) >= filemtime(ROOT_DIR.$this->ConfPath))&&SCHEMA_CACHE) {
					$Cache = file_get_contents(ROOT_DIR.$CacheO);
					$this->Db = unserialize($Cache);
				}
			}*/
			if (!is_object($this->Db)){
				$this->Db = new DbAnalyzer($this->Nom);
				$this->Db->loadSchema($this->Schema);
				unset($this->Schema);
			}
			$this->Db->setCache($this->Cache);
			$this->SchemaLoaded = true;
			if (!empty($this->TriggerFile) && file_exists(ROOT_DIR.$this->TriggerFile)){
				$XmlContent = new xml2array($this->TriggerFile);
				$XmlResult=$XmlContent->getResult();
				$XmlResult=$XmlResult["ACTIONS"]["#"]["ACTION"];
				foreach($XmlResult as $Action){
					$T = new Trigger($Action,$this->Nom);
					$this->Triggers[] = $T;
				}
			}

			return false;
		}
		//Si il y a une source de donnée alternative alors on la charge
		if (is_array($this->Bdd)&&sizeof($this->Bdd)){
			foreach ($this->Bdd as $k=>$b) {
				if (empty($GLOBALS["Systeme"]->Db[$k]))switch ($b["TYPE"]){
					case "mysql":
						require_once ROOT_DIR."Class/Lib/MDB2.php";
						$GLOBALS["Systeme"]->Db[$k] = & MDB2::connect($b["DSN"]);
						$GLOBALS["Systeme"]->Db[$k]->options["portability"]=0;
						$GLOBALS["Systeme"]->Db[$k]->setFetchMode(MDB2_FETCHMODE_ASSOC);
						$GLOBALS["Systeme"]->Db[$k]->exec("SET NAMES 'utf8';");
					break;
					case "ldap":
						$server=$b["HOST"];
						$admin=$b["USERNAME"];
						$passwd=$b["PASS"];
						$GLOBALS["Systeme"]->Db[$k] = ldap_connect($server);  // assuming the LDAP server is on this host
						if ($GLOBALS["Systeme"]->Db[$k]) {
							ldap_set_option($GLOBALS["Systeme"]->Db[$k], LDAP_OPT_PROTOCOL_VERSION, 3);
							// bind with appropriate dn to give update access
							$r=ldap_bind($GLOBALS["Systeme"]->Db[$k], $admin, $passwd);
							if(!$r) die("ldap_bind failed<br>");
// 							echo "ldap_bind success";
							//ldap_close($GLOBALS["Systeme"]->Db[$k]);
						} else {
// 							echo "Unable to connect to LDAP server";
						}
					break;
				}
			}
		}
	}

	/**
	* Chargement de templates
	*/
	private function loadTemplate($t){
		//Recuperation de la template
		$Te = false;
		if (isset($_GET["TemplateId"]))$Te =  Sys::$Modules['Systeme']->callData('ActiveTemplate/'.$_GET["TemplateId"],false,0,1);
		if (intval($t)>0&&!$Te) $Te =  Sys::$Modules['Systeme']->callData('ActiveTemplate/'.$t,false,0,1);
		if (!$Te) $Te = Sys::$Modules['Systeme']->callData('ActiveTemplate/Default=1',false,0,1);
		if ($Te) $Te = genericClass::createInstance('Systeme',$Te[0]);
		
		$Bloc=new Template();
		if ($Te){
			$Bloc->setConfig($Te->TemplateConfig,$Te->Template);
		}else $Bloc->loadData("[DATA]");
		$Bloc->init();
		return $Bloc;
	}

	/**
	 * analyzeObjectClass
	 * Analyse la chaine de l'objectclass pour en extraire les clefs et les vues à utiliser
	 * @param String
	 * @return Array[String] 
	 */
	 function getAnalyzedObjectClass($s){
	 	$o=Array();
	 	//recherche de vues
	 	$s = explode(":",$s);
	 	if (sizeof($s)>1){
	 		//deinfnition de la vue
	 		$o["View"] = $s[1];
	 		
	 	}
		$s = $s[0];
		//recherche de clefs
	 	$s = explode(".",$s);
        if (isset($s[1])&&sizeof(explode("(",$s[1]))) {
            //sortie car ils'agit d'un filtre
            return Array("ObjectClass"=>"");
        }
	 	if (sizeof($s)>1){
	 		//deinfnition de la vue
	 		$o["Key"] = $s[1];
	 		
	 	}
		$s = $s[0];
		//recherche de l'objectclass
		$o["ObjectClass"] = $s;
		return $o;
	 } 
	/**
	* splitQuery
	* Decoupage et test de cohérence de la requete
	* @param String Requete
	* @param Boolean Strict definit si la cohérence doit tester les interfaces.
	* @return Array(Array(String...))
	*/
	public static function splitQuery($Lien,$Strict=false) {
		$Out=explode("/",$Lien);
		$Module = $Out[0];
        if (!isset(Sys::$Modules[$Module])){
            return false;
        }
		array_shift($Out);
        $Lien=implode('/',$Out);
		if (isset(Sys::$Modules[$Module]->_arrayQueryCache[$Strict.'-'.$Lien])) {
			return Sys::$Modules[$Module]->_arrayQueryCache[$Strict.'-'.$Lien];
		}
		//TEST APC CACHE
        /*if (ApcCache::getData('QUERY-'.$Strict.'-'.$Lien))
            return ApcCache::getData('QUERY-'.$Strict.'-'.$Lien);*/

		if (empty($Out))$Out = Array();
        $p="";
		$Last=-1;
		$Type="Erreur";
		$Result=Array();
		Sys::$Modules[$Module]->loadSchema();
        //GESTION OBJECTCLASS PAR DEFAUT (IMPLICITE)
		$t = Sys::$Modules[$Module]->Db->getDefaultObjectClass();


        if ($t&&$t!=$Out[0]){
			//soit il existe une data source par défaut
			for ($i=0,$c = sizeof($Out);$i<$c;$i++) $p .=(($i>0&&$Out[$i]!=""&&$p!="")?"/":"").$Out[$i];
			if (DEBUG_INTERFACE)echo "DEFAULT OBJECTCLASS \r\n";
			if (Bloc::isInterface($Module,$t,$p)){
				$Interface = Array(
					"DataSource"=>$t,
					"Interface"=>$p
				);
			}
			//On insere l'objectclass par defaut au bon endroit pour reconstituer la requete
			$Out2[0] = $t;
			if ($Out[0]!="")for ($i=0,$c=sizeof($Out);$i<$c;$i++) $Out2[$i+1] = $Out[$i];
			$Out = $Out2;
		}

		//GESTION LIEN VIDE
		if ($Lien==""){
			$Result[0]["Type"] = "Interface";
			$Result[0]["Query"] = "";
			$Result[0]["Interface"] = "";
            $Result[0]["InterfacePath"] = Bloc::getInterface($Module,'',$p,false);
			return $Result;
		}

		//INIT
		$LastAssociation = null;

		//DETERMINATION DE LA NATURE DE LA REQUETE
		$d1 = Sys::$Modules[$Module]->getAnalyzedObjectClass($Out[0]);
		$LastDataSource = Sys::$Modules[$Module]->Db->getByTitleOrFkey($d1["ObjectClass"]);
		if (is_object($LastDataSource)&&sizeof($Out)>1&&isset($d1["Key"])){
			$d2 = Sys::$Modules[$Module]->getAnalyzedObjectClass($Out[1]);
			$NextDataSource = Sys::$Modules[$Module]->Db->getByTitleOrFkey($d2["ObjectClass"]);
			if (is_object($NextDataSource)){
				$LastAssociation=$NextDataSource->getParentAssociation($d1["Key"],$d1["ObjectClass"]);
			}
		}

		$lastkey = $d1;
		//print_r($d1);
		if (!is_object($LastDataSource)) {
			//CAS INTERFACE
			if ($Strict)return false;
			//soit il s'agit d'un appel d'interface
			for ($i=1,$c=sizeof($Out);$i<$c;$i++) $p .=(($i>1&&$Out[$i])?"/":"").$Out[$i];
			if (DEBUG_INTERFACE)echo "CAS INTERFACE \r\n";
			if (Bloc::isInterface($Module,$Out[0],$p,false)){
				$Result[0]["Type"] = "Interface";
				$Result[0]["InterfacePath"] = Bloc::getInterface($Module,$Out[0],$p,false);
                $Result[0]["Module"] = $Module;
				$Result[0]["Interface"] = Array(
					"DataSource"=>$Out[0],
					"Interface"=>$p
				);
			}//else return false;
		}else{
			//CAS REQUETE
			//On decompose la requete en tableau par paire et on extrait le dernier objectclass

            for ($i=0,$c=sizeof($Out);$i<$c;$i++) {


                $Ass = null;
				$Object="";
				if ($i>0) { //&&!preg_match("#.*([A-Za-z0-9]+?)\.([A-Za-z0-9]+?)\((.*?)\).*#",$Out[$i])){
					$d1 = Sys::$Modules[$Module]->getAnalyzedObjectClass($Out[$i]);
					$Object=$LastDataSource->getChildObjectClass($d1["ObjectClass"],(isset($lastkey["Key"]))?$lastkey["Key"]:null);
                    //$GLOBALS["Chrono"]->start("MODULE splitQuery tableau test");
					if (!is_object($Object)){
						//parent
						$Object=$LastDataSource->getParentObjectClass($d1["ObjectClass"],(isset($lastkey["Key"]))?$lastkey["Key"]:null);
						if (isset($lastkey["Key"])){
							$Ass=$LastDataSource->getParentAssociation($lastkey["Key"]);
						}
					}else{
						//child
						if (isset($d1["Key"])){
							$Ass=$LastDataSource->getChildAssociation($d1["Key"]);
						}
						if (isset($lastkey["Key"])){
							$Ass=$LastDataSource->getChildAssociation($lastkey["Key"]);
						}
					}
                    //$GLOBALS["Chrono"]->stop("MODULE splitQuery tableau test");
					$lastkey = $d1;
				}

                //TEST DE LA VUE
				if (isset($d1["View"])){
					$LastView = $d1["View"];
				}
				if ($i==0)$Object = $LastDataSource;

                //Détermination des dernières informations
				if (is_object($Object)) {
					if ($Last>-1) {
						//on Genere la donn� (Ds/Value/Ds/Value)
						for ($j=$Last+1;$j<$i;$j++) {
							$Temp=$Out[$j];
							//echo $Out[$Last]."-".$Object[0].$Object[1]."\r\n";
							$Tab['Module'] = $LastDataSource->Module;
							$Tab['DataSource'] = $LastName;
							if (is_object($LastAssociation)){
								$Tab['Key'] = $LastAssociation->titre;
							}
							$Tab['Value'] = $Temp;
							$Result[] = $Tab;
						}
						//Le cas ou on fait une requete parent (Ds/Ds/Value)
						if ($Last==$i-1) {
							$Tab['Module'] = $LastDataSource->Module;
							$Tab['DataSource'] = $LastName;
							if (is_object($LastAssociation)){
								$Tab['Key'] = $LastAssociation->titre;
							}
							$Tab['Value'] = "";
							$Result[] = $Tab;
						}
					}
					$Last=$i;
                    $LastName = $Object->titre;
					$LastDataSource = $Object;
					if ($i>0)$LastAssociation = $Ass;
					unset($Tab);
				}

            }

			//DETERMINATION DU TYPE DE REQUETE
			if (($Last>-1)&&($Last!=sizeof($Out)-1)) {
				//Si il existe un objectclass et qu'il n'est pas le dernier
				for ($j=$Last+1;$j<$i;$j++) {
					//on boucle du dernier objectclass jusqu'a la derniere occurence
					if ($j==$i-1) {
						//Il s'agit de la derniere occurence
						if (sizeof($Result)>0) {
							//Donc il y a plusieurs paires
							if (sizeof($Result)==1&&$Result[0]['Value']=="") {
								//La premiere valeur est vide et il n'y aura que deux paires donc c'est recherche de parent
								$Type="Parent";
							}elseif ($Result[0]['Value']=="*"&&sizeof($Result)==2) {
								//La premier valeur est une etoile et il y aura trois paires donc c'est une recherche parent recursive
								$Type="ParentRecursiv";
							}elseif ($Out[$Last+1]=="*"){
								//La derniere valeur est une etoile
								$Type="ChildRecursiv";
							}elseif($Out[$Last+1]==""){
								//La derniere  valeur est vide donc il s'agit d'une recherche d'enfants
								$Type="Child";
							}elseif (isset($Out[$Last+2])&&$Out[$Last+2]!=""&&$LastDataSource->isReflexive()){
								//il s'agit d'une recherche sur un objet recursif car deux valeurs
								$Type="Direct";
							}elseif (!isset($Out[$Last+2])&&$Out[$Last+1]!="") {
								//il s'agit d'une recherche sur un objet recursif car deux valeurs
								$Type="Direct";
							}else{
								//La requete présente une erreur
								$Type="Erreur";
							}
//							$Obj = $Result[count($Result)-1]['DataSource']."/".$Out[$Last];
							$Obj = $Out[$Last];
						}else{
							//Donc il n'y a qu'une paire
							if ($Out[$Last+1]=="*"){
								//La derniere valeur est une etoile
								$Type="ChildRecursiv";
							}elseif ($Out[$Last+1]!=""){
								//La derniere valeur n'est pas vide  donc c'est une recherche directe
								$Type="Direct";
							}elseif ($Out[$Last+1]==""){
								//La derniere valeur est vide donc il s'agit d'une recherhce d'enfant
								$Type="Child";
							}else{
								//La requete présente une erreur
								$Type="Erreur";
							}
							$Obj = $Out[$Last];
						}
						//die('REFAIRE GESTION du $OUT ICI !!! Module.class.php ligne 509');
						//construction de l'url interface compl�te
						$it2 = $it = "";
						for ($g=$Last+1,$c=sizeof($Out);$g<$c;$g++) $it.=(!empty($it)? "/":"").$Out[$g];
						for ($g=$Last+2,$c=sizeof($Out);$g<$c;$g++) $it2.=(!empty($it2)? "/":"").$Out[$g];
						//On teste si le dernier parametre est une interface et le premier une valeur
						if (DEBUG_INTERFACE)echo "LAST PAIR $Lien Strict : $Strict\r\n";
						if (!$Strict&&Bloc::isInterface($Module,$Out[$Last],$Out[$j],true)) {
							//Si c'est une interface alors ce n est pas une requete
							$O = $LastDataSource->titre;
							$Type="Interface";
							$Interface['DataSource']=$O;
							$Interface['Interface']=$Out[$j];
							if (sizeof($Out)-1==$j&&$j-$Last<2){
								$Tab['DataSource'] = $O;
								$Tab['Module'] = $LastDataSource->Module;
								if (is_object($LastAssociation)){
									$Tab['Key'] = $LastAssociation->titre;
								}
								if (!empty($LastView)){
									$Tab['View'] = $LastView;
								}
								$Result[] = $Tab;
							}
							$Result[0]['InterfacePath'] = Bloc::getInterface($Module,$Out[$Last],$Out[$j],true);
						}elseif (!$Strict&&!empty($it)&&Bloc::isInterface($Module,$Out[$Last],$it,true)){
							$O = $Out[$Last];
							$Type="Interface";
							$Result[0]['InterfacePath'] = Bloc::getInterface($Module,$Out[$Last],$it,true);
							$Result[sizeof($Result)-1]['Value'] = '';
						}elseif (!$Strict&&!empty($it2)&&Bloc::isInterface($Module,$Out[$Last],$it2,true)){
							$O = $Out[$Last];
							$Type="Interface";
							$Result[0]['InterfacePath'] = Bloc::getInterface($Module,$Out[$Last],$it2,true);
							$Result[sizeof($Result)-1]['Value'] = '';
						}elseif (!$Strict&&$Type=="Erreur"&&Bloc::isInterface($Module,$Out[$Last],$it,false)){
							$O = $Out[$Last];
							$Type="Interface";
							$Result[sizeof($Result)-1]['Value'] = '';
							$Result[0]['InterfacePath'] = Bloc::getInterface($Module,$Out[$Last],$it,false);
						}else{
							$Temp=$Out[$j];
							$Tab['Module'] = $LastDataSource->Module;
							$Tab['DataSource'] = $LastDataSource->titre;
							if (is_object($LastAssociation)){
								$Tab['Key'] = $LastAssociation->titre;
							}
							if (!empty($LastView)){
								$Tab['View'] = $LastView;
							}
							$Tab['Value'] = $Temp;
							if (is_array($Result)||empty($Result))$Result[] = $Tab;
							if (DEBUG_INTERFACE)echo "CAS STANDARD\r\n";
							if (!$Strict)$Result[0]['InterfacePath'] = Bloc::getInterface($Module,$Out[$Last],$Out[$j],false);
						}
					}else{
						$Temp=$Out[$j];
						$Tab['Module'] = $LastDataSource->Module;
						$Tab['DataSource'] = $LastDataSource->titre;
						if (is_object($LastAssociation)){
							$Tab['Key'] = $LastAssociation->titre;
						}
						if (!empty($LastView)){
							$Tab['View'] = $LastView;
						}
						$Tab['Value'] = $Temp;
						$Result[] = $Tab;
						if (DEBUG_INTERFACE)echo "CAS STANDARD\r\n";
						if (!$Strict)$Result[0]['InterfacePath'] = Bloc::getInterface($Module,$Out[$Last],"",false);
					}
				}
	/*			if (count($Interface)>1) {
					$Result[] = $Interface;
				}*/
			}else{
				//Verification de la syntaxe de la requete il ne faut pas que les deux dernieres occurences soient des datasources
				if (!isset($Out[$Last-1])||$Out[$Last]!=$Out[$Last-1]){
	 				if (DEBUG_INTERFACE)echo $Lien."--> CHECK SYNTAXE FORCE CHILD - ".$Out[$Last]."\r\n";
					$Type="Child";
					$Tab['Module'] = $LastDataSource->Module;
					$Tab['DataSource'] = $LastDataSource->titre;
					if (is_object($LastAssociation)){
						$Tab['Key'] = $LastAssociation->titre;
					}
					if (!empty($LastView)){
						$Tab['View'] = $LastView;
					}
					$Tab['Value'] = "";
					$Result[] = $Tab;
                    if (!$Strict)$Result[0]['InterfacePath'] = Bloc::getInterface($Module,$Out[$Last],"",false);
				}else{
					$Type="Erreur";
				}
			}

			if ($Type=="Direct"||$Type=="Child") {
				//Verifions qu il ne s agit pas d une multisearch
				if (isset($Out[$Last+1])&&preg_match("#[\<\>\!\=\~]{1,2}#",$Out[$Last+1])) {
					$Type="Multi";
				}
			}
			//On genere la valeur de la query
			$Query=$Module;
			for ($f=0,$c=sizeof($Result);$f<$c;$f++) {
				$Query .= "/".$Result[$f]["DataSource"].(isset($Result[$f]["Key"])&&!empty($Result[$f]["Key"])?".".$Result[$f]["Key"]:"").(!empty($Result[$f]["Value"])?"/".$Result[$f]["Value"]:"");
			}
			//if ($Type!="Interface") $Query.= "/".$Out[sizeof($Out)-1];
			$Result[0]["Type"] = $Type;
			$Result[0]["Query"] = $Query;
			//On récupère le module de la requete de sortie
			switch ($Result[0]["Type"]){
				case "Direct":
				case "ChildRecursiv":
				case "Child":
					$Result[0]["Out"] =sizeof($Result)-1;
				break;
				case "Parent":
					$Result[0]["Out"] =sizeof($Result)-2;
				break;
				case "ParentRecursiv":
					$Result[0]["Out"] =sizeof($Result)-3;
				break;
				default: $Result[0]["Out"] =sizeof($Result)-1;
			}
		}
		//if (DEBUG_INTERFACE)echo $Lien."\r\n";
		//if ($Lien=="ContractBuyerId/Contract/2900") $GLOBALS["Systeme"]->Log->log("SPLIT QUERY",$Result);
		//if ($Lien=="Categorie/Kites/Produit/BANDIT/Donnee/Type=Image+Type=ImageVideo")print_r($Result);
		if (DEBUG_INTERFACE)print_r($Result);

        //cache splitQuery for heavy load
	    Sys::$Modules[$Module]->_arrayQueryCache[$Strict.'-'.$Lien] = $Result;
		//TEST APC CACHE
		//ApcCache::setData('QUERY-'.$Strict.'-'.$Lien,$Result);

        return $Result;
	}
	//Appel depuis storproc pour lexecution de requete
	function callData($Query,$recurs="",$Ofst="",$Limit="",$OrderType="",$OrderVar="",$Selection="",$GroupBy=""){
		//correctio nau cas ou on ait pas le module
		if (!preg_match("#^".$this->Nom."\/#",$Query)){
			$Query = $this->Nom.'/'.$Query;
		}
		//Traitement des access
		/*if (isset(Sys::$User->Access)&&
			is_array(Sys::$User->Access)) 
			foreach (Sys::$User->Access as $A){
				if ($A->ObjectModule==$this->Nom){
					$LastQuery = $Query;
					$Al = preg_replace("#^".$this->Nom."\/#","",$A->Alias);
					if (sizeof(explode($Al,$Query))==1){
						$Query = preg_replace('#^'.$A->ObjectClass.'#',$Al,$Query);
//						$GLOBALS["Systeme"]->Log->log("ACCESS ".$LastQuery,$Query);
					}
				}
		}*/
		//On charge le Schema
		$this->loadSchema();
 		$TabQuery = Module::splitQuery($Query,true);
//$GLOBALS["Systeme"]->Log->log("$Query",$TabQuery);
//		if ($TabQuery[0]["Type"]=="Erreur") return false;
//		if (DEBUG_QUERY)KError::Set('QUERY '.$this->Nom."/".$Query,"",KError::$INFO);
		Module::$LAST_QUERY = $Query;

        try{
			$Tab =  $this->Db->searchObject($TabQuery,$recurs,$Ofst,$Limit,$Query,$OrderType,$OrderVar,$Selection,$GroupBy);
		}catch ( Exception $e ) {
            die($e->getMessage());
		}
		//On ajoute les informations de la requete
		for ($i=0,$c=sizeof($Tab);$i<$c;$i++) {
			if (sizeof($Tab[$i]))$Tab[$i]["QueryType"] = $TabQuery[0]["Type"];
			if (sizeof($Tab[$i]))$Tab[$i]["Query"] = $Query; //$TabQuery[0]["Query"];
			if (sizeof($Tab[$i]))$Tab[$i]["Module"] = $this->Nom; //$TabQuery[$TabQuery[0]["Out"]]["Module"];*/
		}
		return $Tab;
	}

	/**
	* Appel principal depuis la classe Systeme
	*/
	function Affich($Lien) {
        $Bloc = new Bloc();
        if (!empty($Lien)) {
            $Bloc->setData($Lien);
            $Tab = Module::splitQuery($Lien, false);
            if (isset($Tab[0]['Query'])) $GLOBALS['Systeme']->setQuery($Tab[0]['Query']);
            //Definition des variables
            $T = $Bloc->Conf;
            if (sizeof($T)) foreach ($T as $K => $V) {
                $V = Process::processingVars($V);
                Process::RegisterTempVar($K, $V);
            }
            $Bloc->Generate();
        }
        $Result = $Bloc->Affich();
		if (isset($GLOBALS["Systeme"]->CurrentSkin->Template)&&$GLOBALS["Systeme"]->CurrentSkin->Template){
			//On verifie la nature de l'objet à afficher. Si c'est un objet browseable alors on extrait sa template.
			//$IdObject = $this->Db->findByTitle($Tab[0]["DataSource"]);
			if (isset($_GET["DEBUG_TEMPLATE"])&&$_GET["DEBUG_TEMPLATE"]) echo "<h1>DEBUG TEMPLATE</h1><ul>";
			if (isset($Tab[0]["DataSource"])&&isset($Tab[0]['Type'])&&$Tab[0]["Type"]=="Direct"){
				$r =  $this->Db->searchObject($Tab,false,0,1,$Lien);
				$Template = isset($r[0]["Template"]) ? $r[0]["Template"] : "";
				if (isset($_GET["DEBUG_TEMPLATE"])&&$_GET["DEBUG_TEMPLATE"]) echo "<li>- Template propre à l'élément trouvée: $Template</li>";
			}else	if (isset($_GET["DEBUG_TEMPLATE"])&&$_GET["DEBUG_TEMPLATE"]) echo "<li>- pas d élement pour la base de donnée (recherche de type interface)</li>";
			if (!empty($GLOBALS["Systeme"]->CurrentMenu->Template)&&empty($Template)){
				$Template = $GLOBALS["Systeme"]->CurrentMenu->Template;
				if (isset($_GET["DEBUG_TEMPLATE"])&&$_GET["DEBUG_TEMPLATE"]) echo "<li>- Template par défaut du menu $Template</li>";
			}else	if (isset($_GET["DEBUG_TEMPLATE"])&&$_GET["DEBUG_TEMPLATE"]) echo "<li>- pas de menu en cours.</li>";
			
/*			//On verifie que l'objet a bien une template de définie et que la skin autorise l'utilisation de templates.
			$r =  $this->Db->searchObject($Tab,false,0,1,$Lien);
			$Template = isset($r[0]["Template"]) ? $r[0]["Template"] : "";*/
			
			//Chargement de la template
			if (isset($Template)){
				if (isset($_GET["DEBUG_TEMPLATE"])&&$_GET["DEBUG_TEMPLATE"]) echo "<li>Affichage template $Template</li>";
				$Tmpl=$this->loadTemplate($Template);
				$Tmpl->Generate();
				$Result = $Bloc->parseData($Result,$Tmpl->Affich());
			}else	if (isset($_GET["DEBUG_TEMPLATE"])&&$_GET["DEBUG_TEMPLATE"]) echo "<li>- Pas de template définie.</li>";
			if (isset($_GET["DEBUG_TEMPLATE"])&&$_GET["DEBUG_TEMPLATE"]) echo "</ul>";
		}
		return $Result;
	}

	function Query($Prefix,$Class,$Action) {
		$this->loadSchema();
		if ($Action=="AH"){
		//Ajout d'heritage
			$Vars=Process::ProcessQuery("Heritage_");
			$Entree["But"] = "Add_heritage";
			$Entree["Parent"] = $Vars["ObjectType"];
			for ($i=0;$i<10;$i++){
				if (array_key_exists("Enfant_$i",$Vars)){
					$Entree["Enfant"][] = $Vars["Enfant_$i"];
				}
			}
			$Entree["NomPropriete"] = $Vars["NomPropriete"];
			$Entree["TypePropriete"] = $Vars["TypePropriete"];
			$Entree["Description"] = $Vars["DescPropriete"];
			$Entree["Id"] = $Vars["Id"];
			$this->Db->Query($Entree);
		}
		if ($Action=="M"){
		//Modification d'objet
			$Generic = new genericClass($this->Nom);
			$Generic->initFromId($GLOBALS["Systeme"]->PostVars["FormSys_Identifiant"],$Class);
			$Generic->Update = 1;
			$Generic->saveChanges();
		}
		if ($Action=="A"){
		//Ajout d'objet
			$Generic = new genericClass($this->Nom);
			$Generic->initFromType($Class);
			$Generic->saveChanges();
		}
		if ($Action=="AJ"){
		//Ajout d'association
			$Generic = new genericClass($this->Nom);
			$IdEnfant = $GLOBALS["Systeme"]->PostVars["Form_Objet"];
			$IdParent = $GLOBALS["Systeme"]->PostVars["Form_ParentId"];
			$ObjParent = $GLOBALS["Systeme"]->PostVars["Form_ObjectParent"];
			$Generic->initFromId($IdEnfant,$Class);
			$Generic->addFkey($ObjParent,$IdParent);
			$Generic->Save();
		}
		if ($Action=="S"){
		//Suppression
			$Id = $GLOBALS["Systeme"]->PostVars["Form_Id"];
			$ObjectType = $GLOBALS["Systeme"]->PostVars["Form_ObjectType"];
			if ($GLOBALS["Systeme"]->PostVars["Form_Delete"]=="Obj"){
			//d'un objet
				$Generic = new genericClass($this->Nom);
				$Generic->initFromQuery($GLOBALS["Systeme"]->PostVars["Form_Query"]);
				$Old = $Generic->Historique[count($Generic->Historique)-1];
				$GLOBALS["Systeme"]->registerVar("oldId",$Old["Id"]);
				$GLOBALS["Systeme"]->registerVar("oldObj",$Old["ObjectType"]);
				$Generic->setDelete();

			}elseif ($GLOBALS["Systeme"]->PostVars["Form_Delete"]=="Assoc"){
			//d'une association
				$Id =  $GLOBALS["Systeme"]->PostVars["FormSys_Objet"];
				$Generic = new genericClass($this->Nom);
				$Generic->initFromId($Id,$Class);
				$Old = explode("/",$GLOBALS["Systeme"]->PostVars["Form_ObjetSuppr"]);
				$Generic->deleteFKey($Old[0],$Old[1]);
				$Generic->Save();
			}
		}

	}

	function saveData($ObjTemp) {
//		print_r($ObjTemp);
	}
	
	function getTriggers($T="") {
		if (!$T)return $this->Triggers;
		else {
			if (is_array($this->Triggers)) foreach ($this->Triggers as $Tr) if ($Tr->Name==$T) return $Tr;
		}
	}

	/****************************
	*	PUBLIC FUNCTION
	*****************************/
	/**
	* Function recuperant la liste des objets browseable pour la gestion des templates
	* @return array 
	*/
	public function getBrowseable(){
		$temp = Array();
		for ($i=0,$c=sizeof($this->Db->ObjectClass);$i<$c;$i++){
			if ($this->Db->ObjectClass[$i]->browseable)$temp[] = $this->Db->ObjectClass[$i];
		}
		return $temp;
	}
	/****************************
	*	PLUGINS
	*****************************/
	/**
	* getPluginCategories
	* Retourne la liste des categories de plugin d'un module
	* @return array of string
	*/
	public function getPluginCategories(){
		$dir = "Modules/".$this->Nom."/Plugins";
		$out=Array();
		if ($handle = opendir(ROOT_DIR.$dir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && is_dir(ROOT_DIR.$dir.'/'.$file) && !preg_match("#^\..*#",$file)) {
					$out[] = $file;
				}
			}
			closedir($handle);
		}else{
			//Le dossier n'existe pas
			fileDriver::mk_dir(ROOT_DIR.$dir);
		}
		return $out;
	}
	/**
	* getPlugins
	* Retourne la liste des plugins actif ou non du module.
	* @return array of plugins
	*/
	public function getPlugins($Category){
		$dir = "Modules/".$this->Nom."/Plugins/$Category";
		if (!file_exists(ROOT_DIR.$dir)) return false;
		$out=Array();
		if ($handle = opendir(ROOT_DIR.$dir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && is_dir(ROOT_DIR.$dir.'/'.$file) && !preg_match("#^\..*#",$file)) {
					$out[] = $file;
				}
			}
			closedir($handle);
		}
		//Instanciation des plugins
		$Results = Array();
		foreach ($out as $o) $Results[] = Plugin::createInstance($this->Nom,$Category,$o);
		return $Results;
	}
	/**
	* getPlugin
	* Retourne un plugin précis à l'aide d'un nom et d'une categorie sous la forme CAT/PLUGIN_NAME
	* @return object plugins
	*/
	public function getPlugin($Url){
		$dir = "Modules/".$this->Nom."/Plugins/$Url";
		if (!file_exists(ROOT_DIR.$dir)) return false;
		//Instanciation des plugins
		$t = explode("/",$Url);
		$Results = false;
		$Results = Plugin::createInstance($this->Nom,$t[0],$t[1]);
		return $Results;
	}
}



?>
