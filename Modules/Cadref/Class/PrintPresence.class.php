<?php

require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');

class PrintPresence extends FPDF {
	
	private $debut;
	private $fin;
	private $mois;
	private $left = 5;
	private $head;
	private $align;
	private $width;
	private $posy;
	private $titre;
	private $rupture = 0;
	private $enseignants;
	private $cycle;
	private $record;
	private $compteur;
	private $lpage = 0;
	
	
	function PrintPresence($debut, $fin, $mois) {
		parent::__construct('P', 'mm', 'A4');
		$this->AcceptPageBreak(true, 12);

		$this->head = array('','N° Carte','Nom','','','','','');
		$this->width = array(12,22,116,10,10,10,10,10);
		$this->align = array('L','L','L','R','R','R','L','L');

		$this->debut = $debut;
		$this->fin = $fin;
		$this->mois = $mois;

		$this->titre = "Fiche de présence CADREF";
	}
	
	private function cv($txt) {
		return iconv('UTF-8','ISO-8859-15//TRANSLIT',$txt);
	}

	function Header() {
		$y = 5;
		$this->SetFont('Arial','BU',14);
		$this->SetXY($this->left, $y);
		$this->Cell(200, 8, $this->cv($this->titre), 0, 0, 'C');
		$y += 8;
		
		$this->SetFont('Arial','',12);
		$this->SetXY($this->left, $y);
		$this->Cell(45, 6.5, $this->cv($this->record['LibelleA']), 'LT', 0, 'L');
		$this->Cell(117, 6.5, $this->cv($this->record['LibelleD'].' '.$this->record['LibelleN']), 'T', 0, 'L');
		$this->Cell(38, 6.5, $this->cv($this->record['Jour'].' '.$this->record['HeureDebut'].' '.$this->record['HeureFin']), 'RT', 0, 'R');
		$y += 6.5;
		$this->SetXY($this->left, $y);
		$this->Cell(200, 6.5, $this->cv($this->enseignants), 'LRT', 0, 'L');
		$y += 6.5;
		$this->SetXY($this->left, $y);
		$this->Cell(45, 6.5, 'Code : '.$this->record['CodeClasse'], 'LTB', 0, 'L');
		$this->Cell(90, 6.5, $this->cycle, 'LTB', 0, 'L');
		$this->lpage++;
		$this->Cell(65, 6.5, 'Page '.$this->lpage, 'RTB', 0, 'R');
		$y += 10.5;
		
		$this->SetXY($this->left, $y);
		$this->Cell(12, 6.5, '', 'LT', 0, 'L');
		$this->Cell(22, 6.5, '', 'LT', 0, 'L');
		$this->Cell(116, 6.5, '', 'LT', 0, 'L');
		$this->Cell(50, 6.5, $this->cv($this->mois), 'TR', 0, 'C');
		$y += 6.5;
		$this->SetXY($this->left, $y);
		$this->Cell(12, 6.5, '', 'L', 0, 'L');
		$this->Cell(22, 6.5, $this->cv('N° Carte'), 'L', 0, 'C');
		$this->Cell(116, 6.5, 'Nom', 'L', 0, 'C');
		$this->Cell(10, 6.5, '', 'LT');
		$this->Cell(10, 6.5, '', 'LT');
		$this->Cell(10, 6.5, '', 'LT');
		$this->Cell(10, 6.5, '', 'LT');
		$this->Cell(10, 6.5, '', 'LTR');
		$y += 6.5;
		
		$this->posy = $y;
		$this->SetXY($this->left, $this->posy);
	}

	function Footer() {
		$this->SetXY($this->left, $this->posy);
		$this->Cell(200, 0.01, '', 'T');
	}
	
	function PrintLines($list) {
		$this->SetFont('Arial','',12);
		foreach($list as $l) {
			$c = $l['ClasseId'];
			if($this->rupture != $c) {
				$cls = Sys::getOneData('Cadref', 'Classe/'.$c);
				$es = $cls->getParents('Enseignant');
				$s = '';
				foreach($es as $e) {
					if($s != '') $s .= ", ";
					$s .= $e->Prenom.' '.$e->Nom;
				}
				$this->enseignants = $s;
				$this->record = $l;
				$this->compteur = 0;
				$this->rupture = $c;
				$this->lpage = 0;
				$this->cycle = '';
				if($l['CycleDebut']) {
					$this->cycle = 'Cours du '.$l['CycleDebut'].' au '.$l['CycleFin'];
					if($l['Seances']) $this->cycle .= $this->cv('   '.$l['Seances'].' séances');
				}

				$this->AddPage();
			}
			$this->printLine($l);
		}
	}

	private function printLine($l) {
		$this->SetXY($this->left, $this->posy);
		$this->compteur++;
		$this->Cell(12, 6.5, $this->compteur, 'LT', 0, 'C');
		$this->Cell(22, 6.5, $l['Numero'], 'LT', 0, 'C');
		$this->Cell(116, 6.5, $this->cv($l['Nom'].' '.$l['Prenom']), 'LT', 0, 'L');
		$this->Cell(10, 6.5, '', 'LT');
		$this->Cell(10, 6.5, '', 'LT');
		$this->Cell(10, 6.5, '', 'LT');
		$this->Cell(10, 6.5, '', 'LT');
		$this->Cell(10, 6.5, '', 'LTR');
		$this->posy += 6.5;
	} 

}