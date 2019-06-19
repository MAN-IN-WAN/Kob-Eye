<?php
// fiche CreateAnnee
$annee = Cadref::$Annee;
$vars['Annee'] = $annee;
$vars['CurrentMenu'] = Sys::$CurrentMenu;
if(Sys::$User->Admin && !$vars['CurrentMenu']){
    $oc = $o->getObjectClass();
    $vars['CurrentMenu'] = ['Titre' =>$oc->Description ];
}
$vars['identifier'] = 'CadrefAnnee';

$vars['last'] = '';
$vars['Cotisation'] = 0;
$vars['Reduction'] = 0;
$ans = Sys::getData('Cadref', 'Annee');
foreach($ans as $an) if($an->Annee > $vars['last']) {
	$vars['last'] = $an->Annee;
	$vars['Cotisation'] = $an->Cotisation;
	$vars['Reduction'] = $an->Reduction;
}
$vars['next'] = $vars['last'] + 1;

