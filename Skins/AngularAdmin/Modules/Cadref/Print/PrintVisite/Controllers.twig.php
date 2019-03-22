<?php
$vars['Annee'] = Cadref::$Annee;
$vars['module'] = 'Cadref';
$vars['objecttype'] = 'Visite';
$vars['controller'] = $vars['Url'];
$vars['function'] = 'PrintVisite';

$vars['identifier'] = $vars['module'].$vars['objecttype'];
$vars['url'] = $vars['module'].'/'.$vars['objecttype'];
?>