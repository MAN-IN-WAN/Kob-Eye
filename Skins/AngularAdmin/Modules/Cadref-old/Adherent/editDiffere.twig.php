<?php

$info= Info::getInfos($vars['Query']);
$obj = Sys::getOneData($info['Module'],$vars['Query']);

$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$temp = $o->getElementsByAttribute('','',true);
$fields = Array();
foreach ($temp as $k=>$field){
	$fields[$field['name']] = $field;
}
$vars['fields'] = $fields;

if (is_object(Sys::$CurrentMenu)) $vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
else $vars['CurrentUrl'] = $vars['Query'];

$vars['scopeObj'] = 'modalObj';
$vars['Annee'] = $GLOBALS['Systeme']->getRegVars('AnneeEnCours');
$diff = $obj->getChildren('Reglement/Differe=1&Supprime=0&Annee='.$vars['Annee']);
foreach($diff as $d) {
	$d->Mois = date('m', $d->DateReglement);
}
$vars['diff'] = $diff;

