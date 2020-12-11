<?php
$vars['Annee'] = Cadref::$Annee;
$vars['module'] = 'Cadref';
$menu = is_object(Sys::$CurrentMenu) ? Sys::$CurrentMenu->Url : '';
$login = Sys::$User->Login;

$vars['benevole'] = 0;
$groups = Sys::$User->getParents('Group');
foreach($groups as $g) {
	if($g->Nom == 'CADREF_BENE') $vars['benevole'] = 1; 
	if($g->Nom == 'CADREF_ADH' || $g->Nom == 'CADREF_ENS' || $g->Nom == 'CADREF_BENE') $group = $g->Nom;
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
elseif($group == 'CADREF_BENE') {
	$vars['identifier'] = 'CadrefAdherent';
	$vars['objecttype'] = 'Adherent';
	$vars['url'] = 'Cadref/Adherent';
}


?>