<?php
/** DEFAULT CONTROLLER **/
/*$mods = array();
foreach (Sys::$Modules as $k=>$mod){
    $mods[$k] = array_keys($obj->getObjectClass());
}*/
$vars['modules'] = Sys::$Modules;
$vars['menus'] = Sys::$User->Menus[0]->getMainMenus();

$vars['user'] = Sys::$User;

?>