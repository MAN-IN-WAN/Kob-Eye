<?php
class Inscription extends genericClass {
	
	
	function Delete() {
		throw new Exception('Une inscription ne peut être supprimée');
	}
	
	public static function OpenWeb($obj) {
		$mnu = Sys::getOneData('Systeme', 'Menu/Url=adh_inscriptions');
		if($obj['Save']) {
			$mnu->Affiche = $obj['Open'] ? 1 : 0;
			$mnu->Save();
		}
		return $mnu->Affiche != 0;
	} 
	
	function PrintStatistique($obj) {
		require_once ('cadrefStat.class.php');
		require_once ('PrintStatistique.class.php');

		$annee = substr($obj['DateDebut'],6);
		$ddeb = DateTime::createFromFormat('d/m/Y H:i:s', $obj['DateDebut'].' 00:00:00')->getTimestamp(); 
		$dfin = DateTime::createFromFormat('d/m/Y H:i:s', $obj['DateFin'].' 23:59:59')->getTimestamp();
		$file = $file = 'Home/tmp/Statistiques'.date('YmdHi').'.pdf';
		$title = 'Statistiques '.$obj['DateDebut'].'-'.$obj['DateFin'];
		
		$antennes = "ABGLNSV";
		$stats = new cadrefStatList(7);
		
		// antennes
		$antCount = array();
		$sql = "
select count(*) as cnt,Antenne from (
select distinct i.Numero, i.Antenne
from `##_Cadref-Inscription` i   
where i.DateInscription>=1533074400 and i.DateInscription<=1564610399 and (i.Supprime=0 or i.DateSupprime>1564610399) ) t
group by Antenne
order by Antenne";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);
		foreach($pdo as $p) {
			$col = strpos($antennes, $p['Antenne']);
			$antCount[$col] = $p['cnt'];
		}

		// inscriptions
		$intCount = array();
		$sql = "
select count(*) as cnt,Antenne
from `##_Cadref-Inscription` i   
where i.DateInscription>=1533074400 and i.DateInscription<=1564610399 and (i.Supprime=0 or i.DateSupprime>1564610399) 
group by Antenne
order by Antenne";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);
		foreach($pdo as $p) {
			$col = strpos($antennes, $p['Antenne']);
			$insCount[$col] = $p['cnt'];
		}

		// sexe
		$sql = "
select distinct i.Numero, i.Antenne, if(h.Sexe='','Z',h.Sexe) as sex
from `##_Cadref-Inscription` i 
inner join `##_Cadref-Adherent` h on h.Id=i.AdherentId
where i.DateInscription>=$ddeb and i.DateInscription<=$dfin and (i.Supprime=0 or i.DateSupprime>$dfin)
order by sex, i.Antenne";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);
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
$zzz=		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);
		foreach($pdo as $p) {
			$col = strpos($antennes, $p['Antenne']);
			$lib = $p['prof'] != 'ZZZZ' ? $p['Libelle'] : 'Non renseigné';
			$stats->Sum(1, $p['prof'], $lib, $col, 1);
		}
		
		// Situation
		$sql = "
select distinct i.Numero, i.Antenne, ifnull(h.SituationId,'ZZZZ') as situ, c.Libelle
from `##_Cadref-Inscription` i 
inner join `##_Cadref-Adherent` h on h.Id=i.AdherentId
left join `##_Cadref-Situation` c on c.Id=h.SituationId
where i.DateInscription>=$ddeb and i.DateInscription<=$dfin and (i.Supprime=0 or i.DateSupprime>$dfin)
order by situ, i.Antenne";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);
		foreach($pdo as $p) {
			$col = strpos($antennes, $p['Antenne']);
			$lib = $p['situ'] != 'ZZZZ' ? $p['Libelle'] : 'Non renseigné';
			$stats->Sum(2, $p['situ'], $lib, $col, 1);
		}

		// origine
		$sql = "
select distinct i.Numero, i.Antenne, if(h.Origine='' or h.Origine is null,'Z',h.Origine) as orig
from `##_Cadref-Inscription` i 
inner join `##_Cadref-Adherent` h on h.Id=i.AdherentId
where i.DateInscription>=$ddeb and i.DateInscription<=$dfin and (i.Supprime=0 or i.DateSupprime>$dfin)
order by orig, i.Antenne";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);
		foreach($pdo as $p) {
			$col = strpos($antennes, $p['Antenne']);
			switch($p['orig']) {
				case 'I': $lib = 'Internet'; break;
				case 'P': $lib = 'Publicité'; break;
				case 'R': $lib = 'Recommandation'; break;
				case 'Z': $lib = 'Non renseigné'; break;
			}
			$stats->Sum(3, $p['orig'], $lib, $col, 1);
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
		if(! $pdo) return array('sql'=>$sql);
		//$annee = Cadref::$Annee;
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
			$stats->Sum(4, $s, $lib, $col, 1);
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
		if(! $pdo) return array('sql'=>$sql);
		foreach($pdo as $p) {
			$col = strpos($antennes, $p['Antenne']);
			$stats->Sum(5, $p['cours'], $p['cours'].' cours', $col, 1);
		}

		// villes
		$sql = "
select distinct i.Numero, i.Antenne, if(h.CP='30900','30000',h.CP) as cp, h.Ville
from `##_Cadref-Inscription` i 
inner join `##_Cadref-Adherent` h on h.Id=i.AdherentId
where i.DateInscription>=$ddeb and i.DateInscription<=$dfin and (i.Supprime=0 or i.DateSupprime>$dfin)
order by h.Ville, i.Antenne";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);
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
			$stats->Sum(6, $code, $lib, $col, 1);
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
			$stats->Sum(7, $p['disc'], $p['disc'].' : '.$p['lib'], $col, 1);
		}

		// discipline web
		$sql = "
select distinct count(*) as cnt, i.Antenne, ifnull(ws.Libelle,'Non affecté') as lib, ws.WebSection
from `##_Cadref-Inscription` i 
inner join `##_Cadref-Classe` c on c.Id=i.ClasseId
inner join `##_Cadref-Niveau` n on n.id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
left join  `##_Cadref-WebDiscipline` wd on wd.id=d.WebDisciplineId
left join `##_Cadref-WebSection` ws on ws.id=wd.WebSectionId
where i.DateInscription>=$ddeb and i.DateInscription<=$dfin and (i.Supprime=0 or i.DateSupprime>$dfin)
group by lib,i.Antenne
order by lib,i.Antenne";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);
		foreach($pdo as $p) {
			$col = strpos($antennes, $p['Antenne']);
			$stats->Sum(8, $p['WebSection'], $p['lib'], $col, $p['cnt']);
		}

		
		$pdf = new PrintStatistique($obj['DateDebut'], $obj['DateFin'], 7, $antCount, $insCount);
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle(iconv('UTF-8','ISO-8859-15//TRANSLIT',$title));
		
		$pdf->AddPage();
		$pdf->PrintLines($stats);

		$pdf->Output(getcwd().'/'.$file);
		$pdf->Close();
		
		return array('pdf'=>$file, 'sql'=>$zzz);
	}

	function PrintFinance($obj) {
		require_once ('PrintFinance.class.php');
		
		$annee = $obj['Annee'];
		$sql = "select count(*) as nbr,sum(Cotisation) as cotis,sum(AvoirUtilise) as avoir from `##_Cadref-AdherentAnnee` where Annee='$annee' and Cotisation>0";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$d = $pdo->fetch(PDO::FETCH_ASSOC);
		$nbcot = $d['nbr'];
		$cotis = $d['cotis'];
		$avoir = $d['avoir'];
		
		$sql = "
select i.CodeClasse,d.Libelle as libelleD,n.Libelle as libelleN,count(*) as inscrits,
c.Prix,sum(i.Prix) as total,sum(i.Reduction) as red,sum(if(i.Reduction>0,1,0)) as nred, 
sum(i.Soutien) as red2,sum(if(i.Soutien>0,1,0)) as nred2
from `##_Cadref-Inscription` i
inner join `##_Cadref-Classe` c on c.Id=i.ClasseId
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
where i.Annee='$annee' and i.Supprime=0 and i.Attente=0
group by i.CodeClasse
order by i.CodeClasse
";

		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('pdf'=>'', 'sql'=>$sql);
		
		if($obj['mode'] == 'export') {
			$file = 'Home/tmp/RapportFinancier_'.date('YmdHis').'.csv';
			$f = fopen($file, 'w');
			$s = '"Classe";"Libellé";"Inscrits";"Prix";"Remise";"Nb remises";"Soutien";"Nb soutiens";"Total"';
			fwrite($f, Cadref::cv2win("$s\n"));
			
			foreach($pdo as $p) {
				$s = '"'.$p['CodeClasse'].'";';
				$s .= '"'.$p['libelleD'].' '.$p['libelleN'].'";';
				$s .= $p['inscrits'].';';
				$s .= $p['Prix'].';';
				$s .= $p['red'].';';
				$s .= $p['nred'].';';
				$s .= $p['red2'].';';
				$s .= $p['nred2'].';';
				$s .= $p['total']-$p['red']-$p['red2'].';';
				fwrite($f, Cadref::cv2win("$s\n"));
			}
			fclose($f);
			return array('csv'=>$file, 'sql'=>$sql);
		}
			
		$pdf = new PrintFinance($nbcot,$cotis,$avoir);
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle(iconv('UTF-8','ISO-8859-15//TRANSLIT',"Rapport Financier"));

		$pdf->AddPage();
		$pdf->PrintLines($pdo);
		$pdf->PrintTotal();

		$file = '/Home/tmp/RapportFinancier_'.date('YmdHis').'.pdf';
		$pdf->Output(getcwd().$file);
		$pdf->Close();
		
		return array('pdf'=>$file, 'sql'=>$sql);
	}


}


