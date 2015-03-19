<?php

require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');

class SamplePrint extends FPDF {
	
	private $object;
	private $folder = 'Modules/Murphy/Class/images/';
	private $buyer;
	private $contact;
	private $purpose;
	private $cw = array(57,35,34,12,27,16,22,12,23,14,24);
	

	function SamplePrint($obj,$orientation='L',$unit='mm',$format='A4') {
		parent::__construct($orientation,$unit,$format);
		$this->object = $obj;		
		$rec = $this->object->getParents('Third.SampleRequestBuyerId');
		$this->buyer = $rec[0];
		// contract
		$rec = $this->object->getParents('Contract.SampleRequestContractId');
		if(count($rec)) {
			$ctc = $rec[0]->BuyerContactId;
			
		}
		else {
			// proposal
			$rec = $this->object->getParents('Proposal.SampleRequestProposalId');
			if(count($rec)) {
				$rec = $rec[0]->getParents('Enquiry.ProposalEnquiryId');
				if(count($rec)) $ctc = $rec[0]->ContactId;
			}
			else {
				// enquiry
				$rec = $this->object->getParents('Enquiry.SampleRequestEnquiryId');
				if(count($rec)) $ctc = $rec[0]->ContactId;
				else {
					//shipment
					$rec = $this->object->getParents('Shipment.SampleRequestShipmentId');
					if(count($rec)) {
						$rec = $rec[0]->getParents('Contract.ShipmentContractId');
						if(count($rec)) $ctc = $rec[0]->BuyerContactId;
					}
				}
			}
		}
		if($ctc) {
			$rec = Sys::getData('Murphy', 'Contact/'.$ctc);
			$this->contact = $rec[0]->FirstName.' '.$rec[0]->Surname;
		}
		if($this->object->Purpose) {
			$rec = Sys::getData('Murphy', 'SamplePurpose/'.$this->object->Purpose);
			$this->purpose = $rec[0]->Purpose;
		}
	}

	private function cv($txt) {
		return iconv('UTF-8','ISO-8859-15//TRANSLIT',$txt);
	}

	function Header() {
		$tmp = $this->folder.'mwc_sample_shipment.jpg';
		$this->Image($tmp,0,5,292,210);
		$this->SetFont('Helvetica','B',16);
		$this->SetXY(13,46);
		$this->Cell(0,10,'Sample Shipment Report',0,1);
		$this->SetFont('Helvetica','B',11);
		$this->SetXY(13,57);
		$this->Cell(25,10,'Buyer :',0,1);
		$this->SetXY(13,65);
		$this->Cell(25,10,'Contact Person :',0,1);
		$this->SetFont('Helvetica','',11);
		$this->SetXY(53,57);
		$this->Cell(25,10,$this->cv($this->buyer->Company),0,1);
		$this->SetXY(53,65);
		$this->Cell(25,10,$this->cv($this->contact),0,1);
		$this->SetFont('Helvetica','B',11);
		$this->SetXY(125,57);
		$this->Cell(25,10,'Sample Request No :',0,1);
		$this->SetXY(125,65);
		$this->Cell(25,10,'Purpose of shipment :',0,1);
		$this->SetFont('Helvetica','',11);
		$this->SetXY(168,57);
		$this->Cell(25,10,$this->object->Reference,0,1);
		$this->SetXY(168,65);
		$this->Cell(25,10,$this->purpose,0,1);
		$this->SetFont('Helvetica','B',11);
		$this->SetXY(223,57);
		$this->Cell(25,10,'Date :',0,1);
		$this->SetFont('Helvetica','',11);
		$this->SetXY(240,57);
		$this->Cell(25,10,date('F d, Y', $this->object->Date),0,1);
		
		
		$this->SetFillColor(93,24,59);
		$this->SetTextColor(221,175,37);
		$this->SetDrawColor(93,24,59);
		$this->SetLineWidth(.3);
		$this->SetXY(14,79);
		$h=array('Seller / Winery','Varietal','Appellation','Vintage','Reference','Volume','Price','Alc. %','Date Shipped','Courier','AWB #');
		for($j=0; $j<11; $j++)
			$this->Cell($this->cw[$j],7,$h[$j],1,0,'C',1);
	}

	function PrintLines($lines) {
		$y = 86;
		$this->SetFillColor(255);
		$this->SetTextColor(93,24,59);
		$this->SetFont('');
		foreach($lines as $line) {
			$this->SetXY(14,$y);
			$price = '';
			$courier = '';
			$send = '';
			$volume;
			if($line['Volume']) $volume = number_format($line['Volume']);
			if($line['UnitPrice']) {
				if($line['Currency']) {
					$rec = Sys::getData('Murphy', 'Currency/'.$line['Currency']);
					$price = $rec[0]->Currency.' ';
				}
				$price .= number_format($line['UnitPrice'], 4);
			}
			if($line['Courier']) {
				$rec = Sys::getData('Murphy', 'Courier/'.$line['Courier']);
				$courier = $rec[0]->Courier;
			}
			if($line['SendDate']) $send = date('d/m/Y',$line['SendDate']);
			$this->Cell($this->cw[0],6,$line['Supplier'],1,0,'L',1);
			$this->Cell($this->cw[1],6,$line['Varietal'],1,0,'L',1);
			$this->Cell($this->cw[2],6,$line['Appellation'],1,0,'L',1);
			$this->Cell($this->cw[3],6,$line['Vintage'],1,0,'C',1);
			$this->Cell($this->cw[4],6,$line['SampleRef'],1,0,'L',1);
			$this->Cell($this->cw[5],6,$volume,1,0,'R',1);
			$this->Cell($this->cw[6],6,$price,1,0,'R',1);
			$this->Cell($this->cw[7],6,$line['Alc'].'%',1,0,'R',1);
			$this->Cell($this->cw[8],6,$send,1,0,'C',1);
			$this->Cell($this->cw[9],6,$courier,1,0,'L',1);
			$this->Cell($this->cw[10],6,$line['ABNumber'],1,0,'L',1);
			$y += 6;
		}
	}
	
	

	function Footer() {
		$this->SetFont('Helvetica','I',10);
		$this->SetTextColor(93,24,59);
		$this->SetY(185);
		$this->SetX(13);
		$this->Cell(0,10,'All offers are subject to prior sale',0,1);
		$this->SetFont('Helvetica','',10);	
		$this->SetY(193.2);
		$this->SetX(78);
		$this->Cell(0,10,$this->cv(' 8 bis rue Thérèse, 34090 Montpellier ~ tel +33(0)4 99 58 36 70 ~ fax +33(0) 4 99 58 36 71'),0,1);
	}
	
}
