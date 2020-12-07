<?php
$vars['Annee'] = Cadref::$Annee;
$vars['module'] = 'Cadref';
$vars['objecttype'] = 'Adherent';
$vars['identifier'] = $vars['module'].$vars['objecttype'];
$o = genericClass::createInstance($vars['module'],$vars['objecttype']);
$vars['CurrentMenu'] = Sys::$CurrentMenu;
$vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
$vars["CurrentObj"] = $o;

$n = Sys::$User->Login;
$ad = Sys::getOneData('Cadref', 'Adherent/Numero='.$n);
$first = '';
$ta = array();
$as = $ad->getChildren('AdherentAnnee/Cotisation!=0');
foreach($as as $a) {
	if(!$first) $first = $a->Annee;
	$ta[$a->Annee] = $a->Annee.'-'.($a->Annee+1);
}
$vars['attestAnnees'] = $ta;
$vars['first'] = $first;
