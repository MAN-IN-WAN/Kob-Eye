<?php
$vars['module'] = 'Cadref';
$vars['objecttype'] = 'Adherent';
$vars['controller'] = $vars['Url'];
$vars['function'] = 'PrintCertificat';

$vars['identifier'] = $vars['module'].$vars['objecttype'];
$vars['url'] = $vars['module'].'/'.$vars['objecttype'];
$vars['Annee'] = Cadref::$Annee;
?>