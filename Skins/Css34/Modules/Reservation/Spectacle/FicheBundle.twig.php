<?php
$vars['s'] = Sys::getOneData('Reservation',$vars['Query']);
$vars['genre'] = Sys::getOneData('Reservation','Genre/Nom='.$vars['s']->Genre);
$vars['structure'] = $vars['s']->getOneParent('Organisation');
$vars['evenements'] = $vars['s']->getChildren('Evenement');
foreach ($vars['evenements'] as $k=>$e){
    $s = $e->getOneParent('Salle');
    $e->Salle = $s;
    $vars['evenements'][$k] = $e;
}