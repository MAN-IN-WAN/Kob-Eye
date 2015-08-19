<?php

class sqlFunctions{


	static function recursivDeleteOldParity($Grp,$Prefixe){
		for ($i=0;$i<count($Grp);$i++){
			if (isset($Grp[$i]["Condition"]["Groupe"])){
				$SaveGrp=$Grp[$i]["Condition"]["Groupe"];
				unset($Grp[$i]["Condition"]["Groupe"]);
			}else $SaveGrp = array();
			foreach ($Grp[$i]["Condition"] as $K=>$C){
				if (strstr($C,$Prefixe)){unset($Grp[$i]["Condition"][$K]);}
			}
			$SaveGrp = sqlFunctions::recursivDeleteOldParity($SaveGrp,$Prefixe);
			if (count($Grp[$i]["Condition"])==0) unset($Grp[$i]);
			$Grp[$i]["Condition"]["Groupe"] = $SaveGrp;
		}
		return $Grp;
	}
	

	static function joinRecursiv($Tab,$Data,$i,$O) {
		//On ajoute une premiere table d index correspondant
		switch ($O->isReflexive($Tab[$i]["Nom"])){
			case "1":
				if ($i>0){
					//Il faut retirer la contrainte de jointure de la paire precedente
					$Gout="";
					$Out=$Data;
					if (is_array($Data["Groupe"]))
					$Data["Groupe"] = sqlFunctions::recursivDeleteOldParity($Data["Groupe"],sqlFunctions::getPrefixe($Tab,$i).".");
					//Une table supplémentaire
					$Data["Table"][sizeof($Data["Table"])]['Prefix'] = MAIN_DB_PREFIX.$Tab[$i]["Module"]."-";
					$Data["Table"][sizeof($Data["Table"])-1]['Nom'] = $Tab[$i]["Nom"];
					$Data["Table"][sizeof($Data["Table"])-1]['Alias'] = sqlFunctions::getPrefixe($Tab,$i)."t";
				}
				$Data["Table"][sizeof($Data["Table"])]['Prefix'] = MAIN_DB_PREFIX.$Tab[$i]["Module"]."-";
				$Data["Table"][sizeof($Data["Table"])-1]['Nom'] = $Tab[$i]["Nom"];
				$Data["Table"][sizeof($Data["Table"])-1]['Alias'] = sqlFunctions::getPrefixe($Tab,$i);
				//Mise en place des intervalles
				if($i<sizeof($Tab)-1){
					$Data["Table"][sizeof($Data["Table"])]['Prefix'] = MAIN_DB_PREFIX.$Tab[$i]["Module"]."-";
					$Data["Table"][sizeof($Data["Table"])-1]['Nom'] = $Tab[$i]["Nom"]."-Interval";
					$Data["Table"][sizeof($Data["Table"])-1]['Alias'] = sqlFunctions::getPrefixe($Tab,$i)."j";
				}
				$Data["Table"][sizeof($Data["Table"])]['Prefix'] = MAIN_DB_PREFIX.$Tab[$i]["Module"]."-";
				$Data["Table"][sizeof($Data["Table"])-1]['Nom'] = $Tab[$i]["Nom"]."-Interval";
				$Data["Table"][sizeof($Data["Table"])-1]['Alias'] = sqlFunctions::getPrefixe($Tab,$i)."i";
				$Groupe["Lien"] = "AND";
				if (isset($Tab[$i-1])&&($Tab[$i-1]["Card"]=="0,n"||$Tab[$i-1]["Card"]=="1,n")){
					$Condition[]=sqlFunctions::getPrefixe($Tab,$i)."i.Id=".sqlFunctions::getPrefixe($Tab,($i-1))."t.".$Tab[$i-1]["NomEnfant"];
				}else{
					if ($i==0) $Condition[]=sqlFunctions::getPrefixe($Tab,$i)."i.Id=0";
//EM-20150216 probleme de requete recursif pour avoir les éléments du premier noeud
//PROB DRIVEO POUR REQUETE Boutique/Categorie/655/Categorie/*/Produit
//Donc retour sur Id ... Attente de l'autre cas pour créer un FIX.
//TODO
					else $Condition[]=sqlFunctions::getPrefixe($Tab,$i)."t.Id=".sqlFunctions::getPrefixe($Tab,($i-1)).".Id";
//EM-20150611 recorrection car prob de requete
					//else $Condition[]=sqlFunctions::getPrefixe($Tab,$i)."t.".$Tab[$i-1]["Champ"]."=".sqlFunctions::getPrefixe($Tab,($i-1)).".Id";
				}
				if ($i>0)
					$Condition[]=sqlFunctions::getPrefixe($Tab,$i).'i.Id='.sqlFunctions::getPrefixe($Tab,$i)."t.Id";
				$Condition[]=sqlFunctions::getPrefixe($Tab,$i).'j.Id='.sqlFunctions::getPrefixe($Tab,$i).".Id";
				$Condition[]=sqlFunctions::getPrefixe($Tab,$i).'i.bd>='.sqlFunctions::getPrefixe($Tab,$i)."j.bd";
				$Prefs = sqlFunctions::getPrefixe($Tab,$i);
				$Temp=$Prefs."i.bg";
				$Temp.='<=';
				$Temp.=$Prefs."j.bg";
				$Condition[] = $Temp;
				//$GLOBALS["Systeme"]->Log->log($Temp);
				$Groupe["Condition"]=$Condition;
				$Data["Groupe"][] = $Groupe;
				$flag = false;
				if (isset($Data['GroupBy']) && is_array($Data['GroupBy']))
					foreach ($Data['GroupBy'] as $g)
						if ($g == "m.Id")
							$flag = true;
				if (!$flag)
					$Data['GroupBy'][] = "m.Id";
			break;
			case "2" :
				foreach ($O->getChild() as $Child)
					if ($Child -> isChild($Tab[$i]["Nom"]))
						$C = $Child;
				$Data['Table'][sizeof($Data["Table"])]['Nom'] = $C -> getTable();
				$Data['Table'][sizeof($Data["Table"])-1]['Alias'] = "j" . $i . "t";
				$i = 0;
				$Groupe["Lien"] = "AND";
				$Condition[] = "m.Id=j" . $i . "t." . $C -> getField();
				//On ajoute une premiere table d index correspondant
				$Groupe["Condition"]=$Condition;
				$Data["Groupe"][] = $Groupe;
				$Data['Select'][] = Array("Nom" => "j" . $i . "t." . $C -> getField('parent') , "Alias" => "ClefReflexive");
			break;
			default:
				$Data["Table"][sizeof($Data["Table"])]['Prefix'] = MAIN_DB_PREFIX . $Tab[$i]["Module"] . "-";
				$Data["Table"][sizeof($Data["Table"]) - 1]['Nom'] = $Tab[$i]["Nom"];
				$Data["Table"][sizeof($Data["Table"]) - 1]['Alias'] = sqlFunctions::getPrefixe($Tab, $i);
			break;
		}
		if (isset($Tab[$i]["Card"])&&($Tab[$i]["Card"] == "0,1" || $Tab[$i]["Card"] == "1,1")){
			//On verifie que l'alias en question n'existe pas deja
			$exists = false;
			for ($j = 0; $j < sizeof($Data["Table"]); $j++)
				if ($Data["Table"][$j]['Alias'] == sqlFunctions::getPrefixe($Tab, $i))
					$exists = true;
			if (!$exists) {
				$Data["Table"][sizeof($Data["Table"])]['Prefix'] = MAIN_DB_PREFIX . $Tab[$i]["Module"] . "-";
				$Data["Table"][sizeof($Data["Table"]) - 1]['Nom'] = $Tab[$i]["Nom"];
				$Data["Table"][sizeof($Data["Table"]) - 1]['Alias'] = sqlFunctions::getPrefixe($Tab, $i);
			}
			//-------------------CONDITIONS DE JOINTURE
			$NumGroupe = sizeof($Data["Groupe"]);
			$Data['Groupe'][$NumGroupe]['Lien'] = "AND";
			if ($Tab[$i]["Nom"]==$Tab[$i]["NomEnfant"])
				$Data['Groupe'][$NumGroupe]['Condition'][] = sqlFunctions::getPrefixe($Tab,$i).".".$Tab[$i]["Target"]."=".sqlFunctions::getPrefixe($Tab,$i+1).".".$Tab[$i]["Target"];
			else 
				$Data['Groupe'][$NumGroupe]['Condition'][] = sqlFunctions::getPrefixe($Tab,$i).".".$Tab[$i]["Target"]."=".sqlFunctions::getPrefixe($Tab,$i+1).".".$Tab[$i]["Champ"];
		}elseif (isset($Tab[$i]["Card"])&&($Tab[$i]["Card"] == "0,n" || $Tab[$i]["Card"] == "1,n")){
			//On joint la table de liaison
			$Data["Table"][sizeof($Data["Table"])]['Prefix'] = MAIN_DB_PREFIX.$Tab[$i]["Module"]."-";
			$Data["Table"][sizeof($Data["Table"])-1]['Nom'] = $Tab[$i]["Table"];
			$Data["Table"][sizeof($Data["Table"])-1]['Alias'] = "j".$i."s";
			$NumGroupe = sizeof($Data["Groupe"]);
			//---------------CONDITIONS DE JOINTURE
			$Data['Groupe'][$NumGroupe]['Lien'] = "AND";
			$Data['Groupe'][$NumGroupe]['Condition'][] = "j".$i.".Id=j$i"."s.".$Tab[$i]["Nom"]."Id";
			$Data['Groupe'][$NumGroupe]['Condition'][] = sqlFunctions::getPrefixe($Tab,$i+1).".".$Tab[$i]["Target"]."=j$i"."s.".$Tab[$i]["NomEnfant"];
		}
		return $Data;
	}

	static function joinStandard($Tab,$Data,$i,$O,$pref=""){
		//On ajoute la table de l objet concernï¿œ
		$Data["Table"][((isset($Data["Table"]))?sizeof($Data["Table"]):0)]['Prefix'] = MAIN_DB_PREFIX.$Tab[$i]["Module"]."-";
		$Data["Table"][sizeof($Data["Table"])-1]['Nom'] = $Tab[$i]["Nom"];
		//Si ce n est pas le premier element alors on fait la jointure avec la paire precedente.
		if (!isset($Tab[$i]["NomEnfant"])){
			//Si pas de jointure
			if ($Tab[$i]["Out"])$Data["Table"][sizeof($Data["Table"])-1]['Alias'] = "m";
			else $Data["Table"][sizeof($Data["Table"])-1]['Alias'] = "j$i".$pref;
			$NumGroupe = (isset($Data["Groupe"]))?sizeof($Data["Groupe"]):0;
			//Gestion de la reflexivitï¿œ
			if (!$O->noRecursivity&&isset($Tab[$i]["Reflexive"])&&$Tab[$i]["Reflexive"]==2&&$Tab[$i]["Out"]){
				foreach ($O->getChild() as $Child)
					if ($Child -> isChild($Tab[$i]["Nom"]))
						$C = $Child;
				//On joint la table de liaison
				$Data["Table"][sizeof($Data["Table"])]['Prefix'] = MAIN_DB_PREFIX.$O->Module."-";
				$Data["Table"][sizeof($Data["Table"])-1]['Nom'] = $C->getTable();
				$Data["Table"][sizeof($Data["Table"])-1]['Alias'] = "j".$i.$pref."t";
				$NumGroupe = (isset($Data["Groupe"]))?sizeof($Data["Groupe"]):0;
				//---------------CONDITIONS DE JOINTURE
				$Data['Groupe'][$NumGroupe]['Lien'] = "AND";
				$Data['Groupe'][$NumGroupe]['Condition'][] = "m.Id=j$i".$pref."t.".$C->getField('parent');
			}
			//Gestion de la racine si il n'y a pas de recherche globale (*)
			if (!$O->noRecursivity&&isset($Tab[$i]["Reflexive"])&&$Tab[$i]["Reflexive"]==1&&(!isset($Tab[$i-1]["NomEnfant"])||$Tab[$i-1]["NomEnfant"]!=$Tab[$i]["Nom"])&&(sqlFunctions::isFilter($Tab[$i]["Recherche"])||!$Tab[$i]["Recherche"])) {
				
				//Reflexivite faible
				$Data['Groupe'][$NumGroupe]['Lien'] = "OR";
				$Data['Groupe'][$NumGroupe]['Condition'][] = sqlFunctions::getPrefixe($Tab,$i).$pref.".".$O->findReflexive()." IS NULL";
				$Data['Groupe'][$NumGroupe]['Condition'][] = sqlFunctions::getPrefixe($Tab,$i).$pref.".".$O->findReflexive()."=''";
				$Data['Groupe'][$NumGroupe]['Condition'][] = sqlFunctions::getPrefixe($Tab,$i).$pref.".".$O->findReflexive()."=0";
			}elseif (!$O->noRecursivity&&isset($Tab[$i]["Reflexive"])&&(!isset($Tab[$i-1]["NomEnfant"])||$Tab[$i-1]["NomEnfant"]!=$Tab[$i]["Nom"])&&(sqlFunctions::isFilter($Tab[$i]["Recherche"])||!$Tab[$i]["Recherche"])&&$Tab[$i]["Reflexive"]==2){
				$Data['Groupe'][$NumGroupe]['Condition'][] = "j$i".$pref."t.".$C->getField()."=0";
			}
		}else{
			//Si jointure
			$Data["Table"][sizeof($Data["Table"])-1]['Alias'] = "j$i".$pref;
			//On selectionne l'id de la requete pour l'historique
			$Data['Select'][] = Array ("Nom"=>"j$i".$pref.".Id","Alias"=>"__Liaison_J$i");
			$Data['Select'][] = Array ("Valeur"=>$Tab[$i]["Nom"],"Alias"=>"__Liaison_titre_J$i");
			if (($Tab[$i]["Card"] == "0,1" || $Tab[$i]["Card"] == "1,1")){
				//-------------------CONDITIONS DE JOINTURE
				$NumGroupe = (isset($Data["Groupe"]))?sizeof($Data["Groupe"]):0;
				$Data['Groupe'][$NumGroupe]['Lien'] = "AND";
				$Data['Groupe'][$NumGroupe]['Condition'][] = sqlFunctions::getPrefixe($Tab,$i).$pref.".".$Tab[$i]["Target"]."=".sqlFunctions::getPrefixe($Tab,$i+1).".".$Tab[$i]["Champ"];
			}else{
				//On joint la table de liaison
				$Data["Table"][sizeof($Data["Table"])]['Prefix'] = MAIN_DB_PREFIX . $Tab[$i]["ModuleAssoc"] . "-";
				$Data["Table"][sizeof($Data["Table"]) - 1]['Nom'] = $Tab[$i]["Table"];
				$Data["Table"][sizeof($Data["Table"]) - 1]['Alias'] = "j" . $i . $pref . "t";
				$NumGroupe = (isset($Data["Groupe"])) ? sizeof($Data["Groupe"]) : 0;
				//---------------CONDITIONS DE JOINTURE
				$Data['Groupe'][$NumGroupe]['Lien'] = "AND";
				$Data['Groupe'][$NumGroupe]['Condition'][] = "j$i" . $pref . ".Id=j$i" . $pref . "t." . $Tab[$i]["Nom"] . "Id";
				$Data['Groupe'][$NumGroupe]['Condition'][] = sqlFunctions::getPrefixe($Tab, $i + 1) . "." . $Tab[$i]["Target"] . "=j$i" . $pref . "t." . $Tab[$i]["NomEnfant"];
			}
		}
		return $Data;
	}

	static function joinParentRecursiv($Tab, $Data, $i, $O) {
		//Objet Enfant portant les filtres
		$Data["Table"][sizeof($Data["Table"])]['Prefix'] = MAIN_DB_PREFIX.$Tab[$i+2]["Module"]."-";
		$Data["Table"][sizeof($Data["Table"])-1]['Nom'] = $Tab[$i+2]["Nom"];
		$Data["Table"][sizeof($Data["Table"])-1]['Alias'] = "j".($i+2)."p";
		//Objet ciblé
		$Data["Table"][sizeof($Data["Table"])]['Prefix'] = MAIN_DB_PREFIX.$Tab[$i]["Module"]."-";
		$Data["Table"][sizeof($Data["Table"])-1]['Nom'] = $Tab[$i]["Nom"];
		$Data["Table"][sizeof($Data["Table"])-1]['Alias'] = "m";
		//Groupes
		$NumGroupe = sizeof($Data['Groupe']);
		$flag=false;
		switch ($Tab[$i+1]["Card"]) {
			case "0,n":
			case "1,n":
				//Table de liaison entre l objet enfant et l objet parent
				$Data["Table"][sizeof($Data["Table"])]['Prefix'] = MAIN_DB_PREFIX.$Tab[$i+1]["Module"]."-";
				$Data["Table"][sizeof($Data["Table"])-1]['Nom'] = $Tab[$i+1]["Table"];
				$Data["Table"][sizeof($Data["Table"])-1]['Alias'] = "j".($i+1)."t";
				//Liaison entre intervalle de comparaison et la table de liaison
				$Data['Groupe'][$NumGroupe]['Condition'][] = "j".$i."i.Id=j".($i+1)."t.".$Tab[$i+1]["Parent"]."Id";
				$Data['Groupe'][$NumGroupe]['Condition'][] = "j".($i+2)."p.Id=j".($i+1)."t.".$Tab[$i+1]["Champ"];
				break;
			case "0,1":
			case "1,1":
				//Liaison entre intervalle de comparaison et la table de liaison
				$Data['Groupe'][$NumGroupe]['Condition'][] = "j".$i."i.Id=j".($i+2)."p.".$Tab[$i+1]["Champ"];
				break;
		}
		switch ($Tab[$i]["Card"]) {
			case "0,1":
			case "1,1":
				//Ajout de l'ordre
				$Data['Select'][] = Array("Nom"=>'(mj.Bd - mj.Bg)',"Alias"=>'Ob');
				$Data['Order'] = 'Ob ASC';
				//Table d intervalle  l objet parent
				$Data["Table"][sizeof($Data["Table"])]['Prefix'] = MAIN_DB_PREFIX.$Tab[$i]["Module"]."-";
				$Data["Table"][sizeof($Data["Table"])-1]['Nom'] = $Tab[$i]["Nom"]."-Interval";
				$Data["Table"][sizeof($Data["Table"])-1]['Alias'] = "j".$i."i";
				//Mise en place du filtre
				$NumGroupe = sizeof($Data["Groupe"]);
				//$Data['Groupe'][$NumGroupe]['Lien'] = "AND";
				//$Data['Groupe'][$NumGroupe]['Condition'][] = "j".($i+2)."p.Id = ".$Tab[$i+2]["Recherche"];
				$Temp = sqlFunctions::whereSql($Tab[$i+2],Array(),"j".($i+2)."p",$O);
				if (is_array($Temp['Groupe']))foreach ($Temp['Groupe'] as $G){
					$Data['Groupe'][] = $G;
				}
				//Liaison entre intervalle et objet ciblé
				$Data['Groupe'][$NumGroupe]['Condition'][] = "m.Id=mj.Id";
				//Conditions intervallaires
				$Groupe["Lien"] = "AND";
				$Condition[]=sqlFunctions::getPrefixe($Tab,$i)."i.bd<=mj.bd";
				$Condition[]=sqlFunctions::getPrefixe($Tab,$i)."i.bg>=mj.bg";
				$Groupe["Condition"]=$Condition;
				$Data["Groupe"][] = $Groupe;
				if (is_array($Data['GroupBy']))
					foreach ($Data['GroupBy'] as $g)
						if ($g == "m.Id")
							$flag = true;
				if (!$flag)
					$Data['GroupBy'][] = "m.Id";
				break;
		}
		return $Data;
	}
	static function joinParent($Tab,$Data,$i,$O,$pref=""){
		//On ajoute la table de l objet concernï¿œ
		$flag=false;
		//O ajoute la table de filtre
		$ind = (isset($Data["Table"]))?sizeof($Data["Table"]):0;
		$Data["Table"][$ind]['Prefix'] = MAIN_DB_PREFIX.$Tab[$i+1]["Module"]."-";
		$Data["Table"][$ind]['Nom'] = $Tab[$i+1]["Nom"];
		$Data["Table"][$ind]['Alias'] = "j".($i+1).$pref."p";
		//On verifie l'existence de la table de sortie
		$ind++;
		$alreadyexists = false;
		for ($in=0;$in<$ind;$in++) if ($Data["Table"][$in]['Alias']=='m') $alreadyexists = true;
		if (!$alreadyexists){
			//Table resultat
			$Data["Table"][$ind]['Prefix'] = MAIN_DB_PREFIX.$Tab[$i]["ModuleParent"]."-";
			$Data["Table"][$ind]['Nom'] = $Tab[$i]["Parent"];
			$Data["Table"][$ind]['Alias'] = "m";
		}
		if (($Tab[$i]["Card"] == "0,1" || $Tab[$i]["Card"] == "1,1")){
			//-------------------CONDITIONS DE RECHERCHE
			$Temp = sqlFunctions::whereSql($Tab[$i+1],$Data,"j".($i+1).$pref."p",$O);
			if (isset($Temp['Groupe'])&&is_array($Temp['Groupe']))foreach ($Temp['Groupe'] as $G){
				$Data['Groupe'][] = $G;
			}
			$Data['Suffixe'] = (isset($Temp['Suffixe']))?$Temp['Suffixe']:"";
			$NumGroupe = (isset($Data["Groupe"]))?sizeof($Data["Groupe"]):0;
			$Data['Groupe'][$NumGroupe]['Lien'] = "AND";
			$Data['Groupe'][$NumGroupe]['Condition'][] = "j".($i+1).$pref."p.".$Tab[$i]["Champ"]."=m.".$Tab[$i]["Target"];
			//-------------------CONDITIONS DE JOINTURE
			$NumGroupe = sizeof($Data["Groupe"]);
			//$Data['Groupe'][$NumGroupe]['Condition'][] = $Temp['Groupe'][0]['Condition'][0];
		}else{
			//On joint la table de liaison
			$Data["Table"][sizeof($Data["Table"])]['Prefix'] = MAIN_DB_PREFIX.$Tab[$i]["Module"]."-";
			$Data["Table"][sizeof($Data["Table"])-1]['Nom'] = $Tab[$i]["Table"];
			$Data["Table"][sizeof($Data["Table"])-1]['Alias'] = "j".$i.$pref."t";
			$Temp = sqlFunctions::whereSql($Tab[$i+1],Array(),"j".($i+1).$pref."p",$O);
			if (isset($Temp['Groupe'])&&is_array($Temp['Groupe']))foreach ($Temp['Groupe'] as $G){
				$Data['Groupe'][] = $G;
			}
			if (isset($Temp['Suffixe']))$Data['Suffixe'] = $Temp['Suffixe'];
			$NumGroupe = sizeof($Data["Groupe"]);
			$Data['Groupe'][$NumGroupe]['Lien'] = "AND";
			//---------------CONDITIONS DE JOINTURE
			$Data['Groupe'][$NumGroupe]['Lien'] = "AND";
			$Data['Groupe'][$NumGroupe]['Condition'][] = "j".($i+1).$pref."p.Id=j$i".$pref."t.".$Tab[$i]["NomEnfant"];
			$Data['Groupe'][$NumGroupe]['Condition'][] = "m.".$Tab[$i]["Target"]."=j$i".$pref."t.".$Tab[$i]["Parent"]."Id";
			if (isset($Data['GroupBy'])&&is_array($Data['GroupBy']))foreach ($Data['GroupBy'] as $g)if ($g=="m.Id")$flag=true;
			if (!$flag)$Data['GroupBy'][] = "m.Id";
		}
		if (isset($Tab[$i+2])&&is_array($Tab[$i+2])){
			//Ajout d un filtre sur la sortie
			$Temp = sqlFunctions::whereSql($Tab[$i+2],$Data,"m",$O);
			$Data['Groupe'][$NumGroupe]['Condition'][] = $Temp['Groupe'][1]['Condition'][0];
			$Data['Suffixe'] = $Temp['Suffixe'];
		}
		return $Data;
	}

	//Definit les prefixe de table en fonction de l emplacement dans le tableau d analyse
	static function getPrefixe($Tab,$i){
		return ($i==sizeof($Tab)-1||isset($Tab[$i]["Parent"]))?"m":"j".$i;
	}

	//Definit les prefixe de table en fonction de l emplacement dans le tableau d analyse
	static function getTableAssoc($P,$C){
		return ($i==sizeof($Tab)-1)?"m":"j".$i;
	}

	//Cree les conditions de jointures necessaires dans une requete
	static function joinSql($Tab,$Data,$O,$View=null){
		//On boucle sur les objets precedents
		$Parent=false;
		$var  = (sizeof($Tab)>MAIN_DB_RECURSIV_LIMIT&&MAIN_DB_RECURSIV_LIMIT)?sizeof($Tab)-1-MAIN_DB_RECURSIV_LIMIT:0;
		for ($i=$var;$i<sizeof($Tab);$i++){
			if (isset($Tab[$i+1]["Parent"])&&$Tab[$i+1]["Parent"]==$Tab[$i]["Nom"]&&isset($Tab[$i]["Recherche"])&&$Tab[$i]["Recherche"]=="*") {
				$Data = sqlFunctions::joinParentRecursiv($Tab,$Data,$i,$O);
				$Data["Special"] = "RECURSIV";
				$Parent=true;
				$i++;
				$i++;
			}elseif (isset($Tab[$i]["Parent"])&&$Tab[$i]["Parent"]) {
				$Data = sqlFunctions::joinParent($Tab,$Data,$i,$O);
				$i = sizeof($Tab)-1;
				$Parent=true;
			}elseif (!isset($Tab[$i]["Recherche"])||$Tab[$i]["Recherche"]=="*") {
				//RECURSIVE
				$Data = sqlFunctions::joinRecursiv($Tab,$Data,$i,$O);
				$Data["Special"] = "RECURSIV";
			}else{
				//CHAINE
				$Data = sqlFunctions::joinStandard($Tab,$Data,$i,$O);
				$Data = sqlFunctions::whereSql($Tab[$i],$Data,sqlFunctions::getPrefixe($Tab,$i,$O),$O,$View);
			}
		}
		//Dans le cas de jointure recursive
		if ($O->isReflexive()){
			//Cardinalite faible
			if ($O->isReflexive()==1){
				$Data["Select"][]=Array("Nom"=>"m.".$O->findReflexive(),"Alias"=>"ClefReflexive");
				$Data["Select"][]=Array("Nom"=>"mj.Bd","Alias"=>"Bd");
				$Data["Select"][]=Array("Nom"=>"mj.Bg","Alias"=>"Bg");
				$Data["Table"][] = Array("Nom"=>$O->titre."-Interval","Alias"=>"mj","Prefix"=>MAIN_DB_PREFIX.$O->Module."-");

				$Data['Groupe'][]['Condition'][] ="mj.Id = m.Id";
			}
			//Cardinalite forte
			//EM 20140402- Desactivation de la recursivite intervallaire pour les fortes cardinalités.
			if (false && $O->isReflexive()==2){
				$ng = sizeof($Data['Groupe']);
				$interval = true;
				if (sizeof($Tab) > 1 && isset($Tab[sizeof($Tab) - 1]["Nom"]) && isset($Tab[sizeof($Tab) - 2]["Nom"]) && (($Tab[sizeof($Tab) - 1]["Nom"] != $Tab[sizeof($Tab) - 2]["Nom"])))
					$interval = false;
				if ($interval) {
					if (sizeof($Tab) > 1 && !$Parent) {
						for ($j = 0; $j < sizeof($Tab) - 1; $j++) {
							$Data["Table"][] = Array("Nom" => $O -> titre . "-Interval", "Alias" => "mj$j", "Prefix" => MAIN_DB_PREFIX . $O -> Module . "-");
							$Data['Groupe'][$ng]['Condition'][] = "mj$j.Id = j" .  (sizeof($Tab) - 1) . "t" . ".Id";
							if ($j > 0) {
								$Data['Groupe'][$ng]['Condition'][] = "mj" . ($j - 1) . ".Bd > mj" . $j . ".Bd";
								$Data['Groupe'][$ng]['Condition'][] = "mj" . ($j - 1) . ".Bg < mj" . $j . ".Bg";
							}
						}
						$Data["Select"][] = Array("Nom" => "mj" . ($j - 1) . ".Bd", "Alias" => "Bd");
						$Data["Select"][] = Array("Nom" => "mj" . ($j - 1) . ".Bg", "Alias" => "Bg");
						$Data["GroupBy"][] = "m.Id";
					} else {
						$Data["Select"][] = Array("Nom" => "mj.Bd", "Alias" => "Bd");
						$Data["Select"][] = Array("Nom" => "mj.Bg", "Alias" => "Bg");
						$Data["Table"][] = Array("Nom" => $O -> titre . "-Interval", "Alias" => "mj", "Prefix" => MAIN_DB_PREFIX . $O -> Module . "-");
						$Data['Groupe'][$ng]['Condition'][] = "mj.Id = j" . (($Parent) ? (sizeof($Tab) - 2) . "t" : (sizeof($Tab) - 1) . "t") . ".Id";
						$Data["GroupBy"][] = "m.Id";
					}
				}
			}elseif ($O->isReflexive()==2)$Data["GroupBy"][] = "m.Id";

		}
		return $Data;
	}

	//Extrait les joitures en filtres de la chaine de recherche
	static function filterMultiJoin($Tab) {
		//Recherche de l'etape de sortie
		for ($i = 0; $i < sizeof($Tab); $i++)
			if (isset($Tab[$i]['Out']) && $Tab[$i]['Out'] == 1)
				$R = $i;
		if (is_array($Tab[$R]["Recherche"]))$rech = implode('+',$Tab[$R]["Recherche"]);
		else $rech = $Tab[$R]["Recherche"];
		$Tab[$R]["Recherche"]= $rech;
		
		//recherche des jointures en selection
		preg_match_all("#([^|&]*?)\.([^\(]*?)\(([^\)]*?)\)#", $Tab[$R]["Recherche"], $Rer);
		if (!empty($Rer[0][0])) {
			for ($i = 0; $i < sizeof($Rer[0]); $i++) {
				$Tab[$R]["Recherche"] = str_replace($Rer[0][$i], '', $Tab[$R]["Recherche"]);
				$Tab[$R]["multiFilter"][] = Array($Rer[0][$i], $Rer[1][$i], $Rer[2][$i], $Rer[3][$i]);
			}
			//On reecrit la recherche
			$Re = explode("&", $Tab[$R]["Recherche"]);
			$Tab[$R]["Recherche"] = '';
			for ($i = 0; $i < sizeof($Re); $i++)
				if (!empty($Re[$i]))
					$Tab[$R]["Recherche"] .= (($Tab[$R]["Recherche"] != '') ? '&' : '') . $Re[$i];
		}
		return $Tab;
	}

	//Cree les conditions de jointures necessaires dans une requete
	static function multiJoinSql($Tab,$Data,$O){
		$j=0;
		//Recherche de l'etape de sortie
		for ($i=0;$i<sizeof($Tab);$i++) if (isset($Tab[$i]['multiFilter'])&&is_array($Tab[$i]['multiFilter'])){
			 $T = $Tab[$i];

			foreach ($T['multiFilter'] as $Mf){
				$j++;
// 				echo "--------------------------\r\n";
				//Identification et recuperation de la clef à traiter sur les associations enfantes
				$TypeAssoc = ($O->isChildOf($Mf[1]))?"child":(($O->isParentOf($Mf[1]))?"parent":"error");
				//Construction du tableau de requete
				if ($TypeAssoc=="parent"){
					$ClefAssoc = $O->getKey($Mf[1],$Mf[2],"child");
					$Te[0] = $O->getKeyInfo($Mf[1],"",true,$Mf[2]);
					//Cas inverse du parent
					$To = $ClefAssoc->getChildObjectClass();
					$Te[1] = Array(
						"Recherche" => $Mf[3],
						"Module" => $To->Module,
						"Nom" => $Mf[1],
						"Driver" => $ClefAssoc->getDriver()
					);
					$Data = sqlFunctions::joinParent($Te,$Data,0,$O,$j);
				}
				if ($TypeAssoc=="child"){
					$Ob = Sys::$Modules[$O->Module]->Db->getObjectClass($Mf[1]);
					$ClefAssoc = $Ob->getKey($O->titre,$Mf[2],"child");
					$Te[0] = $Ob->getKeyInfo($O->titre,$Mf[3],false,$Mf[2]);
					$To = $ClefAssoc->getParentObjectClass();
					$Te[1] = Array(
						"Recherche" => '',
						"Nom" => $T["Nom"],
						"Module" => $To->Module,
						"Driver" => $ClefAssoc->getDriver(),
						"Out" => 1
					);
					$Data = sqlFunctions::joinStandard($Te,$Data,0,$O,$j);
					$Data = sqlFunctions::whereSql($Te[0],$Data,sqlFunctions::getPrefixe($Te[0],"0".$j,$O),$O);
					$Data["GroupBy"][] = "m.Id";
					//$Data = sqlFunctions::mergeData($D,$Data);
				}
			}
		}
		return $Data;
	}

	static function isFilter($c){
		preg_match("#([^=|^!|^+|^<|^>|^~]+)([=!+<>~]{2})(.*)#",$c,$Out);
		if (sizeof($Out)<2)preg_match("#([^=|^!|^+|^<|^>|^~]+)([=!+<>~]{1})(.*)#",$c,$Out);
		if (sizeof($Out)<2)preg_match("#([^~]*)([~]{1})(.*)#",$c,$Out);
		return sizeof($Out)>1;
	}

	static function multiConditions($c,$Tab,$prefixe,$OData,$O,$View=null)
	{
		//cas de recherche globale
		if ($c=='*') return Array("Condition"=> Array());
		//cas standard
		preg_match("#([^=|^!|^+|^<|^>|^~]+)([=!+<>~]{2})(.*)#",$c,$Out);
		if (sizeof($Out)<2)preg_match("#([^=|^!|^+|^<|^>|^~]+)([=!+<>~]{1})(.*)#",$c,$Out);
		if (sizeof($Out)<2)preg_match("#([^~]*)([~]{1})(.*)#",$c,$Out);
		$T = (isset($Out[1]))?explode(".",$Out[1]):array();
		if (sizeof($T)>1) {$Pref=$T[0].".";$Out[1] = $T[1];}
		elseif (isset($Out[1])){
			//on verifie d'abord l'existence d'un alias du même nom
			$y = $Out[1];
			$j=false;
			if (isset($OData["Select"])){
				foreach ($OData["Select"] as $i=>$d){
					if ($OData["Select"][$i]["Alias"]==$y){
						$Out[1] = $OData["Select"][$i]["Nom"];
						$j=true;
					}
				}
			}
			if (!$j)
				$Pref = $prefixe . ".";
			else
				$Pref = "";
		}
		//gestion des alias (effort de bord des vues)
		if (isset($Out[1])&&!empty($Out[1]))
			if(preg_match("#([A-z0-9]+?)\.([A-z0-9]+)#",$Out[1],$g)){
				$Out[1] = str_replace($g[0],$g[1].'.`'.$O->langProp($g[2]).'`',$Out[1]);
			}else{
				$Out[1] = "`".$O->langProp($Out[1])."`";
			}
		$Te = (isset($Out[2])) ? $Out[2] : "";
		switch ($Te) {
			case "=" :
			case ">" :
			case "<" :
			case "<=" :
			case ">=" :
			case "!=" :
				if (is_numeric($Out[3]) || $Out[3] == "NULL")
					$Data['Condition'][] = "$Pref" . $Out[1] . $Out[2] . "" . $Out[3] . "";
				else {
					$Data['Condition'][] = "$Pref" . $Out[1] . $Out[2] . "'" . addslashes($Out[3]) . "'";

				}
				$Made = 1;
				$Data["Lien"] = "AND";
				break;
			case "><":
				$Fourchette = explode("-->",$Out[3]);
				$Data['Condition'][] = "$Pref".$Out[1].">=".$Fourchette[0];
				$Data['Condition'][] = "$Pref".$Out[1]."<=".$Fourchette[1];
				$Made=1;
				$Data['Lien']="AND";
				break;
			case "!!":
				$Data['Condition'][] = "$Pref".$Out[1]." IS NULL";
				$Made=1;
				$Data["Lien"] = "AND";
				break;
			case "=!":
				$Data['Condition'][] = "$Pref".$Out[1]." IS NOT NULL";
				$Made=1;
				$Data["Lien"] = "AND";
				break;
			case "~":
				$Data['Lien'] = "OR";
				//Suppression des pluriels
				//$c = preg_replace("#(.*)(s|e)$#","$1",$Out[3]);
				$mc= $Out[3];
				$likein = "";
				//Pour la recherche plaintext ajouter searchType="plaintext" dans la déclaration de l'objectclass
				if ($Out[1] != "") {
					$Rt = explode(" ", $mc);
					if (sizeof($Rt) > 1) {
						if ($O -> searchType == "plaintext") {
							$mct = str_replace(" ", "%", $mc);
							$Data['Condition'][] = "$Pref" . $Out[1] . " LIKE '" . addslashes($mct) . "%'";
						} else {
							//Si il y a plusierus mots clefs alors on fait une recherche avec select imbrique et exacte.
							//Recherche d'espaces
							$g = false;
							foreach ($Rt as $t) {
								if (isset($g) && $g)
									$likein .= " OR ";
								else
									$g = true;
								$likein .= "$Pref" . $Out[1] . " LIKE '".$t."%'";
							}
							$Data['Condition'][] = $likein;
							$Data['Suffixe'][] = "having count(m.Id) >= " . sizeof($Rt);
						}
					} else {
						//si il y a qu'u mot clef alors on fait une recherche floue
						$Data['Condition'][] = "$Pref" . $Out[1] . " LIKE '" . addslashes($mc) . "%'";
					}
				} else {
					$Type = "string";
					$lp = $O -> getSearchOrder();
					if (is_object($View)){
						if($lp) $lp = array_merge($lp,$View->ViewProperties);
						else $lp = $View->ViewProperties;
					}
					foreach ($lp as $sOrder) {
						if ($O -> getGlobalType($sOrder["type"]) == $Type) {
							//Gestion des langues
							$PropName = $O -> langProp($sOrder["name"], $Type);
							$pr = isset($sOrder["prefixe"])?$sOrder["prefixe"].'.':$Pref;
							$Data['Condition'][] = "$pr`" . $PropName . "` LIKE \"%" . addslashes($mc) . "%\"";
						}
					}
				}
				$Made = 1;
				break;
			default :
				$Made = 0;
				break;
		}
		if (is_numeric($c) && !$Made) {
			//IntSearch
			$Data['Condition'][] = "$prefixe.Id=" . $c . "";
		} elseif (!$Made) {
			$f = false;
			//StringSearch
			$Type = "string";
			$Data['Lien'] = "AND";
			$Obj = Sys::$Modules[$Tab["Module"]] -> Db -> getObjectClass($Tab["Nom"]);
			$imbriqSql = "$prefixe.Id= (SELECT Id FROM `" . $Obj -> Prefix . $Obj -> titre . "` WHERE ";
			if (is_array($Obj -> Cibles)){
				foreach ($Obj->Cibles as $Nom => $sOrder) {
					if ($Obj -> getPropType($Nom) == $Type) {
						//Gestion des langues
						$PropName = $O -> langProp($Nom, $Type);
						if ($f)
							$imbriqSql .= "OR";
						else
							$f = true;
						$imbriqSql .= "(`" . $PropName . "` = \"$c\")";
						if (AUTO_COMPLETE_LANG && !Sys::$User -> Admin && isset($Obj -> Proprietes[$Nom]["special"]) && $Obj -> Proprietes[$Nom]["special"] == "multi") {
							foreach ($GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE") as $Key => $Lang) {
								if ($GLOBALS["Systeme"] -> DefaultLanguage != $Lang["TITLE"]) {
									if ($f)
										$imbriqSql .= "OR";
									else
										$f = true;
									if (isset($Lang["DEFAULT"]) && $Lang["DEFAULT"] != 1) 
										$imbriqSql .= "(`" . $Key . "-" . $Nom . "` = \"$c\")";
									else
										$imbriqSql .= "(`" . $Nom . "` = \"$c\")";
								}
							}
						}
					}
				}
			}
			$imbriqSql.="LIMIT 0,1)";
			//echo "--> REQUETE IMBRIQUEE => ".$imbriqSql."\r\n";
			if ($f)
				$Data['Condition'][] =$imbriqSql;
			//Erreur si pas de recherche possible
			else throw new Exception("QUERY: No search field available for this filter ".$c);
		}
		return $Data;
	}

	static function getMultiSearch($Tab, $Data, $Rech, $prefixe, $O,$View=null) {
		/*On créé un tableau en découpant chaque (!!) et en notant son parent, son départ, son arrivée, et sa profondeur */
		$TRech = $Rech;
		$brackets = Array();
		$nump = 0;
		$prof = $maxprof = 1;
		$lastWithChilds[] = 0;
		$truePos = 0;
		$nextOpen = 0;
		$lastDone = 0;
		$brackets[0] = Array("prof" => 0, "opened" => 0, "closed" => strlen($Rech));
		while (strpos($TRech, '(!') !== false || strpos($TRech, '!)') !== false) {
			$nextOpen = strpos($TRech, '(!');
			$nextClose = strpos($TRech, '!)');
			//Si le plus proche est une ouverture
			if ($nextOpen !== false && ($nextClose > $nextOpen || $nextClose === false)) {
				$brackets[++$nump]["opened"] = $truePos + $nextOpen + 2;
				$brackets[$nump]["prof"] = $prof++;
				if ($lastDone == "o")
					$lastWithChilds[] = $nump - 1;
				$brackets[$nump]["parent"] = $lastWithChilds[count($lastWithChilds) - 1];
				$brackets[$nump]["lwc"] = $lastWithChilds;
				$TRech = substr($TRech, $nextOpen + 2);
				$truePos = $truePos + $nextOpen + 2;
				$lastDone = "o";
				if ($maxprof < $prof)
					$maxprof = $prof;
			}
			//Si le plus proche est une fermeture
			if ($nextClose !== false && ($nextOpen > $nextClose || $nextOpen === false)) {
				$Index = 0;
				foreach (array_reverse($brackets,true) as $I => $V) {
					if ($Index == 0 && empty($V["closed"]))
						$Index = $I;
				}
				$brackets[$Index]["closed"] = $truePos + $nextClose;
				$TRech = substr($TRech, $nextClose + 2);
				$truePos = $truePos + $nextClose + 2;
				$prof--;
				if ($lastDone == "c") {
					unset($lastWithChilds[count($lastWithChilds) - 1]);
				}
				$lastDone = "c";
			}
		}
		/*On range le tableau pour le rendre un peu recursif*/
		for ($j = $maxprof; $j >= 0; $j--) {
			foreach ($brackets as $K => &$brak) {
				if ($brak["prof"] == $j) {
					$FullCondition = substr($Rech, $brak["opened"], $brak["closed"] - $brak["opened"]);
					$FullCondition = preg_replace("#(\(\!.*\!\))#", "", $FullCondition);
					$brak["Lien"] = (strpos($FullCondition, "+")) ? "OR" : "AND";
					$Conditions = ($brak["Lien"] == "OR") ? preg_split('#(?<!\\\)[\+]{1}#', $FullCondition) : preg_split('#(?<!\\\)[\&]{1}#', $FullCondition);
					foreach ($Conditions as $C) {
						if (!empty($C)) {
							$tempi = sqlFunctions::multiConditions(Utils::unescape($C), $Tab, $prefixe,$Data, $O,$View);
							if (isset($tempi["Suffixe"]) && is_array($tempi["Suffixe"]))
								$Data["Suffixe"] = $tempi["Suffixe"];
							$brak["Condition"]["Groupe"][] = $tempi;
						}
					}
					unset($brak["opened"]);
					unset($brak["closed"]);
					unset($brak["lwc"]);
					unset($brak["prof"]);
					if ($j > 0) {
						$par = $brak["parent"];
						unset($brak["parent"]);
						$brackets[$par]["Condition"]["Groupe"][] = $brackets[$K];
						unset($brackets[$K]);
					}
				}
			}
		}
		$Data["Groupe"][(isset($Data["Groupe"])) ? sizeof($Data["Groupe"]) : 0] = $brackets[0];
		return $Data;
	}

	static function whereSql($Tab, $Data, $prefixe, $O,$View=null) {
		if (!empty($Tab["Recherche"]))
			$Data = sqlFunctions::getMultiSearch($Tab, $Data, $Tab["Recherche"], $prefixe, $O,$View);
		// 		if ($this->titre=="Produit")print_r($Data);
		return $Data;
	}

	/*Fonction récursive:
	 * Créé une requete a partir d'un groupe de conditions
	 * Condition d'arret : Tab[Condition][Groupe].length = 0
	 * Parametre : Tab::tableau
	 * Retourne : ::String
	 */
	static function writeConditions($Tab) {
		$Grps = (isset($Tab["Condition"]["Groupe"])) ? $Tab["Condition"]["Groupe"] : array();
		unset($Tab["Condition"]["Groupe"]);
		if (empty($Tab["Lien"]))
			$Tab["Lien"] = "AND";
		$sqlCond = "(";
		$queryStarted = false;
		for ($i = 0; $i < sizeof($Tab["Condition"]); $i++) {
			if ($i > 0)
				$sqlCond .= " " . $Tab["Lien"] . " ";
			$sqlCond .= "(" . $Tab["Condition"][$i] . ")";
			$queryStarted = true;
		}
		for ($i = 0; $i < sizeof($Grps); $i++) {
			if ($i > 0 || $queryStarted)
				$sqlCond .= " " . $Tab["Lien"] . " ";
			$sqlCond .= sqlFunctions::writeConditions($Grps[$i]);
		}
		if($sqlCond == '(') return "1"; // PAS BEAU ????????????????????????,
		$sqlCond .= ")";
		return $sqlCond;
	}

	//Constructeur magique de recherche en fonction du tableau d analyse
	static function createSql($Type, $Data, $O, $Select = "") {
		//Ajout des parametres principaux
		$sql = 'SELECT ';
		if (!strlen($Select)) {
			if ($Type != "SELECT_INTERVAL" && $Type != "VIEW") {
				$sql .= 'm.Id,m.userCreate,m.tmsCreate,m.tmsEdit,m.userEdit,m.uid,m.gid,m.umod,m.gmod,m.omod';
				//propriétés standard
				foreach ($O->Proprietes as $Key => $Prop) {
					//Priorites de langage
					$Special = (isset($Prop["special"])) ? $Prop["special"] : "";
					if (!empty($Prop["Ref"]))
						$sql .= ",r" . $Prop["Ref_Level"] . ".$Key";
					elseif ($Special == "multi"&&is_object(Sys::$User)&&Sys::$User -> Admin) {
						$sql .= ",m.`" . $O -> langProp($Key) . "` as `" . $Key . "`";
						foreach ($GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE") as $Cod => $Lang) {
							if (!isset($Lang["DEFAULT"]) || !$Lang["DEFAULT"])
								$sql .= ",m.`" . $Cod . "-" . $Key . "`";
						}
					} elseif ($Special == "multi" && AUTO_COMPLETE_LANG && $GLOBALS["Systeme"] -> CurrentLanguage != $GLOBALS["Systeme"] -> DefaultLanguage) {
						$sql .= ",m.`" . $O -> langProp($Key) . "` as `" . $Key . "`";
						foreach ($GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE") as $Cod => $Lang) {
							if (isset($Lang["DEFAULT"]) && $Lang["DEFAULT"])
								$sql .= ",m.`" . $Key . "`as `$Cod-" . $Key . "`";
						}
					} else
						$sql .= ",m.`" . $O -> langProp($Key) . "` as `" . $Key . "`";
				}
				
				//clef courtes
				foreach ($O->getParentElements() as $p){
					if ($p['type']=='fkey'&&$p['card']=='short'){
						$sql .= ",m.`" . $p['field'] . "` as `" . $p['name'] . "`";
					}
				}
			}
			//Ajout des champs Select optionnels
			if (isset($Data['Select']) && is_array($Data['Select'])) {
				foreach ($Data['Select'] as $Selection) {
					if ($sql != "SELECT ")
						$sql .= ",";
					if (!empty($Selection['Nom'])) {
						if (!isset($Selection['Alias']))
							$sql .= $Selection['Nom'];
						else
							$sql .= $Selection['Nom'] . " as `" . $Selection['Alias'] . '`';
					} else {
						$sql .= "'" . $Selection['Valeur'] . "' as `" . $Selection['Alias'] . '`';
					}
				}
			}
		} else {
			//Verificatino des tables ciblées
			$s = explode(",", $Select);
			$f = false;
			if (is_array($s))
				foreach ($s as $se) {
					if (sizeof(explode(".", $se)) == 1 && !preg_match("#COUNT#", $se))
						$sql .= ((is_string($se) && strlen($se)) ? (($f) ? ',' : '') . 'm.`' . $se . '`' : '');
					else
						$sql .= ((is_string($se) && strlen($se)) ? (($f) ? ',' : '') . $se : '');
					$f = true;
				}
		}
		//Ajout des champs Select optionnels
		if (isset($Data['Select']) && is_array($Data['Select'])) {
			foreach ($Data['Select'] as $Selection) {
				if ($sql != "SELECT ")
					$sql .= ",";
				if (!empty($Selection['Nom'])) {
					if (!isset($Selection['Alias']))
						$sql .= $Selection['Nom'];
					else
						$sql .= $Selection['Nom'] . " as `" . $Selection['Alias'] . '`';
				} else {
					$sql .= "'" . $Selection['Valeur'] . "' as `" . $Selection['Alias'] . '`';
				}
			}
		}
		//Ajout des tables et de leur alias
		$sql .= " FROM ";
		$Flag = false;
		if (isset($Data['Table']) && is_array($Data['Table']) && sizeof($Data['Table']))
			foreach ($Data['Table'] as $Table) {
				if ($Flag)
					$sql .= ",";
				$Flag = true;
				$Prefix = (isset($Table['Prefix'])) ? $Table['Prefix'] : $O -> Prefix;
				$sql .= "`" . $Prefix . $Table['Nom'] . '` as `' . $Table['Alias'] . '`';
				//Gestion des Joins
				if (isset($Table['Join']) && is_array($Table['Join']))
					foreach ($Table['Join'] as $Jo) {
						$Prefix = (isset($Jo['Prefix'])) ? $Jo['Prefix'] : $O -> Prefix;
						$sql .= " " . $Jo['Side'] . " JOIN `" . $Prefix . $Jo['Nom'] . '` as `' . $Jo['Alias'] . '` ON (' . $Jo['On'] . ')';
					}
			}
		$r = 0;
		$sql .= ' WHERE 1';
		//Gestion des droits
		if (!empty(Sys::$User -> Id) && !Sys::$User -> Admin && $O -> titre != "Connexion") {
			$GroupeDroits = Array("Lien" => "OR");
			$GroupeDroits["Condition"][] = "m.omod>=2";
			$GGroupeDroits = Array("Lien" => "AND");
			$GGroupeDroits["Condition"][] = "m.umod>=2";
			$GGroupeDroits["Condition"][] = "m.uid=" . Sys::$User -> Id;
			$GroupeDroits["Condition"]["Groupe"][] = $GGroupeDroits;
			if (isset( Sys::$User -> Groups) && is_array( Sys::$User -> Groups))
				foreach (Sys::$User->Groups as $Grp) {
					$GGroupeDroits = Array("Lien" => "AND");
					$GGroupeDroits["Condition"][] = "m.gmod>=2";
					$GGroupeDroits["Condition"][] = "m.gid=" . $Grp -> Id;
					$GroupeDroits["Condition"]["Groupe"][] = $GGroupeDroits;
				}
			$Data["Groupe"][] = $GroupeDroits;
		}
		//Ajout des conditions et des groupes de conditions
		$Linking = " AND ";
		if (isset($Data['Groupe']) && is_array($Data['Groupe'])) {
			foreach ($Data['Groupe'] as $Grp) {
				$Flag_2 = false;
				$NString = sqlFunctions::writeConditions($Grp);
				if ($NString != "" && $NString != "()")
					$sql .= $Linking . " (" . $NString . ") ";
			}
		}
		//Gestion des GROUP BY
		$Flag2 = "";
		if (isset($Data['GroupBy']) && is_array($Data['GroupBy']) && sizeof($Data['GroupBy'])) {
			$sql .= " GROUP BY ";
			foreach ($Data['GroupBy'] as $Group) {
				$sql .= "$Flag2 $Group";
				$Flag2 = ",";
			}
		}
		$Flag2 = "";
		if (isset($Data['Suffixe']) && is_array($Data['Suffixe']) && sizeof($Data['Suffixe'])) {
			foreach ($Data['Suffixe'] as $Group) {
				$sql .= "$Flag2 $Group ";
			}
		}
		//GESTION ORDER
		if (isset($Data['Order']) && sizeof($Data['Order'])) {
//			Klog::l('DATA ORDER',$Data['Order']);
			$sql .= ' ORDER BY ' . $Data['Order'];
		} else {
			$Order=Array();
			$CustomOrder = DbAnalyzer::$Order;
			//test d'une configuration par defaut dans l'objectclass
			if (isset($O -> order))
				$Order[0] = $O -> order;
			if (isset($O -> orderType))
				$Order[1] = $O -> orderType;
			//test de la demande specifique
			if (isset($CustomOrder[0])&&!empty($CustomOrder[0]))
				$Order[0] = $CustomOrder[0];
			if (isset($CustomOrder[1])&&!empty($CustomOrder[1]))
				$Order[1] = $CustomOrder[1];
			
			//Definition des ordres
			if (isset($Order[0]) && isset($Order[1])&& !empty($Order[0]) && !empty($Order[1]) && ($O->isProperties($Order[0])||$Type=="VIEW")) {
				$tmp = '';
				if ($Type!="VIEW") {
					$fld = explode(',', $Order[0]);
					$dir = explode(',', $Order[1]);
					foreach($dir as $k=>$d) {
						if($tmp) $tmp .= ',';
						if (strpos($fld[$k],".") === false) $tmp .= 'm.'.$fld[$k]." $d";
						else $tmp .= $fld[$k]." $d";
					}
					$sql.= ' ORDER BY '.$tmp;
				}
				else {
					//VIEW
					$fld = explode(',', $Order[0]);
					$dir = explode(',', $Order[1]);
					foreach($dir as $k=>$d) {
						if($tmp) $tmp .= ',';
						if (strpos($fld[$k],".") === false) $tmp .= 'm.'.$fld[$k]." $d";
						else $tmp .= $fld[$k]." $d";
					}
					$sql.= ' ORDER BY '.$tmp;
				}
			}else {
				//Par defaut on doit ranger par le champ de type order
				$Champs = $O->getSpecialProp("order");
				if (is_array($Champs)) {
					$sql .= ' ORDER BY ';
					foreach ($Champs as $Nom => $Prop) {
						$sql .= 'm.' . $Nom;
						if (isset($i) && $i)
							$sql .= ",";
						$i = true;
					}
					$sql .= ' ASC,m.Id DESC';
				}else {
					//Definition des ordres par défaut
					$tmp = '';
					$Order = DbAnalyzer::$DefaultOrder;
					$fld = explode(',', $Order[0]);
					$dir = explode(',', $Order[1]);
					foreach($dir as $k=>$d) {
						if($tmp) $tmp .= ',';
						if (strpos($fld[$k],".") === false) $tmp .= 'm.'.$fld[$k]." $d";
						else $tmp .= $fld[$k]." $d";
					}
					$sql.= ' ORDER BY '.$tmp;
				}
			}
		}

		//GESTION LIMIT
		if (isset($Data['Limit']) && sizeof($Data['Limit'])) {
			$Tab = $Data['Limit'];
			$sql .= ' LIMIT ' . $Tab[0] . ',' . $Tab[1];
		} else {
			$Tab = DbAnalyzer::$LimRequete;
			$sql .= ' LIMIT ' . $Tab[0] . ',' . $Tab[1];
		}
		return $sql;
	}

	//----------------------------------------------//
	// INTERROGATION				//
	//----------------------------------------------//
	//REnvoie le nom de la table de l objectClass en cours
	static function getTableName($O) {
		return $O -> Prefix . $O -> titre;
	}

	static function countDb($O) {
		$sql = "SELECT COUNT(*) AS Size FROM `" . $O -> Prefix . $O -> titre . "`";
		$Result = $O -> executeSql($O, $sql, "SELECT_SYS");
		return $Result["Size"];
	}

}
