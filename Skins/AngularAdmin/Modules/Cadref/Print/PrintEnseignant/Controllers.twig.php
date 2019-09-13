<?php
$vars['module'] = 'Cadref';
$vars['objecttype'] = 'Enseignant';
$vars['controller'] = $vars['Url'];
$vars['function'] = 'PrintEtiquettes';

$vars['identifier'] = $vars['module'].$vars['objecttype'];
$vars['url'] = $vars['module'].'/'.$vars['objecttype'];

$annee = Cadref::$Annee;
$vars['antenne'] = 0;
$groups = Sys::$User->getParents('Group');
foreach($groups as $group) {
	if($group->Nom == 'CADREF_SITE') {
		$n = Sys::$User->Login;
		$a = Sys::getOneData('Cadref', "AdherentAnnee/Numero=$n&Annee=$annee");
		$vars['antenne'] = $a->AntenneId;
		break;
	}
}

