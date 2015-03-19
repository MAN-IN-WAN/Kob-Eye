<?php

require_once('Class/Lib/pdfb1/pdfb.php');

class PrintImageCave extends PDFB {
	
	private $date;
	private $width;
	private $align;
	private $total;
	private $cumul;
	private $capacite;
	private $posy;
	private $left = 8;
	
	
	function PrintImageCave($date,$orientation='P',$unit='mm',$format='A4') {
		parent::__construct($orientation,$unit,$format);
		$this->AcceptPageBreak(true, 12);
		$this->date = $date;
	}
	
	function Header() {
		$this->SetFillColor(192,192,192);
		$header = array('Cuve','Capacité','Volume','Opération','Type','Sous-Type','Vol. op.','Lot','Catégorie','Couleur','Degré');
		$this->width = array(10,17,17,21.5,20,20,15,13,32,15,10);
		$this->align = array('L','R','R','C','L','L','R','L','L','L','R');
		$y = 5;
		$this->SetFont('Arial','B',8);
		$this->SetXY($this->left, $y);
		$this->Cell(100, 6, "TRANSVIN            Image de la Cave au ".date('d/m/y', $this->date));
		$this->SetXY(210-20, $y);
		$this->Cell(30, 6, 'Page '.$this->PageNo(),'R');
		$y += 6;
		$this->SetXY($this->left, $y);
		$this->SetFont('Arial','B',8);
		$n = count($header);
		for($i = 0; $i < $n; $i++)
			$this->Cell($this->width[$i], 6, $header[$i], 1, 0, $this->align[$i], true);
		$this->posy = $y + 6;
		$this->SetXY($this->left, $this->posy);
	}
	
	function Footer() {
		$this->SetXY($this->left, $this->posy);
		if($this->total) return;
		$n = count($this->width);
		for($i = 0; $i < $n; $i++)
			$this->Cell($this->width[$i], 0.1, '', 'T', 0, $this->align[$i]);
	}
	
	function PrintLines($lines) {
		foreach($lines as $l) $this->printLine($l);
	}

	private function printLine($l) {
		$this->SetXY($this->left, $this->posy);
		$this->Cell($this->width[0], 5, $l['Cuve'], 'LR', 0, $this->align[0]);
		$this->Cell($this->width[1], 5, $l['Capacite'] ? number_format($l['Capacite'],3,',',' ') : "", 'R', 0, $this->align[1]);
		$this->Cell($this->width[2], 5, $l['Volume'] ? number_format($l['Volume'],3,',',' ') : "", 'R', 0, $this->align[2]);
		$this->Cell($this->width[3], 5, date('d/m/y H:i',$l['Date']), 'R', 0, $this->align[3]);
		$this->Cell($this->width[4], 5, $l['Type'], 'R', 0, $this->align[4]);
		$this->Cell($this->width[5], 5, $l['SousType'], 'R', 0, $this->align[5]);
		$this->Cell($this->width[6], 5, $l['VolumeOperation'] ? number_format($l['VolumeOperation'],3,',',' ') : "", 'R', 0, $this->align[6]);
		$this->Cell($this->width[7], 5, $l['Lot'], 'R', 0, $this->align[7]);
		$this->Cell($this->width[8], 5, $l['Categorie'], 'R', 0, $this->align[8]);
		$this->Cell($this->width[9], 5, $l['Couleur'], 'R', 0, $this->align[9]);
		$this->Cell($this->width[10], 5, $l['Degre'] ? number_format($l['Degre'],2,',',' ') : "", 'R', 0, $this->align[10]);
		$this->posy += 5;
		$this->cumul += $l['Volume'];
		$this->capacite += $l['Capacite'];
	} 

	function PrintTotal() {
		$this->SetFillColor(192,192,192);
		$this->SetXY($this->left, $this->posy);
		$this->Cell($this->width[0], 5, '', 'T', 0, $this->align[0]);
		$this->Cell($this->width[1], 5, number_format($this->capacite,3,',',' '), 1, 0, $this->align[1],true);
		$this->Cell($this->width[2], 5, number_format($this->cumul,3,',',' '), 1, 0, $this->align[2]);
		$this->Cell($this->width[3], 5, '', 'T', 0, $this->align[3]);
		$this->Cell($this->width[4], 5, '', 'T', 0, $this->align[4]);
		$this->Cell($this->width[5], 5, '', 'T', 0, $this->align[5]);
		$this->Cell($this->width[6], 5, '', 'T', 0, $this->align[6]);
		$this->Cell($this->width[7], 5, '', 'T', 0, $this->align[7]);
		$this->Cell($this->width[8], 5, '', 'T', 0, $this->align[8]);
		$this->Cell($this->width[9], 5, '', 'T', 0, $this->align[9]);
		$this->Cell($this->width[10], 5, '', 'T', 0, $this->align[10]);
		$this->total = true;
	}

}