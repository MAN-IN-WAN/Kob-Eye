<?php

//require_once('Class/Lib/pdfb1/pdfb.php');
require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');

class PrintSuivi extends FPDF {
	
	private $recto;
	private $adh;
	private $aan;
	private $width;
	private $align;
	private $head;
	private $posy;
	private $left = 5;
	
	
	function PrintSuivi($adherent, $adhAnnee) {
		parent::__construct('P', 'mm', 'A4');
		$this->adh = $adherent;
		$this->aan = $adhAnnee;
	}
	
	private function cv($txt) {
		return iconv('UTF-8','ISO-8859-15//TRANSLIT',$txt);
	}

	function Header() {
		$annee = Cadref::$Annee;
		$adh = $this->adh;
		$y = 5;
		$img = getcwd().'/Skins/AngularAdmin/Modules/Cadref/assets/img/cadref_logo_noir.png';
		$this->Image($img,25,19,26,30);
		$this->SetFont('Arial','',10);
		$this->SetXY(20, 51);
		$this->Cell(37, 4, 'www.cadref.com', 0, 1, 'C');
		$this->SetX(20);
		$this->Cell(37, 4, 'contact@cadref.com', 0, 0, 'C');
		
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

		$this->SetXY(0,100);
		$this->Cell(50, 6, '4');
		
		
		$this->posy = $y+4;
	}
	
	function Footer() {
	}
	
	function PrintLines($ins) {
		$this->SetFont('Arial','',8);
		foreach($ins as $i) $this->printLine($i);
	}

	private function printLine($l) {
		$cls = $l->getOneParent('Classe');
		$ens = $cls->getParents('Enseignant');
		$lieu = $cls->getOneParent('Lieu');
		$n = '';
		foreach($ens as $e) {
			if($n != '') $n .= ', ';
			$n .= trim($e->Prenom.' '.$e->Nom);
		}
		$this->SetXY($this->left, $this->posy);
		$this->Cell($this->width[0], 5, $l->CodeClasse, 0, 0, $this->align[0]);
		$s = $l->LibelleW.' '.$l->LibelleN;
		if($lieu && $lieu->Libelle) $s .= ' ('.$lieu->Libelle.')';
		$this->Cell($this->width[1], 5, $this->cv($s), 0, 0, $this->align[1]);
		$s = substr($l->Jour, 0, 3).' '.$l->HeureDebut.' '.$l->HeureFin;
		if($l->CycleDebut) $s .= ' ('.$l->CycleDebut.' '.$l->CycleFin.')';
		$this->Cell($this->width[2], 5, $this->cv($s), 0, 0, $this->align[2]);
		$this->Cell($this->width[3], 5, $this->cv($n), 0, 0, $this->align[3]);
		$this->Cell($this->width[4], 5, $l->Prix-$l->Remise1-$l->Remise2, 0, 0, $this->align[4]);
		$this->posy += 4;
	} 

}