<?php
class Parser {
	public static $Objects;
	public static $Beacons;
	public static $PostBeacons;
// --------------------------------------------------//
//			INITIALISEUR		     //
// --------------------------------------------------//
	static function Init($Type="") {
// 		$this->initPostBeacons();
		self::$PostBeacons=$GLOBALS["Systeme"]->Conf->get("PROCESS::POST::BEACON");
		switch ($Type) {
			case "Skin":
// 				$this->initSkinsBeacons();
				self::$Beacons=$GLOBALS["Systeme"]->Conf->get("PROCESS::SKIN::BEACON");
			break;
			default:
			case "Struct":
// 				$this->initAllBeacons();
				self::$Beacons=$GLOBALS["Systeme"]->Conf->get("PROCESS::ALL::BEACON");
			break;
		}
	}


// --------------------------------------------------//
//			TRAITEMENT DES BALISES	     //
// --------------------------------------------------//

	//24/04/2007
	//Methode Generique pour generer une balise
	//HEADER TITLE DESCIPTION ... OK
	//BLOC COL LINE MODULE ... OK
	//IF SWITCH ... OK
	//STORPROC LIMIT ORDER ... OK
	//URL SUBSTR ... OK
	//LIB ... OK
	static function getBeacon($Var,$Data,$Beacon) {
		$Class = $Beacon["OBJECT"];
		$Obj = new $Class;
		$Obj->setFromVar($Var,$Data,$Beacon);
		self::$Objects[] = $Obj;
	}

// --------------------------------------------------//
//			PROCESSUS		     //
// --------------------------------------------------//

	//Cette methode place une chaine de caratere dans le conteneur temporaire
	static function getVarChar($Data) {
		if (!is_string($Data)){
			self::$Objects[]=$Data;
			return ;
		}
		$Test = $Data;
		$Test = str_replace("	","",$Test);
		$Test = preg_replace("#\s#","",$Data);
		if ($Test!="") {
			self::$Objects[] = $Data;
		}
	}

	//Recherche la premiere balise du tableau dans la chaine
	static function searchFirstBeacon($Data,$BeaconTab){
		$Result='';
		if (is_string($Data)) {
			foreach ($BeaconTab as $Key){
				$Temp[] = explode("[".$Key["BEACON"],$Data,2);
			}
			$Long = strlen($Data);
			$i=0;
			foreach ($Temp as $Key){
				if (strlen($Key[0])<$Long) {
					$Long = strlen($Key[0]);
					$Result = $i;
				}
				$i++;
			}
			return $Result;
		}else{
			foreach ($Data as $Temp) {
				if (is_string($Temp)) {
					foreach ($BeaconTab as $k=>$Key){
						$TempResult[$k] = explode("[".$Key["BEACON"],$Temp);
					}
					$Long = strlen($Temp);
					$Max = strlen($Temp);
					foreach ($TempResult as $k=>$Key){
						if (strlen($Key[0])<$Long) {
							$Long = strlen($Key[0]);
							$Result = $k;
						}
					}
					if ($Max!=$Long) return $Result;
				}
			}
		}
	}

	//Supprime les balises non utilisees du tableau
	static function deleteUnusedBeacon($Data,$BeaconTab) {
		$Result=array();
		if (is_string($Data)) {
			if (is_array($BeaconTab)) {
				foreach ($BeaconTab as $Key){
					if (sizeof(explode("[".$Key["BEACON"],$Data,2))>1) $Result[]=$Key;
				}
			}
			if (isset($Result))return $Result;
		}elseif(is_array($Data)){
			if (is_array($BeaconTab)) {
				foreach ($BeaconTab as $Key){
					$Flag=false;
					foreach ($Data as $Temp) {
						if (is_string($Temp))
							if (sizeof(explode("[",$Temp))>1) {
								$Flag=true;
							}
					}
					if ($Flag) $Result[]=$Key;
				}
			}
		}
		return $Result;
	}

	//On separe les attributs de la donnée
	static function splitAttributes($Data) {
		if (is_string($Data)) {
			//Recherche du Data et des Attributs
			$TempData = explode("]",$Data);
			$m=0;
			$Attributes="";
			$Donnee="";
			$Flag=false;
			for ($l=0;$l<sizeof($TempData);$l++) {
				if (!$Flag) {
					$m+=substr_count($TempData[$l],"[");
					if (substr($TempData[$l],0,1)==" "){
						//Si le premier caractere est un espace
						$Attributes.=substr($TempData[$l],1,strlen($TempData[$l]));
					}else{
						$Attributes.=$TempData[$l];
					}
					if ($m==$l) {$Flag=true;}else{$Attributes.="]";}
				}else{
					$Donnee.=$TempData[$l];
					if ($l<sizeof($TempData)-1)$Donnee.="]";
				}
			}
			$Result[0] = $Attributes;
			$Result[1] = $Donnee;
			return $Result;
		}else{
			//Recherche du Data et des Attributs
			$TempData = explode("]",$Data[0]);
			$m=0;
			$Attributes="";
			$Donnee="";
			$Flag=false;
			for ($l=0;$l<sizeof($TempData);$l++) {
				if (!$Flag) {
					$m+=substr_count($TempData[$l],"[");
					if (substr($TempData[$l],0,1)==" "){
						//Si le premier caractere est un espace
						$Attributes.=substr($TempData[$l],1,strlen($TempData[$l]));
					}else{
						$Attributes.=$TempData[$l];
					}
					if ($m==$l) {$Flag=true;}else{$Attributes.="]";}
				}else{
					$Donnee.=$TempData[$l];
					if ($l<sizeof($TempData)-1)$Donnee.="]";
				}
			}
			$Result[0] = $Attributes;
			$Data[0] = $Donnee;
			$Result[1] = $Data;
			return $Result;
		}
	}

	//Recherche le contenu de la balise mentionne et la remplace par le retour de sa fonction
	static function searchAndReplaceBeacon($Data,$Beacon,$AllBeacon) {
		if (sizeof($Beacon)) {
			if (array_key_exists("FUNCTION",$Beacon)){
				 $Function = $Beacon["FUNCTION"];
			 }else $Function = "getBeacon";
			//if (array_key_exists("POST",$Beacon)&&!$NoPost) $Post = $Beacon["POST"];
			if (array_key_exists("VAR",$Beacon)) $Var = $Beacon["VAR"];
			if (array_key_exists("REPLACE",$Beacon)) $Replace = $Beacon["REPLACE"];
		}
		$Result=$EndResult=$BeginResult='';
		$BeaconTab = $Beacon;
		$Post='';
		$Beacon = $Beacon["BEACON"];
		if (empty($AllBeacon)) $NoError=true; else $NoError = false;
		if (is_string($Data)) {
			//On ecarte le debut de la chaine jusqu a la balise
			$TempData = explode("[".$Beacon,$Data,2);
			//On conserve le debut
			$BeginResult = $TempData[0];
			//On enregistre Le debut en tant que varchar
			$BeginResult=self::getVarChar($BeginResult);
//			$BeginResult="";
			$Replace="";
			$Data = $TempData[1];
			$nbFerm = $nbOuv = $nbReplace= 0;
			$TempData = explode("[".$Beacon,$Data);
			$Temp="";
			$Flag=false;
			$l = 0;
			for ($g=0;$g<sizeof($TempData);$g++) {
				$Key = $TempData[$g];
				$nbOuv++;
				//On compte le nombre de fermeture de balise et on compare avec le nombre d ouverture
				$TempData2 = explode("[/".$Beacon."]",$Key);
				if (sizeof($TempData2)>1){
					if ($nbOuv>1) $Temp.="[".$Beacon;
					for($x=0;$x<(sizeof($TempData2)-1);$x++) {
						if ($Flag) {
							$TempResult.=$TempData2[$x]."[/".$Beacon."]";
						}else{
							//Si le nombre d ouverture est egal au nombre de fermeture
							$nbFerm++;
							if ($nbFerm==$nbOuv){
								//On separe les attributs du DATA
								$TempData3 = Parser::splitAttributes($Temp.$TempData2[$x]);
								$Temp=$TempResult="";
								$Attributes = $TempData3[0];
								$Donnee = $TempData3[1];
								if ((strlen($Function))&&((!strlen($Replace)))) {
									/*$FuncString = "return self::".$Function."(stripslashes('".addslashes($Attributes)."'),stripslashes('".addslashes($Donnee)."')";
									$FuncString.=",\$BeaconTab";
									$FuncString.=");";
									$TempResult.= eval($FuncString);*/
// 									echo ">>> $Function ($Attributes,$Donnee,$BeaconTab)\r\n";
									$TempResult.= Parser::$Function($Attributes,$Donnee,$BeaconTab);
								}else{
									$TempResult.= "[".$Replace."-".$nbReplace."]";
									$ReplaceResult[$Replace."-".$nbReplace] = $Attributes."]".$Donnee;
									$nbReplace++;
								}
								//On reinitialise les variables
								$nbFerm=$nbOuv=0;
								$Flag =true;

							}else{
								//Sinon on remet les Balises
								$Temp.=$TempData2[$x]."[/".$Beacon."]";
							}
						}
					}
					if ($Flag) {
						if ($l==sizeof($TempData)-1) {
							//c le dernier ajout
							$EndResult=$TempData2[(sizeof($TempData2)-1)];
						}else{
							$TempResult.=$TempData2[(sizeof($TempData2)-1)];
						}
					}else{
						$Temp.=$TempData2[(sizeof($TempData2)-1)];
					}
				}else{
					if ($nbOuv>1) {
						$Temp.="[".$Beacon.$Key;
						$TempOuv = $nbOuv;
						//Si la premiere fois qu il trouve une balise alors on enregistre le debut de la phrase
					}else{ $Temp.=$Key;}
				}
				if ($Flag) {
					$Flag=false;
					$Result.=$TempResult;
					//On teste si la premiere balise suivante est bien la meme


	/*				if ($Beacon=="BLOC"){ echo "-------------------------------------------\r\n";print_r($AllBeacon);print_r($TempData);echo $Result."\r\n";
						echo "-=>".Parser::searchFirstBeacon($Result,$AllBeacon)."\r\n";
					}*/
					if (!$NoError) {
	// 					echo "Test->".$Result;
						$TR = preg_replace("#\s#","",$Result);
	/*					$TR = str_replace(" ","",$Result);
						$TR = str_replace("\r","",$TR);
						$TR = str_replace("\n","",$TR);*/
	// 					echo " Resultat->".$TR."\r\n";

						if (strlen(Parser::searchFirstBeacon($Result,$AllBeacon))||$TR!="") {
		/*					echo "-=> Test ".$Beacon." - ".Parser::searchFirstBeacon($Result,$AllBeacon)."\r\n";
							print_r($TempData);
							echo "-------------------------------------------\r\n";
		*/
							for ($v=$g+1;$v<sizeof($TempData);$v++) $Result.="[".$Beacon.$TempData[$v];
		// 					echo "-------------------\r\n".$Result."\r\n";
							//On sort de la boucle
							$g=sizeof($TempData);
						}/*else{
							//C la meme balise donc on continue par contre on stocke les phrases eventuelles
							$this->getVarChar($Result);
							$Result="";
						}*/
					}
				}
				//on incremente pour retrouver le dernier enregistrement
				$l++;
			}
			return $BeginResult.$Result.$EndResult;
		}else{
			//On reinitialise les variables
// 			echo ">>>------------------ARRAY--------------\r\n";
			$Temp="";
			$Flag=false;
			$nbFerm = $nbOuv = $nbReplace= 0;
			$Begin = false;
			foreach ($Data as $DataTab) {
				if (is_string($DataTab)) {
					if ((sizeof(explode("[".$Beacon,$DataTab,2))>1)||$Begin) {
						$Begin = true;

						if (!sizeof(explode("[/".$Beacon."]",$DataTab))) {
							$TempData = explode("[".$Beacon,$DataTab,2);
							//ON initialise les variables du processus
							$TempData = explode("[".$Beacon,$TempData[1]);
						}else{
							$TempData = explode("[".$Beacon,$DataTab);
						}
						$l = 0;
						//On boucle sur les ouvertures de balise de la chaine en cours
// 						echo ">>> ON A TROUV� $Beacon ".sizeof($TempData)."\r\n";
						for ($g=0;$g<sizeof($TempData);$g++) {
							$Key = $TempData[$g];
							if ($g>0) $nbOuv++;
							//if ($nbOuv==1) $BigResult=Array();
							//On compte le nombre de fermeture de balise et on compare avec le nombre d ouverture
							$TempData2 = explode("[/".$Beacon."]",$Key);
							if (sizeof($TempData2)>1){
								if ($nbOuv>1) $Temp.="[".$Beacon;
								for($x=0;$x<(sizeof($TempData2)-1);$x++) {
									if ($Flag) {
										//Si on est dans la variable de resultat
										$TempResult.=$TempData2[$x]."[/".$Beacon."]";
									}else{
										//Si le nombre d ouverture est egal au nombre de fermeture
										$nbFerm++;
										if ($nbFerm==$nbOuv){
											//C est le debut de la variable resultat
											//On separe les attributs du DATA
											if (isset($BigResult))$TempData4 = $BigResult;
											$TempData4[]=$TempData2[$x];
											$TempData3 = Parser::splitAttributes($TempData4);
											$Temp=$TempResult="";
											$Attributes = $TempData3[0];
											$Donnee = $TempData3[1];
											$BigResult=$Donnee;
											if (strlen($Function)) {
												//Le cas d une execution de fonction
												/*$FuncString = "return \$this->".$Function."(stripslashes('".addslashes($Attributes)."'),\$BigResult";
												$FuncString.=",\$BeaconTab";
												$FuncString.=");";
												$TempResult.= eval($FuncString);*/
 												//echo ">>> $Function\r\n";
												if (isset($BeaconTab['TARGET']))$TempResult .= $BeaconTab['TARGET']->$Function($Attributes,$Donnee,$BeaconTab);
												else $TempResult .= Parser::$Function($Attributes,$Donnee,$BeaconTab);
											}else{
												//Le cas d un remplacement
												$TempResult.= "[".$Replace."-".$nbReplace."]";
												$ReplaceResult[$Replace."-".$nbReplace] = $Attributes."]".$Donnee;
												$nbReplace++;
											}
											$BigResult="";
											//On reinitialise les variables
											$nbFerm=$nbOuv=0;
											$Flag =true;

										}else{
											//Sinon on remet les Balises
											$Temp.=$TempData2[$x]."[/".$Beacon."]";
										}
									}
								}
								if ($Flag) {
									if ($l==sizeof($TempData)-1) {
										//c le dernier ajout
										$EndResult=$TempData2[(sizeof($TempData2)-1)];
									}else{
										$TempResult.=$TempData2[(sizeof($TempData2)-1)];
									}
								}else{
									$Temp.=$TempData2[(sizeof($TempData2)-1)];
								}
							}else{
								//Il y a des balises d ouverture mais pas de fermeture
								if ($nbOuv>1) {
									//Si on a passe� plus d une ouverture alors on ajoute la balise
									$Temp.="[".$Beacon.$Key;
									$TempOuv = $nbOuv;
								}else{
									//Si c est la premiere alors on enregistre simplement la phrase
									$Temp.=$Key;
								}
							}
							if ($Flag) {
								//Si nous sommes dans une variable resultat
								$Flag=false;
								$Result.=$TempResult;
/*								echo "////////////////////////////////////////\r\n";
								echo $Result;*/
								if (!$NoError) {
									$TR = preg_replace("#\s#","",$Result);
									if (strlen(Parser::searchFirstBeacon($Result,$AllBeacon))||$TR!="") {
										for ($v=$g+1;$v<sizeof($TempData);$v++) $Result.="[".$Beacon.$TempData[$v];
										$g=sizeof($TempData);
									}
								}
							}
							//on incremente pour retrouver le dernier enregistrement
							$l++;
						}
						$BigResult[]=$Temp;
						$Temp="";
					}else{
						//Il n y pas encore eu d ouverture de balise
					}
				}else{
					//C un objet donc on le rajoute dasn la variable resultat
					if ($Begin)$BigResult[]=$DataTab;
				}
			}
			return $BigResult;

		}
	}
	//Recherche de la premiere balise dans la donnée en entree
	//Ensuite on recherche la balise fermante correspondante
	static function processChild($Data,$MotherBeacon,$BeaconTab) {
		//Analyse du tableau des balises
		$MotherBeaconTab["BEACON"] = $MotherBeacon;
//		$MotherBeaconTab["REPLACE"] = "TEMP-".$MotherBeacon;
		$BeaconTab[] = $MotherBeaconTab;
		$BeaconTabTemp = Parser::deleteUnusedBeacon($Data,$BeaconTab);
		//Recherche de la premiere balise
		if (sizeof($BeaconTabTemp)) {
			$FirstBeacon = Parser::searchFirstBeacon($Data,$BeaconTabTemp);
			//recuperation du contenu et execution des fonctions
			if (isset($BeaconTabTemp[$FirstBeacon])&&isset($BeaconTabTemp[$FirstBeacon]["BEACON"])&&$BeaconTabTemp[$FirstBeacon]["BEACON"]==$MotherBeacon) {
				//FIXME
/*				//On stoke la balise dans une variable temporaire
				$DataTemp = Parser::searchAndReplaceBeacon($Data,$BeaconTabTemp[$FirstBeacon]);
				$Data = $DataTemp[0];
				$ReplaceTab = $DataTemp[1];
				if (is_array($ReplaceTab)) $ReplaceKey = array_keys($ReplaceTab);
				for ($i=0;$i<sizeof($ReplaceTab);$i++) $Data=preg_replace("#\[".$ReplaceKey[$i]."]#",$ReplaceTab[$ReplaceKey[$i]],$Data);*/
			}elseif (isset($BeaconTabTemp[$FirstBeacon])){
				$Data = Parser::searchAndReplaceBeacon($Data,$BeaconTabTemp[$FirstBeacon],array());
			}
/*			if ($Recursiv) {
				$TempData = Parser::processRecursiv($Data,$MotherBeacon,$BeaconTab);
				if ($TempData!=$Data) $Data=$TempData;
			}*/
		}
		//retour Data;
		return $Data;
	}

	//Recherche de la premiere balise appartenant a la balise mere
	static function processChildOrphanBeacon($Data,$MotherBeacon,$BeaconTab) {
		//Analyse du tableau des balises
		$MotherBeaconTab["BEACON"] = $MotherBeacon;
		$MotherBeaconTab["REPLACE"] = "TEMP-".$MotherBeacon;
		$BeaconTabTemp[]=$MotherBeaconTab;
		$BeaconTabTemp = Parser::deleteUnusedBeacon($Data,$BeaconTabTemp);
		//Recherche de la premiere balise
		if (sizeof($BeaconTabTemp)) {
			$FirstBeacon = Parser::searchFirstBeacon($Data,$BeaconTabTemp);
			//recuperation du contenu et execution des fonctions
			//On stoke la balise dans une variable temporaire
			$DataTemp = Parser::searchAndReplaceBeacon($Data,$BeaconTabTemp[$FirstBeacon],array());
			$Data = $DataTemp[0];
			$ReplaceTab = $DataTemp[1];
		}
		//Maintenant on execute les fonctions
		$Data = Parser::searchAndReplaceOrphanBeacon($Data,$BeaconTab);
		if (sizeof($BeaconTabTemp)) {
			//Ensuite on remplace les balises Temporaires
			if (is_array($ReplaceTab)) $ReplaceKey = array_keys($ReplaceTab);
			for ($i=0;$i<sizeof($ReplaceTab);$i++) $Data=preg_replace("#\[".$ReplaceKey[$i]."\]#","[".$MotherBeacon." ".$ReplaceTab[$ReplaceKey[$i]]."[/".$MotherBeacon."]",$Data);
		}

		//retour Data;
		return $Data;
	}


	//Recherche des balises orphelines et remplacement par function.
	static function searchAndReplaceOrphanBeacon($Data,$Beacon) {
		$TempData = $Data;
		$Result="";
		$Temp = explode("[".$Beacon["BEACON"],$TempData,2);
		if (sizeof($Temp)>1) {
			Parser::getVarChar($Temp[0]);
			for ($i=1;$i<sizeof($Temp);$i++) {
				$TempResult = Parser::splitAttributes($Temp[$i]);
				Parser::getBeacon($TempResult[0],false,$Beacon);
				//On complete le resultat
				$Result.=$TempResult[1];
			}
		}
		return $Result;
	}


	//Recherche de la premiere balise dans la donnée en entree
	//Ensuite on recherche la balise fermante correspondante
	static function processSplit($Data,$MotherBeacon,$Beacon) {
		if (is_string($Data)) {
			//Analyse du tableau des balises
			$BeaconTab[] = $Beacon;
			$BeaconTabTemp = Parser::deleteUnusedBeacon($Data,$BeaconTab);
			if (sizeof($BeaconTabTemp)) {
				//On divise le data
				$TempData = explode("[".$Beacon."]",$Data);
				//ensuite on parcoure le tableau pour rechercher les ouvertures/fermeture de la balise mere
				$i=$nbOuv=$nbFerm=$j=0;
				foreach ($TempData as $Key){
					//On compte les ouvertures
					$nbOuv += substr_count($Key,"[".$MotherBeacon);
					//On compte les fermetures
					$nbFerm += substr_count($Key,"[/".$MotherBeacon."]");
					if ($nbOuv==$nbFerm) {
						$Result[$i].=$Key;
						$i++;
					}else{
						$nbOuv=$nbFerm=0;
						//Sinon c pas le bon encore donc on ajoute la chaine
						$Result[$i].=$Key;
						if ($j<sizeof($TempData)-1) $Result[$i].="[".$Beacon."]";
					}
					$j++;
				}

			}
			//retour Data;
			return $Result;
		}elseif(is_array($Data)) {
			//Analyse du tableau des balises
			$BeaconTab[] = $Beacon;
			$BeaconTabTemp = Parser::deleteUnusedBeacon($Data,$BeaconTab);
			if (!empty($BeaconTabTemp)) {
				//On divise le data
				if (!empty($Data)) {
					$i=0;
					foreach ($Data as $Temp) {
						if (is_string($Temp)) {
							$TempData = explode("[".$Beacon."]",$Temp);
							//ensuite on parcoure le tableau pour rechercher les ouvertures/fermeture de la balise mere
							if (sizeof($TempData)>1) {
								$Result[$i][]=$TempData[0];
								$i=1;
								$Result[$i][]=$TempData[1];
							}else{
								$Result[$i][]=$Temp;
							}
						}else{
							$Result[$i][]=$Temp;
						}
					}
				}
			}else{
				$Result[0]=$Data;
			}
			//retour Data;
			return $Result;
		}
	}
	//Recherche de la premiere balise dans la donnée en entree
	//Ensuite on recherche la balise fermante correspondante
	static function processRecursiv($Data,$BeaconTab,$NoPost=false) {
		//Analyse du tableau des balises
		$BeaconTabTemp = Parser::deleteUnusedBeacon($Data,$BeaconTab);
		//Recherche de la premiere balise
		if (sizeof($BeaconTabTemp)) {
			$FirstBeacon = Parser::searchFirstBeacon($Data,$BeaconTabTemp);
			//recuperation du contenu et execution des fonctions
			if (!isset($BeaconTabTemp[$FirstBeacon]["TYPE"])||$BeaconTabTemp[$FirstBeacon]["TYPE"]!="ORPHAN") {
				$Data = Parser::searchAndReplaceBeacon($Data,$BeaconTabTemp[$FirstBeacon],$BeaconTabTemp);
			}else{
// 				echo "RECHERCHE ENFANT ORPHAN ".$BeaconTabTemp[$FirstBeacon]["BEACON"]."\r\n";
				$Data =Parser::searchAndReplaceOrphanBeacon($Data,$BeaconTabTemp[$FirstBeacon]);
			}
			//Appel recursif.
			$TempData = Parser::processRecursiv($Data,$BeaconTab,$NoPost);
			if ($TempData!=$Data) $Data=$TempData;
		}else{
			//Il n y a aucune balise donc on stocke la phrase restante en VarChar
			Parser::getVarChar($Data);
		}
		//retour Data;
		return $Data;
	}


	//Remplacement de la balise DATA
	static function ProcessData($Data,$Contenu){
		return preg_replace('#\[DATA\]#',$Contenu , $Data);
	}

	static function processRequire($Data) {
		if (is_string($Data)) {
			//Recuperation d un fichier du cache
			preg_match("#\[\=(.*)\=\]#",$Data,$Out);
			if (!isset($Out[1])||!file_exists($Out[1])) {
				return $Data;
			}else{
				return unserialize(file_get_contents($Out[1]));
			}
		}elseif (is_array($Data)) {
			for($i=0;$i<sizeof($Data);$i++) {
				if (is_string($Data[$i])) {
					//Recuperation d un fichier du cache
					preg_match("#\[\=(.*)\=\]#",$Data[$i],$Out);
					if (isset($Out[1])&&file_exists($Out[1])) {
						$Data[$i]=unserialize(file_get_contents($Out[1]));
					}
				}
			}
		}
		return $Data;
	}
	static function getContent($TabObj) {
		$Data="";
		if (is_array($TabObj)) {
			for ($i=0;$i<count($TabObj);$i++) {
				if (is_object($TabObj[$i])) {
					if (method_exists($TabObj[$i],"Affich"))$Data .= $TabObj[$i]->Affich();
				}elseif (is_string($TabObj[$i])){
					$Data .= (string)$TabObj[$i];
				}
			}
		}
		return $Data;
	}
	static function PostProcessing($Data) {
		//On supprime les commentaires
		self::$Objects=Array();
		$Data=Parser::processRecursiv($Data,self::$PostBeacons,true);
		$Data=Parser::getContent(self::$Objects);
		return $Data;
	}

	static function Processing($Data,$NoPost=false,$Tab=Array()) {
		//O supprime les commentaires
 		$Data = preg_replace("#(?<!http:)(?<!https:)(?<!:)//(.*)#m","",$Data);
		self::$Objects = $Tab;
		$Data=Parser::processRecursiv($Data,self::$Beacons,$NoPost);
		if (is_array(self::$Objects)) foreach (self::$Objects as $K=>$D) {
			if (is_object($D)){
				$D->init();
				$Result[$K] = $D;
			}else $Result[$K] = $D;
		}
		self::$Objects = "";
// 		echo "RETOUR PROCESS >>>>> \r\n";
// 		print_r($Result);
		if (isset($Result))return $Result;
	}
}
?>