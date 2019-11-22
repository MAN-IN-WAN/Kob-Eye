<?php
$info = Info::getInfos($vars['Query']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['CurrentObj'] =  $o;
$vars['filters'] = $o->getCustomFilters();
foreach ($vars['filters'] as $k=>$f){
    if(!empty($f->hasRole)){
        if(!Sys::$User->isRole($f->hasRole)){
            unset($vars['filters'][$k]);
        }
    }
}
$vars['recursiv'] = $o->isRecursiv();
foreach ($vars['filters'] as $k=>$f){
    if (empty($f->icon))$vars['filters'][$k]->icon = 'stats-growth';
    $vars['filters'][$k]->count = Sys::getCount($info['Module'],$info['ObjectType'].'/'.$f->filter);
}
$vars['CurrentMenu'] = Sys::$CurrentMenu;
if(Sys::$User->Admin && !$vars['CurrentMenu']){
    $oc = $o->getObjectClass();
    $vars['CurrentMenu'] = ['Titre' =>$oc->Description ];
}
$vars['identifier'] = $info['Module'] . $info['ObjectType'];

// PGF 20180912
if (isset($vars['Path']))
    $Path = $vars['Path'];
else
    $vars['Path'] = $Path = $vars['Query'];

$file = $o->isRecursiv() ? 'Tree' : 'List';
$vars['listPath'] = 'Systeme/Utils/'.$file;


?>