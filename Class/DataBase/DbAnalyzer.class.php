<?php

class DbAnalyzer extends Root{
	var $ObjectClass = Array();
	var $IndexObjectClass = Array();
	var $Association=Array();
	var $IndexAssociation = Array();
	var $logSql = Array();
	var $tabLimit = Array();
	public static $LimRequete = Array(0,100);
	public static $QueryType = "";
	public static $DefaultOrder = Array("Id","DESC");
	public static $Order = Array();
	var $Module;
	var $ClefsRetenus=Array();
	var $CalledObjectClass=Array();
	var $Cache = Array();
	var $cacheChilds = Array();
	var $QueryTab = Array();
	var $checkRightsSuccess;
	var $tabId= Array();
	var $useLimits;
	var $useOrder;
	var $tabCache = Array();
	var $Dependencies = Array();
	//CONFIG CACHE DEBRAYABLE
	var $liteCache = true;
	//------------------------------//
	// INITIALISATION		//
	//------------------------------//
	/**
	* Constructeur
	*/
	function DbAnalyzer($Module) {
		$this->Module = $Module;
		DbAnalyzer::$LimRequete = Array(0,SQL_MAX_LIMIT);
	}
	/**
	* loadSchema
	* Initialisation depuis le schema
	*/
	function loadSchema($Schema) {
		//Lecture du schema et initialisation des objets
		$this->initObjectClass($Schema);
	}
	/**
	* initObjectClass
	* Traitement du schema et initialisation des objectclass
	*/
	function initObjectClass($XmlResult) {
		if (is_array($XmlResult))$XmlKeys=array_keys($XmlResult);
		//Extraction des cles du tableau associatif
		for ($i=0;$i<count($XmlKeys);$i++) {
			switch ($XmlKeys[$i]) {
				case "OBJECTCLASS":
					for ($j=0;$j<sizeof($XmlResult[$XmlKeys[$i]]);$j++) {
						$this->addObjectClass(ObjectClass::createInstance($XmlResult[$XmlKeys[$i]][$j],$this->Module));
					}
				break;

				default:
					$GLOBALS['Systeme']->Log->log("ERREUR DE CONFIGURATION DANS LE MODULE ".$this->Nom." : Borne Inconnue ".$XmlKeys[$i]);
				break;
			}
		}
	}
	/**
	* initAssociation
	* Initialisation des associations entres objectclass
	*/
	function initAssociations() {
		//Intialisation des associations
		if (sizeof($this->ObjectClass))foreach ($this->ObjectClass as $Objet){
			$Objet->initAssociations();
			$Objet->initViews();
		}
	}
	/**
	* addAssociation
	* Ajout d'une association dans le tableau
	*/
	function addAssociation($A) {
		//Intialisation des associations
		if (sizeof($this->Association))foreach ($this->Association as $As){
			if ($A == $As) return;
		}
		array_push($this->Association,$A);
	}
	/**
	* addDependency
	* add a module dependency
	* @param String Module name
	*/
	public function addDependency($M) {
		if (!in_array($M,$this->Dependencies))$this->Dependencies[]=$M;
	}
	/**
	* addObjectClass
	* add an ObjectClass
	* @param String ObjectClass name
	*/
	public function addObjectClass($O) {
		if (!in_array($O->titre,$this->IndexObjectClass)){
			$n = sizeof($this->ObjectClass);
			$this->ObjectClass[$n]=$O;
			$this->IndexObjectClass[$n] = $O->titre;
		}
	}
	//------------------------------//
	// GESTION DU CACHE		//
	//------------------------------//
	/**
	* loadCache
	* Chargement du cache
	*/
	function loadCache($Id){
		/*Cache SQL*/
		$Query = "_Dossier/Home/".$Id."/_Fichier/SQLsearch.cache";
		$Chemin = "Home/".$Id."/SQLsearch.cache";
		if (SQL_CACHE && file_exists($Chemin)) {
			$Tab = $GLOBALS["Systeme"]->Modules["Explorateur"]->callData($Query);
			$this->Cache = unserialize($Tab[0]["Contenu"]);
			return true;
		}else return false;
	}

	//Ecriture du cache
	function setCache($Cache) {
		$this->isCached=$Cache;
	}
	//Gestion du cache sur une session
	function loadLiteCache($Query,$Select,$GroupBy){
		$Result = Array();
 		if ($this->Module=="Explorateur") return false;
		if (SQL_LITE_CACHE){
			$tabCell = urlencode($Query).urlencode((is_array(DbAnalyzer::$LimRequete))?implode("",DbAnalyzer::$LimRequete):DbAnalyzer::$LimRequete).urlencode((is_array(DbAnalyzer::$Order))?implode("",DbAnalyzer::$Order):DbAnalyzer::$Order);//.urlencode((is_array($Select))?implode("",$Select):$Select).urlencode((is_array($Select))?implode("",$GroupBy):$GroupBy);
            /*$t = apc_fetch($tabCell);
            if ($t){
                //klog::l('Load apc cache'.$tabCell,$t);
                return $t;
            }*/
			if (is_array($this->tabCache)) if (array_key_exists($tabCell,$this->tabCache)){
				$Result = $this->tabCache[$tabCell];
				return $Result;
			}
		}
		 return false;
	}
	function clearLiteCache(){
		$this->tabCache= Array();
	}
	//Ecriture du cache
	function writeInCache($Results,$Query,$Select,$GroupBy){
		if (!SQL_LITE_CACHE)return false;
		$Replace=false;
		if ($this->Module=="Explorateur") return false;
		$tabCell = urlencode($Query).urlencode((is_array(DbAnalyzer::$LimRequete))?implode("",DbAnalyzer::$LimRequete):DbAnalyzer::$LimRequete).urlencode((is_array(DbAnalyzer::$Order))?implode("",DbAnalyzer::$Order):DbAnalyzer::$Order).urlencode((is_array($Select))?implode("",$Select):$Select).urlencode((is_array($Select))?implode("",$GroupBy):$GroupBy);
		$this->tabCache[$tabCell]=$Results;
        /*$rc=apc_store($tabCell,$Results,3600);*/
    }
	//------------------------------//
	// FONCTION D EXPORTATION	//
	//------------------------------//
	//Extraction des donnï¿œes par rapport a un groupe proprietaire
	function saveGroupData($groupId){
		$Resultats = "\r\n\r\n\r\n\r\n#MODULE ".$this->Module."\r\n\r\n\r\n\r\n";
		for ($i=0;$i<count($this->ObjectClass);$i++){
			$TabSql[$this->ObjectClass[$i]->titre] = $this->ObjectClass[$i]->saveGroupData($groupId);
		}
		foreach ($TabSql as $Nom=>$Objet){
			$Resultats .= "\r\n#Contenu de la table $Nom \r\n";
			if (is_array($Objet)) foreach ($Objet as $Requete){
				$Resultats .= $Requete."\r\n";
			}else $Resultats .= "#C'est une table vide\r\n";

		}
		return $Resultats;
	}

	//Sauvegarde de la base de donnï¿œe complete
	function saveData(){
		//Cree des fichiers SQL de toute la base
		for ($i=0;$i<count($this->ObjectClass);$i++) $Result=$this->ObjectClass[$i]->saveData();
		for ($i=0;$i<count($this->Association);$i++) $Result=$this->Association[$i]->saveData();
		return ($Result!=NULL) ? 1 : 0;
	}

	//--------------------------------------//
	// FONCTIONS PUBLIQUES D INTERROGATION	//
	//--------------------------------------//
	//Retourne une reference de l objectclass definit comme master dans le schema
	function getMaster(){
		/*Renvoie l'objectclass principal d'un module*/
		foreach($this->ObjectClass as $ObjClass){
			if (!is_array($ObjClass->childOf)) return $ObjClass->titre;
			else{
				if ($ObjClass->childOf[0]["Titre"]==$ObjClass->titre && !is_array($ObjClass->childOf[1])) return $ObjClass->titre;
			}
		}
	}

	//Retourne un tableau recapitulatif
	function RecapTab(){
		for ($i=0;$i<count($this->ObjectClass);$i++){
			$Tab[$i]["driver"] = $this->ObjectClass[$i]->driver;
			$Tab[$i]["titre"] =  $this->ObjectClass[$i]->titre;
			$Tab[$i]["Module"] =  $this->Module;
			$Tab[$i]["Master"] =  $this->ObjectClass[$i]->Master;
			$Tab[$i]["Prefix"] =  $this->ObjectClass[$i]->Prefix;
			$Tab[$i]["Heritage"] =  $this->ObjectClass[$i]->Heritage;
			$Tab[$i]["AccessPoint"] =  $this->ObjectClass[$i]->AccessPoint;
		}
		return $Tab;
	}


	function getRights($Ids){
		$Num = $this->findByTitle("Droit");
		$Results = $this->ObjectClass[$Num]->getRights($Ids);
		return $Results;
	}


	/*Fonction qui donne le numero de l'objet en echange de son nom.*/
	function findByTitle($titre){
		$result = array_search($titre,$this->IndexObjectClass);
		if (is_int($result)) return $result;
		for ($i=0; $i<count($this->ObjectClass); $i++){
			if (is_array($this->ObjectClass[$i]->ReferChilds)){
				foreach ($this->ObjectClass[$i]->ReferChilds as $Ref){
					if ($titre == $Ref["Titre"]) return "REF";
				}
			}
		}
		return "-1";
	}
	/**
	* getByTitleOrFkey
	* Renvoie soit un objectclass du module courrant
	* Soit une association  du module courrant
	* Soit un objectclass d'un module dépendant
	* @param string Nom de l'element
	*/
	function getByTitleOrFkey($Element,$Module=""){
		//Cas objectclass
		if (empty($Module)||$Module==$this->Module){
			$Obj = $this->getObjectClass($Element);
			if (is_object($Obj)) return $Obj;
			//Cas objectclass dependant
			foreach ($this->Dependencies as $D) {
				$Mod = Sys::getModule($D);
				$Mod->loadSchema();
				$Obj = $Mod->Db->getObjectClass($Element);
				if (is_object($Obj))return $Obj;
			}
		}else if(!empty($Module)){
			//Cas objectclass dependant
			$Mod = Sys::getModule($Module);
			$Mod->loadSchema();
			$Obj = $Mod->Db->getObjectClass($Element);
			if (is_object($Obj))return $Obj;
		}
		return false;
	}
	/**
	* Fonction qui donne le numero de l'objet en echange de son nom.
	* Alias de getObjectClass
	* #DEPRECATED
	*/
	function getByTitle($titre){
		return $this->getObjectClass($titre);
	}
	/**
	* Get ObjectClass avec son titre
	* @return une reference de l'objectclass
	*/
	function getObjectClass($N){
		if (!in_array($N,$this->IndexObjectClass))return false;
		$result = array_search($N,$this->IndexObjectClass);
		return $this->ObjectClass[$result];
	}

	function isReflexive($Obj){
		$Id = $this->findByTitle($Obj);
		if ($Id==-1) return;
		return ($this->ObjectClass[$Id]->noRecursivity)?false:$this->ObjectClass[$Id]->isReflexive();
	}
	
	//Recuperation de l'objectClass definit par defaut
	function getDefaultObjectClass($N=1){
		for ($i=0;$i<sizeof($this->ObjectClass);$i++){
			if ($this->ObjectClass[$i]->Default) return $this->ObjectClass[$i]->titre;
		}
		return false;
	}
	//----------------------------------------------//
	// VERIFICATION DES DONNEES			//
	//----------------------------------------------//
	//Fonction de verification
	function Check(){
		//Lance une verification de la bdd.
		echo "<li>MODULE $this->Module</li><ul>";
		for ($i=0;$i<count($this->ObjectClass);$i++) {
			echo "<li>OBJECTCLASS ".$this->ObjectClass[$i]->titre."</li><ul>";
			$this->ObjectClass[$i]->Check();
			echo "</ul>";
		}
		echo "</ul>";
		echo "<li>ASSOCIATION (TABLES)</li><ul>";
		for ($i=0;$i<count($this->Association);$i++) {
			if ($this->Association[$i]->isLong()){
				echo "<li>".$this->Association[$i]->toString()."</li>\r\n";
				$this->Association[$i]->Check();
			}
		}
		echo "</ul>";
		return 1;
	}

	//----------------------------------------------//
	// FONCTIONS PUBLIQUES DE RECHERCHE/INSERTION	//
	//----------------------------------------------//

	//Fonction d insertion
	function Query($Entree){
		//		$this->tabCache = NULL;
		if(is_object($Entree)){
			$i=$this->findByTitle($Entree->ObjectType);
			$Object = $this->ObjectClass[$i]->Insert($Entree);
			$Object["ObjectType"] = $Entree->ObjectType;
		}
		return $Object;
	}


	/*Cette fonction appelle son homonyme dans ObjectClass pr obtenir un resultat en recherche floue, ou bien appelle getChilds ou getParents si l'url le permet.
	 Parametre: la recherche(string).
	 Renvoi: un tableau*/
	function searchObject($Tab,$Etape='',$Offset='0',$Limit=SQL_MAX_LIMIT,$Query="",$Otype="",$Ovar="",$Select="",$GroupBy=""){
//   		echo "------------------------\r\n";
//   		echo $Query."\r\n";
		//Modification des limites selon le type de recherche/*/*
				//echo "------AV-------> $Offset  | $Limit \r\n";
				//echo "EXECUTION REQUETE ---".$this->Module."/$Query \r\n";
				//var_dump($Otype);
  				//var_dump($Ovar);
 /*		switch ($Tab[0]["Type"]) {
			case "Direct":
				// 				echo "REQUETE DIRECTE !!! 0|1\r\n";
				$Offset = 0;
				$Limit = 1;
				break;
		}*/
		//Definition du type de la requete
		DbAnalyzer::$QueryType = $Tab[0]["Type"];
		//Recherche des champs de type order
		$this->checkRightsQuery = false;
		DbAnalyzer::$LimRequete = Array();
		DbAnalyzer::$LimRequete[0] = intval($Offset);
		DbAnalyzer::$LimRequete[1] = (!empty($Limit))?intval($Limit):SQL_MAX_LIMIT;
		DbAnalyzer::$Order = Array();
		//On verifie l'existence de la propriete sur laquelle on va ordonner.
		DbAnalyzer::$Order[0] = $Ovar;
		DbAnalyzer::$Order[1] = $Otype;
		//Gestion du cache
		if (SQL_CACHE) {
			$QTemp = urlencode($Query."_".$Offset."_".$Limit);
			$QTemp = str_replace("%","_",$QTemp);
			if (empty($this->Cache[$QTemp]) && $this->isCached) {
				$this->Cache[$QTemp]=$this->lookForObject($Tab,$Etape,0,$Query,$Select,$GroupBy,false,$Otype,$Ovar);
				if (empty($this->Cache[$QTemp])) $this->Cache[$QTemp]="NORESULT";
			}elseif(!$this->isCached){
				return $this->lookForObject($Tab,0,array(),$Query,$Select,$GroupBy,false,$Otype,$Ovar);
			}
			if ($this->Cache[$QTemp]=="NORESULT") return "";
			return $this->Cache[$QTemp];
		}else{
			$Cache = $this->loadLiteCache($Query,$Select,$GroupBy,$Otype,$Ovar);
			if (!$Cache){
// 				print_r($Tab);
				$Results = $this->lookForObject($Tab,0,array(),$Query,$Select,$GroupBy,false,$Otype,$Ovar);
				return $Results;
			}else{
				// 			echo "UTILISATION DU CACHE \r\n";
				// 			print_r($Cache);
				return $Cache;
			}
		}
	}


	//----------------------------------------------//
	// FONCTIONS PRIVEES DE RECHERCHE/INSERTION	//
	//----------------------------------------------//
	//Fonction privï¿œe recursive paermettant d'analyser la requete tableau
	function lookForObject($Tab,$NbEtape=0,$Analyse="",$Query="",$Select="",$GroupBy="",$Obj=false,$Otype="",$Ovar=""){
		if (!isset($Tab[$NbEtape]['DataSource'])) return false;
		if (!is_object($Obj))$Obj = $this->getByTitleOrFkey($Tab[$NbEtape]['DataSource'],$Tab[$NbEtape]['Module']);
		else $Obj = $Obj->getLinkedObjectClass($Tab[$NbEtape]['DataSource'],$Tab[$NbEtape]['Module']);
		$Parent=false;		
		if (!is_object($Obj)){return false;}
		//On teste la valeur de la paire en cours
		if (!isset($Tab[$NbEtape]['Value'])||$Tab[$NbEtape]['Value']==''){
			//Si il n y pas de paire suivante 
			if (isset($Tab[$NbEtape+1])&&is_array($Tab[$NbEtape+1])){
				$Temp = $Obj->getKeyInfo($Tab[$NbEtape+1]['DataSource'], $Tab[$NbEtape]['Value'], true, isset($Tab[$NbEtape+1]['Key'])?$Tab[$NbEtape+1]['Key']:null,isset($Tab[$NbEtape+1]['View'])?$Tab[$NbEtape+1]['View']:null);
				//il s agit d une recherche de parents
				$Obj2 = $Obj->getChildObjectClass($Tab[$NbEtape+1]['DataSource']);
				if(!is_object($Obj2)) {
					echo Module::$LAST_QUERY." ".$Tab[$NbEtape+1]['DataSource'] . " n'est pas un enfant de " . $Tab[$NbEtape]['DataSource']." ".print_r($Tab,true); die;
				} 
				if ($Obj->driver!=$Obj2->driver){
					$partialResults = $Obj->partialSearch($Temp,$Tab[$NbEtape+1]['Value'],true,$Temp['Champ'],$GroupBy,Array(),isset($Tab[$NbEtape+1]['Key'])?$Tab[$NbEtape+1]['Key']:null,isset($Tab[$NbEtape+1]['View'])?$Tab[$NbEtape+1]['View']:null);
					if (sizeof($partialResults))
						$Results = $Obj->Search('',$partialResults,false,$Select,$GroupBy,Array(),isset($Tab[$NbEtape+1]['Key'])?$Tab[$NbEtape+1]['Key']:null,isset($Tab[$NbEtape+1]['View'])?$Tab[$NbEtape+1]['View']:null,$Otype,$Ovar);
					else $Results = Array();
				}else{
					
					$Value = (isset($Tab[$NbEtape+1]['Value']))?$Tab[$NbEtape+1]['Value']:"";
					$Results = $Obj->Search($Temp,$Value,true,$Select,$GroupBy,$Analyse,isset($Tab[$NbEtape+1]['Key'])?$Tab[$NbEtape+1]['Key']:null,isset($Tab[$NbEtape+1]['View'])?$Tab[$NbEtape+1]['View']:null,$Otype,$Ovar);
				}
			}else{
				//alors il s agit d'une recherche d enfants
				if (!$NbEtape){
					//Sans contrainte
					$Results = $Obj->Search(false,(isset($Tab[$NbEtape]['Value']))?$Tab[$NbEtape]['Value']:'',false,$Select,$GroupBy,$Analyse,isset($Tab[$NbEtape]['Key'])?$Tab[$NbEtape]['Key']:null,isset($Tab[$NbEtape]['View'])?$Tab[$NbEtape]['View']:null,$Otype,$Ovar);
				}else{
					//Le cas de pilote different
					$Obj2 = $Obj->getParentObjectClass($Tab[$NbEtape-1]['DataSource']);
					if ($Analyse[$NbEtape-1]['Driver']!=$Obj2->driver){
						//$Results = $Obj2->Search('',$Tab[$NbEtape-1]['Value'],false,"Id",$GroupBy,Array(),isset($Tab[$NbEtape]['Key'])?$Tab[$NbEtape]['Key']:null,isset($Tab[$NbEtape]['View'])?$Tab[$NbEtape]['View']:null);
						$Results = $Obj->partialSearch($Analyse[$NbEtape-1],(isset($Tab[$NbEtape]['Value']))?$Tab[$NbEtape]['Value']:'',false,$Select,$GroupBy,Array(),isset($Tab[$NbEtape]['Key'])?$Tab[$NbEtape]['Key']:null,isset($Tab[$NbEtape]['View'])?$Tab[$NbEtape]['View']:null);
					}else{
						$Results = $Obj->Search($Tab[$NbEtape],(isset($Tab[$NbEtape]['Value']))?$Tab[$NbEtape]['Value']:'',false,$Select,$GroupBy,$Analyse,isset($Tab[$NbEtape]['Key'])?$Tab[$NbEtape]['Key']:null,isset($Tab[$NbEtape]['View'])?$Tab[$NbEtape]['View']:null,$Otype,$Ovar);
					}
				}
			}
		}else{
			
			//Il s agit d'une recherche paramï¿œtrï¿œe
			if  (isset($Tab[$NbEtape+1])&&is_array($Tab[$NbEtape+1])) {
				//Il y a une paire suivante donc il s agit d une recherche complexe
				$Temp = $Obj->getKeyInfo($Tab[$NbEtape+1]['DataSource'],$Tab[$NbEtape]['Value'],false,isset($Tab[$NbEtape+1]['Key'])?$Tab[$NbEtape+1]['Key']:null,isset($Tab[$NbEtape+1]['View'])?$Tab[$NbEtape+1]['View']:null);
				if ($Temp) $Analyse[] = $Temp; else return false;
				$Results = $this->lookForObject($Tab,$NbEtape+1,$Analyse,"",$Select,$GroupBy,Array(),$Obj);
				return $Results;
			}else{
				if (isset($Tab[$NbEtape-1]))$Obj2 = $Obj->getParentObjectClass($Tab[$NbEtape-1]['DataSource']);
				if ($NbEtape>0&&$Obj2->driver!=$Obj->driver){
					$Obj2 = $Obj->getParentObjectClass($Tab[$NbEtape-1]['DataSource']);
					$Results = $Obj2->Search('',$Tab[$NbEtape-1]['Value'],false,"Id",$GroupBy,Array(),isset($Tab[$NbEtape]['Key'])?$Tab[$NbEtape]['Key']:null,isset($Tab[$NbEtape]['View'])?$Tab[$NbEtape]['View']:null);
					$Rech = $Analyse[$NbEtape-1]['Champ']."=".((isset($Results[0]))?$Results[0]["Id"]:0);
					if (isset($Tab[$NbEtape]['Value'])) $Rech .= "&".$Tab[$NbEtape]['Value'];
					$Results = $Obj->Search('',$Rech,false,$Select,$GroupBy,Array(),isset($Tab[$NbEtape]['Key'])?$Tab[$NbEtape]['Key']:null,isset($Tab[$NbEtape]['View'])?$Tab[$NbEtape]['View']:null);
				}else{
					$Results =  $Obj->Search((isset($Analyse[$NbEtape-1]))?$Analyse[$NbEtape-1]:"",$Tab[$NbEtape]['Value'],false,$Select,$GroupBy,$Analyse,isset($Tab[$NbEtape]['Key'])?$Tab[$NbEtape]['Key']:null,isset($Tab[$NbEtape]['View'])?$Tab[$NbEtape]['View']:null);
				}
			}
		}
		//----------------------------------------------//
		//Gestion des Historiques	 		//
		//----------------------------------------------//
		if (is_array($Results)){
			$nbR=sizeof($Results);
			for($i=0;$i<$nbR;$i++){
				//if (!$Parent)$Results[$i]["ObjectType"] = $Tab[sizeof($Tab)-1]['DataSource'];
				for ($j=0;$j<sizeof($Tab)-1;$j++){
					if ($Tab[$j]['Value']!='') 
						$Results[$i]["Historique"][] = Array(
							"Id" => $Tab[$j]['Value'],
							"ObjectType" => $Tab[$j]['DataSource']
						);
				}
			}
		}
		if (!empty($Query)&&!$Select) $this->writeInCache($Results,$Query,$Select,$GroupBy);
		return $Results;
	}

	//----------------------------------------------//
	// FONCTIONS FERMETURE DE CONNEXION		//
	//----------------------------------------------//

	function close() {
		//Fermeture de la base de donnee
		//ON desactive le cache pour linstant .....
		if (SQL_CACHE&&$this->Module=="Systeme"){
			$Obj = new genericClass("Explorateur");
			$Obj->initFromType("_Fichier");
			$Obj->Set("Url","Dossier/Home/".Sys::$User->Id);
			$Obj->Set("Nom","SQLsearch.cache");
			$Obj->Set("Contenu",serialize($this->Cache));
			$Obj->Save();
		}
		//On transfere les fichiers a transferer
		if (isset($this->Mouvements)&&is_array($this->Mouvements)){
			foreach ($this->Mouvements as $Deplace){
				$Fichier  = $Deplace["Depart"].$Deplace["Fichier"];
				$Destination = $Deplace["Depart"].$Deplace["Fin"].$Deplace["Fichier"];
				if (!file_exists($Fichier)) continue 1;
				if (!@copy($Fichier,$Destination)){
					$Erreur = "Impossible de copier un fichier";
					$GLOBALS["Systeme"]->Log->log($Erreur,$Fichier);
				}else{
					if (@!unlink($Fichier)){
						$Erreur = "Impossible de supprimer un fichier";
						$GLOBALS["Systeme"]->Log->log($Erreur,$Fichier);
					}
				}
			}
		}
	}

	//----------------------------------------------//
	// FONCTIONS INTERROGATION DE SCHEMA		//
	//----------------------------------------------//
	//renvoie la liste des accesspoint ou un accesspoint en particulier si le parametre est renseignï¿œ
	function AccessPoint($Name=""){
		if ($Name=="") {
			foreach ($this->ObjectClass as $Obj) {
				if (isset($Obj->AccessPoint)&&$Obj->AccessPoint&&$Obj->Module==$this->Module) $Result[] = $Obj;
			}
		}else{
			foreach ($this->ObjectClass as $Obj) {
				if (isset($Obj->AccessPoint)&&$Obj->titre==$Name&&$Obj->AccessPoint&&$Obj->Module==$this->Module) $Result[] = $Obj;
			}
		}
		return $Result;
	}
	//renvoie la liste des accesspoint ou un accesspoint en particulier si le parametre est renseignï¿œ
	function Dico($Name=""){
		$Result=array();
		if ($Name=="") {
			foreach ($this->ObjectClass as $Obj) {
				if ($Obj->Dico&&$Obj->Module==$this->Module) $Result[] = $Obj;
			}
		}else{
			foreach ($this->ObjectClass as $Obj) {
				if ($Obj->titre==$Name&&$Obj->Dico&&$Obj->Module==$this->Module) $Result[] = $Obj;
			}
		}
		return $Result;
	}
}
?>
