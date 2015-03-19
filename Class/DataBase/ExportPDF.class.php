<?php

require_once('Class/Lib/pdfb1/pdfb.php');

class ExportPDF extends PDFB {
	
	private $name;
	private $header;
	private $width;
	private $align;
	private $format;
	private $field;
	private $posy;
	private $left = 5;
	
	
	function ExportPdf(&$cols,&$rec,$name,$fmt='A4') {
		$this->name = $name;
		$this->header = array();
		$this->width = array();
		$this->align = array();
		$this->format = array();
		$this->field = array();
		
		if(sizeof($rec)) $r = $rec[0];
		$w = 0;
		$n = count($cols);
		for($i = 0; $i < $n; $i++) {
			$c = $cols[$i];

			if($c[2] == 'image') {
				$f = $c[1].'_ToolTip';
				if(!r && !isset($r->$d)) continue;
				$this->field[] = $f;
			}
			else $this->field[] = $c[1];
			
			$this->header[] = $c[0] ? $c[0] : $c[1];
			$this->format[] = $c[2];
			if($c[2] == 'image') $l = 20;
			elseif($c[2] == 'progress') $l = 9;
			else $l = $c[3] / 4.5;
			$w += $l;
			$this->width[] = $l;

			switch($c[2]) {
				case 'checkbox':
				case 'boolean': $this->align[] = 'C'; break;
				case 'float':
				case '0dec':
				case '1dec':
				case '2dec':
				case '3dec':
				case '4dec':
				case '5dec': $this->align[] = 'R'; break;
				case 'progress': $this->align[] = 'R'; break;
				case 'date':
				case 'time':
				case 'longDate': $this->align[] = 'C'; break;
				default: $this->align[] = 'L';
			}
		}
		$o = $w < 200 ? 'P' : 'L';
		parent::__construct($o,'mm',$fmt);
		$this->AcceptPageBreak(true, 6);
	}
	
	function Header() {
		$this->SetFillColor(192,192,192);
		
		$y = 5;
		$this->SetFont('Arial','B',8);
		$this->SetXY($this->left, $y);
		$this->Cell(100, 6, $name.'   '.date('d/m/y'));
		$this->SetXY(210-20, $y);
		$this->Cell(30, 6, 'Page '.$this->PageNo(),'R');
		$y += 6;
		$this->SetXY($this->left, $y);
		$this->SetFont('Arial','B',8);
		$n = count($this->header);
		for($i = 0; $i < $n; $i++) {
			$this->Cell($this->width[$i], 6, $this->header[$i], 1, 0, $this->align[$i], true);
		}
		$this->posy = $y + 6;
		$this->SetXY($this->left, $this->posy);
	}
	
	function Footer() {
		$this->SetXY($this->left, $this->posy);
		$n = count($this->width);
		for($i = 0; $i < $n; $i++)
			$this->Cell($this->width[$i], 0.1, '', 'T', 0, $this->align[$i]);
	}
	
	function PrintLines(&$lines) {
		foreach($lines as $l) $this->printLine($l);
	}

	private function printLine($l) {
		$this->SetXY($this->left, $this->posy);
		$n = count($this->field);
		for($i = 0; $i < $n; $i++) {
			$f = $this->field[$i];
			$t = $l->$f;
			switch($this->format[$i]) {
				case 'checkbox':
				case 'boolean':
					break;
				case 'float':
					break;
				case '0dec': 
				case '1dec':
				case '2dec':
				case '3dec':
				case '4dec':
				case '5dec':
					if($t) $t = number_format($t,substr($this->format[$i],0,1),',',' ');
					else $t = '';
					break;
				case 'progress':
					$t .= '%';
					break;
				case 'date':
					if($t) $t = date('d/m/y', $t);
					else $t = '';
					break;
				case 'time':
					if($t) $t = date('d/m/y H:i', $t);
					else $t = '';
					break;
				case 'longDate':
					if($t) $t = date('d/m/Y', $t);
					else $t = '';
					break;
			}
			$c = $f.'_Color';
			if(isset($l->$c)) {
				$a = $this->splitRGB(hexdec($l->$c));
				$this->SetTextColor($a[0],$a[1],$a[2]);
			} 
			else $this->SetTextColor(0);
			$fill = 0;
			$c = $f.'_backgroundColor';
			if(isset($l->$c) && ! empty($l->$c)) {
				$a = $this->splitRGB(hexdec($l->$c));
				$this->SetFillColor($a[0],$a[1],$a[2]);
				$fill = 1;
			} 
			$this->Cell($this->width[$i], 5, $t, $i ? 'R' : 'LR', 0, $this->align[$i], $fill);
		}
		$this->posy += 5;
	} 
	
	private function splitRGB($c) {
		$a = array();
		$a[] = ($c & 0xff0000) >> 16;
		$a[] = ($c & 0x00ff00) >> 8;
		$a[] = ($c & 0x0000ff);
		return $a;
	}


}