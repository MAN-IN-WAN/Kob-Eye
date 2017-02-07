<?php

class Analyse extends genericClass {

	public function Save() {
		parent::Save();
		$this->SaveKeywords();
		parent::Save();
	}

	/**
	 * Enregistre les mots-clés
	 * @return	void
	 */
	function SaveKeywords() {
		$Mcs = $this->genKeyWords();
		if(is_array($Mcs)) {
			foreach($Mcs as $Mc) {
				//On verifie d'abord si il n'existe pas dans la base des mots clefs en tant que canonique
				$Tab2 = $this->storproc("Unibio/MotClef/Canon=".Utils::Canonic($Mc));
				if($Tab2[0]) {
					// Il existe déjà, il suffit de lui rattacher un nouveau parent
					$Mcf = genericClass::createInstance('Unibio', $Tab2[0]);
				}
				else {
					// Il n'existe pas, on le créé
					$Mcf = new genericClass("Unibio","MotClef");
					$Mcf->Set("Nom",$Mc);
					$canon = Utils::Canonic($Mc);
					if(empty($canon)) $canon = $Mc;
					$Mcf->Set("Canon",$canon);
				}
				$Mcf->AddParent("Unibio/Analyse/".$this->Id);
				$Mcf->Save();
			}
		}
	}


	function sendHeader() {
		header("Content-type: text/html; charset=UTF-8");
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: attachment; filename=\"unibio.csv\"");

	}

	function sendHeaderV2( ) {

		include 'Class/Lib/PHPExcel.php';
		include 'Class/Lib/PHPExcel/Writer/Excel2007.php';
		
		$this->objPHPExcel = new PHPExcel();
		$this->objPHPExcel->getProperties()->setCreator("Unibio");
		
		$this->objPHPExcel->getProperties()->setTitle("Liste des examens");
		
		$this->objPHPExcel->setActiveSheetIndex(0);
		$this->objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
		$this->objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Export des examens');
		$this->objPHPExcel->getActiveSheet()->setTitle('Export');
		$this->objWriter = new PHPExcel_Writer_Excel2007($this->objPHPExcel);
	
	}

	function sendHeaderClose( ) {
		$this->objWriter->save("Home/Pdf/ExportUnibio.xls");
	}

	function sendHeaderEntete( $champs) {
		
		$gras=array('font' => array('bold' => true));
		$center=array('alignment'=>array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER));
		//pour aligner à gauche
		$left=array('alignment'=>array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT));
		//pour souligner
		$souligner=array('font' => array('underline' => PHPExcel_Style_Font::UNDERLINE_DOUBLE));

		$array_Cell= "A;B;C;D;E;F;G;H;I;J;K;L;M;N;O;P;Q;R;S;T;U;V;W;X;Y;Z";
		$Cells = explode(';', $array_Cell);
		$array_val = explode('|$|', $champs);
		//var_dump(count($array_val) - 1);die;
		$j=0;$z=-1;
		for($i=0; $i<count($array_val) - 1; $i++) {
			if ($z!=-1) $Col=$Cells[$z].$Cells[$j];
			if ($z==-1) $Col=$Cells[$j];
			if ($j<25) { $j++; } else { $j=0; }
			if ($j==0) $z++;
			$Cellule = $Col;
			$Cellule.= "3";
			$this->objPHPExcel->getActiveSheet()->getColumnDimension($Col)->setAutoSize(true);
			$this->objPHPExcel->getActiveSheet()->getStyle($Cellule)->getFont()->setBold(true);
			$this->objPHPExcel->getActiveSheet()->getStyle($Cellule)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->objPHPExcel->getActiveSheet()->SetCellValue($Cellule, $array_val[$i]); 
		}
	}

	function sendHeaderDetail( $champs, $ligne) {

		$array_Cell= "A;B;C;D;E;F;G;H;I;J;K;L;M;N;O;P;Q;R;S;T;U;V;W;X;Y;Z";
		$Cells = explode(';', $array_Cell);
		$array_val = explode('|$|', $champs);
		//var_dump(count($array_val) - 1);die;
		$j=0;$z=-1;
		for($i=0; $i<count($array_val) - 1; $i++) {
			if ($z!=-1)  $Col=$Cells[$z].$Cells[$j];
			if ($z==-1) $Col=$Cells[$j];
			if ($j<25) { $j++; } else { $j=0; }
			if ($j==0) $z++;
			$Cellule = $Col;
			$Cellule.= $ligne;
			$this->objPHPExcel->getActiveSheet()->getColumnDimension($Col)->setAutoSize(true);
			$this->objPHPExcel->getActiveSheet()->SetCellValue($Cellule, $array_val[$i]); 
		}
		$this->objPHPExcel->getActiveSheet()->calculateColumnWidths();
			
	}

	function renvoietime ($jour,$mois) {
		
		return mktime(0, 0, 0, $mois , $jour, date('Y'));
	}
	function renvoiedate () {
		
		return date('d/m/Y');
	}

	function addLigne($ctc) {
		
    		echo  $ctc  . "\r\n";
  	}
 


	/**
	 * Génère les keywords à partir de tous les champs textuels
	 * @return	Tableau de mots clés
	 */
	private function genKeyWords() {
		// Inclusion de la classe
		include_once("Class/Lib/class.autokeyword.php");
		// Recensement des champs textuels
		$T="";
		$Props = $this->SearchOrder();
		foreach ($Props as $p) $T .= ' ' . $this->{$p["Titre"]};
		//Extraction des mots clefs
		$params['content'] = $T; //page content
		//set the length of keywords you like
		$params['min_word_length'] = 1;  //minimum length of single words
		$params['min_word_occur'] = 1;  //minimum occur of single words
		$params['min_2words_length'] = 1;  //minimum length of words for 2 word phrases
		$params['min_2words_phrase_length'] = 1; //minimum length of 2 word phrases
		$params['min_2words_phrase_occur'] = 1; //minimum occur of 2 words phrase
		$params['min_3words_length'] = 1;  //minimum length of words for 3 word phrases
		$params['min_3words_phrase_length'] = 1; //minimum length of 3 word phrases
		$params['min_3words_phrase_occur'] = 1; //minimum occur of 3 words phrase
		$keyword = new autokeyword($params, "UTF-8");
		$Result = explode(", ",$keyword->parse_words());
		$Out = array();
		if (is_array($Result)) foreach ($Result as $Mc) if ($Mc!="") $Out[] = $Mc;
		return $Out;
	}	







	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return Sys::$Modules['Unibio']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}

}