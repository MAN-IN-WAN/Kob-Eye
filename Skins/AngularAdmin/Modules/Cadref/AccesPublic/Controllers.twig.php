<?php
$vars['Annee'] = Cadref::$Annee;
$vars['module'] = 'Cadref';
$menu = Sys::$CurrentMenu->Url;
$login = Sys::$User->Login;
$group = Sys::$User->getParents('Group')[0]->Nom;

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