<?php
class Process extends Root{
	public static $TempVar;


// --------------------------------------------------//
//			GESTION DES VARIABLES	     //
// --------------------------------------------------//
	static function UnRegisterTempVar($Name) {
		 unset(self::$TempVar[$Name]);
	}
	//enregistrement d'une variable temporaire
	static function RegisterTempVar($Name,$Var) {
// 		print_r($Var);
		if (is_string($Name))if (sizeof(explode("::",$Name))>1){
			$Res = explode("::",$Name);
			if(isset(self::$TempVar[$Res[0]])&&is_object(self::$TempVar[$Res[0]])){
				$N = $Res[1];
				self::$TempVar[$Res[0]]->$N = $Var;
 				//echo "OBJET ".$Res[0]."=>".$Res[1]." = $Var\r\n";
			}else{
				if (empty($Res[1])){
					self::$TempVar[$Res[0]][] = $Var;
				}else{
					self::$TempVar[$Res[0]][$Res[1]] = $Var;
				}
			}
		}else{
			 self::$TempVar[$Name] = $Var;
 			//echo "STANDARD ".$Name."=>".print_r($Var,true)."\r\n";
		}
//  		echo "--------------------\r\n";
	}

	static function GetTempVar($Name){
		return (isset(self::$TempVar[$Name]))?self::$TempVar[$Name]:null;
	}
	//traitement d'une chaine de caractere de maniere recursive
	//Cette fonction a pour but d'optimiser le traitement si il n y a  pas de variable dans la chaine
	//alors on arrette le proc
	static function processingVars($Data) {
		if (!is_string($Data)) return $Data;
		if (preg_match("#\[\!.+?\!\]#",$Data,$o)) {
 			$Data = Process::searchAndReplaceVars($Data);
		}
		if (is_string($Data)&&preg_match("#\[\*\*(.+)\*\*\]#",$Data,$o)) {
			//On envoie dans la moulinette
			$Data = Process::searchAndReplaceVars($Data,"[**","**]");
		}
		return $Data;
	}

	static function processVars($Data,$test=0){
// 		if ($Data=="Lien") print_r(Process::$TempVar);
		if (is_string($Data)&&preg_match("#\[\!.*?\!\]#",$Data,$o) && $test<2) {
			//On envoie dans la moulinette
//			echo "-------------------------$test----------------------\r\n";
//			echo $Data."\r\n";
 			$Data = Process::searchAndReplaceVars($Data);
//			echo $Data."\r\n";
//			echo "************************************************\r\n";
			return Process::processVars($Data,$test+1);
		}
//		echo $Data."\r\n";
		if (is_string($Data)&&preg_match("#\[\*\*(.*)\*\*\]#",$Data,$o) && $test<2) {
//			echo "-------------------------$test----------------------\r\n";
//			echo $Data."\r\n";
			//On envoie dans la moulinette
			$Data = Process::searchAndReplaceVars($Data,"[**","**]");
//			echo $Data."\r\n";
//			echo "************************************************\r\n";
			return Process::processVars($Data,$test+1);
		}
		$sData = $Data;
		if (!is_string($Data))return $Data;
		//On detecte l existence d un calcul
		if (preg_match("#(.*?)(\:\=|\:\+|\:\*|(?<!http)\:\/|\:\-|\*\=|\-\=|\+\=|\.\=|\/\=|\%\=|\:\%)([^`]*)#s",$Data,$Out)){
			//On extrait les valeurs
			$Operateur = $Out[2];
			$Val1 = $Out[1];
			if ($Val1=="") $Val1 = $Out[1];
			$Val2 = $Out[3];
			if ($Val2=="") $Val2 = $Out[3];
			if ($Val2=="EOL")$Val2="\n";
			switch($Operateur){
				case "==":
// 					echo $Val1."=".$Val2."\r\n";
					//Affectatuion forc�e
					if (strlen($Out[1])){
						Process::RegisterTempVar($Out[1],Process::searchAndReplaceVars($Out[3]));
					}
					$Data="";
				break;
				case ":=":
					//Affectation par defaut
					if (strlen($Out[1])&&(Process::ProcessPostVars($Out[1])==""||Process::ProcessPostVars($Out[1])==$Out[1])){
						if (preg_match("#(.*?)(\:\=|\=\=|\:\+|\:\*|\:\/|\:\-|\*\=|\/\=)([^`]*)#s",$Out[3],$Out2)) $Out[3]=Process::GetTempVar($Out[3]);
						//On verifie qu il ne s agissent pas d un tableau
						if (sizeof($Tab = explode ('::',$Out[1]))>1) {
							//C un tableau
							$Temp = Process::ProcessVars($Tab[0]);
 //							echo " - ".$Out[3]." - Separateur ".$Tab[1]."\r\n";
							$a = (empty($Tab[1]))?sizeof($Temp):$Tab[1];
							if (is_array($Temp))$Temp[$a] = $Out[3];
							elseif (is_object($Temp)){
								$Temp->$a = $Out[3];
							}else $Temp[] = $Out[3];
// 							echo "-------------------------\r\n";
							Process::RegisterTempVar($Tab[0],$Temp);
						}else{
// 							echo "/////////////////////\r\n";
// 							print_r($Out);
// 							echo "\r\n";
							
							if (preg_match("#^\[\!(.*?)\!\]$#",$Out[3],$o)){
//								echo "LAUNCH PROCESS ".$Out[3]." => ";
								$Out[3] = Process::processVars($o[1]);
//								print_r($Out[3]);
							}
//  		 					echo "AFFECTATION ".$Out[1]."=".$Out[3]."\r\n";
							Process::RegisterTempVar($Out[1],$Out[3]);
						}
					}
					$Data="";
				break;
				case ":/":
					if (!is_numeric($Val1)&&!is_numeric($Val2)) {
						//Alors il s agit d'une division de chaine
						if ($Val2=="%RC%"){
							$Data=explode("\n",trim($Val1));	
							if (is_array($Data))foreach ($Data as $k=>$v)$Data[$k]=trim($v);
						}else {
							//Alors il s agit d'une division de chaine
						  //(fred)Ajout pour division par espace
						  if ($Val2=="") $Val2=" ";
								$Data=explode($Val2,$Val1);
							if (is_array($Data))foreach ($Data as $k=>$v)$Data[$k]=trim($v);
	// 						print_r($this->TempVar[$Out[1]]);
						}
					}else{
						if (!is_numeric($Val1)) {
							$Val1=Process::processVars($Val1);
						}
						if ($Val2!=""&&$Val2!=0&&!is_array($Val1)) $Data = $Val1/$Val2;
						else $Data=Array($Val1);
					}
				break;
				case ":%":
					if (!is_numeric($Val1)) $Val1=Process::processVars($Val1);
					if ($Val2!=""&&$Val2!=0) $Data = $Val1%$Val2;
					else $Data=0;
				break;
				case ":+":
					if (!is_numeric($Val1)) $Val1=Process::processVars($Val1);
					$Data = $Val1+$Val2;
				break;
				case ":*":
					if (!is_numeric($Val1)) $Val1=Process::processVars($Val1);
					$Data = $Val1*$Val2;
  					//echo "Multiplication ".$Out[1]."|".$Out[3]."|".$this->processVars($Val1)."*".$Val2."=".$Data."</BR>";
				break;
				case ":-":
					if (!is_numeric($Val1)) $Val1=Process::processVars($Val1);
					$Data = $Val1-$Val2;
//  					echo "Soustraction ".$Out[1]."|".$Out[3]."|".$Val1."-".$Val2."=".$Data."</BR>";
				break;
				case "*=":
					$Temp = Process::processVars($Val1);
					Process::RegisterTempVar($Out[1],$Val2*$Temp);
					$Data="";
				break;
				case "/=":
					if (!is_numeric($Val1)&&!is_numeric($Val2)) {
						if ($Val2=="%RC%"){
							Process::RegisterTempVar($Out[1],explode("\r\n",trim(Process::processVars($Val1))));	
						}else {
							//Alors il s agit d'une division de chaine
							 Process::RegisterTempVar($Out[1],explode($Val2,$Val1));
	// 						print_r($this->TempVar[$Out[1]]);
						}
					}else{
						if (!is_numeric($Val1)) {
							$Val1=Process::processVars($Val1);
						}
						if ($Val2!=""&&$Val2!=0) Process::RegisterTempVar($Out[1],$Val1/$Val2);
					}
					$Data="";
				break;
				case "%=":
					$Temp = Process::processVars($Val1);
					Process::RegisterTempVar($Out[1],$Temp%$Val2);
					$Data="";
				break;
				case "+=":
					if (!is_numeric($Val1)&&!is_numeric($Val2)) {
						//Alors il s agit dune concatenation de chaine
						$Val1=Process::processVars($Val1);
						Process::RegisterTempVar($Out[1],$Val1.$Val2);
					}else{
						$Temp = Process::processVars($Out[1]);
						Process::RegisterTempVar($Out[1],$Val2+$Temp);
					}
					$Data="";
				break;
				case "-=":
					$Temp = Process::GetTempVar($Out[1]);
					Process::RegisterTempVar($Out[1],$Temp-$Val2);
					$Data="";
				break;
				case ".=":
					$Temp = Process::GetTempVar($Out[1]);
					Process::RegisterTempVar($Out[1],$Temp.$Val2);
					$Data="";
				break;
			}
			return $Data;
		}
		$Tab = explode ('::',$Data);
		if (sizeof($Tab)>1) {
			switch ($Tab[0]) {

				case "JSON":
					$Json=$GLOBALS['Systeme']->RegVars['JSON'];
					$chaine='$GLOBALS["Systeme"]->RegVars["JSON"]->'.$Tab[1].'->'.$Tab[2];
					$Data = eval("return ".$chaine.";");
				break;
				case "CONF":
					$Data=preg_replace("#CONF\:\:#","",$Data);
					$Data=$GLOBALS['Systeme']->Conf->get($Data);
				break;
				case "SKIN":
					$Data=preg_replace("#SKIN\:\:#","",$Data);
					$Data=$GLOBALS['Systeme']->SkinObj->get($Data);
				break;
				case "TMS":
					if ($Tab[1]=="Now") $Data=time();
					else if ($Tab[1]=="ThisMonth") {$Data=time(0,0,0,0,1,0);}
				break;
				case "PHP":
					try {
						$Tab2 = explode("(", $Tab[1]);
						$Tab3 = explode(")", $Tab2[1]);
						$Params = explode(",", $Tab3[0]);
						$Data = eval("return " . $Tab[1] . ";");
					} catch(Exception $e) {
						$Data = $Tab[1];
					}
				break;
				case "DEBUG":
                                        $Tab = explode ('::',$Data);
					$Data=preg_replace("#DEBUG\:\:#","",$Data);
					echo "*** DEBUG ".$Data." MODE ***\r\n";
					print_r(Process::processVars($Data));
					$Data="";
				break;
				case "Module":
					//Si le premier mot est Module, alors on recherche des informations dans le module.
					$Function = explode("|",$Tab[sizeof($Tab)-1]);
					$Tab[sizeof($Tab)-1] = $Function[0];
					if ($Tab[1]=="Actuel"){
						$BonModule = $GLOBALS["Systeme"]->CurrentModule;
					}else{
						//On pointe sur un module en particulier
						foreach (Sys::$Modules as $Module){
							if ($Tab[1]==$Module->Nom){
								$BonModule = $Module->Nom;
							}
						}
					}
					//Test de la présence de parenthese
					$t = explode("(",$Tab[sizeof($Tab)-1]);
					$Params="";
					if (sizeof($t)>1){
						$Tab[sizeof($Tab)-1] = $t[0];
						$t = explode(")",$t[1]);
						$Params = explode(',',$t[0]);
					}
					//On verifie qu il n y pas d erreur dans la denomination du module
					if (!isset($BonModule))	return "Erreur Module inexistant";
					$Obj=$chaine="Sys::\$Modules['".$BonModule."']";
					//On initialise le module et on charge le schema
					if (is_object(eval("return ".$Obj.";"))) $TmpObj = eval($Obj."->loadSchema();");
					//On construit la requete en syntaxe objet
					for ($i=2;$i<count($Tab);$i++){
						if ($i<count($Tab)-1) $Obj.='->'.$Tab[$i];
						$chaine.='->'.$Tab[$i];
					}
					//On recupere la liste des proprietes de l objet demande
					$TmpObj = eval("return ".$Obj.";");
					//ON verifie qu il s agisse bien d un objet
					if (is_object($TmpObj)){
						//On teste si il s agit d une propriete ou d une methode
						$Temp = get_object_vars($TmpObj);
						if (array_key_exists($Tab[sizeof($Tab)-1],$Temp)) {
							//C est une propriete
							$Data = eval("return ".$chaine.";");
						}else{
							//Sinon verification de l existence de la methode
							if (method_exists($TmpObj,$Tab[count($Tab)-1])){
								//Si il y a des parametres alors on les ajoutent
								if (is_array($Params)&&sizeof($Params)){
									$chaine .= "(";
                                    $v='';
									foreach ($Params as $p){
										$chaine .= $v."'".$p."'";
                                        $v=',';
									}
									$chaine .= ")";
								}else{
									$chaine = $chaine."()";
								}
								$Data=eval("return $chaine;");
							}
						}
					}
				break;
				case "Sys":
				case "Systeme":
					if ($Tab[1]=="Modules")return Sys::$Modules;
					$Obj=$GLOBALS['Systeme'];
					for ($i=1;$i<sizeof($Tab);$i++){
						$n = $Tab[$i];
						if (is_object($Obj)&&property_exists($Obj,$n)){
							try{
								$prop = new ReflectionProperty(get_class($Obj), $n);
								$Obj=$prop->getValue($Obj);
							}catch (Exception $e){
								$Obj=$Obj->$n;
							}
							if ($i==sizeof($Tab)-1)$Data = $Obj;
						}else{
							preg_match('#([^\(]*?)\(([^\)]*?)\)#', $Tab[$i], $Out);
							if(empty($Out)) $Out[1] = $Tab[$i];
							if (method_exists($Obj,$Out[1])){
								return call_user_func_array(array($Obj,$Out[1]),(isset($Out[2]))?explode(',',$Out[2]):Array());
								//return $Obj->$Out[1]( (isset($Out[2]))?$Out[2]:'' );
							}
							else $Data=false;
						}
					}

				break;
				case "ObjectClass":
					//Alors on genere un genericClassVide et on l interroge
					$Data = new genericClass($Tab[1]);
				break;

				case "Vars":
					switch ($Tab[1]) {
						case "Post":
							$Data="";
							if (sizeof($GLOBALS["Systeme"]->PostVars)) {
								foreach ($GLOBALS["Systeme"]->PostVars as $Key=>$Value) {
									$Temp["Value"] = $Value;
									$Temp["Name"] = $Key;
									$Data[] = $Temp;
								}
							}
						break;
						case "Files":
							$Data="";
							if (sizeof($_FILES)) {
								foreach ($_FILES as $Key=>$Value) {
									$Data[$Key] = $Value;
								}
							}
						break;
						case "Get":
							$Data="";
							if (sizeof($GLOBALS["Systeme"]->GetVars)) {
								foreach ($GLOBALS["Systeme"]->GetVars as $Key=>$Value) {
									$Temp["Value"] = $Value;
									$Temp["Name"] = $Key;
									$Data[] = $Temp;
								}
							}
						break;
						case "Temp":
							$Data="";
							if (sizeof($GLOBALS["Systeme"]->TempVars)) {
								foreach ($GLOBALS["Systeme"]->TempVars as $Key=>$Value) {
									$Temp["Value"] = $Value;
									$Temp["Name"] = $Key;
									$Data[] = $Temp;
								}
							}
						break;
						case "Global":
							$Data="";
							if (sizeof($GLOBALS["Systeme"]->RegVars)) {
								foreach ($GLOBALS["Systeme"]->RegVars as $Key=>$Value) {
									$Temp["Value"] = $Value;
									$Temp["Name"] = $Key;
									$Data[] = $Temp;
								}
							}
						break;
					}
				break;
				case "SERVER":
					return isset($_SERVER[$Tab[1]]) ? $_SERVER[$Tab[1]] : '';
				break;
				case "SHELL":
					return shell_exec($Tab[1]);
				break;
				case "Math":
					preg_match("#(.*)\((.*)\)#",$Tab[1],$Out);
					if (!isset($Out[1]))return;
					if (!isset($Out[2]))$Params = 0; else $Params = explode(",",$Out[2]);
					switch ($Out[1]){
						case "Round":
							return round($Params[0]);
						break;
						case "Floor":
							return floor($Params[0]);
						break;
						case "Price":
							return number_format((double)$Params[0], 2, '.', '');
						break;
						case "Price":
							return number_format((double)$Params[0], 2, '.', '');
						break;
						case "PriceV":
							return number_format((double)$Params[0], 2, ',', '');
						break;
					}
				break;
				case "Date":
					preg_match("#(.*)\((.*)\)#",$Tab[1],$Out);
					$Params = explode(",",$Out[2]);
					switch ($Out[1]){
						case "getYear":
							return date("Y",time());
						break;
						case "getMonth":
							return date("m",time());
						break;
						case "getTime":
							return date("H:i:s",$Params[0]);
						break;
					        case "getDayOfWeek":
                                                        $j = array("Mon"=>"Lundi","Tue"=>"Mardi","Wed"=>"Mercredi","Thu"=>"Jeudi","Fri"=>"Vendredi","Sat"=>"Samedi","Sun"=>"Dimanche");
                                                        return $j[date("D",(($Params[0]!="")?$Params[0]:time()))];
                                               break;
                                               case "getDaysOfWeek":
                                                        $j = array("Mon"=>"Lundi","Tue"=>"Mardi","Wed"=>"Mercredi","Thu"=>"Jeudi","Fri"=>"Vendredi","Sat"=>"Samedi","Sun"=>"Dimanche");
                                                        return $j;
                                               break;
					       case "getMonthsOfYear":
						 return array("Janvier","Fevrier","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Decembre");
					       break; 
                                               case "getMonthAlpha":
                                                        $lu = array("1"=>"Janvier","2"=>"Fevrier","3"=>"4","Apr"=>"Avril","5"=>"May","6"=>"Juin","7"=>"Juillet","8"=>"Aout","9"=>"Septembre","10"=>"Octobre","11"=>"Novembre","12"=>"Decembre");
                                                        return $lu[date("n",(($Params[0]!="")?$Params[0]:time()))];
                                               break;
					       case "getWeekDayNum":
						 $d = array("Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi","Dimanche");
						 return array_search($Params[0],$d);
						case "getDay":
							return date("d",time());
						break;
						case "getDate":
							return date($Params[0],$Params[1]);
						break;
						case "isDate":
							$Params = explode(",",$Params[0]);
							$date = $Params[0];
							if( preg_match( "#[0-9]{2}\/[0-9]{2}\/[0-9]{4}#", $date, $regs ) ) {
								return true;
							}else{
								return false;
							}
						break;
					}
				break;
				case "Utils":
					$ClassName = $Tab[0];
                                        $Tab = explode ('::',$Data,2);
					$Out = explode("(",$Tab[1],2);
					$Parametres = explode(")",$Out[1],2);
					$FunctionName = $Out[0];
					$Parametres = explode (",",$Parametres[0]);
					foreach ($Parametres as $K=>$P)$Parametres[$K] = (sizeof(explode("[!",$P))>1)?Process::processVars($P):$P;
					foreach ($Parametres as $K=>$P)$Parametres[$K] = (is_string($P)&&sizeof(explode("[*",$P))>1)?Process::searchAndReplaceVars($P,"[*","*]"):$P;
					//preg_match("#(.*)\((.*)\)#",$Tab[1],$Out);
					//if (method_exists(new $ClassName(),$Out[0])) {
						return Utils::$FunctionName($Parametres);
					//}
				break;
				case "Array":
					$ClassName = $Tab[0];
                                        $Tab = explode ('::',$Data,2);
					$Out = explode("(",$Tab[1],2);
					$Parametres = explode(")",$Out[1],2);
					$FunctionName = $Out[0];
					$Parametres = explode (",",$Parametres[0]);
					foreach ($Parametres as $K=>$P)$Parametres[$K] = (sizeof(explode("[!",$P))>1)?Process::processVars($P):$P;
					//preg_match("#(.*)\((.*)\)#",$Tab[1],$Out);
					if (method_exists(new UtilsArray(),$Out[0])) {
						return UtilsArray::$FunctionName($Parametres);
					}
				break;
				case "JsonP":
					$ClassName = $Tab[0];
                                        $Tab = explode ('::',$Data,2);
					$Out = explode("(",$Tab[1],2);
					$Parametres = explode(")",$Out[1],2);
					$FunctionName = $Out[0];
					$Parametres = explode (",",$Parametres[0]);
					foreach ($Parametres as $K=>$P)$Parametres[$K] = (sizeof(explode("[!",$P))>1)?Process::processVars($P):$P;
					//preg_match("#(.*)\((.*)\)#",$Tab[1],$Out);
					if (method_exists(new $ClassName(),$Out[0])) {
						return JsonP::$FunctionName($Parametres);
					}
				break;
				case "Template":
					$ClassName = $Tab[0];
                                        $Tab = explode ('::',$Data,2);
					$Out = explode("(",$Tab[1],2);
					$Parametres = explode(")",$Out[1],2);
					$FunctionName = $Out[0];
					$Parametres = explode (",",$Parametres[0]);
					foreach ($Parametres as $K=>$P)$Parametres[$K] = (sizeof(explode("[!",$P))>1)?Process::processVars($P):$P;
					//preg_match("#(.*)\((.*)\)#",$Tab[1],$Out);
					if (method_exists(new $ClassName(),$Out[0])) {
						return Template::$FunctionName($Parametres);
					}
				break;
				case "Component":
					$ClassName = $Tab[0];
                                        $Tab = explode ('::',$Data,2);
					$Out = explode("(",$Tab[1],2);
					$Parametres = explode(")",$Out[1],2);
					$FunctionName = $Out[0];
					$Parametres = explode (",",$Parametres[0]);
					foreach ($Parametres as $K=>$P)$Parametres[$K] = (sizeof(explode("[!",$P))>1)?Process::processVars($P):$P;
					//preg_match("#(.*)\((.*)\)#",$Tab[1],$Out);
					if (method_exists(new $ClassName(),$Out[0])) {
						return Component::$FunctionName($Parametres);
					}
				break;
				Default:
					//Si le denominateur correspond a un objet temporaire (cas du storproc)
					if ($Tab[0]=="") {$Ob = "Object";}else{$Ob=$Tab[0];}
					if (isset(self::$TempVar[$Ob])&&is_object(self::$TempVar[$Ob])) {
						//On enleve les parenthese si il y a
						$Function = explode("(",$Tab[1]);
						if (isset($Function[1])){
							$Params = explode(")",$Function[1]);
							$Params = $Params[0];
						}else $Params = '';
						$Function = $Function[0];
						if (array_key_exists($Tab[1],self::$TempVar[$Ob])) {
							$Suivre=true;
							if (method_exists(self::$TempVar[$Ob],"Get"))
							$Data = self::$TempVar[$Ob]->Get($Tab[1]);
							else $Data = self::$TempVar[$Ob]->$Tab[1];
						}elseif(method_exists(self::$TempVar[$Ob],$Function)) {
							//Detection de l existence de parenthese
							if ($Params!=""){
								//On verifie si il n y a pas des variables a traduire
								//Alors il y a des parenthese donc des parametres
								$ParamsTemp = explode(",",$Params);
								$Params="";
								$i=0;
								print_r($Params);
								foreach ($ParamsTemp as $K=>$Key) {
									if (sizeof($Tab = explode("[!",$Key))>1){
										$T[$K] =Process::searchAndReplaceVars($Key);
										$Params.="\$T[".$K."]";
									}elseif (sizeof($Tab = explode("[*",$Key))>1){
										$T[$K] =Process::searchAndReplaceVars($Key,"[*","*]");
										$Params.="\$T[".$K."]";
									}else{
										//$Params.=$Key;
										$T[$K] = $Key;
										$Params.="\$T[".$K."]";
									}
									if ($i<sizeof($ParamsTemp)-1) $Params.=",";
									$i++;
								}
// 								echo "return \$this->TempVar[\$Ob]->$Function($Params);";
								$S=self::$TempVar[$Ob];
								$Data = eval("return \$S->$Function(".$Params.");");
							}else{
								$Data=self::$TempVar[$Ob]->$Function();
							}
						}
					}elseif(isset(self::$TempVar[$Ob])&&is_array(self::$TempVar[$Ob])){
						if (array_key_exists($Tab[1],self::$TempVar[$Ob])) {
							$Data = self::$TempVar[$Ob][$Tab[1]];
// 							echo $Tab[0]."::".$Tab[1]." - ".$Data."\r\n";
						}else{
							self::$TempVar[$Ob][$Tab[1]] = "";
							$Data=0;
						}

					}else{
						if ($Tab[1]=="Reset"){
							unset(self::$TempVar[$Tab[0]]);
							unset($GLOBALS["Systeme"]->GetVars[$Tab[0]]);
							unset($GLOBALS["Systeme"]->PostVars[$Tab[0]]);
						}
						//----------------------------------------------//
						// VARIABLE INEXISTANTE	OU VIDE			//
						//----------------------------------------------//
						//On teste d abord si ce n est pas une variable temporaire
						if (isset(self::$TempVar[$Tab[0]])&&is_array(self::$TempVar[$Tab[0]])){
							if (sizeof(self::$TempVar[$Tab[0]])) {
								$Data = self::$TempVar[$Tab[0]][$Tab[1]];
							}else{
								//Le cas ou la variable est une variable Post
								$Data = Parser::ProcessPostVars($Tab[0]);
								$Data = $Data[$Tab[1]];
							}
						}elseif(isset(self::$TempVar[$Tab[0]])&&is_object(self::$TempVar[$Tab[0]])) {
							$Data = self::$TempVar[$Tab[0]];
							$n = $Tab[1];
							$Data = $Data->$n;
						}elseif(is_object(Process::ProcessPostVars($Tab[0]))) {
							$Data = Process::ProcessPostVars($Tab[0]);
							$n = $Tab[1];
							$Data = $Data->$n;
						}elseif(is_array(Process::ProcessPostVars($Tab[0]))) {
							$Data = Process::ProcessPostVars($Tab[0]);
							$Data = $Data[$Tab[1]];
						}
					}
				break;
			}
			//Si le premier mot est un module, alors on recherche des informations sur un objet de ce module.
		}else {

			//On teste d abord si ce n est pas une variable temporaire
			if (isset(self::$TempVar[$Data]))$Data = self::$TempVar[$Data];
			//Le cas ou la variable est une variable Post
			else $Data = Process::ProcessPostVars($Data);
		}
		if (!empty($Defaut)){
			$Data= $Defaut;
		}
		if ($Data===$sData){
			$Data="";
		}
		return $Data;
	}
	//C est la methode qui scanne la chaine a la recherche de balise varible avant d envoyer a processVars
	static function searchAndReplaceVars($Data,$DebBeacon="[!",$FinBeacon="!]") {
		$EndResult="";
		//On ecarte le debut de la chaine jusqu a la balise
 		$TempData = explode($FinBeacon,$Data);
		if (sizeof($TempData)>1){
			$EndResult = array_pop($TempData);
			$TempData = implode($FinBeacon,$TempData).$FinBeacon;
		}else{
			$TempData=$Data;
		}
		$TempData = explode($DebBeacon,$TempData,2);
		//On conserve le debut
  		//echo "\r\n---SEARCH AND REPLACE-".trim($Data)."-----\r\n";
		$BeginResult = $TempData[0];
		//$BeginResult="";
// 		$Data = $TempData[1];
		$nbFerm = $nbOuv = $nbReplace= 0;
		$TempData = explode($DebBeacon,trim(isset($TempData[1]) ? $TempData[1] : ''));
		$Temp="";
		$Result="";
		$Flag=false;
		$l = 0;
		for ($g=0;$g<sizeof($TempData);$g++) {
			$Key = $TempData[$g];
			$nbOuv++;
			//On compte le nombre de fermeture de balise et on compare avec le nombre d ouverture
			$TempData2 = explode($FinBeacon,$Key);
			if (sizeof($TempData2)>1){
				if ($nbOuv>1) $Temp.=$DebBeacon;
				for($x=0;$x<(sizeof($TempData2)-1);$x++) {
					if ($Flag) {
						$TempResult.=$TempData2[$x].$FinBeacon;
					}else{
						//Si le nombre d ouverture est egal au nombre de fermeture
						$nbFerm++;
						if ($nbFerm==$nbOuv){
							//On reconstitue la variable
							$Var = $Temp.$TempData2[$x];
							$Temp=$TempResult="";
							//Remplacement de la variable par sa valeur
// 							echo "--> SEND $Var \r\n";
							$TempResult= Process::processVars($Var);
// 							print_r($TempResult);
							if ((is_object($TempResult)||is_array($TempResult))&&(sizeof($TempData)>$nbOuv+1||$BeginResult!=""||$EndResult!="")){
								if (preg_match("#(.*)(\:\=|\=\=)#",$BeginResult,$Out)) {
									Process::registerTempVar($Out[1],$TempResult);
									return;
								}else{
									Process::registerTempVar("#".$Var."#",$TempResult);
									$TempResult = $DebBeacon."#".$Var."#".$FinBeacon;
								}
// 								echo "-->$Data<-- ".getType($TempResult)." $Result\r\n";
							}elseif((is_object($TempResult)||is_array($TempResult))&&sizeof($TempData)==$nbOuv+1) {
//  								echo "--> $Data $Var ".$BeginResult." ".getType($TempResult)."<-- \r\n";
//  								print_r($TempResult);
								return $TempResult;
							}
/*
							echo "SEARCH AND REPLACE VARS $Var \r\n";
 							print_r($TempResult);*/
							//On reinitialise les variables
							$nbFerm=$nbOuv=0;
							$Flag =true;
						}else{
							//Sinon on remet les Balises
							$Temp.=$TempData2[$x].$FinBeacon;
						}
					}
				}
				if ($Flag) {
					if ($l==sizeof($TempData)-1) {
						//c le dernier ajout
						//$EndResult=$TempData2[(sizeof($TempData2)-1)];
					}else{
						$TempResult.=$TempData2[(sizeof($TempData2)-1)];
					}
				}else{
					$Temp.=$TempData2[(sizeof($TempData2)-1)];
				}
			}else{
				if ($nbOuv>1) {
					$Temp.=$DebBeacon.$Key;
					$TempOuv = $nbOuv;
					//Si la premiere fois qu il trouve une balise alors on enregistre le debut de la phrase
				}else{ $Temp.=$Key;}
			}
			if ($Flag) {
				$Flag=false;
				if ((is_array($TempResult)||is_object($TempResult))&&$BeginResult==""&&$EndResult==""){
					return $TempResult;
				}else{
					if (!is_array($TempResult)&&!is_object($TempResult)) {
						$Result.=$TempResult;
					}else{
						//Le cas de l egalite
						if (sizeof(preg_match("#(.*)(\:\=|\=\=)#",$BeginResult.$Result,$Out))&&$EndResult==""){
							Process::registerTempVar($Out[1],$TempResult);
							return ;
						}else{
							//Donc on stocke la variable dans les variables temporaires
							Process::registerTempVar($Var,$TempResult);
							//$Result.=$DebBeacon.$Var.$FinBeacon;
							$Result=$TempResult;
						}
					}
				}
			}
			//on incremente pour retrouver le dernier enregistrement
			$l++;
		}

		if (is_string($Result)) {
			return (!is_object($Result))?$BeginResult.$Result.$EndResult:$Result;
		}
		if (is_array($Result)) {
			return $Result;
		}
		if (is_object($Result)) return $Result;
	}


	static function processModule($Data) {
		$BaliseTemp["BEACON"] = "MODULE";
		$BaliseTemp["OBJECT"] = "Bloc";
		$BaliseTemp["TYPE"] = "ORPHAN";
		$Balise[]=$BaliseTemp;
		$Data = $this->searchAndReplaceOrphanBeacon($Data,$Balise);
		$Balise="";
		return $Data;
	}
	//Cette methode convertie les variables Registered/GET/POST
	static function getGlobalVar($Name) {
		//Test des variables Registered
		$Temp=Process::getRegVars($Name);
		if ($Temp!=NULL) return $Temp;
		//Test des variables POSTS
		$Temp=Process::getPostVars($Name);
		if ($Temp!=NULL) return $Temp;
		//Test des variables GET
		$Temp=Process::getGetVars($Name);
		if ($Temp!=NULL) return $Temp;
		//Test des variables FILES
		$Temp=Process::getFilesVars($Name);
		if ($Temp!=NULL) return $Temp;
		//Test des variables Temporaires
		if (isset(Process::$TempVar[$Name])) return Process::$TempVar[$Name];
		return false;
	}
	//Cette methode convertie les variables Registered/GET/POST
	//TODO: Empecher des hacks par la methode POST
	static function ProcessPostVars($Data,$Restore=false) {
		$sData=$Data;
		//Test des variables Registered
		$Temp=Process::getRegVars($sData);
		if ($Temp!=NULL) $Data=$Temp;
		$Temp="";
		//Test des variables POSTS
		$Temp=Process::getPostVars($sData);
		if ($Temp!=NULL) $Data=$Temp;
		$Temp="";
		//Test des variables GET
		$Temp=Process::getGetVars($sData);
		if ($Temp!=NULL) $Data=$Temp;
		//Test des variables FILES
		$Temp=Process::getFilesVars($sData);
		if ($Temp!=NULL) $Data=$Temp;
		return $Data;
	}

	//Cette methode recupere la valeur d une variable POST
	static function getPostVars($Data) {
		return $GLOBALS["Systeme"]->getPostVars($Data);
	}

	//Cette methode recupere la valeur d une variable GET
	static function getGetVars($Data) {
		return $GLOBALS["Systeme"]->getGetVars($Data);
	}

	//Cette methode recupere la valeur d une variable REGISTERED
	static function getRegVars($Data) {
		return $GLOBALS["Systeme"]->getRegVars($Data);
	}
	static function getFilesVars($Data) {
		return $GLOBALS["Systeme"]->getFilesVars($Data);
	}
}
