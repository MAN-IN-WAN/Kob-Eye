<?php

//require_once('Class/Lib/pdfb1/pdfb.php');
require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');

class PrintLabels extends FPDF {
	
	private $labelX = 2;
	private $labelY = 8;
	private $width;
	private $height;
	private $col = 0;
	private $row = 0;
	private $posX = 0;
	private $posY = 0;

	function PrintLabels() {
		parent::__construct('P', 'mm', 'A4');
        $this->SetMargins(0,0); 
        $this->SetAutoPageBreak(false); 
		$this->width = 210/$this->labelX;
		$this->height = 297/$this->labelY;
	}

	private function cv($txt) {
		return iconv('UTF-8','ISO-8859-15//TRANSLIT',$txt);
	}
	
	function AddLabel($l) {
		$this->posX = ($this->width*$this->col)+5;
		$this->posY = ($this->height*$this->row)+5;
		$this->SetFont('Arial','B',11);
		$s = $this->cv($l['Prenom']);
		$w = $this->GetStringWidth($s);
		$this->SetXY($this->posX, $this->posY);
		$this->Cell($this->width,5,$s);
		$this->SetFont('Arial','B',12);
		$s = $this->cv($l['Nom']);
		$this->SetXY($this->posX+$w+2, $this->posY);
		$this->Cell($this->width,5,$s);
		$this->posY += 5.5;

		$this->SetFont('Arial','',12);
		$this->SetXY($this->posX, $this->posY);
		$this->Cell($this->width,5,$this->cv($l['Adresse1']));
		$this->posY += 5.5;
		$this->SetXY($this->posX, $this->posY);
		$this->Cell($this->width,5,$this->cv($l['Adresse2']));
		$this->posY += 5.5;

		$this->SetFont('Arial','B',12);
		$s = $this->cv($l['CP']);
		$w = $this->GetStringWidth($s);
		$this->SetXY($this->posX, $this->posY);
		$this->Cell($this->width,5,$s);
		$this->SetFont('Arial','',12);
		$s = $this->cv($l['Ville']);
		$this->SetXY($this->posX+$w+2, $this->posY);
		$this->Cell($this->width,5,$s);

		if(++$this->col == $this->labelX) {
			$this->col = 0;
			if(++$this->row == $this->labelY) {
				$this->row = 0;
				$this->AddPage();
			}
		}
	}


}