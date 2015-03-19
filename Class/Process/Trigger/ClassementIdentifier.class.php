<?php
class ClassementIdentifier extends TriggerFunction{
	var $Increment;
	static $number = 0;
	function __construct($Options,$Action,$Script){
		TriggerFunction::__construct($Options,$Action,$Script);
		$this->FileName = $this->Name;
		if (isset($Options["Increment"]))$this->Increment = $Options["Increment"];
		if (isset($Options["Link"]))$this->Link = $Options["Link"];
	}
	function Exe($Input) {
		$Ident = $Input->Id;
		$Add = 1;
		$Link="";
		//ON gere le cas ou la reponse est un script
		if (isset($this->Script)&&$this->Script!=""){
			Process::registerTempVar("INPUT",$Input);
			Process::ProcessVars($this->Script);
			$Add = Process::$TempVar["OUTPUT"];
		}
		if (isset($this->Link)&&$this->Link!=""){
			Process::registerTempVar("INPUT",$Input);
			Process::ProcessingVars($this->Script);
			$Add = Process::$TempVar["OUTPUT"];
		}
		//On gere les options
		foreach ($this->Conf as $N=>$V){
			//GROUPEMENT POUR EVITER LES DOUBLONS
			if ($N=="GroupBy") $GroupBy = $Input->Get($V);
			//IDENTIFIANT DE LA LIGNE
			if ($N=="Identifier") $Ident = $Input->Get($V);
			//INCREMENTATION PAR EVNEMENT
			if ($N=="Increment") $Increment = $V;
			//INCREMENTATION PAR EVNEMENT
			if ($N=="Add") $Add = $V;
		}
		if (trim($Ident)=="") return false;
		foreach ($this->Action->Period as $P){
			//Gestion du fichier principal
			//$this->writeToFile($Input,$P,$Increment,$Ident,$Add);
			//Gestion du fichier par identifiant
			$this->writeToFile($Input,$P,$Increment,$Ident,$GroupBy,$Add,$Ident);
		}
	}
	function writeToFile($Input,$P,$Increment,$Ident,$GroupBy,$Add,$Suffix="") {
		$File = $this->CreatePath($Input,date($P,$Input->tmsEdit),$this->FileName.((!empty($Suffix))?"-".$Suffix:""));
		$F=fopen($File,"r");
		$PointPos = 0;
		$Value = 0;
		//On parcourt le fichier a la recherche de Ident
		while(!feof($F)){
			$PointPos = ftell($F);
			$Line = fgets($F);
			$LineArray = explode("|",$Line);
			if($LineArray[0]&&$LineArray[0] == $GroupBy) break;
		}
		//On reouvre le fichier pour ecrire
		$T=fopen($File.".tmp","w");
		//Si l'identifiant est deja dans le fichier
		//On ajoute les deux valeurs
//			$NewValue = (feof($F))?$Add:((isset($LineArray[1]))?$LineArray[1]:0) + $Add;
		$NewValue = intval(((isset($LineArray[1]))?$LineArray[1]:0)) + intval($Add);
		//La ligne a ecrire
		if ($Increment){
			if (isset($this->Link)&&$this->Link!=""){
				//On incremente 
				$Inc = (feof($F))?$Increment:((isset($LineArray[2]))?$LineArray[2]:0) + $Increment;
				$ToPut = "$GroupBy|$NewValue|$Inc|$Link\n";
			}else{
				//On incremente 
				$Inc = (feof($F))?$Increment:((isset($LineArray[2]))?$LineArray[2]:0) + $Increment;
				$ToPut = "$GroupBy|$NewValue|$Inc\n";
			}
		}else{
			 $ToPut = "$GroupBy|$NewValue\n";
		}
		$Val=-1;
		//on repositionne le pointer F
		rewind($F);
		//fseek($F,0);
		$maj=0;
		//On se positionne sur le nouvel emplacement dans le fichier
		while(!feof($F)) {
			//on sauvegarde la ligne
			$Line = fgets($F);
			$LineArray = explode("|",$Line);
			$Val = (isset($LineArray[1]))?$LineArray[1]:"";
			//On ecrit dans le fichier temporaire
			if  ($GroupBy==$LineArray[0]){
				$maj=1;
				fputs($T,$ToPut);
			}else{
				fputs($T,$Line);
			}
		}
		if (!$maj) fputs($T,$ToPut);
		fclose($T);
		fclose($F);
		/*if (strpos($File,'06/22') && $this->Name=="Domaines"){
			Classement::$number ++;
			if (Classement::$number>20)
				die();
		}*/
		//On supprime le fichier original
		unlink($File);
		//On renomme le fichier temporaire 
		rename($File.".tmp",$File);
	}
	function getDatas($Folders,$Ident) {
		$Result = Array();
		if (is_array($Folders)) foreach ($Folders as $Fo){
			$File= $Fo["Url"]."/".$this->FileName."-".$Ident.".rec";
			$F = @fopen($File,"r");
			if ($F)while (!feof($F)) {
				$buffer = fgets($F, 4096);
				if ($buffer!=""){
					$buffer = explode("|",trim($buffer));
					//On recherche un id equivalent
					$t=0;
					$n = sizeof($Result);
					for ($i=0;$i<$n&&!$t;$i++) if ($Result[$i]["Id"]==$buffer[0]){
						//Alors on ajoute à la sortie existante
						$Result[$i]["Value"] += $buffer[1];
						if ($this->Increment)$Result[$i]["Total"]+=$buffer[2];
						$t=1;
					}
					if (!$t){
						$Temp["Id"] = $buffer[0];
						$Temp["Value"] = $buffer[1];
						if ($this->Increment)$Temp["Total"] = $buffer[2];
						if (isset($this->Link))$Temp["Link"] = $buffer[3];
						$Result[] = $Temp;
					}
				}
			}
		}
		//On range maintenant les résultats par valeurs descendantes
		$Result = Trigger::bubbleSort($Result , "Value","DESC");
		return $Result;
	}
}
?>