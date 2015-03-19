<?php

require_once('Class/Lib/pdfb1/pdfb.php');

class PrintPlanning extends PDFB {
	
	private $oper;
	private $deb;
	private $fin;
	private $posy;
	private $left = 8;
	private $lines;
	
	
	function PrintPlanning($lines,$deb,$fin,$orientation='P',$unit='mm',$format='A4') {
		parent::__construct($orientation,$unit,$format);
		$this->AcceptPageBreak(true, 6);
		$this->lines = $lines;
		$this->deb = $deb;
		$this->fin = $fin;
		$this->oper = $lines[0]->Operateur;
	}
	
	function Header() {
		$this->SetFillColor(192,192,192);
		$y = 10;
		$this->SetFont('Arial','B',8);
		$this->SetXY($this->left, $y);
		$this->Cell(194, 8, '', 1, '', '', 1);
		$this->SetXY($this->left, $y);
		$this->Cell(100, 8, "Nom : ".$this->oper);
		$this->SetXY(-110, $y);
		$this->Cell(100, 8, 'Planning du  '.date('d/m/y',$this->deb).'  au  '.date('d/m/y',$this->fin), 0, '', 'R');
		$this->posy = $y + 8;
		$this->SetXY($this->left, $this->posy);
	}
	
	
	function PrintLines() {
		foreach($this->lines as $l) {
			if($this->oper != $l->Operateur) {
				$this->oper = $l->Operateur;
				$this->AddPage();
			}
			$this->printLine($l);
		}
	}

	private function printLine($l) {
		$t = Sys::getData('Cave', 'Tache/'.$l->Id);

		$this->SetXY($this->left, $this->posy);
		$this->Cell(194, 20, '', 1);
		$this->SetXY($this->left, $this->posy);
		$this->SetFont('Arial','B',8);
		$this->Cell(25, 5, date('d/m/y',$l->Date).($l->Heure == 'M' ? '  Matin' : '  Aprè-midi'));
		if($l->Cuve) {
			$this->SetFont('Arial','I',8);
			$this->Cell(24, 5, ($l->AutreCuve ? 'Cuve origine : ' : 'Cuve : ').$l->Cuve);
			if($l->AutreCuve) $this->Cell(20, 5, 'Destination : '.$l->AutreCuve);
		}
		$this->SetFont('Arial','',8);
		$this->SetXY($this->left, $this->posy + 4.5);
		$this->MultiCell(74, 3.6, $l->Description);

		$this->SetXY($this->left + 74, $this->posy);
		$this->Cell(0, 20, '', 'L');
		$this->SetXY($this->left + 74, $this->posy);
		$this->SetFont('Arial','B',8);
		$this->Cell(20, 5, 'Observations');
		$this->SetFont('Arial','',8);
		$this->SetXY($this->left + 74, $this->posy + 4.5);
		$this->MultiCell(60, 3.6, $t[0]->Observations);

		$this->SetXY($this->left + 134, $this->posy);
		$this->Cell(0, 20, '', 'L');
		$this->SetXY($this->left + 134, $this->posy);
		$this->SetFont('Arial','B',8);
		$this->Cell(19, 5, 'Réalisation');
		$this->SetFont('Arial','I',8);
		$this->Cell(30, 5, 'Date, Heure et Commentaires');

		$this->posy += 20;
	} 


}