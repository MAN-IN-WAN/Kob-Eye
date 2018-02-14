<?php
$info = Info::getInfos($vars['Chemin']);

$vars['CurrentObj'] = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['identifier'] = $info['Module'].$info['ObjectType'];

$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();
//$vars["ChildrenElements"] = $vars["ObjectClass"]->getChildElements();
//$vars['Interfaces'] = $vars["ObjectClass"]->getInterfaces();

?>