<?php
$info = Info::getInfos($vars['Query']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['fields'] = $o->getElementsByAttribute('list','',true);
$vars['fichefields'] = $o->getElementsByAttribute('fiche','',true);
foreach ($vars['fichefields'] as $k=>$f){
    if ($f['type']=='fkey'&&$f['card']=='short'){
        $vars['fichefields'][$k]['link'] = Sys::getMenu($f['objectModule'].'/'.$f['objectName']);
    }
}
$vars['formfields'] = $o->getElementsByAttribute('form','',true);
$vars['CurrentMenu'] = Sys::$CurrentMenu;
$vars["CurrentObj"] = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();
$vars["ChildrenElements"] = $vars["ObjectClass"]->getChildElements();
$vars['identifier'] = $info['Module'] . $info['ObjectType'];
if (is_object(Sys::$CurrentMenu))
    $vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
else $vars['CurrentUrl'] = $vars['Query'];
?>