<?php

require_once( dirname(dirname(__FILE__)).'/TypeLivraison.interface.php' );

class LivraisonStockTypeLivraisonStandard extends Plugin implements LivraisonStockTypeLivraisonPlugin {

	public function setTypeLivraison( $typeLivraison ) {
		$this->TypeLivraison = $typeLivraison;
	}

	public function getTarif( $commande, $adresseLivraison ) {
		// Vérifie si la zone est couverte
		$Zone = $this->TypeLivraison->GetZone($adresseLivraison->Pays,$adresseLivraison->CodePostal);
		if(!$Zone) return -1;

		// Critères du tarif à respecter
		$testTarif = 0;
		if ($this->TypeLivraison->SelectionPoids) $testTarif++;
		if ($this->TypeLivraison->SelectionQuantite) $testTarif++;
		if ($this->TypeLivraison->SelectionVolume) $testTarif++;
		if ($this->TypeLivraison->SelectionMontant) $testTarif++;

		// On cherche les tarifs correspondants
		$Tarifs = Sys::getData('LivraisonStock','ZoneLivraison/'.$Zone->Id . '/TarifLivraison/Actif=1',0,100,'ASC','Ordre,MaxiPoids,MaxiQuantite,MaxiVolume,MaxiMontant');

		if (is_array($Tarifs)) {
			foreach ($Tarifs as $TL) {
				$OkTarif=0;
				if($this->TypeLivraison->SelectionPoids) {
					// Vérification du poids maximum
					if ($commande->Poids <= $TL->MaxiPoids || $TL->MaxiPoids==-1)  $OkTarif++;
				}
				if($this->TypeLivraison->SelectionQuantite) {
					// Vérification de la quantité maximum
					if ( $commande->Qte <=$TL->MaxiQuantite || $TL->MaxiQuantite==-1) $OkTarif++;
				}
				if($this->TypeLivraison->SelectionVolume) {
					// Vérification du volume maximum
					if ( $commande->Volume <= $TL->MaxiVolume || $TL->MaxiVolume==-1) $OkTarif++;
				}
				if($this->TypeLivraison->SelectionMontant) {
					// Vérification du montant maximum
					if ($commande->MontantTTC <= $TL->MaxiMontant || $TL->MaxiMontant==-1) $OkTarif++;
				}
				// Tarif valide -> on garde que celui là
				if($OkTarif >= $testTarif) return $TL;
			}
		}

		// Pas de tarif
		return -1;
	}

	public function getChoix( $commande, $adresseLivraison ) {
		return array();
	}

	public function getChoixIntitule( $commande, $adresseLivraison, $Uid ) {
		return "";
	}

	public function isAdresseLivraisonAlternative() {
		return false;
	}

	public function updateInfosBL( $bonLivraison ) {
		return false;
	}
}













