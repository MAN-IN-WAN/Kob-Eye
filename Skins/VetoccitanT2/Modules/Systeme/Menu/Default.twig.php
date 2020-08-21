<?php

$user = Sys::$User;
$menus = $user->getMenus();

foreach ($menus as &$m){
    if(strpos($vars['Lien'],$m->Url) === 0){
        $m->current = true;
    } else {
        $m->current = false;
    }
    if ( empty($m->Url) && empty($vars['Lien']) ){
        $m->current = true;
    }
}



$vars['Menu'] = $menus;
