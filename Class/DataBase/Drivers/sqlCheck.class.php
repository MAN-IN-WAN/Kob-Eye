<?php
class sqlCheck{
	//Variables

	static function Needed() {return  Array("Id"=> "int", "tmsCreate"=> "int","userCreate"=> "int","tmsEdit"=> "int","userEdit"=>"int","uid"=>"int","gid"=>"int","gmod"=>"int","umod"=>"int","omod"=>"int","Display"=>"boolean");}
	static function tabType() {return  Array("int","string","varchar","boolean","password","image","order","date","text","longtext");}
	//----------------------------------------------//
	// VERIFICATION									//
	//----------------------------------------------//
	/**
	* initData
	* Fonction associee a Check qui verifie les donnees entre sql et le schema
	* Si elle n'existe pas on la cree, et si la creation ne marche pas on renvoie une erreur
	*/
	static function initData ($O) {
		if (!sqlCheck::getStructureSql($O)) sqlCheck::generateSqlTable($O);
		if ($O->driver=="mysqlDriver"){
			//Force Engine
			sqlCheck::forceEngine($O);
		}
		//Verification des associations
		sqlCheck::Compare($O);
		//Verification des liaisons
		sqlCheck::CheckKeys($O);
		//On teste si l objectClass est reflexif
		if ($O->isReflexive() && !sqlInterval::verifyIntervalIndex($O)) {
			//Dans ce cas on met en place les index intervallaires
			sqlInterval::createIntervalIndex($O);
		}
		//On verifie les index
		sqlCheck::verifyIndex($O);
	}
	/**
	* getStructureSql
	* Renvoie la structure SQL
	*/
	static function getStructureSql($O) {
		//Verification ObjectClass
		if (!$O->getTableStructure($O->Prefix.$O->titre))return false;
		//Verification Association
		if (is_array($O->Associations))foreach ($O->Associations as $A){
			if ($A->isLong() && $A->isChild($O->titre)){
				if (!$O->getTableStructure($O->Prefix.$O->titre.$A->titre))return false;
			}
		}
		return true;
	}
	/**
	* generateSqlTable
	* Methode qui cree les tables SQL correspondants au contenu de la classe.
	*/
	static function generateSqlTable($O) {
		$Flag=false;
		$sql  = 'CREATE TABLE IF NOT EXISTS `'.$O->Prefix.$O->titre.'` (';
		$sql.= '`Id` INTEGER PRIMARY KEY '.$O->AUTO_INCREMENT.',';
		$sql.= '`tmsCreate` INT NOT NULL,';
		$sql.= '`userCreate` INT NOT NULL,';
		$sql.= '`tmsEdit` INT NOT NULL,';
		$sql.= '`userEdit` INT NOT NULL,';
		$sql.= ' uid int(11) default NULL,';
		$sql.= ' gid int(11) default NULL,';
		$sql.= ' umod int(11) default NULL,';
		$sql.= ' gmod int(11) default NULL,';
		$sql.= ' omod int(11) default NULL,';
		//Puis on insere les proprietes
		foreach ($O->Proprietes as $Key=>$Prop){
			//D'abord le nom
			if (empty($Prop["Ref"])){
				if ($Key!="Id") if ($Flag) $sql.= ' ,';
				if ($Key!="Id") $sql.= sqlCheck::writeProperty($Key,"",$O);
				$Flag = true;
			}
		}
		//Ensuite, on insere les clefs etrangeres
		foreach ($O->getParent() as $A){
			if ($A->isShort()) {
				if ($Flag) $sql.= ',';
				$sql .= sqlCheck::writeForeignKey($A,$O);
				$Flag = true;
			}
			/*if ($A->isLong()&&$A->Module==$O->Module) {
				sqlCheck::generateForeignKeySqlTable($A,$O);
			}*/
		}
		//Ensuite, on insere les clefs etrangeres inter modules
		/*foreach ($O->getChild() as $A){
			if ($A->isLong()&&$A->Module==$O->Module&&$A->isInterModule()) {
				sqlCheck::generateForeignKeySqlTable($A,$O);
			}
		}*/
		//On insere la clef primaire et on finit la requete SQL
		$sql.= ' )';
		//On execute la requete: si elle s'est bien passe = 1, sinon =0
		if ($O->executeSql($O,$sql,"CREATE")) return sqlCheck::fillTable($O);
		else{
// 			$GLOBALS['Systeme']->Error->sendWarningMsg(2,"ERREUR 2 :RENAMESQL");
			return false;
		}
	}
	/**
	* generateSqlTable
	* Methode qui cree les tables SQL correspondants au contenu de la classe.
	*/
	static function forceEngine($O) {
		$Flag=false;
		//recupération du moteur de base par défaut
		if (defined('MYSQL_ENGINE'))$Engine = MYSQL_ENGINE;
		else $Engine = "MyISAM";
		sqlCheck::forceEngineTable($O,$O->Prefix.$O->titre, $Engine);
		
		//Les tabels d'intervalle
		if ($O->isReflexive()){
			sqlCheck::forceEngineTable($O,$O->Prefix.$O->titre.'-Interval', $Engine);
		}
		//Ensuite, on insere les clefs etrangeres
		foreach ($O->getParent() as $A){
			if ($A->isLong()&&$A->Module==$O->Module) {
				sqlCheck::forceEngineTable($O,$O->Prefix.$O->titre.$A->titre,$Engine);
			}
		}
		//Ensuite, on insere les clefs etrangeres inter modules
		foreach ($O->getChild() as $A){
			if ($A->isLong()&&$A->Module==$O->Module&&$A->isInterModule()) {
				sqlCheck::forceEngineTable($O,$O->Prefix.$O->titre.$A->titre,$Engine);
			}
		}
	}
	static function forceEngineTable($O,$tablename,$Engine){
		$sql  = 'ALTER TABLE `'.$tablename.'` ENGINE='.$Engine.';';
		$O->executeSql($O,$sql,"CREATE");
	}
	/**
	* generateForeignKeySqlTable
	* Génération de la table de clef etrangère.
	* @param Assocation Association à générer
	* @param ObjectClass ObjectClass enfant
	*/
	static function generateForeignKeySqlTable($A,$O) {
		$Flag=false;
		$sql  = 'CREATE TABLE IF NOT EXISTS `'.$O->Prefix.$A->getTable().'` (';
		$sql.= '`Id` INTEGER PRIMARY KEY '.$O->AUTO_INCREMENT.',';
		$sql.= '`tmsCreate` INT NOT NULL,';
		$sql.= '`userCreate` INT NOT NULL,';
		$sql.= '`tmsEdit` INT NOT NULL,';
		$sql.= '`userEdit` INT NOT NULL,';
		$sql.= ' uid int(11) default NULL,';
		$sql.= ' gid int(11) default NULL,';
		$sql.= ' umod int(11) default NULL,';
		$sql.= ' gmod int(11) default NULL,';
		$sql.= ' omod int(11) default NULL,';
		//Ecriture du lien parent
		$sql.= '`'.$A->getField().'` INT NOT NULL DEFAULT 0,';
		//Ecriture du lien enfant
		$Fk = $A->getChildObjectClass();
		$sql.= '`'.$Fk->titre.'` INT NOT NULL DEFAULT 0';
		//On insere la clef primaire et on finit la requete SQL
		$sql.= ' )';
		//On execute la requete: si elle s'est bien passe = 1, sinon =0
		if ($O->executeSql($O,$sql,"CREATE")) return sqlCheck::fillTable($O);
		else return false;
	}
	/**
	* Compare
	*Compare sql et le schema
	*/
	static function Compare($O) {
		$erreur = false;
		//On compare les proprietes et les clefs etrangeres
		if (sqlCheck::compareProperties($O) || sqlCheck::compareFKeys($O)){
			sqlCheck::getStructureSql($O);
			if (sqlCheck::compareProperties($O) || sqlCheck::compareFKeys($O)){
				$erreur = true;
			}
		}
		//On teste si l objectClass est reflexif
		if ($O->isReflexive() && !sqlInterval::verifyIntervalIndex($O)) {
			//Dans ce cas on met en place les index intervallaires
			sqlInterval::createIntervalIndex($O);
		}
		//On verifie les index
		sqlCheck::verifyIndex($O);
		return $erreur;
	}

	/**
	* verifyIndex
	* Verification de l'exitence des index sur la clef primaire ainsi que sur les clefs etrangeres
	*/
	static function verifyIndex($O) {
		$list = $O->getIndexList();
		foreach ($O->Associations as $e){
			if ($e->isChild($O->titre)&&$e->isShort()){
				if (!in_array($e->titre,$list)){
					sqlCheck::createIndex($O,$e);
				}
			}
		}
		if ($O->isReflexive()){
			$x = new stdClass();
			$x->titre = "Id";
			//creation d'un index sur le champ Id de la table d'intervalle.
			if (!in_array($x->titre,$list)){
				sqlCheck::createIndex($O,$x,'-Interval');
			}
		}
		//verification des contraintes
/*		foreach ($O->Associations as $e){
			if ($e->isChild($O->titre)&&$e->isShort()){
				if (!in_array($e->titre,$list)){
					sqlCheck::createConstraint($O,$e);
				}
			}
		}*/
	}
	/**
	* CreateIndex
	* Creation des index 
	*/
	static function createIndex($O,$e,$opt="") {
		$sql = "create index `".sqlFunctions::getTableName($O).$opt."_".$e->titre."` on `".sqlFunctions::getTableName($O).$opt."` (".$e->titre.")";
		$O->executeSql($O,$sql,"INDEX");
	}
	/**
	* CreateConstraint
	* Creation des contraintes 
	*/
	static function createConstraint($O,$e) {
		//$sql = "create index `".sqlFunctions::getTableName($O)."_".$e->titre."` on `".sqlFunctions::getTableName($O)."` (".$e->titre.")";
		//$O->executeSql($O,$sql,"INDEX");
	}
	/**
	* CreateData
	* Renvoie la structure de la table et si elle echoue appelle la creation dune nouvelle table.
	*/
	static function createData($O) {
		//$sql = "select column_name from information_schema.columns where table_name=`".$O->Prefix.$O->titre."`";
		//$sql="SHOW COLUMNS FROM `".$O->Prefix.$O->titre."`";
		sqlCheck::generateSqlTable($O);
	}
	/**
	* createNewField
	* Cree un nouveau champ: 'propriete' ou clef 'etrangere' selon le type mentionne.
	*/
	static function createNewField ($Nom,$type,$Prefixe,$O) {
		$sql = 'ALTER TABLE `'.$O->Prefix.$O->titre.'` ADD ';
		if ($type == 'propriete') $sql .= sqlCheck::writeProperty($Nom,$Prefixe,$O);
		if ($type == 'systeme') $sql .= sqlCheck::writeSysProperty($Nom,$Prefixe,$O);
		//Si la requete est OK, on renvoie true et un message. Sinon, on renvoie false et une erreur.
		if ($O->executeSql($O,$sql,"MODIF")) return true;
		else return false;
	}
	/**
	* createNewField
	* Cree un nouveau champ: 'propriete' ou clef 'etrangere' selon le type mentionne.
	*/
	static function createNewForeignKey ($A,$O) {
		$sql = 'ALTER TABLE `'.$O->Prefix.$O->titre.'` ADD ';
		$sql .= sqlCheck::writeForeignKey($A,$O);
		//Si la requete est OK, on renvoie true et un message. Sinon, on renvoie false et une erreur.
		if ($O->executeSql($O,$sql,"MODIF")) return true;
		else return false;
	}
	/**
	* writeProperty
	* Cette methode pre-ecrit les requetes SQL au sujet des proprietes
	*/
	static function writeProperty($Nom,$Prefixe,$O,$P="") {
		if (!isset($O->Proprietes[$Nom])&&!is_array($P)) return;
		if (!is_array($P))$Tab= sqlCheck::initSpecialTypes($O->Proprietes[$Nom],$O);
		else $Tab= sqlCheck::initSpecialTypes($P,$O);
		if ($Prefixe != NULL) $sql= '`'.$Prefixe.'-'.$Nom.'` ';
		else $sql= '`'.$Nom.'` ';
		$sql.= ($Tab['type'] == 'varchar') ? $Tab['type'].'('.(($Tab['length']!="")?$Tab['length']:"255").') ': $Tab['type'].' ';
		//La possibilite d'etre vide:
		if (isset($Tab["null"])&&$Tab['null'] == "false") $sql.='NOT';
		$sql.=' NULL';
		//La valeur par defaut:
		if (isset($Tab['default'])&&$Tab['default']!=""&&sizeof(explode("::",$Tab['default']))==1) $sql.= ' default "'.$Tab['default'].'"';
		return $sql;
	}
	/**
	* writeSysProperty
	* Cette methode pre-ecrit les requetes SQL au sujet des proprietes
	*/
	static function writeSysProperty($Nom,$Prefixe,$O) {
		$Needed = sqlCheck::Needed($O);
		$Tab=$Needed[$Nom];
		$sql= '`'.$Nom.'` ';
		//Le type et la longueur:
		$sql.= $Tab;
		//La valeur par defaut:
		return $sql;
	}
	/**
	* writeForeignKey
	* Cette methode pre-ecrit les requetes SQL au sujet des clefs secondaires
	*/
	static function writeForeignKey($A,$O) {
		//ANalyse de data
		if ($A->getTarget()!="Id"){
			$Fk = $A->getParentObjectClass();
			$P = $Fk->Proprietes[$A->getTarget()];
			$sql = sqlCheck::writeProperty($A->titre,'',$O,$P);
		}else{
			//On leur donne un nom
			$sql= '`'.$A->titre.'`';
			//On leur donne un NULL correspondant a leur cardinalites
			$sql.= 'INT NOT NULL DEFAULT 0';
		}
		return $sql;
	}
	/**
	* alterSql
	* Cette methode met a jour les tables MySql, colonne par colonne.
	*/
	static function alterSql ($nom, $type,$O) {
		if( $O instanceof sqliteDriver) {
			// En SQlite le type de champ ne compte pas, donc inutile de le modifier
			return true;
		}
		$sql = 'ALTER TABLE `'.$O->Prefix.$O->titre.'` MODIFY ';
		//Si c'est une clef etrangere, on precise juste son nom
		switch($type){
			case "prop":
				$sql.= sqlCheck::writeProperty($nom,'',$O);
			break;
			case "sys":
				$sql.= sqlCheck::writeSysProperty($nom,$O);
			break;
			default:
				return false;
			break;
		}
		if ($O->executeSql($O,$sql,"MODIF")) return true;
		else return false;
	}
	/**
	* compareProperties
	* Compare les proprietes du tableau avec celles de la table
	*/
	static function compareProperties($O) {
		//On charge le tableau qui contient les conversions possibles
		$Langues = $GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE");
		$conversion['varchar'] = 'text';
		$conversion['boolean'] = 'int';
		$conversion['int']= 'varchar';
		//On verifie les proprietes systemes
		$Needed = sqlCheck::Needed($O);
		foreach($Needed as $Nom => $Prop){
			if (@!array_key_exists($Nom,$O->tableSql)) {
				//OK
// 				echo "<li>--#> On cree un nouveau champ $Nom </li>";
				sqlCheck::createNewField($Nom, 'systeme','',$O);
			}
		}
		foreach($O->Proprietes as $Nom => $Prop){
			$Prop = sqlCheck::initSpecialTypes($Prop,$O);
			if (!empty($Prop["Ref"])) continue;
			//Compteur de changement
			$noChange = 0;
			echo "<li>$Nom-----------------------</li>\r\n";
			//Compteur de creation
			if (isset($O->tableSql)&&is_array($O->tableSql))if (array_key_exists($Nom,$O->tableSql)) {
				//Le type:
				if ((!isset($Prop['type'])&&!isset($O->tableSql[$Nom]['type']))||(isset($Prop['type'])&&isset($O->tableSql[$Nom]['type'])&&$Prop['type'] == $O->tableSql[$Nom]['type'])) $noChange++;
				//La longueur:
				if ((!isset($Prop['length'])||!isset($O->tableSql[$Nom]['length']))||$Prop['length'] == $O->tableSql[$Nom]['length']) $noChange++; else {
// 					echo "ERREUR LONGUEUR ".$Prop['length']." != ".$O->tableSql[$Nom]['length']."<br />\r\n";
				}
				//La valeur par defaut:
				if ((!isset($Prop['default'])&&!isset($O->tableSql[$Nom]['default']))||$Prop['default'] ==  $O->tableSql[$Nom]['default']) $noChange++;
			}else{
// 				echo "<li>--##> On cree un nouveau champ $Nom </li>";
				sqlCheck::createNewField($Nom, 'propriete','',$O);
				//die("REFRESH PLEASE ...");
				//return true;
			}
			//Cas du changement de langue
			foreach ($Langues as $Prefixe=>$Langue){
				if (isset($Prop["special"])&&$Prop["special"]=="multi"){
					if (!@array_key_exists($Prefixe."-".$Nom,$O->tableSql)&&(!isset($Langue["DEFAULT"])||$Langue["DEFAULT"]!=1)) {
// 						echo "<li>NEW FIELD $Prefixe-$Nom </li>";
						sqlCheck::createNewField($Nom,'propriete',$Prefixe,$O);
					}
				}
			}
			//Si un des noChange n'a pas ete incremente, le champ est mis a jour.
			if ($noChange != 3) {
// 				echo $Nom."--------------------\r\n";
				sqlCheck::alterSql($Nom,'prop',$O);
				continue;
			}
		}
		return false;
	}
	/**
	* Verify
	* Verification des types des prorprietes
	*/
	static function Verify($O){
		//Verifie la valeur du schema
		$error = 0;
		foreach ($O->Proprietes as $Key=>$Value){
			$Tab = sqlCheck::initSpecialTypes($Value,$O);
			if($Tab['type'] == "varchar"&&$Tab['length']=="") die ("Le champ $Key n'a pas de longueur.");
			//On verifie la presence d'un nom et d'un type
			if($Tab['type'] == NULL) die ("Le champ $Key n'a pas de type.");
			//Si la longueur est superieure a 255
			if(isset($Tab["length"])&&$Tab['length'] > 65534&&$Tab['type'] != "text") die ("Le champ $Key a une longueur hors limite: ".$Tab['length']);
			//echo "<li>Check properties ".$Key." $error </li>";
		}
		//Si il n'y a pas eu d'erreur, on valide, sinon on renvoie une erreur.
		return (!$error) ?  true : false;
	}
	/**
	* initSpecialTypes
	* Initialisation des types speciaux
	*/
	static function initSpecialTypes($Tab,$O){
		//Donne une valeur type sql aux types speciaux
		switch (strtolower($Tab['type'])){
			case "image":				//Url locale avec generation de miniature a l insertion ou la modification
				$Tab['type']="varchar";
				$Tab['displayType']="media";
				break;
			case "password":			//generation de mot de passe en md5
				$Tab['type']="varchar";
				$Tab['length']="37";
				$Tab['special']='password';
				$Tab['displayType']="normal";
				break;
			case "conf":			//generation de mot de passe en md5
				$Tab['type']="varchar";
				$Tab['length']="100";
                                $Tab['displayType']="normal";
				break;
			case "alias":				//Uniquement pour les menus permet de faire pointer une url vers une requete
				$Tab['type']="varchar";
				$Tab['special']='alias';
				$Tab['displayType']="normal";
				break;
			case "objectclass":			//Liaison vers une objectClasss particulier
				$Tab['type']="varchar";
				$Tab['displayType']="normal"; 
				// 				$Tab['special']='multi';
				break;
			case "titre":				//Titre d une donnï¿œe (declinable en plusieurs langues)
				$Tab['type']="varchar";
				$Tab['special']='multi';
				$Tab['displayType']="line";
				break;
			case "color":				//Titre d une donnï¿œe (declinable en plusieurs langues)
				$Tab['type']="varchar";
				$Tab['length']="7";
                                $Tab['displayType']="normal";
				break;
			case "random":				//generation d une chaine aleatoire pour les verifications
				$Tab['type']="varchar";
				$Tab['special']='random';
				$Tab['length']="20";
				$Tab['displayType']="normal";
				break;
			case "textonly":				//Champ texte
			case "text":				//Champ texte
			case "raw":				//Champ crue
			case "templateconfig":				//Champ texte
			case "pluginconfig":				//Champ texte
			case "xml":				//Champ texte
			case "metad":				//Champ texte
			case "metak":				//Champ texte
			case "txt":				//Champ texte
				$Tab['type']="text";
				$Tab['displayType']="block";
				//$Tab['special']='multi';
				break;
			case "longtext":				//Champ texte
				$Tab['type']="longtext";
				$Tab['displayType']="block";
				//$Tab['special']='multi';
				break;
			case "metat":				//champ meta-titre
				$Tab['type']="varchar";
				$Tab['length']="200";
                                $Tab['displayType']="normal";
				break;	
			case "metad":				//champ meta-description
				$Tab['type']="varchar";
				$Tab['length']="250";
                                $Tab['displayType']="normal";
				break;
			case "html":				//Champ html
			case "bbcode":				//Champ bbcode
				$Tab['type']="longtext";
				$Tab['special']='multi';
                                $Tab['displayType']="block";
				break;
			case "id":
				$Tab['type']="int";
				$Tab['special']='id';
                                $Tab['displayType']="normal";    
				break;
			case "autodico":			//Enrichissement d un dictionnaire
				$Tab['type']="int";
				$Tab['special']='autodico';
                                $Tab['displayType']="normal";
				break;
			case "mail":				//Champ mail avec verification
			case "url":				//Url
			case "private":				//Champ important
				$Tab['type']="varchar";
                                $Tab['displayType']="normal";
				break;
			case "order":				//Champ ordre
				$Tab['type']="int";
				$Tab['special']='order';
                                $Tab['displayType']="normal";
				break;
			case "date":				//Champ date
				$Tab['type']="bigint";   // PGF "int";
				$Tab['special']='Date';
                                $Tab['displayType']="normal";
				break;
			case "price":				//Champ date
			case "pourcent":				//Champ date
				$Tab['type']="double";
                                $Tab['displayType']="normal";
				break;
			case "float":				//Champ date
				$Tab['type']="double";
                                $Tab['displayType']="normal";
				break;
			case "link":				//Generation d un titre routable sur internet a partir du searchOrder
				$Tab['type']="varchar";
				$Tab['special'] = "multi";
				$Tab['content'] = "link";
                                $Tab['displayType']="normal";
				break;
			case "canonic":				//Generation d un titre routable sur internet a partir du searchOrder
				$Tab['type']="varchar";
				$Tab['special'] = "multi";
				$Tab['content'] = "canonic";
                                $Tab['displayType']="normal";
				break;
			case "langfile":				//Upload de fichier
				$Tab['type']="varchar";
				$Tab['length']="255";
				$Tab['special']='multi';
                                $Tab['displayType']="normal";
				break;
			case "int":				//Upload de fichier
				$Tab['type']="int";
                                $Tab['displayType']="normal";
			break;
			case "varchar":				//Upload de fichier
                                $Tab['displayType']="normal";
			break;
			default:
			case "modele":				//Champ texte
			case "template":				//Champ texte
			case "file":				//Upload de fichier
			case "string":				//Chaine de caractï¿œre
				$Tab['type']="varchar";
                                $Tab['displayType']="normal";
			break;
			case "boolean":				//Chaine de caractï¿œre
				$Tab['type']="boolean";
				$Tab['displayType']="normal";
			break;
		}
		if ($Tab['type']=='varchar')$Tab['length']=(isset($Tab["length"]))?$Tab["length"]:"255";
		return $Tab;
	}
	/**
	* compareFKeys
	* Compare les cles etrangeres du tableau avec celles de la table.
	*/
	static function compareFKeys($O){
		foreach ($O->getParent() as $A){
			if ((!isset($O->tableSql)||!array_key_exists($A->titre,$O->tableSql)) && $A->isShort()) {
				//Si le champ n'existe pas, on le cree.
				sqlCheck::createNewForeignKey($A,$O);
			}
		}
		return false;
	}
	/**
	* CheckKeys
	* Verifie les liaisons des données dans les tables
	*/
	static function CheckKeys($O) {
		foreach ($O->Associations as $A){
			if ($A->isLong()&&$A->isParent($O->titre)&&$A->isChild($O->titre)&&!$A->isInterModule()) {
				//echo "<li> FKEY $A->titre </li>\r\n";
				//Dans le cas d'une liaison recursive de forte cardinalité, on verifie les paires
				$sqlc = "SELECT COUNT(Id) FROM `".$O->Prefix.$O->titre."`";
				$co = $O->executeSql($O,$sqlc);
				if ($co[0]["COUNT(Id)"]<1000){
					$sql="SELECT c.Id,COUNT(cj.Id)
						FROM `".$O->Prefix.$O->titre."` AS c
						LEFT JOIN `".$A->Prefix.$A->getTable()."`as cj ON cj.".$O->titre." = c.Id
						GROUP BY c.Id";
					$res = $O->executeSql($O,$sql);
					if (is_array($res))foreach ($res as $r){
						if ($r['COUNT(cj.Id)']==0){
							$Oc = $A->getParentObjectClass();
							$s = 'INSERT INTO `'.$A->Prefix.$A->getTable().'` SET '.$O->titre.'='.$r['Id'].','.$Oc->titre.'Id=0';
							$O->executeSql($O,$s);
						}
					}
				}
			}
		}
	}
}