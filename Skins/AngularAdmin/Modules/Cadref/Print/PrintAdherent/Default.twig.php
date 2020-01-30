<?php

// fiche printAdherent
$vars['Annee'] = $annee = Cadref::$Annee;
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

	$tmp = array('' => '');
	$as = Sys::getData('Cadref', 'Enseignant');
	foreach($as as $a)
		$tmp[$a->Id] = trim($a->Nom.' '.$a->Prenom);
	$vars['enseignants'] = $tmp;

	$tmp = array('' => '');
	$as = Sys::getData('Cadref', 'Visite/Annee='.$annee);
	foreach($as as $a)
		$tmp[$a->Id] = date('d/m', $a->DateVisite).' - '.$a->Libelle;
	$vars['visites'] = $tmp;

	$tmp = array();
	$ans = Sys::getData('Cadref', 'Annee');
	foreach($ans as $an) {
		$tmp[$an->Annee] = $an->EnCours;
	}
	$vars['annees'] = $tmp;
	
	$tmp = array('' => '');
	$as = Sys::getData('Cadref', 'Lieu');
	foreach($as as $a)
		$tmp[$a->Id] = $a->Lieu.' '.$a->Libelle;
	$vars['lieux'] = $tmp;

	$tmp = array('' => '');
	$as = Sys::getData('Cadref', 'Jour');
	foreach($as as $a)
		$tmp[$a->Id] = $a->Jour;
	$vars['jours'] = $tmp;
	

	$vars['ruptures'] = array('D' => 'Disciplines', 'N' => 'Niveaux', 'C' => 'Classes', 'S' => 'Sans rupture');
	$vars['contenu'] = array('' => '', 'E' => 'Elèves seulement', 'N' => 'Impression du nom', 'A' => 'Impression nom et adresse', 'Q' => 'Etiquettes');
	$vars['mails'] = array('' => '', 'A' => 'Avec', 'S' => 'Sans');
	$vars['inscrits'] = array('' => '', 'I' => 'Inscrit', 'A' => 'Attente');
	$tmp = array('');
	for($a = $annee, $i = 0; $i < 4; $i++) $tmp[] = --$annee;
	$vars['noninscr'] = $tmp;
}
