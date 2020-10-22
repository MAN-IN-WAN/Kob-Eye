<?php

//require_once('Class/Lib/pdfb1/pdfb.php');
require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');

class PrintSuivi extends FPDF {
	
	private $posy;
	private $left = 25;
	private $mode = false;
	
	
	function PrintSuivi() {
		parent::__construct('P', 'mm', 'A4');
	}
	
	private function cv($txt) {
		return iconv('UTF-8','ISO-8859-15//TRANSLIT',$txt);
	}

	function Header() {}
	function Footer() {}
	

	function PrintPage($mode, $adh, $ins, $aan, $annee) {
		$this->mode = $mode == 2;
		
		$this->AddPage();
		$img = getcwd().'/Skins/AngularAdmin/Modules/Cadref/assets/img/cadref_logo_noir.png';
		$this->Image($img,25,19,26,30);
		$this->SetFont('Arial','',10);
		$this->SetXY(20, 51);
		$this->Cell(37, 4, Cadref::$WEB, 0, 1, 'C');
		$this->SetX(20);
		$this->Cell(37, 4, Cadref::$MAIL, 0, 0, 'C');
		
		$this->SetFont('Arial','B',20);
		$this->SetXY(140, 15);
		$this->Cell(50, 10, 'C.A.D.R.E.F.', 0, 1);
		$this->SetFont('Arial','',12);
		$h = 5;
		$this->SetX(140);
		$this->Cell(50, $h, '249 rue de Bouillargues', 0, 1);
		$this->SetX(140);
		$this->Cell(50, $h, $this->cv('30000  NÎMES'), 0, 1);
		$this->SetX(140);
		$this->Cell(50, $h, $this->cv('Téléphone : 04 66 36 99 44'));

		$this->SetFont('Arial','B',18);
		$this->SetXY(0,90);
		$s = "ATTESTATION DE ".($this->mode ? "PAIEMENT" : "SUIVI DE COURS");
		$this->Cell(210, 6, $s, 0, 0, 'C');
		$this->SetFont('Arial','',12);
		$this->SetXY(25,110);
		$s = "Je, soussignée Nathalie Faucher, Directrice du CADREF, atteste ";
		$s .= $this->mode ? "avoir reçu de : \n\n" : "que :\n\n";
		$s .= ($adh->Sexe == "F" ? "Madame " : ($adh->Sexe == "H" ? "Monsieur " : "")).trim($adh->Prenom.' '.$adh->Nom)."\n\n";
		if($this->mode) {
			$cot = $aan->Cotisation;
			$crs = $aan->Cours;
			$reg = $aan->Reglement+$aan->Differe-$aan->Regularisation;
			$tot = $reg;
			$s .= "la somme de $tot Euros correspondant à :\n\n";
			$s .= " -  Cotisation $annee-".($annee+1)." : $cot Euros\n\n";
			$s .= " -  Inscription au".(count($ins) > 1 ? "x" : "")." cours : $crs Euros";
		}
		else {
			$s .= "a suivi".($adh->Sexe == 'F' ? 'e' : '')." au cours de l'année $annee-".($annee+1);
			$s .= count($ins) > 1 ? " les cours suivants :" : " le cours suivant :";
		}
		$this->MultiCell(180, 5, $this->cv($s));
		
		if($this->mode && $aan->Solde > 0) {
			$this->SetFont('Arial','B',12);
			$this->SetXY(0,160);
			$this->Cell(210, 6, $this->cv('SITUATION NON SOLDÉE'), 0, 0, 'C');
			$this->posy = 170;
		}
		else $this->posy = $this->mode ? 160 : 140;
		
		$this->SetFont('Arial','',12);
		foreach($ins as $i) $this->printLine($i);
		
		$this->SetXY(110,297-50);
		$this->Cell(210, 5, $this->cv('Fait à Nîmes, le ').date('d/m/Y'));

	}
	
	private function printLine($l) {
		if($this->mode) $this->SetFont('Arial','I',12);
		else $this->SetFont('Arial','',12);
		
		$this->SetXY($this->left+($this->mode ? 10 : 0), $this->posy);
		$s = ' -  '.$l->LibelleW.' '.$l->LibelleN;
		if($this->mode) $s .= ' : '.($l->Prix-$l->Reduction-$l->Soutien).' Euros';
		$this->Cell(160, 5, $this->cv($s));
		$this->posy += 8;
	} 

}