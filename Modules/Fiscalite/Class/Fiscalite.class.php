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
				$Ttx2->Actif=1;
				$Ttx2->Save();
				$Ttx3=genericClass::createInstance('Fiscalite','TypeTva');
				$Ttx3->Defaut=0;
				$Ttx3->Nom='10';
				$Ttx3->Actif=1;
				$Ttx3->Save();
				$Ttx4=genericClass::createInstance('Fiscalite','TypeTva');
				$Ttx4->Defaut=0;
				$Ttx4->Nom='2.10';
				$Ttx4->Actif=1;
				$Ttx4->Save();
			}else {
				$Ttx= Sys::getData('Fiscalite','TypeTva/1');
				$Ttx2= Sys::getData('Fiscalite','TypeTva/2');
				$Ttx3= Sys::getData('Fiscalite','TypeTva/3');
				$Ttx4= Sys::getData('Fiscalite','TypeTva/4');
			}
			// cree les taux de tva par défaut pour cette zone
			if (!Sys::getCount('Fiscalite','TauxTva/Actif=1&Debut<'. time().'&Fin>='.time())) {
				$Tx=genericClass::createInstance('Fiscalite','TauxTva');
				$Tx->Actif=1;
				$Tx->Taux=20;
				$Tx->Debut=time();
				$Tx->Fin=time()+315360000;
				$Tx->AddParent($Z);
				$Tx->AddParent($Ttx);
				$Tx->Save();

				$Tx2=genericClass::createInstance('Fiscalite','TauxTva');
				$Tx2->Actif=1;
				$Tx2->Taux=5.5;
				$Tx2->Debut=time();
				$Tx2->Fin=time()+315360000;
				$Tx2->AddParent($Z);
				$Tx2->AddParent($Ttx2);
				$Tx2->Save();

				$Tx3=genericClass::createInstance('Fiscalite','TauxTva');
				$Tx3->Actif=1;
				$Tx3->Taux=10;
				$Tx3->Debut=time();
				$Tx3->Fin=time()+315360000;
				$Tx3->AddParent($Z);
				$Tx3->AddParent($Ttx3);
				$Tx3->Save();

				$Tx4=genericClass::createInstance('Fiscalite','TauxTva');
				$Tx4->Actif=1;
				$Tx4->Taux=2.10;
				$Tx4->Debut=time();
				$Tx4->Fin=time()+315360000;
				$Tx4->AddParent($Z);
				$Tx4->AddParent($Ttx4);
				$Tx4->Save();
			}
		}
		return true;
	}


}