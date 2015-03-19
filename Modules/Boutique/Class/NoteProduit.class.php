<?php

class NoteProduit extends genericClass {

	/**
	 * Enregistrement d'une évaluation PRODUTT
	 * -> Recalcule la moyenne pour le produtt
	 * @return	void
	 */
	public function Save() {
		parent::Save();
		// Calcul moyenne
		$total = 0;
		$prod = Sys::$Modules['Boutique']->callData('Produit/NoteProduit/' . $this->Id);
		$evals = Sys::$Modules['Boutique']->callData('Produit/'.$prod[0]['Id'].'/NoteProduit');
		foreach($evals as $e) $total += $e["Note"];
		// Enregistrement pour le produit
		$produit = parent::createInstance('Boutique', $prod[0]);
		$produit->Note = round($total/sizeof($evals), 1);
		$produit->Save();
	}

}