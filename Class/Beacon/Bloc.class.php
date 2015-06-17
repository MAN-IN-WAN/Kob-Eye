<?php
class Bloc extends Beacon {
	//CLASS BLOC
	var $RawData;
	var $VarData;
	var $RawLoaded = false;
	var $ContentLoaded = false;
	var $Content;
	var $BlObjects;
	var $BlData;
	var $PostData;
	var $PostObjects;
	var $PostBlData;
	var $ObjVars;
	var $PostBlObjects;
	var $ChildObjects;
	var $ChildData;
	var $Path;

	// 	INITIALISATION
	function Bloc() {
	}

	function setFromVar($Var,$Data,$Beacon) {
		$this->Vars = $Var;
		$this->Data = $Data;
		$this->Beacon = $Beacon["BEACON"];
		//$this->init($Process);
	}

	function initBloc(){
		$TempVar = explode("|",$this->Vars);
		$NomBloc = $TempVar[0];
		if (!empty(Sys::$BlocLoaded[$NomBloc])){
			$this->RawData=Sys::$BlocLoaded[$NomBloc];
			$this->RawLoaded=true;
			$this->ContentLoaded=true;
		}else{
			$this->loadTemplate($NomBloc);
			$this->Path = $NomBloc;
			Sys::$BlocLoaded[$NomBloc] = $this->RawData;
		}
	}

	function init(){
		if (!$this->Init){
			switch ($this->Beacon) {
				case "MODULE":
					//$this->setModule();
					$this->INITOK="NB ".sizeof($this->ChildObjects);
				break;
				default:
				case "BLOC":
					$this->initBloc();
					$this->BlProcess();
				break;
			}
			$this->Process();
			//On initialise les enfants
/*			if (sizeof($this->ChildObjects)) {
				for($i=0;$i<sizeof($this->ChildObjects);$i++) {
					if (is_object($this->ChildObjects[$i]))$this->ChildObjects[$i]->init($Process);
				}
			}*/
		}
		$this->Init++;
	}

	function setModule() {
		$y=explode("?",$this->Vars);
		if (!sizeof($this->ChildObjects)){
  			if (!preg_match("#\[\!.*\!\]#",$y[0])){
				preg_match("#(.*?)[.\/](.*)#", $y[0], $Out);
				if ($Out[1]!=""){
					$Module = $Out[1];
				}else $Module = $Vars;
				preg_match("#".$Module."\/(.*)#", $y[0], $Out);
				$Module = str_replace(" ","",$Module);
				$Bloc=$GLOBALS['Systeme']->Modules[$Module]->setData($Out[1]);
				if (is_array($Bloc->ChildObjects))foreach ($Bloc->ChildObjects as $C)if (is_object($C))$this->ChildObjects[]=$C; else $this->ChildObjects[]=$C;
				$GLOBALS["Systeme"]->Log->Log("CHARGEMENT BLOC ".sizeof($Bloc->ChildObjects)." ".$Out[1]);
				$this->LOADED = "OK";
  			}else{
				 $GLOBALS["Systeme"]->Log->Log("BLOC A PAS CHARGE ".$y[0]);
				$this->NOTLOADED = "OK";
			}
		}else{
			$GLOBALS["Systeme"]->Log->Log("DEJA INITIALISE ".$y[0]);
			$this->ALREADYLOADED++;
		}
	}

	function loadData($Data) {
		$this->Beacon = "BLOC";
		$this->Data = $Data;
		$this->RawLoaded = true;
	}

	function loadTemplate($NomBloc){
		if (file_exists("Skins/".Sys::$User->Skin."/".$NomBloc.".bl")){
			$this->Path="Skins/".Sys::$User->Skin."/".$NomBloc.".bl";
		}else{
			if (file_exists("Skins/".Sys::$DefaultSkin."/".$NomBloc.".bl")){
				$this->Path="Skins/".Sys::$DefaultSkin."/".$NomBloc.".bl";
			}
		}
		if ($this->Path!="") {
			fopen($this->Path,'r');
			$this->RawData=implode ('', file ($this->Path));
			$this->RawLoaded=true;
			$this->ContentLoaded=true;
			//$GLOBALS['Systeme']->BlockList[]=$this;
		}else{
			//c donc directement le contenu Xml
			return  "Impossible d ouvrir Le fichier Bloc ".$this->Path." Skin ".Sys::$Skin;
		}
	}


	// 	PROCESSUS
	function BlProcess() {
		// 		echo "--> BLPROCESS START \r\n";
		if ($this->Vars!="") $this->RawData = $this->loadVars($this->Vars,$this->RawData);
		$this->BlObjects = Parser::Processing($this->RawData,false);
		if (isset($Process->PostObjects))$this->PostBlObjects = Parser::PostObjects;
		$this->RawData="";
		// 		print_r($this);
		// // 		echo "--> BLPROCESS END \r\n";
	}

	function loadVars($Vars,$Data){
		$Out=explode("|",$Vars);

		//		$Data=preg_replace("#\[([0-9])\]#e",'$Out[\1]',$Data);
		for ($i=1;$i<BLOC_MAX_PARAMS;$i++) {
			$DataTemp=str_replace('['.$i.']',(isset($Out[$i]))?$Out[$i]:'',$Data);
			if ($DataTemp!=$Data) $Data=$DataTemp; else $i=BLOC_MAX_PARAMS;
		}
		/*		for ($i=1;$i<BLOC_MAX_PARAMS;$i++) {
			if (sizeof(explode("[".$i."]",$Data))>1) {
			$DataTemp=str_replace('['.$i.']',$Out[$i],$Data);
			if ($DataTemp!=$Data) $Data=$DataTemp; else $i=BLOC_MAX_PARAMS;
			}
			}*/
		return $Data;
	}

	function addHeader() {
		if (sizeof($this->ObjVars)&&is_object($GLOBALS["Systeme"]->Header)) {
			foreach ($this->ObjVars as $Key){
				switch ($Key["Type"]) {
					case "TITLE":
						$GLOBALS["Systeme"]->Header->setTitle($Key["Data"]);
					break;
					case "DESCRIPTION":
						$GLOBALS["Systeme"]->Header->setDescription($Key["Data"]);
					break;
					case "KEYWORDS":
						$GLOBALS["Systeme"]->Header->setKeywords($Key["Data"]);
					break;
					default:
						if (is_object($GLOBALS["Systeme"]->Header))$GLOBALS["Systeme"]->Header->Add($Key["Data"],$Key["Var"]);
					break;
				}
			}
		}
	}

	// 	AFFICHAGE
	function Affich($test=false) {
		$Data = "";
		//On traite les element d entete
		$this->addHeader();
		//Le contenu du fichier retravaill�
		//$this->Content = Parser::getContent($this->BlObjects);
		//$this->Data = Parser::getContent($this->ChildObjects);
		if ($this->Content!="")$this->Data = $this->parseData($this->Data,$this->Content);
		return $this->Data;
	}
}
?>