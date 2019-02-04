<?php

if(isset($vars['Path'])) $Path = $vars['Path'];
else $vars['Path'] = $Path = $vars['Query'];
$info = Info::getInfos($Path);
$o = genericClass::createInstance($info['Module'], $info['ObjectType']);
$o->setView();
$vars['identifier'] = $info['Module'] . $info['ObjectType'];
$vars['ObjectClass'] = $o->getObjectClass();
$vars['ObjectType'] = $info['ObjectType'];
$vars['Module'] = $info['Module'];
$vars['ObjectType'] = $info['ObjectType'];
$temp = $o->getElementsByAttribute('', '', true);
$fields = Array();
foreach($temp as $k => $field) {
	$fields[$field['name']] = $field;
}
$vars['fields'] = $fields;

if(is_object(Sys::$CurrentMenu)) $vars['CurrentUrl'] = Sys::getMenu($info['Module'] . '/' . $info['ObjectType']);
else $vars['CurrentUrl'] = $Path;


if(!isset($info['ObjectType'])) {
	$tab = explode('/', $info['Query']);
	array_push($tab, 'Form');
} else {
	$tab = array($info['Module'], $info['ObjectType'], 'Form');
}
$vars['Annee'] = Cadref::$Annee;
$vars['context'] = 'children';

$vars['formPath'] = 'Systeme/Utils/Form';

if (!isset($info['ObjectType'])) {
$tab = explode('/', $info['Query']);
	array_push($tab, 'Form');
} else {
	$tab = array($info['Module'], $info['ObjectType'], 'Fiche');
}
$blinfo = Bloc::lookForInterface($tab, 'Skins/AngularAdmin/Modules', true);
if(strpos($blinfo, '/'.$info['Module'].'/')) {
	$p = strpos($blinfo, 'Modules/') + strlen('Modules/');
	$vars['formPath'] = substr(trim($blinfo, '.twig'), $p);
}

?>