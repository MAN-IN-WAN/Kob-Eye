<?php
class Lib extends Beacon{

	var $Name;
	var $Function;
	var $Class;
	var $VarName;
	var $Attributes;
	var $Params;
	var $Obj;
	function Lib(){
	}

	function setFromVar($Var,$Data,$Beacon) {
		$this->Beacon = $Beacon["BEACON"];
		$this->Vars = $Var;
		$this->Data = $Data;
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
			$this->Objects[] = $Data;
		}
	}

	function parseContent() {
		$BaliseTemp["BEACON"] = "METHOD";
		$BaliseTemp["FUNCTION"] = "setMethod";
		$Balise[] = $BaliseTemp;
		$Data = Process::processRecursiv($this->Data,$Balise);
		return ;
	}


	function Generate($Skin=false){
		switch($this->Beacon) {
			case "LIB":
				Beacon::Generate();
				$Vars = Process::processingVars($this->Vars);
				$Vars = explode("|",$Vars);
				$this->Attributes = $Vars;
				$this->generateLib();
				break;
			case "OBJ":
				Beacon::Generate();
				$Vars = Process::processingVars($this->Vars);
				$Vars = explode("|",$Vars);
				$this->Attributes = $Vars;
				$this->generateObj();
				break;
			case "METHOD":
				Beacon::Generate();
				$Vars = Process::processingVars($this->Vars);
				$Vars = explode("|",$Vars);
				$this->Attributes = $Vars;
				$this->generateMethod();
				break;
			case "PARAM":
				$T = Parser::getContent($this->ChildObjects);
				$T = Process::processingVars($T);
				if (!is_object($T)&&!is_array($T))
					Beacon::Generate();
				$this->setChild("METHOD");
				break;
			case "COOKIE":
				Beacon::Generate();
				$Vars = Process::processingVars($this->Vars);
				$Vars = explode("|",$Vars);
				$this->Attributes = $Vars;
				$this->generateCookie();
				break;
			case "CACHE":
				Beacon::Generate();
				$Vars = Process::processingVars($this->Vars);
				$Vars = explode("|",$Vars);
				$this->Attributes = $Vars;
				$this->generateCache();
				break;
		}
		return ;
	}

	function generateCache() {
		switch ($this->Attributes[0]) {
			case "Set":
				$Temp = Process::processVars($this->Attributes[2]);
				Process::$TempVar[$this->Attributes[1]] = $Temp;
				Sys::$User->Cache[$this->Attributes[1]] = $Temp;
				klog::l('SET',$this->Attributes[1]);
			break;
			case "Add":
				$Temp = Process::getGlobalVar($this->Attributes[1]);
				$Temp[] = Process::processVars($this->Attributes[2]);
				Process::RegisterTempVar($this->Attributes[1],$Temp);
				Sys::$User->Cache[$this->Attributes[1]] = $Temp;
				klog::l('Add',$this->Attributes[1]);
				break;
			case "Del":
				//Supprime un cookie
				$Temp = unserialize($_SESSION[$this->Attributes[1]]);
				$i=1;
				foreach ($Temp as $T) {
					if ($i!=$this->Attributes[2])$Result[] = $T;
					$i++;
				}
				Process::RegisterTempVar($this->Attributes[1],$Result);
				$GLOBALS["Systeme"]->Connection->addSessionVar($this->Attributes[1],$Result);
				klog::l('Del',$this->Attributes[1]);
				break;
			case "Reset":
				//Supprime un cookie
				Process::UnRegisterTempVar($this->Attributes[1]);
				$GLOBALS["Systeme"]->Connection->rmSessionVar($this->Attributes[1]);
				klog::l('Reset',$this->Attributes[1]);
				break;
			case "ResetAll":
				//Supprime un cookie
				$_SESSION = "";
				klog::l('ResetAll',$this->Attributes[1]);
				break;
		}
		return ;
	}

	function generateCookie() {
		switch ($this->Attributes[0]) {
			case "Set":
				//Ajoute un cookie
				//setcookie($this->Attributes[1],$this->Attributes[2],3600);
				$Temp = Process::processVars($this->Attributes[2]);
				Process::$TempVar[$this->Attributes[1]] = $Temp;
				$GLOBALS["Systeme"]->Connection->addSessionVar($this->Attributes[1],$Temp);
				break;
			case "Add":
                                //Ajoute un cookie
				//setcookie($this->Attributes[1],$this->Attributes[2],3600);
				$Temp = Process::getGlobalVar($this->Attributes[1]);
				$Temp[] = Process::processVars($this->Attributes[2]);
				Process::RegisterTempVar($this->Attributes[1],$Temp);
				$GLOBALS["Systeme"]->Connection->addSessionVar($this->Attributes[1],$Temp);
				break;
			case "Get":
				//Charge un cookie dans une variable
                                $Temp = unserialize($_SESSION[$this->Attributes[1]]); 
                                $Temp = $Temp[($this->Attributes[3]-1)];
       				Process::RegisterTempVar($this->Attributes[2],$Temp);
				break;
			case "Maj":
				$Temp = unserialize($_SESSION[$this->Attributes[1]]);
				$Temp[($this->Attributes[3]-1)] = Process::$TempVar[$this->Attributes[2]];
				Process::RegisterTempVar($this->Attributes[1],$Temp);
				$GLOBALS["Systeme"]->Connection->addSessionVar($this->Attributes[1],$Temp);
				break;
			case "Del":
				//Supprime un cookie
				$Temp = unserialize($_SESSION[$this->Attributes[1]]);
				$i=1;
				foreach ($Temp as $T) {
					if ($i!=$this->Attributes[2])$Result[] = $T;
					$i++;
				}
				Process::RegisterTempVar($this->Attributes[1],$Result);
				$GLOBALS["Systeme"]->Connection->addSessionVar($this->Attributes[1],$Result);
				break;
			case "Reset":
				//Supprime un cookie
				Process::UnRegisterTempVar($this->Attributes[1]);
				$GLOBALS["Systeme"]->Connection->rmSessionVar($this->Attributes[1]);
				break;
			case "ResetAll":
				//Supprime un cookie
				$_SESSION = "";
				break;
		}
		return ;
	}

	function generateLib(){
		$this->Class=$this->Attributes[0];
		$this->VarName = $this->Attributes[1];
		require_once "Class/Lib/".$this->Class.".class.php";
		//On instancie l objet
		$this->Obj = new $this->Class();
		//On enregistre l objet en tant qu objet temporaire
		Process::$TempVar[$this->VarName]=$this->Obj;
		return ;

	}

	function generateObj(){
		$this->Module=$this->Attributes[0];
		$this->Class=$this->Attributes[1];
		$this->VarName = $this->Attributes[2];
		//On instancie l objet
		unset($this->Obj);
		$this->Obj = genericClass::createInstance($this->Module,$this->Class);
		unset(Process::$TempVar[$this->VarName]);
		Process::$TempVar[$this->VarName]=$this->Obj;
		return ;
	}

	function generateMethod(){
		$this->VarName = $this->Attributes[0];
		$this->Function = $this->Attributes[1];
		//On instancie l objet
		// 		print_r($this);
		$this->Obj=Process::$TempVar[$this->VarName];
		//On recherche les parametres
		//Si il y plus de deux attributs alors les suivantes sont des parametres de la methode
		$params = ",";
		if (sizeof($this->Attributes)>2){
			for ($i=0;$i>sizeof($this->Attributes);$i++) {
				$params .= $this->Attributes[$i];
			}
		}
		//On recherche les balises filles de balise PARAM pour voir si il y pas des parametres suppl�mentaires a ajouter
		$tempParams = $this->getAllChild("METHOD","PARAM");
		//echo "--------------------LISTE DES PARAMS-------------------";
		$Char = "";
		if (is_array($tempParams)) {
			foreach ($tempParams as $K=>$Param) {
				if (strlen($Char)>0) $Char.=",";
//				$Char.="urldecode('".urlencode($Param->getContent($Param->ChildObjects))."')";
				$T[$K] = Parser::getContent($Param->ChildObjects);
				$T[$K] = Process::processingVars($T[$K]);
				$Char.="\$T[$K]";
			}
		}
		//On execute la fonction
		/*		echo "----------------------------------------------\r\n";*/
// 		echo "return \$this->Obj->".$this->Function."(".$Char.");\r\n";
		$temp = eval("return \$this->Obj->".$this->Function."(".$Char.");");
		if (isset($this->Attributes[2])){
			//On enregitre la sortie dans une variable
			Process::$TempVar[$this->Attributes[2]]=$temp;
		}
		//On sauvegarde l objet
		Process::$TempVar[$this->VarName]=$this->Obj;
		$this->Data = $temp;
		return ;
	}


	function setMethod($Var,$Data) {
		$Var=Process::processingVars($Var);
		$Data=Process::processingVars($Data);
		$Vars = explode("|",$Var);
		$Method = $Vars[0];
		$Param = $Data;
		$Param=Parser::Processing($Param);
		$Result["Method"] = $Method;
		$Result["Objects"] = $Param;
		$this->Methods[] = $Result;
		/*		echo "return \$this->Obj->".$Method."(stripslashes('".addslashes($Param)."'));\r\n";
		 return eval("return \$this->Obj->".$Method."(stripslashes('".addslashes($Param)."'));");*/
	}

	function getModulesLoaded($Result = Array()) {
		//		if (!empty($this->Path)) $Result[] = $this->Path;
		return $Result;
	}


	function Affich() {
		//return $this->Data;
		//return $this->getContent($this->ChildObjects);
		//		print_r($this);
	}
	}
	?>