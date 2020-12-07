<?php

switch(Sys::$CurrentMenu->Url) {
	
//	case 'adh_site_enseignant':
//		$vars['fiche'] = 'Cadref/Print/PrintEnseignant/?adherent='.$this->Id;
//		break;
	
	default:
		$menus = array(
			'adh_cours'=>'AdhInscription',
			'adh_informations'=>'AdhInfo',
			'adh_finance'=>'AdhFinance',
			'adh_inscriptions'=>'AdhPanier',
			'adh_cours'=>'AdhInscription',
			'adh_visites'=>'AdhVisite',
			//'adh_visites'=>'AdhPanierVisite',
			'adh_documents/adh_carte'=>'AdhCarte',
			'adh_documents/adh_attestation'=>'AdhAttestation',
			'adh_documents/adh_associatif'=>'AdhDocument',
			'adh_message'=>'sendMessage',
			'adh_panier'=>'AdhPanier',
			'adh_site/adh_site_adherent'=>'AdhSiteAdherent',
			'adh_delegue/adh_delegue_adherent'=>'AdhDelegueAdherent',
			'ens_informations'=>'EnsInfo',
			'ens_absences'=>'EnsAbsence',
			'ens_cours'=>'EnsCours',
			'ens_visites'=>'EnsVisite',
			'ens_message'=>'sendMessage',
			'ens_documents/ens_adherents'=>'EnsAdherent',
			'ens_documents/ens_presence'=>'EnsPresence',
			'ben_adherents'=>'BenAdherent',
			'ben_visites'=>'BenVisite',
			'ben_reservations'=>'BenReservation'
			);
		$vars['fiche'] = 'Cadref/AccesPublic/'.$menus[Sys::$CurrentMenu->Url];
}
