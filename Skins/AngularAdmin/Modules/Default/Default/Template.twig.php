<?php
$info = Info::getInfos($vars['Query']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['CurrentObj'] =  $o;
$vars['filters'] = $o->getCustomFilters();
$vars['recursiv'] = $o->isRecursiv();
foreach ($vars['filters'] as $k=>$f){
    if (empty($f->icon))$vars['filters'][$k]->icon = 'stats-growth';
    $vars['filters'][$k]->count = Sys::getCount($info['Module'],$info['ObjectType'].'/'.$f->filter);
}
$vars['CurrentMenu'] = Sys::$CurrentMenu;
$vars['identifier'] = $info['Module'] . $info['ObjectType'];

?>