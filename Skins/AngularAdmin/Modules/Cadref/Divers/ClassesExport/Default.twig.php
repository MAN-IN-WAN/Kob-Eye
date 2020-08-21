<?php
// fiche SelectAnnee
$annee = Cadref::$Annee;
$vars['Annee'] = $annee;
$vars['CurrentMenu'] = Sys::$CurrentMenu;
if(Sys::$User->Admin && !$vars['CurrentMenu']){
    $oc = $o->getObjectClass();
    $vars['CurrentMenu'] = ['Titre' =>$oc->Description ];
}
$vars['identifier'] = 'CadrefClasse';

$tmp = array();
$ans = Sys::getData('Cadref', 'Annee');
foreach($ans as $an) {
	$tmp[$an->Annee] = $an->EnCours;
}
$vars['annees'] = $tmp;

