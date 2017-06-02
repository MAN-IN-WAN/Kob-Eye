<?
class Article extends genericClass{
	function __construct($Mod,$Tab){
		genericClass::__construct($Mod,$Tab);
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
				$Tab = $GLOBALS["Systeme"]->Modules["Redaction"]->callData("Redaction/BlackList/Titre=".$Mc,"",0,1,"","","COUNT(DISTINCT(m.Id))");
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
		$Tab = $GLOBALS["Systeme"]->Modules["Redaction"]->callData("Redaction/Article/".$this->Id."/Motclef",0,1000);
		if (is_array($Tab))foreach ($Tab as $Mc) {
			$McT = new genericClass("Redaction",$Mc);
			$McT->Delete();
		}
		//On enregistre les nouveaux
		$Mcs = $this->genKeyWords();
		if (is_array($Mcs))foreach ($Mcs as $Mc){
			//On verifie d'abord si il n'existe pas dasn la base des mots clefs en tant que canonique
			$Tab2 = $GLOBALS["Systeme"]->Modules["Redaction"]->callData("Redaction/Article/".$this->Id."/Motclef/Canon=".Utils::Canonic($Mc),null,0,1,"","","COUNT(DISTINCT(m.Id))");
			if ($Tab2[0]["COUNT(DISTINCT(m.Id))"]==0){
				$Mcf = new genericClass("Redaction","Motclef");
				$Mcf->Set("Nom",$Mc);
				$Mcf->Set("Canon",Utils::Canonic($Mc));
				$Mcf->AddParent("Redaction/Article/".$this->Id);
				$Mcf->Save();
			}
		}
	}
}
?>