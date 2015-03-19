<?php

require_once('Class/Lib/pdfb1/pdfb.php');

class JournalVentes extends PDFB {
	
	private $linesWidth;
	private $begin;
	private $finish;
	private $societe;
	private $width;
	private $align;
	private $brut;
	private $net;
	private $mtva;
	private $ttc;
	private $total;
	private $posy;
	
	
	function JournalVentes($begin,$finish,$societe,$orientation='P',$unit='mm',$format='A4') {
		parent::__construct($orientation,$unit,$format);
		$this->begin = $begin;
		$this->finish = $finish;
		$this->societe = $societe;
		$this->AcceptPageBreak(true, 12);
	}
	
	function Header() {
		$this->SetFillColor(192,192,192);
		$header = array('Facture','T','Date','Client','HT Brut','Remise','HT Net','TVA','Montant TVA','Montant TTC');
		$this->width = array(13,3,13,60,20,13,20,9,20,20);
		$this->align = array('L','C','C','L','R','R','R','R','R','R');
		$y = 5;
		$this->SetFont('Arial','B',8);
		$this->SetXY(14, $y);
		$soc = $this->societe == 'L' ? 'LOCANIM' : 'BOPI';
		$this->Cell(100, 6, "Journal des Ventes : Société $soc  du ".date('d/m/y', $this->begin)." au ".date('d/m/y',$this->finish));
		$this->SetXY(210-20, $y);
		$this->Cell(30, 6, 'Page '.$this->PageNo(),'R');
		$y += 6;
		$this->SetXY(14, $y);
		$this->SetFont('Arial','B',8);
		$n = count($header);
		for($i = 0; $i < $n; $i++)
			$this->Cell($this->width[$i], 6, $header[$i], 1, 0, $this->align[$i], true);
		$this->posy = $y + 6;
		$this->SetXY(14, $this->posy);
	}
	
	function Footer() {
		$this->SetXY(14, $this->posy);
		if($this->total) return;
		$n = count($this->width);
		for($i = 0; $i < $n; $i++)
			$this->Cell($this->width[$i], 0.1, '', 'T', 0, $this->align[$i]);
	}
	
	function PrintLines($lines) {
		foreach($lines as $rc) $this->printLine($rc['Id']);
	}

	private function printLine($id) {
		$rec = Sys::$Modules['Devis']->callData("FactureTete/$id");
		$fac = genericClass::createInstance('Devis', $rec[0]);
		$rec = Sys::$Modules['Repertoire']->callData("Tiers/".$fac->ClientId);
		$cli = genericClass::createInstance('Repertoire', $rec[0]);
		$rec = Sys::$Modules['Devis']->callData("TVA/".$fac->CodeTVA);
		$tva = genericClass::createInstance('Devis', $rec[0]);
		$this->SetXY(14, $this->posy);
		$this->Cell($this->width[0], 5, $fac->Reference, 'L', 0, $this->align[0]);
		$this->Cell($this->width[1], 5, $fac->Type, 'L', 0, $this->align[1]);
		$this->Cell($this->width[2], 5, date('d/m/y',$fac->Date), 'L', 0, $this->align[2]);
		$this->Cell($this->width[3], 5, $cli->Intitule, 'L', 0, $this->align[3]);
		$this->Cell($this->width[4], 5, number_format($fac->MontantHTBrut, 2), 'L', 0, $this->align[4]);
		$this->Cell($this->width[5], 5, number_format($fac->RemiseTaux, 2), 'L', 0, $this->align[5]);
		$this->Cell($this->width[6], 5, number_format($fac->MontantHTNet, 2), 'L', 0, $this->align[6]);
		$this->Cell($this->width[7], 5, number_format($tva->Taux, 2), 'L', 0, $this->align[7]);
		$this->Cell($this->width[9], 5, number_format($fac->MontantTVA, 2), 'L', 0, $this->align[8]);
		$this->Cell($this->width[9], 5, number_format($fac->MontantTTC, 2), 'LR', 0, $this->align[9]);
		$this->posy += 5;
		$this->brut += $fac->MontantHTBrut;
		$this->net += $fac->MontantHTNet;
		$this->mtva += $fac->MontantTVA;
		$this->ttc += $fac->MontantTTC;	
	} 

	function PrintTotals() {
		$this->SetXY(14, $this->posy);
		$this->Cell($this->width[0], 5, '', 'T', 0, $this->align[0]);
		$this->Cell($this->width[1], 5, '', 'T', 0, $this->align[1]);
		$this->Cell($this->width[2], 5, '', 'T', 0, $this->align[2]);
		$this->Cell($this->width[3], 5, '', 'T', 0, $this->align[3]);
		$this->Cell($this->width[4], 5, '', 'T', 0, $this->align[4]);
		$this->Cell($this->width[5], 5, '', 'T', 0, $this->align[5]);
		$this->Cell($this->width[6], 5, number_format($this->net, 2), 1, 0, $this->align[6]);
		$this->Cell($this->width[7], 5, '', 'TBR', 0, $this->align[7]);
		$this->Cell($this->width[8], 5, number_format($this->mtva, 2), 'TBR', 0, $this->align[8]);
		$this->Cell($this->width[9], 5, number_format($this->ttc, 2), 'TBR', 0, $this->align[9]);
		$this->total = true;
	}

}