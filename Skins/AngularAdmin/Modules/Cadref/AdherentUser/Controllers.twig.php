<?php
$vars['Annee'] = Cadref::$Annee;
$vars['identifier'] = 'AdherentUser';
$vars['module'] = 'Cadref';
$vars['objecttype'] = 'Adherent';
$n = Sys::$User->Login;
$vars['adher'] = Sys::getOneData('Cadref', 'Adherent/Numero=' . $n);
$vars['url'] = 'Cadref/Adherent';


?>