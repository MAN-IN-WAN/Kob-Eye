<?php
// fiche printPresence
$vars['Annee'] = Cadref::$Annee;
$vars['CurrentMenu'] = Sys::$CurrentMenu;
if(Sys::$User->Admin && !$vars['CurrentMenu']){
    $oc = $o->getObjectClass();
    $vars['CurrentMenu'] = ['Titre' =>$oc->Description ];
}
$vars['identifier'] = 'CadrefPresence';
$vars['mois'] = array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre');

$tmp = array();
$ans = Sys::getData('Cadref', 'Annee');
foreach($ans as $an) {
	$tmp[$an->Annee] = $an->EnCours;
}
$vars['annees'] = $tmp;


