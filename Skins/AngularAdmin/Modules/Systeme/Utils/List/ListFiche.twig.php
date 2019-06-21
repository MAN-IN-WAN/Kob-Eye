<?php
if (isset($vars['Path']))
    $Path = $vars['Path'];
else
    $vars['Path'] = $Path = $vars['Query'];
$info = Info::getInfos($Path);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$o->setView();
$vars['identifier'] = $info['Module'].$info['ObjectType'];
if(!isset($vars['context']))
    $vars['context'] = $info['NbHisto'] > 1 ? 'children':'default';
$vars['ObjectClass'] = $o->getObjectClass();
$vars['ObjectType'] = $info['ObjectType'];
$vars['Module'] = $info['Module'];
$vars['fields'] = $o->getElementsByAttribute('list','',true);
$vars['ObjectType'] = $info['ObjectType'];
foreach ($vars['fields'] as $k=>$f){
    if ($f['type']=='fkey'&&$f['card']=='short'){
        $vars['fields'][$k]['link'] = Sys::getMenu($f['objectModule'].'/'.$f['objectName'],true);
        if ($vars['fields'][$k]['link']==$f['objectModule'].'/'.$f['objectName'])
            $vars['fields'][$k]['link'] = false;
    }
}
if (is_object(Sys::$CurrentMenu)) {
    if (isset($vars['Type'])&&$vars['Type']=='Children') {
        $vars['CurrentUrl'] = Sys::getMenu($info['Module'] . '/' . $info['ObjectType']);
    }else {
        $vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
    }
}else $vars['CurrentUrl'] = $Path;
if (!$vars['ObjectClass']->AccessPoint) $vars['Type'] = "Tail";

$vars["Interfaces"] = $vars["ObjectClass"]->getInterfaces();
if (isset($vars["Interfaces"]['list']))
    $vars["Interfaces"] = $vars["Interfaces"]['list'];


$vars['formPath'] = 'Systeme/Utils/Form';

if (!isset($info['ObjectType'])) {
    $tab = explode('/', $info['Query']);
    array_push($tab, 'Form');
} else {
    $tab = array($info['Module'], $info['ObjectType'], 'Form');
}
?>