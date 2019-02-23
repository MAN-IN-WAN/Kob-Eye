<?php

session_write_close();
$vars['Annee'] = Cadref::$Annee;

$info = Info::getInfos($vars['Query']);
$o = genericClass::createInstance($info['Module'], $info['ObjectType']);
$temp = $o->getElementsByAttribute('', '', true);
$fields = Array();
foreach($temp as $k=> $field) {
	if($info['TypeSearch'] == 'Direct' && ($field['type'] == 'metak' || $field['type'] == 'metad' || $field['type'] == 'metat' || $field['name'] == 'ImgMeta' )) {
		continue;
	}
	if(isset($field['query']) && !empty($field['query'])) {
		$t = explode('::', $field["query"]);
		if(sizeof($t) == 2) $t[2] = $t[1];
		$q = explode('/', $t[0], 2);
		$vals = Sys::getData($q[0], $q[1]);
		$field['query'] = array();
		foreach($vals as $v) {
			$field['query'][$v->{$t[1]}] = $v->{$t[2]};
		}
	}
	if(isset($field['help']) && $field['help']) {
		$field['helpLang'] = strtoupper("__".$info["Module"]."_".$info['ObjectType']."_".$field['name']."_HELP__");
	}
	if($field['type'] == 'fkey' && $field['card'] == 'short') {
		$field['link'] = Sys::getMenu($field['objectModule'].'/'.$field['objectName']);
	}
	$fields[$field['name']] = $field;
}
$fields['LibelleA'] = array('name'=>'LibelleA', 'type'=>'varchar', 'form'=>'1', 'fiche'=>'1');
$fields['LibelleS'] = array('name'=>'LibelleS', 'type'=>'varchar', 'form'=>'1', 'fiche'=>'1');
$fields['LibelleD'] = array('name'=>'LibelleD', 'type'=>'varchar', 'form'=>'1', 'fiche'=>'1');
$fields['LibelleN'] = array('name'=>'LibelleN', 'type'=>'varchar', 'form'=>'1', 'fiche'=>'1');
$fields['LibelleW'] = array('name'=>'LibelleW', 'type'=>'varchar', 'form'=>'1', 'fiche'=>'1');
$vars['fields'] = $fields;

$vars['functions'] = $o->getFunctions();
$vars['fichefields'] = $o->getElementsByAttribute('fiche', '', true);
if(!is_object(Sys::$CurrentMenu) && Sys::$User->Admin) {
	$vars['fichefields'] = $o->getElementsByAttribute('', '', true);
}

foreach($vars['fichefields'] as $k=> $f) {
	if($f['type'] == 'fkey' && $f['card'] == 'short') {
		$vars['fichefields'][$k]['link'] = Sys::getMenu($f['objectModule'].'/'.$f['objectName']);
	}
}
$vars['formfields'] = $o->getElementsByAttribute('form', '', true);
$vars['CurrentMenu'] = Sys::$CurrentMenu;
$vars["CurrentObj"] = genericClass::createInstance($info['Module'], $info['ObjectType']);
$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();
$vars['operation'] = $vars['ObjectClass']->getOperations();
$childs = $vars["ObjectClass"]->getChildElements();
$vars["ChildrenElements"] = array();

foreach($childs as $child) {
	if(
	//test role
		((!isset($child['hasRole']) || Sys::$User->hasRole($child['hasRole'])) &&
		//test hidden
		!isset($child['childrenHidden']) && !isset($child['hidden']))
		//test admin
		|| (!is_object(Sys::$CurrentMenu) && Sys::$User->Admin)) array_push($vars["ChildrenElements"], $child);
}
$vars["Interfaces"] = $vars["ObjectClass"]->getInterfaces();
$vars['identifier'] = $info['Module'].$info['ObjectType'];
if(is_object(Sys::$CurrentMenu)) $vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
else $vars['CurrentUrl'] = $vars['Query'];

$vars['browseable'] = $vars["ObjectClass"]->browseable;
$vars['CurrentObjQuery'] = $vars['Path'];
?>