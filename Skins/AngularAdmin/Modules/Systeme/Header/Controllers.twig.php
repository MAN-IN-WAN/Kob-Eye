<?php

$menus = Sys::$User->Menus;
$modules = Sys::$Modules;

$vars["controllers"] = array();
foreach ($menus as $m) {
    $vars["controllers"][] = $m->Url;
    if (isset($m->Menus))foreach ($m->Menus as $m2) {
        $vars["controllers"][] = $m->Url.'/'.$m2->Url;
    }
}
foreach ($modules as $mod) {
    $vars["controllers"][] = $mod->Nom;
    foreach ($mod->getAccessPoint() as $ap) {
        $vars["controllers"][] = $mod->Nom.'/'.$ap->titre;
    }
}

?>