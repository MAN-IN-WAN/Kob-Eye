<?php

require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');

class UnpaidInvoices extends FPDF {
	
	private $linesWidth;
	private $date;
	private $account;
	private $currency;
	private $width;
	private $align;
	private $tte;
	private $tti;
	private $rupAccount;
	private $rupCurrency;
	private $posy;
	
	
	function UnpaidInvoices($date,$orientation='P',$unit='mm',$format='A4') {
		parent::__construct($orientation,$unit,$format);
		$this->date = $date;
		$this->AcceptPageBreak(true, 12);
		$this->width = array(16,4,15,65,22,22,8,15);
		$this->align = array('L','C','C','L','R','R','L','C');
	}

	private function cv($txt) {
		return iconv('UTF-8','ISO-8859-15//TRANSLIT',$txt);
	}
	
	function Header() {
		$this->posy = 5;
		$this->SetFont('Arial','B',9);
		$this->SetXY(14, $this->posy);
		$tmp = $this->account->Name;
		$tmp .= '                UNPAID INVOICES on '.date('d/m/y', $this->date);
		//$tmp .= '          Currency : '.$this->currency;
		$this->Cell(200, 6, $tmp);
		$this->SetXY(210-20, $this->posy);
		$this->Cell(30, 6, 'Page '.$this->PageNo(),'R');
		$this->posy += 7;
		$this->colHeader();
	}
	
	private function colHeader() {
		$this->SetFillColor(192,192,192);
		$header = array('Invoice','T','Date','Client','Tax Excluded','Tax Included','Cur','Due date');
		$this->SetXY(14, $this->posy);
		$this->SetFont('Arial','B',8);
		$n = count($header);
		for($i = 0; $i < $n; $i++)
			$this->Cell($this->width[$i], 6, $header[$i], 1, 0, $this->align[$i], true);
		$this->posy += 6;
		$this->SetFont('Arial','',8);
	}
	
	function Footer() {
		$this->SetXY(14, $this->posy);
		if($this->total) return;
		$n = count($this->width);
		for($i = 0; $i < $n; $i++)
			$this->Cell($this->width[$i], 0.1, '', 'T', 0, $this->align[$i]);
	}
	
	function PrintLines($lines) {
		foreach($lines as $rc) {
			$rec = Sys::$Modules['Murphy']->callData('Invoice/'.$rc['Id']);
			$inv = genericClass::createInstance('Murphy', $rec[0]);
			$acc = $inv->getParents('Account');
			$acc = $acc[0];
			if($this->rupAccount != $acc->Id || $this->rupCurrency != $inv->Currency) {
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx:$this->rupAccount:",$inv);
				if($this->rupAccount) $this->PrintTotals();
				$cur = genericClass::createInstance('Murphy', 'Currency');
				$cur->initFromId($inv->Currency);
				$this->currency = strtoupper($cur->Currency);
				$this->rupCurrency = $inv->Currency;
				if($this->rupAccount != $acc->Id) {
					$this->account = $acc;
					$this->AddPage();
					$this->rupAccount = $acc->Id;
				}
			}
			$this->printLine($inv);
		}
		if($this->rupAccount) $this->PrintTotals();
	}

	private function printLine($inv) {
		$cli = $inv->getParents('Third');
		$this->SetXY(14, $this->posy);
		$this->Cell($this->width[0], 5, $inv->Reference, 'L', 0, $this->align[0]);
		$this->Cell($this->width[1], 5, $inv->Type, 'L', 0, $this->align[1]);
		$this->Cell($this->width[2], 5, date('d/m/y',$inv->Date), 'L', 0, $this->align[2]);
		$this->Cell($this->width[3], 5, $cli[0]->Company, 'L', 0, $this->align[3]);
		$this->Cell($this->width[4], 5, number_format($inv->TotalTE, 2), 'L', 0, $this->align[4]);
		$this->Cell($this->width[5], 5, number_format($inv->TotalTI, 2), 'L', 0, $this->align[5]);
		$this->Cell($this->width[6], 5, $this->currency, 'L', 0, $this->align[6]);
		$this->Cell($this->width[7], 5, $inv->DueDate === null ? '' : date('d/m/y',$inv->DueDate), 'LR', 0, $this->align[7]);
		$this->posy += 5;
		$this->tte += $inv->TotalTE;
		$this->tti += $inv->TotalTI;	
	} 

	function PrintTotals() {
		$this->SetFont('Arial','B',8);
		$this->SetXY(14, $this->posy);
		$this->Cell($this->width[0], 5, '', 'TBL', 0, $this->align[0]);
		$this->Cell($this->width[1], 5, '', 'TB', 0, $this->align[1]);
		$this->Cell($this->width[2], 5, '', 'TB', 0, $this->align[2]);
		$this->Cell($this->width[3], 5, 'TOTAL', 'TBR', 0, 'R');
		$this->Cell($this->width[4], 5, number_format($this->tte, 2), 'TBR', 0, $this->align[4]);
		$this->Cell($this->width[5], 5, number_format($this->tte, 2), 'TBR', 0, $this->align[5]);
		$this->Cell($this->width[6], 5, $this->currency, 'TBR', 0, $this->align[6]);
		$this->Cell($this->width[7], 5, '', 'TBR', 0, $this->align[7]);
		$this->SetFont('Arial','',8);
		$this->total = true;
		$this->tte = 0;
		$this->tti = 0;
		$this->posy += 5;
	}

}