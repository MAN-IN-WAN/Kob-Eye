<?php
$vars['Annee'] = Cadref::$Annee;
$vars['module'] = 'Cadref';
$menu = Sys::$CurrentMenu->Url;
$login = Sys::$User->Login;

$groups = Sys::$User->getParents('Group');
foreach($groups as $g) {
	if($g->Nom == 'CADREF_ADH' || $g->Nom == 'CADREF_ENS') {
		$group = $g->Nom;
		break;
	}
}

if($group == 'CADREF_ADH') {
	$vars['identifier'] = 'CadrefAdherent';
	$vars['objecttype'] = 'Adherent';
	$vars['entite'] = Sys::getOneData('Cadref', 'Adherent/Numero=' . $login);
	$vars['url'] = 'Cadref/Adherent';
}
elseif($group == 'CADREF_ENS') {
	$vars['identifier'] = 'CadrefEnseignant';
	$vars['objecttype'] = 'Enseignant';
	$vars['entite'] = Sys::getOneData('Cadref', 'Enseignant/code=' . substr($login, 3, 3));
	$vars['url'] = 'Cadref/Enseignant';
}


?>