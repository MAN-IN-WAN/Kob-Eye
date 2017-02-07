<?php
/*
 Copyrights MESSIN Enguerrand .Kob-Eye Tech .
 Cie EXPRESSIV France.
 */
class StorProc extends Beacon {

	var $Offset;
	var $Limit;
	var $Query;
	var $TempVar;
	var $LoopObjects;
	var $ChildObjects;
	var $Recursiv = false;
	var $NoLoop;
	var $NoPost=true;
	var $Module;
	var $GroupBy;
	var $OrderType;
	var $OrderVar;
	var $Select ;
	var $Data = "";

	// constructeur
	function StorProc() {
		$this->Data="";
		$this->Nom="Object";
		$this->MasterBeacon = "STORPROC";
		$this->NoLoop=false;
		$this->NoPost = true;
	}

	function SetFromVar($Var,$Data,$Beacon) {
		$this->Vars = $Var;
		$this->Data= $Data;
		$this->Beacon = $Beacon["BEACON"];
		if ($this->Beacon!="STORPROC") {
			$this->setChild("STORPROC");
		}
	}

	function init(){
		if (!$this->Init){
			$this->Process();
			$this->Init=true;
		}
	}

    function is_assoc($array) {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }
	
	function setVars ($Var) {
		$this->Vars = $Var;
		
		$Temp=explode("|",$this->Vars);
		$this->Nom = (isset($Temp[1]))?Process::processingVars($Temp[1]):"";
        //test de l'existence d'un filtre
		if(preg_match('#(\[!.*?!\])\/(.*)#', $Temp[0],$Filtres)){
			$Query = Process::processingVars($Filtres[1]);
		}else $Query = Process::processingVars($Temp[0]);
		if (is_array($Query)) {
			//Alors Il n y pas de requete a faire mais uniquement un tableau a traiter
			if(sizeof($Filtres)>1) {
//				$Filtres = explode('&', $Filtres[1]);
// mise à jour juin 2012 suite à bug sur menu avec affiche=1
    				$Filtres[2]=Process::processingVars($Filtres[2]);
                		$Filtres = explode('&', $Filtres[2]);
				foreach($Query as $k => $val) {
					$g = true;
					foreach($Filtres as $j => $f) {
						$f = Process::processingVars($f);

						preg_match("#(.*)(!=|>=|<=|~=)(.*)#s",$f,$Out);
						if (sizeof($Out)<3)preg_match("#(.*)(=|>|<)(.*)#s",$f,$Out);
						if ($g && sizeof($Out)>=3){
							if (is_array($val) && !isset( $val[$Out[1]])) $val[$Out[1]] = '';
							if (is_object($val) && !isset( $val->{$Out[1]})) $val->{$Out[1]} = '';
							switch($Out[2]) {
								case "!=" :
									$g = (is_array($val)) ? (isset( $val[$Out[1]]))?$val[$Out[1]] != $Out[3]:null : $val->{$Out[1]} != $Out[3];
								break;
								case "=" :
									$g =(is_array($val)) ? (isset( $val[$Out[1]]))?$val[$Out[1]] == $Out[3]:null : $val->{$Out[1]} == $Out[3];
								break;
								case ">=" :
									$g = (is_array($val)) ?  (isset( $val[$Out[1]]))?$val[$Out[1]] >= $Out[3]:null : $val->{$Out[1]} >= $Out[3];
								break;
								case "<=" :
									$g = (is_array($val)) ?  (isset( $val[$Out[1]]))?$val[$Out[1]] <= $Out[3]:null : $val->{$Out[1]} <= $Out[3];
								break;
								case ">" :
									$g = (is_array($val)) ?  (isset( $val[$Out[1]]))?$val[$Out[1]] > $Out[3]:null : $val->{$Out[1]} > $Out[3];
								break;
								case "<" :
									$g = (is_array($val)) ?  (isset( $val[$Out[1]]))?$val[$Out[1]] < $Out[3]:null : $val->{$Out[1]} < $Out[3];
								break;
								case "~=" :
									$g = (is_array($val)) ?  (isset( $val[$Out[1]])) ? preg_match('#'.$Out[3].'#',$val[$Out[1]]):null :preg_match('#'.$Out[3].'#',$val->{$Out[1]});
								break;
							}
						}
						if(!$g) unset($Query[$k]);
					}
				}
				// asort($Query);
			}
			//$this->QueryTab = (!$this->is_assoc($Query))?array_values($Query):$Query;
			$this->QueryTab = $Query;
			$this->Query="Array";
			$this->Module="Array";
		}else{
			$this->Query = Process::processingVars($Temp[0]);
			if (is_string($this->Query)) $this->Query = Parser::PostProcessing($this->Query);
			//Module et gestion d erreur
			if (is_string($this->Query))$deuxPoints = explode("::",$this->Query);
			if (isset($deuxPoints[0]))$this->Query = $deuxPoints[0];
			else return;
			if (sizeof($deuxPoints)>1)for ($i=1;$i<sizeof($deuxPoints);$i++) {
				$this->Select .="m.".$deuxPoints[$i];
				if ($i<sizeof($deuxPoints)-1) $this->Select .=",";
			}
			$this->QueryTab = $deuxPoints;
			$TempData=explode("/",$this->Query);
			$this->Module=$TempData[0];
			//Analyse de la requete
			$In = Info::getInfos($this->Query);
			$this->Module=(isset($In["QueryModule"]))?$In["QueryModule"]:$TempData[0];
			$this->OutModule = (isset($In["Module"]))?$In["Module"]:$TempData[0];
		}
		if ((isset($Temp[2]))&&isset($Temp[3])){
			$this->Offset = Process::processingVars($Temp[2]);
			$this->Limit = Process::processingVars($Temp[3]);
		}else{
			$this->Offset = 0;
			$this->Limit = SQL_MAX_LIMIT;
		}
		if (isset($Temp[4])&&isset($Temp[5])){
			$this->OrderType = Process::processingVars($Temp[5]);
			$this->OrderVar = Process::processingVars($Temp[4]);
		}/*else{
		$this->OrderType = "DESC";
		$this->OrderVar = "TmsEdit";
		}*/
		if(isset($Temp[6])){
			$this->Select.=Process::processingVars($Temp[6]);
		}
		if(isset($Temp[7])){
			$this->GroupBy.=Process::processingVars($Temp[7]);
		}
	}

	function ProcessLinear($Var){
        //Analyse de la requete
		$this->setVars($Var);
		if ($GLOBALS['Systeme']->isModule($this->Module)) {
			/*			echo "\r\n---------STORPROC------------\r\n";
			 print_r($this->QueryTab);*/
			//Construction/Analayse de la requete
			$TempData=explode("/",$this->Query);
			$Search="";
			for ($i=1,$c=count($TempData);$i<$c;$i++) {
				if ($i>1) $Search.="/";
				$Search.=$TempData[$i];
			}
			if ($Search=="") {
				if ($NoResult = $this->getChild(array($this->MasterBeacon,"LIMIT","ORDER"),"NORESULT")) {
					//Si NORESULT
					$this->processNoResult($NoResult);
				}else{
					//Pas de NORESULT donc aucun affichage
					//$this->ChildObjects="";
				}
				return;
			}
			//On teste si il ne s agit pas d une requete recursive
			if ($this->searchChild($this->MasterBeacon,"RECURSIV")){
				//On modifie La recherche et on ajoute L asterisque a la fin.
				//Evidement pour que ca fonctionne il doit s agir d une recherche d enfant
				//Et la donnee doit etre recursive
				$this->Recursiv = true;
				$Search.="/*";
			}
			//Execution de la requete
            $GLOBALS["Chrono"]->start("STORPROC getdata ");
            $Tab=Sys::$Modules[$this->Module]->callData($Search,"",$this->Offset,$this->Limit,$this->OrderType,$this->OrderVar,$this->Select,$this->GroupBy);
            $GLOBALS["Chrono"]->stop("STORPROC getdata ");

			//Si il s agit d une recherche recursive alors il faut trier et ranger les resultats pour
			//Repartir les resultats en mode recursif
			if ($this->Recursiv) {
				$Tab=StorProc::sortRecursivResult($Tab);
			}
			//Verifier la presence d une classe specifique
			if ((is_array($Tab))&&(sizeof($Tab)>0)&&sizeof($this->QueryTab)==1){
				//Le cas d une simple requete
				foreach ($Tab as $Key) {
					$this->Result[] = genericClass::createInstance($this->OutModule,$Key);
					
				}
				$this->parseContents($this->Result);
			}elseif(sizeof($this->QueryTab)>1){
				if (sizeof($Tab)>1&&sizeof($this->QueryTab)==2){
					foreach ($Tab as $T) {
						$gClass = genericClass::createInstance($this->OutModule,$T);
						$Methods = get_class_methods($gClass);
						$Meth = $this->QueryTab[1];
						if (in_array(strtolower($Meth),$Methods)){
							$this->Result[]=$gClass->{$Meth}();
						}else {
							$this->Result[]=$gClass->{$Meth};
						}
					}
				}elseif ($Tab&&sizeof($Tab)&&sizeof($this->QueryTab)>2){
					foreach ($Tab as $T) {
						$this->Result[$T[$this->QueryTab[1]]]=$T[$this->QueryTab[2]];
					}
				}elseif ($Tab&&sizeof($Tab)){
					$gClass = genericClass::createInstance($this->OutModule,$Tab[0]);
					$Methods = get_class_methods($gClass);
					$Meth = $this->QueryTab[1];
					if (in_array(strtolower($Meth),$Methods)){
						// 						echo "METHODE TROUVE $Meth \r\n";
						$this->Result[]=$gClass->{$Meth}();
					}else{
						$this->Result[]=$gClass->{$Meth};
					}
				}
				if (sizeof($this->Result)>0) {
					$this->parseContents($this->Result);
				}else {
					if ($NoResult = $this->getChild(array($this->MasterBeacon,"LIMIT","ORDER"),"NORESULT")) {
						//Si NORESULT
						$this->processNoResult($NoResult);
					}else{
						//Pas de NORESULT donc aucun affichage
                        $this->Data="";
					}
				}
			}else{
				// 				echo "------------------PAS DE RESULTAT-----------------\r\n".$this->Vars."\r\n";
				//Donc pas de resultat
				if ($NoResult = $this->getChild(array($this->MasterBeacon,"LIMIT","ORDER"),"NORESULT")) {
					//Si NORESULT
					$this->processNoResult($NoResult);
				}else{
					//Pas de NORESULT donc aucun affichage
                    $this->Data="";
				}

			}
		}elseif (is_string($this->Module)&&!empty($this->Module)&&$this->Module!="Array"){
			//ITERATION SUR UN ENTIER
			$Test = $this->Module;
			settype($Test,"int");
			if ($Test<$this->Module) $Test++;
			if ($Test>0) {
				//Donc on genere le tab
				for ($i=$this->Offset;$i<$Test&&$i<$this->Offset+$this->Limit;$i++) {
					$this->Result[]=$i;
				}
			}
			$this->parseContents($this->Result);
		}elseif ($this->Module=="Array"){
			//ITERATION SUR UN TABLEAU
			
			if (is_array($this->QueryTab)&&sizeof($this->QueryTab)) {
				$i=0;
				foreach ($this->QueryTab as $key=>$value) {
					if ($i>=$this->Offset&&$i<($this->Offset+$this->Limit)&&$i<(sizeof($this->QueryTab))) $this->Result[$key] = $value;
					$i++;
				}
				//Tri
				if (!empty($this->OrderVar)&&!empty($this->OrderType)){
					$this->Result = Storproc::SpBubbleSort($this->Result,$this->OrderVar,$this->OrderType);
				}
                if (isset($this->Result)&&sizeof($this->Result))
                    $this->parseContents($this->Result);
			}else{
				if ($NoResult = $this->getChild(array($this->MasterBeacon,"LIMIT","ORDER"),"NORESULT")) {
					//Si NORESULT
					$this->processNoResult($NoResult);
				}else{
					//Pas de NORESULT donc aucun affichage
                    $this->Data="";
				}
			}
		}else{
			//			echo "\r\n----------RECHERCHE VIDE---------".$this->Vars."--\r\n";
			if ($NoResult = $this->getChild(array($this->MasterBeacon,"LIMIT","ORDER"),"NORESULT")) {
				//Si NORESULT
				$this->processNoResult($NoResult);
			}else{
				//Pas de NORESULT donc aucun affichage
                $this->Data="";
			}
		}
		unset($this->Result);
		return ;
	}

	static function sortRecursivResult($Tab,$NomTab="RECURSIV_TAB") {
		//Il faut trouver la clef recursive minimale
		$Clef=-1;
		$Res = null;
		$Result=Array();
		if (is_array($Tab))foreach ($Tab as $Res) {
			if (is_array($Res))
				if ($Res["ClefReflexive"]<$Clef||$Clef==-1) $Clef=$Res["ClefReflexive"];
			if (is_object($Res))
				if ($Res->ClefReflexive<$Clef||$Clef==-1) $Clef=$Res->ClefReflexive;
		}
		if (is_array($Res))
			$Result = StorProc::sortRecursiv($Tab,$Clef,(!empty($Res["Historique"]))?$Res["Historique"]:Array(),$NomTab);
		if (is_object($Res))
			$Result = StorProc::sortRecursiv($Tab,$Clef,(!empty($Res->Historique))?$Res->Historique:Array(),$NomTab);
		return $Result;
	}

	static function sortRecursiv($Tab,$Clef,$Histo=Array(),$NomTab="RECURSIV_TAB") {
	 	$Result=Array();
		//On recherc dans le tableau les occurences enfantes de la clef
		if (is_array($Tab))foreach ($Tab as $Res) {
			if ((is_array($Res)&&$Res["ClefReflexive"]==$Clef)||(is_object($Res)&&$Res->ClefReflexive==$Clef)){
				$Histo2 = $Histo;
				if (is_array($Res)){
					$Histo2[] = Array("Id"=>$Res["Id"],"ObjectType"=>$Res["ObjectType"]);
					$Res[$NomTab]= StorProc::sortRecursiv($Tab,$Res["Id"],$Histo2,$NomTab);
					$Res["Historique"] = $Histo;
				}
				if (is_object($Res)){
					$Histo2[] = Array("Id"=>$Res->Id,"ObjectType"=>$Res->ObjectType);
					$Res->{$NomTab}= StorProc::sortRecursiv($Tab,$Res->Id,$Histo2,$NomTab);
					$Res->Historique = $Histo;
				}
				//Il faut egalement reconstituer l historique
				$Result[]=$Res;
			}
		}
		return $Result;
	}

	/**
	 * cleanRecursivArrays
	 * clean array result from database 
	 * delete id, history, tmscreate etc....
	 */
	 static function cleanRecursivArrays($tab,$recurskey){
	 	$Result=Array();
		//On recherc dans le tableau les occurences enfantes de la clef
		if (is_array($tab))foreach ($tab as $Res) {
			unset($Res["Id"]);
			unset($Res["tmsCreate"]);
			unset($Res["tmsEdit"]);
			unset($Res["userCreate"]);
			unset($Res["userEdit"]);
			unset($Res["uid"]);
			unset($Res["gid"]);
			unset($Res["umod"]);
			unset($Res["gmod"]);
			unset($Res["omod"]);
			unset($Res["Historique"]);
			unset($Res["ClefReflexive"]);
			unset($Res["Bg"]);
			unset($Res["Bd"]);
			unset($Res["QueryType"]);
			unset($Res["Query"]);
			unset($Res["__Liaison_J0"]);
			unset($Res["__Liaison_titre_J0"]);
			unset($Res["__Liaison_J1"]);
			unset($Res["__Liaison_titre_J1"]);
			unset($Res["__Liaison_J2"]);
			unset($Res["__Liaison_titre_J2"]);
			unset($Res["Query"]);
			if (is_array($Res[$recurskey])){
				$Res[$recurskey]= StorProc::cleanRecursivArrays($Res[$recurskey],$recurskey);
			}
			$Result[] = $Res;
		}
		return $Result;
	 }

	function processNoResult($NoResult){
		$this->NoLoop = true;
        $out='';
        if (is_array($NoResult->ChildObjects)) {
            for ($j = 0, $c = sizeof($NoResult->ChildObjects); $j < $c; $j++) {
                if (is_object($NoResult->ChildObjects[$j])) {
                    $tmp = $NoResult->ChildObjects[$j];
                    $tmp->Generate();
                    $out .= $tmp->Affich();
                } else {
                    $tmp = Process::processingVars($NoResult->ChildObjects[$j]);
                    $out .= $tmp;
                }
            }
        }
        $this->Data = $out;
		return ;
	}

	function processRecursiv($Vars){
		$this->Recursiv = true;
		//$this->Vars = Process::processingVars($this->Vars);
		//$this->Vars = Parser::PostProcessing($this->Vars);
		//$this->Query = Process::processVars("STORPROC-QUERY","STOR")."/".$this->Vars;
		//$this->ChildObjects = Process::processVars("STORPROC-OBJ","STOR");
		//$this->PostObjects = Process::processVars("STORPROC-POSTOBJ","STOR");
		//if ($this->PostObjects=="[!STORPROC-POSTOBJ!]") $this->PostObjects="";
		//$this->Nom = Process::processVars("STORPROC-VAR","STOR");
		//$this->Vars = $this->Query."|".$this->Nom;
		$Temp = Process::processVars("STORPROC-RESULT","STOR");
		$this->setVars($this->Vars);
		if (is_array($Temp))foreach ($Temp as $Res) {
			$this->Result[] = genericClass::createInstance($this->Module,$Res);
		}
		$this->parseContents($this->Result);
		return ;
	}

	function loopData($Tab){
        $TempChild = $this->ChildObjects;
 		//unset($this->ChildObjects);
		$k=0;
		if (isset(Process::$TempVar["Key"]))$OldKey = Process::$TempVar["Key"];
		if (isset(Process::$TempVar["Pos"]))$OldPos = Process::$TempVar["Pos"];
		if (isset(Process::$TempVar["Level"])&&Process::$TempVar["Level"]>=0&&$this->Beacon!=$this->MasterBeacon){
			$Level = Process::$TempVar["Level"]+1;
		}else{
			$Level = 0;
		}

		if (isset(Process::$TempVar["NbResult"]))$OldNbResult = Process::$TempVar["NbResult"];
		if (is_array($Tab))$TabKey = array_keys($Tab);
        $tmp = '';
		if (sizeof($Tab)) foreach ($TabKey as $Key){
            Process::RegisterTempVar($this->Nom,$Tab[$Key]);
			Process::$TempVar["Key"]=$Key;
			Process::$TempVar["Pos"]=$k+1;
			Process::$TempVar["Level"]=$Level;
			// 			echo "ZOB TEST LEVEL ---> $Level \r\n";
			Process::$TempVar["NbResult"]=sizeof($Tab);
			if ($this->Recursiv){
				Process::RegisterTempVar($this->MasterBeacon."-RESULT",$Tab[$Key]->RECURSIV_TAB);
			}
            if (is_array($TempChild)) {
                for ($j = 0, $max = sizeof($TempChild); $j < $max; $j++) {
                    if (is_object($TempChild[$j])) {
                        $to = $TempChild[$j];
                        $to->Generate();
                        $tmp .= $to->Affich();
                    } else {
                        $GLOBALS["Chrono"]->start("STORPROC string ");
                        $to = $TempChild[$j];
                        $to = Process::processingVars($to);
                        $tmp .= Parser::PostProcessing($to);
                        //$tmp.=$to;
                        $GLOBALS["Chrono"]->stop("STORPROC string ");
                    }
                }
            }
			$k++;
		}
        $this->Data = $tmp;
		Process::$TempVar["Level"]=0;
		if (isset($OldKey))Process::$TempVar["Key"]=$OldKey;
		if (isset($OldPos))Process::$TempVar["Pos"]=$OldPos;
		if (isset($OldNbResult))Process::$TempVar["NbResult"]=$OldNbResult;
		return ;
	}

	//gestion des limites
	function processLimit($Vars) {
		$Vars = Process::processingVars($Vars);
		$Vars = Parser::PostProcessing($Vars);
		$Temp = explode("|",$Vars);
		$Offset = $Temp[0];
		$Limit = $Temp[1];
		//Recuperation du tableau de la variable
		$Tab = Process::processVars($this->MasterBeacon,"STOR");
		$this->Nom=Process::processVars($this->MasterBeacon."-VAR","STOR");
		//Construction du tableau des objets concernés
		$Max = $Offset+$Limit;
		if ($Max>sizeof($Tab)) $Max=sizeof($Tab);
		$i=0;
		if (is_array($Tab))foreach ($Tab as $K=>$C){
			if ($i>=$Offset&&$i<$Max)$TabTemp[$K]=$C;
			$i++;
		}
		return (isset($TabTemp))?$this->parseContents($TabTemp):'';
	}

	function processOrder($Vars) {
		$Vars = Process::processingVars($Vars);
		$Vars = Parser::PostProcessing($Vars);
		$Vars = explode("|",$Vars);
		$Champs = $Vars[0];
		$Type = $Vars[1];
		$Tab = Process::processVars($this->MasterBeacon,"STOR");
		$this->Nom=Process::processVars($this->MasterBeacon."-VAR","STOR");
		$Tab=Storproc::SpBubbleSort($Tab,$Champs,$Type);
		//On ordonne la table
		//et on renvoie au parseContents
		return $this->parseContents($Tab);
	}

	static function SpBubbleSort($tableau , $triChamp,$Type="ASC"){
		$nbEnregistrement = sizeof($tableau);
		if(isset($tableau[0])&&is_array($tableau[0])) {
			$Champs = $tableau[0];
			if (!isset($Champs[$triChamp])) return $tableau;
		}
		//EM-20150203
		//reindexation du tableau dans le cas d'un filtre
		$tableau = array_values($tableau);
		switch ($Type) {
			case "DESC":
				if (!isset($tableau[0])) break;
				if (isset($tableau[0])&&is_object($tableau[0])) $bubble=1; else $bubble = 0;
				for ($bubble; $bubble<$nbEnregistrement; $bubble++){
					for ($position = $nbEnregistrement-1; $position >0; $position--){
						if (is_object($tableau[$position])){
							$Champs = get_object_vars($tableau[$position]);
							$ChampsPrec = get_object_vars($tableau[$position-1]);
						}else{
							$Champs = $tableau[$position];
							$ChampsPrec = $tableau[$position-1];
						}
						if($Champs[$triChamp]>$ChampsPrec[$triChamp]){
							$temp = $tableau[$position];
							$tableau[$position] = $tableau[$position-1];
							$tableau[$position-1] = $temp;
						}
					}
				}
				break;
			default:
			case "ASC":
				//if ($this->OrderVar=="Ordre") print_r($tableau);
				if (!isset($tableau[0])) break;
				if (is_object($tableau[0])) $bubble=1; else $bubble = 0;
				for ($bubble = 0; $bubble<$nbEnregistrement; $bubble++){
					for ($position = $nbEnregistrement-1; $position >0; $position--){
						if (isset($tableau[$position])&&is_object($tableau[$position])){
							$Champs = get_object_vars($tableau[$position]);
							$ChampsPrec = get_object_vars($tableau[$position-1]);
						}else{
							$Champs = $tableau[$position];
							$ChampsPrec = $tableau[$position-1];
						}
						if($Champs[$triChamp]<$ChampsPrec[$triChamp]){
							$temp = $tableau[$position];
							$tableau[$position] = $tableau[$position-1];
							$tableau[$position-1] = $temp;
						}
					}
				}
				break;
			case "RANDOM":
				shuffle($tableau);
				break;
		}
		return $tableau;
	}

	function Generate($Skin=false) {
        //On transforme les parametres
		//On execute le storproc
		switch ($this->Beacon) {
			CASE "RECURSIV":
				$this->processRecursiv($this->Vars);
				break;
			CASE "ORDER":
				$this->processOrder($this->Vars);
				break;
			CASE "LIMIT":
				$this->processLimit($this->Vars);
				break;
			default:
				$this->processLinear($this->Vars);
				break;
		}
		return ;
	}


	function parseContents($Tab){
        $this->Data="";
		//On recherche l existence d enfants
		if ($this->searchChild($this->MasterBeacon,"LIMIT")||$this->searchChild($this->MasterBeacon,"ORDER")) {
			Process::RegisterTempVar($this->MasterBeacon,$Tab);
			Process::RegisterTempVar($this->MasterBeacon."-VAR",$this->Nom);
			Process::$TempVar["NbResult"]=sizeof($Tab);
			$this->NoLoop = true;
			for ($j=0,$max=sizeof($this->ChildObjects);$j<$max;$j++) {
				if (is_object($this->ChildObjects[$j])){
                    $tmp = $this->ChildObjects[$j];
					$tmp->Generate();
                    $this->Data.= $tmp->Affich();
				}else{
					$tmp = Process::processingVars($this->ChildObjects[$j]);
					$this->Data .= Parser::PostProcessing($tmp);
				}
			}
		}else{
			if (($this->searchChild($this->MasterBeacon,"RECURSIV"))&&($this->Beacon!="RECURSIV")){
				Process::RegisterTempVar($this->MasterBeacon."-QUERY",$this->Query);
				Process::RegisterTempVar($this->MasterBeacon."-OBJ",$this->ChildObjects);
				Process::RegisterTempVar($this->MasterBeacon."-VAR",$this->Nom);
			}
			$this->loopData($Tab);
		}
		return ;
	}

	//Fonction Affichage du List Box Derniere Fonction a executer
	function Affich() {
		//$Data = "";
		//Le contenu du fichier retravaill�
		/*if ($this->Beacon!="NORESULT"){
			if (isset($this->ChildObjects))$Data = Parser::getContent($this->ChildObjects);
		}*/
		return (isset($this->Data))?$this->Data:'';
	}



}
?>
