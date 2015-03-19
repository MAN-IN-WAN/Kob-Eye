<?php
class baseDriver extends ObjectClass{

	/* ---------------------------------
	 |            CONSTRUCTEUR        |
	 ---------------------------------*/
	/* ---------------------------------
	 |            PUBLIQUE            |
	 ---------------------------------*/


	function initData () {
		echo "=> INITDATA <br />\r\n";
	}


	function Purge() {
		//Aucun champ a purger, car on ne peut pas en creer
		echo "=> PURGE <br />\r\n";
	}

	function resetSearch(){
		echo "=> RESET SEARCH <br />\r\n";
	}

	function DriverSearch($Recherche='',$Analyse="",$get=false,$parent=false,$Key="",$Select="",$DataSource="",$filtre=""){
		echo "//////////////////////////////////////////////////////////////////////////////////\r\n";
		echo "=> SEARCH ".$this->titre."<br />\r\n";
		echo "-------------------RECHERCHE-------------\r\n".$Recherche."\r\n";
		echo "-------------------ANALYSE---------------\r\n".print_r($Analyse)."\r\n";
		echo "-------------------KEY-------------------\r\n".$Key."\r\n";
		echo "-------------------SELECT----------------\r\n".$Select."\r\n";
		echo "-------------------DATASOURCE------------\r\n".$DataSource."\r\n";
		echo "-------------------FILTRE----------------\r\n".$filtre."\r\n";
		echo "-------------------BASEDN----------------\r\n".$GLOBALS["Systeme"]->Modules[$this->Module]->Bdd[$this->Bdd]["BASE_DN"]."\r\n";
		echo "-------------------***-------------------------------------------------------------\r\n";
	}


	function searchAll($Opt="NO"){
		echo "=> SEARCH ALL<br />\r\n";
	}


	function generateId($Result){
		echo "=> GENERATE ID<br />\r\n";
	}

	function multiSearch($Recherche){
		echo "=> MULTI SEARCH<br />\r\n";
	}


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

	function insertObject(){
		echo "=> INSERT OBJECT<br />\r\n";
	}


	function createData(){
		echo "=> CREATE DATA<br />\r\n";
	}

	function insertKey($Tab,$Type){
		echo "=> INSERT KEY<br />\r\n";
	}


	function Erase($Id){
		echo "=> ERASE<br />\r\n";
	}


	function EraseAssociation($Relative,$ObjId,$Type){
		echo "=> ERASE ASSOCIATION<br />\r\n";
	}


	/*---------------------------------
	 |           PRIVEE               |
	 ---------------------------------*/

	/*---------------------------------
	 |           CREATION              |
	 ---------------------------------*/

	function init() {
		echo "=> INIT<br />\r\n";
	}


	function saveData(){
		echo "=> SAVE DATA<br />\r\n";
	}


	/*---------------------------------
	 |           RECHERCHE             |
	 ---------------------------------*/


	/* Cette fonction range et classe dans un tableau les donnees trouvees.
	 Renvoi: le tableau de resultat.
	 Parametres: les donnees trouvees dans la base de donnees, la recherche effectuee*/
	function analyzeSearch($Donnees, $Recherche) {
		echo "=> ANALYSE SEARCH<br />\r\n";
		$Resultat= Array();
		$compteur=0;
		$totalCibles=count($this->Cibles);
		//On procede au calcul de la note que l'on enregistre, avec le reste, dans le tableau final
		while($Enregistrement=mysql_fetch_assoc($Donnees)){
			foreach ($this->Cibles as $valeurCible){
				foreach ($Enregistrement as $clefEnr=>$valeurEnr){
					$Resultat[$compteur][$clefEnr] = $valeurEnr;
					//Calcul de la note
					$note= (preg_match('!'.$Recherche.'!i', $valeurEnr) && $clefEnr== $valeurCible['nom']) ? $this->calcNote($valeurEnr,$Recherche,$valeurCible['searchorder']): 10;
				}
			}
			$Resultat[$compteur]['note'] = $note;
			$compteur++;
		}
		$Resultat = $this->bubbleSort($Resultat,'note');
		$Resultat=$this->setSearchOrder($Resultat);
		return $Resultat;
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