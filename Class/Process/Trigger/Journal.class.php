<?php
class Journal extends TriggerFunction{
	var $Identifier;
	function __construct($Options,$Action,$Script){
		TriggerFunction::__construct($Options,$Action,$Script);
		$this->FileName = $this->Name;
		$this->Identifier = $Options["Identifier"];
	}
	function Exe($Input) {
		//On execute pour chaque periode
		foreach ($this->Action->Period as $P){
			//ON gere le cas ou la reponse est un script
			if ($this->Script!=""){
				Process::registerTempVar("INPUT",$Input);
				Process::ProcessVars($this->Script);
				$Add = (isset(Process::$TempVar["OUTPUT"]))?Process::$TempVar["OUTPUT"]:'';
			}else{
				$Add = time()."|".$this->Event;
			}
			$Add.="\n";
			$Id = $this->Identifier;
			if (isset($Input->{$Id})){
				$File = $this->CreatePath($Input,date($P,$Input->tmsCreate),$this->FileName."-".$Input->{$Id});
				$F=@fopen($File,"a+");
				$ToPut = $Add;
				fputs($F,$ToPut);
				fclose($F);
				//Journal general
				$File = $this->CreatePath($Input,date($P,$Input->tmsCreate),$this->FileName); 
				$F=@fopen($File,"a+");
				$ToPut = $Add;
				fputs($F,$ToPut);
				fclose($F);
			}
		}
	}
	function getDatas($D,$O="") {
		$File = "Stats/".$this->Module."/".$this->Action->Name;
		$Result = Array();
		if ($O!="")$Fil= $File."/".$D."/".$this->FileName."-".$O.".rec";
		else $Fil= $File."/".$D."/".$this->FileName.".rec";
		if (!$F=@fopen($Fil,"r")) return false;
		if ($F)while (!feof($F)) {
			$buffer = fgets($F, 4096);
			if ($buffer!=""){
				$buffer = explode("|",trim($buffer));
				$Temp["Date"] = $buffer[0];
				$Temp["Message"] = $buffer[1];
				$Result[] = $Temp;
			}
		}
		fclose ($F);
		return $Result;
	}
}
?>