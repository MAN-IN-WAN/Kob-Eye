<?php
$info = Info::getInfos($vars['Chemin']);
if(isset($info['ObjectType'])){
	$vars['CurrentObj'] = genericClass::createInstance($info['Module'],$info['ObjectType']);
	$vars['identifier'] = $vars['Url'];
	$vars['ident'] = $info['Module'].$info['ObjectType'];
	$vars['module'] = $info['Module'];
	$vars['objecttype'] = $info['ObjectType'];

	$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();
	//$vars["ChildrenElements"] = $vars["ObjectClass"]->getChildElements();
	//$vars['Interfaces'] = $vars["ObjectClass"]->getInterfaces();

	$vars['description'] = $vars['CurrentObj']->getDescription();

	$vars['url'] = Sys::getMenu($info['Module'].'/'.$info['ObjectType']);
	
} else {
	$vars['noExtend'] = true;
}
$vars['Annee'] = $GLOBALS['Systeme']->getRegVars('AnneeEnCours');
?>