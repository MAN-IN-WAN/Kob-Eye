<?php
class BoutiqueCategorie extends genericClass {
	/**
	 * getUrl
	 * Retourne l'url depuis la racine d'un produit donné
	 */
	/*public function getUrl() {
		//recherche des categorie
		if (!Sys::$User -> Admin) {
			$cat = $this -> storproc('Boutique/Categorie/*/Categorie/Categorie/' . $this -> Id);
			//on verifie qu'il n'y pas de menu sur chacune des categories
			$M = false;
			$U = $this->Url;
			//recherche des menus potentiels par catégories parentes
			foreach ($cat as $c) {
				if ('Boutique/Categorie/' . $c["Id"] != $GLOBALS["Systeme"] -> getMenu('Boutique/Categorie/' . $c["Id"])) {
					return $GLOBALS["Systeme"] -> getMenu('Boutique/Categorie/' . $c["Id"]) . (empty($U) ? '' : '/') . $U;
				} else
					$U = $c["Url"] . (empty($U) ? '' : '/') . $U;
			}
			$U = 'Categorie/' . $U;
			//recherche du magasin
			$mag = Magasin::getCurrentMagasin();
			if ('Boutique/Magasin/' . $mag->Id != $GLOBALS["Systeme"] -> getMenu('Boutique/Magasin/' . $mag->Id))
				return $GLOBALS["Systeme"] -> getMenu('Boutique/Magasin/' . $mag->Id) . '/' . $U;
			else
				return 'Boutique/' . $U;
		} else
			return parent::getUrl();
	}
*/


	public function MyRecursiveUrl ($LeParent) {
		$catParent = $this->getParents(Categorie);
		if ($catParent[0]->Id!=0) {
			//klog::l('Url',$this->Url."/".$catParent[0]->RecursiveUrl());
			if ($catParent[0]->Id==$LeParent) {
				return $this->Url;
			} else {
				return $catParent[0]->MyRecursiveUrl($LeParent)  . "/" .$this->Url;
			}

		} 
		return "," ;
	}


	/**
	 * Raccourci vers callData
	 * @return      Résultat de la requete
	 */
	private function storproc($Query, $recurs = '', $Ofst = '', $Limit = '', $OrderType = '', $OrderVar = '', $Selection = '', $GroupBy = '') {
		return Sys::$Modules['Boutique'] -> callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}

}
