<?php

require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');
require_once('Class/Lib/num2word.class.php');

class ContractPrint extends FPDF {
	
	private $object;
	private $folder = 'Modules/Murphy/Class/images/';
	private $currency;
	private $purchaseUnit;

	function ContractPrint($obj,$orientation='P',$unit='mm',$format='A4') {
		parent::__construct($orientation,$unit,$format);
		$this->object = $obj;
		$this->AcceptPageBreak(false, 12);
	}
	
	private function cv($txt) {
		return iconv('UTF-8','ISO-8859-15//TRANSLIT',$txt);
	}

	function Header() {
		$this->SetFont('Helvetica','B',15);
		if($this->object->Status == STC_CANCELLED) {
			$tmp = $this->folder.'mwc_bg_new_cancel.png';
			$this->Image($tmp,0,5,210,292);
			$this->SetXY(155, 70); 
			$this->SetFont('Helvetica','',14);
			$this->Cell(0,10,date('F d, Y', $this->object->Revised));
		}
		elseif($this->object->Revised) {
			$tmp = $this->folder.'revised2.png'; 
			$this->Image($tmp,0,5,210,292);
			$this->SetXY(150, 70); 
			$this->SetFont('Helvetica','',14);
			$this->Cell(0,10,date('F d, Y', $this->object->Revised),0,1);
		}
		else {
			$tmp = $this->folder.'mwc_bg_new.png'; 
			$this->Image($tmp,0,5,210,292);
		}
		$tmp = $this->folder.'mwc_title.jpg';
		$this->Image($tmp,17,10,90,12);
		$tmp = $this->folder.'mwc_logo.jpg';
		$this->Image($tmp,162,10,35,33);
		$this->SetY(20);
		$this->SetX(17);
		$this->Cell(0,10,'Consultancy Agreement',0,1);	
		$this->SetY(32);
		$this->SetX(17);
		$this->SetFont('Helvetica','I',10);
		$this->Cell(0,10,'Montpellier, '.date('F d, Y', $this->object->Date),0,1);
		$this->SetY(38);
		$this->SetX(17);
		$this->Cell(0,10,'Consultancy Agreement number: '.$this->object->Reference,0,1);
		$this->thirdBlock(true);
		$this->thirdBlock(false);
		$this->SetFont('Helvetica','',11);
		$this->SetY(98);
		$this->SetX(38);
		$tmp = '';
		//if($this->object->CountryWine) {
		//	$rec = Sys::$Modules['Murphy']->callData('Country/Id='.$this->object->CountryWine, false, 0, 1);
		//	$tmp = $rec[0]['Country'].' ';
		//}
		if($this->object->Varietal) {
			$rec = Sys::$Modules['Murphy']->callData('Varietal/Id='.$this->object->Varietal, false, 0, 1);
			$tmp .= $rec[0]['Varietal'].' ';
		}
		//if($this->object->Colour) {
		//	$rec = Sys::$Modules['Murphy']->callData('Colour/Id='.$this->object->Colour, false, 0, 1);
		//	$tmp .= $rec[0]['Colour'].' ';
		//}
		$tmp = trim($tmp);
		if($this->object->Appelation) {
			$rec = Sys::$Modules['Murphy']->callData('Appellation/Id='.$this->object->Appellation, false, 0, 1);
			$tmp .= ', '.$rec[0]['Appellation'];
		}
		$this->Cell(0,10,$this->cv($tmp),0,1);
		$this->SetY(117);
		$this->SetX(48);
		$this->Cell(0,10,$this->object->Vintage,0,1);
		$this->SetY(136);
		$this->SetX(55);
		$rec = Sys::$Modules['Murphy']->callData('Quantity/'.$this->object->Quantity, false, 0, 1);
		$this->purchaseUnit = $rec[0];
		$tmp = number_format($this->object->Volume).' '.$this->purchaseUnit['Quantity'];
		if($this->object->Approval) {
			$tmp .= '        Subject to sample approval';
		}
		$this->Cell(0,10,$this->cv($tmp),0,1);
		$this->SetY(155);
		$this->SetX(59);
		$rec = Sys::$Modules['Murphy']->callData('Currency/'.$this->object->Currency, false, 0, 1);
		$this->currency = $rec[0];
		$this->Image($this->folder.$rec[0]['Icon'],37,152,17,17);
		$tmp = $this->object->UnitPrice.' '.$rec[0]['Currency'];
		//$rec = Sys::$Modules['Murphy']->callData('Unit/Id='.$this->object->Unit, false, 0, 1);
		//$tmp .= '/'.$rec[0]['Unit'];
		$tmp .= ' / '.$this->purchaseUnit['Quantity'];
		$rec = Sys::$Modules['Murphy']->callData('IncoTerm/Id='.$this->object->Inco, false, 0, 1);
		$transp = $this->folder.$rec[0]['Icon'];
		$tmp .= ', '.$rec[0]['Inco'];
		$this->Cell(0,10,$this->cv($tmp),0,1);
		$com = trim($this->object->Comments);
		if ($com != "") {
			$this->SetY(172);
		} else {
			$this->SetY(176);
		}
		$this->SetX(61);
		$rec = Sys::$Modules['Murphy']->callData('Filtration/Id='.$this->object->Filtration, false, 0, 1);
		$tmp = $rec[0]['Filtration'];
		if($this->object->SampleRef) $tmp = $this->object->SampleRef.', '.$tmp; 
		$this->Cell(0,10,$this->cv($tmp),0,1);
		$this->SetY(181);
		$this->SetX(61);
		$this->MultiCell(145,4.5,$this->cv($com),0,1);
		$this->SetY(196);
		$this->SetX(61);
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx:".$tmp);
		$this->Image($transp,39,193,17,17);
		$tmp = 'All by '.date('F d, Y', $this->object->ShippingDate);
		$this->Cell(0,10,$this->cv($tmp),0,1);
		$this->SetY(217);
		$this->SetX(58);
		$rec = Sys::$Modules['Murphy']->callData('PaymentTerm/Id='.$this->object->Payment, false, 0, 1);
		$tmp = $rec[0]['Payment'];
		$this->Cell(0,10,$this->cv($tmp),0,1);
		$this->SetY(232);
		$this->SetX(56);
		$this->Cell(0,10,'Thank you for your business.',0,1);
		$this->SetY(238);
		$this->SetX(55);
		$this->SetFont('Helvetica','I',11);
		$this->Cell(0,10,'Nous vous remercions de votre confiance.',0,1);
	}
	
	private function thirdBlock($supplier) {
		if($supplier) {
			$type = 'Supplier';
			$key = 'Third.ContractSupplierId';
			$cnt = $this->object->SupplierContactId;
			$ref = $this->object->SupplierRef;
		}
		else {
			$type = 'Buyer';
			$key = 'Third.ContractBuyerId';
			$cnt = $this->object->BuyerContactId;
			$ref = $this->object->BuyerRef;
			$y = 19;
		}
		$third = $this->object->getParents($key);
		$third = $third[0];

		$this->SetY(48+$y);
		$this->SetX(17);
		$this->SetFont('Helvetica','',9);
		$this->Cell(25,10,$type.' :',0,1);
		
		$this->SetFont('Helvetica','B',10);
		$this->SetY(48+$y);
		$this->SetX($supplier ? 32 : 29);
		$this->Cell(25,10,$this->cv($third->Company),0,1);
		
		if($ref) {
			$this->SetFont('Helvetica','I',9);
			$this->SetY(48+$y);
			$this->SetX(97);
			$this->Cell(25,10,'Ref.  '.$this->cv($ref),0,1);
		}
		$tmp = $third->Address.' ~ ';
		if($third->Address2) $tmp .= $third->Address2.' ~ ';
		$tmp .= $third->PostCode.' '.$third->Town;
		if($third->Country) {
			$rec = Sys::$Modules['Murphy']->callData('Country/Id='.$third->Country, false, 0, 1);
			$tmp .= ' ~ '.$rec[0]['Country'];
		}
		$this->SetY(53+$y);
		$this->SetX(17);
		$this->SetFont('Helvetica','',9);
		$this->Cell(25,10,$this->cv($tmp),0,1);
		$this->SetY(58+$y);
		$this->SetX(17);
		
		$rec = Sys::$Modules['Murphy']->callData('Contact/Id='.$cnt, false, 0, 1);
		$this->SetFont('Helvetica','B',9);
		$this->Write(10,$this->cv($rec[0]['FullName']));
		$this->SetFont('Helvetica','',9);
		$tmp = ' ~ '.$this->object->Phone;
		if($third->Fax) $tmp .= ' ~ '.$third->Fax;
		$this->Write(10,$this->cv($tmp));
	}

	function Footer() {
		$comm = $this->object->ComMWC;
		if($this->object->ComMode == 0) {
			$n2w = new num2word($comm, 'p');
			$str_com_uk = $comm.'% ('.$n2w->convert('en-EN', ' point ').' percent)';
			$str_com_fr = $comm.'% ('.$n2w->convert('fr-FR', ' virgule ').' pourcent)';
		}
		else {
			$n2w = new num2word($comm, strtoupper($this->currency['Currency']));
			$tmp_fr = $n2w->convert('fr-FR');
			$tmp_uk = $n2w->convert('en-EN');
			$tmp_u0 = $n2w->getFormated(' ','.');
			$tmp_f0 = $n2w->getFormated('.',',');
			if($this->object->ComMode == 1) {
				$str_com_uk = $tmp_u0.' ('.$tmp_uk.')';
				$str_com_fr = $tmp_f0.' ('.$tmp_fr.')';
			}
			else {
				$qty = $this->purchaseUnit['Unit'];
				$str_com_uk = $tmp_u0.' / '.$qty.' ('.$tmp_uk.' / '.$qty.')';
				$str_com_fr = $tmp_f0.' / '.$qty.' ('.$tmp_fr.' / '.$qty.')';
			}
		}
		$tmp = 'This agreement represents a confirmation between the parties and outlines the conditions of sale. In accordance, the ';
		$tmp .= $this->object->BuyerBilled ? 'buyer' : 'seller';
		$tmp .= ' agrees to pay Murphy Wine company a commission of ';
		$tmp .= $str_com_uk.' broker\'s fee. Payment of the broker\'s commission does not derogate from the article  5A1 4 French law of 31 Dec. 1949.';
		$this->SetTextColor(152,104,118);
		$this->SetFont('Helvetica','',7.5);
		$this->SetY(-47);
		$this->SetX(38);
		$this->MultiCell(0,3,$this->cv($tmp),0,'J');
		$this->Ln(0.6);
		
		$tmp = 'Le présent bordereau de confirmation constate seulement l\'accord des parties et les conditions de la vente. Par la présente, ';
		$tmp .= $this->object->BuyerBilled ? 'l\'achteur' : 'le fournisseur';
		$tmp .= ' s\'engage à verser à Murphy Wine Company une commission de ';
		$tmp .= $str_com_fr.' en prestation de courtage. Les modalités d\'encaissement du Courtage ne dérogent pas à l\'article 5A1 4 de la loi du 31 Déc. 1949.';
		$this->SetX(38);
		$this->SetFont('Helvetica','I',7.5);
		$this->MultiCell(0,3,$this->cv($tmp),0,'J');
		$this->Ln(0.6);

		$tmp = "Identité professionnelle du courtier en vins et spiritueux : MURPHY-MELVILLE Daniel Francis\n";
		$tmp .= "Numéro de carte professionnelle : 2007/34/003 délivrée le 5 juillet 2007 par la Chambre Régionale de Commerce et d\'industrie de Languedoc-Roussillon.";
		$this->SetX(38);
		$this->SetFont('Helvetica','',7.5);
		$this->MultiCell(0,3,$this->cv($tmp),0,'J');
	}
}
