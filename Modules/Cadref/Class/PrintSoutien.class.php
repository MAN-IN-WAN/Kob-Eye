<?php

require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');

class PrintSoutien extends FPDF {
	
	private $left = 5;
	private $head;
	private $align;
	private $width;
	private $posy;
	private $titre;
	private $nombre = 0;
	private $soutien = 0;
	
	
	function PrintSoutien($annee) {
		parent::__construct('P', 'mm', 'A4');
		$this->AcceptPageBreak(true, 12);

		$this->head = array('Antenne','Nombre','Montant');
		$this->width = array(40,25,25);
		$this->align = array('L','R','R');

		$this->titre = Cadref::$UTL." : Liste des soutiens $annee-".($annee+1);
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
		$this->printTotal();
	}

	private function printLine($l) {
		$this->SetXY($this->left, $this->posy);
		$this->Cell($this->width[0], 4.5, $this->cv($l['Libelle']));
		$this->Cell($this->width[1], 4.5, $l['cnt'], 0,0,'R');
		$this->Cell($this->width[2], 4.5, $l['soutien'],0,0,'R');
		$this->posy += 4.5;
		$this->nombre += $l['cnt'];
		$this->soutien += $l['soutien'];
	} 

	private function printTotal() {
		$this->SetFont('Arial','B',10);
		$this->SetXY($this->left, $this->posy);
		$this->Cell($this->width[0], 4.5, $this->cv('Total général'));
		$this->Cell($this->width[1], 4.5, $this->nombre, 0,0,'R');
		$this->Cell($this->width[2], 4.5, $this->soutien,0,0,'R');
	} 

}
