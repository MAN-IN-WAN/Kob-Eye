<?php

function humanReadable($Mo){
    $units = array('Mo','Go','To','Po');

    $pow = 0;
    while($Mo > 1024){
        $Mo /= 1024;
        $pow++;
    }
    $Mo = number_format($Mo,2);
    return $Mo . $units[$pow];
}




$vars['Urls'] = array(
    'organisations'=>Sys::getMenu('Reservation/Organisation'),
    'clients'=>Sys::getMenu('Reservation/Client'),
    'spectacles'=>Sys::getMenu('Reservation/Spectacle'),
    'reservations'=>Sys::getMenu('Reservation/Reservations'),
    'evenements'=>Sys::getMenu('Reservation/Evenement')
);
