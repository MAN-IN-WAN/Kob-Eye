<?php
if (isset($vars['Path']))
    $Path = $vars['Path'];
else
    $vars['Path'] = $Path = $vars['Query'];
$info = Info::getInfos($Path);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['identifier'] = $info['Module'].$info['ObjectType'];
$vars['context'] = $info['NbHisto'] > 1 ? 'children':'default';

$vars['context'] = isset($vars['templateContext']) ? $vars['templateContext']: $vars['context'];

?>