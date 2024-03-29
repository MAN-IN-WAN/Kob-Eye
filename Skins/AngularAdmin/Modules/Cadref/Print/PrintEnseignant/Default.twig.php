<?php
// fiche printEnseignant
$vars['Annee'] = $annee = Cadref::$Annee;
$vars['CurrentMenu'] = Sys::$CurrentMenu;
if(Sys::$User->Admin && !$vars['CurrentMenu']){
    $oc = $o->getObjectClass();
    $vars['CurrentMenu'] = ['Titre' =>$oc->Description ];
}
$vars['identifier'] = 'CadrefEnseignant';

$antenne = 0;
$groups = Sys::$User->getParents('Group');
foreach($groups as $group) {
	if($group->Nom == 'CADREF_SITE') {
		$n = Sys::$User->Login;
		$a = Sys::getOneData('Cadref', "AdherentAnnee/Numero=$n&Annee=$annee");
		$antenne = $a->AntenneId;
		break;
	}
}
	
if($antenne) {
	$to = array('0'=>"Tous le enseignants de l'antenne");
	$sql = "
	select distinct e.Id,e.Nom,e.Prenom
	from `##_Cadref-ClasseEnseignants` ce 
	inner join `##_Cadref-Classe` c on c.Id=ce.Classe
	inner join `##_Cadref-Enseignant` e on e.Id=ce.EnseignantId
	where c.AntenneId=$antenne and c.Annee='$annee' and e.Mail<>''
	order by Nom,Prenom";
}
else {
	$to = array('0'=>'Tous le enseignants');
	$sql = "select Id,Nom,Prenom from `##_Cadref-Enseignant` where Mail<>'' order by Nom,Prenom";
}
$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
foreach($pdo as $p) {
	$s = $p['Nom'].' '.$p['Prenom'];
	$to[$p['Id']] = $s;
}
$vars['destinataires'] = $to;
