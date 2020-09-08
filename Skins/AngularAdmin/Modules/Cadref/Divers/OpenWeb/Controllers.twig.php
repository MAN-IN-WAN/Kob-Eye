<?php
$vars['Annee'] = Cadref::$Annee;
$vars['module'] = 'Cadref';
$vars['objecttype'] = 'Inscription';
$vars['controller'] = $vars['Url'];
$vars['function'] = 'OpenWeb';

$vars['identifier'] = $vars['module'].$vars['objecttype'];
$vars['url'] = $vars['module'].'/'.$vars['objecttype'];

$mnu = Sys::getOneData('Systeme', 'Menu/Url=adh_inscriptions');
$vars['Open'] = $mnu->Affiche;
