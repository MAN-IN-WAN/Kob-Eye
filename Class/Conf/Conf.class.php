<?php
// #ROLE
// Cette classe permet de generer toute la configuration au chargement de la page
// a partir d un chemin menant vers le fichier xml.
// Cette configuration devrait etre interrogeable depuis n importe quel endroit
// y compris depuis le code KEML mais a partir d un certain noeud
//
// Depuis le code KEML processVars redirigera la requete vers cette classe qui
// analysera la requete et renverra le resultat.
//
// #REQUETE
// La requete prendra la forme d une requete d objet: ex :
// -> Conf::Process::Beacon
// renverra la liste des balises configurée
// -> Conf::Process::Beacon::Storproc
// renverra le detail de la balise storproc. sous forme de tableau
//
// #CODE
// En ce qui concerne le code de la configuration, il sera en format XML
// et sera analysé depuis la classe Xml2Array.
// Plusieurs attributs seront disponibles afin de definir le type de propagation
// des informations de configuration.
// ex:
// -> <XML_TYPE type="Const" type="Private">TEST</XML_TEST>
// Definit une constante XML_TYPE qui a pour valeur TEST et qui ne pourra etre consultée depuis le code KEML.
//
// -> <FILE>Beacon.conf</FILE>
// Designe un autre fichier a scanner afin de completer la configuration d un certain type et qui sera separé pour des raisons pratiques.
//
// #ORGANISATION
// -> CONF 						//Conteneur general
// 	->OPTIONS					//Definition des variables systeme non consultables
// 		->CACHE					//Confguration des caches
// 		->BDD					//Configuration de la base de donn�e contenant les identifiants
// 		->SERVEUR				//Configuration des elements propres au serveur
// 		->AUTH					//Definition des parametres de connexion
// 	->PROCESS					//Defintion des parametres du parseur KEML
// 		->SKIN					//Definition des balises SKINS
// 		->ALL					//Definition de toutes les balises
// 		->POST					//Definition des balises POST
// 	->MODULE					//Parametrage de la classe module
// 		->SYSTEME				//Parametrage du module Systeme
// 			->USER				//Definition des parametres utilisateur
// 			->RIGHTS			//Definition des droits par defaut
// 		->EXPLORATEUR				//Definition des parametres specifiques a l explorateur
// 		->REDACTION				//Definition des parametres specifiques au module redaction
// 		->ETC ...
// 	->SKIN						//Definition des parametres des templates
//
// #ATTRIBUTS STANDARDS
// -> access
// Definit les droits d acces en lecture/ecriture sur une donnee ou un noeud
// -> type
// Definit le type de propagation de la donnee
// 	->const Definie une constante
// 	->Default par defaut une variable est consultable par requete
// 	->array Definie un ensemble convertit en tableau pour une lecture plus simple pour les classes
// -> file
// Definie la necessite de lire un fichier supplementaire pour completer la configuration
// La valeur de l'attribut file definit l'emplacement du fichier a lire.
//
class Conf extends Root{
	var $TabConf;
	var $Consts;
	var $Files;
	function Conf($Url) {
		$CacheO = $Url.".cache";
		/*$Sc = (defined("SCHEMA_CACHE"))?SCHEMA_CACHE:false;
		if (file_exists($CacheO) && (filemtime($CacheO) >= filemtime($Url))&&$Sc) {
			$Cache = file_get_contents($CacheO);
			$Temp = unserialize($Cache);
			$this->Consts = $Temp["Consts"];
			$this->TabConf = $Temp["TabConf"];
			$this->Files = $Temp["Files"];
			//Verification de la date du fichier
			if (is_array($this->Files))foreach ($this->Files as $F){
				if (@filemtime($F)>@filemtime($CacheO)){
				 	$this->TabConf=$this->Files=$this->Consts="";
				}
			}
		}*/
		if (!is_array($this->TabConf)){
			//Parsing du fichier de configuration
			$Obj = new xml2array($Url);
			$TabXml[0] = $Obj->Tableau["CONF"];
			$this->Files[] = $Url;
			//Traitement des donn�es du tableau
			$this->TabConf = $this->Parse($TabXml,"","CONF");
			/*if ($this->Consts["CONF_CACHE"]){
				$T["TabConf"] = $this->TabConf;
				$T["Files"] = $this->Files;
				$T["Consts"] = $this->Consts;
				$this->writeCacheFile(serialize($T),$Url.".cache");
			}*/
		}
		//On declare les constantes
		if (is_array($this->Consts))foreach ($this->Consts as $K=>$C){
			define($K,$C);
		}
	}
	function writeCacheFile($Data,$Url) {
		if (!$File=fopen ($Url,"w"))return false;
		fwrite($File,$Data);
		fclose($File);
	}
	//Fonction recursive qui est appel�e a chaque niveau de recursivite
	//Elle reagit aux attributs standards de la configuration
	function Parse($TabOrig,$option="",$Name="",$formatonly = false) {
		//@ Defini les attributs
		//# Defini les classes
		if (is_array($TabOrig))foreach ($TabOrig as $Tab) {
			if (isset($Tab["@"]))if (sizeof($Tab["@"]))foreach ($Tab["@"] as $Att=>$Value) {
				//On analyse les attributs
				switch ($Att){
					case "access":
						$option[$Att] = $Value;
					break;
					case "type":
						$option[$Att] = $Value;
					break;
					case "file":
						$option[$Att] = $Value;
					break;
				}
			}
			//On analyse les elements
			if (sizeof($Tab["#"])==1){
				if (is_string($Tab["#"])) {
					//Le cas ou il y a directement une valeur
					//->Enregistrement dans le tableau
					// 					echo "-> Enregistrement dans le tableau de la valeur ".$Tab["#"]."\r\n";
					$Result=$this->SaveValue($Tab["#"],$option,$Name);
				}else{
					$Keys = array_keys($Tab["#"]);
					//Le cas ou il n y qu un seul element du meme type
					//->Lancement du parse en mode recursif
					// 					echo "-> Enregistrement recursif du tableau \r\n";
					$Result[$Keys[0]]=$this->Parse($Tab["#"][$Keys[0]],$option,$Keys[0]);

				}
			}elseif(sizeof($Tab["#"])>1){
				//Le cas ou il y plusieurs elements du meme type
				//->Lancement du parse en mode recursif
				foreach ($Tab["#"] as $Item=>$Value){
					// 					echo "-> on relance recursivement TEST la methode parse pour ".$Item."\r\n";
					$Result[$Item]=$this->Parse($Value,$option,$Item);
				}
			}
			if (sizeof($TabOrig)>1) {
				$TabResult[] = $Result;
			}else $TabResult = $Result;
			// 			print_r($TabOrig);
			$Result="";
		}

                
		return $TabResult;
	}

	//Fonction recursive qui est appel�e a chaque niveau de recursivite
	//Elle reagit aux attributs standards de la configuration
	static function parseOnly($TabOrig,$option="") {
		//@ Defini les attributs
		//# Defini les classes
		if (is_array($TabOrig))foreach ($TabOrig as $Tab) {
			if (isset($Tab["@"]))if (sizeof($Tab["@"]))foreach ($Tab["@"] as $Att=>$Value) {
				//On analyse les attributs
				switch ($Att){
					case "access":
						$option[$Att] = $Value;
						break;
					case "type":
						$option[$Att] = $Value;
						break;
					case "file":
						$option[$Att] = $Value;
						break;
				}
			}
			//On analyse les elements
			if (sizeof($Tab["#"])==1){
				if (is_string($Tab["#"])) {
					//Le cas ou il y a directement une valeur
					//->Enregistrement dans le tableau
					// 					echo "-> Enregistrement dans le tableau de la valeur ".$Tab["#"]."\r\n";
					$Result=$Tab["#"];
				}else{
					$Keys = array_keys($Tab["#"]);
					//Le cas ou il n y qu un seul element du meme type
					//->Lancement du parse en mode recursif
					// 					echo "-> Enregistrement recursif du tableau \r\n";
					$Result[$Keys[0]]=Conf::parseOnly($Tab["#"][$Keys[0]],$option,$Keys[0]);

				}
			}elseif(sizeof($Tab["#"])>1){
				//Le cas ou il y plusieurs elements du meme type
				//->Lancement du parse en mode recursif
				foreach ($Tab["#"] as $Item=>$Value){
					// 					echo "-> on relance recursivement TEST la methode parse pour ".$Item."\r\n";
					$Result[$Item]=Conf::parseOnly($Value,$option,$Item);
				}
			}
			if (sizeof($TabOrig)>1) {
				$TabResult[] = $Result;
			}else $TabResult = $Result;
			// 			print_r($TabOrig);
			$Result="";
		}


		return $TabResult;
	}

	function SaveValue($Value,$options,$Name="") {
		//Traitement des options
		if (!empty($options["type"])) {
			switch ($options["type"]) {
				case "const";
					$this->Consts[$Name] = $Value;
				break;
				case "raw":
					if (!empty($options["file"])) {
						//Il y a donc un nouveau fichier a recuperer
						$temp = new xml2array($options["file"]);
						$Value = $temp->Tableau;
						$Value["@"]["file"] = $options["file"];
						$this->Files[] = $options["file"];
						return $Value;
					}
				break;
			}
		}
		if (!empty($options["file"])) {
			//Il y a donc un nouveau fichier a recuperer
			$temp = new xml2array($options["file"]);
			$temp = $temp->Tableau;
			$Value = $this->Parse($temp,"");
			// 			$tempKeys = array_keys($Value);
			// 			$Value=$Value[$tempKeys[0]];
			$Value["@"]["file"] = $options["file"];
			$this->Files[] = $options["file"];
		}
		if (!empty($options["access"])) {}
		return $Value;
	}
	function ArrayKeyExists($Val,$Tab) {
		if (is_array($Tab))foreach ($Tab as $K=>$V) if (strtolower($K)==strtolower($Val))return true;
		return false;
	}
	//Cette methode analyse la requete et retourne le resultat demandé
	//Cette methode est necessairement recursive
	//Il sera necessaire dans l avenir de gerer les droits d acces
	function get($Query,$Tab="INIT") {
		// 		print_r($this->TabConf);
		if ($Tab=="INIT") $Tab = $this->TabConf;
		$Temp = explode("::",$Query,2);

		if (isset($Temp[1])&&$Temp[1]!=""&&$this->ArrayKeyExists($Temp[0],$Tab)) {
			//   			echo $Temp[0]." OK\r\n";
			$Result=$this->get(strtoupper($Temp[1]),$Tab[$Temp[0]]);
		}else{
			if ($this->ArrayKeyExists($Temp[0],$Tab)) {
				$Result = $Tab[$Temp[0]];
				//  				echo $Temp[0]." OK\r\n";
			}else{
				$Result= 0;

				// 				echo $Temp[0]." ERROR\r\n";
			}
		}
		return $Result;
	}
}

?>