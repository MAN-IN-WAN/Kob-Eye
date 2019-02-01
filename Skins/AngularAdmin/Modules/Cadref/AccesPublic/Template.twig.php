<?php
$menus = array('adh_informations'=>'AdhInfo', 'adh_finance'=>'AdhFinance','adh_inscriptions'=>'AdhInscription','adh_visites'=>'AdhVisite',
	'ens_informations'=>'EnsInfo','ens_absences'=>'EnsAbsence','ens_cours'=>'EnsCours','ens_visites'=>'EnsVisite');
$vars['fiche'] = $menus[Sys::$CurrentMenu->Url];
