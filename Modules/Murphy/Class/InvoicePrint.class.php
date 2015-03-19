<?php

require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');

class InvoicePrint extends FPDF {
	
	private $object;
	private $folder = 'Modules/Murphy/Class/images/';
	private $account;
	private $supplier;
	private $currency;

	function InvoicePrint($obj,$orientation='L',$unit='mm',$format='A4') {
		parent::__construct($orientation,$unit,$format);
		$this->object = $obj;
		$rec = $this->object->getParents('Third.InvoiceSupplierId');
		$this->supplier = $rec[0];
		$rec = $this->object->getParents('Account.InvoiceAccountId');
		$this->account = $rec[0];
		$rec = Sys::$Modules['Murphy']->callData('Currency/'.$this->object->Currency,false,0,1);
		$this->currency = $rec[0]['Currency'];
	}

	private function cv($txt) {
		return iconv('UTF-8','ISO-8859-15//TRANSLIT',$txt);
	}

	function Header() {
		$tmp = $this->folder.'mwc_invoice_landscape4.png';
		$this->Image($tmp,0,5,292,210);
		$this->SetFont('Helvetica','B',16);
		$this->SetY(39);
		$this->SetX(14);
		$this->Cell(0,10,'Commissions Invoice',0,1);
		$this->SetY(52);
		$this->SetX(180);
		$this->SetFont('Helvetica','I',11);
		$this->Cell(0,10,'Montpellier, '.date('F d, Y', $this->object->Date),0,1);
		$this->SetY(57);
		$this->SetX(180);
		$this->Cell(0,10,'Invoice number: '.$this->object->Reference,0,1);
		
		$this->SetFont('Helvetica','B',11);
		$this->SetY(47);
		$this->SetX(14);
		$this->Cell(25,10,$this->supplier->Company,0,1);
		$this->SetY(52);
		$this->SetX(14);
		$this->SetFont('Helvetica','',10);
		$tmp = $this->supplier->Address.' ~ ';
		if($this->supplier->Address2) $tmp .= $this->supplier->Address2.' ~ ';
		$tmp .= $this->supplier->PostCode.' '.$this->supplier->Town;
		$this->Cell(25,10,$this->cv($tmp),0,1);
		if($this->supplier->Country) {
			$rec = Sys::$Modules['Murphy']->callData('Country/Id='.$this->supplier->Country, false, 0, 1);
			$this->SetY(57);
			$this->SetX(14);
			$this->SetFont('Helvetica','B',10);
			$this->Write(10,$this->cv($rec[0]['Country']));
		}

	}

	function PrintLines($lines) {
//		$h = array('CA #','Seller Ref.','Buyer','Wine','Volume','Price','Com %','To be paid');
		$h = array('CA #','P/O #','Buyer','Wine','Volume','Price','Com %','To be paid');
		$w = array(30,25,90,57,20,16,15,20);
		$this->SetFillColor(31,16,98);
		$this->SetTextColor(255);
		$this->SetDrawColor(31,16,98);
		$this->SetLineWidth(.3);
		$this->SetY(66);
		$this->SetX(14);
		$this->SetFont('Helvetica','B',10);
		for($i = 0; $i < 8; $i++)
			$this->Cell($w[$i], 6.5, $h[$i], 1, 0, 'C', 1);
		$this->Ln();
		
		$this->SetFillColor(99,186,241);
		$this->SetTextColor(0);
		$this->SetFont('Helvetica','',9);
		$this->SetX(14);
		$n = count($lines);
		for($i = 0; $i < $n; $i++) {
			$line = $lines[$i];
			$vol = $line['Volume'];
			$unp = $line['UnitPrice'];
			$com = $line['ComMWC'];
			$tot = round($vol * $unp * $com / 100, 2);
			$this->Cell($w[0], 5.5, $line['Contract'], 1, 0, 'C', 1);
//			$this->Cell($w[1], 5.5, $line['SupplierInvoice'], 1, 0, 'L', 1);
			$this->Cell($w[1], 5.5, $line['PurchaseOrder'], 1, 0, 'L', 1);
			$this->Cell($w[2], 5.5, $line['Buyer'], 1, 0, 'L', 1);
			$this->Cell($w[3], 5.5, $line['Varietal'], 1, 0, 'L', 1);
			$this->Cell($w[4], 5.5, number_format($vol), 1, 0, 'R', 1);
			$this->Cell($w[5], 5.5, number_format($unp, 4), 1, 0, 'R', 1);
			$this->Cell($w[6], 5.5, $com.' %', 1, 0, 'R', 1);
			$this->Cell($w[7], 5.5, number_format($tot, 2), 1, 0, 'R', 1);
			$this->Ln();
			$this->SetX(14);
		}
		$this->SetTextColor(0);
		$this->SetFont('Arial','',12);
		$this->SetY(76+(5.5 * $n));
		$this->SetX(219);
		$this->Cell(25,6,'Commission due : ',0,0,'L');
		$tmp = $this->currency.' '.number_format($this->object->TotalTE, 2);
		$this->Cell(40,6,$tmp,0,1,'R');
		$this->SetX(219);
		$vat = $this->object->VATRate;
		$tmp = $vat ? number_format($vat, 2) : '0';
		$this->Cell(25,6,'TVA '.$tmp.'% : ',0,0,'L');
		$tmp = $this->currency.' '.number_format($this->object->VATAmount, 2);
		$this->Cell(40,6,$tmp,0,1,'R');
		$this->SetFont('Helvetica','B',12);
		$this->SetX(219);
		$this->Cell(25,11,'TOTAL : ',0,0,'L');
		$tmp = $this->currency.' '.number_format($this->object->TotalTI, 2);
		$this->Cell(40,11,$tmp,0,1,'R');
		if($this->Supplier->VATCharged != 1) { 
			if($this->Supplier->Country != 0) { 
				$message1 = "Opération sous le bénéfice du régime d'autoliquidation.";
				$message2 = "Art.283.2 CGI - Art. 196 de la directive TVA.";
			} 
			else {
				$message1 = "Exonération de TVA. Régime exportation";
				$message2 = "Art. 262.I 1er et 2ième CGI.";
			}
			$this->SetX(177);
			$this->SetFont('Helvetica','',11);
			$this->Cell(25,5,$this->cv($message1),0,1,'L');
			$this->SetX(177);
			$this->Cell(25,5,$this->cv($message2),0,1,'L');
			$this->SetX(177);
		}
	}
	
	
	private function bankInformation() {
		$this->SetTextColor(0);
		$this->SetFont('Helvetica','B',10);
		$this->SetY(135);
		$this->SetX(14);
		$this->Cell(70,5,$this->cv($this->account->Name),0,1,'L');
		$this->SetX(14);
		$this->Cell(70,5,$this->cv($this->account->Instructions),0,1,'L');
		$this->SetX(14);
		$this->Cell(70,5,$this->cv($this->account->BankName),0,1,'L');
		
		$this->SetFont('Helvetica','',10);
		if($this->account->BankNumber) {
			$this->SetX(14);
			$this->Cell(70,6,$this->cv('Bank Number / Code Banque'),1,0,'L');
			$this->Cell(80,6,$this->cv($this->account->BankNumber),1,1,'C');
		}
		$this->SetX(14);
		$this->Cell(70,6,$this->cv('Branch Code / Code Guichet'),1,0,'L');
		$this->Cell(80,6,$this->cv($this->account->BranchCode),1,1,'C');
		$this->SetX(14);
		$this->Cell(70,6,$this->cv('Account Number / Numéro de Compte'),1,0,'L');
		$this->Cell(80,6,$this->cv($this->account->AccountNumber),1,1,'C');
		$this->SetX(14);
		$this->Cell(70,6,$this->cv('Account name / Domiciliation'),1,0,'L');
		$this->Cell(80,6,$this->cv($this->account->AccountName),1,1,'C');
		if($this->account->FRNumber) {
			$this->SetX(14);
			$this->Cell(85,6,$this->cv('Federal Routing number (for domestic wire transfers)'),1,0,'L');
			$this->Cell(80,6,$this->cv($this->account->FRNumber),1,1,'C');
		}
		if($this->account->IdCode) {
			$this->SetX(14);
			$this->Cell(70,6,$this->cv('ID Code / Clé RIB'),1,0,'L');
			$this->Cell(80,6,$this->cv($this->account->IdCode),1,1,'C');
		}
		if($this->account->Iban) {
			$this->SetX(14);
			$this->Cell(70,6,'IBAN / Identifiant International',1,0,'L');
			$this->Cell(80,6,$this->cv($this->account->Iban),1,1,'C');
		}
		$this->SetX(14);
		$this->Cell(70,6,'Code BIC / SWIFT',1,0,'L');
		$this->Cell(80,6,$this->cv($this->account->BicSwift),1,1,'C');
		if($this->account->Ach) {
			$pdf->SetX(14);
			$pdf->Cell(85,6,'ACH',1,0,'L');
			$pdf->Cell(80,6,$this->cv($this->account->Ach),1,1,'C');
		}
	}

	function Footer() {
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxB:",$account);
		$this->bankInformation();
		$this->SetFont('Helvetica','',9);
		$this->SetTextColor(31,16,99);
		$this->SetY(193);
		$this->SetX(78);
		$this->MultiCell(200, 5, $this->cv($this->account->Address));
	}
	
}
