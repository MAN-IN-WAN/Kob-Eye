<?php
class TotalIdentifier extends TriggerFunction{
	function __construct($Options,$Action,$Script){
		TriggerFunction::__construct($Options,$Action,$Script);
		$this->FileName = $this->Name;
	}
	function Exe($Input) {
//		echo $this->FileName."=> ".$this->XScript."\n";
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
		//On execute pour chaque periode
		foreach ($this->Action->Period as $P){
			//Gestion du fichier
			$Add=1;
			//ON gere le cas ou la reponse est un script
			if ($this->Script!=""){
				Process::registerTempVar("INPUT",$Input);
				Process::ProcessVars($this->Script);
				$Add = Process::$TempVar["OUTPUT"];
			}
			$File = $this->CreatePath($Input,date($P,$Input->tmsEdit),$this->FileName."-".$Ident); 
			if (!$F=@fopen($File,"r")) return false;
			$Line = fgets($F);
			$Line= ($Line>0)?$Line:0;
			$NewValue =  intval($Line)+intval($Add);
 			//echo "ECRITURE ".$this->FileName." ".date($P,$Input->tmsEdit)." OLD $Line + $Add NEW $NewValue \r\n";
			//La ligne a ecrire
			fclose($F);
			$F=@fopen($File,"w");
			$ToPut = $NewValue;
			fputs($F,$ToPut);
			fclose($F);
		}
	}
	/*
	*INPUT $D => Timestamp Date
	*
	*/
	function getDatas($Folders,$Ident="") {
                klog::l("getdatas $Ident");
		if (is_array($Folders)) foreach ($Folders as $Fo){
			$err=0;
			$Fil= $Fo["Url"]."/".$this->FileName."-".$Ident.".rec";
			if (!$F=@fopen($Fil,"r")) $err=1;
			if (!$err)$buffer = fgets($F, 4096);
			else $buffer=0;
			$Temp["Id"] = $Fo["Name"];
			$Temp["Title"] = $Fo["Title"];
			$Temp["TimeStamp"] = $Fo["TimeStamp"];
			switch ($this->Unit){
				/*case "Secondes":
					$Temp["Value"] = date("H:i:s",$buffer);
				break;*/
				default:
					$Temp["Value"] = $buffer;
				break;
			}
			$Result[] = $Temp;
			if (!$err)fclose ($F);
		}
		return $Result;
	}
}
?>