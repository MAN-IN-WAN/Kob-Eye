<?php

//require_once('Class/Lib/pdfb1/pdfb.php');
require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');

class PrintClasse extends FPDF {
	
	private $left = 5;
	private $head;
	private $align;
	private $width;
	private $posy;
	private $titre;
	
	
	function PrintClasse($annee) {
		parent::__construct('P', 'mm', 'A4');
		$this->AcceptPageBreak(true, 12);

		$this->head = array('Code','Libellé','Jour','Heures');
		$this->width = array(22,90,20,30);
		$this->align = array('L','L','L','L');

		$this->titre = "CADREF : Liste des classes $annee-".($annee+1);
	}
	
	private function cv($txt) {
		return iconv('UTF-8','ISO-8859-15//TRANSLIT',$txt);
	}

	function Header() {
		$y = 5;
		$this->SetFont('Arial','B',10);
		$this->SetXY($this->left, $y);
		$this->Cell(30, 4.5, date('d/m/Y H:i'));
		$this->SetXY(-40, $y);
		$this->Cell(35, 4.5, 'Page '.$this->PageNo(), 0, 0, 'R');
		$y += 5;
		$this->SetFont('Arial','B',12);
		$this->SetXY($this->left, $y);
		$this->Cell(200, 4.5, $this->cv($this->titre), 0, 0, 'C');
		$y += 8;
		
		$this->SetXY($this->left, $y);
		$this->SetFont('Arial','BU',10);
		$n = count($this->head);
		for($i = 0; $i < $n; $i++)
			$this->Cell($this->width[$i], 5, $this->cv($this->head[$i]), 0, 0, $this->align[$i]);
	
		$this->posy = $y+6;
		$this->SetXY($this->left, $this->posy);
	}

	function Footer() {
		$this->SetXY($this->left, $this->posy);
	}
	
	
	function PrintLines($cls) {
		$this->SetFont('Arial','',10);
		foreach($cls as $r) $this->printLine($r);
	}

	private function printLine($l) {
		$this->SetXY($this->left, $this->posy);
		$this->Cell($this->width[0], 4.5, $l['CodeClasse']);
		$this->Cell($this->width[1], 4.5, $this->cv($l['LibelleD'].' '.$l['LibelleN']));
		$this->Cell($this->width[2], 4.5, $this->cv($l['Jour']));
		$this->Cell($this->width[3], 4.5, $l['HeureDebut'].' - '.$l['HeureFin']);
		$this->posy += 4.5;
	} 

}
