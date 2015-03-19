<?php
class Beacon extends Root{
	var $Init = 0;
	var $Data;
	var $Nom;
	var $Result;
	var $Beacon;
	var $ChildObjects;
	var $ChildData;
	var $Child;
	var $Vars;
	var $TempVar;
	var $Path;
	var $Conf;
	function __construct(){
	}
	function __destruct() {
	}
	function setFromVar($Var,$Data,$Beacon) {
		$this->Beacon = $Beacon["BEACON"];
		$this->Vars = $Var;
		$this->Data = $Data;
	}

	function init(){
		if (!$this->Init)$this->Process();
		$this->Init++;
	}

	function setTempVar($Tab) {
		$this->TempVar = $Tab;
	}
	function loadData($Data) {
		$this->Beacon = "TEMPLATE";
		$this->Data = $Data;
		$this->RawLoaded = true;
	}
	/**
	* Analyze all the params and data
	* @param String Vars
	* @param String Data
	* @void
	*/
	public function loadVars($Vars,$Data){
		$Out=explode("|",$Vars);
		for ($i=1;$i<BLOC_MAX_PARAMS;$i++) {
			$DataTemp=str_replace('['.$i.']',(isset($Out[$i]))?$Out[$i]:'',$Data);
			if ($DataTemp!=$Data) $Data=$DataTemp; else $i=BLOC_MAX_PARAMS;
		}
		return $Data;
	}
	/**
	* Trigger the generate command in order to initialize all zones and components
	* @void
	*/
	function Generate() {
		//if (isset($this->Data))$this->Data = Process::processingVars($this->Data);
		if   ($this->Beacon=="MODULE") {
			//Extraction des variables GET internes
			$TabTemp = explode("|",$this->Vars);
			$Test = explode("?",$TabTemp[0]);
			$Lien = $Test[0];
			/*if (sizeof($Test)>1) {
				//Alors il y a des variables get
				$VarsToRegister = explode("&",$Test[1]);
				for ($i=0;$i<sizeof($VarsToRegister);$i++){
					$Temp = explode("=",$VarsToRegister[$i]);
					Process::RegisterTempVar(Process::processingVars($Temp[0]),Process::processingVars($Temp[1]));
				}
			}*/
			//On genere le module si il n 'est pas generé
			$Vars = Process::processingVars($Lien);
			$Module = explode('/',$Vars,2);
			$Query = (isset($Module[1]))?$Module[1]:"";
			$Module = trim($Module[0]);
			//test de l'existence du module
			if (empty($Module)||!is_object(Sys::$Modules[$Module])){
				return "<h1>LE MODULE $Module N'est pas installé pour la requete ".$this->Vars."</h1>";
			}
			//execution requete
			$Bloc=Sys::$Modules[$Module]->setData($Query);
			if (isset($Bloc)&&is_object($Bloc))$this->ChildObjects=$Bloc->ChildObjects;
			//Gestion des variables temporaires
			$TempVar = Process::$TempVar;
			$Temp=Array("Query"=>$TempVar["Query"]);
			if (isset($Test[1])){
				$Vars = $Test[1];
				preg_match_all("#([^&=|]*?)=([^&|=]*)#",$Vars,$Vars);
				for ($i=0;$i<sizeof($Vars[0]);$i++){
					$Temp[Process::processingVars($Vars[1][$i])] = Process::processingVars($Vars[2][$i]);
				}
				/*echo "****************************\r\n";
				print_r($_POST);
				print_r(Process::$TempVar);*/
				Process::$TempVar = $Temp;
				/*echo $this->Vars."\r\n";
				print_r($Temp);*/
			}//else Process::$TempVar = Array("Query"=>$TempVar["Query"]);
		}
		$this->Vars = Parser::PostProcessing($this->Vars);
		if (isset($this->BlObjects)&&is_array($this->BlObjects)) for ($i=0;$i<sizeof($this->BlObjects);$i++) if (is_object($this->BlObjects[$i])) {
			$this->BlObjects[$i]->Generate();
		}else{
			$this->BlObjects[$i] = Process::processingVars($this->BlObjects[$i]);
			$this->BlObjects[$i] = Parser::PostProcessing($this->BlObjects[$i]);
		}
		if (isset($this->ChildObjects)&&sizeof($this->ChildObjects)) for ($i=0;$i<sizeof($this->ChildObjects);$i++){
			if (is_object($this->ChildObjects[$i])){
				$this->ChildObjects[$i]->Generate();
			}else{
				$this->ChildObjects[$i] = Process::processingVars($this->ChildObjects[$i]);
				$this->ChildObjects[$i] = Parser::PostProcessing($this->ChildObjects[$i]);
			}
		}
		if   ($this->Beacon=="MODULE")Process::$TempVar = $TempVar;
	}

	function writeCacheFile($Data,$Url,$Name) {
		if (!$File=fopen ($Url."/".$Name,"w"))return false;
		fwrite($File,$Data);
		fclose($File);
	}

	function writeCache($Url,$Id="Skin") {
		//On commence par repercuter la commande pour que le processus commence du bout des branches
		for($i=0;$i<sizeof($this->ChildObjects);$i++) {
			if (is_object($this->ChildObjects[$i]))$this->ChildObjects[$i]->writeCache($Url,$Id."-".$i."-".$this->ChildObjects[$i]->Beacon);
		}
		//On ecrit les fichiers
		if ($Id=="Skin")$this->writeCacheFile(serialize($this),$Url,$Id.".cache");
	}

	function searchChild($Beacon,$Child,$First=true,$level=0) {
		// 		if ($level==0&&$First) echo "------------------START-----|$First|----------\r\n";
		$test=false;
		if (gettype($Beacon)=="string") {$BeaconTemp[] = $Beacon;$Beacon = $BeaconTemp;}
		// 		for ($i=0;$i<$level;$i++) echo "--";
		// 		echo "-->RECHERCHE $Child | Balise bloquante $Beacon | BALISE EN COURS ".$this->Beacon." | LEVEL  ".$level."\r\n";
		if (($this->Beacon!=$Child&&!in_array($this->Beacon,$Beacon))||$First) {
			if (is_array($this->ChildObjects)) foreach ($this->ChildObjects as $Key) {
				if (!$test&&is_object($Key)) {
					// 					for ($i=0;$i<$level;$i++) echo "--";
					// 					echo "--->".$Key->Beacon." \r\n";
					$temp = $level+1;
					$test=$Key->searchChild($Beacon,$Child,false,$temp);
				}else{
					// 					for ($i=0;$i<$level;$i++) echo "--";
					// 					echo "-->__PAS D'OBJET\r\n";
				}
			}else{
				// 				for ($i=0;$i<$level;$i++) echo "--";
				// 				echo "-->__PAS DE BALISE FILLE\r\n";
			}
		}else{
			if ($this->Beacon==$Child) {
				$test=true;
				// 				for ($i=0;$i<$level;$i++) echo "--";
				// 				echo "-->//////////// C la Bonne".$this->Beacon." \r\n";
			}else {
				$test=false;
				// 				for ($i=0;$i<$level;$i++) echo "--";
				// 				echo "-->!!!!!!! BALISE BLOQUANTE ".$this->Beacon." level $level | $First\r\n";
			}
		}

		// 		if ($test&&$First)echo "-->!!!!!BINGO!!!! $Child BAL EN COURS ".$this->Beacon." \r\n";
		return $test;
	}

	function getObjVars() {
		if (sizeof($this->ChildObjects)>0) {
			foreach ($this->ChildObjects as $Key) {
				if (is_object($Key)){
					$ObjVars = $Key->getObjVars();
					if (sizeof($ObjVars)) {
						array_push($this->ObjVars,$ObjVars);
					}
				}
			}
		}
		if (sizeof($this->BlObjects)>0) {
			foreach ($this->BlObjects as $Key) {
				if (is_object($Key)){
					$ObjVars = $Key->getObjVars();
					if (sizeof($ObjVars)) {array_push($this->ObjVars,$ObjVars); }
				}
			}
		}
		return $this->ObjVars;
	}


	function setChild($Child) {
		$this->Child = $Child;
	}

	function getModulesLoaded($Result = Array()) {
		if (!empty($this->Path)) {
			//On verifie que l entree n y est pas deja
			$test=false;
			foreach ($Result as $mods) {
				if ($mods==$this->Path)$test=true;
			}
			if (!$test) $Result[] = $this->Path;
		}
		if (isset($this->ChildObjects)&&sizeof($this->ChildObjects)) {
			foreach ($this->ChildObjects as $Key) {
				if (is_object($Key))$Result = $Key->getModulesLoaded($Result);
			}
		}
		if (isset($this->BlObjects)&&sizeof($this->BlObjects)) {
			foreach ($this->BlObjects as $Key) {
				if (is_object($Key))$Result = $Key->getModulesLoaded($Result);
			}
		}
		return $Result;
	}

	function Process() {
 		if ($this->Data!="")$this->ChildObjects= Parser::Processing($this->Data,false);
		//if (isset($Process->PostObjects))$this->PostObjects = $Process->PostObjects;
		unset($this->Data);
	}

	function parseData($Replace,$Content){
		$Data = str_replace('[DATA]',$Replace , $Content);
		return $Data;
	}

	function getChild($Beacon,$Child,$First=true,$Block=true,$level=0) {
		// 		if ($level==0&&$First) echo "------------------START-----|$First|----------\r\n";
		$test=false;
		if (gettype($Beacon)=="string") {$BeaconTemp[] = $Beacon;$Beacon = $BeaconTemp;}
		// 		for ($i=0;$i<$level;$i++) echo "--";
		// 		echo "-->RECHERCHE $Child | Balise bloquante $Beacon | BALISE EN COURS ".$this->Beacon." | LEVEL  ".$level."\r\n";
		if (($this->Beacon!=$Child&&!in_array($this->Beacon,$Beacon))||$First) {
			if (is_array($this->ChildObjects)) foreach ($this->ChildObjects as $Key) {
				if (!$test&&is_object($Key)) {
					// 					for ($i=0;$i<$level;$i++) echo "--";
					// 					echo "--->".$Key->Beacon." \r\n";
					$test=$Key->getChild($Beacon,$Child,false,$Block,$level+1);
				}else{
					// 					for ($i=0;$i<$level;$i++) echo "--";
					// 					echo "-->__PAS D'OBJET\r\n";
				}
			}else{
				// 				for ($i=0;$i<$level;$i++) echo "--";
				// 				echo "-->__PAS DE BALISE FILLE\r\n";
			}
		}else{
			if ($this->Beacon==$Child) {
				return $this;
				// 				for ($i=0;$i<$level;$i++) echo "--";
				// 				echo "-->//////////// C la Bonne".$this->Beacon." \r\n";
			}else {
				$test=false;
				// 				for ($i=0;$i<$level;$i++) echo "--";
				// 				echo "-->!!!!!!! BALISE BLOQUANTE ".$this->Beacon." level $level | $First\r\n";
			}
		}

		// 		if ($test&&$First)echo "-->!!!!!BINGO!!!! $Child BAL EN COURS ".$this->Beacon." \r\n";
		return $test;
	}

	function getAllChild($Beacon,$Child,$First=true,$Block=false,$level=0) {
		$Result=false;
		// 		for ($i=0;$i<$level;$i++) echo "--";
		// 		echo ">GET CHILD RECH $Child Depart Balise ".$this->Beacon." Child ".$this->Child." level ".$level."\r\n";
		if ($this->Beacon!=$Child||$First||$this->Child!=$Beacon) {
			if (sizeof($this->ChildObjects))foreach ($this->ChildObjects as $Key) {
				if (is_object($Key)){
					if ($Block&&!$First){
						// 						for ($i=0;$i<$level;$i++) echo "--";
						// 						echo ">Balise ".$this->Beacon." Child ".$this->Child." level ".$level."\r\n";
						if ($Key->Beacon!=$Child){
							//La balise est differente de la balise bloquante
							// 							for ($i=0;$i<$level;$i++) echo "--";
							// 							echo "Balise differente Balise ".$Beacon." Child ".$Key->Beacon."";
							$test=$Key->getAllChild($Beacon,$Child,false,$Block,$level+1);
							if (is_object($test)) $Result[]=$test;
							$test=false;
						}else{
							//La Balise est identique a la balise bloquante
							if ($Key->Child!=$Beacon){
								if ($Key->Child!="") {
									//Balise identique mais il s agit d'une balise enfant donc on continue
									// 									for ($i=0;$i<$level;$i++) echo "--";
									// 									echo "Balise bloquante mais Child different ".$Child." Child ".$Key->Child."";
									$test=$Key->getAllChild($Beacon,$Child,false,$Block,$level+1);
									if (is_object($test)) $Result[]=$test;
									$test=false;
								}else{
									//Cas de la balise bloquante
									// 									for ($i=0;$i<$level;$i++) echo "--";
									// 									echo ">Balise Bloquante level:$level --> balBlock".$Beacon." Child ".$this->Child." - BalTrouve ".$Key->Beacon."\r\n";
								}
							}else{
								$test=$Key->getAllChild($Beacon,$Child,false,$Block,$level+1);
								if (is_object($test)) $Result[]=$test;
								$test=false;
							}
						}
					}else{
						$test=$Key->getAllChild($Beacon,$Child,false,$Block,$level+1);
						if (is_object($test)) $Result[]=$test;
						$test=false;
					}
				}else{
				}
			}
		}elseif($this->Beacon==$Child){
			$Result=$this;
			// 			if ($test) echo "ON a l objet\r\n";
		}
		// 		for ($i=0;$i<$level;$i++) echo "--";
		// 		echo "Fin ".$this->Beacon." ".$this->Child." $level // $test\r\n";
		return $Result;

	}
	function loadCacheFile($FileName){
		return unserialize(file_get_contents($this->URL));
	}

	function affich() {
	}
}

?>