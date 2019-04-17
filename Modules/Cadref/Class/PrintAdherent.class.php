<?php

require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');

class PrintAdherent extends FPDF {

	private $contenu;
	private $rupture;
	private $antenne;
	private $attente;
	private $adherent;
	private $head;
	private $align;
	private $width;
	private $left = 5;
	private $posy;
	private $titre;
	private $rupAnt = 0;
	private $rupVal = '';
	private $antLib = '';
	private $rupLib = '';
	private $totaux = [[0, 0, 0], [0, 0, 0], [0, 0, 0]];
	private $mode;
	private $rupEns = "\t";  // valeur initiale non vide

	function PrintAdherent($mode, $contenu, $rupture, $antenne, $attente, $adherent) {
		parent::__construct('P', 'mm', 'A4');
		$this->AcceptPageBreak(true, 12);

		$this->contenu = $contenu;
		$this->rupture = $rupture;
		$this->adherent = $adherent;

		$this->mode = $mode;
		switch($mode) {
			case 0:
				$this->titre = "CADREF : Liste des adhérents ";
				switch($adherent) {
					case 'B': $this->titre .= '(Bureau) '; break;
					case 'A': $this->titre .= '(Administrateurs) '; break;
					case 'D': $this->titre .= '(Délégués) '; break;
				}
				if($antenne) {
					$this->antenne = Sys::getOneData('Cadref', 'Antenne/' . $antenne);
					$this->titre .= ": " . $this->antenne->Libelle;
				}
				break;
			case 1:
				$this->titre = "CADREF : Certificats médicaux invalides";
				break;
			case 2:
				$this->titre = "CADREF : Fiches adhérents incomplètes";
				break;
		}

		$this->head = array('', 'Inscrits', 'Attentes', 'Total', '', 'Classe/Att.');
		$this->width = array(130, 15, 15, 15, 1, 24);
		$this->align = array('L', 'R', 'R', 'R', 'L', 'L');
	}

	private function cv($txt) {
		return iconv('UTF-8', 'ISO-8859-15//TRANSLIT', $txt);
	}

	function Header() {
		$y = 5;
		$this->SetFont('Arial', 'B', 10);
		$this->SetXY($this->left, $y);
		$this->Cell(30, 4, date('d/m/Y H:i'));
		$this->SetXY(-40, $y);
		$this->Cell(35, 4, 'Page ' . $this->PageNo(), 0, 0, 'R');
		$y += 5;
		$this->SetFont('Arial', 'B', 12);
		$this->SetXY($this->left, $y);
		$this->Cell(200, 4, $this->cv($this->titre), 0, 0, 'C');
		$y += 8;

		if($this->mode == 0) {
			$this->SetFont('Arial', 'BU', 9);
			$this->SetXY($this->left, $y);
			$n = count($this->head);
			for($i = 0; $i < $n; $i++)
				$this->Cell($this->width[$i], 5, $this->cv($this->head[$i]), 0, 0, $this->align[$i]);
			$y += 6;
		}

		$this->posy = $y;
		$this->SetXY($this->left, $this->posy);
	}

	function PrintLines($list) {
		foreach($list as $l) {
			if($this->rupture != 'S') {
				if($this->rupture != 'E') {
					$a = $l['AntenneId'];
					if($this->rupAnt != $a) {
						if($this->rupVal != '') {
							$this->footRupture();
							$this->rupVal = '';
						}
						if($this->rupAnt != 0) $this->footAntenne();
						$this->headAntenne($a);
						$this->rupAnt = $a;
					}
				}

				$c = $l['CodeClasse'];
				switch($this->rupture) {
					case 'D': $r = substr($c, 0, 7);
						break;
					case 'N': $r = substr($c, 0, 9);
						break;
					case 'C':
					case 'E':
						$r = $c;
						break;
				}
				if($this->rupVal != $r) {
					if($this->rupVal != '' && $this->rupture != 'E') {
						$this->footRupture();
						if($this->rupture == 'C') $this->AddPage();
					}
					$this->headRupture($l);
					$this->rupVal = $r;
				}
			}
			if($l['Attente']) $this->totaux[0][1] ++;
			else $this->totaux[0][0] ++;
			$this->totaux[0][2] ++;

			if($this->contenu == 'N' || $this->contenu == 'A') $this->printLine($l);
		}
		if($this->rupture != 'S' && $this->rupture != 'E') {
			if($this->rupVal != '') $this->footRupture();
			if($this->rupAnt != 0) $this->footAntenne();
		}
		$this->footTotal();
	}


	private function printLine($l) {
		$this->SetXY($this->left, $this->posy);
		$this->SetFont('Arial', '', 10);
		$this->Cell(12, 4, $l['Numero'], 0, 0, 'L');

		$this->SetXY($this->left + 13, $this->posy);
		$w = $this->GetStringWidth($this->cv($l['Nom']));
		$this->Cell($w + 1, 4, $this->cv($l['Nom']));
		$this->SetFont('Arial', '', 9);
		$w = $this->GetStringWidth($this->cv($l['Prenom']));
		$this->Cell($w + 2, 4, $this->cv($l['Prenom']));
		$this->SetFont('Arial', 'I', 8);
		$this->Cell(129 - $w, 4, $l['Mail']);

		$this->SetFont('Arial', '', 10);
		$this->SetXY($this->left + 124, $this->posy);
		$this->Cell(26, 4, $l['Telephone1'], 0, 0, 'L');
		$this->Cell(26, 4, $l['Telephone2'], 0, 0, 'L');

		switch($this->mode) {
			case 0:
				if($this->adherent) {
					$cls = Sys::getOneData('Cadref', 'Classe/' . $l['Delegue']);
					$s = $cls->CodeClasse;
				}
				else if($this->attente) $s = date('d/m/Y H:i', $l['DateAttente']);
				else $s = 'C:'.substr($l['CodeClasse'], 10, 1);
				break;
			case 1:
				$s = $l['DateCertificat'] ? date('d/m/Y', $l['DateCertificat']) : 'N.D.';
				break;
			case 2:
				$s = '';
				break;
		}
		$this->SetXY($this->left + 176, $this->posy);
		$this->Cell(25, 4, $s, 0, 0, 'L');

		if($this->contenu == 'A') {
			$this->posy += 4;
			$this->SetFont('Arial', '', 10);
			$this->SetXY($this->left + 13, $this->posy);
			$this->Cell(105, 4, $l['Adresse1'], 0, 0, 'L');
			$this->Cell(75, 4, $l['CP'] . '  ' . $l['Ville'], 0, 0, 'L');
			if($l['Adresse2'] != '') {
				$this->posy += 4;
				$this->SetXY($this->left + 13, $this->posy);
				$this->Cell(150, 4, $l['Adresse2'], 0, 0, 'L');
			}
		}
		$this->posy += 4.5;
	}

	private function headAntenne($a) {
		$ant = Sys::getOneData('Cadref', 'Antenne/' . $a);
		$this->antLib = $ant->Libelle;
		if($this->contenu != 'N' && $this->contenu != 'A') return;

		$this->posy += 2;
		$this->SetXY($this->left, $this->posy);
		$this->SetFont('Arial', '', 10);
		$this->Cell(16, 4, 'Antenne :', 0, 0, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(16, 4, $this->cv($this->antLib), 0, 0, 'L');
		$this->posy += 5;
	}

	private function footAntenne() {
		$this->posy += 2;
		$this->SetXY($this->left + 40, $this->posy);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(80, 4, $this->cv($this->antLib), 0, 0, 'L');

		$this->SetXY($this->width[0], $this->posy);
		$this->Cell($this->width[1], 4, $this->totaux[1][0], 0, 0, $this->align[1]);
		$this->Cell($this->width[2], 4, $this->totaux[1][1], 0, 0, $this->align[2]);
		$this->Cell($this->width[3], 4, $this->totaux[1][2], 0, 0, $this->align[3]);
		$this->posy += 5;

		for($i = 0; $i < 3; $i++) {
			$this->totaux[2][$i] += $this->totaux[1][$i];
			$this->totaux[1][$i] = 0;
		}
	}

	private function headRupture($l) {
		$this->rupLib = $l['LibelleD'];
		if($thi->rupture != 'D') $this->rupLib .= ' ' . $l['LibelleN'];
		if($this->contenu != 'N' && $this->contenu != 'A') return;

		if($this->rupture == 'C' || $this->rupture == 'E') {
			$c = Sys::getOneData('Cadref', 'Classe/' . $l['ClasseId']);
			$j = Sys::getOneData('Cadref', 'Jour/' . $c->JourId);
			$es = $c->getParents('Enseignant');
			$ens = '';
			foreach($es as $e) {
				if($ens != '') $ens .= ", ";
				$ens .= $e->Nom;
			}
			if($this->rupture == 'E') {
				if($this->rupEns != "\t" && $this->rupEns != $ens) $this->AddPage();
				$this->rupEns = $ens;
			}
		}

		$this->posy += 2;
		$this->SetXY($this->left, $this->posy);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(90, 4, $this->cv($this->rupLib), 0, 0, 'L');

		if($this->rupture == 'C' || $this->rupture == 'E') {
			$this->Cell(20, 4, $this->cv($j->Jour), 0, 0, 'L');
			$this->Cell(15, 4, $c->HeureDebut, 0, 0, 'L');
			$this->Cell(75, 4, $this->cv($ens), 0, 0, 'L');
		}

		$this->posy += 5;
	}

	private function footRupture() {
		$this->posy += 2;
		$this->SetXY($this->left + 30, $this->posy);
		$this->SetFont('Arial', 'I', 10);
		$this->Cell(30, 4, $this->rupVal, 0, 0, 'L');
		$this->Cell(60, 4, $this->cv($this->rupLib), 0, 0, 'L');

		$this->SetXY($this->width[0], $this->posy);
		$this->Cell($this->width[1], 4, $this->totaux[0][0], 0, 0, $this->align[1]);
		$this->Cell($this->width[2], 4, $this->totaux[0][1], 0, 0, $this->align[2]);
		$this->Cell($this->width[3], 4, $this->totaux[0][2], 0, 0, $this->align[3]);
		$this->posy += 5;

		for($i = 0; $i < 3; $i++) {
			$this->totaux[1][$i] += $this->totaux[0][$i];
			$this->totaux[0][$i] = 0;
		}
	}

	private function footTotal() {
		$this->posy += 2;
		$this->SetXY($this->left + 40, $this->posy);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(80, 4, $this->cv('TOTAL GENERAL'), 0, 0, 'L');

		$col = ($this->rupture == 'S' || $this->rupture == 'E') ? 0 : 2;
		$this->SetXY($this->width[0], $this->posy);
		$this->Cell($this->width[1], 4, $this->totaux[$col][0], 0, 0, $this->align[1]);
		$this->Cell($this->width[2], 4, $this->totaux[$col][1], 0, 0, $this->align[2]);
		$this->Cell($this->width[3], 4, $this->totaux[$col][2], 0, 0, $this->align[3]);
	}

}
