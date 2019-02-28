<?php
$vars['login'] = Sys::$User->Login;
$group = Sys::$User->getParents('Group')[0]->Nom;
if($group == 'CADREF_ADH') {
	$n = Sys::$User->Login;
	$a = Sys::getOneData('Cadref', 'Adherent/Numero='.$n);
}
else if($group == 'CADREF_ENS') {
	$n = substr(Sys::$User->Login, 3, 3);
	$a = Sys::getOneData('Cadref', 'Enseignant/Code='.$n);
}
$vars['LastConnection'] = date('d/m/Y H:m:s', Sys::$User->LastConnection);

