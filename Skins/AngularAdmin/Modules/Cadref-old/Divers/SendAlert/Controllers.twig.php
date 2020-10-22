<?php
$vars['Annee'] = Cadref::$Annee;
$vars['module'] = 'Systeme';
$vars['objecttype'] = 'AlertUser';
$vars['controller'] = $vars['Url'];
$vars['function'] = 'SendAlert';

$vars['identifier'] = $vars['module'].$vars['objecttype'];
$vars['url'] = $vars['module'].'/'.$vars['objecttype'];
?>