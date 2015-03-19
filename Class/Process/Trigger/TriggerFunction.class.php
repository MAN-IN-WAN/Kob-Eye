<?php

class TriggerFunction{
	
	var $Name="";
	var $Description="";
	var $Type="";
	var $Conf="";
	var $IdUnit="";
	var $Unit;
	var $Module;
	var $Action;
	var $Graph;
	var $Script;
	var $Category;
	var $FileName;
	var $PourcentTotal;
	var $Event="";
	var $Filters = "";
	

	function __construct($Options,$Action,$Script){
		if (isset($Options["Name"]))$this->Name = $Options["Name"];
		if (isset($Options["Type"]))$this->Type = $Options["Type"];
		if (isset($Options["Category"]))$this->Category = $Options["Category"];
		if (isset($Options["Event"]))$this->setEvent($Options["Event"]);
		if (isset($Options["Filter"]))$this->setFilter($Options["Filter"]);
		$this->Description = $Options["Description"];
		if (isset($Options["IdUnit"]))$this->IdUnit = $Options["IdUnit"];
		if (isset($Options["Graph"]))$this->Graph = $Options["Graph"];
		if (isset($Options["Unit"]))$this->Unit = $Options["Unit"];
		if (isset($Options["PourcentTotal"]))$this->PourcentTotal = $Options["PourcentTotal"];
		$this->Conf = $Options;
		$this->Module = $Action->Module;
		$this->Action = $Action;
		$this->Script = $Script;
	}
	function createPath($Input,$Path="",$FileName=""){
		/*
		Createur de l'emplacement et du fichier .rec
		Paramètres : $DateArray::Array pour obtenir la date donnee
		*/
		if ($FileName=="") $FileName = $this->Id;
		$File = ROOT_DIR."Stats/".$this->Module."/".$this->Action->Name;
		$File.= "/".$Path."/";
		if (!is_dir($File)){
			//echo "MKDIR ".$File." \r\n";
			fileDriver::mk_dir($File,0755);
		}
		$File.= $FileName.".rec";
		touch($File);
		return $File;
	}
	function setOutputVar($File=""){
		/*
		Mutateur de la prop OutputVar.
		Parametre : -
		Parametre optionnel : $File, le fichier concerne si l'on souhaite stocke les resultats ailleurs.
		*/
	
		//On va chercher dans le fichier la variable enregistree
		if (!$File) $File = $this->createPath();
		$Var = file_get_contents($File);
		$Array = unserialize($Var);
		$Array=Array();
		$this->OutputVar = $Array;
		//$this->Process->RegisterTempVar("OUTPUT",$Array);
	}
	function setEvent($MyEvent){
		$this->Event = $MyEvent;
	}
	function setFilter($MyFilter){
		if (empty($MyFilter)) return true;
		$this->Filters = $MyFilter;
	}
	function initFilter($Input){
		if ($this->Filters=="")return true;

		$Cond = new Condition();
		Process::registerTempVar("INPUT",$Input);
// 		echo $this->Name."TEST FILTER ".$this->Filters."\r\n";
		if (!$Cond->getCondition($this->Filters,"","")) return false;
		return true;
	}
	function Execute($Input) {
		if ($this->initFilter($Input)) $this->Exe($Input);
	}
	function Exe($Input){}
	function Record(){
		/*
		On enregistre le resultat dans un fichier.
		Parametre : -
		*/
	
		//On enregistre la nouvelle valeur de OUTPUT
		if (!$File) $File = $this->createPath();
		$F = @fopen($File,"w+");
		fwrite($F,serialize($this->OutputVar));
		fclose($F);
	}
	function getDatas($D) {
		$File = "Stats/".$this->Module."/".$this->Action->Name;
		$File.= "/".$D."/".$this->FileName.".rec";
		$F = @fopen($File,"r");
		$Result = Array();
		if ($F)while (!feof($F)) {
			$buffer = fgets($F, 4096);
			if ($buffer!=""){
				$buffer = explode("|",$buffer);
				$Temp["Id"] = $buffer[0];
				$Temp["Value"] = $buffer[1];
				$Result[] = $Temp;
			}
		}
		return $Result;
	}
}