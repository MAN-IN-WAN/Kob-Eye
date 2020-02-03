<?php
/** DEFAULT CONTROLLER **/
/*$mods = array();
foreach (Sys::$Modules as $k=>$mod){
    $mods[$k] = array_keys($obj->getObjectClass());
}*/
$vars['modules'] = Sys::$Modules;
if(count(Sys::$User->Menus))
    $vars['menus'] = Sys::$User->Menus[0]->getMainMenus();
//print_r(Sys::$User->Menus);
$vars['user'] = Sys::$User;
$vars['group'] = Sys::$User->Groups[0];

$vars['menuIcon'] = Sys::isModule('Cadref');
?>