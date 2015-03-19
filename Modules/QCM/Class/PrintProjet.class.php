<?php

require_once('Class/Lib/fpdf_1_7/fpdf.php');

class PrintProjet extends FPDF {

	private $posy;
	private $prj;
	private $par;
	private $rep;
	private $not;
	private $ptot;
	private $pnum;

	function PrintProjet($project, $participation, $reponse, $orientation='P',$unit='mm',$format='A4') {
		parent::__construct($orientation,$unit,$format);
		$this->AcceptPageBreak(true, 10);
		$this->prj = $project;
		$this->par = $participation;
		$this->rep = $participation ? true : $reponse;
		
		$pgs = Sys::getData('QCM', 'QCM/Projet/'.$this->prj->Id.'/Page');
		foreach($pgs as $pg) {
			$qs = Sys::getData('QCM', 'QCM/Page/'.$pg->Id.'/Question');
			foreach($qs as $q) {
				if($q->TypeQuestionId == 1 || $q->TypeQuestionId == 2 || $q->TypeQuestionId == 3 || $q->TypeQuestionId == 5 || $q->TypeQuestionId == 6) $this->not++;
			}
		}
		
	}

	private function cv($txt) {
		return iconv('UTF-8','ISO-8859-15//TRANSLIT',$txt);
	}
	
	function Header() {
		$this->SetFont('Arial','B',14);
		$this->SetXY(10, 5);
		$this->Image(QCMLOGO, 10, 5, null, 10);
		$this->Cell(190, 8, $this->cv($this->prj->Description), 0, 0, 'C');
		$this->SetFont('Arial','',10);
		$this->SetX(190);
		$this->Cell(30, 8, 'Page '.$this->PageNo(),'R');
		$this->Ln(10);
		if($this->PageNo() == 1) {
			$y = $this->GetY();
			$this->SetX(10);
			$this->SetFont('Arial','',10);
			$this->Cell(12, 5, 'Nom :', 'LT');
			$tmp = '';
			if($this->par) $tmp = $this->par->Prenom.' '.$this->par->NomFamille;
			$this->SetFont('Arial','B',10);
			$this->Cell(120, 5, $this->cv($tmp), 'T');
			$this->Ln();
			
			$this->SetX(10);
			$this->SetFont('Arial','',10);
			$this->Cell(12, 5, 'Date :', 'L');
			$tmp = '';
			if($this->par && $this->par->DateValidation) $tmp = date('d/m/Y', $this->par->DateValidation);
			$this->SetFont('Arial','B',10);
			$this->Cell(120, 5, $tmp, 0);
			$this->Ln();

			$this->SetX(10);
			$this->SetFont('Arial','',10);
			$this->Cell(12, 5, 'Note :', 'LB');
			$tmp = '';
			if($this->par) $tmp = $this->par->Note.'/'.$this->not;
			$this->SetFont('Arial','B',10);
			$this->Cell(120, 5, $tmp, 'B');
			$this->Ln();

			$this->SetXY(142,$y);
			$this->SetFont('Arial','',10);
			$this->MultiCell(58, 5, "Signature :\n\n\n", 1);
			$this->Ln(2);
			
			$this->SetDrawColor(0, 0, 0);
			$this->SetFillColor(192,192,192);
			$this->SetFont('Arial','I',10);
			$x = 10;
			$y = $this->GetY();
			$this->Rect($x, $y, 2.8, 2.8, 'DF');
			$this->SetX($x + 4);
			$this->Cell(50, 5, $this->cv("Réponses attendues"));
			$x = 50;
			$this->Rect($x, $y, 2.8, 2.8, 'D');
			$this->Line($x, $y, $x+2.8, $y+2.8);
			$this->Line($x+2.8, $y, $x, $y+2.8);
			$this->SetX($x + 4);
			$this->Cell(50, 5, $this->cv("Réponses fournies"));
			$this->Ln(6);
		}
	}

	function PrintPages() {
		$pgs = Sys::getData('QCM', 'QCM/Projet/'.$this->prj->Id.'/Page');
		$this->ptot = count($pgs);
		$this->pnum = 0;
		foreach($pgs as $pg) {
			$this->pnum++;
			$this->AddPage();
			$this->SetFont('Arial','B',12);
			$this->SetX(10);
			$this->Cell(190, 8, $this->cv($pg->Nom).' ('.$this->pnum.'/'.$this->ptot.')');
			$this->Ln(10);
			$this->printQuestions($pg);
		}
	}
	
	private function printQuestions($pg) {
		$qs = Sys::getData('QCM', 'QCM/Page/'.$pg->Id.'/Question');
		foreach($qs as $q) {
			$this->SetFont('Arial',$q->Gras ? 'B' : '',10);
			$this->SetX(15);
			if($q->Icon != 'mx_formHeading') $this->Cell(6, 4, $q->Numero);
			$this->MultiCell(190, 4, $this->cv(trim($q->Question)));
			if(trim($q->Explication)) {
				$this->SetFont('Arial','I',9);
				$this->SetX(15);
				$this->MultiCell(190, 3.6, $this->cv(trim($q->Explication)));
			}
			if($q->Image) {
				$this->Ln(2);
				$this->SetX(15);
				$img = $q->Image;
				$dim = getimagesize($img);
				$this->Image($img, $this->GetX(), $this->GetY(), -96);
				$this->Ln($dim[1]/96*25.4+2);
			}
			$this->printReponses($q);
		}
	}

	private function printReponses($q) {
		$this->SetFont('Arial','',10);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(192,192,192);

		if($q->DictionnaireId) $rs = Sys::getData('QCM', 'Dictionnaire/'.$q->DictionnaireId.'/Entree');
		else $rs = Sys::getData('QCM', 'Question/'.$q->Id.'/Reponse');
		if($this->par) {
			$ts = Sys::getData('QCM', 'Participation/'.$this->par->Id.'/Resultat/QuestionId='.$q->Id);
		}
		switch($q->Icon) {
			case 'mx_number':
			case 'mx_textInput':
				$this->Ln(1.5);
				$tmp = '';
				if($this->rep) $tmp = 'Attendu : '.$q->Reponse;
				if(sizeof($ts)) $tmp .= '      Répondu : '.$ts[0]->Reponse;
				$this->SetX(15);
				$this->Cell(190, 8, $this->cv($tmp), 1);
				$this->Ln(14);
				break;
			case 'mx_textArea':
				$this->Ln(1);
				$tmp = sizeof($ts) ? trim($ts[0]->Reponse) : "\n\n\n\n";
				$this->SetX(15);
				$this->MultiCell(190, 6, $this->cv($tmp), 1);
				$this->Ln(30);
				break;
			case 'mx_hSlider':
				$tmp = 'Valeur de 0 à '.($q->Reponse > 0 ? $q->Reponse : 10).' : ';
				if(sizeof($ts)) $tmp .= '      Répondu : '.$ts[0]->Reponse;
				$this->SetX(15);
				$this->Cell(190, 8, $this->cv($tmp), 1);
				$this->Ln(14);
				break;
			case 'mx_radioButton':
			case 'mx_comboBox':
			case 'mx_checkBox':
				$this->Ln(2.8);
				$x = 16;
				$y = $this->GetY();
				$maxh = 0;
				foreach($rs as $r) {
					if($q->Horizontal && $x+4+$this->GetStringWidth($r->Reponse) > 200) {
						$x = 16;
						$this->Ln();
						$y = $this->GetY();
					}
					$tmp = $this->rep && $r->BonneReponse ? 'DF' : 'D'; 
					$this->Rect($x, $y, 2.8, 2.8, $tmp);
					if(sizeof($ts)) {
						foreach($ts as $t) {
							if($t->ReponseId == $r->Id) {
								$this->Line($x, $y, $x+2.8, $y+2.8);
								$this->Line($x+2.8, $y, $x, $y+2.8);
							}
						}
					}
					$this->SetXY($x + 4, $y);
					if($r->Image) {
						$img = $r->Image;
						$dim = getimagesize($img);
						$this->Image($img, $this->GetX(), $this->GetY(), -96);
						$x = $this->GetX()+$dim[0]/96*25.4;
						$this->SetX($x);
						$h = $dim[1]/96*25.4;
						if($h > $maxh) $maxh = $h;
					}
					$this->MultiCell(190, 3, $this->cv($r->Reponse));
					if($q->Horizontal) {
						$x += 12 + $this->GetStringWidth($this->cv($r->Reponse));
					}
					else {
						if($maxh) $this->Ln($maxh);
						$maxh = 0;
						$this->Ln();
						$y = $this->GetY();
					}
				}
				if($maxh) $this->Ln($maxh);
				$this->Ln(5);
				break;
		}
	}
}

