<?php
$info = Info::getInfos($vars['Chemin']);

$vars['CurrentObj'] = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['identifier'] = $vars['Url'];
$vars['keIdentifier'] = $info['Module'].$info['ObjectType'];

$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();