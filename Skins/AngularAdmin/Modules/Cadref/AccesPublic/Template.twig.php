<?php
$menus = array(
	'adh_informations'=>'AdhInfo',
	'adh_finance'=>'AdhFinance',
	'adh_inscriptions'=>'AdhPanier',  //'AdhInscription',
	'adh_visites'=>'AdhVisite',
	'adh_visites'=>'AdhVisite',
	'adh_documents/adh_carte'=>'AdhCarte',
	'adh_documents/adh_attestation'=>'AdhAttestation',
	'adh_message'=>'sendMessage',
	'adh_panier'=>'AdhPanier',
	'ens_informations'=>'EnsInfo',
	'ens_absences'=>'EnsAbsence',
	'ens_cours'=>'EnsCours',
	'ens_visites'=>'EnsVisite',
	'ens_message'=>'sendMessage'	
	);
$vars['fiche'] = $menus[Sys::$CurrentMenu->Url];
