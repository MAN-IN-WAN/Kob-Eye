<?php

class textDriver extends ObjectClass{
	var $Folder = 'Data';
	var $Separateur = '|^|';
	var $PregSeparateur = '\|\^\|';
	var $Proc=0;
	var $ProcTemp=0;

	/* ---------------------------------
	 |            CONSTRUCTEUR        |
	 ---------------------------------*/
	function __construct($S,$M) {
		$this->Module = $M;
		if ($S) $this->initFromXml($S);
	}
	/* ---------------------------------
	 |            PUBLIQUE            |
	 ---------------------------------*/


	function initData () {
		//Creation / reparation / indexation des bases
		//Creation du chemin 
		if (!file_exists($this->Folder.'/'.$this->Module.'/'.$this->titre.''))
			Connection::mk_dir($this->Folder.'/'.$this->Module.'/'.$this->titre);
		//Creation du fichier de base de donnée
		if (!file_exists($this->Folder.'/'.$this->Module.'/'.$this->titre.'/bdd.dat')){
			if (!$f = fopen($this->Folder.'/'.$this->Module.'/'.$this->titre.'/bdd.dat',"w")) {die("CREATION FICHIER ".$this->Folder.'/'.$this->Module.'/'.$this->titre." PERMISSION REFUSEE ");}
			fclose($f);
		}
		//Creation du fichier de structure
		if (!file_exists($this->Folder.'/'.$this->Module.'/'.$this->titre.'/bdd.struct')){
			if (!$f = fopen($this->Folder.'/'.$this->Module.'/'.$this->titre.'/bdd.struct',"w")) {die("CREATION FICHIER STRUCTURE".$this->Folder.'/'.$this->Module.'/'.$this->titre." PERMISSION REFUSEE ");}
			//Creation de la structure
			$T = $this->getProperties();
			$db = new Flatfile();
			$db->datadir = $this->Folder.'/'.$this->Module.'/'.$this->titre.'/';
			foreach ($T as $D){
				$newId = $db->insertWithAutoId('bdd.struct', 0, Array(
					0 => '0',
					1 => $D
				));
			}
			fclose($f);
		}
		//Creation du fichier index Id
		if (!file_exists($this->Folder.'/'.$this->Module.'/'.$this->titre.'/bdd.id')){
			$fi = fopen($this->Folder.'/'.$this->Module.'/'.$this->titre.'/bdd.id',"w");
			fputs($fi,"1");
			fclose($fi);
		}
	}

	function getProperties() {
		//Creation de l'entete
		$T = array_keys(ObjectConst::NeededPHP());
		foreach ($T as $D)$L[]=$D;
		$T = array_keys($this->Proprietes);
		foreach ($T as $D)$L[]=$D;
		//Creation des clefs
		$T = array_keys($this->Etrangeres);
		foreach ($T as $D)if ($this->Etrangeres[$D]["card"]=="0,1"||$this->Etrangeres[$D]["card"]=="1,1")$L[]=$D;
		return $L;
	}

	function getPropertyId($P) {
		//Creation de l'entete
		$T = $this->getProperties();
		foreach ($T as $K=>$D) if ($P==$D) return $K;
	}
	function getPropertyType($P) {
		//Creation de l'entete
		$T = ObjectConst::NeededPHP();
		foreach ($T as $D)$L[]=$D;
		$T = $this->Proprietes;
		foreach ($T as $D)$L[]=$D["type"];
		//Creation des clefs
		$T = $this->Etrangeres;
		foreach ($T as $D)if ($D["card"]=="0,1"||$D["card"]=="1,1")$L[]="int";
	}
	function Purge() {
		//Aucun champ a purger, car on ne peut pas en creer
		echo "=> PURGE <br />\r\n";
	}

	function resetSearch(){
		echo "=> RESET SEARCH <br />\r\n";
	}
	/*---------------------------------
	 |           RECHERCHE             |
	 ---------------------------------*/


	function DriverSearch($Analyse,$Select="",$GroupBy=""){
		$Recherche = $Analyse[0]["Recherche"];
//  		echo "//////////////////////////////////////////////////////////////////////////////////\r\n";
// 		echo "=> SEARCH ".$this->titre."<br />\r\n";
// 		echo "-------------------RECHERCHE-------------\r\n".print_r($Recherche,true)."\r\n";
// 		echo "-------------------ANALYSE---------------\r\n".print_r($Analyse,true)."\r\n";
// 		echo "-------------------KEY-------------------\r\n".$Key."\r\n";
// 		echo "-------------------SELECT----------------\r\n".$Select."\r\n";
// 		echo "-------------------DATASOURCE------------\r\n".$DataSource."\r\n";
// 		echo "-------------------FILTRE----------------\r\n".$filtre."\r\n";
// 		echo "-------------------***-------------------------------------------------------------\r\n";
		//Analyse des filtres de recherches
		
// 		print_r($Recherche);
		if (!empty($Recherche)){
			if (sizeof(explode("&",$Recherche)))$Results = $this->multiSearch($Recherche);
			else $Results = $this->searchUnique($Recherche);
		}else $Results = $this->searchAll();
		if (is_string($Select)&&preg_match("#COUNT\(DISTINCT\(.*?\)\)#",$Select))$Results[0][$Select] = sizeof($Results);
// 		print_r($Results);
		return $Results;
	}

	function searchUnique($Recherche){
		$Lim = $GLOBALS["Systeme"]->Modules[$this->Module]->Db->LimRequete;
		$T = $this->getProperties();
		$db = new Flatfile();
		$db->datadir = $this->Folder.'/'.$this->Module.'/'.$this->titre.'/';
		$N = $db->selectAll('bdd.dat');
		if (!empty($N)&&is_array($N))foreach ($N as $K=>$n){
			$i=0;
			foreach ($T as $D){
				$Results[$K][$D]=$n[$i];
				$i++;
			}
		}
		/*echo "SEARCH UNIQUE $Recherche =>\r\n";
		print_r($Results);*/
		return $Results;
	}

	function searchAll(){
		$Lim = $GLOBALS["Systeme"]->Modules[$this->Module]->Db->LimRequete;
		$T = $this->getProperties();
		$db = new Flatfile();
		$db->datadir = $this->Folder.'/'.$this->Module.'/'.$this->titre.'/';
		$N = $db->selectAll('bdd.dat');
		if (!empty($N)&&is_array($N))foreach ($N as $K=>$n){
			$i=0;
			foreach ($T as $D){
				$Results[$K][$D]=$n[$i];
				$i++;
			}
		}
// 		echo "SEARCH ALL =>\r\n";
// 		print_r($Results);
		return $Results;
	}


	function generateId($Result){
		echo "=> GENERATE ID<br />\r\n";
	}

	function multiSearch($c){
// 		echo "MULTI SEARCH =>\r\n";
		//Construction de l'expression reguliere
		$Results = Array();
		$C = $this->multiCondition($c);
// 		print_r($C);
		$T = $this->getProperties();
// 		print_r($T);
		$db = new Flatfile();
		$db->datadir = $this->Folder.'/'.$this->Module.'/'.$this->titre.'/';
		$N = $db->selectWhere('bdd.dat', $C, 100,new OrderBy(0, DESCENDING, INTEGER_COMPARISON));
// 		print_r($N);
		if (!empty($N)&&is_array($N))foreach ($N as $K=>$n){
			$i=0;
			foreach ($T as $D){
				if (isset($n[$i]))$Results[$K][$D]=$n[$i];
				$i++;
			}
		}
// 		print_r($Results);
		return $Results;
	}

	function multiCondition($Recherche){
		//Construction de l'expression reguliere
		$T = $this->getProperties();
		$Rech = explode('&',$Recherche);
		$Condition = new AndWhereClause();
		if (is_array($Rech)) foreach ($Rech as $c){
			preg_match("#(.*)([=!+<>~]{2})(.*)#",$c,$Out);
			if (sizeof($Out)<2)preg_match("#(.*)([=!+<>~]{1})(.*)#",$c,$Out);
			if (isset($Out[1])&&sizeof(explode(".",$Out[1]))>1)$Pref="";else $Pref=".";
			if (isset($Out[1])&&$this->getPropertyId($Out[1])){
				//Recherche type de comparaison
				$Type = $this->getPropertyType($Out[1]);
				$Condition->add(new SimpleWhereClause($this->getPropertyId($Out[1]),$Out[2],$Out[3],($Type=="int")?'intcmp':($Type=="float")?'numcmp':'strcmp'));
			}elseif (is_numeric($c)){
				$Condition->add(new SimpleWhereClause($this->getPropertyId('Id'),'=',$c,'intcmp'));
			}
		}
		return $Condition;
	}
	/*---------------------------------
	 |          UTILS            |
	 ---------------------------------*/

	function getReference(){
		echo "=> GET REFERENCE<br />\r\n";
	}


	function getReflexiveRelatives($id,$typeSearch,$Parametres=''){
		echo "=> GET REFLEXIVE RELATIVE<br />\r\n";
	}


	function getBeacon($Var,$Data,$SupVar){
		echo "=> GET DEFAULT<br />\r\n";
	}

	function getFkeyRelatives($id,$typeSearch,$Recherche,$Card,$Parametres=''){
		echo "=> GET FKEY RELATIVE<br />\r\n";
	}

	function createBeaconTab(){
		echo "=> CREATE BEACON TAB<br />\r\n";
	}

	/*---------------------------------
	 |       CREATION / EDITION        |
	 ---------------------------------*/


	function insertObject($Properties){
		//Detection modification / ajout
		$T = $this->getProperties();
		$db = new Flatfile();
		$db->datadir = $this->Folder.'/'.$this->Module.'/'.$this->titre.'/';
		if (isset($Properties["Id"])){
			foreach ($T as $D) if (isset($Properties[$D]))$N[]=$Properties[$D];else $N[]='';
			$Results = $db->updateSetWhere('bdd.dat',$N,new SimpleWhereClause(0, '=', $N[0]));
		}else{
			//Ajoute
			$N[0] = $this->getNextId();
			if (empty($N[0]))$N[0] = 1;
			foreach ($T as $D)if (isset($Properties[$D])) $N[]=$Properties[$D];elseif ($D!="Id")$N[]='';
			$Results = $db->insert('bdd.dat',$N);
			$this->setNextId($N[0]+1);
		}
	}
	function createData(){
		echo "=> CREATE DATA<br />\r\n";
	}

	function insertKey($Tab,$Type){
		echo "=> INSERT KEY<br />\r\n";
	}

	private function getNextId() {
		//Ouverture du fichier nextId
		$path=$this->Folder."/".$this->Module."/".$this->titre.'/bdd.id';
		if (!file_exists($path)) $this->initData();
		$f=fopen($path,"r");
		$Id = fread($f,10);
		fclose($f);
		return $Id;
	}

	private function setNextId($Id) {
		//Ouverture du fichier nextId en ecriture
		$f = fopen($this->Folder.'/'.$this->Module.'/'.$this->titre.'/bdd.id',"w");
		fputs($f,$Id);
		fclose($f);
	}

	/*---------------------------------
	 |           SUPPRESSIOB           |
	 ---------------------------------*/

	function DriverErase($Id){
//  		echo "=> ERASE $Id<br />\r\n";
		//Supression d'une ligne
		$db = new Flatfile();
		$db->datadir = $this->Folder.'/'.$this->Module.'/'.$this->titre.'/';
		$Results = $db->deleteWhere('bdd.dat',
			new SimpleWhereClause(0, '=', $Id));
	}


	function EraseAssociation($Relative,$ObjId,$Type){
		echo "=> ERASE ASSOCIATION<br />\r\n";
	}


	/*---------------------------------
	 |            PRIVEE               |
	 ---------------------------------*/

	function init() {
		echo "=> INIT<br />\r\n";
	}


	function saveData(){
		echo "=> SAVE DATA<br />\r\n";
	}


	/*---------------------------------
	 |          TEST/AUTO              |
	 ---------------------------------*/

	function getTableName(){
		echo "=> GET TABLE NAME<br />\r\n";
	}

	function findReflexive(){
		echo "=> FIND REFLEXIVE<br />\r\n";
	}

	function getTime(){
		echo "=> GET TIME<br />\r\n";
	}
	
}