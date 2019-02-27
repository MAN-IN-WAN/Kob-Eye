<?php

require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');
require_once('nuts.class.php');

class PrintAttestation extends FPDF {
	
	private $anCotis;
	private $anFisc;
	private $date;
	private $left = 5;
	
	
	function PrintAttestation($anneeCotis, $anneeFisc) {
		parent::__construct('P', 'mm', 'A4');
		$this->anCotis = $anneeCotis.'-'.($anneeCotis+1);
		$this->anFisc = $anneeFisc;
		$mois = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
		$d = date('d/m/Y');
		$this->dateText = $this->cv("Nîmes, le ".substr($d,0,2).' '.$mois[substr($d,3,2)-1].' '.substr($d,6,4));
	}
	
	private function cv($txt) {
		return iconv('UTF-8','ISO-8859-15//TRANSLIT',$txt);
	}

	function Header() {
	}
	
	function Footer() {
	}
	
	function PrintPage($l) {
		$this->AddPage();
		$this->SetFillColor(192,192,192);
		
		$img = getcwd().'/Skins/AngularAdmin/Modules/Cadref/assets/img/cadref_logo_gris.png';
		$this->Image($img,20,21,26,30);
		
		$this->SetFont('Arial','B',12);
		$this->SetXY(-120,21);
		$s = $this->cv("Université de la Culture Permanente");
		$this->Cell(100,5,$s,0,0,'R');
		$this->SetFont('Arial','',14);
		$this->SetXY(-120,40);
		$s = $this->cv("CADREF DU GARD");
		$this->Cell(100,6,$s,0,0,'R');
		$this->SetFont('Arial','B',10);
		$this->SetXY(20,52);
		$s = $this->cv("REÇU AU TITRE DE DON OU PARTICIPATION");
		$this->Cell(170,5,$s,0,0,'C');

		$this->SetFont('Arial','B',10);
		$this->SetXY(20,65);
		$s = $this->cv("BÉNÉFICIAIRE");
		$this->Cell(170,8,$s,0,0,'C',1);
		
		$this->SetFont('Arial','B',10);
		$this->SetXY(20,76);
		$s = $this->cv("L'UNIVERSITÉ DE LA CULTURE PERMANENTE CADREF DU GARD");
		$this->Cell(170,4,$s,0,1,'L');
		$this->SetFont('Arial','',10);
		$this->SetX(20);
		$s = $this->cv("249 rue de Bouillargues");
		$this->Cell(170,4,$s,0,1,'L');
		$this->SetX(20);
		$s = $this->cv("30000 NÎMES");
		$this->Cell(170,4,$s,0,0,'L');
		
		$this->SetFont('Arial','B',10);
		$this->SetXY(20,95);
		$s = $this->cv("AYANT POUR OBJET :");
		$this->Cell(170,4,$s,0,1,'L');
		$this->SetFont('Arial','',10);
		$this->SetX(20);
		$s = "Activité intellectuelles, physiques, ludiques... au travers de cours, pour favoriser l'entretien des ";
		$s .= "capacités physiques et intellectuelles des adhérents, favoriser le lien social entre générations, ";
		$s .= "et lutter contre l'isolement social...";
		$s = $this->cv($s);
		$this->MultiCell(170,4,$s);
		
		$this->SetFont('Arial','B',10);
		$this->SetXY(20,119);
		$s = $this->cv("ASSOCIATION D'INTÉRÊT GÉNÉRAL");
		$this->Cell(170,4,$s,0,1,'L');
		$this->SetFont('Arial','',10);
		$this->SetX(20);
		$s = "Déclarée d'intérêt général le 9 juin 2017 par la Direction Départementale des Finances du Gard ";
		$s .= "notifiant la décision du Collège territorial de Toulouse.";
		$s = $this->cv($s);
		$this->MultiCell(170,4,$s);
		
		$this->SetFont('Arial','B',10);
		$this->SetXY(20,140);
		$s = $this->cv("DONATEUR");
		$this->Cell(170,8,$s,0,0,'C',1);
		
		$this->SetFont('Arial','',12);
		$this->SetXY(20,152);
		$this->Cell(170,4,$l['Numero'],0,0,'L');
		
		$this->SetFont('Arial','B',16);
		$s = $this->cv($l['Prenom']);
		$w = $this->GetStringWidth($s);
		$this->SetXY(-($w+20),152);
		$this->Cell($w+0.5,8,$s,0,0,'R');
		
		$this->SetFont('Arial','B',20);
		$s = $this->cv($l['Nom']);
		$this->SetXY(-($w+20+120),152);
		$this->Cell(117,8,$s,0,1,'R');
		
		$this->SetFont('Arial','',12);
		$s = $this->cv($l['Adresse1']);
		$this->SetX(-140);
		$this->Cell(120,4.5,$s,0,1,'R');
		$s = $this->cv($l['Adresse2']);
		$this->SetX(-140);
		$this->Cell(120,4.5,$s,0,1,'R');
		$s = $this->cv($l['CP'].'  '.$l['Ville']);
		$this->SetX(-140);
		$this->Cell(120,4.5,$s,0,0,'R');
		
		
		$s = "LE BÉNÉFICIAIRE reconnaît avoir reçu en titre de dons et versements ouvrant droit à réduction ";
		$s .= "d'impôt pour l'année fiscale $this->anFisc (cotisation $this->anCotis), la somme de :";
		$this->SetFont('Arial','B',10);
		$s = $this->cv($s);
		$this->SetXY(20,181);
		$this->MultiCell(170,4,$s);

		$nuts = new nuts($l['Cotisation'], 'EUR');
		$text = strtoupper($nuts->convert("fr-FR"));
		$nbre = strtoupper($nuts->getFormated(" ", ",", "fr-FR"));

		$this->SetXY(20,194);
		$this->Cell(170,4,$nbre,0,1,'C');
		$this->SetX(20);
		$this->Cell(170,4,$text,0,0,'C');

		$s = "Le bénéficiaire certifie que les dons et versements ouvrent droit à la réduction d’impôt prévue par ";
		$s .= "l’article 200 du Code Général des Impôts.\n\n";
		$s .= "Le versement est un don en numéraire fait sous la forme de chèques, d’espèces, par carte bancaire ou par virements.";
		$this->SetFont('Arial','',10);
		$s = $this->cv($s);
		$this->SetXY(20,208);
		$this->MultiCell(170,4,$s);

		$this->SetXY(110,234);
		$this->Cell(90,6,$this->dateText,0,0,'L');
		$s = $this->cv("Le président du Conseil d'Administration");
		$this->SetXY(110,240);
		$this->Cell(90,6,$s,0,0,'L');	
	}
	
}