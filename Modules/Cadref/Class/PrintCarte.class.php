<?php

//require_once('Class/Lib/pdfb1/pdfb.php');
require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');

class PrintCarte extends FPDF {
	
	private $recto;
	private $adh;
	private $aan;
	private $width;
	private $align;
	private $head;
	private $posy;
	private $left = 5;
	
	
	function PrintCarte($adherent, $adhAnnee, $recto=false) {
		parent::__construct('P', 'mm', 'A4');
		$this->adh = $adherent;
		$this->aan = $adhAnnee;
		$this->recto = $recto;
		$this->head = array('Cours','Discipline','Horaires','Enseignant','Tarif');
		$this->width = array(16,76,41,60,8);
		$this->align = array('L','L','L','L','R');
	}
	
	private function cv($txt) {
		return iconv('UTF-8','ISO-8859-15//TRANSLIT',$txt);
	}

	function Header() {
		$annee = $GLOBALS['Systeme']->getRegVars('AnneeEnCours');
		$adh = $this->adh;
		$y = 5;
		$this->SetFont('Arial','B',12);
		$this->SetXY($this->left, $y);
		$w = $this->GetStringWidth($this->cv($adh->Nom));
		$this->Cell($w+2, 5, $this->cv($adh->Nom));
		$this->SetFont('Arial','B',10);
		$this->Cell(100, 5, $this->cv($adh->Prenom));
		
		$this->SetFont('Arial','B',12);
		$this->SetXY(-24, $y);
		$this->Cell(20, 5, $this->cv('N° '.$adh->Numero), 0, 0, 'R');
		$y += 4;
		
		$this->SetFont('Arial','',10);
		$this->SetXY($this->left, $y);
		$this->Cell(150, 5, $this->cv($adh->Adresse1));
		$y += 4;
		
		$this->SetXY($this->left, $y);
		$this->Cell(150, 5, $this->cv($adh->Adresse2));
		
		$this->SetFont('Arial','B',12);
		$this->SetXY(-24, $y);
		$this->Cell(20, 5, $annee.'-'.($annee+1), 0, 0, 'R');
		$y += 4;
		
		$this->SetFont('Arial','',10);
		$this->SetXY($this->left, $y);
		$this->Cell(150, 5, $this->cv($adh->CP.' '.$adh->Ville));
		
		$this->SetXY(-34, $y);
		$this->Cell(30, 5, $this->cv('* Cotisation : '.$this->aan->Cotisation), 0, 0, 'R');
		$y += 5;
		
		$this->SetXY($this->left, $y);
		$this->SetFont('Arial','BU',8);
		$n = count($this->head);
		for($i = 0; $i < $n; $i++)
			$this->Cell($this->width[$i], 5, $this->cv($this->head[$i]), 0, 0, $this->align[$i]);
	
		$this->posy = $y+4;
	}
	
	function Footer() {
		$this->SetFont('Arial','',8);
		$this->SetXY($this->letf, 90);
		$this->Cell(200, 5, $this->cv('* Le reçu fiscal pour la cotisation vous sera délivré courant février.'), 0, 0, 'C');
		if(! $this->recto) return;
		
		$t = 99;  // tiers de page
		$this->SetLineWidth(0.1);
		$this->Line(5, $t, 10, $t);
		$this->Line(200, $t, 205, $t);
		
		$this->SetXY(70, $t+25);
		$this->SetFont('Arial','B',11);
		$s = $this->cv("CADREF\nUniversité de la Culture\nPermanante et du Temps Libre");
		$this->MultiCell(70, 4, $s, 0, 'C');
		$this->SetXY(70, $t+43);
		$this->SetFont('Arial','U',11);
		$s = $this->cv("Secrétariat :");
		$this->MultiCell(70, 4, $s, 0, 'C');
		$this->SetXY(70, $t+47);
		$this->SetFont('Arial','',11);
		$s = $this->cv("249, rue de Bouillargues\n30000 NÎMES");
		$this->MultiCell(70, 4, $s, 0, 'C');
		$this->SetXY(70, $t+64);
		$this->SetFont('Arial','B',11);
		$s = $this->cv("Tél. : 04 66 36 99 44\nFax. : 04 66 36 99 45");
		$this->MultiCell(70, 4, $s, 0, 'C');
		$this->SetXY(70, $t+78);
		$this->SetFont('Arial','',11);
		$s = $this->cv("www.cadref.com\ncontact@cadref.com");
		$this->MultiCell(70, 4, $s, 0, 'C');

		$s = getcwd().'/Skins/'.Sys::$Skin.'/Modules/Cadref/assets/img/cadref_logo_noir.png';
		$this->Image($s, 159, $t+17, 32, 37);
		$this->SetXY(140, $t+61);
		$this->SetFont('Arial','B',14);
		$s = $this->cv("CARTE D'ÉTUDIANT\nET DE MEMBRE\nC.A.D.R.E.F.");
		$this->MultiCell(70, 6, $s, 0, 'C');

		$this->Line(5, 198, 10, 198);
		$this->Line(200, 198, 205, 198);

	}
	
	function PrintLines($ins) {
		$this->SetFont('Arial','',8);
		foreach($ins as $i) $this->printLine($i);
	}

	private function printLine($l) {
		$cls = $l->getOneParent('Classe');
		$ens = $cls->getParents('Enseignant');
		$lieu = $cls->getOneParent('Lieu');
		$n = '';
		foreach($ens as $e) {
			if($n != '') $n .= ', ';
			$n .= trim($e->Prenom.' '.$e->Nom);
		}
		$this->SetXY($this->left, $this->posy);
		$this->Cell($this->width[0], 5, $l->CodeClasse, 0, 0, $this->align[0]);
		$s = $l->LibelleW.' '.$l->LibelleN;
		if($lieu && $lieu->Libelle) $s .= ' ('.$lieu->Libelle.')';
		$this->Cell($this->width[1], 5, $this->cv($s), 0, 0, $this->align[1]);
		$s = substr($l->Jour, 0, 3).' '.$l->HeureDebut.' '.$l->HeureFin;
		if($l->CycleDebut) $s .= ' ('.$l->CycleDebut.' '.$l->CycleFin.')';
		$this->Cell($this->width[2], 5, $this->cv($s), 0, 0, $this->align[2]);
		$this->Cell($this->width[3], 5, $this->cv($n), 0, 0, $this->align[3]);
		$this->Cell($this->width[4], 5, $l->Prix-$l->Remise1-$l->Remise2, 0, 0, $this->align[4]);
		$this->posy += 4;
	} 

}