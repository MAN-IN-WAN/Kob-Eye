<?php
$vars['Annee'] = Cadref::$Annee;
$vars['module'] = 'Cadref';
$vars['objecttype'] = 'Inscription';
$vars['controller'] = $vars['Url'];
$vars['function'] = 'OpenWeb';

$vars['identifier'] = $vars['module'].$vars['objecttype'];
$vars['url'] = $vars['module'].'/'.$vars['objecttype'];
