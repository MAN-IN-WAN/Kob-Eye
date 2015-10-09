<?php
class sqlInterval{
	//----------------------------------------------//
	//	RECURSIVITE INTERVALLAIRE		//
	//----------------------------------------------//

	//Creation des tables d intervalle
	static function createIntervalIndex($O){
		//ON cree donc une table contenant les bords droits , bords gauche ainsi que l association
		//avec l id de la donnï¿œe
		$sql  = 'CREATE TABLE IF NOT EXISTS `'.$O->Prefix.$O->titre.'-Interval` (';
		$sql.= '`Id` INT NOT NULL ,';
		$sql.= '`Bd` INT NOT NULL ,';
		$sql.= '`Bg` INT NOT NULL )';
		$O->executeSql($O,$sql,"CREATE");
		sqlInterval::purgeInterval($O);
		sqlInterval::IndexInterval(0,1,$O);
	}

	//Vide la table d intervalles
	static function purgeInterval($O){
		$sql='DELETE FROM `'.$O->Prefix.$O->titre.'-Interval`';
		$O->executeSql($O,$sql,"DELETE");
	}

	//Fonction de reindexation du systeme d intervalle a partir de l association parente
	static function IndexInterval($Id,$Bg,$O){
		//On commence par enregistrer le bord gauche
		//----> Bord Gauche ok
		$BordGauche = $Bg;
		//On pousse les bornes droite de 1
		$sql = 'UPDATE `'.$O->Prefix.$O->titre.'-Interval` SET Bd=Bd+1 WHERE Bd >= '.$Bg;
		$O->executeSql($O,$sql,"SELECT_SYS");
		//On pousse egalement le bord gauche de 1
		$sql = 'UPDATE `'.$O->Prefix.$O->titre.'-Interval` SET Bg=Bg+1 WHERE Bg >= '.$Bg;
		$O->executeSql($O,$sql,"SELECT_SYS");
		$Bg++;
		$Objs=sqlInterval::getChildForInterval($Id,$O);
		if (is_array($Objs))foreach ($Objs as $Obj){
			//Pour chaque reponse on relance la methode pour mettre a jour toute la table
			$Bg=sqlInterval::IndexInterval($Obj["Jointure"],$Bg,$O);
			$Bg++;
			//On pousse les bornes droite de 1
			$sql = 'UPDATE `'.$O->Prefix.$O->titre.'-Interval` SET Bd=Bd+1 WHERE Bd >= '.$Bg;
			$O->executeSql($O,$sql,"SELECT_SYS");
			//On pousse egalement le bord gauche de 1
			$sql = 'UPDATE `'.$O->Prefix.$O->titre.'-Interval` SET Bg=Bg+1 WHERE Bg >= '.$Bg;
			$O->executeSql($O,$sql,"SELECT_SYS");
		}
		//On enregistre le bord droit
		sqlInterval::recordInterval($Id,$BordGauche,$Bg,$O);
		return $Bg;
	}

	//Insere un nouvel index
	static function insertIntervalData($Id,$IdParent,$O){
		if (!$IdParent)$IdParent = 0;
		if ($O->isReflexive()==1){
			//Recuperation des informations du parent pour avoir le bord droit
			if ($IdParent=="") $IdParent = 0;
			$sql='SELECT Bd,Bg FROM `'.$O->Prefix.$O->titre.'-Interval` WHERE Id='.$IdParent;
			$Result = $O->executeSql($O,$sql,"SELECT_SYS");
            if (!isset($Result[0])) return;
			$Bg = $Result[0]["Bg"];
			$Bg++;
			//On pousse les bornes droite de 1
			$sql = 'UPDATE `'.$O->Prefix.$O->titre.'-Interval` SET Bd=Bd+1 WHERE Bd >= '.$Bg;
			$O->executeSql($O,$sql,"SELECT_SYS");
			//On pousse egalement le bord gauche de 1
			$sql = 'UPDATE `'.$O->Prefix.$O->titre.'-Interval` SET Bg=Bg+1 WHERE Bg >= '.$Bg;
			$O->executeSql($O,$sql,"SELECT_SYS");
			$Bd = sqlInterval::IndexInterval($Id,$Bg,$O);
		}
		//EM 20140402- Desactivation de la recursivite intervallaire pour les fortes cardinalités.
		if (false && $O->isReflexive()==2){
			foreach ($O->childOf as $Child){
				if ($Child["Titre"]==$O->titre) {
					$Key = $Child;
					$Card = $Child["Card"];
				}
			}
			if ($IdParent>0){
				$Data['Limit'][0] = 0;
				$Data['Limit'][1] = 100000;
				$Data['Table'][0]['Alias'] = "m";
				$Data['Table'][0]['Nom'] = $Key["Table"];
				$Data['Select'][0]['Nom'] = "m.Id";
				$Data['Select'][0]['Alias'] = "Jointure";
				$Data['Groupe'][0]['Condition'][0] = "m.".$Key["Titre"]."=$IdParent";
				$sql = sqlFunctions::createSql("SELECT_INTERVAL",$Data,$O);
				$resql = $O->executeSql($O,$sql,"SELECT_SYS");
			}else{
				$resql[0]['Jointure'] = $IdParent;
			}
			if (is_array($resql))foreach ($resql as $r){
				//On recupere les bords du parent
				$Jointure = $r['Jointure'];
				$sql='SELECT Bd,Bg,Id as Jointure FROM `'.$O->Prefix.$O->titre.'-Interval` WHERE Id='.$Jointure;
				$Result = $O->executeSql($O,$sql,"SELECT_SYS");
				//$Bd = $Result[0]["Bd"];
				$Bg = $Result[0]["Bg"];
				$Bg++;
				//On pousse les bornes droite de 1
				$sql = 'UPDATE `'.$O->Prefix.$O->titre.'-Interval` SET Bd=Bd+1 WHERE Bd >= '.$Bg;
				$O->executeSql($O,$sql,"SELECT_SYS");
				//On pousse egalement le bord gauche de 1
				$sql = 'UPDATE `'.$O->Prefix.$O->titre.'-Interval` SET Bg=Bg+1 WHERE Bg >= '.$Bg;
				$O->executeSql($O,$sql,"SELECT_SYS");
				//On reindexe les enfants
				sqlInterval::IndexInterval($Id,$Bg,$O);
			}
		}
	}

	//Eneleve un index
	static function removeIntervalData($Id,$O,$IdParent=-1){
		foreach ($O->getChild() as $Child){
			if ($Child->isChild($O->titre)) $A = $Child;
		}
		if ($O->isReflexive()==1){
			//Recuperation des informations du parent pour avoir le bord droit
			$sql='SELECT Bd,Bg FROM `'.$O->Prefix.$O->titre.'-Interval` WHERE Id='.$Id;
			$Result = $O->executeSql($O,$sql,"SELECT_SYS");
			if (!sizeof($Result)) return;
			$Bd = $Result[0]["Bd"];
			$Bg = $Result[0]["Bg"];
			//Recherche des enfants
			$sql='SELECT Bg,Bd,Id FROM `'.$O->Prefix.$O->titre.'-Interval` WHERE Bd<='.$Bd.' AND Bg>='.$Bg;
			$Result = $O->executeSql($O,$sql,"SELECT_SYS");
			if (is_array($Result))foreach ($Result as $R) {
				$sql = 'DELETE FROM `'.$O->Prefix.$O->titre.'-Interval` WHERE Id='.$R['Id'];
				$O->executeSql($O,$sql,"SELECT_SYS");
			}
			//ON pousse les bornes droite de 2 fois le nombre de resultat
			$sql = 'UPDATE `'.$O->Prefix.$O->titre.'-Interval` SET Bg=Bg-'.(sizeof($Result)*2).' WHERE Bg >= '.$Bg;
			$O->executeSql($O,$sql,"SELECT_SYS");
			//On pousse egalement le bord gauche de 2 fois le nombre de resultat
			$sql = 'UPDATE `'.$O->Prefix.$O->titre.'-Interval` SET Bd=Bd-'.(sizeof($Result)*2).' WHERE Bd >= '.$Bg;
			$O->executeSql($O,$sql,"SELECT_SYS");
		}
		//EM 20140402- Desactivation de la recursivite intervallaire pour les fortes cardinalités.
		if (false && $O->isReflexive()==2){
			foreach ($O->childOf as $Child){
				if ($Child["Titre"]==$O->titre) {
					$Key = $Child;
					$Card = $Child["Card"];
				}
			}
			$Data['Limit'][0] = 0;
			$Data['Limit'][1] = 100000;
			$Data['Table'][0]['Alias'] = "m";
			$Data['Table'][0]['Nom'] = $O->titre;
			$Data['Table'][1]['Alias'] = "j";
			$Data['Table'][1]['Nom'] = $A->getTable();
			$Data['Select'][0]['Nom'] = "j.Id";
			$Data['Select'][0]['Alias'] = "Jointure";
			$Data['Groupe'][0]['Lien'] = "AND";
			$Data['Groupe'][0]['Condition'][0] = "m.Id=j.".$A->getField();
			$Data['Groupe'][0]['Condition'][1] = "m.Id=$Id";
			if ($IdParent>-1)$Data['Groupe'][0]['Condition'][2] = "j.".$A->getField('parent')."=$IdParent";
			$sql = sqlFunctions::createSql("SELECT_INTERVAL",$Data,$O);
			$resql = $O->executeSql($O,$sql,"SELECT_SYS");
			if (is_array($resql))foreach ($resql as $r) {
				$sql='SELECT Bg,Bd FROM `'.$O->Prefix.$O->titre.'-Interval` WHERE Id='.$r['Jointure'];
				$Result = $O->executeSql($O,$sql,"SELECT_SYS");
				$Bd = $Result[0]["Bd"];
				$Bg = $Result[0]["Bg"];
				//Recherche des enfants
				$sql='SELECT Bg,Bd,Id FROM `'.$O->Prefix.$O->titre.'-Interval` WHERE Bd<='.$Bd.' AND Bg>='.$Bg;
				$Result = $O->executeSql($O,$sql,"SELECT_SYS");
				if (is_array($Result))foreach ($Result as $R) {
					$sql = 'DELETE FROM `'.$O->Prefix.$O->titre.'-Interval` WHERE Id='.$R['Id'];
					$O->executeSql($O,$sql,"SELECT_SYS");
				}
				//ON pousse les bornes droite de 2 fois le nombre de resultat
				$sql = 'UPDATE `'.$O->Prefix.$O->titre.'-Interval` SET Bg=Bg-'.(sizeof($Result)*2).' WHERE Bg >= '.$Bg;
				$O->executeSql($O,$sql,"SELECT_SYS");
				//On pousse egalement le bord gauche de 2 fois le nombre de resultat
				$sql = 'UPDATE `'.$O->Prefix.$O->titre.'-Interval` SET Bd=Bd-'.(sizeof($Result)*2).' WHERE Bd >= '.$Bg;
				$O->executeSql($O,$sql,"SELECT_SYS");
			}
		}
	}

	//Recupere l ensemble des enfants d une donnee
	static function getChildForInterval($Id,$O) {
		$Results = Array();		
		//On recherche recursivement les categories et on recree l index selon les cardinalites
		foreach ($O->getChild() as $Child){
			if ($Child->isChild($O->titre)) $A = $Child;
		}
		$Data['Limit'][0] = 0;
		$Data['Limit'][1] = 100000;
		if ($A->isShort()){
			//On appelle la table concernï¿œe
			$Data['Table'][0]['Alias'] = "m";
			$Data['Table'][0]['Nom'] = $O->titre;
			$Data['Select'][0]['Nom'] = "m.Id";
			$Data['Select'][0]['Alias'] = "Jointure";
			if ($Id==0) {
				$Data['Groupe'][0]['Lien'] = "OR";
				$Data['Groupe'][0]['Condition'][0] = "m.".$A->getField()." IS NULL";
				$Data['Groupe'][0]['Condition'][1] = "m.".$A->getField()."=0";
			}else{
				$Data['Groupe'][0]['Lien'] = "AND";
				$Data['Groupe'][0]['Condition'][0] = "m.".$A->getField()."=$Id";
			}
		}
		//EM 20140402- Desactivation de la recursivite intervallaire pour les fortes cardinalités.
		if (false && $A->isLong()){
			//Il faut donc faire une jointure avec la table associative
			//On appelle la table concernï¿œe
			$Data['Table'][0]['Alias'] = "m";
			$Data['Table'][0]['Nom'] = $O->titre;
			$Data['Table'][1]['Alias'] = "j";
			$Data['Table'][1]['Nom'] = $A->getTable();
			$Data['Select'][0]['Nom'] = "j.Id";
			$Data['Select'][0]['Alias'] = "Jointure";
			$Data['Groupe'][0]['Lien'] = "AND";
			if ($Id==0) {
				$Data['Groupe'][0]['Condition'][0] = "j.".$A->getField()."=0";
			}else{
				$Data['Table'][2]['Alias'] = "j2";
				$Data['Table'][2]['Nom'] = $A->getTable();
				$Data['Groupe'][0]['Condition'][0] = "j2.Id='$Id'";
				$Data['Groupe'][0]['Condition'][2] = "j.".$A->getField()."=j2.".$A->getField('parent');
			}
			$Data['Groupe'][0]['Condition'][1] = "m.Id=j.".$A->getField('parent');
		}
		$sql = sqlFunctions::createSql("SELECT_INTERVAL",$Data,$O);
		$resql = $O->executeSql($O,$sql,"SELECT_SYS");
		//if (MDB2::isError($resql)) return false;
		$i = 0;
		if (is_array($resql))foreach($resql as $tab){
			$Results[$i]=$tab;
			$Results[$i]["ObjectType"]=$O->titre;
			$i++;
		}
		return $Results;
	}

	//Enregistre d un index d intervalle
	static function recordInterval($Id,$Bg,$Bd,$O){
		$sql  = 'INSERT INTO `'.$O->Prefix.$O->titre.'-Interval` (';
		if ($Bg!="") $sql .= ' Bg,';
		if ($Bd!="") $sql .= ' Bd,';
		$sql .= 'Id) VALUES (';
		if ($Bg!="") $sql .= '"'.$Bg.'",';
		if ($Bd!="") $sql .= '"'.$Bd.'",';
		$sql .= '"'.$Id.'")';
		$O->executeSql($O,$sql,"UPDATE");
	}

	static function verifyIntervalIndex($O) {return false;}


}
