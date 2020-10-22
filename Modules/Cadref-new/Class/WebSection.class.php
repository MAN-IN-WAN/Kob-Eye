<?php
class WebSection extends genericClass {
	
	function Delete() {
		$rec = $this->getChildren('WebDiscipline');
		if(count($rec)) throw new Exception('Cette section ne peut être supprimée');

		return parent::Delete();
	}
	
	function Export($args) {
		$annee = Cadref::$Annee;
		$antenne = 3;
		$rupSec = 0;
		$rupDis = 0;
		$html = '';
		
		
		$sql = "
select a.Libelle as LibelleA, ws.Is as secId, ws.Libelle as LibelleS, wd.Id as disId, wd.Libelle as LibelleD, n.Libelle as LibelleN,
wd.Description as descrD, n.Description as descrN, j.Jour, c.HeureDebut, c.HeureFin, c.CycleDebut, c.CycleFin, c.Programmation, c.Prix,
l.Adresse1, l.Adresse2, l.Ville
from `##_Cadref-WebSection` ws
left join `##_Cadref-WebDiscipline` wd on wd.WebSectionId=ws.Id
left join `##_Cadref-Discipline` d on d.WebDisciplineId=wd.Id
left join `##_Cadref-Niveau` n on n.DisciplineId=d.Id
left join `##_Cadref-Classe` c on c.NiveauId=n.id
left join `##_Cadref-Jour` j on j.Id=c.JourId
left join `##_Cadref-Lieu` l on l.Id=c.LieuId
left join `##_Cadref-Antenne` a on a.id=n.AntenneId
where n.AntenneId=$antenne and n.AccesWeb=1 and c.AccesWeb=1 and c.Annee='$annee'
order by LibelleS, LibelleD, n.Niveau, j.Jour, c.HeureDebut 
";
		
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		foreach($pdo as $p) {
			$sec = $p['secId'];
			if($sec != $rupSec) {
				if($rupSec) $html .= "<p><br>********** Fin de section<br></p>";
				$t = $p['LibelleS'];
				$html .= "<p><h2>$t</h2></p>";
				$rupSec = $sec;
				$rupDis = 0;
			}
			$dis = $p['disId'];
			if($dis != $rupDis) {
				if($rupDis) {
					$html .= "</tbody></table>";
					$html .= "<p>$descr</p><p><br><br></p>";
				}
				$rupDis = $dis;
				
				$lieux = array();
				$t = $p['LibelleD'].' ('.$p['LibelleA'].')';
				$html .= "<p><h2>$t</h2></p>";
				$html .= '<table><tr class="head"><td>Niveau</td><td>';
				
				$descr = $p['descrD'];
				$lieu = $p['Adresse1'];
				if($p['Adresse2']) $lieu .= ' - '.$p['Adresse2'];
				if($lieu) $html .= "<p>Lieu : $lieu</p>";
			}
			
			if($p['descrN']) $descr .= '<br>'.$p['descrN'];

			$libn = $p['LibelleN'];
			
		}
	}
	

	private function exportEnseignants($cid) {
		$s = '';
		
		$sql = "
select e.Prenom, e.Nom
from `##_Cadref-ClasseEnseignants` ce
inner join `##_Cadref-Enseignant` e on c.Id=ce.EnseignantId
where ce.Classe=$cid and e.AccesWeb=1
";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		foreach($pdo as $p) {
			if($s) $s .= ', ';
			$s .= trim($p['Prenom']).' '.$p['Nom'];
		}
		return $s;
	} 
	private function exportDates($cid) {
		$s = array();
		
		$sql = "
select DateCours
from `##_Cadref-ClasseDate`
where cd.Classe=$cid
order by DateCours
";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		foreach($pdo as $p) {
			$s[] = date('d/m', $p['DateCours']);
		}
		return $s;
	} 
	
}
