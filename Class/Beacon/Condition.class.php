<?php
/*
 Copyrights MESSIN Enguerrand .Kob-Eye Tech .
 Cie EXPRESSIV France.
 */
Class Condition extends Beacon{
	var $CaseTab;
	var $CaseDefault;

	// constructeur
	function __construct() {
		$this->Data="";
		$this->Nom="Condition";
	}
	function SetFromVar($Var,$Data,$Beacon) {
		$this->Vars = $Var;
		$this->Data= $Data;
		$this->Beacon = $Beacon["BEACON"];
		// 		$this->init($Process);
	}

	function SetParams($Temp) {
		$Out="";
		for ($i=0;$i<count($Temp);$i++) {
			$Out.="'".$Temp[$i]."'";
			if ($i<(count($Temp)-1)) {
				$Out.=",";
			}
		}
		return $Out;
	}

	function init() {
		if (!$this->Init){
			$this->Process();
		}
		$this->Init=true;
	}

	function searchChild($Beacon,$Child,$First=true,$level=0){
		if ($this->Beacon=="IF"){
			$test=false;
			$Tab = $this->ChildObjects;
			$this->ChildObjects=$Tab[0];
			if (Beacon::searchChild($Beacon,$Child,$First=true,$level=0)) $test=true;
			if (isset($Tab[1]))$this->ChildObjects=$Tab[1];
			if (Beacon::searchChild($Beacon,$Child,$First=true,$level=0)) $test=true;
			if (isset($Tab))$this->ChildObjects = $Tab;
			return $test;
		}else{
			$test=false;
			$Tab = $this->ChildObjects;
			if (isset($Tab[0]))$this->ChildObjects=$Tab[0];
			if (Beacon::searchChild($Beacon,$Child,$First=true,$level=0)) $test=true;
			if (isset($Tab[1]))$this->ChildObjects=$Tab[1];
			if (Beacon::searchChild($Beacon,$Child,$First=true,$level=0)) $test=true;
			if (isset($Tab))$this->ChildObjects = $Tab;
			return $test;
		}
	}

	function Process() {
		$this->ChildObjects = Parser::Processing($this->Data);
		if ($this->Beacon=="IF") {
			$TempObjs = Parser::processSplit($this->ChildObjects,"IF","ELSE");
			$this->ChildObjects = $TempObjs;
		}else{
			$this->parseCase($this->ChildObjects);
			$this->parseDefault($this->ChildObjects);
			$this->ChildObjects = Array();
		}
		$this->Data="";
	}

	function writeCache($Url,$Id="Skin") {
		//On commence par repercuter la commande pour que le processus commence du bout des branches
		$Url.="/";
		if ($this->Beacon=="IF") {
			$j=0;
			for($i=0;$i<sizeof($this->ChildObjects[$j]);$i++) {
				if (is_object($this->ChildObjects[$j][$i]))$this->ChildObjects[$j][$i]->writeCache($Url,$Id."-IF"."-".$i."-".$this->ChildObjects[$i]->Beacon);
			}
			//Ecriture des fichiers
			$this->writeCacheFile(serialize($this->ChildObjects[0]),$Url,$Id."-IF.cache");
			$this->ChildObjects[0] = "[=".$Url.$Id."-IF.cache"."=]";
			if (is_array($this->ChildObjects[1])){
				$this->writeCacheFile(serialize($this->ChildObjects[1]),$Url,$Id."-ELSE.cache");
				$this->ChildObjects[1] = "[=".$Url.$Id."-ELSE.cache"."=]";
			}
		}else{
			for($j=0;$j<sizeof($this->CaseTab);$j++) {
				for($i=0;$i<sizeof($this->CaseTab[$j]["DATA"]);$i++) {
					if (is_object($this->CaseTab[$j]["DATA"][$i]))$this->CaseTab[$j]["DATA"][$i]->writeCache($Url,$Id."-CASE-".urlencode($this->CaseTab[$j]["VAR"])."-".$i."-".$this->CaseTab[$j]["DATA"][$i]->Beacon);
				}
				$this->writeCacheFile(serialize($this->CaseTab[$j]["DATA"]),$Url,$Id."-CASE-".urlencode($this->CaseTab[$j]["VAR"]).".cache");
				//Remplacement des objets par un lien.
				$this->CaseTab[$j]["DATA"] = "[=".$Url.$Id."-CASE-".urlencode($this->CaseTab[$j]["VAR"]).".cache"."=]";
			}
			for($i=0;$i<sizeof($this->CaseDefault);$i++) {
				if (is_object($this->CaseDefault[$i]))$this->CaseDefault[$i]->writeCache($Url,$Id."-DEFAULT"."-".$i."-".$this->ChildObjects[$i]->Beacon);
			}
			$this->writeCacheFile(serialize($this->CaseDefault),$Url,$Id."-DEFAULT.cache");
			//Remplacement des objets par un lien.
			$this->CaseDefault = "[=".$Url.$Id."-DEFAULT.cache"."=]";
		}
		//On ecrit les fichiers

	}

	function getSwitch($Var,$Data) {
		$Vars = explode("|",$Var);
		//		$Data = $this->getContent($Data);
		/*		echo "------------------------------\r\n";
		print_r($Data);*/
		if (is_array($this->CaseTab))foreach ($this->CaseTab as $Key){
			//On verifie l egalite
			if (Condition::getCondition($Vars[0],$Vars[1],$Key["VAR"])) {
				// 				echo "Condition Vrai : ".$Vars[0].$Vars[1].$Key["VAR"]."\r\n";
				$Result = Parser::processRequire($Key["DATA"]);
				return $Result;
			}
		}
		//  		echo "Default : ".$Vars[0].$Vars[1].$Key["VAR"]."\r\n";
		$Result=Parser::processRequire($this->CaseDefault);
		return $Result;
	}

	function getCondition($Val1,$Op="",$Val2=""){

		//valeur booleennes
		if ($Val1==1&&strlen($Val1)==1)return true;
		if ($Val1==0&&strlen($Val1)==0) return false;
		
		//decomposition de la valeur 1
		if (empty($Op)&&is_string($Val1)){
			//Operateur à deux caractères
			 preg_match('#(.*)(\<\=|\>\=|\!\=|\=\=|\~\=|\<\>)(.*)#i',$Val1,$Result);
			//si pas de resultat alors test operateur un seul caractere
			if (empty($Result))preg_match('#(.*)(\<|\>|\~|\=)(.*)#i',$Val1,$Result);
			if (isset($Result[1]))$Result[1] = Process::processingVars($Result[1]);
			else $Result[1] = Process::processingVars($Val1);
			if (isset($Result[3]))$Result[3] = Process::processingVars($Result[3]);
		}else{
			$Result[1] = str_replace('"',"",Process::processingVars($Val1));
			$Result[2] = $Op;
			$Result[3] = str_replace('"',"",Process::processingVars($Val2));
		}

		//Cas test existence variable objet
		if(is_object($Result[1])||is_array($Result[1])){
			if(!isset($Result[2])||empty($Result[2])) return true;
			if (isset($Result[3])&&!empty($Result[3])){
			 	if (($Result[2]=="="||$Result[2]=="=="))return $Result[1]==$Result[3];
				elseif(($Result[2]=="!="||$Result[2]=="<>"))return $Result[1]!=$Result[3];
				else return false;
			}else {
			 	if (($Result[2]=="="||$Result[2]=="=="))return false;
				elseif(($Result[2]=="!="||$Result[2]=="<>"))return true;
				else return false;
			}
		}
		//Traitement de la condition
//		if (isset($Result[1][0])&&$Result[1][0]=="[") return false;
		if (isset($Result[2])&&$Result[2]=='=') $Result[2] = '==';
		if(isset($Result[2])&&($Result[2]=="!="||$Result[2]=="<>"))return $Result[1]!=$Result[3];
		if (isset($Result[2])&&$Result[2]=="~") {
			if ($Result[3]!="")if (preg_match("#".addcslashes($Result[3],'*.()[]/\\#')."#",$Result[1])) {
				return true;
			}
			/*if ($Result[1]!="")if (preg_match("#".addcslashes($Result[1],'*.()[]/\\#')."#",$Result[3])) {
				return true;
			}*/
		}else{
			if (isset($Result[1]))$Result[1] = addslashes(str_replace('"','',$Result[1]));else $Result[1]='';
			if (isset($Result[3]))$Result[3] = str_replace('"','',$Result[3]);
			if (isset($Result[3]))$Result[3] = str_replace('&quot;','',$Result[3]);
			if (isset($Result[3]))$Result[3] = addslashes(str_replace("'",'',$Result[3])); else $Result[3] = '';
			if ((!isset($Result[2])||empty($Result[2]))&&(!isset($Result[3])||empty($Result[3]))){
				//valeur booleennes
				if ($Result[1]=="1")return true;
				if ($Result[1]=="0") return false;
				return (!empty($Result[1]))?true:false;
			}
			//Cas tout vide
			if ($Result[1]=='0'&&$Result[2]=='=='&&$Result[3]=='')return true;
			//Autre
			return eval ("if ('".utf8_encode(addslashes(htmlentities(utf8_decode(($Result[1])))))."' ".$Result[2]." '".utf8_encode(addslashes(htmlentities(utf8_decode(($Result[3])))))."')return true;");
		}
		return false;
	}

	function getIf($Var,$Objs) {
		//Recherche pr�ence du ELSE
		$this->Test=1;
		// 		$TempObjs = Process::processSplit($Objs,"IF","ELSE");
		$TempObjs = $Objs;
		$Result=Array();
		$Traitement = false;
		//Gestion de multiple condition &&
		$Vars = explode("&&",$Var);
		if (sizeof($Vars)>1) {
			$test=true;
			$Traitement = true;
			foreach ($Vars as $Cond) {
				$temp=Condition::getCondition($Cond,"","");
				// 	 			echo $Cond." -> $temp\r\n";
				if (!$temp) $test=false;
			}
		}
		//Gestion de multiple condition ||
		$Vars = explode("||",$Var);
		if (sizeof($Vars)>1&&!$Traitement) {
			$test=false;
			$Traitement = true;
			foreach ($Vars as $Cond) {
				$temp=Condition::getCondition($Cond,"","");
				if ($temp) $test=true;
			}
		}
		if (!$Traitement) {
			$test=Condition::getCondition($Var,"","");
		}
		// 		if ($test) echo "-!-!-!-> ".$Var."\r\n";
		if (!$test){
			if (isset($TempObjs)&&sizeof($TempObjs)>1) {
				//echo "Condition fausse avec un else";
				$Result= $TempObjs[1];
			}else{
				//echo "Condition fausse sans else";
				$Result= "";
			}
		}else{
			if (isset($Data2)&&sizeof($Data2)>1) {
				//echo "Condition Vraie avec un else";
				$Result= $TempObjs[0];
			}else{
				//echo "Condition Vraie sans else";
				$Result= $TempObjs[0];
			}
		}
		return Parser::processRequire($Result);
	}

	//gestion des limites
	function setCase($Vars,$Data) {
		$Vars = str_replace("\r","",$Vars);
		$Vars = str_replace("\n","",$Vars);
		$Vars = str_replace(" ","",$Vars);
		$Vars = str_replace("	","",$Vars);
		$nb=sizeof($this->CaseTab);
		$this->CaseTab[$nb]["DATA"] = $Data;
		$this->CaseTab[$nb]["VAR"] = $Vars;
		return "";
	}

	function setDefault($Vars,$Data) {
		$this->CaseDefault = $Data;
		return "";
	}
	function Generate($Skin=false) {
		if ($this->Beacon=="IF") {
			$ChildObjects=$this->getIf($this->Vars,$this->ChildObjects);
		}else{
			$ChildObjects=$this->getSwitch($this->Vars,$this->ChildObjects);
		}
        $this->Data='';
		//On propage la generatio nuniquement aux objets concern�s
		for ($i=0;$i<sizeof($ChildObjects);$i++) {
            $tmp='';
			if (isset($ChildObjects[$i])&&is_object($ChildObjects[$i])) {
				$ChildObjects[$i]->Generate($Skin);
                $tmp = $ChildObjects[$i]->Affich();
			}else {
				if (isset($ChildObjects[$i])){
                    $tmp = $ChildObjects[$i];
                    $tmp = Process::processingVars($tmp);
                    $tmp = Parser::PostProcessing($tmp);
				}
			}
            $this->Data.=$tmp;
		}
	}

	function parseCase($Data){
		$BaliseTemp["BEACON"] = "CASE";
		$BaliseTemp["FUNCTION"] = "setCase";
		$BaliseTemp["TARGET"] = $this;
		$Balise[]=$BaliseTemp;
		$BaliseTemp = "";
		$DataTemp=Parser::processChild($Data,"SWITCH",$Balise);
		return $Data;
	}

	function parseDefault($Data){
		$BaliseTemp["BEACON"] = "DEFAULT";
		$BaliseTemp["FUNCTION"] = "setDefault";
		$BaliseTemp["TARGET"] = $this;
		$Balise[]=$BaliseTemp;
		$DataTemp=Parser::processChild($Data,"SWITCH",$Balise,$this);
		return $Data;
	}

	function getVarChar($Data) {
		$Test = $Data;
		//		$Test = str_replace("	","",$Test);
		$Test = preg_replace("#\s#","",$Data);
		/*		echo "-------------------------------\r\n";
		 echo "On teste : ".$Data."|".$Test."\r\n";*/
		if ($Test!="") {
			/*			echo "-------------------------------\r\n";
			 echo "Celui la passe : ".$Data."---".$Test."\r\n";*/
			$NumBeacon = $this->Beacon;
			$this->Beacon++;
			$this->BeaconTab[$NumBeacon] = $Data;
			return "[=".$NumBeacon."=]";
		}
	}

	//Fonction Affichage du List Box Derniere Fonction a executer
	function Affich() {
		return $this->Data;
	}


}
?>