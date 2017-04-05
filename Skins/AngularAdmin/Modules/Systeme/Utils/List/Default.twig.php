<?php
$info = Info::getInfos($vars['Query']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['identifier'] = $info['Module'].$info['ObjectType'];
$vars['fields'] = $o->getElementsByAttribute('list','',true);
$vars['filters'] = $o->getCustomFilters();
if (is_object(Sys::$CurrentMenu))
    $vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
else $vars['CurrentUrl'] = $vars['Query'];
?>