<?php

class BonLivraison extends genericClass {
    var $MontantLivraisonHT = 0;

	public function Save() {
		parent::Save();
		if ($this->NumBL=='') $this->NumBL = sprintf("BL".Date('Y').Date('m')."%05d",$this->Id);
		if(isset($this->LigneBl)&&is_array($this->LigneBl))foreach($this->LigneBl as $obj) :
			$obj->AddParent( $this );
			$obj->Save();
		endforeach;
		parent::Save();
	}

	/**
	 * Initialisation du Bon de livraison
	 * @param  	object  Commande KE
	 * @param  	object  Adresse KE (Livraison)
	 * @param  	object  TypeLivraison KE
	 * @param	object	TarifLivraison KE
	 * @param	object	ZoneLivraison KE
	 * @param	string	Choix complémentaire pour la livraison (unique id)
	 * @return	void
	 */
	public function InitBL($Commande, $AdresseLivraison, $TypeLivraison, $TarifLivraison, $ZoneLivraison, $ChoixLivraison,$TxTvar=19.6) {
        $this->MontantLivraisonHT = $TarifLivraison->Tarif;
        $this->MontantLivraisonTTC = $TypeLivraison->getTTC($TarifLivraison->Tarif);
		$this->TypeLivraison = $TypeLivraison->Nom;
		$this->ZoneLivraison = $ZoneLivraison->Code;
		$this->ChoixLivraisonId = $ChoixLivraison;
		$this->ChoixLivraison = $TypeLivraison->getPlugin()->getChoixIntitule($Commande, $AdresseLivraison, $ChoixLivraison);
		$this->TrancheLivraison = $TarifLivraison->Description;
		$this->DateLivPrev = $this->getDateLivPrev( $ZoneLivraison->LivreEn );
		$this->AdresseLivraisonAlternative = $TypeLivraison->getPlugin()->isAdresseLivraisonAlternative();
		$this->TxTvaBonLivr = $TxTvar;
		$this->AddParent($TypeLivraison);
		$lignes = $Commande->getLignesCommande();
		$this->LigneBl = array();
		foreach($lignes as $l) :
			$obj = genericClass::createInstance('LivraisonStock', 'LigneBonLivraison');
			$Ref = $l->getReference();
			$Prod = $Ref->getProd();
			$obj->QuantiteCde = $l->Quantite;
			$obj->QuantiteLivre = $l->Quantite;
			$obj->Produit = $Prod->Reference;
			$obj->ProduitId = $Prod->Id;
			$obj->Reference = $Ref->Reference;
			$obj->ReferenceId = $Ref->Id;
 			$obj->Poids = $Ref->Poids;
			if (empty($Ref->Poids))  $obj->Poids = $Prod->Poids;
			$obj->Largeur = $Ref->Largeur;
			if (empty($Ref->Largeur))  $obj->Largeur = $Prod->Largeur;
			$obj->Hauteur = $Ref->Hauteur;
			if (empty($Ref->Hauteur))  $obj->Hauteur = $Prod->Hauteur;
			$obj->Profondeur = $Ref->Profondeur;
			if (empty($Ref->Profondeur))  $obj->Profondeur = $Prod->Profondeur;
			$this->LigneBl[]=$obj;
		endforeach;
	}
	/**
	 * Recherche les infos de la livraison pour impression bon de commande et facture
	 * @param	none
	 * @return	array
	 */
	public function getInfoCdeFac() {
		$InfoCdeFac=Array ();
		$InfoCdeFac['Nom']= $this->TypeLivraison ;
		$InfoCdeFac['Tva']= $this->TxTvaBonLivr;
		$totHt= $this->getHTLiv($this->MontantLivraisonTTC, $this->TxTvaBonLivr);
		$InfoCdeFac['MontantHT']= $totHt;
		$InfoCdeFac['MontantTva']= $this->getMontantTvaLiv($totHt,$this->TxTvaBonLivr);
		return $InfoCdeFac;
	}
	/**
	 * Met à jour des informations complémentaires dans le BL une fois le paiement confirmé
	 * @return void
	 */
	public function updateInfosLivraison() {
		$typeLivraison = $this->getTypeLivraison();
		$plugin = $typeLivraison->getPlugin();
		$plugin->updateInfosBL( $this );
	}

	public function getTypeLivraison() {
		if(is_object($this->TypeLivraison)) return $this->TypeLivraison;
		$tl = $this->storproc('LivraisonStock/TypeLivraison/BonLivraison/'.$this->Id);
		if(is_array($tl) && sizeof($tl) > 0) return genericClass::createInstance('LivraisonStock',$tl[0]);
	}

	public function getCommande() {
		$Cde = $this->storproc('LivraisonStock/Commande/BonLivraison/'.$this->Id);
		if(is_array($Cde) && sizeof($Cde) > 0) return genericClass::createInstance('Boutique',$Cde[0]);
	}


	/**
	 * Recherche les infos de la livraison pour impression bon de commande et facture
	 * @param	float montant ht et taux de tva
	 * @return	array
	 */

	public function getMontantTvaLiv($MontantHt, $Taux) {
		$TotalTva= $MontantHt *(1 + $Taux / 100);
		return $TotalTva;
	}
	public function getHTLiv($MontantTTC, $Taux) {
		$MontantHt= $MontantTTC /(1 + $Taux / 100);
		return $MontantHt;
	}


	/**
	 * Estime la date de livraison par rapport à aujourd'hui
	 * @param	int		Nb de jours pour la livraison
	 * @return	timestamp
	 */
	private function getDateLivPrev( $offset ) {
		$tms = time();
		if(date('H') >= 12) $offset;
		for($i=0; $i<$offset; $i++) {
			$tms += 86400;
			if(date('N', $tms) >= 6) $offset++; // Jour non ouvré on décale d'un jour
		}
		return $tms;
	}

	
	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return Sys::$Modules['LivraisonStock']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}


}