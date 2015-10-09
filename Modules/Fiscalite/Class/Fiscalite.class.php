<?php
class Fiscalite extends Module {
	/**
	 * Surcharge de la fonction init
	 * Avant l'authentification de l'utilisateur
	 * @void 
	 */

	function Check() {

		parent::Check();
		// Vérification de l'existence d'une devise par Defaut
		if (!Sys::getCount('Fiscalite','ZoneFiscale/Default=1')) {
			$Z=genericClass::createInstance('Fiscalite','ZoneFiscale');
			$Z->Code='ZoneDefault';
			$Z->Default= '1';
			$Z->Save();

			// est ce que les taux existent
			if (!Sys::getCount('Fiscalite','TypeTva')) {
				$Ttx=genericClass::createInstance('Fiscalite','TypeTva');
				$Ttx->Defaut=1;
				$Ttx->Nom='20';
				$Ttx->Actif=1;
				$Ttx->Save();
				$Ttx2=genericClass::createInstance('Fiscalite','TypeTva');
				$Ttx2->Defaut=0;
				$Ttx2->Nom='5.5';
				$Ttx2->Save();
			}
			$ZTz= Sys::getData('Fiscalite','ZoneFiscale/Default=1&');
			$Ttx= Sys::getData('Fiscalite','TypeTva/Defaut=1&Actif=1');
			$Ttx2= Sys::getData('Fiscalite','TypeTva/Defaut=0&Actif=1');
			// cree les taux de tva par défaut pour cette zone
			if (!Sys::getCount('Fiscalite','TauxTva/Actif=1&Debut<'. time().'&Fin>='.time())) {
				$Tx=genericClass::createInstance('Fiscalite','TauxTva');
				$Tx->Actif=1;
				$Tx->Taux=20;
				$Tx->Debut=time();
				$Tx->Fin=time()+31536000;
				$Tx->AddParent("Fiscalite/ZoneFiscale/" . $ZTz[0]->Id);	
				$Tx->AddParent("Fiscalite/TypeTva/" . $Ttx[0]->Id);	
				$Tx->Save();
				$Tx2=genericClass::createInstance('Fiscalite','TauxTva');
				$Tx2->Actif=1;
				$Tx2->Taux=5.5;
				$Tx2->Debut=time();
				$Tx2->Fin=time()+31536000;
				$Tx2->AddParent("Fiscalite/ZoneFiscale/" .  $ZTz[0]->Id);	
				$Tx2->AddParent("Fiscalite/TypeTva/" . $Ttx2[0]->Id);	
				$Tx2->Save();
				
			} else {
				$tauxtva= Sys::getData('Fiscalite','TauxTva/Actif=1&'. time().'&Fin>='.time() );
				$tauxtva->AddParent("Fiscalite/ZoneFiscale/" .  $ZTz[0]->Id);	
				$tauxtva->Save();
			}
		}
		return true;
	}


}