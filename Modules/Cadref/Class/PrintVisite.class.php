<?php

require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');

class PrintVisite extends FPDF {
	
	private $left = 20;
	private $head;
	private $align;
	private $width;
	private $posy;
	private $titre;
	private $rupture = 0;
	private $enseignants;
	private $record;
	private $compteur;
	private $lpage = 0;
	private $chauffeur;
	
	
	function PrintVisite($chauffeur) {
		parent::__construct('L', 'mm', 'A4');
		$this->AcceptPageBreak(true, 12);
		
		$this->chauffeur = $chauffeur;
		$this->head = array('','N° Carte','Nom','Réglé','Départ','Présent','Observations');
		$this->width = array(9,22,83,15,72,18,48);
		$this->align = array('C','C','C','C','C','C','C');

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
		$n = count($this->width);
		for($i = 0; $i < $n; $i++) {
			if($this->chauffeur) {
				$w = $this->width[$i];
				if($i == 2) $w += $this->width[3];
				if($i != 3) $this->Cell($w, 6.5, $this->cv($this->head[$i]), 'RT'.($i == 0 ? 'L' : ''), 0, $this->align[$i]);
			}
			else $this->Cell($this->width[$i], 6.5, ($i == 3 && $this->chauffeur) ? '' : $this->cv($this->head[$i]), 'RT'.($i == 0 ? 'L' : ''), 0, $this->align[$i]);
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
		}
	}

	private function printLine($l) {
		$this->SetXY($this->left, $this->posy);
		$this->compteur++;
		$this->SetFont('Arial','',12);
		$this->Cell($this->width[0], 6.5, $this->compteur, 'LRT', 0, 'C');
		$this->Cell($this->width[1], 6.5, $l['Numero'], 'RT', 0, 'C');
		$this->Cell($this->width[2]+($this->chauffeur ? $this->width[3] : 0), 6.5, $this->cv($l['Nom'].' '.$l['Prenom']), 'RT', 0, 'L');
		if(! $this->chauffeur) $this->Cell($this->width[3], 6.5, $l['Montant'], 'RT', 0, 'C');
		$this->SetFont('Arial','',10);
		$this->Cell($this->width[4], 6.5, $this->cv(trim($l['HeureDepart'].'  '.$l['LibelleL'])), 'RT', 0, 'L');
		$this->Cell($this->width[5], 6.5, '', 'RT', 0, 'L');
		$this->Cell($this->width[6], 6.5, $l->Notes, 'RT', 0, 'L');
		$this->posy += 6.5;
	} 

}
