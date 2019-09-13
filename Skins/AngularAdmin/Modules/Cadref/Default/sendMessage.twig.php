<?php
$annee = Cadref::$Annee;

$vars['mode'] = 'public';
$menu = Sys::$CurrentMenu->Url;
if($menu == 'ens_message') {
	$n = substr(Sys::$User->Login, 3, 3);
	$e = Sys::getOneData('Cadref', 'Enseignant/Code='.$n);
	$id = $e->Id;

	$to = array('C'=>'Cadref (Secrétariat)','T'=>'Tous mes élèves');
	$sql = "
select distinct c.Id, concat(ifnull(dw.Libelle,d.Libelle),' ',ifnull(n.Libelle,'')) as Libelle,
CycleDebut, CycleFin
from `##_Cadref-ClasseEnseignants` ce
inner join `##_Cadref-Classe` c on c.Id=ce.Classe
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
left join `##_Cadref-WebDiscipline` dw on dw.Id=d.WebDisciplineId
left join `##_Cadref-Lieu` l on l.Id=c.LieuId
where ce.EnseignantId=$id and c.Annee='$annee'
";
	$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
	$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
	foreach($pdo as $p) {
		$s = $p['Libelle'];
		if($p['CycleDebut']) $s .= ' ('.$p['CycleDebut'].' '.$p['CycleFin'].')';
		$to[$p['Id']] = $s;
	}
}
elseif($menu == 'adh_message') {
	$n = Sys::$User->Login;
	$a = Sys::getOneData('Cadref', 'Adherent/Numero='.$n);
	$id = $a->Id;
	
	$to = array('C'=>'Cadref (Secrétariat)');
	$sql = "
select distinct Mail, Nom, Prenom
from `##_Cadref-Inscription` i
inner join `##_Cadref-ClasseEnseignants` ce on ce.Classe=i.ClasseId
inner join `##_Cadref-Enseignant` e on e.Id=ce.EnseignantId
where i.AdherentId=$id and e.Mail<>''";
	$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
	$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
	foreach($pdo as $p) {
		$s = $p['Prenom'].' '.$p['Nom'];
		$to[$p['Mail']] = $s;
	}
}
else $vars['mode'] = 'admin';
$vars['destinataires'] = $to;
