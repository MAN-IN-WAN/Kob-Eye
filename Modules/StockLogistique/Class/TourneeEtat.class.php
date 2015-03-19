<?php

require_once('Class/Lib/pdfb1/pdfb.php');

class TourneeEtat extends PDFB {
	
	private $object;
	private $width;
	private $posy;
	private $bottom = false;
	private $fond;
	
	
	function TourneeEtat($obj,$fond,$orientation='P',$unit='mm',$format='A4') {
		parent::__construct($orientation,$unit,$format);
		$this->object = $obj;
		$this->fond = $fond;
		$this->AcceptPageBreak(true, 30);
	}
	
	
	function Header() {
		//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx", $this->object);
		$this->SetFillColor(192,192,192);

		$pg = $this->PageNo();
		if($pg == 1) {
			if($this->object->VehiculeId) {
				$rec = Sys::$Modules['StockLogistique']->callData("Vehicules/".$this->object->VehiculeId);
				$ve = $rec[0]['Designation'];
			}
			if($this->object->ChauffeurId) {
				$rec = Sys::$Modules['Repertoire']->callData("Tiers/".$this->object->ChauffeurId);
				$ch = $rec[0]['Intitule'];
			}
			// number
			$this->BarCode('$TR'.sprintf('%06d',$this->object->Id), "C128B", 145, 5, 200, 35, 0.5, 0.5, 2, 5, "", "PNG");// 
			$y = 20;
			$this->SetXY(10,$y);
			$this->SetFont('Arial','B',12);
			$this->Cell(190,8,'FEUILLE DE ROUTE',1,0,'C');
			$this->SetXY(185,$y);
			$this->SetFont('Arial','',10);
			$this->Cell(35, 8, 'Page '.$this->PageNo());
			$y += 8;
			$this->SetXY(10,$y);
			$this->SetFont('Arial','B',8);
			$this->Cell(15,5,'Numéro','L',0,'C',true);
			$this->Cell(16,5,'Date','L',0,'C',true);
			$this->Cell(80,5,'Chauffeur','L',0,'C',true);
			$this->Cell(79,5,'Véhicule','LR',0,'C',true);
			$y += 5;
			$this->SetXY(10,$y);
			$this->SetFont('Arial','',10);
			$this->Cell(15,7,$this->object->Reference,'LB',0,'C');
			$this->Cell(16,7,date('d.m.y',$this->object->Date),'LB',0,'C');
			$this->Cell(80,7,$ch,'LB',0,'L');
			$this->Cell(79,7,$ve,'LBR',0,'L');
			$y += 9;
		}
		else {
			$y = 5;
		}
		$header = array('BL/BR','Adresse','Commentaires');
		$this->width = array(36,74,80);
		$align = array('C','L','L');
		$this->SetXY(10, $y);
		$this->SetFont('Arial','B',8);
		$this->Cell($this->width[0], 5, $header[0], 1, 0, 'C', true);
		$this->Cell($this->width[1], 5, $header[1], 1, 0, 'C', true);
		$this->Cell($this->width[2], 5, $header[2], 1, 0, 'C', true);
		$this->posy = $y + 5;
	}
	
	
	function PrintLines($lines) {
		$id = $this->object->Id;
		$y = $this->posy;
		foreach($lines as $rc) {
			if($rc['ObjectType'] == 'BLTete') {
				$req = 'BLTete/';
				$ref = "Livraison ";
				$cod = '$LV';
			}
			else {
				$req = 'RepriseTete/';
				$ref = "Reprise ";
				$cod = '$RP';
			}
			$rec = Sys::$Modules['StockLogistique']->callData($req.$rc['Id'], false, 0, 1);
			$liv = genericClass::createInstance('StockLogistique',$rec[0]);
			$ref .= $liv->Reference."\n\n\n\n\n";
			$cod .= sprintf('%06d',$liv->Id);
			
			$rec = Sys::$Modules['Repertoire']->callData('Tiers/'.$liv->LivraisonId, false, 0, 1);
			$cli = genericClass::createInstance('Repertoire',$rec[0]);
			$adr = $cli->Intitule."\n".$cli->Adresse1."\n".$cli->Adresse2;
			$adr .= "\n".$cli->CodPostal.' '.$cli->Ville;
			$adr .= "\nTél. : ".$cli->Telephone;
			$x = 10;
			$this->BarCode($cod, "C128B", 13, $y+6, 200, 35, 0.3, 0.45, 2, 5, "", "PNG"); 
			$this->SetXY($x,$y);
			$this->MultiCell($this->width[0], 4, $ref, 'LB');
			$x += $this->width[0];
			$this->SetXY($x,$y);
			$this->MultiCell($this->width[1], 4, $adr, 'LB');
			$x += $this->width[1];
			$this->SetXY($x,$y);
			$this->Cell($this->width[2], 20, '', 'LRB', 0, $this->align[2]);
			$y += 20;
		}
	}

	function PrintBottom() {
		if($bottom) return;
		$bottom = true;
	}

}
