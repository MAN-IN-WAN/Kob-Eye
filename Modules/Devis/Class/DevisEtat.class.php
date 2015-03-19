<?php
require_once('Class/Lib/pdfb1/pdfb.php');

class DevisEtat extends PDFB {
	
	private $object;
	private $linesWidth;
	private $euro;
	private $commercial;
	private $facture;
	private $fond;
	private $picture;
	private	$header = array('Référence','Désignation','Quantité','P.U. Brut','Remise','Prix Net','T');
	private $width = array(23,98,14,23,14,23,4);
	private $align = array('L','L','R','R','R','R','C');
	private $posy;
	private $posTop;
	
	public $mode;
	
	
	function DevisEtat($obj,$fact,$fond,$orientation='P',$unit='mm',$format='A4') {
		parent::__construct($orientation,$unit,$format);
		$this->object = $obj;
		$this->facture = $fact;
		$this->fond = $fond;
		// commercial
		$cid = $obj->CommercialId;
		if($cid) {
			$rec = Sys::$Modules['Systeme']->callData("Systeme/User/$cid");
			$this->commercial = genericClass::createInstance('Systeme', $rec[0]);
		}
		$this->euro = '';
		$this->AcceptPageBreak(true, 30);
	}
	
	
	private function printConditions() {
		$this->Image('Modules/Devis/Class/conditions.jpg', 0, 0, 210, 297);
	}

	function Header() {
		if($this->mode == 2) {
			$this->printConditions();
			return;
		}
		$this->SetFillColor(192,192,192);
		if($this->fond) {
		 	$code = 'FOND_'.$this->object->Societe;
		 	$rec = Sys::$Modules['Devis']->callData('Constante/Code='.$code);
			$this->picture = 'Modules/Devis/Class/'.$rec[0]['Valeur'];
			$this->Image($this->picture, 0, 0, 210, 297);
		}
		// number
		$this->BarCode(($this->facture ? '$FA' : '$DV').sprintf('%06d',$this->object->Id), "C128B", 145, 5, 200, 35, 0.5, 0.5, 2, 5, "", "PNG");// 
		$y = 24;
		$this->SetXY(110,$y);
		$this->SetFont('Arial','',12);
		$this->Cell(22,6,$this->facture ? 'Facture N°' : 'Devis N°');
		$this->SetFont('Arial','B',12);
		$this->Cell(40,6,$this->object->Reference);
		$pag = $this->PageNo();
		if($pag > 1) {
			$this->SetFont('Arial','',12);
			$this->Cell(35, 6, 'Page '.$this->PageNo());
		}
		$y += 6;
		$this->SetXY(110,$y);
		$this->SetFont('Arial','',12);
		$this->Cell(22,6,'Date');
		$this->SetFont('Arial','B',12);
		$this->Cell(40,6,date('d.m.Y',$this->object->Date));
		$y += 6;
		$this->SetXY(110,$y);
		$this->SetFont('Arial','',12);
		$this->Cell(22,6,'Contact');
		//$this->SetFont('Arial','B',12);
		$this->Cell(40,6,$this->commercial->Initiales);
		// delivery address
		$y = 45;
		$this->SetXY(5, $y);
		$this->SetFont('Arial','B',8);
		$this->Cell(94,5,'Adresse de Livraison',1,0,'C',true);
		$y += 5;
		$this->SetXY(5, $y);
		$this->SetFont('Arial','B',10);
		$this->Cell(94,8,$this->object->LivraisonIntitule,'LR');
		$y += 5;
		$this->SetXY(5,$y);
		$this->SetFont('Arial','',10);
		$this->MultiCell(94,5,$this->object->LivraisonAdresse1."\n".$this->object->LivraisonAdresse2."\n".$this->object->LivraisonCodPostal.'  '.$this->object->LivraisonVille."\n",'LR'); // .$this->object->LivraisonAdresse3
		$y += 3*5;
		$this->SetXY(5, $y);
		$this->SetFont('Arial','B',10);
		$this->Cell(94,6,$this->object->LivraisonPays,'LRB');
		
		// invoice address
		$y = 45;
		$this->SetXY(110, $y);
		$this->SetFont('Arial','B',8);
		$this->Cell(94,5,'Adresse de Facturation',1,0,'C',true);
		$y += 5;
		$this->SetXY(110, $y);
		$this->SetFont('Arial','B',10);
		$this->Cell(94,8,$this->object->ClientIntitule,'LR');
		$y += 5;
		$this->SetXY(110,$y);
		$this->SetFont('Arial','',10);
		$this->MultiCell(94,5,$this->object->ClientAdresse1."\n".$this->object->ClientAdresse2."\n".$this->object->ClientCodPostal.'  '.$this->object->ClientVille."\n",'LR'); // .$this->object->ClientAdresse3
		$y += 3*5;
		$this->SetXY(110, $y);
		$this->SetFont('Arial','B',10);
		$this->Cell(94,6,$this->object->ClientPays,'LRB');

		$y += 7;

		$header = $this->header;
		$width = $this->width;
		$align = $this->align;
		$this->linesWidth = array_sum($width);
		$y = 80;
		$this->SetXY(5, $y);
		$this->SetFont('Arial','B',8);
		$n = count($header);
		for($i = 0; $i < $n; $i++)
			$this->Cell($width[$i], 6, $header[$i], 1, 0, $align[$i], true);
		$this->posTop = $this->posy = $y += 6;
		$this->SetXY(5, $y);
	}
	
	function PrintLines($lines) {
		if(! $this->facture) {
			if($this->object->Societe == 'B' && $this->object->DateDebut) {
				$this->SetFont('Arial','B',8);
				$this->printLine('',"CONTRAT LONGUE DUREE",0,0,0,0,'','C');
			}
			if($this->object->OperationId) {
				$rec = Sys::$Modules['Devis']->callData('OperationSpeciale/'.$this->object->OperationId);
				if(count($rec)) {
					$this->SetFont('Arial','B',8);
					$txt = $rec[0]['Designation'];
					if(! $this->object->DateDebut) $txt .= "\n";
					$this->printLine('',$txt,0,0,0,0,'','C');
				}
			}
			if($this->object->DateDebut) {
				$txt = 'Livraison le '.date('d/m/Y', $this->object->DateDebut);
				if($this->object->DateFin) $txt .= '  -  Reprise le '.date('d/m/Y', $this->object->DateFin);
				$txt .= "\n ";
				$this->SetFont('Arial','B',8);
				$this->printLine('',$txt,0,0,0,0,'','C');
			}
		}
		$this->SetFont('Arial','',8);
		foreach($lines as $rc) {
			$this->printLine($rc['Famille'],$rc['Designation'],$rc['Quantite'],$rc['PrixUnitaire'],$rc['Remise'],$rc['PrixNet'],$rc['CodeTVA'],'L');
		}

	}

	private function printLine($fam,$des,$qte,$pxu,$rem,$pxn,$tva,$aln) {
		$width = $this->width;
		$align = $this->align;
		$n = $this->NbLines($width[1], $des);
		$mh = $n * 4.5;
		$this->SetXY(5, $this->posy);
		$this->Cell($width[0], $mh, $fam, 'LR', 0, $align[0]);
		$this->MultiCell($width[1], 4.5, $des, 0, $aln);
		$this->SetXY(5 + $width[0] + $width[1], $this->posy);
		$this->Cell($width[2], $mh, $qte ? number_format($qte, 2) : '', 'L', 0, $align[2]);
		$this->Cell($width[3], $mh, $pxu ? number_format($pxu, 2) : '', 'L', 0, $align[3]);
		$this->Cell($width[4], $mh, $rem ? number_format($rem, 2).' %' : '', 'L', 0, $align[4]);
		$this->Cell($width[5], $mh, $pxn ? number_format($pxn, 2) : '', 'L', 0, $align[5]);
		$this->Cell($width[6], $mh, $tva, 'LR', 0, $align[6]);
		$this->posy += $mh;
	}

	function Footer() {
		if($this->mode) return;
		$width = $this->width;
		$h = 267 - $this->posy;
		if($h > 0) {
			$this->SetXY(5, $this->posy);
			for($i = 0; $i < 7; $i++)
				$this->Cell($width[$i], $h, '', $i == 6 ? 'LR' : 'L', 0);
				$this->posy += $h;
		}
		$this->SetXY(5, $this->posy);
		$this->Cell($this->linesWidth,0,'','T');
	}
	
	function PrintTotals() {
		if($this->facture) {
			if($this->GetY > 252) $this->AddPage();
		}
		elseif($this->posy > 171) $this->AddPage();
		$this->AcceptPageBreak(false);

		$width = $this->width;
		//$align = $this->align;
		$h = ($this->facture ? 154 : 85) - ($this->posy - $this->posTop);
		$this->SetXY(5, $this->posy);
		for($i = 0; $i < 7; $i++)
			$this->Cell($width[$i], $h, '', $i == 6 ? 'LR' : 'L', 0);
		$this->posy += $h;
		$this->SetXY(5, $this->posy);

		if(! $this->facture) {
			$this->SetFont('Arial','B',8);
			$txt = "Merci de nous retourner ce DEVIS pour confirmation\ntamponné et signé par FAX au 04.66.02.14.47\nou par mail : commercial@locanim.fr";
			$this->printLine('',$txt,0,0,0,0,'','C');
			$this->SetFont('Arial','',7);
			$txt = "Suivant les conditions générales de location";
			$this->printLine('',$txt,0,0,0,0,'','C');
		}
		$y = $this->posy;
		
		$this->SetXY(5, $y);
		$this->Cell($this->linesWidth,0,'','T');
		//
		if(! $this->facture) {
			$y += 6;
			$this->SetXY(5, $y);
			$this->SetFont('Arial','B',9);
			$txt = "NOM DU CHEF DE RAYON :";
			$this->MultiCell(198, 5, $txt);
			$y += 12;
			$this->SetXY(5, $y);
			$this->SetFont('Arial','',8);
			$txt = "Cachet et signature précédé de la mention :\n\"BON POUR ACCORD\"\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
			$this->MultiCell(64, 4, $txt, 1, 'C'); 
		}
		if($this->facture && $this->Bopi) {
			$y = 266;
			$regl = 'N° INTRACOM. FR25 418 04 3006  ';
			$regl .= 'Valeur en votre aimable règlement le '.date('d/m/Y',$this->object->Echeance);
			$regl .= "\nLe soussigné reconnait avoir pris connaissance et accepté les conditions générales de location";
			$this->SetXY(5, $y);
			$this->SetFont('Arial','I',8);
			$this->MultiCell(200, 4, $regl);
		}

		if(!$this->facture && $this->object->NombreEcheance > 1) $this->printDueDates();
		else {
			// TVA
			$ctva = $this->object->CodeTva;
			$rec = Sys::$Modules['Devis']->callData("TVA/$ctva");
			$ttva = genericClass::createInstance('Devis', $rec[0]);
			// lines
			$this->SetFillColor(192,192,192);
			$header = array('Montant HT Brut','Remise','Montant HT Net','T','TVA','Montant TVA','Montant TTC');
			$width = array(25,14,25,5,14,25,25);
			$align = array('R','R','R','C','R','R','R');
			$y = 252;
			$x = (5 + $this->linesWidth) - array_sum($width);
			$this->SetXY($x, $y);
			$this->SetFont('Arial','B',8);
			$n = count($header);
			for($i = 0; $i < $n; $i++)
				$this->Cell($width[$i], 6, $header[$i], 1, 0, $align[$i], true);
			$y += 6;
			$this->SetXY($x, $y);
			$this->SetFont('Arial','',8);
			$this->Cell($width[0], 10, $this->object->MontantHTBrut ? $this->euro.number_format($this->object->MontantHTBrut,2) : '', 1, 0, $align[0]);
			$this->Cell($width[1], 10, $this->object->RemiseTaux ? number_format($this->object->RemiseTaux,2).'%' : '', 1, 0, $align[1]);
			$this->SetFont('Arial','B',8);
			$this->Cell($width[2], 10, $this->object->MontantHTNet ? $this->euro.number_format($this->object->MontantHTNet,2) : '', 1, 0, $align[2]);
			$this->SetFont('Arial','',8);
			$this->Cell($width[3], 10, $ctva, 1, 0, $align[3]);
			$this->Cell($width[4], 10, $ttva->Taux ? number_format($ttva->Taux,2).' %' : '', 1, 0, $align[4]);
			$this->Cell($width[5], 10, $this->object->MontantTVA ? $this->euro.number_format($this->object->MontantTVA,2) : '', 1, 0, $align[5]);
			$this->SetFont('Arial','B',10);
			$this->Cell($width[6], 10, $this->object->MontantTTC ? $this->euro.number_format($this->object->MontantTTC,2) : '', 1, 0, $align[6]);
			$y += 9;
			$this->SetXY($x, $y);
			$this->SetFont('Arial','I',7);
			$this->Cell(90,6,'Tous les montants sont exprimés en Euro.');
		}
		$this->mode = 1;
	}

	private function printDueDates() {
		$tab = array();
		$eche = $this->object->DateDebut;
		if(date('j', $eche) >= 25) $eche = strtotime("+1 month", $eche);
		$tab[] = array($eche, $this->object->PremiereEcheance);
		$der = $this->object->DerniereEcheance;
		$aut = $this->object->AutresEcheance;
		$n = $this->object->NombreEcheance;
		for($i = ($der ? 2 : 1); $i < $n; $i++) {
			$eche = strtotime("+1 month", $eche);
			$tab[] = array($eche, $aut);
		}
		if($der) {
			$eche = strtotime("+1 month", $eche);
			$tab[] = array($eche, $der);
		}
		$n = count($tab);
		while($n++ < 12) $tab[] = null;

		$this->SetFillColor(192,192,192);
		$month = array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre');
		$header = array('Date','Montant HT','Date','Montant HT','Date','Montant HT');
		$width = array(24,20,24,20,25,20);
		$align = array('L','R','L','R','L','R');
		$y = 237;
		$x = (5 + $this->linesWidth) - array_sum($width);
		$this->SetXY($x, $y);
		$this->SetFont('Arial','B',10);
		$this->Cell(90,5,'ECHEANCES HORS TAXES');
		$y += 5;
		$this->SetXY($x, $y);
		$this->SetFont('Arial','',8);
		$this->Cell($width[0], 6, $header[0], 'LTB', 0, $align[0], true);
		$this->Cell($width[1], 6, $header[1], 'TB', 0, $align[1], true);
		$this->Cell($width[2], 6, $header[2], 'LTB', 0, $align[2], true);
		$this->Cell($width[3], 6, $header[3], 'TB', 0, $align[3], true);
		$this->Cell($width[4], 6, $header[4], 'LTB', 0, $align[4], true);
		$this->Cell($width[5], 6, $header[5], 'TBR', 0, $align[5], true);
		$y += 6;
		$this->SetXY($x, $y);
		$this->SetFont('Arial','',8);
		$n = 0;
		for($i = 0; $i < 4; $i++) {
			for($j = 0; $j < 6; $j += 2) {
				$eche = $mont = '';
				if($tab[$n]) {
					$m = date('n', $tab[$n][0]) - 1;
					$eche = $month[$m].' '.date('Y', $tab[$n][0]);
					$mont = number_format($tab[$n][1], 2);
				}
				$this->Cell($width[$j], 5, $eche,'LB',0,'');
				$this->Cell($width[$j+1], 5, $mont, 'RB', 0, 'R');
				$n++;
			}
			$y += 5;
			$this->SetXY($x, $y);
			if(! $eche) break;
		}
		$this->SetXY($x, $y);
		$this->SetFont('Arial','I',7);
		$this->Cell(90,6,'Tous les montants sont exprimés en Euro.');
	}


	function NbLines($w, $txt)
	{
	    //Computes the number of lines a MultiCell of width w will take
	    $cw=&$this->CurrentFont['cw'];
	    if($w==0)
	        $w=$this->w-$this->rMargin-$this->x;
	    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	    $s=str_replace("\r", '', $txt);
	    $nb=strlen($s);
	    if($nb>0 and $s[$nb-1]=="\n")
	        $nb--;
	    $sep=-1;
	    $i=0;
	    $j=0;
	    $l=0;
	    $nl=1;
	    while($i<$nb)
	    {
	        $c=$s[$i];
	        if($c=="\n")
	        {
	            $i++;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	            continue;
	        }
	        if($c==' ')
	            $sep=$i;
	        $l+=$cw[$c];
	        if($l>$wmax)
	        {
	            if($sep==-1)
	            {
	                if($i==$j)
	                    $i++;
	            }
	            else
	                $i=$sep+1;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	        }
	        else
	            $i++;
	    }
	    return $nl;
	}

}