<?php
if (isset($vars['Path']))
    $Path = $vars['Path'];
else
    $vars['Path'] = $Path = $vars['Query'];
$info = Info::getInfos($Path);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['identifier'] = $info['Module'].$info['ObjectType'];

if(!isset($vars['context']))
    $vars['context'] = $info['NbHisto'] > 1 ? 'children':'default';

$vars['ObjectClass'] = $o->getObjectClass();
$vars['ObjectType'] = $info['ObjectType'];
$vars['Module'] = $info['Module'];
$vars['functions'] = $o->getFunctions();
$vars['operation'] = $vars['ObjectClass']->getOperations();
foreach($vars['operation'] as $k=>$op){
    if(is_array($op)){
        $ok = false;
        foreach ($op as $r){
            if(Sys::$User->isRole($r)){
                $ok = true;
                break;
            }
        }
        $vars['operation'][$k] = $ok;
    }
}

$vars['fields'] = $o->getElementsByAttribute('list','',true);
$vars['searchfields'] = $o->getElementsByAttribute('searchOrder|search','',true);
$vars['ObjectType'] = $info['ObjectType'];
foreach ($vars['fields'] as $k=>$f){
    if ($f['type']=='fkey'&&$f['card']=='short'){
        $vars['fields'][$k]['link'] = Sys::getMenu($f['objectModule'].'/'.$f['objectName']);
    }
}

$vars['filters'] = $o->getCustomFilters();
if (is_object(Sys::$CurrentMenu)) {
    if ($vars['Type']=='Children') {
        $vars['CurrentUrl'] = Sys::getMenu($info['Module'] . '/' . $info['ObjectType']);
    }else {
        $vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
    }
}else $vars['CurrentUrl'] = $Path;
if (!$vars['ObjectClass']->AccessPoint) $vars['Type'] = "Tail";

$vars["Interfaces"] = $vars["ObjectClass"]->getInterfaces();
$vars["Interfaces"] = $vars["Interfaces"]['list'];


