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
$vars['tempContext'] = isset($_GET['tempContext']) && $_GET['tempContext'];
$vars['Annee'] = Cadref::$Annee;

?>