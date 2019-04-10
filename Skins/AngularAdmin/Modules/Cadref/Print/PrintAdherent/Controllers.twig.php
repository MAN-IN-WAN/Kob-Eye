<?php
$vars['module'] = 'Cadref';
$vars['objecttype'] = 'Adherent';
$vars['controller'] = $vars['Url'];
$vars['function'] = 'PrintAdherent';

$vars['identifier'] = $vars['module'].$vars['objecttype'];
$vars['url'] = $vars['module'].'/'.$vars['objecttype'];

$vars['obj'] = array();
if(isset($_COOKIE['PHPSESSID'])) {
	$vars['obj'] = $_SESSION['PrintAdherent'];
}

?>