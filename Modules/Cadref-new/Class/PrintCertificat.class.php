<?php

require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');
require_once('nuts.class.php');

class PrintCertificat extends FPDF {
		
	
	function PrintCertificat($anneeCotis, $anneeFisc) {
		parent::__construct('P', 'mm', 'A5');
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
		
		$img = getcwd().'/Skins/AngularAdmin/Modules/Cadref/assets/img/cadref_logo_noir.png';
		$this->Image($img,25,19,26,30);
		
		$y = 21;
		$this->SetFont('Arial','B',14);
		$this->SetXY(-120,$y);
		$s = $this->cv("Université de la Culture Permanente\n et du Temps Libre");
		$this->MultiCell(100,5,$s,0,'R');
		$y += 20;
		$this->SetFont('Arial','',16);
		$this->SetXY(-120,$y);
		$s = $this->cv("CADREF DU GARD");
		$this->Cell(100,6,$s,0,0,'R');
		$y += 30;
		$this->SetFont('Arial','B',14);
		$this->SetXY(7,$y);
		$s = $this->cv("ATTESTATION SUR L'HONNEUR");
		$this->Cell(138,5,$s,0,0,'C');
		$y += 8;
		$this->SetFont('Arial','I',12);
		$this->SetXY(7,$y);
		$s = $this->cv("(Certificat de non contre-indication à la pratique d'une activité physique)");
		$this->Cell(138,5,$s,0,0,'C');
		$y += 10;
		$this->SetFont('Arial','',12);
		$this->SetXY(7,$y);
		$s = "Je soussigné";
		$s .= $l['Sexe'] == 'F' ? 'e,' : ',';
		$this->Cell(138,5,$this->cv($s));
		$y += 8;

		$this->SetFont('Arial','B',12);
		$s = $this->cv($l['Prenom']);
		$w = $this->GetStringWidth($s);
		$this->SetXY(7,$y);
		$this->Cell(138,5,$s);
		$this->SetFont('Arial','B',14);
		$s = $this->cv($l['Nom']);
		$this->SetXY(8+$w,$y);
		$this->Cell(131-$w,5,$s);
		$y += 8;

		$s = "Certifie, à ce jour, ne pas vouloir fournir de certificat médical de mon médecin ";
		$s .= "tratant et atteste sur l'honneur ne pas avoir de contre-indication et être apte ";
		$s .= "à pratiquer les activités physique suivantes :";
		$this->SetFont('Arial','',12);
		$this->SetXY(7,$y);
		$this->MultiCell(200,5,$this->cv($s));
		$y += 23;

		$id = $l['Id'];
		$annee = Cadref::$Annee;
		$sql = "
select wd.Libelle
from `kob-Cadref-Inscription` i 
inner join `kob-Cadref-Classe` c on c.Id=i.ClasseId 
inner join `kob-Cadref-Niveau` n on n.Id=c.NiveauId 
inner join `kob-Cadref-Discipline` d on d.Id=n.DisciplineId 
left join `kob-Cadref-WebDiscipline` wd on wd.Id=d.WebDisciplineId
where i.AdherentId=$id and i.Annee=$annee and i.Supprime=0 and i.Attente=0 
";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$s = '';
		foreach($pdo as $p) {
			if($s) $s .=', ';
			$s .= $p['Libelle'];
		}
		$this->SetFont('Arial','B',12);
		$this->SetXY(7,$y);
		$this->MultiCell(138,5,$this->cv($s));
		$y += 8;



		$s = $this->cv("CADREF  -  249 rue de Bouillargues  -  30000 NÎMES");
	}
	
}