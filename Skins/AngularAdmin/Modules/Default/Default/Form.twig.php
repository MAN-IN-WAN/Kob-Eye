<?php
$info = Info::getInfos($vars['Query']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['fields'] = $o->getElementsByAttribute('list','',true);
$vars['formfields'] = $o->getElementsByAttribute('form','',true);
$vars["CurrentObj"] = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();
$vars["ChildrenElements"] = $vars["ObjectClass"]->getChildElements();
?>