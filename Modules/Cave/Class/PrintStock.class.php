<?php

require_once('Class/Lib/pdfb1/pdfb.php');

class PrintStock extends PDFB {
	
	private $date;
	private $width;
	private $align;
	private $total;
	private $couleur;
	private $totcoul;
	private $posy;
	private $left = 5;
	
	
	function PrintStock($date,$orientation='P',$unit='mm',$format='A4') {
		parent::__construct($orientation,$unit,$format);
		$this->AcceptPageBreak(true, 12);
		$this->date = $date;
	}
	
	function Header() {
		$this->SetFillColor(192,192,192);
		$header = array('Catégorie','Couleur','Volume','Cuves');
		$this->width = array(32,15,20,135);
		$this->align = array('L','L','R','L');
		$y = 5;
		$this->SetFont('Arial','B',8);
		$this->SetXY($this->left, $y);
		$this->Cell(100, 6, "TRANSVIN            Stock au ".date('d/m/y', $this->date));
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
		$p = false;
		$cat = '';
		$col = '';
		$qte = 0;
		$cvs = '';
		foreach($lines as $l) {
			if($cat != $l->Categorie || $col != $l->Couleur) {
				if($p) {
					if($this->couleur != $col) {
						if($this->couleur) $this->totalCouleur();
						$this->couleur = $col;
						$this->totcoul = 0;
					}
					$this->printLine($cat, $col, $qte, trim($cvs));
					$qte = 0;
					$cvs = '';
				}
			}
			$p = true;
			$cat = $l->Categorie;
			$col = $l->Couleur;
			$qte += $l->Volume;
			$cvs .= $l->Cuve.' ';
		}
		if($p) {
			$this->printLine($cat, $col, $qte, trim($cvs));
			$this->totalCouleur();
		}
	}


	private function totalCouleur() {
		$this->SetFillColor(192,192,192);
		$this->SetXY($this->left, $this->posy);
		$this->Cell($this->width[0], 5, '', 'LR', 0, $this->align[0], true);
		$this->Cell($this->width[1], 5, $this->couleur, 'R', 0, $this->align[1], true);
		$this->Cell($this->width[2], 5, number_format($this->totcoul,3,',',' '), 'R', 0, $this->align[2], true);
		$this->Cell($this->width[3], 5, '', 'R', 0, $this->align[3], true);
		$this->posy += 5;
	}

	private function printLine($cat, $col, $qte, $cvs) {
		$this->SetXY($this->left, $this->posy);
		$this->Cell($this->width[0], 5, $cat, 'LR', 0, $this->align[0]);
		$this->Cell($this->width[1], 5, $col, 'R', 0, $this->align[1]);
		$this->Cell($this->width[2], 5, $qte ? number_format($qte,3,',',' ') : "", 'R', 0, $this->align[2]);
		$this->Cell($this->width[3], 5, $cvs, 'R', 0, $this->align[3]);
		$this->posy += 5;
		$this->totcoul += $qte;
		$this->total += $qte;
	} 

	function PrintTotal() {
		$this->SetFillColor(192,192,192);
		$this->SetXY($this->left, $this->posy);
		$this->Cell($this->width[0], 5, '', 'T', 0, $this->align[0]);
		$this->Cell($this->width[1], 5, '', 'T', 0, $this->align[1]);
		$this->Cell($this->width[2], 5, number_format($this->total,3,',',' '), 1, 0, $this->align[2]);
		$this->total = true;
	}

}