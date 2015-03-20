<?php

/**
 * CLASS VIEW
 * 
 * 
 */
class View extends Root{
	var $titre = "";
	//liste des selections
	var $Select = Array();
	//liste des filtres
	var $Filters = Array();
	//liste des jointures
	var $Joins = Array();
	//reference vers objectclass parent
	var $ObjectClass;
	//view par defaut
	var $default = false;
	//Tableau SQL
	var $sqlTab =Array();
	//Config
	var $Config = Array();
	//Counter
	var $Counter = 0;
	//proprites supplémentaires
	var $ViewProperties = Array();
	//order
	var $ViewOrder = array();
	/**
	 * Constructeur
	 * @param T Tableau des configurations
	 * @param O Objectclass parent
	 * @return void 
	 */
	public function View($T,$O) {
		$this->titre = $T["@"]["title"];
		if (isset($T["@"]["filters"]))$this->Filters = $T["@"]["filters"];
		if (isset($T["@"]["default"])) $this->default = $T["@"]["default"];
		$this->ObjectClass = $O;
		$this->Config = $T;
	}
	/**
	 * createInstance
	 * Cree une instance de view
	 * @param T Tableau des configurations
	 * @param O Objectclass parent
	 * @return void
	 */
	static public function createInstance($T,$O){
		return new View($T,$O);
	}
	/**
	 * init
	 * Initialisation de la vue
	 */
	public function init(){
		$T = $this->Config;
		if (isset($T["#"]["OBJECTCLASS"][0])){
			//Cas OBJECTCLASS
			$this->analyze($T["#"]["OBJECTCLASS"][0]);
		}elseif (isset($T["#"]["SQL"])){
			//Cas SQL DIRECT
			//TODO
		}
	}
	
	/**
	 * Search
	 * @param Array Analyse
	 * @return Array Results
	 */
	public function Search ($Analyse,$Filters="",$Select="",$GroupBy="",$Otype="",$Ovar=""){
		//suppression des filtres jointés
		$Analyse = sqlFunctions::filterMultiJoin($Analyse);
		//Construction des jointures
		foreach ($Analyse as $k=>$A) if ($A['Out']==1) $Analyse[$k]['View'] = $this->titre;
		$Data = sqlFunctions::joinSql($Analyse,$this->sqlTab,$this->ObjectClass,$this);
		//Construction des jointures en mode selection + modification de la recherche pour éviter les collisions avec les filtres
		$Data = sqlFunctions::multiJoinSql($Analyse,$Data,$this->ObjectClass);
		//suppresion de la table de sortie par défaut
		$flag = false;
		if (is_array($Data))foreach ($Data["Table"] as $p=>$t){
			if ($t["Alias"]=="m") if (!$flag){
				$flag =true;
			}else{
				unset($Data["Table"][$p]);
			}
		}
		//gestion des filtres
		//if (!empty($Filters)){
			//unset($Data["Groupe"]);
			//return Array(Array($Data,true));
			//$Data = sqlFunctions::getMultiSearch(Array(),$Data,$Filters,"m",$this->ObjectClass,$this);
		//}
		//gestion des select
		if (!empty($Select)){
			//filtre sur la selection
			$Data = $this->filterSelection($Data,$Select);
			//$Select.=','.$this->ObjectClass->titre.' as ObjectType';
			//$Data["Select"][] = Array("Nom"=>"CONCAT('".$this->ObjectClass->titre."','')", "Alias"=>"ObjectType");
		}
		//gestion des ordres
		if (!empty($Ovar)&&!empty($Otype)){
			//on vérifie l'existence des champs d'ordre et on ajoute le bon alias
			if (array_key_exists($Ovar,$this->ViewProperties)){
				$Data['Order'] = '`'.$Ovar.'` '.$Otype;
			}
			
		}
		//Creation du sql;
		$sql = sqlFunctions::createSql("VIEW",$Data,$this->ObjectClass);
		
		//echo "SQL $sql \r\n";
		//return Array(Array($sql));
		//execution du sql
		$Results = mysqlDriver::executeSql($this->ObjectClass,$sql,"VIEW");
		//post traitement
		if (sizeof($Results))for ($i=0;$i<sizeof($Results);$i++){
			$Results[$i]['ObjectType'] = $this->ObjectClass->titre;
			$Results[$i]['_view'] = $this->titre;
		}

		return $Results;
	} 
	/**
	 * filterSelection
	 * @param Array[Array[String]] Tableau sql
	 * @param String Chaine des selection
	 * @return Array[Array[String]]
	 */
	 function filterSelection($tab,$select){
	 	$oldSel = $this->__clone_array($this->sqlTab["Select"]);
		$newSel = Array();
		
	 	//decoupage de la chaine de selection
	 	$sel = explode(",",$select);
		foreach ($sel as $s){
			$se = explode(" as ",$s);
			$found=false;
			//recherche de la propriete
			foreach ($oldSel as $os){
				if (isset($os["Alias"])&&$os["Alias"]==$se[0]){
					$newSel[] = $os;
					$found=true;
				}
			}
			if (!$found){
				if (sizeof($se)>1){
					$newSel[] = Array(
						"Nom" => $se[0],
						"Alias" => $se[1]
					);
				}else $newSel[] = Array(
					"Nom" => $se[0]
				);
			}
		}
		$tab["Select"] = $newSel;
		return $tab;
	 }
	/**
	 * analyze
	 * Analyse la table principale
	 * @param Array Xml Tab
	 * @void
	 */
	private function analyze($T){
		//Detail de la table principale
		$Ta = Array(
			"Prefix" => $this->ObjectClass->Prefix,
			"Nom" => $this->ObjectClass->titre,
			"Alias" => "m"
		);
		//Selection 
		if (isset($T["@"]["select"])&&!empty($T["@"]["select"])){
			$Se = $T["@"]["select"];
			$Se = explode(",",$Se);
			foreach ($Se as $s){
				$si = explode(" as ",$s);
				if (sizeof($si)>1){
					$Sel[] = Array(
						"Nom" => "m.".$s[0],
						"Alias" => $s[1]
					);
				}elseif ($s=="*")$Sel[] = Array(
                    "Nom" => "m.".$s
                );
                else $Sel[] = Array(
					"Nom" => "m.".$s,
					"Alias" => $s
				);
			} 
		}else if (isset($T['#']["PROPERTIES"])&&is_array($T['#']["PROPERTIES"])){
			foreach ($T['#']["PROPERTIES"] as $P){
				$this->ViewProperties[isset($P['@']['alias']) ? $P['@']['alias'] : $P['#']] = $this->ObjectClass->parseAttributes($P['@'],$P['#']);
				$this->ViewProperties[isset($P['@']['alias']) ? $P['@']['alias'] : $P['#']]["name"] = isset($P['@']['alias']) ? $P['@']['alias'] : $P['#'];
				$Sel[] = Array(
					"Nom" => "m.".($P['#']) ,
					"Alias" => isset($P['@']['alias']) ? $P['@']['alias'] : $P['#']
				);
			}
			//$GLOBALS["Systeme"]->Log->log("PROPETRTIES",$Sel);
		}else{
			$Sel[] = Array(
				"Nom" => "m.*"
			);
		}
		//Ajout des Sélections obligatoires
		$Sel[] = Array(
			"Valeur" => $this->ObjectClass->titre,
			"Alias" => "ObjectType"
		);
		//Filtres
		//TODO
		//GroupBy
		//TODO
		//Order
		if (isset($T["@"]["order"])&&!empty($T["@"]["order"])&&isset($T["@"]["orderType"])&&!empty($T["@"]["orderType"])){
			$fld = explode(',', $T["@"]["order"]);
			$dir = explode(',', $T["@"]["orderType"]);
			$tmp = '';
			foreach($dir as $k=>$d) {
				if($tmp) $tmp .= ',';
				if(strpos($fld[$k],".") === false) $tmp .= 'm.'.$fld[$k]." $d";
				else $tmp .= $fld[$k]." $d";
			}
			$this->sqlTab["Order"] = $tmp;
		}
		//Création de la SQL Table
		$this->sqlTab["Table"][] = $Ta;
		$this->sqlTab["Select"] = $Sel;
		//$this->sqlTab["Groupe"][] = $gr;
		//Jointures
		if (isset($T["#"]["JOIN"])&&is_array($T["#"]["JOIN"]))
			foreach ($T["#"]["JOIN"] as $j) $this->addJoin($j,$this->ObjectClass,"m");
		//Count
		if (isset($T["#"]["COUNT"])&&is_array($T["#"]["COUNT"]))
			foreach ($T["#"]["COUNT"] as $j) $this->addCount($j,$this->ObjectClass,"m");
	}
	/**
	 * addJoin
	 * Ajoute une jointure sur l'objet précédent
	 * @param Array Xml Tab
	 * @param Object Parent Objet
	 * @void 
	 */
	private function addJoin ($T,$P,$La){
		$this->Counter++;
		//type de la jointure
		$inner = (!isset($T["@"]["type"])||$T["@"]["type"]=="inner")? true:false;
		//Récupéartion du module de l'objet à joindre
		$mod = Sys::$Modules[(isset($T["@"]["module"]))? $T["@"]["module"]:$P->Module];
		//Récupération de l'objet à joindre
		$oj = $mod->Db->getObjectClass($T["@"]["title"]);
		if (!is_object($oj))return;
		//definition de la clef
		$key = $T["@"]["on"];
		//definition de l'alias;
		$alias = $T["@"]["title"].$this->Counter;
		//initialisation du tableau des selections
		$Sel = Array();
		if ($inner){
			//CAS INNER
			//Detail de la table 
			$Ta = Array(
				"Prefix" => $oj->Prefix,
				"Nom" => $oj->titre,
				"Alias" => $alias
			);
			//Ajout des conditions de jointure
			$gr = Array(
				"Lien" => "OR",
				"Condition" => Array(
					$La.".".$T["@"]["on"]." = ".$alias.".".$T["@"]["target"]
				)
			);
			if (!$inner){
				$gr['Condition'][] = $La.".".$T["@"]["on"].' is null';
			}
			$this->sqlTab["Table"][] = $Ta;
			$this->sqlTab["Groupe"][] = $gr;
		}else{
			//CAS OUTER
			$Jl = Array(
				"Side" => "LEFT",
				"Prefix" => $oj->Prefix,
				"Nom" => $oj->titre,
				"Alias" => $alias,
				"On" =>	$La.".".$T["@"]["on"]." = ".$alias.".".$T["@"]["target"]
			);
			$this->sqlTab["Table"][0]["Join"][] = $Jl;
		}
		//Selection 
		if (isset($T["@"]["select"])&&!empty($T["@"]["select"])){
			$Se = $T["@"]["select"];
			$Se = explode(",",$Se);
			foreach ($Se as $s){
				$si = explode(" as ",$s);
				$nprop = (sizeof($si)>1)?$si[0]:$s;
				$nalias = (sizeof($si)>1)?$si[1]:$s;
				$Sel[] = Array(
					"Nom" => ($inner)?$alias.".".$nprop: 'ifnull('.$alias.'.'.$nprop.', "")',
					"Alias" => $nalias
				);
			} 
		}else if (isset($T['#']["PROPERTIES"])&&is_array($T['#']["PROPERTIES"])){
			foreach ($T['#']["PROPERTIES"] as $P){
				$this->ViewProperties[isset($P['@']['alias']) ? $P['@']['alias'] : $P['#']] = $this->ObjectClass->parseAttributes($P['@'],$P['#']);
				$this->ViewProperties[isset($P['@']['alias']) ? $P['@']['alias'] : $P['#']]["name"] = $P['#'];
				$this->ViewProperties[isset($P['@']['alias']) ? $P['@']['alias'] : $P['#']]["prefixe"] = $alias;
				$this->ViewProperties[isset($P['@']['alias']) ? $P['@']['alias'] : $P['#']]["alias"] = isset($P['@']['alias']) ? $P['@']['alias'] : $P['#'];
				$Sel[] = Array(
					"Nom" => ($inner)?$alias.".".($P['#']): 'ifnull('.$alias.'.'.$P['#'].', "")' ,
					"Alias" => isset($P['@']['alias']) ? $P['@']['alias'] : $P['#']
				);
			}
		}
		//Filtres
		//TODO
		//GroupBy
		//TODO
		//Order
		if (isset($T["@"]["order"])&&!empty($T["@"]["order"])&&isset($T["@"]["orderType"])&&!empty($T["@"]["orderType"])){
			$fld = explode(',', $T["@"]["order"]);
			$dir = explode(',', $T["@"]["orderType"]);
			$tmp = '';
			foreach($dir as $k=>$d) {
				if($tmp) $tmp .= ',';
				if (strpos($fld[$k],".") === false) $tmp .= $alias.'.'.$fld[$k]." $d";
				else $tmp .= $fld[$k]." $d";
			}
			if(! isset($this->sqlTab["Order"])) $this->sqlTab["Order"] = '';
			if($this->sqlTab["Order"]) $this->sqlTab["Order"] .= ',';
			$this->sqlTab["Order"] .= $tmp;
		}
		//Saisie de la table sql
		$this->sqlTab["Select"] = array_merge($this->sqlTab["Select"],$Sel);
		//Jointures
		if (isset($T["#"]["JOIN"])&&is_array($T["#"]["JOIN"]))
			foreach ($T["#"]["JOIN"] as $j) $this->addJoin($j,$this->ObjectClass,$alias);
		//Count
		if (isset($T["#"]["COUNT"])&&is_array($T["#"]["COUNT"]))
			foreach ($T["#"]["COUNT"] as $j) $this->addCount($j,$this->ObjectClass,$alias);
	}
	/**
	 * addCount
	 * Ajoute un compte sur l'objet précédent
	 * @param Array Xml Tab
	 * @param Object Parent Objet
	 * @void 
	 */
	private function addCount ($T,$P,$La){
		$this->Counter++;
		//type de la jointure
		$inner = (!isset($T["@"]["type"])||$T["@"]["type"]=="inner")? true:false;
		//Récupéartion du module de l'objet à joindre
		$mod = Sys::$Modules[(isset($T["@"]["module"]))? $T["@"]["module"]:$P->Module];
		//Récupération de l'objet à joindre
		$oj = $mod->Db->getObjectClass($T["@"]["title"]);
		if (!is_object($oj))return;
		//definition de la clef
		$key = $T["@"]["on"];
		//definition de l'alias;
		$alias = $T["@"]["title"].$this->Counter;
		//initialisation du tableau des selections
		$Sel = Array();
		//Selection 
		$Sel[] = Array(
			"Nom" => "ifnull((select count(".$alias.".Id) from `". $oj->Prefix.$oj->titre."` as ".$alias." where ".$La.".".$T["@"]["on"]." = ".$alias.".".$T["@"]["target"]."),0)",
			"Alias" => $T["@"]["alias"]
		);
		//Declaration en tant que propriete
		$T["@"]["type"] = "int"; 
		$this->ViewProperties[$T["@"]["alias"]] = $this->ObjectClass->parseAttributes($T['@'],$T['#']);
		$this->ViewProperties[$T["@"]["alias"]]["name"] = $T["@"]["alias"];
		$this->ViewProperties[$T["@"]["alias"]]["prefixe"] = $alias;
		$this->ViewProperties[$T["@"]["alias"]]["alias"] =$T["@"]["alias"];
		//Saisie de la table sql
		$this->sqlTab["Select"] = array_merge($this->sqlTab["Select"],$Sel);
	}
}
?>
