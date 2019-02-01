<?php

// fiche printAdherent
$vars['Annee'] = Cadref::$Annee;
$vars['CurrentMenu'] = Sys::$CurrentMenu;
if(Sys::$User->Admin && !$vars['CurrentMenu']) {
	$oc = $o->getObjectClass();
	$vars['CurrentMenu'] = ['Titre' => $oc->Description];
}
$vars['identifier'] = 'CadrefAdherent';

$menus = ['impressionslisteadherents', 'impressionscertificatesmedicaux', 'impressionsfichesincompletes'];
$t = explode('/', Sys::$CurrentMenu->Url);
$vars['mode'] = array_search($t[0] . $t[1], $menus);

if($vars['mode'] == 0) {
	$tmp = array('' => '');
	$gs = Sys::getData('Systeme', 'Group/Nom=CADREF_ADMIN/*/User');
	foreach($gs as $g)
		$tmp[] = $g->Initiales;
	$vars['users'] = $tmp;
	$vars['initiales'] = Sys::$User->Initiales;

	$vars['typeAdh'] = array('' => '', 'B' => 'Bureau', 'A' => 'Administrateur', 'D' => 'Délégué', 'S' => 'Simple adhérent');

	$tmp = array('' => '');
	$as = Sys::getData('Cadref', 'Antenne');
	foreach($as as $a)
		$tmp[$a->Id] = $a->Libelle;
	$vars['antennes'] = $tmp;

	$tmp = array();
	$as = Sys::getData('Cadref', 'Jour');
	foreach($as as $a)
		$tmp[$a->Id] = $a->Jour;
	$vars['jours'] = $tmp;

	$vars['ruptures'] = array('D' => 'Disciplines', 'N' => 'Niveaux', 'C' => 'Classes', 'S' => 'Sans rupture');
	$vars['contenu'] = array('' => '', 'E' => 'Elèves seulement', 'N' => 'Impression du nom', 'A' => 'Impression nom et adresse', 'Q' => 'Etiquettes');
	$vars['mails'] = array('' => '', 'A' => 'Avec', 'S' => 'Sans');
	$vars['attentes'] = array('' => '', 'I' => 'Inscrit', 'A' => 'Attente');
}
