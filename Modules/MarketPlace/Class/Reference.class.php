<?php

class Reference extends genericClass {

	/**
	 * Enregistrement d'une référence
	 * -> Check référence
	 * @return	void
	 */
	public function Save() {
		if (!$this->Id)$this->StockOrigine = $this->Quantite;
		parent::Save();
		$this->SaveRef();
		//Appel de modification du produit pour mettre à jour les infos du produit
		$P = $GLOBALS["Systeme"]->Modules['Boutique']->callData('Boutique/Produit/Reference/'.$this->Id,false,0,1);
		if (!empty($P)&&is_array($P[0])){
			$P = genericClass::createInstance('Boutique',$P[0]);
			$this->Nom = $P->Nom;
			$this->TypeSupport = $P->TypeSupport;
			if (!$this->Image)$this->Image = $P->Image;
			elseif (!@file_exists($this->Image))$this->Image = $P->Image;
			$P->Save();
		}
		parent::Save();
	}

	/**
	 * Création d'une référence si c'est une nouvelle référence
	 * @return	void
	 */
	private function SaveRef() {
		//if(empty($this->Reference)) {
			$this->Reference = sprintf("REF%05d",$this->Id);

		//}
	}

	/**
	 * Indique si la référence est encore dispo ou non
	 * @return true ou false
	 */
	public function estDisponible($Q=1) {
		return ( ($this->Quantite>=$Q || $this->StockPermanent)&& $this->Actif  );
	}

	/**
	 * Renvoie le lien complet en passant par les categories et le produit
	 * @return varchar
	 */
	public function getLink($Q=1) {
		$U = "";
		//Recuperation du produit
		$P = $this->storproc('Boutique/Produit/Reference/'.$this->Id,false,0,1,'DESC','tmsCreate');
		if (is_array($P)&&is_array($P[0])){
			//Recuperation des categories
			$P = genericClass::createInstance('Boutique',$P[0]);
			$C = $this->storproc('Boutique/Categorie/*/Categorie/Produit/'.$P->Id);
			if (is_array($C))foreach ($C as $Ca):
				$U.=(($U!="")?'/':'').$Ca['Url'];
			endforeach;
			//Ajout du produit
			$U.='/Produit/'.$P->Url.'/Reference/'.$this->Reference;
			return $U;
		}
		return false;
	}

	/**
	 * Decrementation du stock
	 * @return true ou false
	 */
	public function decrementeStock($Q=1){
		if(!$this->StockPermanent) :
			$this->Quantite-=$Q;
			if(!$this->Quantite) $this->Actif = 0;
		endif;
		$this->QuantiteVendue+=$Q;
		$this->Save();
	}
	/**
	 * Incrementation du stock
	 * @return true ou false
	 */
	public function incrementeStock($Q=1){
		if(!$this->StockPermanent) :
			$this->Quantite+=$Q;
			$this->Actif = 1;
		endif;
		$this->QuantiteVendue-=$Q;
		$this->Save();
	}
	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return $GLOBALS['Systeme']->Modules['Boutique']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}




}