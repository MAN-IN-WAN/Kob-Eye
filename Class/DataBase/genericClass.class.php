<?php

class genericClass extends Root {
	var $Id = NULL;
	var $Module;
	var $Referent;
	var $ObjectType;
	var $Historique = Array();
	//Tableaux pour linsertion
	var $Parents = Array();
	var $Enfants = Array();
	var $Error = Array();
	var $Success = Array();
	var $Warning = Array();
	//Variable POST correspondante
	var $setPost;
	var $isHeritage = false;
	var $Triggers;
	var $_view = "";
    //dirty
    var $dirtyPage = false;

	//----------------------------------------------//
	//		INITIALISATION			//
	//----------------------------------------------//
	/**
	 * Constructeur
	 */
	function __construct($refMod = "", $Data = '') {
		//Cree un objet generique correspondant a un resultat de recherche
		$this -> Module = $refMod;
		if (!is_object(Sys::$Modules[$this -> Module]))
			return false;
		Sys::$Modules[$this -> Module] -> loadSchema();
		$ClassName = (gettype($Data) == "array") ? $Data["ObjectType"] : $Data;
		$ObjClass = Sys::$Modules[$refMod] -> Db -> getObjectClass($ClassName);
		$this ->Interface = $ObjClass ->Interface;
		if ($Data != NULL) {
			switch(gettype($Data)) {
				case 'array' :
					$this -> ObjectType = $Data["ObjectType"];
					$this -> initFromArray($Data);
					break;
				case 'string' :
					$this -> ObjectType = $Data;
					$this -> initFromType($Data);
					break;
			}
		}
	}

	/**
	 * GETTER / SETTER
	 */
	/**
	 * Get
	 * Return a value of a property given by his name
	 * @return
	 */
	//public function __get($name){return $this->Get($name);}
	public function Get($Data, $Nom = false) {
		//Appelle une methode pour renvoyer une propriete ou un enfant de l'objet
		$this -> launchTriggers(__FUNCTION__, $Data);
		$Tab = Array();
		if ($Data == "NS") {
			Sys::$Modules[$this -> Module] -> loadSchema();
			$i = Sys::$Modules[$this -> Module] -> Db -> findByTitle($this -> ObjectType);
			$tabProp = Sys::$Modules[$this -> Module] -> Db -> ObjectClass[$i] -> Proprietes;
			$int = 0;
			if (is_array($tabProp))
				foreach ($tabProp as $Key => $Propriete) {
					if (!isset($Propriete["searchOrder"])) {
						$Tab[$int]['Nom'] = $Key;
						$Tab[$int]['Valeur'] = (isset($this -> {$Key})) ? $this -> {$Key} : null;
						$int++;
					}
				}
			return $Tab;
		}
		if (preg_match("#S[0-9]+#", $Data)) {
			if ($Data == "S0")
				return $this -> Id;
			//Si on a un nombre precede d'un S, c'est donc forcement un SearchOrder:
			$Data = substr($Data, 1, count($Data));
			$So = $this -> SearchOrder();
			$Key = false;
			if (sizeof($So)) {
				if (is_array($So))
					foreach ($So as $S) {
						if ($S["searchOrder"] == $Data && !$Key) {
							$Key = $S["Titre"];
						}
					}
			}
			if (!$Key)
				return false;
			return (!$Nom && isset($this -> {$Key})) ? $this -> {$Key} : $Key;
		}
		//On verifie si c est un mot reserve
		if ($Data == "Id")
			return $this -> Id;
		//Sinon, on recherche le type: si c'est une clef etrangere, on recherche les enfants
		/*		echo "---------------$Data--------------\r\n";
		 print_r($this);*/
		if (is_array($this -> Etrangeres()))
			if (array_key_exists($Data, $this -> Etrangeres()))
				return $this -> getChilds($Data);
		//Sinon, on renvoie la propriete recherchee
		foreach ($this->Proprietes() as $Propriete) {
			// 			echo "VAR $Data TYPE ".$Propriete['Nom']."\r\n";
			if ($Propriete['Nom'] == $Data) {
				switch ($Propriete['Type']) {
					case "bbcode" :
						$temp = new charUtils();
						$temp -> ChildObjects[] = $this -> {$Data};
						$temp -> Beacon = "UTIL";
						$temp -> Vars = "BBCODE";
						return $temp -> affich();
						break;
					/*					case "raw":
					 return nl2br($this->$Data);
					 break;*/
					case "txt" :
					case "text" :
					case "html" :
					default :
						return $this -> {$Data};
						break;
				}
			}
		}
        //klog::l($Data.'->'.$this -> {$Data});
		//Donc pas une propriete , donc il s agit d une variable
		if (isset($this -> {$Data}))
			return $this -> {$Data};
		//Si on a pas trouve la propriete, elle n'existe pas et on renvoie une rreur
		return false;
	}

	/**
	 * createInstance
	 * Renvoie une instance d'un genericClass ou d'une class surchagre définie.
	 * @param String Nom du module
	 * @param Array|String Nom de l'objectclass ou tableau de donnée
	 * @return genericClass
	 */
	static function createInstance($refMod, $Data) {
		if (!$refMod)
			return false;
		if (!is_object(Sys::$Modules[$refMod]))
			exit("Le module $refMod n'existe pas.");
		Sys::$Modules[$refMod] -> loadSchema();
		$ClassName = (gettype($Data) == "array") ? $Data["ObjectType"] : $Data;
		$ObjClass = Sys::$Modules[$refMod] -> Db -> getObjectClass($ClassName);
		if (!is_object($ObjClass))
			return;
		$Special = $ObjClass ->Class;
		if ($Special != "") {
			//Le cas d une class etendue
			require_once (ROOT_DIR.$Special);
			if (!empty($ObjClass -> className))
				$ClassName = $ObjClass -> className;
			$Class = new $ClassName($refMod, $Data);
		} else {
			$Class = new genericClass($refMod, $Data);
		}
		return $Class;
	}

	/**
	 * initFromType
	 * Initialisation d'un genericClass vide depuis le nom d'un objectClass
	 * @param String Nom de l'objectClass
	 * @return genericClass
	 */
	function initFromType($Type) {
		//On recupere le type pr creer des proprietes vides a laide du schema
		if ($this -> Module == "")
			return false;
		Sys::$Modules[$this -> Module] -> loadSchema();
		$ObjClass = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($Type);
		$this -> ObjectType = $Type;
		if (isset(Sys::$Modules[$this -> Module]) && is_object($ObjClass))
			$Schema = $ObjClass -> getSchema();
		if (is_array($Schema['Properties']))
			foreach ($Schema['Properties'] as $Key => $Prop) {
                $defaultValue = '';
		        switch ($Prop["type"]){
                    case "random":
                        $defaultValue = Utils::genererCode();
                        break;
                }
				//Priorites de langage
				if (isset($Prop["special"]))
					$Special = $Prop["special"];
				//valeur par défaut
				$defaultValue = (isset($Prop["default"]))?$Prop["default"]:$defaultValue;
				if (is_object(Sys::$User) && Sys::$User -> Admin && isset($Special) && $Special == "multi") {
					$Tab[$Key] = $defaultValue;
					foreach ($GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE") as $Cod => $Lang) {
						if (!isset($Lang["DEFAULT"]) || !$Lang["DEFAULT"])
							$Tab[$Cod . "-" . $Key] = $defaultValue;
					}
				} else {
					$Tab[$Key] = $defaultValue;
				}
			}
		$Tab['ObjectType'] = $Type;
		$this -> initFromArray($Tab);
		if ($this -> isHeritage) {
			$Result = $this -> getHeritagesProp();
			if (is_array($Result))
				$this -> initHeritages($Result);
		}
	}

	/**
	 * initFromId
	 * Initialisation d'un genericClass depuis son Id
	 * @param Interger Identifiant de l'objet
	 * @return genericClass
	 */
	function initFromId($Id, $Obj = null) {
		if ($Id == "")
			return false;
		if ($Obj == null)
			$Obj = $this -> ObjectType;
		$S = $this -> Module . "/" . $Obj . "/" . $Id;
		$T = Sys::$Modules[$this -> Module] -> callData($S);
		//$GLOBALS["Systeme"] -> Log -> log($S, $T);
		$this -> initFromArray($T[0]);
	}

	/**
	 * initFromArray
	 * Initialisation d'un genericClass depuis un tableau
	 * @param Array Tableau de donnÃ©e.
	 * @return genericClass
	 */
	function initFromArray($Tab) {
		//Initialise l'objet a partir d'un tableau
		if (isset($Tab["Sys_Module"]) && $Tab["Sys_Module"] != NULL) {$this -> Module = $Tab["Sys_Module"];
		}
		if (!is_array($Tab))
			return false;
		foreach ($Tab as $Key => $Value) {
			if (is_array($Value))
				$this -> {$Key} = $Value;
			else
//				$this -> $Key = is_null($Value) ? null : stripslashes($Value);
				$this -> {$Key} = is_null($Value) ? null : $Value;
		}
		//On definit les valeurs par defaut
		/*$Props = $this -> Proprietes(false, true);
		if (is_array($Props))
			foreach ($Props as $P) {
				$N = $P["Nom"];
				if ((!isset($this -> $N) || $this -> $N == "") && isset($P["Default"]) && $P["Default"] != "") {
					$this -> $N = Process::ProcessingVars($P["Default"]);
				}
			}*/
		//Verif des heritages
		$this -> launchTriggers(__FUNCTION__);
	}

	//----------------------------------------------//
	// PUBLIC INFORMATIONS				//
	//----------------------------------------------//
	/**
	 * getIcon
	 * Renvoi l'icone par d"faut de l'objectClass
	 * @return String Chemin vers l'icone
	 */
	public function getIcone() {return $this->getIcon();}
	public function getIcon() {
		
		if (!$this -> Module)
			return false;
		$ObjClass = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		$Icon = $ObjClass -> Icon;
		if ($Icon == "")
			$Icon = "/Skins/AdminV2/Img/folder.gif";
		return $Icon;
	}

	/**
	 * Renvoie les propriétés par catégories, ordonnées selon les types
	 * @return [(String,[(String,String)])]
	 */
	public function getOrderedProperties($ParQuery = "", $L = "") {
		$Ordered = array();
		$buildCat = array("line" => array(), "normal" => array(), "block" => array(), "media" => array());
		foreach ($this->Proprietes($L) as $p) {
			$cat = "";
			if (empty($p["Category"])) {
				$cat = "Autres";
			} else {
				$cat = $p["Category"];
			}
			/*if ( empty($p["Valeur"] ) ) {
			 $type = "normal";
			 }else {*/
			$type = $p["displayType"];
			//}
			//On verifie que la propriété ne doivent pas être cachée
			//via une conditionnelle
			$display = true;
			if (isset($this -> Conditions) && is_array($this -> Conditions))
				foreach ($this->Conditions as $C) {
					if (in_array($p["Nom"], $C["PropsName"])) {
						$val = "";
						if (empty($C["About"])) {
							$val = $this -> getHtmlVar("Form_" . $C["Name"]);
							if (!$val) {
								$val = $this -> Get($C["Name"]);
							}
						} else {
							$Parents = $this -> getParents($C["About"]);
							if (empty($Parents) && !$this -> Id) {
								$Parents = $this -> doQuery($this -> Module, $ParQuery);
								$Parents[0] = genericClass::createInstance($this -> Module, $Parents[0]);

							}
							$val = $Parents[0] -> Get($C["Name"]);
						}
						if (!in_array($val, $C["Value"])) {
							$display = false;
						}
					}
				}

			if (!isset($p["hidden"]) && isset($display) && $display) {
				if (!array_key_exists($cat, $Ordered)) {
					$Ordered[$cat] = $buildCat;
				}
				$Ordered[$cat][$type][] = $p;
			}
		}
		return $Ordered;
	}

	/**
	 * Renvoie le type d'affichage pour un type donné
	 * @param String $Type Le type
	 * @return String
	 */
	public function getFirstPropertyOf() {
		$numargs = func_num_args();
		$arg_list = func_get_args();
		foreach ($this->Proprietes() as $p) {
			for ($i = 0; $i < $numargs; $i++) {
				if ($arg_list[$i] == $p['Type'])
					return $p;
			}
		}
	}

	/**
	 * Renvoie le type d'affichage pour un type donné
	 *
	 * @param String $Type Le type
	 * @return String
	 */
	public function getDisplayType($Type) {
		$normalTypes = array("varchar"=>true, "int"=>true, "private"=>true, "password"=>true, "alias"=>true, "objectclass"=>true, "color"=>true, "random"=>true, "metat"=>true, "metad"=>true, "id"=>true, "autodico"=>true, "mail"=>true, "url"=>true, "private"=>true, "order"=>true, "date"=>true, "price"=>true, "pourcent"=>true, "link"=>true, "canonic"=>true, "langfile"=>true, "string"=>true);
		$lineTypes = array("titre"=>true);
		$blockTypes = array("text"=>true, "txt"=>true, "html"=>true, "bbcode"=>true);
		$mediaTypes = array("file"=>true, "image"=>true);
		if (isset($normalTypes[$Type])) {
			return "normal";
		} elseif (isset($lineTypes[$Type])) {
			return "line";
		} elseif (isset($blockTypes[$Type])) {
			return "block";
		} elseif (isset($mediaTypes[$Type])) {
			return "media";
		} else {
			return "normal";
		}
	}

	/**
	 * getCategories
	 * Renvoie la liste des catégories pour un objet
	 * @return Array(Array(String))
	 */
	public function getCategories() {
		//Recupération de l'objetClass
		$Obj = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		//Récupération de la liste des catégories de propriétés
		return $Obj -> getCategories();
	}
	/**
	 * getCategories
	 * Renvoie une categorie avec tous ses attributs
	 * @param String name
	 * @return Array(Array(String))
	 */
	public function getCategory($name) {
		//Recupération de l'objetClass
		$Obj = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		//Récupération de la liste des catégories de propriétés
		return $Obj -> getCategory($name);
	}
	/**
	 * getElement
	 * @param String nom de l'element
	 */
	public function getElement ($name){
		return $this->getElementsByAttribute('name', $name ,true);
	}
	/**
	 * getElements
	 * @param L String Prefixe de langue ex: FR, EN ...
	 * Renvoi l'ensemble des éléments internes d'un objet ordonné par catégorie.
	 *  - les propriétés
	 *  - les clef parentes
	 *  - les clefs enfantes
	 * @return Array(Array(Array(String => String)))
	 */
	public function getElements($L="") {
		$Prefixe="";
		$DefaultLanguage = $GLOBALS["Systeme"] -> DefaultLanguage;
		$CurrentLanguage = $GLOBALS["Systeme"] -> CurrentLanguage;
		//Vérification de la langue par défaut
		if (array_key_exists($DefaultLanguage, $GLOBALS["Systeme"] -> Language)) {
			if (!empty($L)){
				if ($GLOBALS["Systeme"] -> Language[$DefaultLanguage] == $L)
					$default=true;
				else $default = false;
			}else {
				//cas par défaut
				$default=true;
			}
		}
		//Vérification de l'existence et de l'accès de la langue
		if (!$default) {
			$test = false;
			foreach ($GLOBALS["Systeme"]->Language as $Lang => $Pref) {
				if ($L == $Pref && (($Lang==$DefaultLanguage||$Lang==$CurrentLanguage)||(isset(Sys::$User->Admin)&&Sys::$User->Admin))) {
					$test = true;
					$Prefixe = $Pref.'-';
				}
			}
			if (!$test) return;
		}
		//Recupération de l'objetClass
		$Obj = Sys::$Modules[$this->Module]->Db->getObjectClass($this->ObjectType);
		$t = $Obj->getElements($this->_view);
		foreach ($t as $c=>$o){
			for ($j=0;$j<sizeof($t[$c]["elements"]);$j++){
				if ($default||(isset($t[$c]["elements"][$j]["special"])&&$t[$c]["elements"][$j]["special"]=="multi")){
					$p = &$t[$c]["elements"][$j];
					$n = $Prefixe.$p["name"];
					$p['name'] = $n;
					if (isset($this->{$n}))
						$p["value"] = $this->{$n};
					if (empty($p["description"]))$p["description"] = $p["name"];
				}else{
					array_splice($t[$c]["elements"],$j,1);
					$j--;
				}
			}
			//si la catégorie est vide alors on la supprime
			if (!sizeof($t[$c]["elements"])) unset($t[$c]);
		}
		return $t;
	}

	/**
	 * getChildElements
	 * Renvoi l'ensemble des éléments enfants
	 *  - les clefs enfantes
	 * @return Array(Array(Array(String => String)))
	 */
	public function getChildElements() {
		//RecupÃ©ration de l'objetClass
		$Obj = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		return $Obj -> getChildElements();
	}
	/**
	 * getParentElements
	 * Renvoi l'ensemble des éléments parents
	 *  - les clefs parentes
	 * @return Array(Array(Array(String => String)))
	 */
	public function getParentElements() {
		//RecupÃ©ration de l'objetClass
		$Obj = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		return $Obj -> getParentElements();
	}

	/**
	 * getProperty
	 * renvoi une propriete
	 * @param String Name of the property
	 * @return Array property details
	 */
	#DEPRECATED
	public function getPropriete($N) {
		return $this -> getProperty($N);
	}

	public function getProperty($N) {
		//Il faut le fair epour chaque langue
		$DefaultLanguage = $GLOBALS["Systeme"] -> DefaultLanguage;
		foreach ($GLOBALS["Systeme"]->Language as $Lang => $Pref) {
			if ($Lang != $DefaultLanguage)
				$Tab = $this -> Proprietes($Pref);
			else
				$Tab = $this -> Proprietes();
			foreach ($Tab as $P) {
				if ($P["Nom"] == $N) {
					return $P;
				}
			}
		}
		return false;
	}

	/**
	 * getPropertiesByAttribute
	 * renvoi une liste de proprietes filtrÃ©e sur un attribut
	 * @param String Name of the attribute
	 * @param String Value of the attribute
	 * @return Array property details
	 */
	public function getPropertiesByAttribute($A, $V = "") {
		$O = Array();
		//Il faut le fair epour chaque langue
		$DefaultLanguage = $GLOBALS["Systeme"] -> DefaultLanguage;
		foreach ($GLOBALS["Systeme"]->Language as $Lang => $Pref) {
			if ($Lang != $DefaultLanguage)
				$Tab = $this -> Proprietes($Pref);
			else
				$Tab = $this -> Proprietes();
			foreach ($Tab as $P) {
				if (isset($P[$A]) && (empty($V) || $P[$A] == $V)) {
					$O[] = $P;
				}
			}
		}
		if (!sizeof($O))
			return false;
		return $O;
	}

	/**
	 * getElementsByAttribute
	 * renvoi une liste d'elements' filtrÃ©e sur un attribut
	 * @param String Name of the attribute
	 * @param String Value of the attribute
	 * @return Array elments details
	 */
	public function getElementsByAttribute($At, $V = "",$flat=false, $L="") {
		$O = Array();
		$attr = explode('|',$At);
		//Il faut le faire pour chaque langue
		$Tab = $this -> getElements($L);
        foreach ($Tab as $CatName => $Cat)
            foreach ($Cat as $ElemsName => $Elems)
                foreach ($Elems as $Elem) {
                    $ok = false;
                    foreach ($attr as $A)if (isset($Elem[$A]) && (empty($V) || $Elem[$A] == $V)||$A==null) $ok = true;
                    if ($ok){
                        if ($flat){
                            $O[] = $Elem;
                        }else{
                            if (!isset($O[$CatName])) {
                                $O[$CatName] = Array();
                                $O[$CatName][$ElemsName] = Array();
                            }
                            $O[$CatName][$ElemsName][] = $Elem;
                        }
                    }
                }
		//on ordonne sur l'attribut
		if (empty($V)){
			$O = Storproc::SpBubbleSort($O,$A);
		}
		if (!sizeof($O))
			return false;
		return $O;
	}

	/**
	 * getProperties
	 * Return the list properties list of this genericClass
	 * @return Array of properties
	 */
	#DEPRECATED
	public function Proprietes($L = "", $I = false) {
		return $this -> getProperties($L, $I);
	}

	public function getProperties($L = "", $I = false) {

		//Tableau contenant les proprietes par ordre d'importance et avec toutes les infos necessaires
		// 		if (!sizeof($Props)) {
		Sys::$Modules[$this -> Module] -> loadSchema();
		$ObjectClass = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		if (!is_object($ObjectClass))
			return false;
		$tabProp = $ObjectClass -> Proprietes;
		//Cas d'un objet chargé en vue
		if (isset($this -> _view) && !empty($this -> _view)) {
			$View = $ObjectClass -> getView($this -> _view);
			$tabProp = array_merge($tabProp, $View -> ViewProperties);
		}
		//Cas d'un objet anonyme
		// EM - 20140502 Suppression du cas, pour avoir les champs de la vue sur un objet anonyme, il faut faire un setView
		/*if (empty($this -> Id) && is_object($ObjectClass -> defaultView)) {
			$View = $ObjectClass -> defaultView;
			if (is_array($View -> ViewProperties))
				$tabProp = array_merge($tabProp, $View -> ViewProperties);
			//print_r($tabProp);
		}*/
		$i = 0;
		$DefaultLanguage = $GLOBALS["Systeme"] -> DefaultLanguage;
		$CurrentLanguage = $GLOBALS["Systeme"] -> CurrentLanguage;
		if (array_key_exists($DefaultLanguage, $GLOBALS["Systeme"] -> Language)) {
			$Prefixes[$DefaultLanguage] = $GLOBALS["Systeme"] -> Language[$DefaultLanguage];
		}
		if (Sys::$User && isset(Sys::$User -> Admin) && Sys::$User -> Admin) {
			foreach ($GLOBALS["Systeme"]->Language as $Lang => $Pref) {
				if ($DefaultLanguage != $Lang) {
					$Prefixes[$Lang] = $Pref;
				} else {
					$DefaultPrefixe = $Pref;
				}
			}
		}
		if ($L != "" && $L) {
			if (in_array($L, $GLOBALS["Systeme"] -> Language)) {
				foreach ($GLOBALS["Systeme"]->Language as $Lang => $Pref) {
					if ($L == $Pref) {
						$Prefixes[$Lang] = $Pref;
					}
				}
			}
		}
		$SearchOrder = $tabProp;
		//On parcourt donc tout d'abord les objets ayant des SearchOrder
		if (is_array($SearchOrder)) {
			foreach ($SearchOrder as $Key => $Cible) {
				if ($Key != "Id" && ((isset($tabProp[$Key]["adminOnly"]) && $tabProp[$Key]["adminOnly"] && Sys::$User -> Admin) || (!isset($tabProp[$Key]["adminOnly"]) || !$tabProp[$Key]["adminOnly"]) || $I)) {
					$Infos = $tabProp[$Key];
					$Infos['Nom'] = $Key;
					$Infos['name'] = $Key;
					$Infos['Langue'] = $Prefixes[$DefaultLanguage];
					if (isset($this -> {$Key})&& is_string($this->{$Key}) && strlen($this -> {$Key}) == 0)
						$Infos['isNull'] = "True";
					else
						$Infos['isNull'] = "False";
					$Infos['Valeur'] = (isset($this -> {$Key})) ? $this -> {$Key} : null;
					if (isset($tabProp[$Key]['searchOrder']))
						$Infos['SearchOrder'] = $tabProp[$Key]['searchOrder'];
					else
						$Infos['SearchOrder'] = null;
					if (isset($tabProp[$Key]['Ref']))
						$Infos['Ref'] = $tabProp[$Key]['Ref'];
					$Infos['Type'] = $tabProp[$Key]['type'];
					if (isset($tabProp[$Key]['filter']))
						$Infos['Filter'] = $tabProp[$Key]['filter'];
					if (isset($tabProp[$Key]['category']))
						$Infos['Category'] = $tabProp[$Key]['category'];
					$Infos['displayType'] = $this -> getDisplayType($tabProp[$Key]['type']);
					if (isset($tabProp[$Key]['length']))
						$Infos['Longueur'] = $tabProp[$Key]['length'];
					if (isset($tabProp[$Key]['target']))
						$Infos['Target'] = $tabProp[$Key]['target'];
					if (isset($tabProp[$Key]['description']))
						$Infos['description'] = $tabProp[$Key]['description'];
					else
						$Infos['description'] = $Infos['Nom'];
					if (isset($tabProp[$Key]["default"]))
						$Infos['Default'] = $tabProp[$Key]["default"];
					$Props[$i] = $Infos;
					$i++;
					// 					Verification pour le multilingue
					if (is_array($Prefixes))
						foreach ($Prefixes as $NomPref => $Prefixe) {
							if ($NomPref!=$DefaultLanguage){
								$NomLangue = $Prefixe . "-$Key";
								if (isset($Infos["special"])&&$Infos["special"]=="multi"){
									$Props[$i] = $Infos;
									$Props[$i]['Nom'] = $Prefixe . "-" . $Key;
									$Props[$i]['Langue'] = $Prefixe;
									$Props[$i]['description'] = "$Key ($NomPref)";
									$Props[$i]['Valeur'] = isset($this -> {$NomLangue}) ? $this -> {$NomLangue}: '';
									$i++;
								}
							}
						}
				}
			}
		}
		//On ajoute egalement un champ Titre pour les proprietes
		if (isset($Props) && is_array($Props))
			for ($i = 0; $i < sizeof($Props); $i++) {
				$Props[$i]["Titre"] = $Props[$i]["Nom"];
			}
		if ($L == "") {
			//On recupere le language par defaut
			$DefaultLanguage = $GLOBALS["Systeme"] -> DefaultLanguage;
			$L = $GLOBALS["Systeme"] -> Language[$DefaultLanguage];
		}
		//Alors il faut renvoyer un tri sur les prorprietes selon la langue
		$Result = Array();
		if (isset($Props) && is_array($Props))
			for ($i = 0; $i < sizeof($Props); $i++) {
				if ($Props[$i]["Langue"] == $L)
					$Result[] = $Props[$i];
			}
			
		return $Result;
	}

	/**
	 * getSearchOrder
	 * Return the only the significatives property of this object
	 * @param String Language to use
	 * @return Array of properties
	 */
	#DEPRECATED
	public function SearchOrder($Langue = "") {
		return $this -> getSearchOrder($Langue);
	}

	public function getSearchOrder($Langue = "") {
		$Result = "";
		if (!in_array($Langue, $GLOBALS["Systeme"] -> Language)) {
			//On recupere le language par defaut
			$DefaultLanguage = $GLOBALS["Systeme"] -> DefaultLanguage;
			$Langue = $GLOBALS["Systeme"] -> Language[$DefaultLanguage];
		}
		$Prop = $this -> Proprietes();
		if (is_array($Prop))
			for ($i = 0; $i < sizeof($Prop); $i++) {
				if (is_numeric($Prop[$i]["SearchOrder"]) && $Prop[$i]["Langue"] == $Langue)
					$Result[] = $Prop[$i];
			}
		return $Result;
	}

	/**
	 * getFirstSearchOrder
	 * return a reference of the first searchordered property
	 * @return Array of property
	 */
	public function getFirstSearchOrder() {
		return $this -> Get("S1");
	}

	/**
	 * getSecondSearchOrder
	 * return a reference of the second searchordered property
	 * @return Array of property
	 */
	function getSecondSearchOrder() {
		return $this -> Get("S2");
	}

	/**
	 * getFilter
	 * return the list of properties used to defines filter o, this kind of object
	 * @return Array list of properties
	 */
	public function GetFilter() {
		$Result = "";
		$Prop = $this -> Proprietes();
		if (is_array($Prop))
			for ($i = 0; $i < sizeof($Prop); $i++) {
				if (isset($Prop[$i]["filter"]))
					$Result[] = $Prop[$i];
			}
		return $Result;
	}

	/**
	 * getIntegratedLinks
	 * return a list of links defined as "integrated"
	 * @return Array of links
	 */
	#DEPRECATED
	public function IntegratedLinks() {
		return $this -> getIntergatedLinks();
	}

	public function getIntegratedLinks() {
		$Enfants = $this -> typesEnfant();
		foreach ($Enfants as $Enf) {
			if ($Enf["Behaviour"] == "Integrated") {
				$Result[] = $Enf;
			}
		}
		return $Result;
	}

	/**
	 * getInfos
	 * return a list of informations from this object
	 * @return Array of informations
	 */
	#DEPRECATED
	public function infoSysteme() {
		return $this -> getInfos();
	}

	public function getInfos() {
		$Reserved = $this -> Reserved();
		//Construction du tableau de propriete des droits
		$Infos['Nom'] = "Type d'ObjectClass";
		$Infos['Valeur'] = $this -> ObjectType;
		$Infos['Type'] = "varchar";
		$infoSysteme[] = $Infos;
		$Infos['Nom'] = "Date de création";
		$Infos['Valeur'] = $Reserved[0]['dateCreate'];
		$Infos['Type'] = "date";
		$infoSysteme[] = $Infos;
		$Infos['Nom'] = "Date de modification";
		$Infos['Valeur'] = $Reserved[0]['dateEdit'];
		$Infos['Type'] = "date";
		$infoSysteme[] = $Infos;
		$U = Sys::$Modules["Systeme"] -> callData("Systeme/User/" . $Reserved[0]['userCreate'], 0, 0, 1);
		$C = $Reserved[0]['userCreate'];
		$Infos['Nom'] = "Créé par l'utilisateur";
		$Infos['Valeur'] = $C;
		$Infos['Type'] = "varchar";
		$infoSysteme[] = $Infos;
		$U = Sys::$Modules["Systeme"] -> callData("Systeme/User/" . $Reserved[0]['userEdit'], 0, 0, 1);
		$C = $Reserved[0]['userEdit'];
		$Infos['Nom'] = "Edité par l'utilisateur";
		$Infos['Valeur'] = $C;
		$Infos['Type'] = "int";
		$infoSysteme[] = $Infos;
		return $infoSysteme;
	}

	/**
	 * getParentAssociations
	 * Recupere la liste des association parentes
	 */
	public function getParentAssociations() {
		Sys::$Modules[$this -> Module] -> loadSchema();
		$ObjClass = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		$C = $ObjClass -> getParentAssociations();
		return $C;
	}
	/**
	 * getChildAssociations
	 * Recupere la liste des associations enfantes
	 */
	public function getChildAssociations() {
		Sys::$Modules[$this -> Module] -> loadSchema();
		$ObjClass = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		$C = $ObjClass -> getChildAssociations();
		return $C;
	}
	/**
	 * getParentTypes
	 * Return a list of parent objectclass
	 * @return Array of Association Object
	 */
	#DEPRECATED
	public function Etrangeres() {
		return $this -> getParentTypes();
	}

	public function typesParent() {
		return $this -> getParentTypes();
	}

	public function getParentTypes() {
		Sys::$Modules[$this -> Module] -> loadSchema();
		$ObjClass = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		$C = $ObjClass -> getParent();
		$Ct = Array();
		if (is_array($C) && !empty($C))
			foreach ($C as $A) {
				$O = $A -> getParentObjectClass();
				$Ct[] = Array("Titre" => $O -> titre, "Icon" => $O -> Icon, "Driver" => $O -> driver, "Nom" => $A -> titre, "Card" => $A -> getCard('parent'), "Target" => $A -> getTarget(), "Default" => $A->Default, "Long" => $A -> isLong(), "Short" => $A -> isShort(), "Description" => $O -> Description,"browseable" => $O -> browseable,"noRecursivity"=>$O->noRecursivity,"stopPage"=>$O->stopPage);
			}
		return $Ct;
	}

	/**
	 * getChildTypes
	 * Retourne les enfants d'un objectclass
	 * @return Array of Association Object
	 */
	#DEPRECATED
	public function typesEnfant($force=false) {
		return $this -> getChildTypes($force);
	}

	public function getChildTypes($force=false) {
		$ObjClass = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		$P = $ObjClass -> getChild();
		$Pt = Array();
		if (is_array($P) && !empty($P))
			foreach ($P as $A) {
				$O = $A -> getChildObjectClass();
				//evite les boucles pour un cas de reflexivité non recursive (produits associés par exemple)
				if ($ObjClass->titre==$O->titre&&$O->noRecursivity&&!$force) continue;
				$Pt[] = Array("Titre" => $O -> titre, "Icon" => $O -> Icon, "Driver" => $O -> driver, "Nom" => $A -> titre, "Card" => $A -> getCard('child'), "Target" => $A -> getTarget(), "Default" => $A->Default, "Long" => $A -> isLong(), "Short" => $A -> isShort(), "Description" => $O -> Description,"browseable" => $O -> browseable,"noRecursivity"=>$O->noRecursivity,"stopPage"=>$O->stopPage);
			}
		return $Pt;
	}


	/**
	 * getObjectClass
	 * Return the objectClass
	 * @return ObjectClass
	 */
	public function getObjectClass() {
		return Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
	}


	/**
	 * getFunctions
	 * Return the function defined for this object
	 * @return Array of String name of functions
	 */
	#DEPRECATED
	public function Functions() {
		return $this -> getFunctions();
	}

	public function getFunctions() {
		$ObjClass = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		if (!isset($ObjClass -> Functions))
			return false;
		$Funcs = $ObjClass -> Functions;
		$Functions = false;
		if (is_array($Funcs))
			foreach ($Funcs as $Name => $F) {
				$Temp = $F;
				$Temp["Nom"] = $Name;
				$Functions[] = $Temp;
			}
		return $Functions;
	}

	/**
	 * getFunction
	 * Return a detailed version of the function
	 * @param string name of the function
	 * @return Array
	 */
	public function getFunction($n) {
		$ObjClass = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		return Array($ObjClass -> getFunction($n));
	}

	/**
	* getInterface
	* Return a list of available interface config
	*/
	public function getInterfaces() {
		$ObjClass = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		if (!isset($ObjClass -> Interfaces))
			return false;
		$Inters = $ObjClass -> Interfaces;
		return $Inters;
	}
	/**
	* getConfiguration
	* Return a list of available configuration
	*/
	public function getConfiguration() {
		$ObjClass = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		if (!isset($ObjClass -> Configuration))
			return false;
		$Confs = $ObjClass -> Configuration;
		return $Confs;
	}

	/**
	 * getRights
	 * Return an array with rights defined of this object
	 * @return Array of rights
	 */
	#DEPRECATED
	public function Droits() {
		return $this -> getRights();
	}

	public function getRights() {
		//Construction du tableau de propriete des droits
		$C = $this -> uid;
		$Infos['Nom'] = "Proprietaire";
		$Infos['Valeur'] = $C;
		$Infos['Type'] = "varchar";
		$Droits[] = $Infos;
		$C = $this -> gid;
		$Infos['Nom'] = "Groupe";
		$Infos['Valeur'] = $C;
		$Infos['Type'] = "varchar";
		$Droits[] = $Infos;
		$Infos['Nom'] = "Umod";
		$Infos['Valeur'] = $this -> umod;
		$Infos['Type'] = "int";
		$Droits[] = $Infos;
		$Infos['Nom'] = "Gmod";
		$Infos['Valeur'] = $this -> gmod;
		$Infos['Type'] = "int";
		$Droits[] = $Infos;
		$Infos['Nom'] = "Omod";
		$Infos['Valeur'] = $this -> omod;
		$Infos['Type'] = "int";
		$Droits[] = $Infos;
		return $Droits;
	}

	/**
	 * getHistory
	 * Return The history of this object in the original query context
	 * @return Array of History
	 */
	#DEPRECATED
	public function Historique() {
		return $this -> getHistorical();
	}

	public function getHistorical() {
		//Construit l'url et edite l'historique
		$Url = $this -> Module;
		$Nbhisto = count($this -> Historique) - 1;
		//Construction de l'url
        $Obc = null;
		for ($i = 0; $i <= $Nbhisto; $i++) {
			$this -> Historique[$i]['Num'] = $i;
			//On verifie si l'objectClass n'est pas default
			$Obc = ($i == 0 || $this -> Historique[$i - 1]['ObjectType'] == $this -> Historique[$i]['ObjectType']) ? Sys::$Modules[$this -> Module] -> Db -> findByTitle($this -> Historique[$i]['ObjectType']) : $Obc;
			if (!is_object($Obc) || !$Obc->Default)
				$Url .= '/' . $this -> Historique[$i]['ObjectType'];
			$this -> Historique[$i]['objectUrl'] = $Url;
			$Url .= '/' . $this -> Historique[$i]['Id'];
			$this -> Historique[$i]['getUrl'] = $Url;
			if ($i == count($this -> Historique) - 1)
				$this -> beforeUrl = $Url;
			if (empty($kUrl) && isset($this -> Historique[$i]['Url']))
				$kUrl = $this -> Historique[$i]['Url'];
			elseif (isset($this -> Historique[$i]['Url']))
				$kUrl .= '/' . $this -> Historique[$i]['Url'];
			if (isset($kUrl))
				$this -> Historique[$i]['quickUrl'] = $kUrl;
		}
		$this -> firstObjType = ($i > 0 && isset($this -> Historique[$i]['ObjectType'])) ? $this -> Historique[$i]['ObjectType'] : $this -> ObjectType;
		$this -> menuUrl = ((isset($kUrl)) ? $kUrl . '/' : '') . $Url;
		$Obc = Sys::$Modules[$this -> Module] -> Db -> findByTitle($this -> ObjectType);
		if (!is_object($Obc) || !$Obc->Default)
			$Url .= '/' . $this -> ObjectType;
		$this -> objectUrl = $Url;
		$this -> Level = $i + 1;
		if (isset($this -> Id))
			$Url .= '/' . $this -> Id;
		$this -> myUrl = $Url;
		return $this -> Historique;
	}

	/**
	 * getUrl
	 * return the url of this object in the original query context
	 * @return String of history's query
	 */
	public function getUrl() {
		if ($site = Site::getCurrentSite()) {
			//recherche des pages pour ce domaine
			$pags = $site->getChildren('Page/PageModule=' . $this->Module . '&PageObject=' . $this->ObjectType . '&PageId=' . $this->Id);
			if (sizeof($pags)) return $pags[0]->Url;

		    $mens =  Sys::getMenus($this->Module.'/'.$this->ObjectType.'/'.$this->Id,true,true);
            if (sizeof($mens)) return $mens[0]->Url;
		}
		$Url = $this -> Module;
		$Nbhisto = sizeof($this -> Historique()) - 1;
		for ($i = 0; $i <= $Nbhisto; $i++) {
			$this -> Historique[$i]['Num'] = $i;
			if ($i == 0)
				$this -> firstObjType = $this -> Historique[$i]['ObjectType'];
			$Url .= '/' . $this -> Historique[$i]['ObjectType'];
			$this -> Historique[$i]['objectUrl'] = $Url;
			$Url .= '/' . $this -> Historique[$i]['Id'];
			$this -> Historique[$i]['getUrl'] = $Url;
			if ($i == count($this -> Historique) - 1)
				$this -> beforeUrl = $Url;
		}
		//		if (!$Nbhisto) $this->Historique = Array();
		if (empty($this -> firstObjType))
			$this -> firstObjType = $this -> ObjectType;
		//$this->menuUrl = $kUrl.'/'.$this->Url;
		$Url .= '/' . $this -> ObjectType;
		$this -> objectUrl = $Url;
		$this -> Level = $i + 1;
		$Url .= '/' . $this -> Id;
		$this -> myUrl = $Url;
		return $Url;

		//."/".$this->ObjectType."/".$this->Id;
	}

	/**
	 * isRecursiv
	 * Ask if the objectclass of this object is recursiv
	 * @return Boolean
	 */
	public function isRecursiv() {
		return $this -> isReflexive();
	}

	public function isReflexive() {
		return Sys::$Modules[$this -> Module] -> Db -> isReflexive($this -> ObjectType);
	}

	/**
	 * getChilds
	 * Return Childs of a given type from this object
	 * @param String Name of the child type
	 * @return Array of genericClass
	 */
	public function getChilds($Type) {
		return $this -> getChildren($Type);
	}

	public function getChildren($Type) {
        $Childs = Array();
		//Renvoie un tableau contenant les enfants de l'objet en cours
		if ($this -> Id) {
			$Query = $this -> ObjectType . '/' . $this -> Id . '/' . $Type;
			$Chi = $this -> executeQuery($Query);
			if (is_array($Chi))
				foreach ($Chi as $k => $Ch) {
					if (strlen($Ch["Module"])) {
						$Childs[] = genericClass::createInstance($Ch["Module"], $Ch);
					}
				}
		}
		return $Childs;
	}
	
	/**
	 * getOneChild
	 * Return First Child found of a given type from this object
	 * @param String Name of the child type
	 * @return genericClass
	 */
	public function getOneChild($Type) {
		$childs = $this -> getChildren($Type);
		if (sizeof($childs)) return $childs[0];
		return false;
	}
	

	/**
	 * getParents
	 * Return Parents of a given type from this object
	 * @param String Name of the parent type
	 * @return Array of genericClass
	 */
	public function getParents($Type = "") {
		//Renvoie un tableau contenant les parents de l'objet en cours
		if ($this -> Id != 0) {
			$Query = $Type . '/' . $this -> ObjectType . '/' . $this -> Id;
			$Par = $this -> executeQuery($Query);
			$Parents = Array();
			if (is_array($Par))
				foreach ($Par as $k => $Pa) {
					if (strlen($Pa["Module"])) {
						$tmp = genericClass::createInstance($Pa["Module"], $Pa);
						if ($tmp) $Parents[] = $tmp;
					}
				}
		} else {
			//Si pas enregistré alors on simule la table parent à partir de la table temporaire
			$Parents = Array();
			if (isset($this -> Parents) && !empty($this -> Parents)) {
				//detection d'une clef specifique
				if(preg_match("#(.*?)\.([^:]*)#", $Type, $t)){
					foreach ($this->Parents as $P) {
						if($P["Titre"] == $t[1] && $P["Fkey"] == $t[2]) {
							$Pt = genericClass::createInstance($P["Module"], $P["Titre"]);
							$Pt -> initFromId($P["Id"], $P["Titre"]);
							$Parents[] = $Pt;
						}
					}
				}
				else {
					foreach ($this->Parents as $P) {
						if ((!empty($Type) && $P["Titre"] == $Type) || empty($Type)) {
							$Pt = genericClass::createInstance($P["Module"], $P["Titre"]);
							$Pt -> initFromId($P["Id"], $P["Titre"]);
							$Parents[] = $Pt;
						}
					}
				}
			}
		}
		return $Parents;
	}
	
	/**
	 * getOneParent
	 * Return The first Parent found of a given type from this object
	 * @param String Name of the parent type
	 * @return genericClass
	 */
	public function getOneParent($Type = "") {
		$parents = $this->getParents($Type);
		if (sizeof($parents)) return $parents[0];
		return false;
	}

	/**
	 * getCard
	 * Return the cardinality between this object's objectclass and and the type given by the parameter
	 * @param String Object Type name
	 * @return String Cardinality
	 */
	public function getCard($Class) {
		if (is_array($Class))
			$Class = $Class[0];
		$Oc = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		$ObjClass = $Oc -> getChildObjectClass($Class);
		if (is_object($ObjClass))
			return $ObjClass -> getCard($Class);
		$ObjClass = $Oc -> getParentObjectClass($Class);
		if (is_object($ObjClass))
			return $ObjClass -> getCard($Class, 'parent');
		return false;
	}

	/**
	 * isTail
	 * Return the tail information if this object is a recursiv one.
	 * @return Boolean
	 */
	public function isTail() {
		if ($this -> Get("Bd") && $this -> Get("Bg"))
			return ($this -> Get("Bd") - $this -> Get("Bg") > 1) ? false : true;
		else
			return false;
	}

	/**
	 * Check if the given name is a property's name
	 * @param String Name of the property
	 * @return Boolean
	 */
	public function isProperty($p) {
		//Il faut le fair epour chaque langue
		$DefaultLanguage = $GLOBALS["Systeme"] -> DefaultLanguage;
		foreach ($GLOBALS["Systeme"]->Language as $Lang => $Pref) {
			if ($Lang != $DefaultLanguage)
				$Tab = $this -> Proprietes($Pref, true);
			else
				$Tab = $this -> Proprietes();
			foreach ($Tab as $P) {
				if ($P["Nom"] == $p)
					return true;
			}
		}
		return false;
	}

	/**
	 * isChild
	 * Check if the parameter is a child objectclass name.
	 * @param String Name of the objectType
	 * @return Boolean
	 */
	public function isChild($p) {
		$Tab = $this -> typesEnfant();
		foreach ($Tab as $P)
			if ($P["Titre"] == $p)
				return true;
		return false;
	}

	/**
	 * isParent
	 * Check if the parameter is a child objectclass name
	 * @param String Name of the objecttype
	 * @return Boolean
	 */
	public function isParent($p) {
		$Tab = $this -> typesParent();
		foreach ($Tab as $P)
			if ($P["Titre"] == $p)
				return true;
		return false;
	}

	/**
	 * isCurrent
	 * Does this object belong to the orignal query
	 * @return Boolean
	 */
	public function isCurrent() {
		$Q = Process::processVars("Query");
		if (sizeof(explode($this -> ObjectType . '/' . $this -> Id, $Q)) > 1)
			return true;
	}

	//----------------------------------------------//
	//	PUBLIC MODIFICATION			//
	//----------------------------------------------//
	/**
	 * Delete
	 * Delete this function
	 * @return Boolean
	 */
	public function Delete() {
		//Programme la suppression de l'objet courant
		$NumObj = Sys::$Modules[$this -> Module] -> Db -> findByTitle($this -> ObjectType);
		if ($NumObj < 0 || empty($this -> Id))
			return false;
		$obj = $this->getObjectClass();
		if ($obj->browseable){
			//Suppression des pages correspondantes
			$tls = Sys::getData('Systeme','Page/PageModule='.$this->Module.'&PageObject='.$this->ObjectType.'&PageId='.$this->Id);
			foreach ($tls as $tl) $tl->Delete();
		}
		//Suppression de l'objet
		$Flag = Sys::$Modules[$this -> Module] -> Db -> ObjectClass[$NumObj] -> Erase($this);
		//$Flag = Sys::$Modules[$this->Module]->Db->ObjectClass[$Obj]->Erase($this);
		$this -> launchTriggers(__FUNCTION__);
		if ($obj->browseable){
			$this->deletePages();
		}
		return $Flag;
	}

	/**
	 * addParent
	 * Add a parent link
	 * @param String Object Type name
	 * @param Int Id of the parent object
	 */
	#DEPRECATED
	public function addParent($Q = "", $SpeFKey = "") {
		if (is_object($Q))
			$Q = $Q -> Module . '/' . $Q -> ObjectType . '/' . $Q -> Id;
		if (!$Q)
			return false;
		//detection d'une clef specifique
		if (!$SpeFKey&&preg_match_all("#\/.*?\.(.*?)\/#", $Q,$t)){
			$SpeFKey = $t[sizeof($t)-1][0];
		}
		
		$Ie = explode('/', $Q, 2);
        if (!is_object(Sys::$Modules[$Ie[0]])){
            print_r($Q);
            die("AddParent: Mauvais format ");
        }
        $I = Sys::$Modules[$Ie[0]]->splitQuery($Q);
		if ($I[0]["Type"] == "Child" && sizeof($I) > 1)
			$Q = $I[sizeof($I) - 2]["Module"] . "/" . $I[sizeof($I) - 2]["DataSource"] . "/" . $I[sizeof($I) - 2]["Value"];
		else
			$Q = $I[sizeof($I) - 1]["Module"] . "/" . $I[sizeof($I) - 1]["DataSource"] . "/" . $I[sizeof($I) - 1]["Value"];
		$ExplQ = explode("/", $Q);
		if (sizeof($ExplQ) < 3)
			return false;
		$NbQ = sizeof($ExplQ) - 1;
		$this -> addFkey($ExplQ[$NbQ - 2], $ExplQ[$NbQ - 1], $ExplQ[$NbQ], 2, $SpeFKey);
	}

	/**
	 * delParent
	 * Delete a parent link
	 * @param String Object Type name
	 * @param Int Id of the parent object
	 */
	#DEPRECATED
	public function delParent($Q = "", $SpeFKey = "") {
		if (is_object($Q))
			$Q = $Q -> getUrl();
		if (!$Q)
			return false;
		//detection d'une clef specifique
		if (!$SpeFKey&&preg_match_all("#\/.*?\.(.*?)\/#", $Q,$t)){
			$SpeFKey = $t[sizeof($t)-1][0];
		}
		$Ie = explode('/', $Q, 2);
		$I = Sys::$Modules[$Ie[0]] -> splitQuery($Q);
		if ($I[0]["Type"] == "Child" && sizeof($I) > 1)
			$Q = $I[sizeof($I) - 1]["Module"] . "/" . $I[sizeof($I) - 2]["DataSource"] . "/" . $I[sizeof($I) - 2]["Value"];
		else
			$Q = $I[sizeof($I) - 1]["Module"] . "/" . $I[sizeof($I) - 1]["DataSource"] . "/" . $I[sizeof($I) - 1]["Value"];
		$ExplQ = explode("/", $Q);
		if (sizeof($ExplQ) < 3)
			return false;
		$NbQ = sizeof($ExplQ) - 1;
		$this -> addFkey($ExplQ[$NbQ - 2], $ExplQ[$NbQ - 1], $ExplQ[$NbQ], 0,$SpeFKey);
	}

	/**
	 * resetParents
	 * delete all parent link from an object Type
	 * @param String Object Type name
	 */
	public function resetParents($Class,$SpeFKey = "") {
		if (!empty($this->Id)){
			if (!$SpeFKey&&preg_match_all("#.*?\.(.*)#", $Class,$t)){
				$SpeFKey = $t[sizeof($t)-1][0];
			}
			$Parents = $this -> getParents($Class);
			if (!empty($Parents))
				for ($i = 0; $i < sizeof($Parents); $i++) {
					$this -> addFKey($Parents[$i] -> Module, $Class, $Parents[$i] -> Id, 0, $SpeFKey);
				}
		}
	}

	/**
	 * addChild
	 * add a child link
	 * @param String Object Type name
	 * @param Int Id of the parent object
	 */
	public function addChild($Type, $Id) {
		//Associe un enfant existant Ã  l'objet courant
		$Enfant = new genericClass($this -> Module);
		$Enfant -> initFromId($Id, $Type);
		$Enfant -> addFkey($Enfant -> Module, $this -> ObjectType, $this -> Id);
		$Enfant -> Save();
	}

	/**
	 * delChild
	 * add a child link
	 * @param String Object Type name
	 * @param Int Id of the parent object
	 */
	public function delChild($Type, $Id) {
		//Associe un enfant existant Ã  l'objet courant
		$Enfant = new genericClass($this -> Module);
		$Enfant -> initFromId($Id, $Type);
		$Enfant -> addFkey($this -> Module, $this -> ObjectType, $this -> Id, 0);
		$Enfant -> Save();
	}

	/**
	 * resetChilds
	 * delete all childs link from an object Type
	 * @param String Object Type name
	 */
	public function resetChilds($Class) {
		$Childs = $this -> getChilds($Class);
		if (isset($this -> Childs))
			unset($this -> Childs);
		if (!empty($Childs))
			for ($i = 0; $i < sizeof($Childs); $i++) {
				$Childs[$i] -> addFKey($this -> Module, $this -> ObjectType, $this -> Id, 0);
				$Childs[$i] -> Save();
			}
	}

	/**
	 * setRights
	 * Define rights of this object
	 * @param String Name of the function to tigger
	 * @param
	 */
	public function ModifierDroits($Recursif = 0, $Pid = "", $Gid = "", $Umod = "", $Gmod = "", $Omod = "") {
		return $this -> setRights($Recursif, $Pid, $Gid, $Umod, $Gmod, $Omod);
	}

	public function setRights($Recursif = 0, $Pid = "", $Gid = "", $Umod = "", $Gmod = "", $Omod = "") {
		$NumObj = Sys::$Modules[$this -> Module] -> Db -> findByTitle($this -> ObjectType);
		$Flag = Sys::$Modules[$this -> Module] -> Db -> ObjectClass[$NumObj] -> ChangeRights($this -> Id, $Pid, $Gid, $Umod, $Gmod, $Omod);
	}

    //setter php
    public function __set($Prop, $newValue){
	    $this->Set($Prop, $newValue);
    }
    //getter php
    public function __get($Prop){
        //klog::l($Prop);
        return $this->Get($Prop);
    }

    /**
	 * setRights
	 * Define rights of this object
	 * @param String Name of the property to define
	 * @param Value of the property to define
	 */
	public function Set($Prop, $newValue) {

		if (empty($Prop)) return;
		$this -> launchTriggers(__FUNCTION__);
		$Props = $this -> Proprietes(false, true);
		for ($i = 0; $i < sizeof($Props); $i++) {
			if ($Props[$i]["Nom"] == $Prop) {
				switch ($Props[$i]["Type"]) {
					case "password" :
						if ($newValue != "*******")
							$newValue = trim($newValue);
						else
							return false;
						break;
					case "text" :
						break;
                    /*case "date" :
                            $newValue = intval($newValue);
                        break;*/
					default :
						if (is_string($newValue))
							$newValue = trim($newValue);
				}
			}
		}
		if (is_string($newValue))
			$newValue = trim($newValue);
		$this -> {$Prop} = $newValue;
		return true;
	}

	//----------------------------------------------//
	//	PRIVATE FUNCTIONS			//
	//----------------------------------------------//
	/**
	 * addFkey
	 * delete all parent link from an object Type
	 * @param String Object Type name
	 *	ACTION
	 *	2 AJOUT
	 *	1 MODIFICATION
	 *	0 SUPPRESSION
	 */
	public function addFkey($Module, $Class, $Nid, $Action = 2, $SpeFKey = "") {
		//Ajoute une nvelle clef etrangere
		if (empty($Nid))
			return false;
		if ($Class == $this -> Module)
			$Class = $this -> ObjectType;
		$Class = explode(":",$Class);
		//extraction de la vue
		$Class = $Class[0];
		$Class = explode(".",$Class);
		//extraction de la vue
		if (isset($tmp[1]))$SpeFKey = $Class[1];
		$Class = $Class[0];
		$this -> Parents[] = Array("Module" => $Module, "Titre" => $Class, "Id" => $Nid, "Action" => $Action, "Fkey" => (empty($SpeFKey)) ? $Class : $SpeFKey);
	}

	/**
	 * getParentsFromType
	 * Return an array of temporary parents in a specified type
	 * @param String Name of parent objectclass
	 * @return Array of FKey
	 */
	private function getParentsFromType($Type) {
		$Result = Array();
		if (isset($this -> Parents) && is_array($this -> Parents))
			foreach ($this->Parents as $P) {
				if (is_array($P))
					if ($P["Titre"] == $Type)
						$Result[] = $P;
				if (is_object($P))
					if ($P -> Titre == $Type)
						$Result[] = $P;
			}
		return $Result;
	}

	//----------------------------------------------//
	//		EVENEMENTS			//
	//----------------------------------------------//
	/**
	 * launchTriggers
	 * Trigger some function on events
	 * @param String Name of the function to tigger
	 * @param
	 */
	private function launchTriggers($function_name, $param = "") {
		if (!isset($this -> Module) || !isset($this -> ObjectType) || ($this -> Module == "Systeme" && $this -> ObjectType == "Event"))
			return;
		//extraction de l'objectclass
		$ObjClass = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		/*
		 Parametres : Le nom de la fonction appelante, un parametre si necessaire
		 Resultat : true si tout s'est bien passe, false sinon
		 Declenchement des evenements associes dans Modules/Module.Name/Module.Name.Actions
		 */
		$this->Triggers = Sys::$Modules[$this -> Module] ->getTriggers();
		//Copie du tableau des triggers
		$KnownEvents = Array();
		for ($i = 0; $i < count($this -> Triggers); $i++) {
			if (is_array($this -> Triggers[$i] -> Functions))
				foreach ($this->Triggers[$i]->Functions as $K => $F) {
					$KnownEvents[$this -> Triggers[$i] -> Functions[$K] -> Event][] = $this -> Triggers[$i] -> Functions[$K];
				}
		}
		$v = Array();
		switch(strtolower($function_name)) {
			case "initfromarray" :
				if (in_array("Load", array_keys($KnownEvents)))
					$v = array_merge($v, $KnownEvents["Load"]);
				break;
			case "get" :
				//TODO : Mode de recherche d'un seul champ
				if (in_array("Get", array_keys($KnownEvents)))
					$v = array_merge($v, $KnownEvents["Get"]);
				break;
			case "set" :
				//TODO : Mode de modification d'un seul champ
				if (in_array("Load", array_keys($KnownEvents)))
					$v = array_merge($v, $KnownEvents["Set"]);
				break;
			case "delete" :
				//declenche action
				if (in_array("Delete", array_keys($KnownEvents))) {
					$v = array_merge($v, $KnownEvents["Delete"]);
				}
				if ($ObjClass -> logEvent) {
					//enregistrement de l'evenement
					$e = genericClass::createInstance("Systeme", Array("ObjectType" => "Event", "Module" => "Systeme", "Titre" => "DELETE: " . $this -> Module . "/" . $this -> getDescription() . " " . $this -> getFirstSearchOrder(), "Data" => serialize($this), "EventType" => "Delete", "EventModule" => $this -> Module, "EventObjectClass" => $this -> ObjectType, "EventId" => $this -> Id));
					$e -> Save();
				}
				break;
			case "save" :
				//NEW
				if (isset($this -> tmsCreate) && isset($this -> tmsEdit) && $this -> tmsCreate == $this -> tmsEdit) {
					//declenche action
					if (in_array("New", array_keys($KnownEvents)))
						$v = array_merge($v, $KnownEvents["New"]);
					//enregistrement de l'evenement
					if ($ObjClass -> logEvent) {
						$e = genericClass::createInstance("Systeme", Array("ObjectType" => "Event", "Module" => "Systeme", "Titre" => "NEW: " . $this -> Module . "/" . $this -> getDescription() . " " . $this -> getFirstSearchOrder(), "EventType" => "Create", "EventModule" => $this -> Module, "EventObjectClass" => $this -> ObjectType, "Data" => serialize($this), "EventId" => $this -> Id));
						$e -> Save();
					}
				} else {
					//declenche action
					if (in_array("Save", array_keys($KnownEvents)))
						$v = array_merge($v, $KnownEvents["Save"]);
					if ($ObjClass -> logEvent) {
						//enregistrement de l'evenement
						$e = genericClass::createInstance("Systeme", Array("ObjectType" => "Event", "Module" => "Systeme", "Titre" => "EDIT: " . $this -> Module . "/" . $this -> getDescription() . " " . $this -> getFirstSearchOrder(), "Data" => serialize($this), "EventType" => "Edit", "EventModule" => $this -> Module, "EventObjectClass" => $this -> ObjectType, "EventId" => $this -> Id));
						$e -> Save();
					}
				}
				break;
		}
		for ($i = 0; $i < sizeof($v); $i++) {
			$v[$i] -> Execute($this);
		}
	}

	//----------------------------------------------//
	//		VERIFICATION			//
	//----------------------------------------------//
	/**
	 * Verify
	 * Verify the object integrity
	 */
	public function Verify() {
		$error = 1;
		$this -> Error = array();
		//Lancement de la verification des prorpietes
		$Props = $this -> Proprietes(false, true);
		if (is_array($Props))
			foreach ($Props as $p) {
				//Verification de la valeur
				if (!$this -> Check($p, "", "Conformity")) {
					$error = 0;
				}
			}
		//Verification des cardinalites.
        $fkeys = $this->getParentElements();
        foreach ($fkeys as $fkey){
            if($fkey['card']=='short'&&$fkey['obligatoire']) {
                if (empty($this -> {$fkey['name']})){
                    //on vérifie aussi dans le tableau des linjs temporaires
                    $found = false;
                    foreach ($this->Parents as $p){
                        if ($p["Module"]==$fkey["objectModule"]&&$p["Titre"]==$fkey["objectName"])$found=true;
                    }
                    if (!$found) {
                        //erreur
                        $this->AddError(array("Message" => "__LE_CHAMP__ " . (($fkey["description"] != "") ? $fkey["description"] : $fkey["name"]) . " __EST_OBLIGATOIRE__.", "Prop" => $fkey["name"]));
                        $error = 0;
                    }
                }
            }
        }
		return $error;
	}

	/**
	 * Check
	 * Check property integrity
	 * @param String Name of the property to check
	 * @param Value of the property to check
	 * @param String Type of the property to check
	 */
	public function Check($Nom, $Value = "", $Type = "") {
		//Verifie la valeur dun champ (ex: mot de passe)
		if (is_string($Nom)) {
			$Props = $this -> Proprietes(false, true);
			for ($i = 0; $i < sizeof($Props); $i++) {
				if ($Props[$i]["Nom"] == $Nom) {
					$Prop = $Props[$i];
				}
			}
		} elseif (is_array($Nom)) {
			$Prop = $Nom;
		}
		$error = 1;
		
		//Verification en fonction du type
		switch ($Prop["Type"]) {
			case "password" :
				if ($Type == "Conformity") {
					/*if ($this -> $Prop["Titre"] == "") {
						$error = 0;
					}else $this -> AddError(array("Message" => "__LA_VALEUR_DU_CHAMP__ " . $Prop["Titre"] . " __ALREADY_EXISTS__", "Prop" => $Prop["Titre"]));*/
				} else {
					if ($Value != "" && $this -> {$Prop["Titre"]} != "") {
						if (md5($Value) != $this -> {$Prop["Titre"]}) {
							$e["Message"] = "__LA_VALEUR_DU_CHAMP__ " . (($Prop["description"] != "") ? $Prop["description"] : $Prop["Titre"]) . " __INVALID__";
							$e["Prop"] = $Prop["Titre"];
							$this -> AddError($e);
							$error = 0;
						}
					} else {
						$error = 0;
					}
				}
				break;
			case "mail" :
				if (! Utils::isMail($this -> {$Prop["Titre"]}) && $this -> {$Prop["Titre"]} != "") {
					$e["Message"] = "__LA_VALEUR_DU_CHAMP__ " . (($Prop["description"] != "") ? $Prop["description"] : $Prop["Titre"]) . " __INVALID__";
					$e["Prop"] = $Prop["Titre"];
					$this -> AddError($e);
					$error = 0;
				}
				break;
			case "date" :
				if ((!Utils::isDate($this ->{$Prop["Titre"]}) && strlen($this -> {$Prop["Titre"]}) > 1 && !is_numeric($this -> {$Prop["Titre"]})) || (is_numeric($this -> {$Prop["Titre"]}) && strlen($this -> {$Prop["Titre"]}) < 1)) {
					$e["Message"] = "__LA_VALEUR_DU_CHAMP__ " . (($Prop["description"] != "") ? $Prop["description"] : $Prop["Titre"]) . " __FORMAT_DATE_INVALID__";
					$e["Prop"] = $Prop["Titre"];
					$this -> AddError($e);
					$error = 0;
				}
				break;
			case "int" :
				/*if (!intval($this -> $Prop["Titre"])>0&&) {
					$e["Message"] = "__LA_VALEUR_DU_CHAMP__ " . (($Prop["description"] != "") ? $Prop["description"] : $Prop["Titre"]) . " __NOT_INT__ ";
					$e["Prop"] = $Prop["Titre"];
					$this -> AddError($e);
					$error = 0;
				}*/
				break;
			case "string" :
				if (is_string($this -> {$Prop["Titre"]}) && $this -> {$Prop["Titre"]} != "") {
					$e["Message"] = "__LA_VALEUR_DU_CHAMP__ " . (($Prop["description"] != "") ? $Prop["description"] : $Prop["Titre"]) . " __INVALID_CHAR__";
					$e["Prop"] = $Prop["Titre"];
					$this -> AddError($e);
					$error = 0;
				}
				break;
		}
		//Verification en fonction des attributs
		if (isset($Prop["obligatoire"]) && $Prop["obligatoire"]) {
			if ((empty($this -> {$Prop["Titre"]})||$this -> {$Prop["Titre"]}=="0") && $Prop["Type"] != "file") {
				$e["Message"] = "__LE_CHAMP__ " . (($Prop["description"] != "") ? $Prop["description"] : $Prop["Titre"]) . " __EST_OBLIGATOIRE__.";
				$e["Prop"] = $Prop["Titre"];
				$this -> AddError($e);
				$error = 0;
			} elseif ($Prop["Type"] == "file") {
				if (!is_array($_FILES["Form_" . $Prop["Titre"] . "_Upload"]) && $this -> {$Prop["Titre"]} == "") {
					$e["Message"] = "__LE_CHAMP__ " . (($Prop["description"] != "") ? $Prop["description"] : $Prop["Titre"]) . " __EST_OBLIGATOIRE__.";
					$e["Prop"] = $Prop["Titre"];
					$this -> AddError($e);
					$error = 0;
				}
			}
		}
		if (isset($Prop["unique"]) && $Prop["unique"] && (!isset($this -> Id) || $this -> Id == "")) {
			$Res = Sys::getCount($this -> Module,  $this -> ObjectType . "/" . $Prop["Titre"] . "=" . $this -> {$Prop["Titre"]});
			if ($Res) {
				$this -> AddError(array("Message" => "__LA_VALEUR_DU_CHAMP__ " . $Prop["Titre"] . " __ALREADY_EXISTS__", "Prop" => $Prop["Titre"]));
				$error = 0;
			}
		}
		if (isset($Prop["unique"]) && $Prop["unique"] && (isset($this -> Id) && $this -> Id != "")) {
            $Res = Sys::getCount($this -> Module,  $this -> ObjectType . "/" . $Prop["Titre"] . "=" . $this -> {$Prop["Titre"]}."&Id!=". $this -> Id);
			if ($Res) {
                $this -> AddError(array("Message" => "__LA_VALEUR_DU_CHAMP__ " . $Prop["Titre"] . " __ALREADY_EXISTS__", "Prop" => $Prop["Titre"]));
                $error = 0;
			}
		}
		return $error;
	}

	//----------------------------------------------//
	//		UTILITAIRES			//
	//----------------------------------------------//
	/**
	 * normalizeFileName
	 * Normalize the filename in order to access to browse it on the web
	 * @param String File Name
	 * @return String New name of the file
	 */
	protected function normalizeFileName($chaine) {
		//On enleve les accents
		$chaine = strtr($chaine, "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ", "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn");
		//On remplace tous ce qui n est pas une lettre ou un chiffre par un _
		$chaine = preg_replace('/([^.a-z0-9]+)/i', '_', $chaine);
		$chaine = strtolower($chaine);
		//         $chaine = preg_replace('#(.+)\.([a-z]{3})#i','$1-'.time().'.$2',$chaine);
		return $chaine;
	}

	/**
	 * executeQuery
	 * Execute a query and return the result
	 * @param String Query
	 * @return [Array[String=>Value]]
	 */
	private function executeQuery($Query) {
		$Results = Sys::$Modules[$this -> Module] -> callData($Query, false, 0, 10000);
		return $Results;
	}

	//----------------------------------------------//
	//		ENREGISTREMENT			//
	//----------------------------------------------//
	/**
	 * fileUpload
	 * Manage the upload of a file when saving an object
	 * @param Array Property
	 * @param String Value of this property
	 * @return Boolean Success or not
	 */
	private function fileUpload($Prop, $value = "") {
		if (is_array($value))$FileArray = $value;
		elseif (!empty($_FILES[$value]['name']))
			$FileArray = $_FILES[$value];
		elseif (!empty($_FILES['Form_' . $Prop["Nom"]]['name']))
			$FileArray = $_FILES['Form_' . $Prop["Nom"]];
		elseif (!empty($_FILES['Form_' . $Prop["Nom"]]['name']))
			$FileArray = $_FILES['Form_' . $Prop["Nom"]];
		elseif (!empty($_FILES['Form_' . $Prop["Nom"] . '_Upload']['name']))
			$FileArray = $_FILES['Form_' . $Prop["Nom"] . '_Upload'];
		elseif (!empty($_FILES['Filedata']))
			$FileArray = $_FILES["Filedata"];
		elseif (empty($FileArray))
			return false;
		$fichier = basename($FileArray['name']);
		//On definit l emplacement du fichier upload par defaut
		$Usr = ($this -> Get("Fake_User_Upload")) ? $this -> Get("Fake_User_Upload") : Sys::$User -> Id;
		if ($this -> Module == "Explorateur" && $this -> ObjectType == "Insert") {
			$dossier = "Home/$Usr/" . $this -> get("Destination") . "/";
		} else {
			$dossier = "Home/$Usr/" . $this -> Module . "/" . $this -> ObjectType . "/";
		}
		if (!file_exists($dossier))
			fileDriver::mk_dir($dossier);
		//On verifie si la taille ne depasse pas le seuil autorise
		$taille_maxi = 1000000000;
		$taille = @filesize($FileArray['tmp_name']);
		$extension = strrchr($fichier, '.');

		if (isset($this->Type)&&$this -> Type == "Media") {
			$extensions = array('.png', '.gif', '.jpg', '.jpeg', '.flv', '.bmp', '.tiff');
			//Ensuite on teste
			if (!in_array(strtolower($extension), $extensions)) {
				return false;
			}
		}
		$fichier = $this -> normalizeFileName($fichier);
		$file_name = basename($fichier, $extension);
		if ($taille > $taille_maxi) {
			$GLOBALS["Systeme"] -> Error -> sendWarningMsg(5);
		}

		$d = 0;
		if (!empty($Vars[$Prop["Nom"]])) {
			//if(!
			move_uploaded_file($FileArray['tmp_name'], $Vars[$Prop["Nom"]]);
			//) $GLOBALS["Systeme"]->Error->sendWarningMsg(4);
		} else {
			if (file_exists($dossier . $file_name . '_' . $d . $extension)) {
				while (file_exists($dossier . $file_name . '_' . $d . $extension))
					$d++;
				$file_name = $file_name . '_' . $d;
			}
			if (!move_uploaded_file($FileArray['tmp_name'], $dossier . $file_name . $d . $extension)) {
				//$GLOBALS["Systeme"]->Error->sendWarningMsg(41);
                //echo "IS DIR ".$dossier . $file_name . $d . $extension." => ".file_exists($dossier . $file_name . $d . $extension);
                //print_r($Vars[$Prop["Nom"]]);
			}
			$N = $Prop["Nom"];
			$this -> {$N} = $dossier . $file_name . $d . $extension;
			return true;
		}
		unset($_FILES['Form_' . $Prop["Nom"] . '_Upload']);
		unset($_FILES['Form_' . $Prop["Nom"]]);
		return false;
	}

	/**
	 * Clone
	 * Clone an object
	 * @param Boolean noreset prevent from removing sys information
	 * @return Object
	 */
	public function getClone($noreset=false) {
		$O = parent::getClone();
		if (!$noreset){
			unset($O -> Id);
			unset($O -> tmsCreate);
			unset($O -> tmsEdit);
			unset($O -> uid);
			unset($O -> gid);
			unset($O -> userEdit);
			unset($O -> userCreate);
			unset($O -> groupCreate);
			unset($O -> groupEdit);
		}
		return $O;
	}

	/**
	 * SaveRemote
	 */
	public function saveRemote($args, $multistatus = false) {
		$type = $this -> Id ? 'edit' : 'add';
		$status = Array();
		$arg = $args -> args[0];
		if (isset($arg) && !empty($arg))
			foreach ($arg as $k => $v) {
				$this -> Set($k, $v);
			}
		$data = $args -> data;
		if ($data -> parentClass && $data -> createParent) {
			$this -> addParent($data -> module . '/' . $data -> parentClass . '/' . $data -> parentId);
		}
		$parents = array();
		$P = $this -> getParentAssociations();
		if (is_array($P) && !empty($P)) {
			foreach ($P as $pa) {
				$objet = $pa->getParentObjectClass();
				$nomObjet = $objet->titre;
				$nomAssociation = $pa->titre;
				$nomComplet = $nomObjet.'.'.$pa->titre;
				$vraiNom = "";
				if (isset($arg -> {$nomObjet})) $vraiNom = $nomObjet;
				if (isset($arg -> {$nomAssociation})) $vraiNom = $nomAssociation;
				if (isset($arg -> {$nomComplet})) $vraiNom = $nomComplet;
				if ($vraiNom) {
					if (!is_array($arg -> {$vraiNom}))
						$arg -> {$vraiNom} = Array($arg -> {$vraiNom});
					//remise à zero des liaisons de ce type
					$this -> resetParents($nomComplet);
					foreach ($arg->{$vraiNom} as $par) {
						$this -> AddParent($this -> Module . "/" . $nomComplet . "/" . $par);
						//$GLOBALS["Systeme"] -> Log -> log("ADD PARENT " . $this -> Module . "/" . $nomComplet . "/" . $par);
					}
				}
			}
		}
        $res = '';
		if (!$this -> Verify()) {
			$status[] = Array($type, 0, $this -> Id, $this -> Module, $this -> ObjectType, null, null, $this -> Error, $res);
			return WebService::WSStatusMulti($status);
		}
		$this->cleanParentTable();
		$par = $this->Parents;
		$status = array_merge($this->Save(),$status);
		// check for default status
		$default = true;
		if(count($status)) {
			foreach($status as &$st) {
				if(! $st[1]) {
					if(is_object($GLOBALS['Systeme']->Db[0])) 
						$GLOBALS['Systeme']->Db[0]->query("ROLLBACK");
					return WebService::WSStatusMulti($status);
				}
				if($st[3] == $this->Module && $st[4] == $this->ObjectType && $st[2] == $this->Id) {
					$default = false;
					$st[9] = $par;
				}
			}
		}

		if (!$this -> Verify())
			$status[] = Array($type, 0, $this -> Id, $this -> Module, $this -> ObjectType, '', '', $this -> Error, $res);
		elseif($default)
			$status[] = Array($type, 1, $this -> Id, $this -> Module, $this -> ObjectType, '', '', $this -> Success, $res, $par);

		return WebService::WSStatusMulti($status);
	}

	

	private function cleanParentTable() {
		$n = count($this->Parents);
		$i = 0;
		while($i < $n) {
			$t = $this->Parents[$i];
			if($t['Action'] === 0) {
				$t['Action'] = 2;
				$p = array_search($t, $this->Parents);
				if($p !== false) {
					array_splice($this->Parents, $p, 1);
					array_splice($this->Parents, $i, 1);
					$n -= 2;
				}
				else $i++;
			}
			else $i++;
		}
	}
	
	

	/**
	 * changeRights
	 * @param recursively
	 */
	public function changeRights($arg) {
		$type = 'edit';
		$GLOBALS["Systeme"] -> Log -> log("GENERIC CLASS ", $arg);
		$status = Array();
		//definition des proprietaires
		$this -> uid = $arg -> sys_uid[0];
		$this -> gid = $arg -> sys_gid[0];
		//transformation des droits
		$this -> umod = ($arg -> sys_ur * 2) + ($arg -> sys_uw * 4) + 1;
		$this -> gmod = ($arg -> sys_gr * 2) + ($arg -> sys_gw * 4) + 1;
		$this -> omod = ($arg -> sys_or * 2) + ($arg -> sys_ow * 4) + 1;
		$res = $this -> Save();
		if ($arg -> recursive) {
			//detection des types de parent
			$ch = $this -> getChildTypes();
			if (is_array($ch))
				foreach ($ch as $c) {
					$chs = $this -> getChilds($c["Titre"]);
					if (is_array($chs))
						foreach ($chs as $cs)
							$cs -> changeRights($arg);
				}
		}
		if (!sizeof($status)) {
			if (!$this -> Verify())
				$status[] = Array($type, 0, $this -> Id, $this -> Module, $this -> ObjectType, null, null, $this -> Error, $res);
			else
				$status[] = Array($type, 1, $this -> Id, $this -> Module, $this -> ObjectType, null, null, $this -> Success, $res);
		}

		return WebService::WSStatusMulti($status);
	}
    /**
     * fileBase64
     * Transform
     */
    private function fileBase64($Prop,$Val) {
        //check base64
        if (empty($Val)||base64_encode(base64_decode($Val, true)) !== $Val) {
            return false;
        }

        //destination
        $dossier = "Home/".Sys::$User->Id."/" . $this -> Module . "/" . $this -> ObjectType . "/";
        if (!file_exists($dossier))
            fileDriver::mk_dir($dossier);

        //nom et extension
        $extension = '.jpg';
        $file_name = $Prop['Nom'].'-'.Sys::$User->Id;

        $d = 0;
        if (file_exists($dossier . $file_name . '_' . $d . $extension)) {
            while (file_exists($dossier . $file_name . '_' . $d . $extension))
                $d++;
        }
        $path = $dossier . $file_name . '_' . $d . $extension;
        file_put_contents($path,base64_decode($Val));

        //on enregistre le lien
        $N = $Prop["Nom"];
        $this -> {$N} = $path;

        return $file_name;
    }
	/**
	 * Save
	 * Save an object
	 * @return Boolean Success or not
	 */
	public function Save() {
		//si l'element possede le generateUrl = true alors on génère ses mots clefs
		$obj = $this->getObjectClass();
		if ($obj->browseable){
			$this->SaveHeaderVars();
		}
		//Sauvegarde l'enregistrement en cours dans la base de donnees
		//On traite les dictionnaires automatiques
		foreach ($GLOBALS["Systeme"]->Language as $Lang => $Pref) {
			foreach ($this->Proprietes($Pref) as $Prop) {
				switch ($Prop["Type"]) {
					case "image":
					case "file" :
						if (!$this -> fileUpload($Prop, $Prop["Valeur"]))
                            $this->fileBase64($Prop,$Prop["Valeur"]);
						break;
					case "autodico" :
						$Nom = $Prop["Nom"];
						$Valeur = $this -> {$Nom};
						$Query = $this -> Module . "/" . $Prop["Target"];
						$Tab = Sys::$Modules[$this -> Module] -> callData($Query);
						if (!is_array($Tab))
							$Id = 1;
						else {
							$maybeId = 0;
							foreach ($Tab as $Dico) {
								if ($Dico["Id"] > $maybeId)
									$maybeId = $Dico["Id"];
								if ($Dico["Type"] == $Valeur) {
									$Id = $Dico["Id"];
									$Test = true;
								}
							}
						}
						if ($Test == false) {
							if (empty($Id))
								$Id = $maybeId + 1;
							$Generic = new genericClass($this -> Module);
							$Generic -> initFromType($Prop["Target"]);
							$Generic -> Id = $Id;
							$Generic -> Type = $Valeur;
							$Generic -> Save();
						}
						$this -> {$Nom} = $Id;
						break;
					case "float" :
						$Nom = $Prop["Nom"];
						if ($this -> {$Nom} == "0")
							$this -> {$Nom} = "0.0";
						break;
					case "datetime" :
					case "date" :
						$Nom = $Prop["Nom"];
						if(isset($this->{$Nom})&&! is_null($this->{$Nom})) {
							if (preg_match("#^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})\ ([0-9]{2})\:([0-9]{2})\:([0-9]{2})$#", $this -> {$Nom}, $out)) {
								$this -> {$Nom} = mktime($out[4], $out[5], $out[6], $out[2], $out[1], $out[3]);
							}
							if (preg_match("#^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})\ ([0-9]{2})\:([0-9]{2})$#", $this -> {$Nom}, $out)) {
								$this -> {$Nom} = mktime($out[4], $out[5], 0, $out[2], $out[1], $out[3]);
							}
							if (preg_match("#^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$#", $this ->{$Nom}, $out)) {
								$this -> {$Nom} = mktime(0, 0, 0, $out[2], $out[1], $out[3]);
							}
						}
						break;
				}
			}
		}
		//if (sizeof($this->Error))return false;
		//Si il n a pas ï¿œtï¿œ dï¿œfini de paren
		//print_r($this->Parents);
		//print_r($this->typesParent());
		/*if (is_array($this -> typesParent()))
			foreach ($this->typesParent() as $Par) {
				//Recherche de la clef etrangere parmis les parents
				$Parents = $this -> getParentsFromType($Par["Titre"]);
				if (is_array($Parents))
					foreach ($Parents as $K => $Fkey) {
						if ($Fkey["Action"] == "")
							$Action = "2";
						else
							$Action = $Fkey["Action"];
						if ($Par["Target"] != "Id") {
							//Alors il faut recuperer la bonne valeur
							$Enfant = genericClass::createInstance($this -> Module, $Fkey["Titre"]);
							$Enfant -> initFromId($Fkey["Id"], $Fkey["Titre"]);
							$Fkey["Id"] = $Enfant -> get($Par["Target"]);
						}
						$NewParent[] = $Fkey;
					}
			}
		if (isset($NewParent))
			$this -> Parents = $NewParent;*/
		//On met a jour les heritages
		if (isset($this -> Heritages) && sizeof($this -> Heritages)) {
			for ($i = 0; $i < sizeof($this -> Heritages); $i++) {
				$Name = $this -> Heritages[$i]["Nom"];
				$this -> Heritages[$i]["Valeur"] = $this -> {$Name};
				$this -> Heritages[$i]["Value"] = $this -> {$Name};
			}
		}
		$obj = $this->getObjectClass();
		$Results = Sys::$Modules[$this -> Module] -> Db -> Query($this);
		if (!is_array($Results)&&is_string($Results)&&!empty($Results)){
		    $this->addError(Array("Message"=>$Results));
		    return false;
        }
		$this -> Parents = array();
		$this -> initFromArray($Results);
		$this -> launchTriggers(__FUNCTION__);

		//si l'element possede le generateUrl = true alors on génère ses mots clefs
		if ($obj->browseable){
			if (isset($this->Display)&&$this->Display)
				$this->SaveKeywords();
			else
				$this->deletePages();
		}
		return true;
	}

	//----------------------------------------------//
	//		ERREURS				//
	//----------------------------------------------//
	/**
	 * addError
	 * Add an error in the error array
	 * @param Array Error
	 */
	public function addError($err) {
		$this -> Error[] = $err;
	}

	/**
	 * getErrors
	 * Add an error in the error array
	 * @param Array Error
	 */
	//public function Error() {return $this->getErrors();}
	public function getErrors() {
		return $this -> Error;
	}

	/**
	 * resetErrors
	 * reset All errors
	 */
	public function resetErrors() {
		$this -> Error = Array();
		//		unset($this->Error);
	}

	/**
	 * addSuccess
	 * Add an success message in the success array
	 * @param Array Success
	 */
	public function addSuccess($succ) {
		$this -> Success[] = $succ;
	}

	/**
	 * getSuccess
	 * get all success messages
	 * @param Array Success
	 */
	//public function Success() {return $this->getSuccess();}
	public function getSuccess() {
		return $this -> Success;
	}

	/**
	 * resetSuccess
	 * reset All success
	 */
	public function resetSuccess() {
		//unset($this->Success);
		$this -> Success = Array();
	}

	/**
	 * addWarning
	 * Add an warning message in the success array
	 * @param Array Warning
	 */
	public function addWarning($warn) {
		$this -> Warning[] = $warn;
	}

	/**
	 * getWarning
	 * get all warning messages
	 * @param Array Warning
	 */
	//public function Warning() {return $this->getWarning();}
	public function getWarning() {
		return $this -> Warning;
	}

	/**
	 * resetWarning
	 * reset All warning
	 */
	public function resetWarning() {
		//unset($this->Warning);
		$this -> Warning = Array();
	}
	/**
	 * TEMPLATES
	 */
	public function getTemplates() {
		//Recuperation de l'objectclass
		return $GLOBALS['Systeme'] -> getTemplates();
	}

	/**
	 * PLUGINS
	 */
	public function getPlugins() {
		//Recuperation de l'objectclass
		return Plugin::getPlugins($this -> Module, $this -> ObjectType);
	}

	/**
	 * getDescription
	 * Renvoie la Description de l'objetclass
	 */
	public function getDescription() {
		$obj = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		return !empty($obj -> Description) ? $obj -> Description : $obj -> titre;
	}

	/**
	 *getViewFields
	 * Renvoi la liste des champs, la vue y compris
	 */
	public function getViewFields() {
		$obj = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		$view = $obj -> defaultView;
		//if (is_array($view->sqlTables["Select"]))
	}
	/**
	 * setView
	 * Configure l'objet pour utiliser la vue specifiée
	 */
	 public function setView($name=""){
	 	$obj = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
	 	if ($name==""){
	 		if (is_object($obj->defaultView))
	 			$name = $obj->defaultView->titre;
			else return;
	 	}
	 	$this->_view = $name;
	 }
	 
	/**
	 * PGF
	 * getALerts
	 * recupere les alertes pour l'objet
	 */
	 public function getAlerts($lastAlert, $time) {
	 	return null;
	 }
	 
	/**
	 * PGF
	 * createAlerts
	 * génère les alertes dans Systeme/Alert
	 */
	 public function createAlerts($time) {
	 }


	 /**
	  * getFilters
	  * return filters from view if available
	  */
	  public function getFilters() {
		$obj = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		$view = $obj -> defaultView;
		if (is_object($view) && ! empty($view->Filters)){
			return Process::processingVars($view->Filters);
		}
	  }
	 /**
	  * getCustomFilters
	  * return custom filters from objectclass
	  */
	  public function getCustomFilters() {
		$obj = Sys::$Modules[$this -> Module] -> Db -> getObjectClass($this -> ObjectType);
		$filters = $obj->getCustomFilters();
		if (is_array($filters))foreach ($filters as $k=>$f){
			$f->filter = Process::processingVars($f->filter);
			$filters[$k] = $f;
		}
		return $filters;
	  }
	/**
	 * getSubMenu
	 * return the children objects of the same type
	 * @return Array( genericClass ) 
	 */
	 public function getSubMenus(){
	 	$out = Array();
		//local storage ( session cookie )
	 	if (isset($this->Menus)&&is_array($this->Menus)){
	 		$out = array_merge($this->Menus,$out);
	 	}else {
			//children menus
			$chds = $this->getChildren($this->ObjectType.'/Display=1');
		 	if (isset($chds)&&is_array($chds)){
		 		$out = array_merge($chds,$out);
		 	}
		}
		//normalisation des elements
		foreach ($out as $o){
			$o->Titre = $o->getFirstSearchOrder();
		}
		return $out;
	 } 
	 //______________________________________________________________________________________________
	 //										    REFERENCEMENT
	 /**
	  * SaveHeaderVars
	  * Enregistre les valeurs des champs de referencement si ils sont vides
	  */
	 function SaveHeaderVars() {
        $obj = $this->getObjectClass();
        $T=' - '.$obj->getDescription().' - ';
        $Props = $this->SearchOrder();
		 if (empty($this->TitleMeta)) {
			 if (is_array($Props)) foreach ($Props as $p) {
				 //Verification de la valeur
				 switch ($p["Type"]) {
					 case "titre":
					 case "varchar":
						 // Type text : on concatene
						 $T .= ' ' . $this->{$p["Titre"]};
						 break;
				 }
			 }
			 $this->TitleMeta = substr($T, 0, 150);

             //on force la mise à jour des pages
             $this->dirtyPage = true;
		 }
        $T='';
		 if (empty($this->DescriptionMeta)) {
			 $Props = $this->Proprietes();
			 if (is_array($Props)) foreach ($Props as $p) {
				 //Verification de la valeur
				 switch ($p["Type"]) {
					 case "text":
						 // Type text : on concatene
						 $T .= ' ' . $this->{$p["Titre"]};
						 break;
					 case "bbcode":
						 // Type text : on concatene
						 $T .= ' ' . strip_tags($this->{$p["Titre"]});
						 break;
				 }
			 }
			 $this->DescriptionMeta = substr(strip_tags($T), 0, 250);
             //on force la mise à jour des pages
             $this->dirtyPage = true;
		 }
        $T='';
		 if (empty($this->ImgMeta)) {
			 $Props = $this->Proprietes();
			 if (is_array($Props)) foreach ($Props as $p) {
				 //Verification de la valeur
				 switch ($p["Type"]) {
					 case "image":
						 // Type text : on concatene
						 if (isset($this->{$p["Titre"]})) {
							 $T = $this->{$p["Titre"]};
							 break 2;
						 }
						 break;
				 }
			 }
			 $this->ImgMeta = $T;
             //on force la mise à jour des pages
             $this->dirtyPage = true;
		 }
	 }
	 //______________________________________________________________________________________________
	 //											     TAGS
	 /**
	  * getPages
	  * Renvoie les pages associées à cet élément.
	  * @return Array
	  */
	 public function getPages($strict=false){
		$tls = Sys::getData('Systeme','Page/PageModule='.$this->Module.'&PageObject='.$this->ObjectType.'&PageId='.$this->Id);
		
		if(!Sys::$User->Admin && is_object(Site::getCurrentSite())){
			$site = Site::getCurrentSite();
			$siteId = $site->Id;
			$tls = array_filter($tls,function($a)use($siteId){
				return ($siteId == $a->SiteId);
			});
		}

		if (!sizeof($tls)&&$this->ObjectType!="Menu"&&!$strict){
			$tls = Array();
			//recherche des menus pointant vers cette donnée
			$menus = Sys::getMenus($this->Module.'/'.$this->ObjectType.'/'.$this->Id,true,true);
			//Si des menus pointent vers cette donnée alors on ajoute les mots sur ces pages
			foreach ($menus as $m){
				unset($m->Menus);
				//mise à jour a partir des menus
				$tls=array_merge($tls,$m->getPages());
			}

			if (!$this->isRecursiv()||(!Sys::getCount($this->Module,$this->ObjectType.'/'.$this->ObjectType.'/'.$this->Id.'/Display=1'))){
				//recherche des menus pouvant emmener à cette donnée
				$menus = Sys::getMenus($this->Module.'/'.$this->ObjectType,true,true);
				foreach ($menus as $m){
					unset($m->Menus);
					//récupération des pages
					$ps = $m->getPages();
					foreach ($ps as $po){
						//Pour chacune des pages trouvées on en ajoute une à la suite
						$pn = genericClass::createInstance('Systeme','Page');
						$pn->Url = $po->Url."/".$this->Url;
						$pn->FromUrl = $po->Url;
						$pn->LastMod = date('Y-m-d');
						$pn->PageModule = $this->Module;
						$pn->PageObject = $this->ObjectType;
						$pn->PageId = $this->Id;
						//récupération du site
						$s = $po->getParents('Site');
						if (isset($s[0]))
							$pn->addParent($s[0]);
						$pn->Save();
						$tls[] = $pn;
					}
				}
			}
			//Nous allons donc récupérer les éléments browseable parents afin de pouvoir générer les pages.
			//si pas de menu associé on met à jour tous les parents browseable
			$pars = $this->getParentTypes();
			$browseable = $this->getObjectClass()->browseable;
			foreach ($pars as $pa){
				if ($pa["browseable"]&&!$pa["stopPage"]){
					$pas = $this->getParents($pa["Titre"]);
					foreach ($pas as $p){
					    if (!$p->Display) continue;
						//récupération des pages
						$ps = $p->getPages();

						foreach ($ps as $po){
                            //suffixe
                            $suffixe = '';
                            if ($this->ObjectType!=$po->PageObject && $po->PageObject!='Menu')
                                $suffixe = $this->ObjectType."/";
                            elseif ($this->ObjectType!=$po->PageObject && $po->PageObject=='Menu') {
                                //il faut vérifier la cible du menu
                                $m = Sys::getOneData('Systeme','Menu/'.$po->PageId);
                                $i = Info::getInfos($m->Alias);
                                if ($i["TypeChild"]!=$this->ObjectType) {
                                    $suffixe = $this->ObjectType . "/";
                                }else $suffixe = '';
                            }else $suffixe = '';

                            //Pour chacune des pages trouvées on en ajoute une à la suite
							$pn = genericClass::createInstance('Systeme','Page');
							$pn->Url = $po->Url."/".$suffixe.$this->Url;
							$pn->FromUrl = $po->Url;
							$pn->LastMod = date('Y-m-d');
							$pn->PageModule = $this->Module;
							$pn->PageObject = $this->ObjectType;
							$pn->PageId = $this->Id;
							//récupération du site
							$s = $po->getParents('Site');
							if (isset($s[0]))
								$pn->addParent($s[0]);
							$pn->Save();
							$tls[] = $pn;
						}
					}
				}else if (!$pa["browseable"]&&$browseable){
					$pas = $this->getParents($pa["Titre"]);
					foreach ($pas as $p){
                        if (!$p->Display) continue;

                        //recherche des menus pouvant emmener à cette donnée
						$menus = Sys::getMenus($this->Module.'/'.$pa["Titre"].'/'.$p->Id,true,true);
						$suffixe = '/'.$this->ObjectType;
						if (!sizeof($menus)){
							$menus = Sys::getMenus($this->Module.'/'.$pa["Titre"].'/'.$p->Id.'/'.$this->ObjectType,true,true);
							$suffixe='';
						}

                        //Dans le cas ou l'objectclass du menu est la meme que celle en cours.
                        if ($this->ObjectType == $pa["Titre"] && $this->isRecursiv()) $suffixe='';

						foreach ($menus as $m){
							unset($m->Menus);
							//récupération des pages
							$ps = $m->getPages();
							foreach ($ps as $po){
								//Pour chacune des pages trouvées on en ajoute une à la suite
								$pn = genericClass::createInstance('Systeme','Page');
								$pn->Url = $po->Url.$suffixe."/".$this->Url;
								$pn->FromUrl = $po->Url;
								$pn->LastMod = date('Y-m-d');
								$pn->PageModule = $this->Module;
								$pn->PageObject = $this->ObjectType;
								$pn->PageId = $this->Id;
								//récupération du site
								$s = $po->getParents('Site');
								if (isset($s[0]))
									$pn->addParent($s[0]);
								$pn->Save();
								$tls[] = $pn;
							}
						}
					}
				}
			}
		}
		//Dans le cas des menus la génération est automatique à l'enregistrement.

		return array_values($tls);
	 }	 
	 /**
	  * Supprime les pages d'un élément
	  */
	 function deletePages() {
		//On recherche les pages
		$tls = $this->getPages(true);
		//mise à jour des pages
		for ($i=0;$i<sizeof($tls);$i++){
			$tls[$i]->Delete();
		}
	 }

	/**
	 * Enregistre les mots-clés pour cet élément
	 * @return	void
	 */
	function SaveKeywords($secondpass = false) {

		//On recherche les pages
		$tls = $this->getPages();

		//mise à jour des pages
		for ($i=0;$i<sizeof($tls);$i++){
			$tls[$i]->Title = $this->TitleMeta;
			$tls[$i]->Description = $this->DescriptionMeta;
			$tls[$i]->Keywords = $this->KeywordsMeta;
			$tls[$i]->Image = $this->ImgMeta;
			$url = $tls[$i]->Url;
			$last = explode('/',$url);
			if ($last[sizeof($last)-1]!=$this->Url  && $tls[$i]->PageObject!='Menu'){
				//mise à jour url
				$tls[$i]->Url = preg_replace('#'.$last[sizeof($last)-1].'$#',$this->Url,$url);
				//TODO requete recursive dans le cas ou on modifie une url contenant d'autres pages.
			}
		}

		if (Sys::getKeywordsProcessing()&&sizeof($tls)) {

			//generation des mots-clefs
			$Mcs = $this->genKeyWords();

			//generation des tags
			if (is_array($Mcs)) {
				foreach ($Mcs as $Mc => $Occ) {
					if ($Mc != " " && !empty($Mc)) {
						//On verifie d'abord si il n'existe pas dans la base des mots clefs en tant que canonique
						//$Tab2 = Sys::getData('Systeme','Tag/Canonic='.Utils::Canonic($Mc));
						//if(!sizeof($Tab2)) {
                            // Il n'existe pas, on le créé
                            $Mcf = genericClass::createInstance("Systeme", "Tag");
                            $Mcf->Set("Nom", $Mc);
                            $Mcf->Set("Canonic", Utils::Canonic($Mc));
                            $Mcf->Set("Poids", $Occ);
    						//$Mcf->Save();
						//}else $Mcf = $Tab2[0];
						//Affectation au taglink

                        $Mcf->Pages = array();
						for ($i = 0; $i < sizeof($tls); $i++){
							//$tls[$i]->addParent($Mcf);
                            $Mcf->Pages[] = $tls[$i]->Id;
                        }

                        if (isset(Sys::$keywords[$Mcf->Canonic])){
                            $Mcf->Pages = array_merge($Mcf->Pages,Sys::$keywords[$Mcf->Canonic]->Pages);
                            $Mcf->Poids += Sys::$keywords[$Mcf->Canonic]->Poids;
                        }
                        Sys::$keywords[$Mcf->Canonic] = $Mcf;

					}
				}
			}
		}
        for ($i = 0; $i < sizeof($tls); $i++){
            $tls[$i]->Save();
        }
	}
    /**
     * rip_tags
     * strip_tags replacement
     */
    public function rip_tags($string) {

        // ----- remove HTML TAGs -----
        $string = preg_replace ('/<[^>]*>/', ' ', $string);

        // ----- remove control characters -----
        $string = str_replace("\r", '', $string);    // --- replace with empty space
        $string = str_replace("\n", ' ', $string);   // --- replace with space
        $string = str_replace("\t", ' ', $string);   // --- replace with space

        // ----- remove multiple spaces -----
        $string = trim(preg_replace('/ {2,}/', ' ', $string));

        return $string;

    }
	/**
	* Génère la phrase significative de la donnée
	* @return String
	* */
	public function getSignificantSentence() {
		// Recensement des champs textuels
		$Props = $this->Proprietes();
		$T="";
		if (is_array($Props)) foreach ($Props as $p) {
			//Verification de la valeur
			switch ($p["Type"]) {
				case "titre":
				case "text":
				case "varchar":
				/*case "metat":
				case "metad":
				case "metak":*/
					// Type text : on concatene
					if (isset($p["SearchOrder"])&&intval($p["SearchOrder"])>0){
						for ($i=20; $i>=intval($p["SearchOrder"]); $i--){
							$T .= ' ' .trim( htmlspecialchars_decode($this->{$p["Titre"]}));
						}
						
					}elseif (isset($this->{$p["Titre"]}))
						$T .= ' ' .trim( htmlspecialchars_decode($this->{$p["Titre"]}));
				break;
				case "html":
				case "bbcode":
					// Type text : on concatene
					$T .= ' ' . trim($this->rip_tags($this->{$p["Titre"]}));
				break;
			}
		}
		$obj = $this->getObjectClass();
		if (sizeof($obj->tagObjects))foreach ($obj->tagObjects as $to){
			$ch = $this->getChildren($to);
			foreach ($ch as $c) $T.=' '.$c->getSignificantSentence();
		}
		return $T;
	}

	/**
	 * Génère les keywords à partir de tous les champs textuels
	 * @return	Tableau de mots clés
	 */
	private function genKeyWords() {
		// Inclusion de la classe
		include_once("Class/Utils/autokeyword.class.php");

		$T = $this->getSignificantSentence();
		//Extraction des mots clefs
		$params['content'] = $T; //page content
		//set the length of keywords you like
		$params['min_word_length'] = 4;  //minimum length of single words
		$params['min_word_occur'] = 1;  //minimum occur of single words
		
		$keyword = new autokeyword($params, "UTF-8");
		//EM-20150218 Probleme de génération de mots clefs
		$mcs = $keyword->get_keywords();
		if (is_array($mcs))foreach ($mcs as $Mc=>$occ){
			if ($Mc!=""){
				$Nb = false;//Sys::getCount("Systeme","TagBlackList/Titre=".$Mc);
				if (!$Nb) $Out[$Mc] = $occ;
			}
		}
		return $Out;
	}

	 /**
	  * getOrderField
	  */
	 public function getOrderField() {
		$O = $this->getObjectClass();
		$Champs = $O->getSpecialProp("order");
		return $Champs;
	 }
	 
	 public function exportCSV($cols,$module,$qry,$ord,$fld,$nam,$mode) {
		//type du fichier demandé dans l'url....
		//$type = $GLOBALS["Systeme"]->type;
		$n = count($cols);
		$cs = array();
		for($i = 0; $i < $n; $i++) $cs[] = explode('::',$cols[$i]);
		$rs = Sys::getData($module,$qry,0,10000,$ord,$fld);

		if($mode == 'xls') return $this->exportXLS($cs,$rs,$nam);
		if($mode == 'pdf') return $this->exportPDF($cs,$rs,$nam);

	 	$csv = '';
		$l = '';
		for($i = 0; $i < $n; $i++) {
			if($i) $l .= ';';
			$l .= '"'.($cs[$i][0] ? $cs[$i][0] : $cs[$i][1]).'"';
		}
		$csv .= $l ."\r\n";
		foreach($rs as $r) {
			$l = '';
			for($i = 0; $i < $n; $i++) {
				if($i) $l .= ';';
				$d = $cs[$i][1];
				$v = $r->{$d};
				switch($cs[$i][2]) {
					case 'date':
						$v = $v ? date('Y-m-d', $v) : '';
						break;
					case 'time':
						$v = $v ? date('Y-m-d H:i', $v) : '';
						break;
					case 'image':
						$d .= '_ToolTip';
						if(isset($r->{$d})) $v = $r->{$d};
						break;
				}
				$l .= '"'.$v.'"';
			}
			$csv .= $l ."\r\n";
		}
		return $csv;
	}

	private function exportXLS(&$cs,&$rs,$nam) {
		require_once('Class/Lib/phpExcel/PHPExcel.php');
		$n = count($cs);
		$xl = new phpExcel();
		$xl->getProperties()->setTitle($nam);
		$xl->setActiveSheetIndex(0);
		$sh = $xl->getActiveSheet();
		for($i = 0; $i < $n; $i++) {
			$p = PHPExcel_Cell::stringFromColumnIndex($i);
			$sh->SetCellValue($p.'1', $cs[$i][0] ? $cs[$i][0] : $cs[$i][1]);
			switch($cs[$i][2]) {
				case 'date':
					$sh->getStyle($p)->getNumberFormat()->setFormatCode('dd/mm/yy');
					break;
				case 'time':
					$sh->getStyle($p)->getNumberFormat()->setFormatCode('dd/mm/yy h:mm');
					break;
			}
		}
		$l = 2;
		foreach($rs as $r) {
			for($i = 0; $i < $n; $i++) {
				$d = $cs[$i][1];
				$v = $r->{$d};
				switch($cs[$i][2]) {
					case 'date':
					case 'time':
						$v = $v ? PHPExcel_Shared_Date::PHPToExcel($v) : '';
						break;
					case 'image':
						$d .= '_ToolTip';
						if(isset($r->{$d})) $v = $r->{$d};
						break;
				}
				$p = PHPExcel_Cell::stringFromColumnIndex($i).$l;
				$sh->SetCellValue($p,$v);

				$c = $d.'_Color';
				if(isset($r->{$c})) {
					$h = $this->hexaColor($r->{$c});
					$sh->getStyle($p)->getFont()->getColor()->applyFromArray(array("rgb"=>$h));
				} 
				$c = $d.'_backgroundColor';
				if(isset($r->{$c}) && ! empty($r->{$c})) {
					$h = $this->hexaColor($r->{$c});
					$sh->getStyle($p)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
					$sh->getStyle($p)->getFill()->getStartColor()->setRGB($h);
				} 
			}
			$l++;
		}
		$wr = new PHPExcel_Writer_Excel2007($xl);
		ob_start();
		$wr->Save('php://output');
		$xls = ob_get_contents();
		ob_end_clean();
		return $xls;
	}

	private function hexaColor($c) {
		if(substr($c,0,1)=='#') return strtoupper(substr($c,1,6));
		if(substr($c,0,2)=='0x') return strtoupper(substr($c,2,6));
		return strtoupper($c);
	}

	private function exportPDF(&$cs,&$rs,$nam) {
		require_once('ExportPDF.class.php');
		
		$pdf = new ExportPDF($cs,$rs,$nam,'A4');
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle($nam);
		
		$pdf->AddPage();
		$pdf->PrintLines($rs);
		$res = $pdf->Output('','S');
		$pdf->Close();
		return $res;
	}

}
