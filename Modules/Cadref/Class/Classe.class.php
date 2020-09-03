<?php
class Classe extends genericClass {
	
	function Save() {
		$annee = Cadref::$Annee;
//klog::l("<<<<<<<<<<<<<<<<<<<<<<<<<<",$this);
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
	
	public static function ClassesExport($args) {
		$an = $args['Annee'];
		$sql = "select c.Annee,c.CodeClasse,c.JourId,c.HeureDebut,c.HeureFin,c.CycleDebut,c.CycleFin,c.Seances,c.Programmation, "
			."c.Places,c.Prix,c.Reduction1,c.Reduction2, "
			."ifnull(l.Lieu,'') as Lieu, "
			."ifnull(e.Code,'') as Enseignant, "
			//."concat(d.Libelle,' ',n.Libelle) as Libelle, "
			."ifnull(d.WebDiscipline,'') as Web, "
			."concat(wd.Libelle,' ',n.Libelle) as LibelleWeb "
			."from `##_Cadref-Classe` c "
			."inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId "
			."inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId "
			."left join `##_Cadref-Lieu` l on l.Id=c.LieuId "
			."left join `##_Cadref-ClasseEnseignants` ce on ce.Classe=c.Id "
			."left join `##_Cadref-Enseignant` e on e.Id=ce.EnseignantId "
			."left join `##_Cadref-WebDiscipline` wd on wd.Id=d.WebDisciplineId "
			."where Annee='$an' "
			."group by c.CodeClasse "
			."order by c.CodeClasse";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);

		$file = 'Home/tmp/Classes_'.$an.'_'.date('YmdHis').'.csv';
		$f = fopen($file, 'w');
		$s = '"Annee";"CodeClasse";"JourId";"HeureDebut";"HeureFin";"CycleDebit";"CycleFin";"Seances";"Programmation";"Places";'
			.'"Prix";"Reduction1";"Reduction2";"Lieu";"Enseignant";"Web";"LibelleWeb"';
		fwrite($f, Cadref::cv2win("$s\n"));
			
		foreach($pdo as $a) {
			$s = '"'.$a['Annee'].'";'
				.'"'.$a['CodeClasse'].'";'
				.'"'.$a['JourId'].'";'
				.'"'.$a['HeureDebut'].'";'
				.'"'.$a['HeureFin'].'";'
				.'"'.$a['CycleDebut'].'";'
				.'"'.$a['CycleFin'].'";'
				.'"'.$a['Seances'].'";'
				.'"'.$a['Programmation'].'";'
				.'"'.$a['Places'].'";'
				.'"'.$a['Prix'].'";'
				.'"'.$a['Reduction1'].'";'
				.'"'.$a['Reduction2'].'";'
				.'"'.$a['Lieu'].'";'
				.'"'.$a['Enseignant'].'";'
				.'"'.$a['Web'].'";'
				.'"'.$a['LibelleWeb'].'"';
			fwrite($f, Cadref::cv2win("$s\n"));
		}
		fclose($f);
		return array('csv'=>$file, 'sql'=>$sql);		
	}
	
	public static function ClassesImport($args) {
		$sql = "select Annee from `##_Cadref-Annee` order by Annee desc limit 1";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$d = $pdo->fetch(PDO::FETCH_ASSOC);
		$annee = $d['Annee'];
		$next = $annee+1;
		
		$msg = '';
		$err = false;
		$flds = ['Annee','CodeClasse','JourId','HeureDebut','HeureFin','CycleDebut','CycleFin','Seances','Programmation',
			'Places','Prix','Reduction1','Reduction2','Lieu','Enseignant','Web','LibelleWeb'];

		$f = getcwd().'/'.$args['FilePath'];
		$f = str_replace("\r\n", "\n", file_get_contents($f));
		$ls = explode("\n", $f);
		$lig = 0;
		foreach($ls as $l) {
			if($l == '') break;
			if(!$lig++) continue; // header

			$cs = explode(";", $l);
			if($cs[0] == '') break;
		
			$n = 0;
			$nbdt = 0;
			$ok = true;
			foreach($cs as $c) {
				if($c[0] == '"') $c = substr($c, 1, -1);
				switch($n) {
					case 0:
						if($c != $annee) {
							$msg .= "lig $lig: Annee erronee $c. Ligne non traitee.\n";
							$ok = false;
						}
						break;
					case 1:
						$clas = $c;
						break;
					case 2:
						$del = ($c == '' || $c == '0');
						
						$new = false;
						$cls = Sys::getOneData('Cadref', "Classe/Annee=$annee&CodeClasse=$clas");
						if(!$cls && !$del) {
							$new = true;
							$cls = genericClass::createInstance('Cadref', 'Classe');
							$cls->Annee = $annee;
							$cls->Classe = substr($clas, 6, 1);
							$niv = Sys::getOneData('Cadref', 'Niveau/CodeNiveau='.substr($clas, 0, 6));
							if(!$niv) {
								$msg .= "lig $lig: $clas Niveau introuvable $clas. Ligne non traitee.\n";
								$ok = false;
							}
							$cls->addParent($niv);
						}
						elseif($cls && $del) {
							$cls->Delete();
							$msg .= "lig $lig: $clas Classe supprimee.\n";
							$ok = false;
						}
						elseif($del) $ok = false;
					case 3:
					case 4:
					case 5:
					case 6:
					case 7:
					case 8:
					case 9:
					case 10:
					case 11:
					case 12:
						if($n == 2) $day = $c;
						elseif($n == 7) $sean = $c;
						elseif($n == 8)	$prog = $c;
						elseif($n == 5 || $n == 6) $c = self::convDat($c);
						$fld = $flds[$n];
						$cls->$fld = $c;
						break;
					case 13:
						$lieu = Sys::getOneData('Cadref', 'Lieu/Lieu='.$c);
						if(!$lieu) $msg .= "lig $lig: $clas Lieu introuvable $c.\n";
						else $cls->addParent($lieu);
						break;
					case 14: 
						$ens = Sys::getOneData('Cadref', 'Enseignant/Code='.$c);
						if(!$ens) $msg .= "lig $lig: $clas Enseignant introuvable $c.\n";
						else $cls->addParent($ens);
						$cls->Save();
						if(!$new) {
							$dts = $cls->getChildren('ClasseDate');
							foreach($dts as $dt) $dt->Delete();
						}
						break;
					case 15:
						break; // ignore libelle
					case 16:
						break; // ignore Web
					default:
						$nb = 0;
						if($c == '' || $prog == 0) $n == -1;
						else {
							$nbdt++;
							$c = self::convDat($c);
							$ds = explode('/', $c); 
							$dt = date_create_from_format('d/m/Y', "$c/".($ds[1] > 7 ? $annee : $next));
							if(!$dt) $msg .= "lig $lig: $clas Date erronee $c.\n";
							else {
								$w = $dt->format('w');
								if(!$w) $w = 7;
								if($w != $day) $msg .= "lig $lig: $clas Date differente du jour $c.\n";
								$ts = $dt->getTimestamp();
								$dat = genericClass::createInstance('Cadref', 'ClasseDate');
								$dat->addParent($cls);
								$dat->DateCours = $ts;
								$dat->Save();
							}
						}
				}
				if(!$ok || $n == -1) break;
				$n++;
			}
			if($prog && $nbdt != $sean) $msg .= "lig $lig: $clas Nombre de dates different des seances $sean.\n";
			$lig++;
		}
		$msg .= "\n$lig lignes traitees\n";
		$file = 'Home/tmp/Import_'.date('YmdHis').'.txt';
		file_put_contents($file, $msg);
		
		return array('file'=>$file);
	}

	public static function ClassesCheck($args) {
		$sql = "select Annee from `##_Cadref-Annee` order by Annee desc limit 1";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$d = $pdo->fetch(PDO::FETCH_ASSOC);
		$annee = $d['Annee'];
		$next = $annee+1;
		
		$msg = '';
		$err = false;
		$flds = ['Annee','CodeClasse','JourId','HeureDebut','HeureFin','CycleDebut','CycleFin','Seances','Programmation',
			'Places','Prix','Reduction1','Reduction2','Lieu','Enseignant','Web','LibelleWeb'];

		$f = getcwd().'/'.$args['FilePath'];
		$f = str_replace("\r\n", "\n", file_get_contents($f));
		$ls = explode("\n", $f);
		$lig = 0;
		foreach($ls as $l) {
			if($l == '') break;
			if(!$lig++) continue; // header

			$cs = explode(";", $l);
			if($cs[0] == '') break;
	
			$n = 0;
			$nbdt = $sean = 0;
			$ok = true;
			foreach($cs as $c) {
				if(substr($c, 0, 1) == '"') $c = substr($c, 1, -1);
				
				switch($n) {
					case 0:
						if($c != $annee) {
							$msg .= "lig $lig: Annee erronee $c.\n";
							$ok = false;
						}
						break;
					case 1:
						$clas = $c;
						break;
					case 2:
						$del = ($c == '' || $c == '0');
						
						$new = false;
						$cls = Sys::getOneData('Cadref', "Classe/Annee=$annee&CodeClasse=$clas");
						if(!$cls && !$del) {
							$new = true;
							$cls = genericClass::createInstance('Cadref', 'Classe');
							$cls->Annee = $annee;
							$cls->Classe = substr($clas, 6, 1);
							$niv = Sys::getOneData('Cadref', 'Niveau/CodeNiveau='.substr($clas, 0, 6));
							if(!$niv) {
								$msg .= "lig $lig: $clas Niveau inexistant ".substr($clas,0,6).".\n";
								$dis = Sys::getOneData('Cadref', 'Discipline/CodeDiscipline='.substr($clas, 1, 4));
								if(!$dis) {
									$msg .= "lig $lig: $clas Discipline inexistante ".substr($clas,1,4).".\n";
									$sec = Sys::getOneData('Cadref', 'Section/Section='.substr($clas, 1, 2));
									if(!$sec) $msg .= "lig $lig: $clas Section inexistante ".substr($clas,1,2).".\n";
								}
							}
						}
						elseif($cls && $del) {
							$msg .= "lig $lig: $clas Classe a supprimer.\n";
							$ok = false;
						}
						elseif($del) $ok = false;
					case 3:
					case 4:
					case 5:
					case 6:
					case 7:
					case 8:
					case 9:
					case 10:
					case 11:
					case 12:
						if($n == 2) $day = $c;
						elseif($n == 7) $sean = $c;
						elseif($n == 8)	$prog = $c;
						elseif($n == 5 || $n == 6) $c = self::convDat($c);
						$fld = $flds[$n];
						$cls->$fld = $c;
						break;
					
					case 13:
						if(!$c) $msg .= "lig $lig: $clas Lieu non renseigne.\n";
						else {
							$lieu = Sys::getOneData('Cadref', 'Lieu/Lieu='.$c);
							if(!$lieu) $msg .= "lig $lig: $clas Lieu inexistant $c.\n";
						}
						break;
					case 14: 
						if(!$c) $msg .= "lig $lig: $clas Enseignant non renseigne.\n";
						else {
							$ens = Sys::getOneData('Cadref', 'Enseignant/Code='.$c);
							if(!$ens) $msg .= "lig $lig: $clas Enseignant inexistant $c.\n";
						}
						break;
					case 15:
						if(!$c) $msg .= "lig $lig: $clas WebDiscipline non renseigne.\n";
						else {
							$web = Sys::getOneData('Cadref', 'WebDiscipline/CodeDiscipline='.$c);
							if(!$ens) $msg .= "lig $lig: $clas WebDiscipline inexistante $c.\n";
						}
						break;
					case 16:
						break; // ignore libelle
					default:
						if($c == '') $n = -1;
						else {
							if($prog == 0) {
								$n = -1;
								$msg .= "lig $lig: $clas Date sur cours hebdomadaire $c.\n";
							}
							$nbdt++;
							$c = self::convDat($c);
							$ds = explode('/', $c); 
							$dt = date_create_from_format('d/m/Y', "$c/".($ds[1] > 7 ? $annee : $next));
							if(!$dt) $msg .= "lig $lig: $clas Date erronée $c.\n";
							else {
								$w = $dt->format('w');
								if(!$w) $w = 7;
								if($w != $day) $msg .= "lig $lig: $clas Date differente du jour $c.\n";
							}
						}
				}
				if(!$ok || $n == -1) break;
				$n++;
			}
			if($prog && $nbdt != $sean) $msg .= "lig $lig: $clas Nombre de dates et de seances differents $sean.\n";
		}
		$msg .= "\n$lig lignes traitees\n";
		$file = 'Home/tmp/Import_'.date('YmdHis').'.txt';
		file_put_contents($file, $msg);
		
		return array('file'=>$file);
	}
	
	private static function convDat($dt) {
		if(strpos($dt, '-') === false) return $dt;
		$t = explode('-', Cadref::win2utf($dt));
		$m = strpos('janv;févr;mars;avr ;mai ;juin;juil;août;sept;oct ;nov ;déc ;', $t[1]) / 5 + 1;
		$t = sprintf('%s/%02d', $t[0], $m);
		return $t;
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
		$eid = isset($obj['Enseignant']) ? $obj['Enseignant'] : 0;
		
		$sql = "
select i.CodeClasse, i.ClasseId, d.Libelle as LibelleD, n.Libelle as LibelleN, e.Numero, e.Nom, e.Prenom, 
a.Libelle as LibelleA, c.HeureDebut, c.HeureFin, j.Jour, c.CycleDebut, c.CycleFin, c.Seances
from `##_Cadref-Inscription` i
inner join `##_Cadref-Classe` c on c.Id=i.ClasseId";
		
		if($eid) $sql .= "
inner join `##_Cadref-ClasseEnseignants` ce on ce.Classe=c.Id and ce.EnseignantId=$eid
";
		$sql .= "
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
		if(! $pdo) return array('pdf'=>false, 'sql'=>$sql);
		
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
select c.CodeClasse, d.Libelle as LibelleD, n.Libelle as LibelleN, c.HeureDebut, c.HeureFin, j.Jour, c.CycleDebut, c.CycleFin, c.AvoirReporte
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
		return '"'.iconv('UTF-8','ISO-8859-15//TRANSLIT',str_replace('"', "\"", $s)).'"';
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
