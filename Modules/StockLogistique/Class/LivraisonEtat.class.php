<?php

require_once('Class/Lib/pdfb1/pdfb.php');

class LivraisonEtat extends PDFB {
	
	private $object;
	private $width;
	private $posy;
	private $bottom = false;
	private $fond;
	private $livraison;
	
	
	function LivraisonEtat($obj,$livr,$fond,$orientation='P',$unit='mm',$format='A4') {
		parent::__construct($orientation,$unit,$format);
		$this->object = $obj;
		$this->fond = $fond;
		$this->livraison = $livr;
	}
	
	
	function Header() {
		$this->SetFillColor(192,192,192);
		if($this->fond) {
		 	$code = 'FOND_'.$this->object->Societe;
		 	$rec = Sys::$Modules['Devis']->callData('Constante/Code='.$code);
			$this->picture = 'Modules/Devis/Class/'.$rec[0]['Valeur'];
			$this->Image($this->picture, 0, 0, 210, 297);
		}

		$pg = $this->PageNo();
		if($pg == 1) {
			// number
			$tmp = $this->livraison ? '$LV' : '$RP';
			$tmp .= sprintf('%06d',$this->object->Id);
			$this->BarCode($tmp, "C128B", 145, 5, 200, 35, 0.5, 0.5, 2, 5, "", "PNG");// 
			$y = 26;
			$this->SetXY(110,$y);
			$this->SetFont('Arial','B',12);
			$tmp = $this->livraison ? 'Livraison' : 'Reprise';
			$tmp .= ' n° '.$this->object->Reference."\nDate ".date('d.m.Y',$this->object->Date);
			$this->MultiCell(90,6,$tmp);
			$y += 6;
			// client
			$rec = Sys::$Modules['Repertoire']->callData("Tiers/".$this->object->ClientId);
			$cli = genericClass::createInstance('Repertoire', $rec[0]);
			$rec = Sys::$Modules['Repertoire']->callData("Tiers/".$this->object->LivraisonId);
			$liv = genericClass::createInstance('Repertoire', $rec[0]);
			// delivery address
			$y = 45;
			$this->SetXY(10, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,5,'Adresse de Livraison',1,0,'C',true);
			$y += 5;
			$this->SetXY(10, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,8,$liv->Intitule,'LR');
			$y += 5;
			$this->SetXY(10,$y);
			$this->SetFont('Arial','',10);
			$this->MultiCell(90,5,$liv->Adresse1."\n".$liv->Adresse2."\n".$liv->Adresse3."\n",'LR');
			$y += 3*5;
			$this->SetXY(10, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,5,$liv->CodPostal.' '.$liv->Ville.' '.$liv->Cedex,'LR');
			$y += 5;
			$this->SetXY(10, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,6,'Tél. :'.$liv->Telephone,'LRB');
			
			// invoice address
			$y = 45;
			$this->SetXY(110, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,5,'Adresse de facturation',1,0,'C',true);
			$y += 5;
			$this->SetXY(110, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,8,$cli->Intitule,'LR');
			$y += 5;
			$this->SetXY(110,$y);
			$this->SetFont('Arial','',10);
			$this->MultiCell(90,5,$cli->Adresse1."\n".$cli->Adresse2."\n".$cli->Adresse3."\n",'LR');
			$y += 3*5;
			$this->SetXY(110, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,5,$cli->CodPostal.' '.$cli->Ville.' '.$cli->Cedex,'LR');
			$y += 5;
			$this->SetXY(110, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,6,'Tél. :'.$cli->Telephone,'LRB');
			$y += 6;
		}
		else {
			$y = 6;
			$this->SetXY(10,$y);
			$this->SetFont('Arial','B',12);
			$tmp = $this->livraison ? 'Livraison' : 'Reprise';
			$tmp .= ' n° '.$this->object->Reference."\nDate ".date('d.m.Y',$this->object->Date);
			$this->MultiCell(90,6,$tmp);
			$y += 6;
		}
		$header = array('Famille','Désignation','Qté','Référence');
		$this->width = array(30,110,10,40);
		$align = array('L','L','R','L');
		$y += 1;
		$this->SetXY(10, $y);
		$this->SetFont('Arial','B',8);
		for($i = 0; $i < 4; $i++) 
			$this->Cell($this->width[$i], 5, $header[$i], 1, 0, $align[$i], true);
		$this->posy = $y + 5;
	}
	
	
	function PrintLines($lines) {
		$this->SetFillColor(192,192,192);
		$y = $this->posy;
		$this->SetFont('Arial','',10);
		foreach($lines as $rc) {
			if($y >= 235) {
				$this->PrintBottom();
				$this->AddPage();
			}
			$this->SetXY(10,$y);
			$this->Cell($this->width[0], 8, $rc['Famille'], 'LB', 0, $this->align[0]);
			$this->Cell($this->width[1], 8, $rc['Designation'], 'LB', 0, $this->align[1]);
			$this->Cell($this->width[2], 8, $rc['Quantite'], 'LB', 0, $this->align[2]);
			$this->Cell($this->width[3], 8, $rc['Reference'], 'LBR', 0, $this->align[3]);
			$y += 8;
		}
	}
	
	function PrintBottom() {
		if($bottom) return;
		$bottom = true;
		$y = 240;
		$this->SetXY(10,$y);
		$this->SetFont('Arial','B',10);
		$this->MultiCell(90,5,"Signature et cachet\n\n\n\n",1);
	}

}
