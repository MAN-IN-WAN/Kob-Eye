<?php

require_once('Class/Lib/pdfb1/pdfb.php');

class PrintRapport extends PDFB {

	private $posy;
	private $prj;

	function PrintRapport($project, $orientation='P',$unit='mm',$format='A4') {
		parent::__construct($orientation,$unit,$format);
		$this->AcceptPageBreak(true, 10);
		$this->prj = $project;
	}
	
	function Header() {
		$this->SetFont('Arial','B',14);
		$this->SetXY(10, 5);
		$this->Cell(190, 8, $this->prj->Description, 0, 0, 'C');
		$this->SetFont('Arial','',10);
		$this->SetX(190);
		$this->Cell(30, 8, 'Page '.$this->PageNo(),'R');
		$this->Ln(10);
	}

	function PrintPages() {
		$this->AddPage();
		$pgs = Sys::getData('QCM', 'QCM/Projet/'.$this->prj->Id.'/Page');
		foreach($pgs as $pg) {
			$this->printQuestions($pg);
		}
	}
	
	private function printQuestions($pg) {
		$qs = Sys::getData('QCM', 'QCM/Page/'.$pg->Id.'/Question');
		foreach($qs as $q) {
			$this->SetFont('Arial',$q->Gras ? 'B' : '',10);
			$this->SetX(15);
			$this->MultiCell(190, 4, trim($q->Question));
			$this->printReponses($q);
		}
	}

	private function printReponses($q) {
		$this->SetFont('Arial','',10);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(192,192,192);

		if($q->DictionnaireId) $rs = Sys::getData('QCM', 'Dictionnaire/'.$q->DictionnaireId.'/Entree');
		else $rs = Sys::getData('QCM', 'Question/'.$q->Id.'/Reponse');
		
		$res = Sys::getData('QCM', 'Projet/'.$this->prj->Id.'/Participation/*/Resultat/QuestionId='.$q->Id);
		$cn = count($res);
		
		switch($q->Icon) {
			case 'mx_textInput':
				$this->Ln(1.5);
				foreach($res as $re) {
					$tmp = $re->Reponse;
					$this->SetX(15);
					$this->Cell(190, 6, $tmp, 1);
					$this->Ln();
				}
				$this->Ln(2);
				break;
			case 'mx_textArea':
				$this->Ln(1);
				foreach($res as $re) {
					$this->SetX(15);
					$tmp = $re->Reponse;
					$this->MultiCell(190, 3, $tmp, 1);
					$this->Ln();
					$this->Ln(1);
				}
				break;
			case 'mx_number':
			case 'mx_hSlider':
				$av = 0;
				foreach($res as $re) $av += $re->Reponse;
				$tmp = 'Moyenne : '.round($av/($cn*($q->Reponse>0 ? $q->Reponse : 10))*100,0);
				$this->SetX(15);
				$this->Cell(190, 6, $tmp);
				$this->Ln(8);
				break;
			case 'mx_radioButton':
			case 'mx_comboBox':
			case 'mx_checkBox':
				$av = array();
				foreach($res as $re) $av[$re->ReponseId]++;
				
				$this->Ln(2.8);
				$x = 16;
				$y = $this->GetY();
				foreach($rs as $r) {
					$tmp = round($av[$r->Id]/$cn*100,0).' %';
					$this->SetXY($x, $y);
					$this->Cell(12, 3, $tmp, 0, 0, 'R');
					$this->SetXY($x+16, $y);
					$this->MultiCell(170, 3, $r->Reponse);
					$this->Ln();
					$y = $this->GetY();
				}
				$this->Ln(5);
				break;
		}
	}
}

