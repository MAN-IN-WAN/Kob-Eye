<?php

//require_once('Class/Lib/pdfb1/pdfb.php');
require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');

class PrintReglement extends FPDF {
	
	private $user;
	private $debut;
	private $fin;
	private $left = 5;
	private $head;
	private $align;
	private $width;
	private $posy;
	private $rupture = '';
	private $totrup = 0;
	private $totgen = 0;
	private $numrup = 0;
	private $numgen = 0;
	private $total = faux;
	private $mode = 0;  // 0:reglement, 1:differes, 2:non encaisses
	private $titre;
	private $type;
	private $modes = ['B'=>'Chèques','E'=>'Espèces','C'=>'Cartes','P'=>'Prélèvements','V'=>'Virements','A'=>'Chèques vacances','X'=>'Non affectés','W'=>'Web'];
	
	
	function PrintReglement($mode, $type, $user, $debut, $fin) {
		parent::__construct('P', 'mm', 'A4');
		$this->AcceptPageBreak(true, 12);

		$this->head = array('Util','Date','Montant','','Adhérent','');
		$this->width = array(10,18,19,1,110,40);
		$this->align = array('L','L','R','L','L','L');

		$this->mode = $mode;
		$this->user = $user;
		$this->debut = $debut;
		$this->fin = $fin;
		$this->type = $type;

//		if($this->mode) {  // masque la colonne espèces
//			$this->head[4] = '';
//			$this->width[4] = 0.01;
//		}
		$this->titre = "CADREF : Règlements ";
		switch($this->mode) {
			case 0: $this->titre .= "du ".$this->debut." au ".$this->fin; break;
			case 1: $this->titre .= "différés au ".$this->debut; break;
			case 2: $this->titre .= "différés non encaissés au ".$this->debut; break;
		}
		if($type != 'T') $this->titre .= "  ".$this->modes[$type];
		$this->titre .= "  (".$this->user.")";
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
	
	function PrintTotal($mode) {
		$this->SetFont('Arial','B',10);
		$this->SetXY($this->left, $this->posy+($mode ? 4 : 0));
		$this->Cell($this->width[0], 4.5, $mode ? $this->numgen : $this->numrup, 0, 0, 'R');
		$this->Cell($this->width[1], 4.5, '');
		$this->Cell($this->width[2], 4.5, $mode ? $this->totgen : $this->totrup, 0, 0, 'R');
		$this->Cell($this->width[3], 4.5, '');
		if($mode) $t =  "Total Général";
		else $t = "Total ".$this->modes[$this->rupture];
		$this->Cell($this->width[4], 4.5, $this->cv($t));
		$this->SetFont('Arial','',10);
		$this->total = true;
		$this->posy += 6;
	}
	
	function PrintLines($regl) {
		$this->SetFont('Arial','',10);
		foreach($regl as $r) {
			$m = $r['ModeReglement'];
			if($m == '') $m = 'X';
			if($m != $this->rupture) {
				if($this->rupture) $this->PrintTotal(false);
				$this->totrup = 0;
				$this->numrup = 0;
				$this->rupture = $m;

//				if($this->type != 'T') {
//					$this->SetFont('Arial','B',10);
//					$this->SetXY($this->left, $this->posy);
//					$this->Cell(60, 4.5, $this->cv($this->modes[$m]));
//					$this->SetFont('Arial','',10);
//					$this->posy += 4.5;
//				}
			}
			$f = (float)$r['Montant'];
			$this->totrup += $f;
			$this->totgen += $f;
			$this->numrup++;
			$this->numgen++;
			if($this->type != 'T') $this->printLine($r);
		}
		$this->PrintTotal(false);
		$this->PrintTotal(true);
	}

	private function printLine($l) {
		$m = (float)$l['Montant'];
		$this->SetXY($this->left, $this->posy);
		$this->Cell($this->width[0], 4.5, $l['Utilisateur']);
		$this->Cell($this->width[1], 4.5, date('d/m/y', $l['DateReglement']));
		if($l['Differe']) {
			if($l['Encaisse']) $this->SetFont('Arial','I',10);
			else $this->SetFont('Arial','B',10);
		}
		$this->Cell($this->width[2], 4.5, $m, 0, 0, 'R');
		$this->SetFont('Arial','',10);
		$this->Cell($this->width[3], 4.5, '');
		$this->Cell($this->width[4], 4.5, $l['Numero'].'   '.$this->cv($l['Nom'].'  '.$l['Prenom']));
		if($l['ModeReglement'] == 'P') {
			if($l['IBAN'] == '' || $l['BIC'] == '' || !$l['DateRUM']) {
				$this->SetTextColor(255,0,0);
				$this->Cell($this->width[5], 4.5, 'IBAN manquant');
				$this->SetTextColor(0);
			}
//			else {
//				$this->SetFont('Arial','',8);
//				$this->SetFont('Arial','',10);
//			}
		}
		$this->posy += 4.5;
	} 

}