<?php
$vars['Annee'] = Cadref::$Annee;
$vars['module'] = 'Cadref';
$vars['objecttype'] = '';
$vars['identifier'] = $vars['module'].$vars['objecttype'];
$o = genericClass::createInstance($vars['module'],$vars['objecttype']);
$vars['CurrentMenu'] = Sys::$CurrentMenu;
$vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
$vars["CurrentObj"] = $o;

$n = Sys::$User->Login;
$en = Sys::getOneData('Cadref', 'Enseignant/Code='.substr($n, 3));
//klog::l(">>>>>>>>$n ".substr($n,3), )
$tmp = array(''=>'Toutes mes classes');
$cl = $en->getChildren('Classe/Annee='.Cadref::$Annee);
foreach($cl as $c) {
	$tmp[$c->CodeClasse] = $c->CodeClasse.' - '.$c->LibelleW.' '.$c->LibelleN;
}
$vars['classes'] = $tmp;
$vars['mois'] = array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre');
$vars['encours'] = date('n')-1;