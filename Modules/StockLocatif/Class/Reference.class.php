<?php
class StockLocatifReference extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	/*
	 * 
	 */
	function PrintLabel($ids, $a4, $first=1) {
		if(! count($ids)) return WebService::WSStatus('method', 1, '', '', '', '', '', null, null);
		//if($a4) return $this->printLabelA4($ids, $first);
		//else return $this->printLabel10x15($ids);
		return $this->printLabel10x15($ids,$a4,$first);
	}
		
	private function printLabel10x15($ids,$laser,$first) {
		require_once('Class/Lib/pdfb1/pdfb.php');
		$pdf = new PDFB('L','mm', $laser ? array(210,297) : array(100,150));
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle('Etiquettes');
		$pdf->SetAutoPagebreak(false);
		if($first && $first <= 8) $i = $first - 1;
		else $i = 0;
		if($laser) $pdf->AddPage();
		foreach($ids as $id) {
			if($laser) {
				if($i >= 4) {
					$pdf->AddPage();
					$i = 0;
				}
				$x = ($i % 2) * (297 / 2) + 5;
				$y = floor($i / 2) * 105 + 7;
			}
			else {
				$pdf->SetAutoPagebreak(false);
				$pdf->AddPage();
				$x = 2;
				$y = 2;
			}
			$rec = Sys::$Modules['StockLocatif']->callData("Reference/$id",false,0,1);
			$r = $rec[0];
			$pdf->SetXY($x, $y);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(100,5,"Propriété de inaliénable et insaisissable de");
			$y += 6;
			$pdf->SetXY($x,$y);
			$pdf->SetFont('Arial','B',14);
			$pdf->Cell(70,5,"SARL LOC'ANIM",0,false,'C');
			$y += 5.5;
			$pdf->SetXY($x,$y);
			$pdf->SetFont('Arial','',14);
			$pdf->Cell(70,5,"30320 MARGUERITTES",0,false,'C');
			$y += 5.5;
			$pdf->SetXY($x,$y);
			$pdf->SetFont('Arial','B',14);
			$pdf->Cell(70,5,"Tél. : 0820.000.757",0,false,'C');
			$y += 5.5;
			$pdf->SetXY($x,$y);
			$pdf->SetFont('Arial','',12);
			$pdf->Cell(70,5,"RC Nîmes 418 043 006 00018",0,false,'C');
			$y += 10;
			$pdf->BarCode('+'.$r['Reference'], "C128B", $x, $y, 200, 40, 0.87, 1, 2, 5, "", "PNG");
			if($laser) $i++;
		}
		$file = 'Home/tmp/doc'.rand(0, 2000).'.pdf';
		$pdf->Output($file);
		$pdf->Close();
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), array(printFiles=>array($file)));
	}


	private function printLabelA4($ids, $first=1) {
		require_once('Class/Lib/pdfb1/pdfb.php');
		$pdf = new PDFB('P','mm','A4');
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle('Etiquettes');
		$pdf->AcceptPageBreak(false);
		if($first && $first <= 8) $i = $first - 1;
		else $i = 0;
		$pdf->AddPage();
		foreach($ids as $id) {
			$rec = Sys::$Modules['StockLocatif']->callData("Reference/$id",false,0,1);
			$r = $rec[0];
			if($i >= 8) {
				$pdf->AddPage();
				$i = 0;
			}
			$x = ($i % 2) * 105 + 5;
			$y = floor($i / 2) * (297 / 4) + 5;
//$GLOBALS["Systeme"]->Log->log("xxxxxxxx:$i:$x:$y");
			$pdf->SetXY($x, $y);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(70,4,"Propriété de inaliénable et insaisissable de");
			$y += 6;
			$pdf->SetXY($x,$y);
			$pdf->SetFont('Arial','B',12);
			$pdf->Cell(65,4,"SARL LOC'ANIM",0,false,'C');
			$y += 5;
			$pdf->SetXY($x,$y);
			$pdf->SetFont('Arial','',12);
			$pdf->Cell(65,4,"30320 MARGUERITTES",0,false,'C');
			$y += 5;
			$pdf->SetXY($x,$y);
			$pdf->SetFont('Arial','B',12);
			$pdf->Cell(65,4,"Tél. : 0820.000.757",0,false,'C');
			$y += 5;
			$pdf->SetXY($x,$y);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(65,4,"RC Nîmes 418 043 006 00018",0,false,'C');
			$y += 8;
			$pdf->BarCode('+'.$r['Reference'], "C128B", $x, $y, 200, 45, 0.6, 0.5, 2, 5, "", "PNG");
			$i++;
		}
		$file = 'Home/tmp/doc'.rand(0, 2000).'.pdf';
//$GLOBALS["Systeme"]->Log->log("333333333333:",$file);
		$pdf->Output($file);
		$pdf->Close();
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), array(printFiles=>array($file)));
	}

}
?>