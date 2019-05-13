<?php

require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');

class PrintVisite extends FPDF {
	
	private $left = 20;
	private $pageWidth;
	private $lineHeight = 6.5;
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
		parent::__construct($mode == 2 ? 'P' : 'L', 'mm', 'A4');
		$this->AcceptPageBreak(true, 12);
		
		$this->mode = $mode;
		$this->head = array(
			array('','N° Carte','Nom','Réglé','Départ','Présent','Observations'),
			array('','N° Carte','Nom','Départ','Présent','Observations'),
			array('','N° Carte','Nom','Prix','Dép.','Téléphone','Téléphone','Mail'));
		$this->width = array(
			array(9,22,83,15,72,18,48),
			array(9,22,98,72,18,48),
			array(5,13,51,7,8,21,21,51,22));
		if($mode == 2) {
			$this->left = 5; 
			$this->lineHeight = 5;
		}
		$this->pageWidth = 0;
		foreach($this->width[$mode] as $w) $this->pageWidth += $w;

		$this->titre = "Visites Guidées CADREF";
	}
	
	private function cv($txt) {
		return iconv('UTF-8','ISO-8859-15//TRANSLIT',$txt);
	}

	function Header() {
		$y = 5;
		$this->SetFont('Arial','B',$mode == 2 ? 12 : 14);
		$this->SetXY($this->left, $y);
		$this->Cell($this->pageWidth, 8, $this->cv($this->titre), 0, 0, 'C');
		$y += 8;
		
		$this->SetFont('Arial','B',$mode == 2 ? 10 : 12);
		$this->SetXY($this->left, $y);
		$this->Cell($this->pageWidth-30, $this->lineHeight, $this->cv($this->record['Libelle']), 'LT', 0, 'L');
		$this->Cell(30, $this->lineHeight, date('d/m/Y', $this->record['DateVisite']), 'LRT', 0, 'R');
		$y += $this->lineHeight;
		$this->SetFont('Arial','',$mode == 2 ? 10 : 12);
		$this->SetXY($this->left, $y);
		$this->Cell(30, $this->lineHeight, $this->record['Visite'], 'LTB', 0, 'L');
		$this->Cell($this->pageWidth-60, $this->lineHeight, $this->cv($this->enseignants), 'LTB', 0, 'L');
		$this->lpage++;
		$this->Cell(30, $this->lineHeight, 'Page '.$this->lpage, 'LRTB', 0, 'R');
		$y += 10;
		
		$this->SetFont('Arial','',$this->mode == 2 ? 9 : 12);
		$this->SetXY($this->left, $y);
		$n = count($this->width[$this->mode]);
		for($i = 0; $i < $n; $i++) {
			$this->Cell($this->width[$this->mode][$i], $this->lineHeight, $this->cv($this->head[$this->mode][$i]), 'RT'.($i == 0 ? 'L' : ''), 0, 'C');
		}
		$y += $this->lineHeight;
		
		$this->posy = $y;
		$this->SetXY($this->left, $this->posy);
	}

	function Footer() {
		$this->SetXY($this->left, $this->posy);
		$this->Cell($this->pageWidth, 0.01, '', 'T');
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
			if(!$l['Supprime'] && !$l['Attente']) $this->total += $l['Montant'];
		}
		if($this->rupture) $this->printTotal();
	}
	
	private function printTotal() {
		if($this->rupture && $this->mode != 1) {
			$this->SetXY($this->left, $this->posy);
			$this->SetFont('Arial','B',$mode == 2 ? 9 :12);
			$this->Cell($this->pageWidth, $this->lineHeight, $this->cv('Total général : ').$this->total, 'LRT');
			$this->posy += $this->lineHeight;
		}		
	}

	private function printLine($l) {
		$mode = $this->mode;
		if($mode == 1 && ($l['Attente'] || $l['Supprime'])) return;
		
		$this->SetXY($this->left, $this->posy);
		$this->compteur++;
		$this->SetFont('Arial','',$mode == 2 ? 9 : 12);
		$this->Cell($this->width[$mode][0], $this->lineHeight, $this->compteur, 'LRT', 0, 'C');
		$this->Cell($this->width[$mode][1], $this->lineHeight, $l['Numero'], 'RT', 0, 'C');
		$this->Cell($this->width[$mode][2], $this->lineHeight, $this->cv($l['Nom'].' '.$l['Prenom']), 'RT', 0, 'L');
		$n = 3;
		if($this->mode != 1) $this->Cell($this->width[$mode][$n++], $this->lineHeight, $l['Montant'], 'RT', 0, 'C');
		$this->SetFont('Arial','',$mode == 2 ? 8 : 10);
		if($this->mode == 2) {
			$this->Cell($this->width[$mode][$n++], $this->lineHeight, $l['Lieu'], 'RT', 0, 'L');
			$this->Cell($this->width[$mode][$n++], $this->lineHeight, $l['Telephone1'], 'RT', 0, 'L');
			$this->Cell($this->width[$mode][$n++], $this->lineHeight, $l['Telephone2'], 'RT', 0, 'L');
			$this->Cell($this->width[$mode][$n++], $this->lineHeight, $l['Mail'], 'RT', 0, 'L');			
		}
		else {
			$this->Cell($this->width[$mode][$n++], $this->lineHeight, $this->cv(trim($l['HeureDepart'].'  '.$l['LibelleL'])), 'RT', 0, 'L');
			$this->Cell($this->width[$mode][$n++], $this->lineHeight, '', 'RT', 0, 'L');
		}
		$s = $l['Notes'];
		if($l['Supprime'] == 1) $s = ($mode == 2 ? 'Sup ' : 'Supprimé ').date('d/m', $l['DateSupprime']);
		else if($l['Supprime'] == 2) $s = ($mode == 2 ? 'Ech ' : 'Echangé ').date('d/m', $l['DateSupprime']);
		else if($l['Attente'] == 1) $s = ($mode == 2 ? 'Att ' : 'Attente ').date('d/m H:i', $l['DateAttente']);
		$this->Cell($this->width[$mode][$n], $this->lineHeight, $this->cv($s), 'RT', 0, 'L');
		$this->posy += $this->lineHeight;
	} 

}
