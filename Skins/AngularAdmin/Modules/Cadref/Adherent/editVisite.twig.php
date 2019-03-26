<?php

$info= Info::getInfos($vars['Query']);
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
$vars['res'] =  true;
$vars['Annee'] = Cadref::$Annee;

