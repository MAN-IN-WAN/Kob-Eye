<?php

$req = explode('/', $vars['Query'],2);
$tc = Sys::getOneData('Reservations',$req[1]);
$vars['bureaux'] = $tc->getChildren('Court');
$vars['nbBureaux'] = count($vars['bureaux']);
$vars['nbCol'] = $vars['nbBureaux'] > 4 ? 3 : (12/$vars['nbBureaux']);
$service = $tc->getOneChild('Service');
$horaires = $service->getHoraires();
$vars['horaires'] = array();
foreach($horaires as $ho){
    $vars['horaires'][$ho] = explode(':',$ho);
}
//print_r($vars['Lien']);


