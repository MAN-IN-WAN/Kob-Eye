<?php
class mysqlInherit{

	//----------------------------------------------//
	// HERITAGE					//
	//----------------------------------------------//
	//Recupere l'ensemble des propriï¿œtï¿œs ï¿œ hï¿œriter d'un objet faisant heriter
	function getHeritages($Id,$Recursive=0){
		if (!$this->Heritage||$Id=="") return false;
		//ON genere les elements sql qui vont permettre de generer les langues dieffrentes
		foreach ($GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE") as $Key=>$Lang) {
			if($GLOBALS["Systeme"]->DefaultLanguage==$Lang["TITLE"]||Sys::$User->Admin) {
				$Select.=",CONCAT('$Key') as Lang,v$Key.DefaultValue as `$Key-Value`,v$Key.Title as `$Key-Titre`";
				if ($Lang["DEFAULT"]) $Select.=",v$Key.DefaultValue as `Value`,v$Key.Title as `Titre`";
				$From .=",`".$this->Prefix.$this->titre."-inheritsLang-$Key` as v$Key";
				$Where.="AND(s.Id=v$Key.InhId)";
			}
		}
		//On recherche egalement sur les parents de l id pour voir
		if ($this->isReflexive()==1&&$Recursive){
			//ON recupere d abord les bords droite et bords gauche
			$sql2= "SELECT Bd,Bg FROM `".$this->Prefix.$this->titre."-Interval` WHERE Id='".$Id."'";
			$Intervals = mysqlFunctions::executeSql($O,$sql2,"SYS_SELECT");
			$From .=",`".$this->Prefix.$this->titre."-Interval` as Iv";
			$Where .="AND(s.ObjId=Iv.Id)AND(Iv.Bd>=".$Intervals[0]["Bd"].")AND(Iv.Bg<=".$Intervals[0]["Bg"].")";
		}else{
			//Dans le cas ou la donnï¿œe n est pas recursive
			$Where.="AND(ObjId=$Id)";
		}
		$sql= "SELECT s.Id,s.ObjId,s.Field,s.Order,s.Group,s.Type,s.Target,s.Level$Select FROM `".$this->Prefix.$this->titre."-inherits` as s$From WHERE 1 $Where ORDER BY s.Order";
		$Results = mysqlFunctions::executeSql($O,$sql,"SYS_SELECT");
		return $Results;
	}

	//Recupere les proprietes herites d' un objet heritant
	function getHeritagesValues($StockId){
		if (!$this->Heritage) return false;
		//ON genere les elements sql qui vont permettre de generer les langues dieffrentes
		foreach ($GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE") as $Key=>$Lang) {
			if($GLOBALS["Systeme"]->DefaultLanguage==$Lang["TITLE"]||Sys::$User->Admin) {
				if ($Lang["DEFAULT"]) $Select=",CONCAT('$Key') as Lang,s.Field as `Nom`,v$Key.Value as `Valeur`,v$Key.Title as `Titre`,v$Key.Title as `description`";
				else $Select=",CONCAT('$Key') as Lang,CONCAT('$Key') as Lang,CONCAT('$Key-',s.Field) as Nom,v$Key.Value as `Valeur`,v$Key.Title as `Titre`,v$Key.Title as `description`";
				$From =",`".$this->Prefix.$this->titre."-inheritsValuesLang-$Key` as v$Key";
				$Where="AND(s.Id=v$Key.InhId)";
				$sql= "SELECT s.Id as `NumHeritage`,s.ObjId as `ObjId`,s.Group as `Group`,s.Type as `Type`$Select FROM `".$this->Prefix.$this->titre."-inheritsValues` as s$From WHERE (";
				$Flag=false;
				if (is_array($StockId)) {
					foreach ($StockId as $IdObjet){
						if (strlen($IdObjet)>0) {
							if ($Flag) $sql.=" OR ";
							$sql.= " (s.ObjId=$IdObjet)";
							$Flag = true;
						}
					}
				}elseif ($StockId!=""){
					$sql.= " s.ObjId=$StockId";
					$Flag = true;
				}else return false;
				if (!$Flag) return false;
				$sql.= ")$Where GROUP BY s.Id ORDER BY s.Order ASC ";
				$TempResults = mysqlFunctions::executeSql($O,$sql,"SYS_SELECT");
				foreach ($TempResults as $Temp) $Results[] = $Temp;
			}
		}
		return $Results;
	}


	//Pour l instant specifique au module systeme mais permet d'hï¿œriter d'objets complets(ex menus)
	function heritageHierarchique($id,$Recherche,$TypeParent="?"){
		$Results=Array();
		if ($TypeParent=="?") $TypeParent = $this->Etrangeres[$Recherche."Id"]["inherit"];
		$Order = $GLOBALS["Systeme"]->Modules[$this->Module]->Db->Order;
		$Parent = $GLOBALS["Systeme"]->Modules[$this->Module]->callData($TypeParent."/".$Recherche."/".$id);
		if (is_array($Parent)){
			foreach ($Parent as $Par){
				$Results2 = $GLOBALS["Systeme"]->Modules[$this->Module]->callData($TypeParent."/".$Par["Id"]."/".$this->titre,"",0,100,$Order[1],$Order[0]);
				/* 28.08.2007 --> Fred
				 Ajout d'une recursivite pour heritage hierarchique.
				 Non testee pour l'instant, non exempt de bugs.*/
				$num = $GLOBALS["Systeme"]->Modules[$this->Module]->Db->findByTitle($TypeParent);
				if ($GLOBALS["Systeme"]->Modules[$this->Module]->Db->ObjectClass[$num]->isReflexive())
				{
					$Results3 = $GLOBALS["Systeme"]->Modules[$this->Module]->Db->ObjectClass[$num]->getHeritageHierarchique($Par["Id"],$TypeParent,$TypeParent);
				}
				for($i=0;$i<count($Results2);$i++)
				{
					if(!empty($Results2[$i]))
					{
						$Results2[$i]["Delegation"] = 1;
					 $Results[] = $Results2[$i];
					}
				}
				for($i=0;$i<count($Results3);$i++)
				{
					if(!empty($Results3[$i]))
					{
						$Results3[$i]["Delegation"] = 1;
					 $Results[] = $Results3[$i];
					}
				}
			}
			return $Results;
		}else return false;
	}
	//Creation d'un heritage
	function addInherit($Infos){
		//Ajoute une regle d'heritage a l'objet
		$sql = 'INSERT INTO `'.$this->Prefix.$this->titre.'-inherits` ';
		$sql.= 'VALUES ("","'.$Infos["NomPropriete"].'",'.$Infos["Id"].', "'.$Infos["Order"].'","'.$Infos["TypePropriete"].'","'.$Infos["Enfant"].'","'.$Infos["Group"].'","'.$Infos["Level"].'")';
		$Result = mysqlFunctions::executeSql($O,$sql,"INSERT");
		//Recuperation de l id de l heritage pour la propagation
		$sql = 'SELECT Id FROM `'.$this->Prefix.$this->titre.'-inherits` ';
		$sql.= 'WHERE  ObjId="'.$Infos["Id"].'" AND `Field`="'.$Infos["NomPropriete"].'" AND `Order`="'.$Infos["Order"].'" AND `Group`="'.$Infos["Group"].'" AND `Type`="'.$Infos["TypePropriete"].'" AND `Target`="'.$Infos["Enfant"].'" AND `Level`="'.$Infos["Level"].'"';
		$Result = mysqlFunctions::executeSql($O,$sql,"SELECT_SYS");
		$InhId = $Result[0]["Id"];
		//Ajout des valeurs pas defaut sur toutes les donnï¿œes enfants par recursivitï¿œ
		$this->propagateCreateInheritedValue($Infos["Enfant"],$InhId,$Infos["Id"],$this->titre,$Infos["NomPropriete"],$Infos["Order"],$Infos["TypePropriete"],$Infos["Group"]);
		return $Infos;
	}

	function propagateCreateInheritedValue($Cible,$InhId,$ObjId,$ObjClass,$Field,$Order,$Type,$Group) {
		//Construction de la requete
		$Req = $this->Module."/".$this->titre."/".$ObjId."/*/".$Cible;
		$Result=$GLOBALS["Systeme"]->Modules[$this->Module]->callData($Req);
		if (is_array($Result))foreach ($Result as $Obj) {
			$this->insertInheritedValue($Obj["Id"],$InhId,$ObjClass,$Cible,$Field,$Order,$Type,$Group);
		}
	}

	//INSERTION D UNE VALEUR SUR UN OBJET QUI HERITE
	function createInheritedValue($Tab){
		$sql = 'SELECT Id FROM `'.$this->Prefix.$Tab["Target"].'-inheritsValues` WHERE `Field`="'.$Tab["Field"].'" AND `InhId`="'.$Tab["Id"].'" AND ObjClass="'.$Tab["ObjectClass"].'" AND ObjId="'.$Tab["ObjId"].'"';
		$Result = mysqlFunctions::executeSql($O,$sql,"SELECT_SYS");
		$InhIdTemp = $Result[0]["Id"];
		if (!$InhIdTemp!="") {
			$this->insertInheritedValue($Tab["ObjId"],$Tab["Id"],$Tab["ObjectClass"],$Tab["Target"],$Tab["Field"],$Tab["Order"],$Tab["Type"],$Tab["Group"]);
			//Recuperation des Ids des proprietes existantes pour chaques objets
			$sql = 'SELECT Id FROM `'.$this->Prefix.$Tab["Target"].'-inheritsValues` WHERE `Field`="'.$Tab["Field"].'" AND `InhId`="'.$Tab["Id"].'" AND ObjClass="'.$Tab["ObjectClass"].'" AND ObjId="'.$Tab["ObjId"].'"';
			$Result = mysqlFunctions::executeSql($O,$sql,"SELECT_SYS");
			$InhIdTemp = $Result[0]["Id"];
		}
		$this->insertInheritedValueLang($Tab["ObjId"],$Tab["Titre"],$Tab["Value"],$Tab["Langue"],$InhIdTemp,$Tab["Target"],$Tab["Group"]);
	}

	function insertInheritedValue($ObjId,$InhId,$ObjClass,$Cible,$Field,$Order,$Type,$Group){
		//Insere une valeur de propriete heritee
		$sql = "INSERT INTO `".$this->Prefix.$Cible."-inheritsValues` SET ObjId=\"".$ObjId."\",InhId=\"".$InhId."\" ,ObjClass=\"".$ObjClass."\",`Field`=\"".$Field."\",`Order`=\"".$Order."\",`Type`=\"".$Type."\",`Group`=\"".$Group."\"";
		$Result = mysqlFunctions::executeSql($O,$sql,"INSERT");
	}


	//Creation d'un heritage
	function addInheritValue($Infos){
		//ON recupere l id de la propriete heritï¿œe
		$sql = 'SELECT Id,Target FROM `'.$this->Prefix.$this->titre.'-inherits` WHERE `Field`="'.$Infos["Field"].'" AND `ObjId`="'.$Infos["Id"].'"';
		$Result = mysqlFunctions::executeSql($O,$sql,"SELECT_SYS");
		$InhId = $Result[0]["Id"];
		$Cible = $Result[0]["Target"];
		//Ajoute une regle d'heritage a l'objet pour la langue en question
		$sql = 'INSERT INTO `'.$this->Prefix.$this->titre.'-inheritsLang-'.$Infos["Lang"].'` ';
		$sql.= 'VALUES ("",'.$InhId.',"'.$Infos["Titre"].'","'.addslashes($Infos["Value"]).'")';
		$Result = mysqlFunctions::executeSql($O,$sql,"INSERT");
		//Ajout des valeurs pas defaut sur toutes les donnï¿œes enfants par recursivitï¿œ
		$this->propagateCreateInheritedValueLang($Cible,$Infos["Value"],$InhId,$Infos["Lang"],$Infos["Id"],$this->titre,$Infos["Field"],$Infos["Titre"],$Infos["Group"]);
		return $Infos;
	}
	function propagateCreateInheritedValueLang($Cible,$Value,$InhId,$Lang,$ObjId,$ObjClass,$Field,$Titre,$Group) {
		//Construction de la requete
		$Req = $this->Module."/".$this->titre."/".$ObjId."/*/".$Cible;
		$Result=$GLOBALS["Systeme"]->Modules[$this->Module]->callData($Req);
		if (is_array($Result))foreach ($Result as $Obj) {
			//Recuperation des Ids des proprietes existantes pour chaques objets
			$sql = 'SELECT Id FROM `'.$this->Prefix.$Cible.'-inheritsValues` WHERE `Field`="'.$Field.'" AND `InhId`="'.$InhId.'" AND ObjClass="'.$this->titre.'" AND ObjId="'.$Obj["Id"].'"';
			$Result = mysqlFunctions::executeSql($O,$sql,"SELECT_SYS");
			$InhIdTemp = $Result[0]["Id"];
			$this->insertInheritedValueLang($Obj["Id"],$Titre,$Value,$Lang,$InhIdTemp,$Cible,$Group);
		}
	}



	function insertInheritedValueLang($ObjId,$Titre,$Value,$Lang,$InhId,$Cible,$Group){
		//Insere une valeur de propriete heritee
		$sql = "INSERT INTO `".$this->Prefix.$Cible."-inheritsValuesLang-$Lang` SET InhId=\"".$InhId."\" ,`Value`=\"".addslashes($Value)."\",`Title`=\"".$Titre."\"";
		// 		echo $sql."\r\n";
		$Result = mysqlFunctions::executeSql($O,$sql,"INSERT");
	}


	function changeHeritage($Heritage,$Id){
		//Il manque La valeur par defaut
		$sql = "UPDATE `".$this->Prefix.$this->titre."-inherits` SET `Field`=\"".$Heritage["Field"]."\", `Target`=\"".$Heritage["Target"]."\", `Type`=\"".$Heritage["Type"]."\", `Group`=\"".$Heritage["Group"]."\", `Level`=\"".$Heritage["Level"]."\", `Order`=\"".$Heritage["Order"]."\" , `ObjId`=\"".$Heritage["ObjId"]."\"  WHERE Id=".$Id."";
		$Results = mysqlFunctions::executeSql($O,$sql,"UPDATE");
		//Ajout des valeurs pas defaut sur toutes les donnï¿œes enfants par recursivitï¿œ
		$this->propagateUpdateInherited($Heritage["Target"],$Id,$Heritage["ObjId"],$this->titre,$Heritage["Field"],$Heritage["Order"],$Heritage["Type"],$Heritage["Group"]);
	}

	function propagateUpdateInherited($Cible,$InhId,$ObjId,$ObjClass,$Field,$Order,$Type,$Group) {
		//Construction de la requete
		$Req = $this->Module."/".$this->titre."/".$ObjId."/*/".$Cible;
		$Result=$GLOBALS["Systeme"]->Modules[$this->Module]->callData($Req);
		foreach ($Result as $Obj) {
			$this->updateInherited($InhId,$Obj["Id"],$ObjClass,$Cible,$Field,$Order,$Type,$Group);
		}
	}

	function updateInherited($InhId,$ObjId,$ObjClass="",$Cible="",$Field="",$Order="",$Type="",$Group=""){
		//Insere une valeur de propriete heritee
		if ($Cible=="")$Cible=$this->titre;
		$sql = "UPDATE `".$this->Prefix.$Cible."-inheritsValues` SET  ";
		if ($Field!="")  $sql.=" `Field`='".$Field."'";
		if ($Field&&$Order!="") {$sql.=",";$flag=true;}
		if ($Order!="")  $sql.=" `Order`='".$Order."'";
		if ($Type!="") {$sql.=",";$flag=true;}
		if ($Type!="")  $sql.=" `Type`='".$Type."'";
		if ($Group!=""||$flag) {$sql.=",";$flag=true;}
		if ($Group!="")  $sql.=" `Group`='".$Group."'";
		if ($Group!=""&&$ObjClass!="") {$sql.=",";$flag=true;}
		if ($ObjClass!="")  $sql.=" ObjClass='".$ObjClass."'";
		$sql.= " WHERE ObjId=".$ObjId." AND InhId=".$InhId;
		$Result = mysqlFunctions::executeSql($O,$sql,"UPDATE");
	}

	function changeHeritageValue($Heritage,$Id){
		//Il manque La valeur par defaut
		$sql.= "SELECT s.Target,l.DefaultValue from `".$this->Prefix.$this->titre."-inherits` as s,`".$this->Prefix.$this->titre."-inheritsLang-".$Heritage["Lang"]."` as l WHERE s.Id=$Id AND l.InhId=s.Id";
		$Results = mysqlFunctions::executeSql($O,$sql,"SYS_SELECT");
		$Cible = $Results[0]["Target"];
		$OldValue = $Results[0]["DefaultValue"];
		$sql = "UPDATE `".$this->Prefix.$this->titre."-inheritsLang-".$Heritage["Lang"]."` SET  `Title`=\"".$Heritage["Titre"]."\", `DefaultValue`=\"".addslashes($Heritage["Value"])."\"  WHERE InhId=".$Id."";
		// 		echo $sql."\r\n";
		$Results = mysqlFunctions::executeSql($O,$sql,"UPDATE");
		//Ajout des valeurs pas defaut sur toutes les donnï¿œes enfants par recursivitï¿œ
		$this->propagateUpdateInheritedValue($Cible,$Id,$Id,$this->titre,$Heritage["Value"],$Heritage["Lang"],$OldValue);
	}

	function propagateUpdateInheritedValue($Cible,$InhId,$ObjClass,$Value,$Lang,$Titre,$OldValue) {
		//Construction de la requete
		$sql.= "SELECT `Id` from `".$this->Prefix.$Cible."-inheritsValues` WHERE InhId=".$InhId;
		$Results = mysqlFunctions::executeSql($O,$sql,"SYS_SELECT");
		if (is_array($Result))foreach ($Result as $Obj) {
			$this->verifyDefaultInheritedValue($Id,$Cible,$Obj["Id"],$ObjClass,$Value,$Lang,$Titre,$OldValue);
		}
	}

	//On verifie qu il n existe pas deja une valeur avant d appliquer le changement de valeur par defaut
	function verifyDefaultInheritedValue($Id,$Cible,$InhId,$ObjClass,$Value,$Lang,$Titre,$OldValue){
		$sql.= "SELECT `Value` from `".$this->Prefix.$Cible."-inheritsValues-$Lang` WHERE InhId=$InhId";
		$Results = mysqlFunctions::executeSql($O,$sql,"SYS_SELECT");
		if ($Results[0]["Value"]!=$OldValue) $ValueTemp=""; else $ValueTemp = $Value;
		$this->updateInheritedValue($InhId,$ValueTemp,$Cible,$Lang,$Titre);
	}


	function updateInheritedValue($InhId,$Value,$Cible,$Lang,$Titre=""){
		//Insere une valeur de propriete heritee
		if ($Cible=="")$Cible=$this->titre;
		$sql = "UPDATE `".$this->Prefix.$Cible."-inheritsValuesLang-$Lang` SET  ";
		$sql.=" `Value`='".addslashes($Value)."'";
		if ($Titre!="")$sql.=", `Title`='".$Titre."'";
		$sql.= " WHERE InhId=".$InhId;
		$Result = mysqlFunctions::executeSql($O,$sql,"UPDATE");
	}

	function removeHeritage($IdHeritage){
		//On recupere d abord la cible de l heritage
		$sql.= "SELECT `Target` from `".$this->Prefix.$this->titre."-inherits` WHERE Id=$IdHeritage ";
		$Cible = mysqlFunctions::executeSql($O,$sql,"SYS_SELECT");
		$Cible = $Cible[0]["Target"];
		foreach ($GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE") as $Key=>$Lang) {
			$Select .=",`".$this->Prefix.$this->titre."-inheritsLang-$Key`";
			$From .=",`".$this->Prefix.$this->titre."-inheritsLang-$Key`";
			$Where.="AND(`".$this->Prefix.$this->titre."-inherits`.Id=`".$this->Prefix.$this->titre."-inheritsLang-$Key`.InhId)";
		}
		$sql = "DELETE `".$this->Prefix.$this->titre."-inherits` $Select FROM `".$this->Prefix.$this->titre."-inherits` $From WHERE (`".$this->Prefix.$this->titre."-inherits`.Id=$IdHeritage)$Where";
		$Results = mysqlFunctions::executeSql($O,$sql,"REMOVE");
		$this->removeHeritageValue($IdHeritage,$Cible);
	}

	function removeHeritageValue($IdHeritage,$Cible){
		//On recupere d abord la cible de l heritage
		foreach ($GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE") as $Key=>$Lang) {
			$Select .=",`".$this->Prefix.$Cible."-inheritsValuesLang-$Key`";
			$From .=" , `".$this->Prefix.$Cible."-inheritsValuesLang-$Key`";
			$Where.="AND(`".$this->Prefix.$Cible."-inheritsValues`.Id=`".$this->Prefix.$Cible."-inheritsValuesLang-$Key`.InhId)";
		}
		$sql = "DELETE `".$this->Prefix.$Cible."-inheritsValues` $Select FROM `".$this->Prefix.$Cible."-inheritsValues` $Select WHERE (`".$this->Prefix.$Cible."-inheritsValues`.InhId=$IdHeritage)$Where";
		$Results = mysqlFunctions::executeSql($O,$sql,"REMOVE");
		$sql = "DELETE FROM `".$this->Prefix.$Cible."-inheritsValues`WHERE (InhId=$IdHeritage)";
		$Results = mysqlFunctions::executeSql($O,$sql,"REMOVE");
	}

	//Verifie l'existence des tables d'heritages pour cet objectClass
	function verifyInheritStruct() {
		$Result=true;
		//ON verifie l existence de la table qui va porter les champs
		$sql = "SHOW TABLES LIKE '`".$this->Prefix.$this->titre."-inherits`' ";
		$test=mysqlFunctions::executeSql($O,$sql,"VERIF");
		if (sizeof($test)&&$Result) $Result=true; else $Result=false;
		//ON verifie l existence de la table qui va porter les valeurs
		$sql = "SHOW TABLES LIKE '`".$this->Prefix.$this->titre."-inheritsValues`' ";
		$test=mysqlFunctions::executeSql($O,$sql,"VERIF");
		if (sizeof($test)&&$Result) $Result=true; else $Result=false;
		foreach ($GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE") as $Key=>$Lang) {
			$Suffix="-".$Key;
			$sql = "SHOW TABLES LIKE '`".$this->Prefix.$this->titre."-inheritsLang$Suffix`' ";
			$test=mysqlFunctions::executeSql($O,$sql,"VERIF");
			if (sizeof($test)&&$Result) $Result=true; else $Result=false;
			$sql = "SHOW TABLES LIKE '`".$this->Prefix.$this->titre."-inheritsValuesLang$Suffix`' ";
			$test=mysqlFunctions::executeSql($O,$sql,"VERIF");
			if (sizeof($test)&&$Result) $Result=true; else $Result=false;
		}
		return $Result;
	}

	//Cree les tables d'heritages pour cet objetClass
	function createInheritStruct() {
		$sql  = 'CREATE TABLE IF NOT EXISTS `'.$this->Prefix.$this->titre.'-inherits` (';
		$sql.= '`Id` INT NOT NULL AUTO_INCREMENT,';
		$sql.= '`Field` VARCHAR(30) NOT NULL,';
		$sql.= '`ObjId` INT NOT NULL,';
		$sql.= '`Order` INT NOT NULL,';
		$sql.= '`Type` VARCHAR(30) NOT NULL,';
		$sql.= '`Target` VARCHAR(30) NOT NULL,';
		$sql.= '`Group` VARCHAR(30) NOT NULL,';
		$sql.= '`Level` INT NOT NULL,';
		$sql.= ' UNIQUE (`Id`) )';
		$test=mysqlFunctions::executeSql($O,$sql,"CHECK");
		$sql  = 'CREATE TABLE IF NOT EXISTS `'.$this->Prefix.$this->titre.'-inheritsValues` (';
		$sql.= '`Id` INT NOT NULL AUTO_INCREMENT,';
		$sql.= '`Field` VARCHAR(30) NOT NULL,';
		$sql.= '`ObjId` INT NOT NULL,';
		$sql.= '`InhId` INT NOT NULL,';
		$sql.= '`Order` INT NOT NULL,';
		$sql.= '`Group` VARCHAR(30) NOT NULL,';
		$sql.= '`Type` VARCHAR(30) NOT NULL,';
		$sql.= '`ObjClass` VARCHAR(30) NOT NULL,';
		$sql.= ' UNIQUE (`Id`) )';
		$test=mysqlFunctions::executeSql($O,$sql,"CHECK");
		foreach ($GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE") as $Key=>$Lang) {
			$Suffix="-".$Key;
			$sql  = 'CREATE TABLE IF NOT EXISTS `'.$this->Prefix.$this->titre.'-inheritsLang'.$Suffix.'` (';
			$sql.= '`Id` INT NOT NULL AUTO_INCREMENT,';
			$sql.= '`InhId` INT NOT NULL,';
			$sql.= '`Title` VARCHAR(100) NOT NULL,';
			$sql.= '`DefaultValue` text,';
			$sql.= ' UNIQUE (`Id`) )';
			$test=mysqlFunctions::executeSql($O,$sql,"CHECK");
			$sql  = 'CREATE TABLE IF NOT EXISTS `'.$this->Prefix.$this->titre.'-inheritsValuesLang'.$Suffix.'` (';
			$sql.= '`Id` INT NOT NULL AUTO_INCREMENT,';
			$sql.= '`InhId` INT NOT NULL,';
			$sql.= '`Title` VARCHAR(100) NOT NULL,';
			$sql.= '`Value` text,';
			$sql.= ' UNIQUE (`Id`) )';
			$test=mysqlFunctions::executeSql($O,$sql,"CHECK");
		}
	}
}
