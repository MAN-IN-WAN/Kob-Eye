<?php

class Genre extends genericClass {
	public function getNbProduitConsole($Console="") {
		if (empty($Console))return 0;
		$produits = $this->storproc('Boutique/Categorie/' . $Console .'/*/Produit/GenreId=' . $this->Id);
		return is_array($produits) ? sizeof($produits) : 0;
	}
	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return Sys::$Modules['Boutique']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}
}