<?php
if (isset($vars['Path']))
    $Path = $vars['Path'];
else
    $vars['Path'] = $Path = $vars['Query'];
$info = Info::getInfos($Path);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['identifier'] = $info['Module'].$info['ObjectType'];
$vars['context'] = $info['NbHisto'] > 1 ? 'children':'default';
if(isset($vars['templateContext']))
    $vars['context'] = $vars['templateContext'];

$vars['ObjectClass'] = $o->getObjectClass();
$vars['ObjectType'] = $info['ObjectType'];

$vars['CurrentUrl'] = Sys::getMenu($info['Module'] . '/' . $info['ObjectType']);



$vars['Module'] = $info['Module'];
$vars['functions'] = $o->getFunctions();
$vars['operation'] = $vars['ObjectClass']->getOperations();
$vars['fields'] = $o->getElementsByAttribute('list','',true);
$vars['searchfields'] = $o->getElementsByAttribute('searchOrder|search','',true);
$vars['ObjectType'] = $info['ObjectType'];
foreach ($vars['fields'] as $k=>$f){
    if ($f['type']=='fkey'&&$f['card']=='short'){
        $vars['fields'][$k]['link'] = Sys::getMenu($f['objectModule'].'/'.$f['objectName']);
    }
}

$vars['filters'] = $o->getCustomFilters();

if (!$vars['ObjectClass']->AccessPoint) $vars['Type'] = "Tail";

$vars["Interfaces"] = $vars["ObjectClass"]->getInterfaces();
if (isset($vars["Interfaces"]['list']))
    $vars["Interfaces"] = $vars["Interfaces"]['list'];

?>