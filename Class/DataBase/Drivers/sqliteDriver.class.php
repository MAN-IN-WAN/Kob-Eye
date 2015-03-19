<?php

class sqliteDriver extends ObjectClass{
	var $AUTO_INCREMENT = "AUTOINCREMENT";
	//----------------------------------------------//
	// VERIFICATION									//
	//----------------------------------------------//

	//Fonction associee a Check qui verifie les donnees entre sql et le schema
	//Si elle n'existe pas on la cree, et si la creation ne marche pas on renvoie une erreur
	function initData () {
		//require_once("Class/");
		sqlCheck::initData($this);
	}

	function createData () {
		//require_once("Class/");
		sqlCheck::createData($this);
	}


	//----------------------------------------------//
	// RECHERCHE					//
	//----------------------------------------------//

	//Effectue une recherche et la renvoie a lanalyse
	function DriverSearch($Analyse,$Select,$GroupBy){
		$Data = Array("Table"=>Array(),"Groupe"=>Array(),"GroupBy"=>Array());
		//suppression des filtres jointés
		$Analyse = sqlFunctions::filterMultiJoin($Analyse);
		//Construction des jointures
		$Data = sqlFunctions::joinSql($Analyse,$Data,$this);
		//Construction des jointures en mode selection + modification de la recherche pour éviter les collisions avec les filtres
		$Data = sqlFunctions::multiJoinSql($Analyse,$Data,$this);
		//Creation de la requete à partir du tableau
		$sql  = sqlFunctions::createSql("SELECT",$Data,$this,$Select);
		$Results=sqliteDriver::executeSql($this,$sql,"SELECT",$GroupBy);
		$Results =  (sizeof($Results)!=0) ? $this->analyzeSearch($Results,"") : Array();
		return $Results;
	}

	/* Cette fonction range et classe dans un tableau les donnees trouvees.
	 Renvoi: le tableau de resultat.
	 Parametres: les donnees trouvees dans la base de donnees, la recherche effectuee*/
	function analyzeSearch($Donnees, $Recherche) {
		$Resultat= Array(); $compteur=0;
		//On procede au calcul de la note que l'on enregistre, avec le reste, dans le tableau final
		if (is_array($Donnees))foreach ($Donnees as $Enregistrement){
			if (!is_array($Enregistrement)) continue;
			$Resultat[$compteur] = $Enregistrement;
			$Resultat[$compteur]['ObjectType'] = $this->titre;
			$Resultat[$compteur]['note'] = 10;
			$compteur++;
		}
		return $Resultat;
	}



	//----------------------------------------------//
	// INSERTION					//
	//----------------------------------------------//
	//Insere un nouvel objet ou met a jour un ancien selon la presence de lid
	function insertObject($Properties){
		$Flag=false;
		$Noms = '';
		$Valeurs = '';
		$RefWhere='';
		if (!isset($Properties['Id'])) {
			foreach ($Properties as $NomProp=>$ValeurProp){
				if($Flag) {$Noms.=",";$Valeurs.=",";$RefWhere.=" AND ";}
				$Noms.= '`'.$NomProp.'`';
				$Type = $this->getPropType($NomProp);
				switch ($Type){
					case "integer":
						$Valeurs.= intval($ValeurProp);
					break;
					case "float":
						$Valeurs.= floatval($ValeurProp);
					break;
					default:
						$Valeurs.= $GLOBALS["Systeme"]->Db[$this->Bdd]->Quote($ValeurProp);
					break;
				}
				if ($NomProp!="Id")$RefWhere .= '(`'.$NomProp.'` = '.$GLOBALS["Systeme"]->Db[$this->Bdd]->Quote($ValeurProp).' )';
				$Flag=true;
			}
			//On construit la requete SQL
			$sql = 'INSERT INTO `'.$this->Prefix.$this->titre.'` ('.$Noms.') VALUES ('.$Valeurs.')';
		}else{
			$Requete="";
			foreach ($Properties as $NomProp=>$ValeurProp){
				if($Flag) $Requete.=" , ";
				$Requete.= "`$NomProp`=".$GLOBALS["Systeme"]->Db[$this->Bdd]->Quote($ValeurProp);
				$Flag=true;
			}
			$sql = 'UPDATE `'.$this->Prefix.$this->titre.'` SET '.$Requete.' WHERE `Id`='.$Properties['Id'];
		}
		sqliteDriver::executeSql($this,$sql,"UPDATE");
		$new=false;
		if (!isset($Properties['Id'])){
			$new=true;
			$Properties['Id'] = $GLOBALS["Systeme"]->Db[$this->Bdd]->lastInsertId();
		}

		//GESTION DES INTERVALLES
		if ($this->isReflexive()==1) {
			if (!$new&&isset($Properties[$this->findReflexive()]))sqlInterval::removeIntervalData($Properties['Id'],$this);
			if ($new ||isset($Properties[$this->findReflexive()]))sqlInterval::insertIntervalData($Properties['Id'],(!isset($Properties[$this->findReflexive()]))?0:$Properties[$this->findReflexive()],$this);
		}
		if ($this->isReflexive()==2) {
			sqlInterval::insertIntervalData($Properties['Id'],0,$this);
		}
		return $Properties;
	}

	//----------------------------------------------//
	// MODFICATION					//
	//----------------------------------------------//
	//Modifie les droits
	function changeRights($Id,$Ui="",$Gi="",$Um="",$Gm="",$Om="")	{
		$sql = "UPDATE `".$this->Prefix.$this->titre."` SET ";
		$sql.="uid=$Ui,gid=$Gi,umod=$Um,omod=$Om,gmod=$Gm WHERE Id=$Id";
		sqliteDriver::executeSql($this,$sql,"UPDATE");
		return true;
	}
	/**
	* insertKey
	* Insere une association sur un objet donné
	* @param Array Tableau des proporiétés de l'objet
	* @param Integer Id de l'element à lier
	* @param Association Association concerne
	*/
	function insertKey($Tab,$Id,$A){
		$TabFields = $this->AddNeededFields('',"ASSOC");
		//Cas d'une clef reflexive longue
		if ($A->isRecursiv()==1) {
			sqlInterval::removeIntervalData($Id,$this,0);
			//Dans le cas d'un deplacement sur un arbre recursif alors on supprime l'acces racine par defaut
			$sql="DELETE FROM `".$this->Prefix.$C['Table']."` WHERE `".$Tab['Titre']."`=".$Id." AND `".$Tab['Titre']."Id`=0";
			sqliteDriver::executeSql($this,$sql,"UPDATE");
		}
		//Cas d'une clef secondaire longue
		if ($A->isLong()){
			//Verification existence de la clef
			$sql = "SELECT COUNT(Id) FROM `".$this->Prefix.$A->getTable()."` WHERE `".$this->titre."`=$Id AND ";
			if ($Tab["Id"]!="")$sql.= "`".$Tab['Fkey']."Id`=".$Tab['Id'];
			else $sql.="`".$Tab['Fkey']."Id`=0";
			$result = sqliteDriver::executeSql($this,$sql,"SELECT_SYS");
			//Si existe deja on sort
			if ($result[0]['COUNT(Id)']>0) return false;
			//C'est une insertion
			$sql =  "INSERT INTO `".$this->Prefix.$A->getTable()."` ";
			$FIELDS = "";
			$VALUES = "";
			$TabFields = $this->AddNeededFields('','ASSOC');
			foreach ($TabFields as $Nom=>$Valeur){
				if ($Nom!="tmsCreate"||$Nom!="userCreate"){
					//$sql.=$Nom."=".$Valeur.", ";
					if (!empty($FIELDS))$FIELDS .= ',';
					$FIELDS .= '`'.$Nom.'`';
					if (!empty($VALUES))$VALUES .= ',';
					$VALUES .= '"'.$Valeur.'"';
				}
			}
			if ($Tab["Id"]!=""&&$Tab["Id"]!=0){
				//$sql.= "`".$Tab['Fkey']."Id`=".$Tab['Id'].', ';
				if (!empty($FIELDS))$FIELDS .= ',';
				$FIELDS .= '`'.$Tab['Fkey'].'Id`';
				if (!empty($VALUES))$VALUES .= ',';
				$VALUES .= '"'.$Tab['Id'].'"';
			}else{
				//$sql.= "`".$Tab['Fkey']."Id`=0, ";
				if (!empty($FIELDS))$FIELDS .= ',';
				$FIELDS .= '`'.$Tab['Fkey'].'Id`';
				if (!empty($VALUES))$VALUES .= ',';
				$VALUES .= "0";
			}
			$FIELDS .= ',`'.$this->titre.'`';
			$VALUES .= ','.$Id;
			//$sql.= "`".$this->titre."`=".$Id;
			$sql.= '('.$FIELDS.') VALUES ('.$VALUES.')';
		}
		//Cas d'une cle courte
		if ($A->isShort()){
			$sql = "UPDATE `".$this->Prefix.$this->titre."` SET ";
			foreach ($TabFields as $Nom=>$Valeur){
				if ($Nom!="tmsCreate"||$Nom!="userCreate") $sql.=$Nom."=".$Valeur.", ";
			}
			if ($Tab['Id']!=""&&$Tab['Id']!=0) $sql.=$A->titre."=".$Tab['Id'];
			else $sql.=$A->titre." = 0";
			$sql.= ' WHERE Id='.$Id;
		}
		//Execution de la requete
		$res = sqliteDriver::executeSql($this,$sql,"UPDATE");
		//Ajout d'un intervalle dans le cas de la recursivite
		if ($A->isRecursiv()==2) {
			$lastid = $GLOBALS["Systeme"]->Db[$this->Bdd]->lastInsertId();
			sqlInterval::insertIntervalData($lastid,$Tab['Id'],$this);
		}
		return $res;
	}
	//----------------------------------------------//
	// UTILITAIRES					//
	//----------------------------------------------//
	//Selon le type de la propriete et la langue en vigueur il ajoute le prefixe de langue ou pas
	function langProp($Name) {
		// 		print_r($this->Proprietes);
		//print_r($this->Proprietes[$Name]);
		//echo "$Name $Special \r\n";
		$Special = "";
		if (array_key_exists($Name,$this->Proprietes)){
			$Special = (isset($this->Proprietes[$Name]["special"]))?$this->Proprietes[$Name]["special"]:"";
		}
		$Prefixe = $GLOBALS["Systeme"]->Language[$GLOBALS["Systeme"]->DefaultLanguage];
		$isNotDefault = $GLOBALS["Systeme"]->DefaultLanguage!=$GLOBALS["Systeme"]->CurrentLanguage;
		//echo "$isNotDefault\r\n";
		if ($isNotDefault && !Sys::$User->Admin && $Special=="multi") $Name= "".$Prefixe."-".$Name;
		//echo Sys::$User->Admin." $Special $Prefixe - $Name\r\n";
		return $Name;
	}
	//----------------------------------------------//
	// SUPPRESSION					//
	//----------------------------------------------//
	//Detruit un objet et toutes les associations qu-il comprend
	function DriverErase($Id){
		foreach($this->getChild() as $Assoc) {
			$Flag=$this->EraseTableAssociation($Id,"p",$Assoc);
		}
		foreach($this->getParent() as $Assoc){
			$Flag=$this->EraseTableAssociation($Id,"c",$Assoc);
		}
		$sql="DELETE FROM `".$this->Prefix.$this->titre."` WHERE Id=\"".$Id."\"";
		$Flag = sqliteDriver::executeSql($this,$sql,"DELETE");
		return $Flag;
	}
	/**
	* EraseTableAssociation
	* Detruit toutes les associations vers Id dans la tqble
	* @param Integer Id de l'element à dissocier
	* @param String Sens de l'association
	* @param Association Objet association
	*/
	public function EraseTableAssociation($Id,$Type,$A){
		//Pour les card n1, seul les parents sont concernes
		$sql="";
		if ($A->isShort() && $Type=="p"){
				$sql="UPDATE `".$this->Prefix.$A->getTable()."` SET `".$A->getField()."`='0' WHERE `".$A->getField()."`=\"".$Id."\"";
		}
		if ($A->isLong()){
			if ($Type=="c")
				$sql="DELETE FROM `".$this->Prefix.$A->getTable()."` WHERE `".$this->titre."`=\"".$Id."\"";
			elseif ($Type=="p")
				$sql="DELETE FROM `".$this->Prefix.$A->getTable()."` WHERE `".$A->getField()."`=\"".$Id."\"";
		}
		if ($A->isRecursiv()&&$Type=="p"){
			sqlInterval::removeIntervalData($Id,$this);
		}
		$driver = $A->getDriver();
		$driver::executeSql($A->getAssociationOwner(),$sql,"DELETE");
	}
	/**
	* EraseAssociation
	* Supprime une association
	* @param Integer Id de l'objet à dissocier
	* @param Association Objet association
	* @param Integer Id de l'objet distant lié
	*/
	public function EraseAssociation($currentId,$A,$beforeId){
		echo "ERASE ASSOCIATION \r\n";
		$sql="";
		//On efface l'association de this vers X ou X vers this dans une table
		if ($A->isShort()){
			$sql="UPDATE `".$this->Prefix.$this->titre."` SET `".$A->getField()."`='0' WHERE (`Id`=\"".$currentId."\")";
		}
		if ($A->isLong()){
			$sql="DELETE FROM `".$this->Prefix.$A->getTable()."` WHERE ";
			if ($beforeId!=""&&$beforeId!=0){
				$sql.=" (`".$A->getField()."`=".$beforeId.")";
			}else{
				$sql.="(`".$A->getField()."`=0)";
			}
			$sql.= "AND (`".$this->titre."`=".$currentId.")";
		}
		if ($A->isRecursiv()) {
			sqlInterval::removeIntervalData($currentId,$this);
		}
		echo $sql."\r\n";
		sqliteDriver::executeSql($this,$sql,"UPDATE_SYS");
		sqlCheck::CheckKeys($this);
		//sqlInterval::createIntervalIndex($this);
		echo "FIN ERASE ASSOCIATION \r\n";
	}
	//----------------------------------------------//
	// EXPORTATION					//
	//----------------------------------------------//

	//Exporation d'une table complete dans un fichier
	function fillTable() {
		//On verifie qu il esxiste un fichier
		$Path = "Modules/".$this->Module."/Backup/".$this->titre.".sql";
		if (file_exists($Path)) {
			//Alors on insere les donnÃ©es ligne apres ligne
			$file = fopen($Path,"r");
			while (!feof($file)){
				$ligne=fgets($file,4096);
				$sql="INSERT INTO `".$this->Prefix.$this->titre."` ".$ligne;
				sqliteDriver::executeSql($this,$sql,"INSERT");
			}
		} else $GLOBALS['Systeme']->Log->log("/ERREUR Impossible de trouver le jeu d essai ".$this->titre);
		return true;
	}
	//Exporation d'une table complete dans un fichier
	function saveData(){
		$sql="SELECT * FROM `".$this->Prefix.$this->titre."` as m";
		if (!$resql=sqliteDriver::executeSql($this,$sql,"SELECT")) return false;
		@mkdir("Modules/".$this->Module."/Backup/");
		$Path="Modules/".$this->Module."/Backup/".$this->titre.".sql";
		$Fichier=fopen($Path,'w');
		$GLOBALS['Systeme']->Log->log("/INFO Enregistrement du fichier  ".$this->titre);
		foreach ($resql as $Selection){
			foreach ($Selection as $Nom=>$Valeur){
				if ($this->isProperties($Nom) || $this->isFKey($Nom )||$Nom=="uid"||$Nom=="gid"||$Nom=="umod"||$Nom=="gmod"||$Nom=="omod") {
					if ($Flag){ $Noms.=','; $Valeurs.=",";}
					$Noms.='`'.$Nom.'`';
					$Valeurs.='"'.htmlentities($Valeur).'"';
					$Flag=true;
				}else{

				}
			}
			$ligne="(".$Noms.") VALUES (".$Valeurs.")\r\n";
			fwrite($Fichier,$ligne);
			$Noms = $Valeurs = $Flag = "";
		}
		return (file_exists($Path)) ? true:false;
	}

	//Renvoie l'ensemble des donnï¿œes propre ï¿œ un group
	function saveGroupData($gId){
		$sql = "select * from `".$this->Prefix.$this->titre."` ";
		$sql .= "where (gId=$gId) OR (omod>1)";
		$requete = mysql_query($sql) or die(mysql_error());
		while ($Resultat = mysql_fetch_assoc($requete)){
			$this->ObjectTable[] = $Resultat;
			$sql = "INSERT INTO `%LEPREFIXE%-".$this->Module."-".$this->titre."` (";
			foreach ($Resultat as $Nom=>$Valeur){
				if ($Flag) {$Noms.=", ";$Valeurs.=", ";}
				$Noms .= '`'.$Nom.'`';
				$Valeurs .= '"'.addslashes($Valeur).'"';
				if (!$Flag) $Flag = true;
			}
			$sql .= $Noms.") VALUES ($Valeurs);";
			$TabSql[] = $sql;
			$Noms = $Valeurs = $Flag = "";
		}
		if (is_array($this->childOf)) foreach ($this->childOf as $Parent){
			if ($Parent["Card"]=="0,n"||$Parent["Card"]=="1,n"){
				$sql = "select * from `".$this->Prefix.$this->titre.$Parent["Titre"]."` ";
				$requete = mysql_query($sql) or die(mysql_error());
				while ($Resultat = mysql_fetch_assoc($requete)){
					//On verifie que l'id appartient bien au User
					$Verif =$GLOBALS["Systeme"]->Modules[$this->Module]->Db->ObjectClass[$Parent["Id"]]->idPossess($Resultat[$Parent["Titre"]."Id"]);
					if ($this->idPossess($Resultat[$this->titre]) && $Verif){
						$sql = "INSERT INTO `%LEPREFIXE%-".$this->Module."-".$this->titre.$Parent["Titre"]."` VALUES ('".$Resultat["Id"]."','".$Resultat["tmsCreate"]."','".$Resultat["userCreate"]."','".$Resultat["tmsEdit"]."','".$Resultat["userEdit"]."','".$Resultat[$this->titre]."','".$Resultat[$Parent["Titre"]."Id"]."');";
						$TabSql[] = $sql;
					}
				}
			}
		}
		return $TabSql;
	}
	/**
	* recupere la liste des colonnes d'une table
	*
	*/
	function getTableStructure($TableName){
		$sql = "PRAGMA table_info(`".$TableName."`)";
		$resql=sqliteDriver::executeSql($this,$sql,"CHECK");
		//On reformate la table
		$o = Array();
		if (is_array($resql))foreach ($resql as $k=>$r){
			$length="";
			if (preg_match("#(.*?)\((.*?)\)#", $r['type'],$out)){
				$type = $out[1];
				$length = $out[2];
			}else {
				$type = $r['type'];
			}
			$o[$r["name"]] = Array(
				"type" => $type,
				"null" => ($r["notnull"])?"YES":"NO",
				"key" => ($r["pk"])?"PRI":"",
				"default" => $r["dflt_value"],
				"length" => $length,
				"extra" => ""
			);
		}
		$this->tableSql = $o;
		return (sizeof($o))?$o:false;
	}
	/**
	* recupere la liste des indexs d'une table
	*
	*/
	function getIndexList() {
		$sql="PRAGMA index_list('".sqlFunctions::getTableName($this)."');";
		$T = sqliteDriver::executeSql($this,$sql,"INDEX");
		$o=array();
		if (is_array($T))foreach($T as $t){
			$sql2="PRAGMA index_info('".$t["name"]."');";
			$T2 = sqliteDriver::executeSql($this,$sql2,"INDEX");
			$o[] = $T2[0]["name"];
		}
		return $o;
	}
	
	//----------------------------------------------//
	// EXECUTION SQL				//
	//----------------------------------------------//
	//Execution d une requete SQL
	static function executeSql($O,$sql,$type='SELECT',$GroupBy=""){
		$GLOBALS["Systeme"]->connectSQLITE();
		$i=false;
		if ($GroupBy!="") $sql.= ' GROUP BY '.$GroupBy;
		$Result = $GLOBALS["Systeme"]->Db[$O->Bdd]->query ( $sql );
		if ($Result)$Result = $Result->fetchALL ( PDO::FETCH_ASSOC );
		//#DIRTY WORKAROUND 
		if (preg_match("#COUNT\(DISTINCT\(m\.Id\)\)#",$sql,$o)&&sizeof($Result)>1)$Result = Array(Array("COUNT(DISTINCT(m.Id))"=>sizeof($Result)));
		//#DIRTY WORKAROUND 
		$Er = $GLOBALS["Systeme"]->Db[$O->Bdd]->errorInfo();
		if (DEBUG_MYSQL){
			//affichage des erreurs mysql
			if ($Er[0]!="00000")KError::Set('SQL ERROR '.Module::$LAST_QUERY,$sql.'<br />'.$Er[2],KError::$WARNING);
			elseif (DEBUG_MYSQL>=KError::$INFO) KError::Set('SQL INFO '.Module::$LAST_QUERY,$sql,KError::$INFO);
			
		}
		//if (DEBUG_ALL_BDD&&$Er[0]!="00000")echo "\r\nSQLITE ERROR ".Module::$LAST_QUERY."<br />\r\n".$sql."<br />\r\n".$Er[2]."<br />\r\n-------------------------------<br />\r\n";
		if (AUTO_COMPLETE_LANG&&$GLOBALS["Systeme"]->CurrentLanguage!=$GLOBALS["Systeme"]->DefaultLanguage&&!Sys::$User->Admin){
			foreach ($GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE") as $Cod=>$Lang) {
				if (isset($Lang["DEFAULT"])&&$Lang["DEFAULT"]) $DefautPref = $Cod;
			}
			if (is_array($Result)) for( $i=0; $i<sizeof($Result);$i++){
				foreach ($O->Proprietes as $Key=>$Prop){
					//Priorites de langage
					if (isset($O->Proprietes[$Key]["special"])&&$O->Proprietes[$Key]["special"]=="multi"&&$Result[$i][$Key]==""){
						$Result[$i][$Key] = $Result[$i][$DefautPref."-".$Key];
					}
				}
			}
		}
		return $Result;
	}

}
