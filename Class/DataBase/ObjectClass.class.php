<?php

class ObjectClass extends Root{
	//LIAISONS
	var $Cibles = Array();
	var $Proprietes = Array();
	var $Functions = Array();
	var $Interfaces = Array();
	var $Configuration = Array();
	var $Associations = Array();
	var $Categories = Array();
	var $Filters = Array();
	var $Views = Array();
	var $FKEY = Array();
	var $RKEY = Array();
	//OPTIONS
	var $titre;
	var $driver;
	var $order;
	var $orderType;
	var $Module;
	var $Prefix;
	var $Reference = 0;
	var $Heritage = 0;
	var $Dico = 0;
	var $master;
	var $tagObjects;
	var $browseable;
	var $generateUrl;
	var $Class = 0;
	var $logEvent = 0;
	var $color;
	var $cache;
	var $hidden;
	var $Interface;
	var $className = 0;
	var $noRecursivity = 0;							//permet la création d'une clef recursive sans le comportement recursif
	var $stopPage = 0;							//stoppe l'exploration des pages pour les clefs sous jacentes.
	var $Operations = Array("add"=>true,"edit"=>true,"delete"=>true,"export"=>true);
	//DEFAULT
	var $defaultView = false;
	var $Default=0;
	var $DroitsDefault;
	var $Description;
	var $Conf;
	var $Icon;
	var $objRef;
	var $References ="";
	var $ReferChilds;
	var $ProprietesRef;
	var $isLongKey = Array("1,n","0,n");
	var $isShortKey = Array("1,1","0,1");
	var $ObjectTable = Array();
	var $searchType = "keywords"; 						//plaintext | keywords
	//BASE DE DONNEE PAR DEFAUT
	var $Bdd = 0;

	/* ----------------------------
	 |       Initialisation       |
	 -----------------------------*/

	static function createInstance($schema=false,$Module){
		if (is_array($schema['@']))$Driver = $schema['@']['driver'].'Driver';
		else $Driver = $schema['driver'].'Driver';
		$Class = new $Driver($schema,$Module);
		$Class->Module = $Module;
		return $Class;
	}


	function __construct($schema=false,$Module) {
		$this->Module = $Module;
		$this->Prefix = MAIN_DB_PREFIX.$Module."-";
		if ($schema) $this->initFromXml($schema);
	}

	function initFromXml($schema){
		//Initialisation des attributs de l objectClass
		if (isset($schema['@']['title']))$this->titre = $schema['@']['title'];
		if (isset($schema['@']['searchType']))$this->searchType = $schema['@']['searchType'];
		if (isset($schema['@']['driver']))$this->driver = $schema['@']['driver'].'Driver';
		if (isset($schema['@']['order']))$this->order = $schema['@']['order'];
		if (isset($schema['@']['orderType']))$this->orderType = $schema['@']['orderType'];
		if (isset($schema['@']['Droits']))$this->DroitsDefault = $schema['@']['Droits'];
		if (isset($schema['@']['AccessPoint']))$this->AccessPoint = $schema['@']['AccessPoint'];
		if (isset($schema['@']['Default']))$this->Default = $schema['@']['Default'];
		if (isset($schema['@']['Dico']))$this->Dico = $schema['@']['Dico'];
		if (isset($schema['@']['master']))$this->Master = $schema['@']['master'];
		if (isset($schema['@']['Heritage']))$this->Heritage = $schema['@']['Heritage'];
		if (isset($schema['@']['Class']))$this->Class = $schema['@']['Class'];
		if (isset($schema['@']['Description']))$this->Description = $schema['@']['Description'];
		if (isset($schema['@']['Reference']))$this->Reference = $schema['@']['Reference'];
		if (isset($schema['@']['Icon']))$this->Icon = $schema['@']['Icon'];
		if (isset($schema['@']['Interface']))$this->Interface = $schema['@']['Interface'];
		if (isset($schema['@']['plugin']))$this->plugin = $schema['@']['plugin'];
		if (isset($schema['@']['generateUrl']))$this->generateUrl = $schema['@']['generateUrl'];
		if (isset($schema['@']['logEvent']))$this->logEvent = $schema['@']['logEvent'];
		if (isset($schema['@']['color']))$this->color = $schema['@']['color'];
		if (isset($schema['@']['cache']))$this->cache = $schema['@']['cache'];
		if (isset($schema['@']['hidden']))$this->hidden = $schema['@']['hidden'];
		if (isset($schema['@']['className']))$this->className = $schema['@']['className'];
		if (isset($schema['@']['noRecursivity']))$this->noRecursivity = $schema['@']['noRecursivity'];
		if (isset($schema['@']['stopPage']))$this->stopPage = $schema['@']['stopPage'];
        if (isset($schema['@']['Operations']))$this->setOperations($schema['@']['Operations']);
		if (isset($schema['@']['tagObjects'])){
			$to = explode(',',$schema['@']['tagObjects']);
			foreach ($to as $t) $this->tagObjects[] = trim($t);
		}
		if (isset($schema['@']['browseable'])){
			$this->browseable = $schema['@']['browseable'];
			$this->generateUrl = 1;
		}
		if (isset($schema['@']['Display']))$this->Display = $schema['@']['Display'];
		if ($schema['@']['driver']=="ldap"){
			$this->ObjectClass = $schema['@']['ObjectClass'];
		}
		if (isset($schema['@']['driver'])&&$schema['@']['driver']=="sqlite")$this->Bdd = 1;
		//PARSE XML 
		$this->parseXml($schema['#']);
		//INITIALISATION DU PILOTE
		$this->createTargets();
	}
	private function setOperations($str) {
		//reset
		$this->Operations  = Array("add"=>false,"edit"=>false,"delete"=>false,"export"=>false);
		$str = explode(',',$str);
		if (sizeof($str))foreach ($str as $s){
			$this->Operations[$s] = true;
		}
	}
	public function getOperations(){
		return $this->Operations;
	}
    public function getInterfaces(){
        return $this->Interfaces;
    }
	/**
	 * __________________________________________________________________________________________________
	 * 																							   PARSER
	 */
	/**
	 * parseXml
	 * Parse le xml pour trouver:
	 * - les catégories
	 * - les conditions
	 * - les catégories
	 * - les clefs
	 * @param XmlArraycreateTarge
	 * @param Array Tableau temporaire contenant des proprietes et des clefs
	 * @param String category container
	 * @void
	 */
	private function parseXml($Xml,$o = Array(),$lc="") {
		if (is_array($Xml))$XmlKeys=array_keys($Xml);
		//Extraction des cles du tableau associatif
		for ($i=0;$i<count($XmlKeys);$i++) {
			switch ($XmlKeys[$i]) {
				case "PROPERTY":
				case "PROPERTIES":
					//proprietes
					$temp = $Xml[$XmlKeys[$i]];
					for ($j=0;$j<sizeof($temp);$j++) {
						$this->Proprietes[$temp[$j]['#']] = $this->parseAttributes($temp[$j]['@'],$temp[$j]['#']);
						$categoryName = (isset($temp[$j]['@']['category']))?$temp[$j]['@']['category']:((!empty($lc))?$lc:OBJECTCLASS_CATEGORY_DEFAULT);
						$this->addCategory(Array("title"=>$categoryName),Array("type"=>"property","title"=>$temp[$j]['#']));
						$o[] = Array("type"=>"property","name"=>$temp[$j]['#']);
					}
				break;
				case "IF":
					//TODO CONDITIONS
					//conditions
					/*$temp = $Xml[$XmlKeys[$i]];
					for( $i=0; $i<sizeof($temp); $i++ ) {
						//On ajoute les props
						$temp2 = $temp[$i]['#']['PROPERTIES'];
						$ps = array();
						for ($j=0;$j<count($temp2);$j++) {
						//$this->Proprietes[$temp2[$j]['#']] = $this->parseAttributes($temp2[$j]['@'],$temp2[$j]['#']);
						//$this->Proprietes[$temp2[$j]['#']]["Emp"] = $nbprop + $j;
						$ps[] = $temp2[$j]['#'];
						}
						$t = array("Name" => $temp[$i]['@']['name'],
									"Value" => explode(",",$temp[$i]['@']['value'] ),
									"About" => $temp[$i]['@']['about'],
									"PropsName" => $ps);
						$this->Conditions[] = $t;
						$o[] = $this->Proprietes[$t];
					}*/
				break;
				case "CATEGORY":
					//categories
					$temp = $Xml[$XmlKeys[$i]];
					for ($j=0;$j<count($temp);$j++) {
						//analyse recursive
						$this->parseXml($temp[$j]['#'],Array(),$temp[$j]['@']['title']);
						//Ajout de la category
						$this->addCategory($temp[$j]['@']);
					}
				break;
				case "FUNCTION":
					//fonctions
					$temp = $Xml[$XmlKeys[$i]];
					for ($j=0;$j<sizeof($temp);$j++) {
						if (!is_array($temp[$j]['#'])){
							#DEPRECATED
							$this->Functions[$temp[$j]['#']] = $this->parseAttributes((isset($temp[$j]['@']))?$temp[$j]['@']:Array());
							$o[] = Array("type"=>"function","name"=>$temp[$j]['#']);
						}else{
							$this->Functions[$temp[$j]['@']['name']] = $this->parseAttributes((isset($temp[$j]['@']))?$temp[$j]['@']:Array());
							//parameters of function
							if (isset($temp[$j]['#']["PROPERTIES"])&&is_array($temp[$j]['#']["PROPERTIES"]))foreach ($temp[$j]['#']["PROPERTIES"] as $p){
								$this->Functions[$temp[$j]['@']['name']]["properties"][$p['#']] = $this->parseAttributes((isset($p['@']))?$p['@']:Array());
								$this->Functions[$temp[$j]['@']['name']]["properties"][$p['#']]["name"] = $p['#'];
							}
							//optionnal actions
							if (isset($temp[$j]['#']["ACTION"])&&is_array($temp[$j]['#']["ACTION"]))foreach ($temp[$j]['#']["ACTION"] as $a){
								$this->Functions[$temp[$j]['@']['name']]["actions"][] = $a["#"];
							}
						}
					}
				break;
				
				case "INTERFACES":
					//Interfaces
					$temp = $Xml[$XmlKeys[$i]];
					for ($j=0;$j<sizeof($temp);$j++) {
						//parameters of function
						if (isset($temp[$j]['#']["FORM"])&&is_array($temp[$j]['#']["FORM"]))foreach ($temp[$j]['#']["FORM"] as $p){
							$this->Interfaces[$temp[$j]['@']['name']][$p['#']] = $this->parseAttributes((isset($p['@']))?$p['@']:Array());
							$this->Interfaces[$temp[$j]['@']['name']][$p['#']]["name"] = $p['#'];
						}
					}
				break;
				case "CONFIGURATION":
					//Interfaces
					$temp = $Xml[$XmlKeys[$i]];
					for ($j=0;$j<sizeof($temp);$j++) {
						//parameters of function
						if (isset($temp[$j]['#']["FORM"])&&is_array($temp[$j]['#']["FORM"]))foreach ($temp[$j]['#']["FORM"] as $p){
							$this->Configuration[$temp[$j]['@']['name']][$p['#']] = $this->parseAttributes((isset($p['@']))?$p['@']:Array());
							$this->Configuration[$temp[$j]['@']['name']][$p['#']]["name"] = $p['#'];
						}
					}
				break;
				
//TODO
/*				case "INTERFACE":
					//interface
					$temp = $Xml[$XmlKeys[$i]];
					for ($j=0;$j<sizeof($temp);$j++) {
						if (!is_array($temp[$j]['#'])){
							#DEPRECATED
							$this->Functions[$temp[$j]['#']] = $this->parseAttributes((isset($temp[$j]['@']))?$temp[$j]['@']:Array());
							$o[] = Array("type"=>"interface","name"=>$temp[$j]['#']);
						}else{
							$this->Functions[$temp[$j]['@']['name']] = $this->parseAttributes((isset($temp[$j]['@']))?$temp[$j]['@']:Array());
							//parameters of function
							if (isset($temp[$j]['#']["PROPERTIES"])&&is_array($temp[$j]['#']["PROPERTIES"]))foreach ($temp[$j]['#']["PROPERTIES"] as $p){
								$this->Functions[$temp[$j]['@']['name']]["properties"][$p['#']] = $this->parseAttributes((isset($p['@']))?$p['@']:Array());
								$this->Functions[$temp[$j]['@']['name']]["properties"][$p['#']]["name"] = $p['#'];
							}
							//optionnal actions
							if (isset($temp[$j]['#']["ACTION"])&&is_array($temp[$j]['#']["ACTION"]))foreach ($temp[$j]['#']["ACTION"] as $a){
								$this->Functions[$temp[$j]['@']['name']]["actions"][] = $a["#"];
							}
						}
					}
				break;*/
				
				case "PARENT":
				case "FKEY":
					//clef parentes internes
					$temp = $Xml[$XmlKeys[$i]];
					for ($j=0;$j<sizeof($temp);$j++) {
						$this->FKEY[$temp[$j]['#']]=$temp[$j];
						$categoryName = (isset($temp[$j]['@']['category']))?$temp[$j]['@']['category']:((!empty($lc))?$lc:OBJECTCLASS_CATEGORY_DEFAULT);
						$this->addCategory(Array("title"=>$categoryName),Array("type"=>"fkey","title"=>$temp[$j]['#'],"attributes"=>$temp[$j]['@']));
						$o[] = Array("type"=>"fkey","name"=>$temp[$j]['#'],"attributes"=>$temp[$j]['@']);
					}
				break;
				case "CHILD":
				case "RKEY":
					//clefs enfantes internes
					$temp = $Xml[$XmlKeys[$i]];
					for ($j=0;$j<sizeof($temp);$j++) {
						$this->RKEY[$temp[$j]['#']]=$temp[$j];
						$categoryName = (isset($temp[$j]['@']['category']))?$temp[$j]['@']['category']:((!empty($lc))?$lc:OBJECTCLASS_CATEGORY_DEFAULT);
						$this->addCategory(Array("title"=>$categoryName),Array("type"=>"rkey","title"=>$temp[$j]['#'],"attributes"=>$temp[$j]['@']));
						$o[] = Array("type"=>"rkey","name"=>$temp[$j]['#'],"attributes"=>$temp[$j]['@']);
					}
				break;
				case "VIEW":
					//vues
					$temp = $Xml[$XmlKeys[$i]];
					for ($j=0;$j<sizeof($temp);$j++) {
						$V = View::createInstance($temp[$j],$this);
						$this->Views[] = $V;
						if ($V->default) $this->defaultView = $V;
					}
				break;
				case "FILTER":
					//filter
					$temp = $Xml[$XmlKeys[$i]];
					for ($j=0;$j<sizeof($temp);$j++) {
						$t = new StdClass();
						$t->filter = $temp[$j]['#'];
						$t->name = $temp[$j]['@']['name'];
						if (isset($temp[$j]['@']['color'])) $t->color = $temp[$j]['@']['color'];
						if (isset($temp[$j]['@']['view'])) $t->view = $temp[$j]['@']['view'];
						$this->Filters[] = $t; 
					}
				break;
				default:
					//Klog::l("ERREUR DE CONFIGURATION DANS LE MODULE ".$this->Nom." : Borne Inconnue ".$XmlKeys[$i]);
				break;
			}
		}
		//Ajout des elements en fonction de certains attributs
		//Ajout des propriétés propre au référencement
		//INITILISATION DES PROPRIETES DYNAMIQUES
		if (isset($this->generateUrl)&&$this->generateUrl){
			//On ajoute une proprietes Url
			$this->Proprietes["Url"] = $this->parseAttributes(array("type"=>"link","searchOrder"=>3,"auto"=>0,"category"=>"Configuration", "form"=>1,"fiche"=>1),"Url");
			$this->addCategory(Array("title"=>"Configuration"),Array("type"=>"property","title"=>"Url"));
		}
		//GESTION DES PLUGINS
		if (isset($this->plugin)&&$this->plugin){
			//On ajoute une proprietes Plugin
			if (!isset($this->Proprietes["Plugin"]))
				$this->Proprietes["Plugin"] = $this->parseAttributes(array("type"=>"plugin","category"=>"Configuration","query"=>"Explorateur/_Dossier/Modules/".$this->Module."/Plugins/".$this->titre."/_Dossier::Nom", "form"=>1,"fiche"=>1, "obligatoire"=>1),"Plugin");
			$this->addCategory(Array("title"=>"Configuration"),Array("type"=>"property","title"=>"Plugin"));
			//On ajoute une propriete PluginConfig
			$this->Proprietes["PluginConfig"] = $this->parseAttributes(array("type"=>"pluginconfig","category"=>"Configuration", "form"=>1,"fiche"=>1, "auto"=>1),"PluginConfig");
			$this->addCategory(Array("title"=>"Configuration"),Array("type"=>"property","title"=>"PluginConfig"));
		}
		if ($this->browseable){
			//On ajoute une proprietes Template
			if(!isset($this->Proprietes["Template"])) {
                $this->Proprietes["Template"] = $this->parseAttributes(array("type" => "modele", "category" => "Configuration", "query" => "Systeme/ActiveTemplate::Id::Nom", "form" => 1, "fiche" => 1, "auto" => 1), "Template");
                $this->addCategory(Array("title" => "Configuration"), Array("type" => "property", "title" => "Template"));
            }
			//TitleMeta
			$this->addCategory(Array("title"=>"Référencement","fold"=>'1'),Array("type"=>"property","title"=>"TitleMeta"));
			$o[] = Array("type"=>"property","name"=>"TitleMeta");
			$this->Proprietes['TitleMeta'] = Array('type'=>'metat','special'=>'multi','description'=>'META: Titre de la page (réseaux sociaux également)', "form"=>1, "auto"=>1, "large"=>"1", "fullTitle"=>"1");
			//DescriptionMeta
			$this->addCategory(Array("title"=>"Référencement"),Array("type"=>"property","title"=>"DescriptionMeta", "form"=>1,"fiche"=>1, "auto"=>1));
			$o[] = Array("type"=>"property","name"=>"DescriptionMeta");
			$this->Proprietes['DescriptionMeta'] = Array('type'=>'metad','special'=>'multi','description'=>'META: Description de la page (réseaux sociaux également)', "form"=>1, "auto"=>1, "large"=>"1", "fullTitle"=>"1");
			//KeywordsMeta
			$this->addCategory(Array("title"=>"Référencement"),Array("type"=>"property","title"=>"KeywordsMeta"));
			$o[] = Array("type"=>"property","name"=>"KeywordsMeta");
			$this->Proprietes['KeywordsMeta'] = Array('type'=>'metak','special'=>'multi','description'=>'META: Mots clefs de la page (réseaux sociaux également)', "form"=>1, "auto"=>1, "large"=>"1", "fullTitle"=>"1");
			//ImgMeta
			$this->addCategory(Array("title"=>"Référencement"),Array("type"=>"property","title"=>"ImgMeta"));
			$o[] = Array("type"=>"property","name"=>"ImgMeta");
			$this->Proprietes['ImgMeta'] = Array('type'=>'image','description'=>'META: Image de la page pour les réseaux sociaux', "form"=>1, "auto"=>1, "large"=>"1", "fullTitle"=>"1");
			//Displayed
			$this->addCategory(Array("title"=>"Configuration"),Array("type"=>"property","title"=>"Display"));
			$o[] = Array("type"=>"property","name"=>"Display");
			$this->Proprietes['Display'] = Array('type'=>'boolean','default'=> 1,'description'=>'Publier', "form"=>1,"fiche"=>1, "auto"=>1, "list"=>1,'listDescr'=>'Publier');
		}
		return $o;
	}
	/**
	 * __________________________________________________________________________________________________
	  * 																					    FUNCTIONS
	 */
	 /**
	  * getFunction
	  * return a detailed function beacon
	  * @param String name of the function
	  * @return Array
	  */
	public function getFunction ($n){
		if (is_array($this->Functions))foreach ($this->Functions as $nf=>$f){
			if ($nf==$n){
				return $f;
			}
		}
	}
	/**
	 * __________________________________________________________________________________________________
	  * 																					    FILTERS
	 */
	 /**
	  * getFilters
	  * return a list of custom filter
	  * @return Array
	  */
	public function getCustomFilters (){
		return $this->Filters;
	}
		/**
	 * __________________________________________________________________________________________________
	  * 																					   CATEGORIES
	 */
	/**
	 * addCategorie
	 * Ajoute ou  complete une categorie
	 * @param Array options de la categorie
	 * @param Array elements internes de la categorie
	 * @void
	 */
	private function addCategory($opts,$inner=false){
		//configuration de la category
		$c = (isset($this->Categories[$opts["title"]]))?$this->Categories[$opts["title"]]:Array();
		$c["elements"] = (isset($c["elements"])&&sizeof($c["elements"]))?$c["elements"]:Array();
		$ak = array_keys($opts);
		foreach ($ak as $a)if ($a!="title") $c[$a] = $opts[$a];
		//configuration des elements internes
		if ($inner){
			$alreadyexists = false;
			foreach ($c["elements"] as $k=>$e)if ($e["title"]==$inner["title"]){
                $alreadyexists = true;
                $index = $k;
			}
			if ($alreadyexists)
				$c["elements"][$index] = $inner;
			else
                $c["elements"][] = $inner;
		}
		//enregistrement
		$this->Categories[$opts["title"]] = $c;
	}
	/**
	 * getCategories
	 * recupère la liste des catégories
	 * @return Array(String)
	 */
	public function getCategories() {
		return array_keys($this->Categories);
	}
	/**
	 * getCategory
	 * renvoie une catégorie avec tous les attributs
	 * @return Array(String)
	 */
	public function getCategory($name) {
		return (isset($this->Categories[$name]))?$this->Categories[$name]:false;
	}
	/**
	 * getDescription
	 * retourne une description de l'objet courant
	 */
	public function getDescription() {
		return (!empty($this->Description))?$this->Description:$this->titre;
	}
	/**
	 * getElements
	 * retourne l'ensemble des elements de l'objet
	 * @return Array(Array(Object))
	 */
	public function getElements($viewname="") {
		//recuperation du tableau des categories
		$c = $this->Categories;
		$o = Array();
		//affectation des références de propriété et de clef pour chaque ligne.
		foreach ($c as $titlecat => $cat) {
			$o[$titlecat] = $cat;
            $o[$titlecat]["elements"] = array();
			foreach ($cat["elements"] as $k=>$el) {
				switch ($el["type"]){
					case "property":
						$o[$titlecat]["elements"][$k] = $this->getProperty($el["title"]);
						$o[$titlecat]["elements"][$k]["name"] = $el["title"];
						if (isset($el["default"]))$o[$titlecat]["elements"][$k]["value"] = $el["default"];
					break;
					case "rkey":
					case "fkey":
						//nom de lobjet distant
						$name= explode(',',$el["attributes"]["data"]);
						if ($el['type']=="fkey")
							$as = $this->getParentAssociation($el["title"],$name[0]);
						else
                            $as = $this->getChildAssociation($el["title"],$name[0]);
						if (!is_object($as)){
							//throw new Exception("MAUVAISE DESTINATION DE CLEF OU MODULE".print_r($el,true), 1);
							$GLOBALS["Systeme"]->Log->log("*************** ERREUR (MAUVAISE DESTINATION DE CLEF OU MODULE) ***************",$el);
							continue;
						}
						if ($el["type"]=="rkey") $ot = $as->getChildObjectClass();
						else $ot = $as->getParentObjectClass();
						$o[$titlecat]["elements"][$k] = Array(
							"type"			=>	$el["type"],
							"recursive"		=>	($as->isRecursiv()||$ot->isReflexive())?true:false,
							"description"	=>	$as->getDescription(),
							"name"			=>	$as->titre,
							"obligatoire"	=>	$as->isMandatory(),
							"card"			=>  ($as->isShort())?"short":"long",
							"objectName"	=>	$ot->titre,
							"objectModule"	=>	$ot->Module,
							"objectDescription"	=>	$ot->Description
						);
						//Ajout des attributs custom
						if (is_array($el["attributes"]))foreach ($el["attributes"] as $kat=>$at)
							if (!in_array($kat,Array("data","card")))$o[$titlecat]["elements"][$k][$kat] = $at;
					break;
				}
			}
		}
		if (!empty($viewname)){
			//recuperation de la vue
			$View = $this->getView($viewname);
			if (is_object($View)&&is_array($View->ViewProperties)){
				$o["_".$viewname]= Array();
				$o["_".$viewname]["elements"] = Array();
				$i=0;
				foreach ($View->ViewProperties as $k=>$P){
					$o["_".$viewname]["elements"][$i] = $P;
					$o["_".$viewname]["elements"][$i]["name"] = $k;
					$i++;
				}
			}
		}
		return $o;
	}
	/**
	 * getSearchOrder()
	 * Recupere la liste des searchOrder de la config en cours
	 */
	 function getSearchOrder($viewname="")
     {
        $O = Array();
        $A = "searchOrder";
        //Il faut le faire pour chaque langue
        $Tab = $this->getElements($viewname);
        foreach ($Tab as $CatName => $Cat) {
            if(!is_array($Cat)) continue;
            foreach ($Cat as $ElemsName => $Elems) {
                if(!is_array($Elems)) continue;
                foreach ($Elems as $Elem) {
                    if (isset($Elem[$A])) {
                        $O[] = $Elem;
                    }
                }
            }
        }
		if (!sizeof($O))
			return false;
		return $O;
	 }
	/**
	 * getParentElements
	 * retourne l'ensemble des elements parents de l'objet
	 * @return Array(Array(Object))
	 */
	public function getParentElements() {
		$o= Array();
		$k=0;
		foreach ($this->Associations as $A)
			if ($A->isChild($this->titre)){
				$ot = $A->getParentObjectClass();
				$o[$k] = Array(
					"type"			=>	"fkey",
					"recursive"		=>	($A->isRecursiv()||$ot->isReflexive())?true:false,
					"description"	=>	$A->getDescription(),
					"name"			=>	$A->titre,
					"obligatoire"	=>	$A->isMandatory(),
					"card"			=>  ($A->isShort())?"short":"long",
					"objectName"	=>	$ot->titre,
					"objectModule"	=>	$ot->Module,
					"objectDescription"	=>	$ot->Description,
					"field"			=> 	$A->getField("parent")
				);
				//Ajout des attributs custom
				if (is_array($A->attributes))foreach ($A->attributes as $kat=>$at)
					if (!in_array($kat,Array("data","card")))$o[$k][$kat] = $at;
				$k++;
			}
		return $o;
	}
	/**
	 * getChildElements
	 * retourne l'ensemble des elements enfants de l'objet
	 * @return Array(Array(Object))
	 */
	public function getChildElements() {
		$o= Array();
		$k=0;
		foreach ($this->Associations as $A)
			if ($A->isParent($this->titre)){
				$ot = $A->getChildObjectClass();
				$o[$k] = Array(
					"type"			=>	"rkey",
					"recursive"		=>	($A->isRecursiv()||$ot->isReflexive())?true:false,
					"description"	=>	$A->getDescription(),
					"name"			=>	$A->titre,
					"obligatoire"	=>	$A->isMandatory(),
					"card"			=>  ($A->isShort())?"short":"long",
					"objectName"	=>	$ot->titre,
					"objectModule"	=>	$ot->Module,
					"objectDescription"	=>	$ot->Description,
					"field"			=> 	$A->getField("child")
				);
				//Ajout des attributs custom
				if (is_array($A->attributes))foreach ($A->attributes as $kat=>$at)
					if (!in_array($kat,Array("data","card")))$o[$k][$kat] = $at;
				$k++;
			}
		return $o;
	}
	/**
	 * __________________________________________________________________________________________________
	 * 																						 ASSOCIATIONS
	 */

	/**
	* initAssociations
	* Initialisation des associations entre les objectclass.
	* @void
	*/
	public function initAssociations() {
		//FKEY
		if (isset($this->FKEY)&&sizeof($this->FKEY)){
			foreach ($this->FKEY as $FK){
				$FKAT = array_change_key_case($FK["@"],CASE_LOWER);
				//Verification de l'existence de l'objet
				$ModName = (isset($FKAT['module']) ? $FKAT['module'] : $this->Module);
				$Mod = Sys::getModule($ModName);
				if (!is_object($Mod)){
					//$this->fatalError("Un module dépendant est introuvable: ".$FKAT['module']." appelé depuis le module en initialisation ".$this->Module.".");
					continue;
				}else 
					$Mod->loadSchema();
				//récupération du module
				$M = Sys::getModule($this->Module);
				//Recuperation du nom de l'objectclass cible
				$Name = explode(",",$FKAT['data']);
				if (!is_object($Mod->Db->getObjectClass($Name[0]))) continue;
				$As = new Association($this->Module,$FK['#']);
				//Ajout de l'association dur le module
				$M->Db->addAssociation($As);
				//definition de la description
				if (isset($FKAT['description']))$As->setDescription($FKAT['description']);
				//configuration des attributs accessoires
				$As->initCustomAttributes($FK["@"]);
				//Configuration de l'association
				$As->addLink($this,"0,n");
				//Recherche du module
				$Obj = $Mod->Db->getObjectClass($Name[0]);
				$As->addLink($Obj,$FKAT['card'],$Name[1]);
				//Definition du proprietaire
				$As->setOwner(0);
				if ($As->isInterModule()){
					$M->loadSchema();
					$M->Db->addDependency($ModName);
					$M->Db->addObjectClass($Mod->Db->getObjectClass($Name[0]));
					$Mod->Db->addObjectClass($this);
				}
			}
			unset($this->FKEY);
		}
		
		//RKEY
		if (isset($this->RKEY)&&sizeof($this->RKEY)){
			foreach ($this->RKEY as $RK){
				$RKAT = array_change_key_case($RK["@"],CASE_LOWER);
				$As = new Association($this->Module,$RK['#']);
				//Recherche du module
				$ModName = (isset($RKAT['module']) ? $RKAT['module'] : $this->Module);
				$Mod = Sys::getModule($ModName);
				if (!is_object($Mod)){
					//throw new Exception("Depndance non staifaite module $ModName", 1);
					continue;
				}else $Mod->loadSchema();
				//récupération du module
				$M = Sys::getModule($this->Module);
				//Recuperation du nom de l'objectclass cible
				$Name = explode(",",$RKAT['data']);
				$As->addLink($Mod->Db->getObjectClass($Name[0]),"0,n",$Name[1]);
				//Ajout de l'association dur le module
				$M->Db->addAssociation($As);
				//definition de la description
				if (isset($RKAT['description']))$As->setDescription($RKAT['description']);
				//configuration des attributs accessoires
				$As->initCustomAttributes($RKAT);
				//Configuration de l'association
				$As->addLink($this,$RKAT['card']);
				//Definition du proprietaire
				$As->setOwner(1);
				if ($As->isInterModule()){
					$M->Db->addDependency($ModName);
					$M->Db->addObjectClass($Mod->Db->getObjectClass($Name[0]));
					//other side
					$Mod->Db->addObjectClass($this);
				}
			}
			unset($this->RKEY);
		}
	}
	/**
	* addAssociation
	* Add an association in the association array
	* @void
	*/
	public function addAssociation($As){
		//Check if association already exists
		if (!in_array($As,$this->Associations)){
			$this->Associations[] = $As;
		}
	}
	/**
	 * getAssociation
	 * retourne l'association par son nom
	 * @param String nom de l'association
	 * @return Object
	 */
	public function getAssociation($name) {
		foreach ($this->Associations as $a) if ($a->titre==$name) return $a;
	}
	/**
	 * getChildAssociation
	 * retourne l'association enfant par son nom
	 * @param String nom de l'association
	 * @return Object
	 */
	public function getChildAssociation($name,$to="") {
		//en prioirite les associations non reflexives
		$from=$this->titre;
		//echo "GET CHILD ASSOCIATION $name | $from => $to\r\n";
		foreach ($this->Associations as $a){
			if ($a->titre==$name){
				if ($a->isParent($from)&&!$a->isRecursiv())
					if (!empty($to)&&$a->isChild($to))
						return $a;
					elseif (empty($to))
						return $a;
			} 
		} 
		//ensuite les associations reflexives
		foreach ($this->Associations as $a){
			if ($a->titre==$name){
				if ($a->isParent($from)&&$a->isRecursiv())
					if (!empty($to)&&$a->isChild($to))
						return $a;
					elseif (empty($to))
						return $a;
			} 
		} 
		return false;
	}
	/**
	 * getParentAssociation
	 * retourne l'association parente par son nom
	 * @param String nom de l'association
	 * @return Object
	 */
	public function getParentAssociation($name,$to="") {
		//en prioirite les associations non reflexives
		$from=$this->titre;
		//echo "GET PARENT ASSOCIATION $name | $from => $to\r\n";
		foreach ($this->Associations as $a){
			if ($a->titre==$name){
				if ($a->isChild($from)&&!$a->isRecursiv())
					if (!empty($to)&&$a->isParent($to))
						return $a;
					elseif (empty($to))
						return $a;
			} 
		} 
		//ensuite les associations reflexives
		foreach ($this->Associations as $a){
			if ($a->titre==$name){
				if ($a->isChild($from)&&$a->isRecursiv())
					if (!empty($to)&&$a->isParent($to))
						return $a;
					elseif (empty($to))
						return $a;
			} 
		} 
		return false;
	}
	/**
	 * getParentAssociation
	 * retourne toutes les associations parentes
	 * @return Object
	 */
	public function getParentAssociations($to="") {
		$from=$this->titre;
		$out=Array();
		//echo "GET PARENT ASSOCIATION $name | $from => $to\r\n";
		foreach ($this->Associations as $a){
			if ($a->isChild($from))
				if (!empty($to)&&$a->isParent($to))
					array_push($out,$a);
				elseif (empty($to))
					array_push($out,$a);
		} 
		return $out;
	}
	/**
	 * getChildAssociation
	 * retourne toutes les associations enfantes
	 * @return Object
	 */
	public function getChildAssociations($to="") {
		$from=$this->titre;
		$out=Array();
		//echo "GET CHILD ASSOCIATION $from => $to\r\n";
		foreach ($this->Associations as $a){
			if ($a->isParent($from))
				if (!empty($to)&&$a->isChild($to))
					array_push($out,$a);
				elseif (empty($to))
					array_push($out,$a);
		} 
		return $out;
	}
		/**
	 * __________________________________________________________________________________________________
	 * 																						   REFERENCES
	 */
	function initFromReference($Tab,$Nom){
		//Initialisation des attributs de l objectClass
		$this->titre = $Nom;
		$this->driver = $Tab['driver'].'Driver';
		$this->AccessPoint = 0;
		$this->Master = 0;
		$this->Heritage = 0;
		$this->Description = $Tab['description'];
		$this->Icon = $Tab['Icon'];

		//INTIALISATION DES PROPRIETES
		$this->Proprietes['Reference'.$Tab["Module"]] = $Tab;
		$this->Proprietes['Reference'.$Tab["Module"]]['type']= 'ObjectClass';
		$this->Proprietes['Reference'.$Tab["Module"]]['length']= '255';
		$this->Proprietes['Reference'.$Tab["Module"]]['titre']= 'Reference'.$Tab["Module"];
		$this->Proprietes['Reference'.$Tab["Module"]]['special']= 'Reference';
		$this->Proprietes['Reference'.$Tab["Module"]]['searchOrder']= 1;
		//INITIALISATION DES ASSOCIATIONS
		$t=explode(",",$Tab["data"]);
		$this->Etrangeres[$this->titre]['card']= '1,1';
		$this->Etrangeres[$this->titre]['data']= $Tab["data"];
		$this->Etrangeres[$this->titre]['Target']= $t[1];
		$this->Etrangeres[$this->titre]['Table']= $t[0];
		$this->Etrangeres[$this->titre]['Champ']= $t[1];
		$this->Etrangeres[$this->titre]['behaviour']= "Integrated";
		//INITIALISATION DU PILOTE
		$this->createTargets();

		//GESTION DES TYPES
/*		$InvProp = array_keys($this->Proprietes);
		for ($i=0;$i<count($this->Proprietes);$i++){
			$this->Proprietes[$InvProp[$i]] =  $this->initSpecialTypes($this->Proprietes[$InvProp[$i]]);
		}*/
	}
	/**
	 * __________________________________________________________________________________________________
	 * 																						   PROPERTIES
	 */
	/**
	 * getProperty
	 * retourne une propriete pour un nom donné
	 */
	public function getProperty($name){
		return (isset($this->Proprietes[$name]))?$this->Proprietes[$name]:false;
	}
	/**
	 * __________________________________________________________________________________________________
	 * 																						        VIEWS
	 */
	/**
	 * initViews
	 * Initilisation des vues
	 */
	public function initViews() {
		foreach ($this->Views as $V) $V->init();
	}
	/**
	 * getView
	 * Recuperation d'une vue par son titre 
	 */
	 public function getView($titre) {
	 	if (is_array($this->Views))foreach ($this->Views as $V){
	 		if ($V->titre==$titre)return $V;
	 	}
	 }
	/**
	 * __________________________________________________________________________________________________
	 * 																						        UTILS
	 */
	/**
	 * checkNew
	 * Fonction de verification d'intégrite de la base
	 * @void
	 */
	function checkNew($Drv="",$Data=""){
		if (empty($Drv)) $Drv = $this->Conf["CHECKFROM"];
		$Drv = $Drv."Driver";
		return $this->checkNew($Data);
	}

	function parseAttributes($tab,$Nom='') {
		if (!isset($tab))return;
		//ParseAttributes permet de transformer le schema en attributs de la classe
		//On recherche les attributs...
		$v=0;
		$nbAttr = sizeof($tab);
		$tabTemp = array_keys($tab);
		$Result=Array();
		//Et ensuite on fait un switch qui les charge dans le tableau
		for ($i=0;$i<$nbAttr;$i++) {
			if(!isset($tab['card'])||$tab['card']!='0,n' || $tab['card']!='1,n') {
				switch ($tabTemp[$i]) {
					case 'values':	//Valeurs possible
						$temp = explode(",",$tab[$tabTemp[$i]]);
						foreach ($temp as $t) {
							if (strpos($t,'::')) {
								$s = explode('::', $t);
								$Result["Values"][$s[0]] = $s[1];
							}else
								$Result["Values"][$t] = $t;
						}
						break;
					case 'type':		//Type l information
					case 'query':		//Execution requete
					case 'default':		//Valeurs par defaut
					case 'color':		//Type de couleur en hexadecimal
					case 'param':		//Parametre quand type = ObjectClass
					case 'card':		//FKEY : Cardinalite
					case 'data':		//FKEY : Nom de la liaison
					case 'description':	//Description
					case 'length':		//Longueur
					case 'null':		//definie la possibilite d'une valeur nulle
					case 'searchOrder':	//Ordre de recherche
					case 'inherit':		//Definie un heritage
					case 'behaviour':	//Definie un comportement
					case 'action':		//Definie un comportement
					case 'obligatoire':	//Champ obligatoire
					case 'unique':		//Valeur unique
					case 'target':		//Dictionnaire automatique ??
						$Result[$tabTemp[$i]]=$tab[$tabTemp[$i]];
					break;
					default:
						$Process = new Process();
						$Result[$tabTemp[$i]]=$Process->processingVars($tab[$tabTemp[$i]]);
					break;
				}
			}
		}
		return $Result;
	}

	function saveGroupData($gId){
		return $this->saveGroupData($gId);
	}

	function isProperties($Property) {
		//Verifie si Property est une propriete
		if (array_key_exists($Property,$this->Proprietes)) return true;
		if (array_key_exists($Property,ObjectConst::NeededPHP())) return true;
		return false;
	}


	function isFKey($Fkey) {
		//Verifie si Fkey est une clef etrangere
		$Test = in_array($this->Etrangeres[$Fkey]['card'],$this->isShortKey);
		return (array_key_exists($Fkey,$this->Etrangeres) && $Test) ? true:false;
	}

	/* -----------------------------------------------------------------
	 |       Verification et modification des donnees structure       |
	 -----------------------------------------------------------------*/

	function countDb(){
		return $this->countDb();
	}


	function idPossess($id){
		//Dummy qui permet de verifier si l'user a bien l'id specifie
		return $this->idPossess($id);
	}

	function Verify(){
		return sqlCheck::Verify($this);
	}


	function Check() {
		//Verifie et modifie les donnes qui differe entre l'objet et la table SQL
		if (!$this->Verify()) $GLOBALS["Systeme"]->Error->sendErrorMsg(60,1);
		$this->initData();
	}
	function getSchema(){
		//Renvoie le schema dans un tableau
		return Array('Properties'=>$this->Proprietes, 'FKeys'=>$this->Associations);
	}
	/**
	* findReflexive
	* Retreive the reflexive field
	* @return String Name of recursiv key
	*/
	function findReflexive(){
		foreach($this->Associations as $A){
			if ($A->isRecursiv()) return $A->titre;
		}
	}

	function findReflexiveCard(){
		//On recherche la clef de parente
		foreach ($this->childOf as $Key){
			if ($Key["Objet"]==$this->titre) return $Key["card"];
		}
		return false;
	}
	function createTargets() {
		//Creer le tableau des cibles (priorite de recherches)
		$this->Cibles['Id'] = 0;
		if (isset($this->Proprietes['Url']))$this->Cibles['Url'] = 0;
		//SearchOrder
		$this->SearchOrder['Id'] = 0;
		if (isset($this->Proprietes['Url']))$this->SearchOrder['Url'] = 0;
		// On ge un tableau avec la propriete et son indice de priorite.
		foreach ($this->Proprietes as $Clef=>$Propriete){
			if (isset($Propriete['searchOrder'])) {
				$this->SearchOrder[$Clef]= $Propriete['searchOrder'];
			}
			if (isset($Propriete['unique'])&&$Propriete['unique']=="1") {
//			if (isset($Propriete['searchOrder'])&&$Propriete['searchOrder']>=1) {
				$this->Cibles[$Clef]= sizeof($this->Cibles);
			}
		}
		//On range le tableau selon ses clefs
		asort($this->SearchOrder);
		return true;
	}

	/**
	 * getGlobalType
	 * return the global type of a kobeye type
	 * @param String kob-eye type
	 * @return String global type
	 */
	 function getGlobalType($Type){
		switch (strtolower($Type)){
			case "pourcent":
			case "price":
			case "float":
				$Type="float";
			break;
			case "autodico":
			case "order":
			case "date":
			case "datetime":
			case "duration":
			case "modele":
			case "int":
				$Type="integer";
				break;
			case "txt":
			case "raw":
			case "text":
			case "bbcode":
			case "html":
			case "bin":
				$Type="text";
			break;
			case "longtext":
				$Type="longtext";
			break;
			case "private":
			case "image":
			case "file":
			case "url":
			case "mail":
			case "titre":
			case "canonic":
			case "varchar":
            case "link":
            case "extlink":
				$Type="string";
				break;
			case "boolean":
				break;
		}
		return ($Type) ? $Type:false;
	 }

	/**
	 * getPropType
	 * return the global type of a property
	 * @param String Name of the property
	 * @return String;
	 */
	function getPropType($Nom){
		//Renvoie le type d-une propriete
		$Type="";
		if (array_key_exists($Nom,$this->Proprietes)) $Type = $this->Proprietes[$Nom]["type"];
		elseif ($this->isKey($Nom)) $Type = "integer";
		$Needed = ObjectConst::NeededPHP();
		if (array_key_exists($Nom,$Needed)) $Type =$Needed[$Nom];
		return $this->getGlobalType($Type);
	}

	function getSpecialProp($Name){
		//Renvoie les proprietes d un certain type
		$Tab = $this->Proprietes;
		foreach ($Tab as $Nom=>$Value) {
			if ((isset($Value["special"])&&$Value["special"]==$Name)||(isset($Value["type"])&&$Value["type"]==$Name)) {
				$Result[$Nom] = $Value;
			}
		}
		return (isset($Result)&&is_array($Result)) ? $Result:false;
	}

	/* -------------------------------------
	 |       Fonctions de recherche       |
	 -----------------------------------  */
	/**
	* isChildOf
	* Retourne vrai si l'objet est bien un enfant
	*/
	function isChildOf($Obj){
		foreach ($this->Associations as $A){
            //echo $this->titre." isChildOf ".$Obj." A->isparent ".$A->isParent($Obj)." A->ischild ".$A->isChild($this->titre)."\r\n";
            if ($A->isParent($Obj)&&$A->isChild($this->titre)) return true;
        }
        return false;
	}
	/**
	* isParentOf
	* Retourne vrai si l'objet est bien un enfant
	*/
	function isParentOf($Obj){
		foreach ($this->Associations as $A) {
            if ($A->isChild($Obj) && $A->isParent($this->titre)) return true;
        }
		return false;
	}
	/**
	* getCard
	* Retourne la cardinalite d'une association
	* @param String Nom de l'objectclass
	* @return String Cardinalité
	*/
	public function getCard($Class) {
		foreach ($this->getParent() as $A) {
			if ($A->isParent($Class)) return $A->getCard('parent');
		}
		foreach ($this->getChild() as $A) {
			if ($A->isChild($Class)) return $A->getCard('child');
		}
	}
	/**
	* getParent
	* Renvoie la liste des liaisons parentes
	* @return Array of Association Object
	*/
	function getParent() {
		$Out= Array();
		foreach ($this->Associations as $A)
			if ($A->isChild($this->titre)) $Out[] = $A;
		return $Out;
	}
	/**
	* getChild
	* Renvoie la liste des liaisons enfantes
	* @return Array of Association Object
	*/
	public function getChild() {
		$Out= Array();
		foreach ($this->Associations as $A){
			if ($A->isParent($this->titre)) $Out[] = $A;
		}
		return $Out;
	}
	/**
	* getChildObjectClass
	* Renvoie un objectclass enfant de cet objet
	* @param String Objectclass Name
	* @return ObjectClass
	*/
	public function getChildObjectClass($N) {
		foreach ($this->Associations as $A)
			if ($A->isParent($this->titre)&&$A->isChild($N)) return $A->getChildObjectClass();
		return false;
	}
	/**
	* getParentObjectClass
	* Renvoie un objectclass parent de cet objet
	* @param String Objectclass Name
	* @return ObjectClass
	*/
	public function getParentObjectClass($N) {
		foreach ($this->Associations as $A){
			if ($A->isChild($this->titre)&&$A->isParent($N)){
				return $A->getParentObjectClass();
			}
		}
		return false;
	}
	/**
	* getLinkedObjectClass
	* Renvoie un objectclass lié de cet objet
	* @param String Objectclass Name
	* @return ObjectClass
	*/
	public function getLinkedObjectClass($N,$Module="") {
		foreach ($this->Associations as $A){
			if (!empty($Module)){
				if ($A->isParent($this->titre)&&$A->isChild($N)&&$A->isLinkedWithModule($Module)) return $A->getChildObjectClass();
				elseif ($A->isChild($this->titre)&&$A->isParent($N)&&$A->isLinkedWithModule($Module)) return $A->getParentObjectClass();
			}else{
				if ($A->isParent($this->titre)&&$A->isChild($N)) return $A->getChildObjectClass();
				elseif ($A->isChild($this->titre)&&$A->isParent($N)) return $A->getParentObjectClass();
			}
		}
		return false;
	}
	/**
	* isKey
	* Verifie si le nom donnée est le nom d'une association de type courte
	* @param String Association Name
	* @return Boolean
	*/
	public function isKey($AName){
		foreach ($this->Associations as $A){
			if ($A->isShort()&&$A->titre==$AName)return true;
		}
		return false;
	}
	/**
	* findKey
	* Renvoie le nom du champ tendant vers $DataName dans la table courante.
	* On verifie si on a le nom d'un objectclass, si oui on renvoie le nom de champ
	* @param String ObjectClass Name
	* @param String Type of link
	* @return String Name of the link
	*/
	public function findKey($DataName,$Type='child',$Module=null){
		if (!$Module) $Module = $this->Module;
//  		echo "FIND KEY ".$this->titre." | $DataName | $Type \r\n";
		foreach ($this->Associations as $A){
			if ($Type=='child'){
				//avec le nom de l'association
				if ($A->hasModule($Module)&&$A->titre==$DataName&&$A->isParent($this->titre)) {
					return $A->titre;
				}
				//avec le nom de l'objectclass
				if ($A->hasModule($Module)&&$A->isChild($DataName)&&$A->isParent($this->titre)) {
					return $A->titre;
				}
			}else{
				//avec le nom de l'association
				//echo "find Key ".$A->Module."==$Module&&".$A->titre."==$DataName&&".$A->isChild($this->titre)." \r\n";
				if ($A->hasModule($Module)&&$A->titre==$DataName&&$A->isChild($this->titre)) {
					return $A->titre;
				}
				//echo "find Key ".$A->Module."==$Module&&".$A->isParent($DataName)."&&".$A->isChild($this->titre)." \r\n";
				//avec le nom de l'objectclass
				if ($A->hasModule($Module)&&$A->isParent($DataName)&&$A->isChild($this->titre)) {
					return $A->titre;
				}
			}
		}
		return false;
	}
	/**
	* isreflexive
	* Check if an objectclass is reflexive
	* @param String Name of objectclass
	* @return Boolean 
	*/
	public function isReflexive($P=""){
		if (!empty($P))	$Object = Sys::$Modules[$this->Module]->Db->getObjectClass($P);
		else $Object = $this;
		if ($Object->noRecursivity) return false;
		foreach($Object->Associations as $A)
			if ($R = $A->isRecursiv()) return $R;
		return false;
	}
	/**
	* getKey
	* Return the Association object identified by the link name and objectClass Name
	* @param String ObjectClass name
	* @param String Association Name
	* @param String child or parent link type
	* @return Object
	*/
	public function getKey($ObjectClass="",$Champ="",$Type="child"){
		foreach($this->Associations as $A){
			if ($Type=="child"){
				if ($A->titre==$Champ && $A->isChild($ObjectClass)){
//                    echo "	-> ".$A->titre."\r\n";
					return $A;
				}
			}else
				 if ($A->titre==$Champ && $A->isParent($ObjectClass)){
// 					echo "	-> ".$A->titre."\r\n";
					return $A;
				}
		}
		return false;
	}
	/**
	* getKeyInfo
	* Return informations about links
	* @param String Datasource object
	* @param String Value or research
	* @param Boolean Optional define the sens of search
	* @return Array of informations.
	*/
	function getKeyInfo($DataSource,$Value,$parent=false,$Key="",$View=""){
// 		echo "*****************************\r\n";
// 		echo "* O:$this->titre D:$DataSource V:$Value P:$parent\r\n";
// 		echo "*****************************\r\n";
		//Construction du tableau d analyse
		if (empty($Key)){
			$Ke = $this->findKey($DataSource,"child");
			if (!$Ke) return;
			$K = $this->getKey($DataSource,$Ke,"child");
		}else{
			$K = $this->getChildAssociation($Key,$DataSource);
		}
		if (!is_object($K)) return;
		$TabAnalyse["Recherche"] = $Value;
		$TabAnalyse[($parent)?"Parent":"Nom"] = $this->titre;
		$TabAnalyse["Card"] = $K->getCard('child');
		$TabAnalyse["Champ"] = $K->getField(($parent)?"parent":"child");
		$TabAnalyse["Driver"] = $K->getDriver();
		$TabAnalyse["Table"] = $K->getTable();
		$TabAnalyse["Target"] = $K->getTarget();
		$TabAnalyse["NomEnfant"] = $K->Links[0]["ObjectClass"]->titre;
		$TabAnalyse["Out"] =($parent)?1:0;
		$TabAnalyse["View"] = $View;
		if ($K->isRecursiv()) $TabAnalyse["Reflexive"] = true;
		$TabAnalyse["Module"] = $this->Module;
		$TabAnalyse["ModuleAssoc"] = $K->Module;
		if ($parent){
			$TabAnalyse["Module"] = $K->Module;
			$O=$K->getChildObjectClass();
			$TabAnalyse["ModuleParent"] = $this->Module;
		}
		return $TabAnalyse;
	}

	function Search($Etape,$Value,$parent=false,$Select="",$GroupBy="",$Analyse=Array(),$Key="",$View="",$Otype="",$Ovar="") {
		$Result=false;
		if ($parent) {
			//Recherche objectClass
			$Ob = $this->getChildObjectClass($Etape["NomEnfant"]);
			if (empty($Key)){
				$Ke = $this->findKey($Ob->titre,"child");
				if (!$Ke) return;
				$K = $this->getKey($Ob->titre,$Ke,"child");
			}else{
				$K = $this->getChildAssociation($Key,$Etape["NomEnfant"]);
			}
			if (!is_object($K))return;
 /*			$Ke = $this->findKey($Ob->titre,"child");
			//if (!$Ke) return false;
			$K = $this->getKey($Ob->titre,$Ke,"child");*/
			//RECHERCHE PARENT SIMPLE
			$Analyse[] = $Etape;
			//Filtre
			$TabAnalyse["Recherche"] = $Value;
			$TabAnalyse["Nom"] =$Etape["NomEnfant"];
			$TabAnalyse["Driver"] =$this->driver;
			$TabAnalyse["Module"] = $Ob->Module;
			$Analyse[] = $TabAnalyse;
			//Existe t il un filtre ?
			if (isset($filtre)&&is_array($filtre)){
				$Analyse[] = array("Recherche"=>$filtre["Value"]);
			}
			//Analyse terminée donc on envoi maintenant la requete au pilote.
			$Results = $this->DriverSearch($Analyse,$Select,$GroupBy);
		}else{
			//if ($this->driver=="mysqlDriver")$Analyse=Array($Key);
			$TabAnalyse["Recherche"] = $Value;
			$TabAnalyse["Nom"] = $this->titre;
			$TabAnalyse["Driver"] = $this->driver;
			$TabAnalyse["Out"] = true;
			$TabAnalyse["Reflexive"] = $this->isReflexive();
			$TabAnalyse["Module"] = $this->Module;
			if (is_array($Etape)&&!sizeof($Analyse))$Analyse[] = $Etape;
			$Analyse[] = $TabAnalyse;
			//Analyse terminée donc on envoi maintenant la requete au pilote.
            if (!empty($View)&&is_object($this->getView($View))&&$View!="NOVIEW"){
				$viewObject = $this->getView($View);
				$Results = $viewObject->Search($Analyse,$Value,$Select,$GroupBy,$Otype,$Ovar);
			}elseif (DbAnalyzer::$QueryType!="Direct"&&$this->defaultView&&$View!="NOVIEW"){
				$Results = $this->defaultView->Search($Analyse,$Value,$Select,$GroupBy,$Otype,$Ovar);
			}else
				$Results = $this->DriverSearch($Analyse,$Select,$GroupBy);

        }
		if (AUTO_COMPLETE_LANG&&$GLOBALS["Systeme"]->LangageDefaut!=$GLOBALS["Systeme"]->DefaultLanguage&&!Sys::$User->Admin){
			foreach ($GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE") as $Cod=>$Lang) {
				if (isset($Lang["DEFAULT"])) $DefautPref = $Cod;
			}
			if (is_array($Result)) for( $i=0; $i<sizeof($Result);$i++){
				foreach ($this->Proprietes as $K=>$Prop){
					//Priorites de langage
					$Special = $this->Proprietes[$K]["special"];
					if ($Special=="multi"&&$Result[$i][$K]==""){
						$Result[$i][$K] = $Result[$i][$DefautPref."-".$K];
						//print_r($Result[$i]);
					//	echo "CHANGEMENT $Key => ".$Result[$i][$DefautPref."-".$Key]."\r\n";
					}
				}
			}
		}
//       		echo "--------------------\r\n";
//       		print_r($Analyse);
		return $Results;
	}

	/**
	 * partialSearch
	 * get id from association table in case of different drivers
	 */
	function partialSearch($Etape,$Value,$parent=false,$Select="",$GroupBy="",$Analyse=Array(),$Key="",$View="") {
		$Result=false;
		if ($parent) {
			//Recherche objectClass
			$Ob = $this->getChildObjectClass($Etape["NomEnfant"]);

			//Determination de la clef si non fournie
			if (empty($Key)){
				$Ke = $this->findKey($Ob->titre,"child");
				if (!$Ke) return;
				$K = $this->getKey($Ob->titre,$Ke,"child");
			}else{
				$K = $this->getChildAssociation($Key,$Etape["NomEnfant"]);
			}
			if (!is_object($K))return;
			
			//Modification de la selection
			$Select = $K->getField("child");
			
			if ($K->isLong()){
				//Clef longue
				
				//Modification de la paire pricipale car nous ne pouvons jointer avec des pilotes differents
				$Etape["Champ"] = "Id";
				$Etape["Target"] = $K->getField("parent");
				$Etape["Parent"] = $Etape["Table"];
				$Etape["Card"] = "1,1";
				$Etape["ModuleParent"] = $K->Module;
				$Analyse[] = $Etape;
	
				//Filtre
				$TabAnalyse["Recherche"] = $Value;
				$TabAnalyse["Nom"] =$Etape["NomEnfant"];
				$TabAnalyse["Driver"] =$this->driver;
				$TabAnalyse["Module"] = $Ob->Module;
				$Analyse[] = $TabAnalyse;
			}else{
				//Clef courte
				
				//Filtre
				$TabAnalyse["Recherche"] = $Value;
				$TabAnalyse["Nom"] =$Etape["NomEnfant"];
				$TabAnalyse["Driver"] =$Ob->driver;
				$TabAnalyse["Module"] = $Ob->Module;
				$TabAnalyse["Out"] = 1;
				$Analyse[] = $TabAnalyse;
				
			}

			//Existe t il un filtre ?
			if (isset($filtre)&&is_array($filtre)){
				$Analyse[] = array("Recherche"=>$filtre["Value"]);
			}

			//Analyse terminée donc on envoi maintenant la requete au pilote.de l'enfant (car pilote différent)
			$Results = $Ob->DriverSearch($Analyse,$Select,$GroupBy);
			$o = Array();
			foreach ($Results as $r)
				if ($r[$Select]>0)$o[] = $r[$Select]; 
			
			return $o;
			
		}else{
			//TODO partialsearch child diffrent drivers
			//Recherche objectClass
			$Ob = $this->getParentObjectClass($Etape["Nom"]);

			//Determination de la clef si non fournie
			if (empty($Key)){
				$Ke = $this->findKey($Ob->titre,"parent");
				if (!$Ke) return;
				$K = $this->getKey($Ob->titre,$Ke,"parent");
			}else{
				$K = $this->getParentAssociation($Key,$Etape["NomEnfant"]);
			}
			if (!is_object($K))return;
			
			//Modification de la selection
			//$Select = $K->getField("parent");
			
			if ($K->isLong()){
				//Clef longue
				
				//Modification de la paire pricipale car nous ne pouvons jointer avec des pilotes differents
				$Etape["Recherche"] = $K->getField("child").'='.$Etape["Recherche"];
				$Etape["Target"] =$K->getField("parent");
				$Etape["Champ"] = "Id";
				$Etape["Nom"] = $Etape["Table"];
				$Etape["Card"] = "1,1";
				$Etape["Module"] = $this->Module;
				$Analyse[] = $Etape;
	
				//Filtre
				$TabAnalyse["Recherche"] = $Value;
				$TabAnalyse["Nom"] =$Etape["NomEnfant"];
				$TabAnalyse["Driver"] =$this->driver;
				$TabAnalyse["Out"] = 1;
				$TabAnalyse["Module"] = $this->Module;
				$Analyse[] = $TabAnalyse;
			}else{
				//Clef courte
				
				//Filtre
				$TabAnalyse["Recherche"] = $K->getField("child").'='.$Etape["Recherche"];
				$TabAnalyse["Nom"] =$Etape["NomEnfant"];
				$TabAnalyse["Driver"] =$Etape["Driver"]; 
				$TabAnalyse["Module"] = $Etape["ModuleAssoc"];
				$TabAnalyse["Out"] = 1;
				$Analyse[] = $TabAnalyse;
				
			}
			//Analyse terminée donc on envoi maintenant la requete au pilote.
			if (!empty($View)&&is_object($this->getView($View))&&$View!="NOVIEW"){
				$viewObject = $this->getView($View);
				$Results = $viewObject->Search($Analyse,$Value,$Select,$GroupBy);
			}elseif (DbAnalyzer::$QueryType!="Direct"&&$this->defaultView&&$View!="NOVIEW"){
				$Results = $this->defaultView->Search($Analyse,$Value,$Select,$GroupBy);
			}else
				$Results = $this->DriverSearch($Analyse,$Select,$GroupBy);
			return $Results;
		}
	}


	/* -------------------------------------------
	 |       Fonctions de calcul de notes       |
	 -------------------------------------------  */


	function calcNote($valeurEnr,$Recherche,$valeurCible){
		//Cette fonction permet de calculer la note qui classera les resultats
		$totalCibles = sizeof($this->Cibles);
		$note = (10/$totalCibles)*($totalCibles-($valeurCible/$totalCibles));
		//On obtient, appart pour Id, des notes inferieures a 10, auquel on va retirer la pertinence du resultat
		if ($Recherche != $valeurEnr){
			//Au cas ou la recherche soit au debut, a la fin ou au milieu
			$note=$this->attribNote($note,'!^'.$Recherche.'!i',$valeurEnr,2,$Recherche);
			$note=$this->attribNote($note,'!'.$Recherche.'$!i',$valeurEnr,3,$Recherche);
			$note=$this->attribNote($note,'!'.$Recherche.'!i',$valeurEnr,5,$Recherche);
		}
		return $note;
	}


	function attribNote($note,$eReg,$Chaine,$Facteur,$Recherche){
		//Cette fonction fait les calculs d'amputations de points pour calcNote
		if (preg_match($eReg, $Chaine)){
			$note = $note - ($Facteur/count($this->Cibles));
			$note = $note*(strlen($Recherche)/strlen($Chaine));
		}
		return $note;
	}

	//-------------------------------------------//
	// FONCTIONS UTILITAIRES
	//-------------------------------------------//



	function autoLink($Field,$Obj,$Prefixe="",$keep=false) {
		//Analyse des searchOrder pour Detecter de la chaine a encoder
		if ($Prefixe!="")$Prefixe.="-";

		if($keep && $this->getPropType($Field)=="string"){
            $Prop=$Field;
		}else{
            $Ok = false;
            foreach ($this->SearchOrder as $K=>$C) {
                if ($C>0&&!$Ok){
                    if ($this->getPropType($K)=="string") {
                        $Prop=$K;
                        $Ok=true;
                    }
                }
            }
		}

		if ($Prefixe!=""){
			$chaine = $Obj[$Prefixe.$Prop];
			if ($chaine == ""){
				$chaine = $Obj[$Prop];
			}
		}else $chaine = $Obj[$Prop];
		if (empty($chaine)) $chaine=$Obj['Id'];
		$chaine = str_replace("°", "-", $chaine);
		$chaine=utf8_decode($chaine);
		$chaine=stripslashes($chaine);
		$chaine = preg_replace('`\s+`', '-', trim($chaine));
		$chaine = str_replace("'", "-", $chaine);
		$chaine = str_replace("&", "et", $chaine);
		$chaine = str_replace('"', "-", $chaine);
		$chaine = str_replace("?", "", $chaine);
		$chaine = str_replace("+", "-", $chaine);
		$chaine = str_replace("=", "-", $chaine);
		$chaine = str_replace("!", "", $chaine);
		$chaine = str_replace(".", "", $chaine);
		$chaine = str_replace("%", "", $chaine);
		$chaine = str_replace("²", "", $chaine);
		$chaine = preg_replace('`[\,\ \(\)\+\'\/\:]`', '-', trim($chaine));
		$chaine=strtr($chaine,utf8_decode("ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ?>#<+;,²³°"),"aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn-------23o");
		$chaine = preg_replace('`[-]+`', '-', trim($chaine));
		$chaine =  utf8_encode($chaine);
		//ON verifie qu il n existe pas deja une entité avec la meme url
		$Suffixe=(isset($Obj["Id"]))?"&Id!=".$Obj["Id"]:"";
		$modif = false;
		$chainet = preg_replace('`[\/]`', '-', trim($chaine));
		$recursiv = $this->isReflexive() && !$this->noRecursivity ? '*/':'';
		$Result =  Sys::$Modules[$this->Module]->callData($this->titre."/".$recursiv.$Field."=".$chainet.$Suffixe,"","","","","","COUNT(*)");
		$int=0;
		if (is_array($Result)&&isset($Result[0])&&
		$Result[0]["COUNT(*)"]>0)while (isset($Result[0])&&$Result[0]["COUNT(*)"]>0){
			$int++;
			$Result =  Sys::$Modules[$this->Module]->callData($this->titre."/".$recursiv.$Field."=".$int.$chainet.$Suffixe,"","","","","","COUNT(*)");
			$modif = true;
			//             echo $this->titre."/".$Field."=$int".$chaine.$Suffixe." => ".$Result[0]["COUNT(*)"]."\r\n";
		}
		if ($modif)$chaine=$int.$chaine;
        if(''.intval($chaine) == $chaine) $chaine = 'u-'.$chaine;
		return $chaine;
	}
	function autoCanon($Obj,$Prefixe="") {
		//Analyse des searchOrder pour Detecter de la chaine a encoder
		if ($Prefixe)$Prefixe.="-";
		foreach ($this->Cibles as $K=>$C) {
			if ($C>0&&$K!="Url"){
				if ($this->getPropType($K)=="string"&&!$t) {
					$Prop=$K;$t=1;
				}
			}
		}
		$chaine = $Obj[$Prefixe.$Prop];
		//ON verifie qu il n existe pas deja une entité avec la meme url
		$chaine=utf8_decode($chaine);
        $chaine=strtr($chaine,utf8_decode("ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ?>#<+;,²³°"),"aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn-------23o");
		if (strlen($chaine)>3) $chaine = preg_replace("#(e|s|es|ent)$#","",$chaine);
		return utf8_encode($chaine);
	}
	function changeRights($Id,$Ui="",$Gi="",$Um="",$Om="",$Gm=""){
// 		return $this->changeRights($Id,$Ui,$Gi,$Um,$Om,$Gm);
	}
	/*----------------------------------
	 |     Fonctions d'insertion       |
	 ----------------------------------*/

	function AddNeededFields($Obj='',$type){
		//Rajoute les champs necessaires a une insertion
		$LesDroits= Array("uid","gid","umod","gmod","omod");
		$Tableau = Array();
		if ($type == "OBJECT"&&isset($Obj["Id"])) $Tableau['Id']=$Obj["Id"];
		//On met a jour les tms et User
		$TimeStamp = time();
		$CurrentUser = (is_object(Sys::$User)&&isset(Sys::$User->Id))?Sys::$User->Id:0;
		$CurrentGroup = (is_object(Sys::$User)&&isset(Sys::$User->Groups[0]))?Sys::$User->Groups[0]->Id:0;
		if (!isset($Obj["Id"])){
			if ($type!="ASSOC") {
				$Tableau['uid'] = $CurrentUser;
				$Tableau['gid'] = $CurrentGroup;
			}
			$Tableau['tmsCreate']=$TimeStamp;
			$Tableau['userCreate']=$CurrentUser;
		}else{
			if ($type!="ASSOC") {
				$Tableau['uid'] = (isset($Obj['uid']))?$Obj['uid']:$CurrentUser;
				$Tableau['gid'] = (isset($Obj['gid']))?$Obj['gid']:$CurrentGroup;
			}
			$Tableau['tmsCreate']=(isset($Obj['tmsCreate']))?$Obj['tmsCreate']:$TimeStamp;
			$Tableau['userCreate']=(isset($Obj['userCreate']))?$Obj['userCreate']:$CurrentUser;
		}
		for ($i=0;$i<sizeof($LesDroits);$i++){
			if (!empty($Obj[$LesDroits[$i]])) $Tableau[$LesDroits[$i]] = $Obj[$LesDroits[$i]];
		}
		$Tableau['tmsEdit'] = $TimeStamp;
		$Tableau['userEdit'] = $CurrentUser;
		return $Tableau;
	}


	function Insert($Object){
		//Insere/met a jour un objet et ses clefs etrangeres
		$Obj = get_object_vars($Object);
		$OrdreProp = $this->AddNeededFields($Obj,"OBJECT");
		$Error = false;
		//Traitement multilingue
		$Prefixes = array();
		$DefaultLanguage=$GLOBALS["Systeme"]->DefaultLanguage;
		foreach ($GLOBALS["Systeme"]->Language as $Lang=>$Pref){
			if ($DefaultLanguage!=$Lang){
				$Prefixes[] = $Pref;
			}
		}
		foreach($this->Proprietes as $Key=>$Prop){
			//Si le champ est obligatoire et que l'insertion est vide, erreur
		  	if (isset($Prop["obligatoire"])&&$Prop["obligatoire"] && !($Prop['type']=="boolean" && $Obj[$Key]==0) && empty($Obj[$Key])){
				//KError::fatalError("$Key property is required in ".$this->Module."/".$this->titre);
				$Error = "$Key property is required in ".$this->Module."/".$this->titre;
			}
			if (isset($Prop["unique"])&&$Prop["unique"] && !empty($Obj[$Key])&&empty($Obj["Id"])){
				$Result =  Sys::$Modules[$this->Module]->callData($this->titre."/".$Key."=".$Obj[$Key]);
				if ($Result && $Result[0]["Id"]!=$Obj["Id"]){
					//$GLOBALS["Systeme"]->Error->sendUserMsg(16,$Key.' n est pas unique : '.$Obj[$Key]);
					$Error='La valeur du champs '.$Key.' n\'est pas unique : '.$Obj[$Key];
				}
			}
			if(isset($Prop["type"])&&$Prop["type"]=="random"&&empty($Obj[$Key])){ //&&( !isset($Obj["tmsEdit"])||$Obj["tmsEdit"]<(time()-(CONNECT_TIMEOUT*60)) || $Obj["CodeVerif"]=="")){
				$OrdreProp[$Key] = Utils::genererCode();
			}else{
				if (empty($Prop["Ref"])&&isset($Obj[$Key])) $OrdreProp[$Key]=$Obj[$Key];
			}
			if ((isset($Prop["content"])&&$Prop["content"]=="link")||(isset($Prop["type"])&&$Prop["type"]=="link")&&empty($OrdreProp[$Key])) {
				$OrdreProp[$Key] = $this->autoLink($Key,$Obj);
				if (isset($Prop["special"])&&$Prop["special"]=="multi") {
					//Pr tous les langages, on verifie q la variable de la prop existe.
					foreach ($Prefixes as $Prefixe){
						$OrdreProp[$Prefixe."-".$Key] = $this->autoLink($Key,$Obj,$Prefixe);
					}
				}
			}
			if (isset($Prop["content"])&&$Prop["content"]=="canonic") {
				$OrdreProp[$Key] = $this->autoCanon($Obj);
				if ($Prop["special"]=="multi") {
					//Pr tous les langages, on verifie q la variable de la prop existe.
					foreach ($Prefixes as $Prefixe){
						$OrdreProp[$Prefixe."-".$Key] = $this->autoCanon($Obj,$Prefixe);
					}
				}
			}
			if (isset($Prop["special"])&&$Prop["special"]=="multi") {
				//Pr tous les langages, on verifie q la variable de la prop existe.
				foreach ($Prefixes as $Prefixe){
					if (array_key_exists($Prefixe."-".$Key,$Obj)&&!isset($OrdreProp[$Prefixe."-".$Key])){
						$OrdreProp[$Prefixe."-".$Key] = $Obj[$Prefixe."-".$Key];
					}
				}
			}

		}
		if ($Error&&!empty($Error)){
			return $Error;
		} 
		//On insere ensuite les proprietes dans une variable objet
		$Properties = $OrdreProp;
		//Verification des droits
		if (isset($Properties["Id"])){
			if (!empty($this->DroitsDefault)){
				if (empty($Properties["gmod"])) $Properties["gmod"] = $this->DroitsDefault[1];
				if (empty($Properties["umod"])) $Properties["umod"] = $this->DroitsDefault[0];
				if (empty($Properties["omod"])) $Properties["omod"] = $this->DroitsDefault[2];
			}
			if (empty($Properties["gmod"])) $Properties["gmod"] = 7;
			if (empty($Properties["umod"])) $Properties["umod"] = 7;
			if (empty($Properties["omod"])) $Properties["omod"] = 7;
		}else{
			if (empty($Properties["gmod"])) $Properties["gmod"] = 7;
			if (empty($Properties["umod"])) $Properties["umod"] = 7;
			if (empty($Properties["omod"])) $Properties["omod"] = 7;
		}
		//Ajout des clefs "courtes"
		if (isset($Obj['Parents'])&&is_array($Obj['Parents'])){
			for ($z=0;$z<sizeof($Obj['Parents']);$z++){
				//recehrche de la clef
				$Par = $Obj['Parents'][$z];
				$keyName = $this->findKey($Par['Fkey'],'parent',$Par['Module']);
				$Association = $this->getKey($Par['Titre'],$keyName,'parent');
				if (is_object($Association)&& $Par["Action"] == 2  && $Association->isShort()&& $Association->isChild($this->titre)) {
					//affectation de la valeur dans le tableau des proprietes
					$Properties[$Association->titre] = $Par["Id"];
				}elseif (is_object($Association)&& $Par["Action"] == 0  && $Association->isShort()&& $Association->isChild($this->titre)) {
					//suppression d'une clef.
					$this->EraseAssociation($Obj["Id"],$Association,$Par["Id"]);
				}
			}
		}
		//Enregistrement objet
		if (!$Properties = $this->insertObject($Properties)) return false;
		//Enregistrement des clefs longues
		if (!isset($Obj["Id"])) {$Id =$Obj["Id"] = $Properties['Id']; $Insert=true;}else {$Id = $Obj["Id"]; $Insert=false;}
		//On fait une boucle qui va inserer les clefs etrangeres les unes apres les autres
		if (isset($Obj['Parents'])&&is_array($Obj['Parents'])){
			for ($z=0;$z<sizeof($Obj['Parents']);$z++){
				$Par = $Obj['Parents'][$z];
				$keyName = $this->findKey($Par['Fkey'],'parent',$Par['Module']);
				$Association = $this->getKey($Par['Titre'],$keyName,'parent');
				if (is_object($Association)){
					if ($Par["Action"]==0 && $Association->isLong()){
						$this->EraseAssociation($Obj["Id"],$Association,$Par["Id"]);
					}elseif($Par["Action"] == 2  && $Association->isLong()){
						$this->insertKey($Par,$Obj['Id'],$Association);
					}
				}
			}
		}
		return $Properties;
	}

	//-------------------------//
	// ERASE		   //
	//-------------------------//

	function Erase($Objet){
		//Efface completement un objet
		$Id = $Objet->Id;
		if ($Id=="") return false;
		return $this->DriverErase($Id);
	}

	function EraseAssociation($currentId,$beforeDs,$beforeId){
		//Efface une association
		return $this->EraseAssociation($currentId,$beforeDs,$beforeId);
	}


	//-------------------------//
	// SAUVEGARDE		   //
	//-------------------------//

	function saveData(){
		//Sauvegarde des donnnees dans des fichiers sql
		return $this->saveData();
	}

	/**
	* TEMPLATES
	*/
	function getTemplates(){
		$dir = "Modules/".$this->Module."/Templates/".$this->titre;
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
}
?>
