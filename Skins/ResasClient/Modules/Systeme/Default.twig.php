<?php


$vars['url'] = null;
$vars['missingType'] = false;


$nbType = Sys::getCount('Reservations', 'TypeCourt');
if($nbType > 1) {
    $vars['url'] = Sys::getMenu('Reservations/TypeCourt');
} elseif ($nbType == 1){
    $type = Sys::getOneData('Reservations', 'TypeCourt');
    $vars['url'] = $type->getUrl();
} else {
    $vars['missingType'] = true;
}