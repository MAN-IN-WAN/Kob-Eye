<?php
$info = Info::getInfos($vars['Query']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['fields'] = $o->getElementsByAttribute('list','',true);
$vars['functions'] = $o->getFunctions();
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
$vars["ChildrenElements"] = $vars["ObjectClass"]->getChildElements();
$vars["Interfaces"] = $vars["ObjectClass"]->getInterfaces();
$vars['identifier'] = $info['Module'] . $info['ObjectType'];
if (is_object(Sys::$CurrentMenu))
    $vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
else $vars['CurrentUrl'] = $vars['Query'];


$vars['browseable'] = $vars["ObjectClass"]->browseable;

$vars['CurrentObjId']=$GLOBALS["Systeme"]->getGetVars("CurrentObjId");
$vars['CurrentObjQuery']=$info['Module'].'/'.$info['ObjectType'].'/'.$vars['CurrentObjId'];


?>