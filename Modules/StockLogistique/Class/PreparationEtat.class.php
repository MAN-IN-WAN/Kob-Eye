<?php

require_once('Class/Lib/pdfb1/pdfb.php');

class PreparationEtat extends PDFB {
	
	private $object;
	private $width;
	private $posy;
	
	
	function PreparationEtat($obj,$orientation='P',$unit='mm',$format='A4') {
		parent::__construct($orientation,$unit,$format);
		$this->object = $obj;
		$this->AcceptPageBreak(true, 10);
	}
	
	
	function Header() {
		//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx", $this->object);
		// number
		$tmp = ':'.sprintf('%06d',$this->object->Id);
		$this->BarCode($tmp, "C128B", 145, 5, 200, 35, 0.5, 0.5, 2, 5, "", "PNG");

		$this->BarCode('*ENLEVER', "C128B", 170, 80, 200, 35, 0.3, 0.5, 2, 5, "", "PNG");
		$this->BarCode('*FAMILLE', "C128B", 170, 110, 200, 35, 0.3, 0.5, 2, 5, "", "PNG");
		$this->BarCode('*TOURNEE', "C128B", 170, 140, 200, 35, 0.3, 0.5, 2, 5, "", "PNG");
		$this->BarCode('*LIVRAISON', "C128B", 170, 170, 200, 35, 0.3, 0.5, 2, 5, "", "PNG");
		$this->BarCode('*ELEMENT', "C128B", 170, 200, 200, 35, 0.3, 0.5, 2, 5, "", "PNG");

		$this->SetFillColor(192,192,192);
		$y = 10;
		$this->SetXY(10,$y);
		$this->SetFont('Arial','B',12);
		$this->MultiCell(90,6,'Préparation n° '.$this->object->Reference."\nDate ".date('d.m.Y',$this->object->Date));
		$y += 6;

		$pg = $this->PageNo();
		if($pg == 1) {
			// client
			$rec = Sys::$Modules['Repertoire']->callData("Tiers/".$this->object->ClientId);
			$cli = genericClass::createInstance('Repertoire', $rec[0]);
			$rec = Sys::$Modules['Repertoire']->callData("Tiers/".$this->object->LivraisonId);
			$liv = genericClass::createInstance('Repertoire', $rec[0]);
			// delivery address
			$y = 24;
			$this->SetXY(10, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,5,'Adresse de Livraison',1,0,'C',true);
			$y += 5;
			$this->SetXY(10, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,5,$liv->Intitule,'LR');
			$y += 5;
			$this->SetXY(10,$y);
			$this->SetFont('Arial','',10);
			$this->MultiCell(90,4,$liv->Adresse1."\n".$liv->Adresse2."\n",'LR');
			$y += 2*4;
			$this->SetXY(10, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,4,$liv->CodPostal.' '.$liv->Ville.' '.$liv->Cedex,'LR');
			$y += 4;
			$this->SetXY(10, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,5,'Tél. :'.$liv->Telephone,'LRB');
			
			// invoice address
			$y = 24;
			$this->SetXY(110, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,5,'Client',1,0,'C',true);
			$y += 5;
			$this->SetXY(110, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,5,$cli->Intitule,'LR');
			$y += 5;
			$this->SetXY(110,$y);
			$this->SetFont('Arial','',10);
			$this->MultiCell(90,4,$cli->Adresse1."\n".$cli->Adresse2."\n",'LR');
			$y += 2*4;
			$this->SetXY(110, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,4,$cli->CodPostal.' '.$cli->Ville.' '.$cli->Cedex,'LR');
			$y += 4;
			$this->SetXY(110, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,5,'Tél. :'.$cli->Telephone,'LRB');
			$y += 5;
		}
		$y += 2;
/*
		$header = array('Code','Famille','Désignation','Qté');
		$this->width = array(40,30,110,10);
		$align = array('L','L','L','R');
		$y += 2;
		$this->SetXY(10, $y);
		$this->SetFont('Arial','B',10);
		$this->Cell($this->width[0], 6, $header[0], 1, 0, 'C', true);
		$this->Cell($this->width[1], 6, $header[1], 1, 0, 'C', true);
		$this->Cell($this->width[2], 6, $header[2], 1, 0, 'C', true);
		$this->Cell($this->width[3], 6, $header[3], 1, 0, 'C', true);
		$this->posy = $y + 6;
 * 
 */
		$header = array('Famille','Désignation','Qté');
		$this->width = array(30,110,10);
		$align = array('L','L','R');
		$y += 2;
		$this->SetXY(10, $y);
		$this->SetFont('Arial','B',10);
		$this->Cell($this->width[0], 6, $header[0], 1, 0, 'C', true);
		$this->Cell($this->width[1], 6, $header[1], 1, 0, 'C', true);
		$this->Cell($this->width[2], 6, $header[2], 1, 0, 'C', true);
		$this->posy = $y + 6;
	}
	
	
	function PrintLines($lines) {
		$this->SetFillColor(192,192,192);
		$y = $this->posy;
		$this->SetFont('Arial','',10);
		foreach($lines as $rc) {
/*
			$this->BarCode('-'.$rc['Famille'], "C128B", 12, $y+2, 148, 50, 0.3, 0.28, 2, 5, "", "PNG");
			$this->SetXY(10,$y);
			$this->Cell($this->width[0], 16, '', 'LB', 0, $this->align[0]);
			$this->Cell($this->width[1], 16, $rc['Famille'], 'LB', 0, $this->align[1]);
			$this->Cell($this->width[2], 16, $rc['Designation'], 'LB', 0, $this->align[2]);
			$this->Cell($this->width[3], 16, $rc['Quantite'], 'LRB', 0, $this->align[3]);
			$y += 16;
*/
			$this->SetXY(10,$y);
			$this->Cell($this->width[0], 6, $rc['Famille'], 'LB', 0, $this->align[0]);
			$this->Cell($this->width[1], 6, $rc['Designation'], 'LB', 0, $this->align[1]);
			$this->Cell($this->width[2], 6, $rc['Quantite'], 'LRB', 0, $this->align[2]);
			$y += 6;
		}
	}
	

}
?>