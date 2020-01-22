<?php

//require_once('Class/Lib/pdfb1/pdfb.php');
require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');
require_once ('cadrefStat.class.php');

class PrintStatistique extends FPDF {
	
	private $debut;
	private $fin;
	private $left = 10;
	private $statw = 60;
	private $colw = 26;
	private $colv = 10;
	private $colp = 10;
	private $head;
	private $posy = 5;
	private $antennes;
	private $titre;
	private $header = false;
	private $types;
	private $rupture = -1;
	private $totaux;
	private $antCount;
	private $antTot = 0;
	private $insCount;
	private $insTot = 0;
	
	
	function PrintStatistique($debut, $fin, $antennes, $antCount, $insCount) {
		parent::__construct('L', 'mm', 'A4');
		$this->AcceptPageBreak(true, 8);

		$this->debut = $debut;
		$this->fin = $fin;
		$this->antennes = $antennes;
		$this->antCount = $antCount;
		$this->insCount = $insCount;
		
		foreach($antCount as $a) $this->antTot += $a;
		foreach($insCount as $a) $this->insTot += $a;

		$this->types = array('Sexes','Professions','Situation','Origine','Répartition par ages','Nombre de cours','Répartition par communes','Disciplines','Disciplines Web');
		$this->head = array('Alès','Bagnols','Le Grau','Le Vigan','Nîmes','Sommières','Villeneuve');
		$this->titre = Cadref::$UTL." : Statistiques du ".$this->debut." au ".$this->fin;
		
		$this->totaux = array();
		for($i = 0; $i < $this->antennes+1; $i++) $this->totaux[$i] = 0;
		$this->totaux[$i] = 0;
	}
	
	private function cv($txt) {
		return iconv('UTF-8','ISO-8859-15//TRANSLIT',$txt);
	}

	function Header() {
		$y = 5;
		$this->SetFont('Arial','B',10);
		$this->SetXY($this->left, $y);
		$this->Cell(30, 4, date('d/m/Y H:i'));
		$this->SetXY(-40, $y);
		$this->Cell(35, 4, 'Page '.$this->PageNo(), 0, 0, 'R');
		$y += 5;
		$this->SetFont('Arial','B',12);
		$this->SetXY($this->left, $y);
		$this->Cell(277, 4, $this->cv($this->titre), 0, 0, 'C');
		$this->posy = $y+8;
		$this->SetXY($this->left, $this->posy);

		if($this->rupture != -1) {
			$this->statHeader();
			$this->header  = true;
		}
	}
	
	private function statHeader() {
		$this->SetXY($this->left, $this->posy);
		if($this->header) return;
		
		$this->SetFont('Arial','BI',8);
		$this->SetXY($this->left, $this->posy);
		$this->Cell($this->statw, 5, $this->cv($this->types[$this->rupture]), 0, 0, 'L');
		for($i = 0; $i < $this->antennes; $i++)
			$this->Cell($this->colw, 5, $this->cv($this->head[$i]), 0, 0, 'L');
		$this->Cell($this->colw, 5, 'Cumuls', 0, 0, 'L');
	
		$this->posy += 6;
		$this->SetXY($this->left, $this->posy);
	}

	function Footer() {
		$this->SetXY($this->left, $this->posy);
	}
	
	function PrintLines($stats) {
		$this->rupture = -1;
		foreach($stats->Stats as $stat) {
			if($this->rupture != $stat->Type) {
				if($this->rupture != -1) $this->printTotaux();
				
				$this->rupture = $stat->Type;
				for($i = 0; $i < $this->antennes; $i++) $this->totaux[$i] = 0;

				$this->statHeader();
				$this->header = false;
			}
			$this->SetFont('Arial','',8);
			$this->SetXY($this->left, $this->posy);
			$this->Cell($this->statw, 4, $this->cv($stat->Libelle), 0, 0, 'L');
			$x = $this->left + $this->statw;
			for($i = 0; $i < $this->antennes; $i++) {
				$this->SetXY($x, $this->posy);
				$x += $this->colw;
				$v = $stat->Valeurs[$i];
				$t = $stat->Type >= 7 ? $this->insCount[$i] : $this->antCount[$i];
				$this->Cell($this->colv, 4, $v ? $v : '-', 0, 0, 'R');
				$this->Cell($this->colp, 4, $v && $t ? round($v / $t * 100).'%' : '-', 0, 0, 'R');	
				$this->totaux[$i] += $v;
			}
			$this->totaux[$i] += $stat->Total;
			$this->SetXY($x, $this->posy);
			$v = $stat->Total;
			$t = $stat->Type >= 7 ? $this->insTot : $this->antTot;
			$this->Cell($this->colv, 4, $v, 0, 0, 'R');
			$this->Cell($this->colp, 4, $v && $t ? round($v / $t * 100).'%' : '-', 0, 0, 'R');
			$this->posy += 4;
		}
		if($this->rupture != -1) $this->printTotaux();
	}

	private function printTotaux() {
		$this->SetFont('Arial','B',8);
		$this->posy += 2;
		$this->SetXY($this->left, $this->posy);
		$this->Cell($this->statw-10, 4, 'Totaux', 0, 0, 'R');
		$x = $this->left + $this->statw;
		for($i = 0; $i < $this->antennes; $i++) {
			$this->SetXY($x, $this->posy);
			$x += $this->colw;
			$this->Cell($this->colv, 4, $this->totaux[$i], 0, 0, 'R');
			$this->Cell($this->colp, 4, round($this->totaux[$i] / $this->totaux[$this->antennes] * 100).'%', 0, 0, 'R');				
		}
		$this->SetXY($x, $this->posy);
		$this->Cell($this->colv, 4, $this->totaux[$this->antennes], 0, 0, 'R');
		$this->posy += 4;
		$this->posy += 6;

		$this->totaux = array();
		for($i = 0; $i < $this->antennes+1; $i++) $this->totaux[$i] = 0;
		$this->totaux[$i] = 0;
	}
	
}