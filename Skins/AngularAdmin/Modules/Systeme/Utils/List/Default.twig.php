<?php
$info = Info::getInfos($vars['Query']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['identifier'] = $info['Module'].$info['ObjectType'];
$vars['context'] = $info['NbHisto'] > 1 ? 'children':'default';
$vars['ObjectClass'] = $o->getObjectClass();
$vars['functions'] = $o->getFunctions();
$vars['operation'] = $vars['ObjectClass']->getOperations();
$vars['fields'] = $o->getElementsByAttribute('list','',true);
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
}else $vars['CurrentUrl'] = $vars['Query'];
if (!$vars['ObjectClass']->AccessPoint) $vars['Type'] = "Tail";
?>