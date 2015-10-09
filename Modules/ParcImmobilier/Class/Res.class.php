<?
class Produit extends genericClass {
	function Produit($Mod,$Tab){
		genericClass::__construct($Mod,$Tab);
	}
	function genRef() {
		//Generation des references d un produit
		//On supprime les references existantes
		$Refs = $GLOBALS['Systeme']->Modules[$this->Module]->callData($this->myUrl."/Reference");
		if (is_array($Refs))foreach ($Refs as $R){
			$T = new genericClass($this->Module,$R);
			$T->Delete();
		}
		//On recupere les modeles differents
		$ModTab = $GLOBALS['Systeme']->Modules[$this->Module]->callData($this->myUrl."/Modele");
		if (is_array($ModTab))foreach ($ModTab as $Key=>$Mod) {
			//On genere les tableaux contenant les resultats des valeurs
			$ModValues[$Mod["Id"]] = $GLOBALS['Systeme']->Modules[$this->Module]->callData("Modele/".$Mod["Id"]."/ModeleValeur");
		}
		if (is_array($ModTab))foreach ($ModTab as $Key=>$Mod) {
			if ($Key==0) {
				if (is_array($ModValues[$Mod["Id"]])) foreach ($ModValues[$Mod["Id"]] as $V) {
					$Result[][] = $V;
				}
			}else{
			//Construction de la chaine de reference
				$TempResult="";
				if (is_array($Result))foreach ($Result as $K=>$Res){
					if (is_array($ModValues[$Mod["Id"]])) foreach ($ModValues[$Mod["Id"]] as $V) {
						$Temp =$Res;
						$Temp[] = $V;
						$TempResult[] = $Temp;
					}
				}
				$Result = $TempResult;
			}
			if ($Key==sizeof($ModTab)-1) $Sortie = $Result;
		}
		//Creation des nouvelles references
		foreach ($Sortie as $S){
			$T = new genericClass($this->Module,"Reference");
			$f=true;$Re=$this->Reference;
			foreach ($S as $V) {
				$T->AddParent($this->Module."/ModeleValeur/".$V["Id"]);
				$Re .= $V["Ref"];
				$f =false;
			}
			//Calcul du tarif
			$Variation = 0;
			foreach ($T->Parents as $P) {
				$P =  $GLOBALS['Systeme']->Modules[$this->Module]->callData("Boutique/ModeleValeur/".$P["Id"]);
				//Recuperation des variations de tarif par modele
				$Variation += $P[0]["VariationTarif"];
			}
			$Tarif = $GLOBALS['Systeme']->Modules[$this->Module]->callData($this->myUrl."/Tarif");
			$T->Set("Tarif",$Variation+$Tarif[0]["Montant"]);
			$T->Set("Ref",$Re);
			$T->Save();
		}
		return "<li> ".sizeof($Sortie)." R�f�rences cr�es</li>";
	}
	//Generation de la reference du produit en cours avec les modeles fournies en parametre
	function getRef($ModTab) {
		$Reference = $this->Reference;
		foreach ($GLOBALS['Systeme']->Modules[$this->Module]->callData($this->myUrl."/Modele") as $M) {
			$Ref= $GLOBALS['Systeme']->Modules[$this->Module]->callData($this->Module."/ModeleValeur/".$ModTab[$M["Id"]]);
			if (empty($Ref[0]["Ref"])) {
				//Donc la reference n existe pas et il faut regenerer les references de l objet
				$this->genRef();
				$Ref= $GLOBALS['Systeme']->Modules[$this->Module]->callData($this->Module."/ModeleValeur/".$ModTab[$M["Id"]]);
			}
			$Reference .= $Ref[0]["Ref"];
		}
		return $Reference;
	}
	function genKeyWords() {
		include_once("Class/Lib/class.autokeyword.php");
		//recensement des champs textuels
		$Props = $this->Proprietes(false,true);
		$T="";
		if (is_array($Props)) foreach ($Props as $p){
			//Verification de la valeur
			switch ($p["Type"]) {
				case "titre":
				case "text":
				case "varchar":
					//Recuperation des mots clefs
					$T .= $this->$p["Titre"];
					//Insertion dans la base de donnee
					
				break;
			}
		}
		//Extraction des mots clefs
		$params['content'] = $T; //page content
		//set the length of keywords you like
		$params['min_word_length'] = 3;  //minimum length of single words
		$params['min_word_occur'] = 1;  //minimum occur of single words
		
		$params['min_2words_length'] = 3;  //minimum length of words for 2 word phrases
		$params['min_2words_phrase_length'] = 10; //minimum length of 2 word phrases
		$params['min_2words_phrase_occur'] = 2; //minimum occur of 2 words phrase
		
		$params['min_3words_length'] = 3;  //minimum length of words for 3 word phrases
		$params['min_3words_phrase_length'] = 10; //minimum length of 3 word phrases
		$params['min_3words_phrase_occur'] = 2; //minimum occur of 3 words phrase
		
		$keyword = new autokeyword($params, "UTF-8");
		/*$Result = "--------------TEXTE---------------<br />";
		$Result .= $T."<br />";
		$Result .= "--------------MOTS CLEFS 1---------------<br />";*/
		$Result = explode(", ",$keyword->parse_words());
		if (is_array($Result))foreach ($Result as $Mc){
			if ($Mc!=""){
				$Tab = Sys::$Modules["Boutique"]->callData("Boutique/BlackList/Titre=".$Mc,"",0,1,"","","COUNT(DISTINCT(m.Id))");
				if ($Tab[0]["COUNT(DISTINCT(m.Id))"]==0) $Out[] = $Mc;
			}
		}
		/*$Result .= "--------------MOTS CLEFS 2---------------<br />";
		$Result .= $keyword->parse_2words()."<br />";
		$Result .= "--------------MOTS CLEFS 3---------------<br />";
		$Result .= $keyword->parse_3words()."<br />";*/
		return $Out;
	}
	function Save(){
		genericClass::Save();
		//Suppression des mots clefs existants.
		$Tab = Sys::$Modules["Boutique"]->callData("Boutique/Produit/".$this->Id."/Motclef",0,1000);
		if (is_array($Tab))foreach ($Tab as $Mc) {
			$McT = new genericClass("Boutique",$Mc);
			$McT->Delete();
		}
		//On enregistre les nouveaux
		$Mcs = $this->genKeyWords();
		if (is_array($Mcs))foreach ($Mcs as $Mc){
			//On verifie d'abord si il n'existe pas dasn la base des mots clefs en tant que canonique
			$Tab2 = Sys::$Modules["Boutique"]->callData("Boutique/Produit/".$this->Id."/Motclef/Canon=".Process::Canonise($Mc),null,0,1,"","","COUNT(DISTINCT(m.Id))");
			if ($Tab2[0]["COUNT(DISTINCT(m.Id))"]==0){
				$Mcf = new genericClass("Boutique","Motclef");
				$Mcf->Set("Nom",$Mc);
				$Mcf->Set("Canon",Process::Canonise($Mc));
				$Mcf->AddParent("Boutique/Produit/".$this->Id);
				$Mcf->Save();
			}
		}
	}
}
?>