<?php

require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');

class PrintVisite extends FPDF {
	
	private $left = 20;
	private $head;
	private $width;
	private $posy;
	private $titre;
	private $rupture = 0;
	private $enseignants;
	private $record;
	private $compteur;
	private $lpage = 0;
	private $mode;
	private $total = 0;
	
	
	function PrintVisite($mode) {
		parent::__construct('L', 'mm', 'A4');
		$this->AcceptPageBreak(true, 12);
		
		$this->mode = $mode;
		$this->head = array(
			array('','N° Carte','Nom','Réglé','Départ','Présent','Observations'),
			array('','N° Carte','Nom','Départ','Présent','Observations'),
			array('','N° Carte','Nom','Réglé','Dép.','Téléphone','Téléphone','Mail'));
		$this->width = array(
			array(9,22,83,15,72,18,48),
			array(9,22,98,72,18,48),
			array(9,22,83,15,10,28,28,72));

		$this->titre = "Visites Guidées CADREF";
	}
	
	private function cv($txt) {
		return iconv('UTF-8','ISO-8859-15//TRANSLIT',$txt);
	}

	function Header() {
		$y = 5;
		$this->SetFont('Arial','B',14);
		$this->SetXY($this->left, $y);
		$this->Cell(267, 8, $this->cv($this->titre), 0, 0, 'C');
		$y += 8;
		
		$this->SetFont('Arial','B',12);
		$this->SetXY($this->left, $y);
		$this->Cell(237, 6.5, $this->cv($this->record['Libelle']), 'LT', 0, 'L');
		$this->Cell(30, 6.5, date('d/m/Y', $this->record['DateVisite']), 'LRT', 0, 'R');
		$y += 6.5;
		$this->SetFont('Arial','',12);
		$this->SetXY($this->left, $y);
		$this->Cell(30, 6.5, $this->record['Visite'], 'LTB', 0, 'L');
		$this->Cell(207, 6.5, $this->cv($this->enseignants), 'LTB', 0, 'L');
		$this->lpage++;
		$this->Cell(30, 6.5, 'Page '.$this->lpage, 'LRTB', 0, 'R');
		$y += 10;
		
		$this->SetFont('Arial','',12);
		$this->SetXY($this->left, $y);
		$n = count($this->width[$this->mode]);
		for($i = 0; $i < $n; $i++) {
			$this->Cell($this->width[$this->mode][$i], 6.5, $this->cv($this->head[$this->mode][$i]), 'RT'.($i == 0 ? 'L' : ''), 0, 'C');
		}
		$y += 6.5;
		
		$this->posy = $y;
		$this->SetXY($this->left, $this->posy);
	}

	function Footer() {
		$this->SetXY($this->left, $this->posy);
		$this->Cell(267, 0.01, '', 'T');
	}
	
	function PrintLines($list) {
		foreach($list as $l) {
			$c = $l['VisiteId'];
			if($this->rupture != $c) {
				$this->printTotal();
				$this->total = 0;
				$cls = Sys::getOneData('Cadref', 'Visite/'.$c);
				$es = $cls->getParents('Enseignant');
				$s = '';
				foreach($es as $e) {
					if($s != '') $s .= ", ";
					$s .= trim($e->Prenom.' '.$e->Nom);
				}
				$this->enseignants = $s;
				$this->record = $l;
				$this->compteur = 0;
				$this->rupture = $c;
				$this->lpage = 0;

				$this->AddPage();
			}
			$this->printLine($l);
			$this->total += $l['Montant'];
		}
		if($this->rupture) $this->printTotal();
	}
	
	private function printTotal() {
		if($this->rupture && $this->mode != 1) {
			$this->SetXY($this->left, $this->posy);
			$this->SetFont('Arial','B',12);
			$this->Cell(267, 6.5, $this->cv('Total général : ').$this->total, 'LRT');
			$this->posy += 6.5;
		}		
	}

	private function printLine($l) {
		$mode = $this->mode;
		$this->SetXY($this->left, $this->posy);
		$this->compteur++;
		$this->SetFont('Arial','',12);
		$this->Cell($this->width[$mode][0], 6.5, $this->compteur, 'LRT', 0, 'C');
		$this->Cell($this->width[$mode][1], 6.5, $l['Numero'], 'RT', 0, 'C');
		$this->Cell($this->width[$mode][2], 6.5, $this->cv($l['Nom'].' '.$l['Prenom']), 'RT', 0, 'L');
		$n = 3;
		if($this->mode != 1) $this->Cell($this->width[$mode][$n++], 6.5, $l['Montant'], 'RT', 0, 'C');
		$this->SetFont('Arial','',10);
		if($this->mode == 2) {
			$this->Cell($this->width[$mode][$n++], 6.5, $l['Lieu'], 'RT', 0, 'L');
			$this->Cell($this->width[$mode][$n++], 6.5, $l['Telephone1'], 'RT', 0, 'L');
			$this->Cell($this->width[$mode][$n++], 6.5, $l['Telephone2'], 'RT', 0, 'L');
			$this->Cell($this->width[$mode][$n++], 6.5, $l['Mail'], 'RT', 0, 'L');			
		}
		else {
			$this->Cell($this->width[$mode][$n++], 6.5, $this->cv(trim($l['HeureDepart'].'  '.$l['LibelleL'])), 'RT', 0, 'L');
			$this->Cell($this->width[$mode][$n++], 6.5, '', 'RT', 0, 'L');
			$this->Cell($this->width[$mode][$n], 6.5, $l->Notes, 'RT', 0, 'L');
		}
		$this->posy += 6.5;
	} 

}
