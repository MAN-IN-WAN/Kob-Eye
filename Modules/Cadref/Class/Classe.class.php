<?php
class Classe extends genericClass {
	
	function Save() {
		$annee = Cadref::$Annee;
		
//		if(!empty($this->Annee) && $this->Annee < $annee) {
//			$this->addError(array("Message" => "Cette fiche ne peut être modifiée ($this->Annee)", "Prop" => ""));
//			return false;			
//		}
		$id = $this->Id;
		if(! $id) {
			$n = $this->getOneParent('Niveau');
			$this->Antenne = $n->Antenne;
			$this->Section = $n->Section;
			$this->Discipline = $n->Discipline;
			$this->Niveau = $n->Niveau;
			$p = $n->getOneParent('Antenne');
			$this->addParent($p);
			$p = $n->getOneParent('Section');
			$this->addParent($p);
			$p = $n->getOneParent('Discipline');
			$this->addParent($p);
			$this->CodeClasse = $this->Antenne.$this->Section.$this->Discipline.$this->Niveau.$this->Classe;
			if(empty($this->Annee)) $this->Annee = $annee;
			$next = $this->Annee+1;
			if(! $this->DateReduction1) $this->DateReduction1 = strtotime("$next-01-01");
			if(! $this->DateReduction2) $this->DateReduction2 = strtotime("$next-03-01");
		}
		else {
			$this->Attentes = Sys::getCount('Cadref','Classe/'.$this->Id.'/Inscription/Attente=1&Supprime=0');
			$this->Inscrits = Sys::getCount('Cadref','Classe/'.$this->Id.'/Inscription/Attente=0&Supprime=0');
			$this->Attachements = Sys::getCount('Cadref','Classe/'.$this->Id.'/Attachement');
		}
		return parent::Save();
	}
			
	function Delete() {
		$rec = $this->getChildren('Inscription');
		if(count($rec)) throw new Exception('Cette classe ne peut être supprimée');
		
		$ds = $this->getChildren('ClasseDate');
		foreach($ds as $d) $d->Delete();

		return parent::Delete();
	}
	
	function GetFormInfo() {
		$a = $this->getOneParent('Antenne');
		$s = $this->getOneParent('Section');
		$d = $this->getOneParent('Discipline');
		$n = $this->getOneParent('Niveau');
		$l = $this->getOneParent('Lieu');
		$t = array();
		$ens = Sys::getData('Cadref','Enseignant'); 
		foreach($ens as $e) $t[] = array('id'=>$e->Id, 'label'=>$e->Nom.' '.$e->Prenom);
		return array('LibelleA'=>$a->Libelle, 'LibelleS'=>$s->Libelle, 'LibelleD'=>$d->Libelle, 'LibelleN'=>$n->Libelle, 'LibelleL'=>$l ? $l->Libelle : '', 'Enseignants'=>$t);
	}
	
	function ListeEnseignants() {
		$t = array();
		$ens = Sys::getData('Cadref','Enseignant'); 
		foreach($ens as $e) $t[] = array('id'=>$e->Id, 'label'=>$e->Nom.' '.$e->Prenom);
		return array('Enseignants'=>$t);
	}
	 
	function ListClassSetSession($obj) {
		$_SESSION['ListClasse'] = $obj['ClasseAnnee'];
		return true;
	}
	function ListClassGetSession($obj) {
		if(isset($_SESSION['ListClasse'])) $obj = $_SESSION['ListClasse'];
		else $obj = false;
		return $obj;
	}
	
	function CopyDates($args) {
		$annee = $args['Annee'];
		$org = $args['CopyFrom'];
		
		$ds = $this->getChildren('ClasseDate');
		foreach($ds as $d) $d->Delete();
	
		
		$sql = "
select d.DateCours
from `##_Cadref-Classe` c 
left join `##_Cadref-ClasseDate` d on c.Id=d.ClasseId
where c.Annee='$annee' and c.CodeClasse='$org'
order by d.DateCours";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		foreach($pdo as $p) {
			$d = genericClass::createInstance('Cadref', 'ClasseDate');
			$d->Annee = $annee;
			$d->DateCours = $p['DateCours'];
			$d->addParent($this);
			$d->Save();
		}
		return array('args'=>$args, 'sql'=>$sql);
	}
	
	function NextDate() {
		$id = $this->Id;
		$annee = Cadref::$Annee;
		$t = time();
		if($this->Programmation == 0) {
			$cy = $this->CycleDebut;
			if($cy) {
				$m = substr($cy, 3, 2);
				$cd = strtotime(str_replace('/', '-', $cy).'-'.($m > 8 ? $annee : $annee + 1));
				if($t < $cd) return array('Date'=>$cd);
				$cy = $p['CycleFin'];
				$m = substr($cy, 3, 2);
				$cf = strtotime(str_replace('/', '-', $cy).'-'.($m > 8 ? $annee : $annee + 1));
				$cf += 86400 - 1;
				if($t > $cf) return array('Date'=>0);
			}
			$j = date('w', $t);
			if($j <= $this->JourId) $t += ($this->JourId-$j)*86400;
			else $t += (7-($j-$this->JourId));
			$sql = "select DateDebut from `##_Cadref-Vacance` where Annee='$annee' and Type='D' and DateDebut>$t and JourId=".$this->JourId." limit 1";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			if($pdo->rowCount()) {
				$d = $pdo->fetch(PDO::FETCH_ASSOC);
				return array('Date'=>$d['DateDebut'],'sql'=>$sql);
			}
			while(true) {
				$sql = "select DateDebut,DateFin from `##_Cadref-Vacance` where Annee='$annee' and Type='V' and DateDebut<=$t and DateFin>=$t limit 1";
				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
				$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
				if($pdo->rowCount()) $t += 7*86400;
				else return array('Date'=>$t,'sql'=>$sql);
			}
		}
		else {
			$sql = "select DateCours from `##_Cadref-ClasseDate` where Annee='$annee' and ClasseId=$id and DateCours>=$t order by DateCours limit 1";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			if($pdo->rowCount()) {
				$d = $pdo->fetch(PDO::FETCH_ASSOC);
				return array('Date'=>$d['DateCours'],'sql'=>$sql);
			}
		}
		return array('Date'=>0);
	}
	
	function PrintPresence($obj) {
		require_once ('PrintPresence.class.php');

		$annee = Cadref::$Annee;
		$debut = $obj['Debut'];
		$fin = $obj['Fin'];
		$fin .= substr('ZZZZZZZ', 0, 7-strlen($fin));
		
		$sql = "
select i.CodeClasse, i.ClasseId, d.Libelle as LibelleD, n.Libelle as LibelleN, e.Numero, e.Nom, e.Prenom, 
a.Libelle as LibelleA, c.HeureDebut, c.HeureFin, j.Jour, c.CycleDebut, c.CycleFin, c.Seances
from `##_Cadref-Inscription` i
inner join `##_Cadref-Classe` c on c.Id=i.ClasseId
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
inner join `##_Cadref-Adherent` e on e.Id=i.AdherentId 
inner join `##_Cadref-Antenne` a on a.Id=n.AntenneId 
left join `##_Cadref-Jour` j on j.Id=c.JourId 
where i.Annee=$annee and i.Supprime=0 and i.Attente=0 ";
		if($debut != '') $sql .= "and i.CodeClasse>='$debut' ";
		if($fin != '') $sql .= "and i.CodeClasse<='$fin' ";
		$sql .= "order by i.CodeClasse, e.Nom, e.Prenom";

		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return false;
		
		$pdf = new PrintPresence($debut, $fin, $obj['Mois']);
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle('Fiches de presence');

		$pdf->PrintLines($pdo);

		$file = 'Home/tmp/FichePresence_'.date('YmdHis').'.pdf';
		$pdf->Output(getcwd() . '/' . $file);
		$pdf->Close();

		return array('pdf'=>$file);
	}

	function PrintClasse($obj) {
		require_once ('PrintClasse.class.php');

		$annee = Cadref::$Annee;
		$sql = "
select c.CodeClasse, d.Libelle as LibelleD, n.Libelle as LibelleN, c.HeureDebut, c.HeureFin, j.Jour, c.CycleDebut, c.CycleFin
from `##_Cadref-Classe` c
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
left join `##_Cadref-Jour` j on j.Id=c.JourId 
where c.Annee='$annee'
order by c.CodeClasse";

		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return false;
		
		$file = 'Home/tmp/ListeClasse_'.date('YmdHis');
		if($obj['mode'] == 1) {
			$f = fopen(getcwd().'/'.$file.'.csv', 'w');
			foreach($pdo as $p) {
				$s = $this->dblCotes($p['CodeClasse']).";".$this->dblCotes($p['LibelleD'].' '.$p['LibelleN']).";";
				$s .= $this->dblCotes($p['CycleDebut'].' '.$p['CycleFin']).";".($p['Jour']).";".$this->dblCotes($p['HeureDebut'].' '.$p['HeureFin'])."\n";
				fwrite($f, $s);
			}
			fclose($f);
			return array('csv'=>$file.'.csv');
		}
		
		$pdf = new PrintClasse($annee);
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle('Liste des classe');

		$pdf->AddPage();
		$pdf->PrintLines($pdo);

		$pdf->Output(getcwd().'/'.$file.'.pdf');
		$pdf->Close();

		return array('pdf'=>$file.'.pdf');
	}
	
	private function dblCotes($s) {
		return '"'.str_replace('"', "\"", $s).'"';
	}
	
	
	function CheckAbsence($start, $end) {
		$annee = Cadref::$Annee;

		// vacances
		$vacances = array();
		$sql = "select DateDebut,DateFin,JourId from `##_Cadref-Vacance` where Annee='$annee'";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		foreach($pdo as $p) {
			$t = $p['Type'];
			$d = $p['DateDebut'];
			$f = $p['DateFin'] ? $p['DateFin'] : $d;
			$v = new stdClass();
			$v->type = $t;
			$v->start = $d;
			$v->end = $f;
			$v->day = $p['JourId'];
			$vacances[] = $v;
		}
		// horaires
		$cd = 0;
		$cy = $this->CycleDebut;
		if($cy != '') {
			$m = substr($cy, 3, 2);
			$cd = strtotime(str_replace('/', '-', $cy).'-'.($m > 8 ? $annee : $annee + 1));
			$cy = $this->CycleFin;
			$m = substr($cy, 3, 2);
			$cf = strtotime(str_replace('/', '-', $cy).'-'.($m > 8 ? $annee : $annee + 1));
			$cf += (24 * 60 * 60) - 1;
		}
		$j = $this->JourId - 1;
		$d = $start + ($j * 24 * 60 * 60);
		while($d < $end) {
			$ok = !($cd && ($d < $cd || $d > $cf));
			if($ok) {
				foreach($vacances as $v) {
					switch($v->type) {
						case 'D':
							$w = date('N', $d);
							$ok = !($v->day == $w && $d < $v->start);
							break;
						case 'F':
							$w = date('N', $d);
							$ok = !($v->day == $w && $d > $v->start);
							break;
						case 'V':
							$ok = !($d >= $v->start && $d <= $v->end);
							break;
					}
					if(!$ok) break;
				}
			}
			if($ok) {
				$cstart = Date('Y-m-d', $d).'T'.$this->HeureDebut;
				$cend = Date('Y-m-d', $d).'T'.$this->HeureFin;
				$hd = strtotime($cstart);
				$hf = strtotime($cend);
				if(Cadref::between($hd, $start, $end) || Cadref::between($hf, $start, $end))
					return true;
			}
			$d += 7 * 24 * 60 * 60;
		}
		return false;
	}
}
