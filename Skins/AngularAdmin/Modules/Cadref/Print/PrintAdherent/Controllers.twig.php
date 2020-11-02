<?php
$vars['Annee'] = Cadref::$Annee;
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

$vars['emetteur'] = '';
$as = Sys::getData('Cadref', 'Parametre/Domaine=MAIL&SousDomaine=EMETTEUR');
foreach($as as $a) {
	if($a->Parametre == '1') $vars['emetteur'] = $a->Valeur;
}


?>