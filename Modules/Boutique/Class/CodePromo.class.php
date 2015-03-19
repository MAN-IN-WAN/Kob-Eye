<?php

/**
 * Ajout d'un utilisateur automatique pour un Client
 *
 */
class CodePromo extends genericClass {

	/**
	 * Retourne les offres spéciales pour une commande
	 * @return	array
	 */
	public function getReducMontant($TotalCommande) {
	//	echo $TotalCommande;
		$Montant=0;
		// est ce que ce code promo est dépendant d'un minimum d'achat
		if ($this->MiniAchat>0) {
			if ($TotalCommande>=$this->MiniAchat) $reduc=true;
			else $this->Message = "Le montant minimum d'achat n'a pas été atteint (".$this->MiniAchat." € minimum)";
		} else {
			$reduc=true;
		}
		// Pourcentage de la commande
		if ($this->TypeVariation==1) { 
			$Montant= ($TotalCommande * $this->Variation )/100; 
		}
		// Montant fixe
		if ($this->TypeVariation==2) { 
			if ($TotalCommande>=$this->Variation) {
				$Montant= $this->Variation; 
			} else {
				$this->Message="La remise ne peut pas être supérieure au montant de la commande !";
				$Montant=0;
			}
		}
		return floatval($Montant);
	}
	
	
	
	
	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return Sys::$Modules['Boutique']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}





}
