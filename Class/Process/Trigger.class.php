<?php
class Trigger{
	
	var $Name="";
	var $Target="";
	var $Period="";
	var $Functions = Array();
	var $InputVar;
	var $OutputVar;
	var $Module="";

	/*
	Constructeur de la classe Trigger.
	Parametres : $MyName, le nom de la trigger.
	*/
	function __construct($Xml,$Module){
		$this->setModule($Module);
		if ($Xml!="")$this->initFromXml($Xml);
	}
	/*
	Destructeur de la classe trigger
	*/
	function __destruct(){
	}
	/*
	Initialisation depuis un fichier xml
	*/
	function initFromXml($Xml){
		$this->setTarget($Xml["@"]["Object"]);
		$this->setPeriod($Xml["@"]["Period"]);
		for ($i=0;$i<count($Xml["#"]["FUNCTION"]);$i++){
			$this->addFunction($Xml["#"]["FUNCTION"][$i]["@"],trim($Xml["#"]["FUNCTION"][$i]["#"]));
		}
		$this->Name = $Xml["@"]["Name"];
	}
	//******************************//
	//	SETTERS			//
	//******************************//
	function setTarget($MyTarget){
		$this->Target = $MyTarget;
	}
	function setModule($MyModule){
		$this->Module = $MyModule;
	}
	function setInput($Object){
		$this->InputVar = $Object;
	}
	function setPeriod($MyPeriods){
		$this->Period = explode(",",$MyPeriods);
	}
	function addFunction($Options,$Script){
		/*
		Ajout à la propriete PresetFunctions.
		Parametres :    $FunctionName::String le nom de la function, 
				$FunctionType::String la fonction a appeler
				$FunctionOptions::String les options pour la fonction a appeler
		*/
		$this->Functions[$Options["Name"]] = new  $Options["Type"]($Options,$this,$Script);
	}
	//******************************//
	//	EXECUTION		//
	//******************************//

	function Execute($Input){
		$this->setInput($Input);
		if (is_array($this->Functions))foreach ($this->Functions as $N=>$F){
			$F->Execute($Input);
		}
	}

	//****************************//
	//	FONCTION SORTIE 
	//****************************//

	function mergeArrays($Arrays){
		/*
		Fusionne deux tableaux, et procede a des additions si necessaires.
		Parametre : $Arrays::Array, un tableau de tableaux
		Resultat : Un tableau
		*/
	
		$Fields = array_keys($Arrays[0]);
		$Values = Array();
		$R = Array();
		foreach ($Fields as $Field)
		{
			for ($i=0;$i<sizeof($Arrays);$i++)
			{
				if (!empty($Arrays[$i][$Field])) $Values[$Field][] = $Arrays[$i][$Field];
			}
			if (is_numeric($Arrays[0][$Field])) $R[$Field] = array_sum($Values[$Field]);
			else $R[$Field] = $Values[$Field];
		}
		return $R;
	}


	function getDatas($F,$Dd,$Df){
		$Result = $this->Functions[$F]->getDatas($this->getFoldersByDate($Dd,$Df));
		return $Result;
	}
	function getFunctions($Req) {
		return ($Req!="")?Array($this->Functions[$Req]):$this->Functions;
	}
	function getInfos($Req) {
		$Result = $this->getFunctions($Req);
		return $Result;
	}
	function getCategory($T="",$Q="") {
		if (!$T){
			if (is_array($this->Functions)) foreach ($this->Functions as $F) {
				if ($F->Category!="")$Cat[] = $F->Category; else $Cat[] = $F->Name;
			}
			return $Cat;
		}
		else {
			if (is_array($this->Functions)) foreach ($this->Functions as $Fr){
				if ($Fr->Category==$T) {
					$Cat[]=$Fr;
					$Result[$Fr->Name] = $this->Functions[$Fr->Name]->getDatas($Q);
				}
			}
			$Result["Functions"] = $Cat;			
			return $Result;
		}
	}
	function getFoldersByDate($Dd,$Df) {
		//selection affichage
		$Delta = $Df-$Dd;
		$Affich="";
		$Result="";
		//Annee
		if ($Delta/31536000>2) $Affich = "Annee";
		//Mois
		if ($Delta/2592000>2) $Affich = "Mois";
		//Jour
		if (isset($Affich)) $Affich = "Jour";
		//Requete affichage
		$File = "Stats/".$this->Module."/".$this->Name;
		$Result = Array();
		//Construction requete dossier
		switch ($Affich){
			case "Annee":
				for ($i=date("Y",$Dd);$i<=date("Y",$Df);$i++){
					$T["Url"] = $File."/".$i;
					$T["Name"] = $i;
					$Folders[] = $T;
				}
			break;
			case "Mois":
				$Ad = date("Y",$Dd);
				$Af = date("Y",$Df);
				$Md = date("m",$Dd);
				$Mf = date("m",$Df);
				for ($i=$Ad;$i<=$Af;$i++){
					for ($j=$Md;($j<=$Mf&&$i==$Af)||($j<=12&&$i<$Af);$j++){
						$T["Url"] = $File."/".$i."/".sprintf("%02d", $j);
						$T["Title"] = sprintf("%02d", $j)."/".$i;
						$T["Name"] = $j;
						$Folders[] = $T;
					}
					$Md=1;
				}
			break;
			case "Jour":
				$Ad = date("Y",$Dd);
				$Af = date("Y",$Df);
				$Md = date("m",$Dd);
				$Mf = date("m",$Df);
				$Jd = date("d",$Dd);
				$Jf = date("d",$Df);
				for ($i=$Ad;$i<=$Af;$i++){
					for ($j=$Md;($j<=$Mf&&$i==$Af)||($j<=12&&$i<$Af);$j++){
						$NbJourMois = 31;
						for ($k=$Jd;($k<=$Jf&&$j==$Mf)||($k<=$NbJourMois&&$j<$Mf);$k++){
							$T["Url"] = $File."/".$i."/".sprintf("%02d", $j)."/".sprintf("%02d", $k);
							$T["Title"] = sprintf("%02d", $k)."/".sprintf("%02d", $j)."/".$i;
							$T["Name"] = $k;
							$Folders[] = $T;
						}
						$Jd=1;
					}
					$Md=1;
				}
			break;
		}
		return $Folders;
	}
	function getFolders($Req="",$Path=""){
		$Result="";
		if ($Path=="")$Path = "Stats/".$this->Module."/".$this->Name;
		if ($Req!="") $Path=$Path."/".$Req;
		if (is_dir($Path)) {
			if ($dh = opendir($Path)) {
				while (($file = readdir($dh)) !== false) {
					if ($file != "." && $file != ".." &&is_dir($Path."/".$file)) {
         				  	$T["Name"] = $file;
         				    	//$T["Folders"] = $this->getFolders($Path,$file);
					 	$Result[] = $T;
        				}
				}
				closedir($dh);
			}
		}
		return $Result;
	}
	static function bubbleSort($tableau , $triChamp,$Type){
		$nbEnregistrement = sizeof($tableau);
		switch ($Type) {
			case "DESC":
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
				if (is_object($tableau[0])) $bubble=1; else $bubble = 0;
				for ($bubble = 0; $bubble<$nbEnregistrement; $bubble++){
					for ($position = $nbEnregistrement-1; $position >0; $position--){
						if (is_object($tableau[$position])){
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

}
?>