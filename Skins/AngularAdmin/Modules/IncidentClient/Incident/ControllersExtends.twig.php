<?php
$info = Info::getInfos($vars['Chemin']);

$vars['CurrentObj'] = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['identifier'] = $vars['Url'];

$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();
//$vars["ChildrenElements"] = $vars["ObjectClass"]->getChildElements();
//$vars['Interfaces'] = $vars["ObjectClass"]->getInterfaces();

$vars["uid"] = Sys::$User->Id;

?>