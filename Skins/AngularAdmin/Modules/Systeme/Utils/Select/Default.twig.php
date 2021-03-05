<?php
$info = Info::getInfos($vars['Path']);

if(empty($vars['Object'])){
    $q = $vars['Query'];
    if (!empty($vars['QueryOverride'])){
        $q = $vars['QueryOverride'];
    }
    $infoParent = Info::getInfos($q);
    $parent = genericClass::createInstance($info['Module'],$infoParent['ObjectType']);
} else{
    $module = !empty($vars['Module']) ? $vars['Module'] : $info['Module'];
    $parent = genericClass::createInstance($module,$vars['Object']);

}

$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$f = $parent->getElement($vars['FieldKey']);

$vars['filter'] = $f[0]['filter'];
$vars['identifier'] = $info['Module'].$info['ObjectType'];
$vars['ObjectClass'] = $o->getObjectClass();
$vars['operation'] = $vars['ObjectClass']->getOperations();
$vars['fields'] = $o->getElementsByAttribute('list','',true);
$vars['filters'] = $o->getCustomFilters();

if(!isset($vars['ParentObject'])){
    $vars['context'] = 'select' ;
} else{
    $menu = Sys::$CurrentMenu;
    $menuInfo = Info::getInfos($menu->Alias);

    if( $menuInfo['ObjectType'] != $vars['ParentObject'] ){
        //On est sur le form d'un enfant
        $vars['context'] = 'cparentselect';
        $vars['identifier'] .= 'CP';
    } else{
        $vars['context'] = 'parentselect';
    }

}

if (is_object(Sys::$CurrentMenu)) {
    if ($vars['Type']=='Children') {
        $vars['CurrentUrl'] = Sys::getMenu($info['Module'] . '/' . $info['ObjectType']);
    }else $vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
}else $vars['CurrentUrl'] = $vars['Query'];
if (!$vars['ObjectClass']->AccessPoint) $vars['Type'] = "Tail";


?>