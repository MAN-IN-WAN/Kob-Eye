<?php
if (is_object(Sys::$CurrentMenu))
    $vars['controller'] = str_replace('/','',Sys::$CurrentMenu->Url);
else
    $vars['controller'] = str_replace('/','',$vars['Query']);
$info = Info::getInfos($vars['Query']);
$vars['CurrentObj'] = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['identifier'] = $info['Module'].$info['ObjectType'];
$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();
$vars["ChildrenElements"] = $vars["ObjectClass"]->getChildElements();
$vars['Interfaces'] = $vars["ObjectClass"]->getInterfaces();
?>