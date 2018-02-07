<?php
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

class Produit extends genericClass {

    function __construct($refMod = "", $Data = '')
    {
        parent::__construct($refMod, $Data);
        if(is_object($GLOBALS["Systeme"]->Header))
            $GLOBALS["Systeme"]->Header->Add('<meta name="robots" content="noarchive">',"");
    }

    /**
	 * Enregistrement d'un produit
	 * -> Check Reference
	 * -> Enregistre les mots clés
	 * @return	void
	 */
	public function Save( $recurs = true ) {
		parent::Save();
		//$this->SaveKeywords();
	}

	/**
	 * Enregistre les mots-clés pour ce produit
	 * @return	void
	 */
/*	function SaveKeywords() {
		$Mcs = $this->genKeyWords();
		if(is_array($Mcs)) {
			foreach($Mcs as $Mc) {
				//On verifie d'abord si il n'existe pas dans la base des mots clefs en tant que canonique
				$Tab2 = $this->storproc("Catalogue/MotClef/Canon=".Utils::Canonic($Mc));
				if($Tab2[0]) {
					// Il existe déjà, il suffit de lui rattacher un nouveau parent
					$Mcf = genericClass::createInstance('Catalogue', $Tab2[0]);
				}
				else {
					// Il n'existe pas, on le créé
					$Mcf = new genericClass("Catalogue","MotClef");
					$Mcf->Set("Nom",$Mc);
					$Mcf->Set("Canon",Utils::Canonic($Mc));
				}
				$Mcf->AddParent("Catalogue/Produit/".$this->Id);
				$Mcf->Save();
			}
		}
	}*/

	function PrixTTC ($PxHt, $TauxTva) {

		$TTC= $PxHt + (($PxHt *  $TauxTva)/100);
		return $TTC ;
	}
	function CalcCreditImpot ($PxTTC) {
		$Ci= ($PxTTC *  $this->CreditImpot )/100;
		//var_dump($PxTTC,$this->CreditImpot, $Ci);die;
		return $Ci ;
	}


	function explodeCSV( $content ) {
		return explode(PHP_EOL, $content);
	}

	function sendHeader() {
		header("Content-type: application/vnd.ms-excel"); 
		header("Content-disposition: attachment; filename=\"ProduitsAddwords.csv\"");
	}

  	function addTitre($ctc) {
   		echo ";;"  .";"  . $ctc . ";"  . "\r\n";
   		echo ";;;;"  . "\r\n";
		echo "Type appareil;Marque;Référence;Modèle;URL;Date Insertion\r\n" ;
   		echo ";;;;"  . "\r\n";
  	}

  	function addProduit($ctc, $Categorie,$UrlCat, $Fab) {
		$url="http://gaz-service.fr/LeCatalogue/". $UrlCat ."/Produit/" .$ctc->Url;
		$dateCrea= date('d/m/Y', $ctc->tmsCreate);
    		echo $Categorie . ";" . $Fab . ";" . $ctc->References . ";". $ctc->Titre . ";" . $url . ";" . $dateCrea . ";"  . "\r\n";
  	}
	
	function addTotal($ctc, $Categorie='') {
    		echo "\r\n";
			if ($Categorie=='') $Categorie = "de toutes les catégories : ";
    		echo "Total produits " . ";" . $Categorie . ";" . $ctc . "\r\n";
   			echo "\r\n";
   	}

	function rc() {
		echo "\r\n";
	}
	




/**
	 * Génère les keywords à partir de tous les champs textuels
	 * @return	Tableau de mots clés
	 */
	/*private function genKeyWords() {
		// Inclusion de la classe
		include_once("Class/Lib/class.autokeyword.php");

		// Recensement des champs textuels
		$Props = $this->SearchOrder();
		$T="";
		if (is_array($Props)) foreach ($Props as $p) {
			//Verification de la valeur
			switch ($p["Type"]) {
				case "titre":
				case "text":
				case "varchar":
				case "bbcode":
					// Type text : on concatene
					$T .= ' ' . $this->$p["Titre"];
				break;
			}
		}
		//Extraction des mots clefs
		$params['content'] = $T; //page content
		//set the length of keywords you like
		$params['min_word_length'] = 2;  //minimum length of single words
		$params['min_word_occur'] = 1;  //minimum occur of single words
		$params['min_2words_length'] = 3;  //minimum length of words for 2 word phrases
		$params['min_2words_phrase_length'] = 10; //minimum length of 2 word phrases
		$params['min_2words_phrase_occur'] = 2; //minimum occur of 2 words phrase
		$params['min_3words_length'] = 3;  //minimum length of words for 3 word phrases
		$params['min_3words_phrase_length'] = 10; //minimum length of 3 word phrases
		$params['min_3words_phrase_occur'] = 2; //minimum occur of 3 words phrase
		$keyword = new autokeyword($params, "UTF-8");
		$Result = explode(", ",$keyword->parse_words());
		if (is_array($Result))foreach ($Result as $Mc){
			if ($Mc!=""){
				$Tab = $this->storproc("Catalogue/BlackList/Titre=".$Mc,"",0,1,"","","COUNT(DISTINCT(m.Id))");
				if ($Tab[0]["COUNT(DISTINCT(m.Id))"]==0) $Out[] = $Mc;
			}
		}
		return $Out;
	}
*/


	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return Sys::$Modules['Catalogue']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}

	
	


}