<?php

class Reglement extends genericClass {
	
	function Save($mode = false) {
		$ret = parent::Save();
		if(! $mode) {
			$adh = $this->getOneParent('Adherent');
			$adh->SaveAnnee(new stdClass(), 2);	
		}
		return $ret;
	}

	function PrintReglement($obj) {
		require_once ('PrintReglement.class.php');

		$menus = ['impressionslistereglements','impressionsreglementsdifferes','impressionsdifferesnonencaisses'];
		$mode = array_search($obj['CurrentUrl'], $menus);
		
		$user = $obj['Utilisateur'];
		$file = 'Home/tmp/';
		switch($mode) {
			case 0:
				$ddeb = DateTime::createFromFormat('d/m/Y H:i:s', $obj['DateDebut'].' 00:00:00')->getTimestamp(); 
				$dfin = DateTime::createFromFormat('d/m/Y H:i:s', $obj['DateFin'].' 23:59:59')->getTimestamp();
				$where = "r.DateReglement>=$ddeb and r.DateReglement<=$dfin and (r.Differe=0 or r.Encaisse=1)";
				$title = 'Règlements '.$user.' '.$obj['DateDebut'].'-'.$obj['DateFin'];
				$file .= 'Reglements_'.$user.'_'.date('Ymd', $ddeb).'_'.date('Ymd', $dfin).'.pdf';
				break;
			case 1:
				$ddeb = DateTime::createFromFormat('d/m/Y H:i:s', '01/'.$obj['DateDebut'].' 00:00:00')->getTimestamp();
				$dfin = DateTime::createFromFormat('d/m/Y H:i:s', '01/'.$obj['DateDebut'].' 00:00:00'); 
				$dfin->add(DateInterval::createFromDateString('1 month'));
				$dfin->add(DateInterval::createFromDateString('1 second'));
				$dfin = $dfin->getTimestamp();
				$where = "r.DateReglement>=$ddeb and r.DateReglement<$dfin and r.Differe=1";
				$title = 'Différés '.$obj['DateDebut'];
				$file .= 'Differes_'.substr($obj['DateDebut'], 3).substr($obj['DateDebut'], 0, 2).'.pdf';
				break;
			case 2:
				$ddeb = DateTime::createFromFormat('d/m/Y H:i:s', '01/'.$obj['DateDebut'].' 00:00:00')->getTimestamp(); 
				$where = "r.DateReglement<$ddeb and r.Differe=1 and r.Encaisse=0";
				$title = 'Différés non encaissés '.$obj['DateDebut'];
				$file .= 'Differes_non_encaisses_'.substr($obj['DateDebut'], 3).substr($obj['DateDebut'], 0, 2).'.pdf';
		}

		$sql = "
select r.Utilisateur,r.DateReglement,r.Montant,r.ModeReglement,h.Numero,h.Nom,h.Prenom,r.Differe,r.Encaisse
from `##_Cadref-Reglement` r 
inner join `##_Cadref-Adherent` h on h.Id=r.AdherentId
where ".$where;
		$sql .= " and r.Supprime=0 ";	
		if($user != '') $sql .= " and r.Utilisateur='$user' ";
		$sql .= " order by r.DateReglement, h.Nom, h.Prenom";
		
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);

		$pdf = new PrintReglement($mode, $user, $obj['DateDebut'], $obj['DateFin']);
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle(iconv('UTF-8','ISO-8859-15//TRANSLIT',$title));

		$pdf->AddPage();
		$pdf->PrintLines($pdo);
		$pdf->PrintTotal();

		$pdf->Output(getcwd().'/'.$file);
		$pdf->Close();
		
		return array('pdf'=>$file, 'sql'=>$sql);
	}

	
}
