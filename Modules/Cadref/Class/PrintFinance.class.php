<?php

require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');

class PrintFinance extends FPDF {
	
	private $left = 12;
	private $head;
	private $align;
	private $width;
	private $posy;
	private $total = faux;
	private $titre;
	private $nbcot;
	private $cotis;
	private $prix = 0;
	private $reduc = 0;
	private $reduc2 = 0;
	private $cumul = 0;
	private $cours = 0;
	private $inscrits = 0;
	
	
	function PrintFinance($nbcot,$cotis) {
		parent::__construct('P', 'mm', 'A4');
		$this->AcceptPageBreak(true, 12);

		$this->nbcot = $nbcot;
		$this->cotis = $cotis;
		$this->head = array('Classe','','Inscrits','Prix','Remise','Soutien','Total');
		$this->width = array(18,75,10,20,20,20,20);
		$this->align = array('L','L','R','R','R','R','R');

		$this->titre = Cadref::$UTL." : Rapport financier ";
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
	
	function PrintTotal() {
		$this->SetFont('Arial','B',10);
		$this->SetXY($this->left, $this->posy+4);
		$this->Cell($this->width[0], 4.5, '');
		$this->Cell($this->width[1], 4.5, 'Totaux', 0, 0, 'R');
		$this->Cell($this->width[2], 4.5, $this->inscrits, 0, 0, 'R');
		$this->Cell($this->width[3], 4.5, $this->prix, 0, 0, 'R');
		$this->Cell($this->width[4], 4.5, $this->reduc, 0, 0, 'R');
		$this->Cell($this->width[5], 4.5, $this->reduc2, 0, 0, 'R');
		$this->Cell($this->width[6], 4.5, $this->cumul, 0, 0, 'R');
		$this->posy += 8;
		$this->total = true;
		$this->SetFont('Arial','',10);
		$this->SetXY($this->left, $this->posy+4);
		$this->Cell(60, 4.5, $this->cv('Nombre de cotisants : ').$this->nbcot);
		$this->Cell(60, 4.5, $this->cv('Montant des cotisations : ').$this->cotis);
		$this->Cell(60, 4.5, $this->cv('Nombre de cours : ').$this->cours);
	}
	
	function PrintLines($regl) {
		$this->SetFont('Arial','',10);
		foreach($regl as $r) $this->printLine($r);
	}

	private function printLine($l) {
		$this->SetXY($this->left, $this->posy);
		$this->Cell($this->width[0], 4.5, $l['CodeClasse']);
		$s = $this->cv(trim($l['libelleD'].' '.$l['libelleN']));
		$this->Cell($this->width[1], 4.5, $s);
		$this->Cell($this->width[2], 4.5, $l['inscrits'], 0, 0, 'R');
		$this->Cell($this->width[3], 4.5, $l['Prix'], 0, 0, 'R');
		$nred = $l['nred'];
		$s = '';
		if($nred) $s = $l['red']." ($nred)";
		$this->Cell($this->width[4], 4.5, $s, 0, 0, 'R');
		$nred2 = $l['nred2'];
		$s = '';
		if($nred2) $s = $l['red2']." ($nred2)";
		$this->Cell($this->width[5], 4.5, $s, 0, 0, 'R');
		$tot = $l['total'] - $l['red'] - $l['red2'];
		$this->Cell($this->width[5], 4.5, $tot, 0, 0, 'R');
		$this->posy += 4.5;
		
		$this->cours++;
		$this->inscrits += $l['inscrits'];
		$this->prix += $l['Prix'];
		$this->reduc += $l['red'];
		$this->reduc2 += $l['red2'];
		$this->cumul += $tot;
	} 

}