<?php

if (isset($vars['Path']))
    $Path = $vars['Path'];
else
    $vars['Path'] = $Path = $vars['Query'];
$info = Info::getInfos($Path);

$vars['identifier'] = 'SystemePage';
$vars['context']= 'SystemePage'.$info['Module'].$info['ObjectType'];

$o = genericClass::createInstance('Systeme','Page');
$vars['ObjectClass'] = $o->getObjectClass();
$vars['ObjectType'] = 'Page';
$vars['Module'] = 'Systeme';
$vars['functions'] = $o->getFunctions();
$vars['operation'] = $vars['ObjectClass']->getOperations();
$vars['fields'] = $o->getElementsByAttribute('list','',true);
$vars['searchfields'] = $o->getElementsByAttribute('searchOrder|search','',true);
foreach ($vars['fields'] as $k=>$f){
    if ($f['type']=='fkey'&&$f['card']=='short'){
        $vars['fields'][$k]['link'] = Sys::getMenu($f['objectModule'].'/'.$f['objectName']);
    }
}

$vars['filters'] = $o->getCustomFilters();
if (is_object(Sys::$CurrentMenu)) {
    if (isset($vars['Type'])&&$vars['Type']=='Children') {
        $vars['CurrentUrl'] = Sys::getMenu('Systeme/Page');
    }else {
        $vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
    }
}else $vars['CurrentUrl'] = $Path;
if (!$vars['ObjectClass']->AccessPoint) $vars['Type'] = "Tail";

$vars["Interfaces"] = $vars["ObjectClass"]->getInterfaces();
if (isset($vars["Interfaces"]['list']))
    $vars["Interfaces"] = $vars["Interfaces"]['list'];

//Au cas ou la page pointerai plutot sur un menu
$mainmenus = Sys::getData('Systeme','Menu');
$menus = Sys::searchInMenus('Alias',$vars['CurrentObj'],Array(),$mainmenus);
$vars['ObjMenus'] = $menus;