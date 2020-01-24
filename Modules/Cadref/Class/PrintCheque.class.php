<?php

require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');
require_once('nuts.class.php');

class PrintCheque extends FPDF {

	private $date;
	private $dateText;
	
	function PrintCheque() {
		parent::__construct('P', 'mm', 'A4');
		
		$mois = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
		$d = date('d/m/Y');
		$this->date = $d;
		$this->dateText = $this->cv("Nîmes, le ".substr($d,0,2).' '.$mois[substr($d,3,2)-1].' '.substr($d,6,4));
	}
	
	private function cv($txt) {
		return iconv('UTF-8','ISO-8859-15//TRANSLIT',$txt);
	}

	function Header() {
	}
	
	function Footer() {
	}
	
	function PrintPage($l, $params) {
		$this->AddPage();
		
		$img = getcwd().'/Skins/AngularAdmin/Modules/Cadref/assets/img/cadref_logo_noir.png';
		$this->Image($img,25,19,26,30);
		$this->SetFont('Arial','',10);
		$this->SetXY(20, 51);
		$this->Cell(37, 4, Cadref::$WEB, 0, 1, 'C');
		$this->SetX(20);
		$this->Cell(37, 4, Cadref::$MAIL, 0, 0, 'C');
		
		$this->SetFont('Arial','B',20);
		$this->SetXY(140, 15);
		$this->Cell(50, 10, 'C.A.D.R.E.F.', 0, 1);
		$this->SetFont('Arial','',12);
		$h = 5;
		$this->SetX(140);
		$this->Cell(50, $h, '249 rue de Bouillargues', 0, 1);
		$this->SetX(140);
		$this->Cell(50, $h, $this->cv('30000  NÎMES'), 0, 1);
		$this->SetX(140);
		$this->Cell(50, $h, $this->cv('Téléphone : 04 66 36 99 44'));
		
		$this->SetFont('Arial','B',12);
		$nom = '';
		if($l) {
			switch($l['Sexe']) {
				case 'H':
					$nom = 'M '; break;
				case 'F':
					$nom = 'Mme '; break;
			}
			$nom .= $l['Nom'];
		}
		else {
			$nom = $params['Civilite'];
			if($nom) $nom .= ' ';
			$nom .= $params['Nom'];
		}
		$nom = $this->cv($nom);
		$w = $this->GetStringWidth($nom);
		$h = 6;
		$this->SetXY(104, 55);
		$this->Cell($w, $h, $nom, 0, 0);
		$this->SetFont('Arial','B',10);
		$this->SetXY(104+$w+1, 55);
		$p = $this->cv($l ? $l['Prenom'] : $params['Prenom']);
		$this->Cell(104, $h, $p, 0, 1);
		$nom .= ' '.$p;
		$this->SetFont('Arial','B',12);
		$this->SetX(104);
		$this->Cell(104, $h, $this->cv($l ? $l['Adresse1'] : $params['Adresse1']), 0, 1);
		$this->SetX(104);
		$this->Cell(104, $h, $this->cv($l ? $l['Adresse2'] : $params['Adresse2']), 0, 1);
		$this->SetX(104);
		$this->Cell(104, $h, $this->cv($l ? $l['CP'].'  '.$l['Ville'] : $params['Ville']));
		
		$this->SetFont('Arial','',12);
		$this->SetXY(20, 100);
		$this->Cell(80, 6, $this->cv('Objet :  '.$params['Objet']));
		$this->SetXY(104, 100);
		$this->Cell(80, 6, $this->dateText);

		$nuts = new nuts($params['Montant'], 'EUR');
		$text = strtoupper($nuts->convert("fr-FR"));
		$nbre = $nuts->getFormated(" ", ",", "fr-FR");
		
		$h = 4.8;
		$this->SetXY(20, 112);
		$this->MultiCell(170, $h, "Madame, Monsieur,\n\n");
		$this->SetX(20);
		$s = $this->cv('Je vous prie de trouver ci-joint un chèque barré de');
		$w = $this->GetStringWidth($s);
		$this->Cell($w, $h, $s, 0, 0);
		$this->SetFont('Arial','B',12);
		$this->SetX(20+$w+1);
		$this->Cell(170-$w, $h, $nbre, 0, 1);
		$this->SetFont('Arial','',12);
		$this->SetX(20);
		$s = "établi à votre ordre sur le Crédit Agricole à Nîmes, en remboursement de :\n\n";
		$this->MultiCell(170, $h, $this->cv($s));
		$this->SetFont('Arial','B',12);
		$this->SetX(20);
		$this->MultiCell(170, $h, $this->cv($params['Description'])."\n\n", 0, 'C');
		$this->SetFont('Arial','',12);
		$this->SetX(20);
		$s = "Vous en souhaitant bonne réception, je vous prie d'agréer, Madame, Monsieur, l'expression de mes sentiments distingués";
		$this->MultiCell(170, $h, $this->cv($s));
		
		$this->SetXY(125,180);
		//$s = "Nathalie FAUCHER\nDirectrice du CADREF";
		$p = Cadref::GetParametre('DOCUMENT', 'CHEQUE', 'SIGNATURE');
		$this->MultiCell(45, 6, $this->cv($p->Texte));
		
		$this->SetFont('Arial','',10);
		$this->SetXY(65,234.5);
		$this->MultiCell(80, 4, '* '.$text.' *');
		$this->SetXY(43,246);
		$this->Cell(101, 4, $nom);
		$s = $nbre = $nuts->getFormated(" ", ",");
		$p = strpos($s, ' EUR');
		$s = substr($s, 0, $p);
		$this->SetFont('Arial','B',12);
		$this->SetXY(166, 244);
		$this->Cell(35, 4, '* '.$s.' *');
		$this->SetFont('Arial','',10);
		$this->SetXY(168, 253);
		$this->Cell(35, 4, $this->cv('Nîmes'));
		$this->SetXY(168, 259);
		$this->Cell(35, 4, $this->date);
	}
	
}