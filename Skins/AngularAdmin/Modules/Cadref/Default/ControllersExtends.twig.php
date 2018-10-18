<?php
$info = Info::getInfos($vars['Chemin']);
if($info['ObjectType']){
	$vars['CurrentObj'] = genericClass::createInstance($info['Module'],$info['ObjectType']);
	$vars['identifier'] = $vars['Url'];
	$vars['ident'] = $info['Module'].$info['ObjectType'];

	$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();
	//$vars["ChildrenElements"] = $vars["ObjectClass"]->getChildElements();
	//$vars['Interfaces'] = $vars["ObjectClass"]->getInterfaces();

	$vars['description'] = $vars['CurrentObj']->getDescription();
} else {
	$vars['noExtend'] = true;
}
?>