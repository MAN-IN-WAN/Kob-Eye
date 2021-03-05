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
    if ($m->Url == "urgence") {
       continue;
    }
    else {
        $menu2 []=$m;
    }
}

$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$params = $Minisite->getParamsValues();
foreach($params as $param){
    $vars[$param->Nom] = $param->vms;
}

//print_r($vars);
//
$vars['Menu'] = $menu2;
