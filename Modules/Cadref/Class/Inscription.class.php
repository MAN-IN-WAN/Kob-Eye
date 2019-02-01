<?php
class Inscription extends genericClass {
	
	
	function Delete() {
		throw new Exception('Une inscription ne peut être supprimée');
	}

	function PrintStatistique($obj) {
		require_once ('cadrefStat.class.php');
		require_once ('PrintStatistique.class.php');

		$ddeb = DateTime::createFromFormat('d/m/Y H:i:s', $obj['DateDebut'].' 00:00:00')->getTimestamp(); 
		$dfin = DateTime::createFromFormat('d/m/Y H:i:s', $obj['DateFin'].' 23:59:59')->getTimestamp();
		$file = $file = 'Home/tmp/Statistiques'.date('YmdHi').'.pdf';
		$title = 'Statistiques '.$obj['DateDebut'].'-'.$obj['DateFin'];
		
		$antennes = "ABGLNSV";
		$stats = new cadrefStatList(7);
		
		$pdf = new PrintStatistique($obj['DateDebut'], $obj['DateFin'], 7);
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle(iconv('UTF-8','ISO-8859-15//TRANSLIT',$title));

		// sexe
		$sql = "
select distinct i.Numero, i.Antenne, if(h.Sexe='','Z',h.Sexe) as sex
from `##_Cadref-Inscription` i 
inner join `##_Cadref-Adherent` h on h.Id=i.AdherentId
where i.DateInscription>=$ddeb and i.DateInscription<=$dfin and (i.Supprime=0 or i.DateSupprime>$dfin)
order by sex, i.Antenne";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);;
		foreach($pdo as $p) {
			$col = strpos($antennes, $p['Antenne']);
			$s = $p['sex'];
			if($s == 'H') $lib = 'Homme';
			elseif($s == 'F') $lib = 'Femme';
			else $lib = 'Non renseigné';
			$stats->Sum(0, $s, $lib, $col, 1);
		}

		// profession
		$sql = "
select distinct i.Numero, i.Antenne, ifnull(h.ProfessionId,'ZZZZ') as prof, p.Libelle
from `##_Cadref-Inscription` i 
inner join `##_Cadref-Adherent` h on h.Id=i.AdherentId
left join `##_Cadref-Profession` p on p.Id=h.ProfessionId
where i.DateInscription>=$ddeb and i.DateInscription<=$dfin and (i.Supprime=0 or i.DateSupprime>$dfin)
order by prof, i.Antenne";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);;
		foreach($pdo as $p) {
			$col = strpos($antennes, $p['Antenne']);
			$lib = $p['prof'] != 'ZZZZ' ? $p['Libelle'] : 'Non renseigné';
			$stats->Sum(1, $p['prof'], $lib, $col, 1);
		}
		
		// cursus
		$sql = "
select distinct i.Numero, i.Antenne, ifnull(h.CursusId,'ZZZZ') as curs, c.Libelle
from `##_Cadref-Inscription` i 
inner join `##_Cadref-Adherent` h on h.Id=i.AdherentId
left join `##_Cadref-Cursus` c on c.Id=h.CursusId
where i.DateInscription>=$ddeb and i.DateInscription<=$dfin and (i.Supprime=0 or i.DateSupprime>$dfin)
order by curs, i.Antenne";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);;
		foreach($pdo as $p) {
			$col = strpos($antennes, $p['Antenne']);
			$lib = $p['curs'] != 'ZZZZ' ? $p['Libelle'] : 'Non renseigné';
			$stats->Sum(2, $p['curs'], $lib, $col, 1);
		}
	
		// age
		$sql = "
select distinct i.Numero, i.Antenne, if(h.Naissance is null or h.Naissance='','0000',h.Naissance) as nais
from `##_Cadref-Inscription` i 
inner join `##_Cadref-Adherent` h on h.Id=i.AdherentId
where i.DateInscription>=$ddeb and i.DateInscription<=$dfin and (i.Supprime=0 or i.DateSupprime>$dfin)
order by nais desc, i.Antenne";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);;
		$annee = Cadref::$Annee;
		foreach($pdo as $p) {
			$col = strpos($antennes, $p['Antenne']);
			if($p['nais'] == '9999') {
				$s = '999';
				$lib = 'Non renseigné';
			}
			else {
				$age = $annee - $p['nais'];
				if($age < 50) {
				  $s = "50";
				  $lib = "Moins de 50 ans";
				}
				else if($age < 60) {
				  $s = "60";
				  $lib = "50 à 59 ans";
				}
				else if($age < 65) {
				  $s = "65";
				  $lib = "60 à 64 ans";
				}
				else if($age < 70) {
				  $s = "70";
				  $lib = "65 à 69 ans";
				}
				else if($age < 75) {
				  $s = "75";
				  $lib = "70 à 74 ans";
				}
				else if($age < 80) {
				  $s = "80";
				  $lib = "75 à 80 ans";
				}
				else {
				  $s = "81";
				  $lib = "80 ans et plus";
				}
				
			}
			$stats->Sum(3, $s, $lib, $col, 1);
		}

		// nombre de cours
		$sql = "
select i.Numero,i.Antenne,count(*) as cours 
from `##_Cadref-Inscription` i 
where i.DateInscription>=$ddeb and i.DateInscription<=$dfin and (i.Supprime=0 or i.DateSupprime>$dfin)
group by i.Numero, i.Antenne
order by cours, i.Antenne";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);;
		foreach($pdo as $p) {
			$col = strpos($antennes, $p['Antenne']);
			$stats->Sum(4, $p['cours'], $p['cours'].' cours', $col, 1);
		}

		// villes
		$sql = "
select distinct i.Numero, i.Antenne, if(h.CP='30900','30000',h.CP) as cp, h.Ville
from `##_Cadref-Inscription` i 
inner join `##_Cadref-Adherent` h on h.Id=i.AdherentId
where i.DateInscription>=$ddeb and i.DateInscription<=$dfin and (i.Supprime=0 or i.DateSupprime>$dfin)
order by h.Ville, i.Antenne";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
$zzz=$sql;
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);;
		foreach($pdo as $p) {
			$col = strpos($antennes, $p['Antenne']);
			if(substr($p['cp'], 0 , 2) != '30') {
				$code = '?????';
				$lib = 'Hors département';
			}
			else {
				$code = $p['cp'];
				$lib = $p['cp'].' : '.$p['Ville'];
			}
			$stats->Sum(5, $code, $lib, $col, 1);
		}
		
		// disciplines
		$sql = "
select i.Numero, i.Antenne,substr(i.CodeClasse,1,9) as disc, concat(d.Libelle,' ',n.Libelle) as lib
from `##_Cadref-Inscription` i 
inner join `##_Cadref-Classe` c on c.Id=i.ClasseId
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
where i.DateInscription>=$ddeb and i.DateInscription<=$dfin and (i.Supprime=0 or i.DateSupprime>$dfin)
order by disc, i.Antenne";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);;
		foreach($pdo as $p) {
			$col = strpos($antennes, $p['Antenne']);
			$stats->Sum(6, $p['disc'], $p['disc'].' : '.$p['lib'], $col, 1);
		}

		
		
		$pdf->AddPage();
		$pdf->PrintLines($stats);

		$pdf->Output(getcwd().'/'.$file);
		$pdf->Close();
		
		return array('pdf'=>$file, 'sql'=>$zzz);
	}
	
}


