<?php

class Reference extends genericClass {

	/**
	 * Enregistrement d'une référence
	 * -> Check référence
	 * @param	boolean		Mettre à jour les infos par rapport au produit
	 * @return	void
	 */
	public function Save( $SP = true ) {
		// Pas de quantité négative
		$this->Quantite = max(0,$this->Quantite);

		// Premier enregistrement, attribution du stock d'origine
		if(!isset($this->Id)||!$this->Id) $this->StockOrigine = $this->Quantite;

		// Désactivation de la référence si plus de stock
		if($this->Quantite <= 0 && !$this->StockPermanent) $this->Actif = 0;

		// Désactivation de la référence si plus de stock
		$this->checkReference();
		
		// Enregistrement
		parent::Save();

		// Appel de modification du produit pour mettre à jour les infos du produit
		if($SP) {
			$P = $this->getProd();
			if(is_object($P)) {
				if($this->Reference=='') $this->Reference = $P->Reference;
				//if($this->Tarif==0) $this->Tarif = $P->Tarif;
				switch($P->TypeProduit) {
					case 1 :
						// cas produit à références uniques
						$this->SaveRefUnique();
						if ($this->Nom=='') $this->Nom = $P->Nom;
						if (!$this->Image)$this->Image = $P->Image;
						elseif (!@file_exists($this->Image))$this->Image = $P->Image;
						// Repair stock anomalie
						/*if ($this->Quantite>0)$this->Quantite = 1; else $this->Quantite = 0;
						if ($this->QuantiteVendue>0)$this->QuantiteVendue = 1; else $this->QuantiteVendue = 0;
						$this->StockOrigine = 1;*/ 
					break;
					case 2 :
						// cas produit standard / decliné
						$this->SaveRefDecline();
// ENGUER À VOIR AVEC TOI CAR EN FAIT CHAQUE FOIS QUELLE MODIFIE ON AJOUTE DES DECLINAISONS DANS LE NOM !!!
//OU ALORS SI ELLE MODIFIE UNE DECLINAISON IL FAUT RE -ENREGISTRÉ LE NOM DE LA REFERENCE OU LUI ?????

						if ($this->Nom=='') $this->Nom = $P->Nom  ;
						$Ds = $this->getDeclinaisons();
						
						if (is_array($Ds))foreach ($Ds as $D):
							$pos = strpos( $this->Nom, $D->Nom);
							if ($pos === false)  $this->Nom .= ' '.$D->Nom ;
						endforeach;
						if (!$this->Image)$this->Image = $P->Image;
						elseif (!@file_exists($this->Image))$this->Image = $P->Image;
					break;
					case 3 :
						// cas produit unique
						$P->Actif = $this->Actif;
						// Repair stock anomalie
						/*if ($this->Quantite>0)$this->Quantite = 1; else $this->Quantite = 0;
						if ($this->QuantiteVendue>0)$this->QuantiteVendue = 1; else $this->QuantiteVendue = 0;
						$this->StockOrigine = 1;*/ 
					break;
					default :
						$this->SaveRefUnique();

					break;
				}
				// Tarif Produit = "prix à partir de ..."
				if($this->Tarif < $P->Tarif) $P->Tarif = $this->Tarif;
				
				//Mise à jour du poids et des dimensions
				if (!$this->Poids) $this->Poids = $P->Poids;
				if (!$this->Largeur) $this->Largeur = $P->Largeur;
				if (!$this->Hauteur) $this->Hauteur = $P->Hauteur;
				if (!$this->Profondeur) $this->Profondeur = $P->Profondeur;
				$P->Save();
			}
			parent::Save();
		}
	}

	/**
	 * checkReference
	 * Verifie la reference
	 */
	public function checkReference() {
		$chaine=utf8_decode($this->Reference);
		$chaine=stripslashes($chaine);
		$chaine = preg_replace('`\s+`', '-', trim($chaine));
		$chaine = str_replace("'", "-", $chaine);
		$chaine = str_replace('"', "-", $chaine);
		$chaine = str_replace("?", "", $chaine);
		$chaine = str_replace("!", "", $chaine);
		$chaine = str_replace(".", "", $chaine);
		$chaine = preg_replace('`[\,\ \(\)\+\'\/\:]`', '-', trim($chaine));
		$chaine=strtr($chaine,utf8_decode("ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ?"),"aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn-");
		$chaine = preg_replace('`[-]+`', '-', trim($chaine));
		$this->Reference =  utf8_encode($chaine);
	}
	
	/**
	 * Renvoie le produit associé à la référence
	 * @return 	Produit KE
	 */
	public function getProd() {
		if(!isset($this->Prod)) {
			$this->Prod = Sys::getOneData('Boutique','Produit/Reference/' . $this->Id);
		}		
		return $this->Prod;
	}
	

	/**
	 * Création d'une référence si c'est une nouvelle référence declinée
	 * @return	void
	 */
	private function SaveRefDecline() {
		if (!empty($this->Reference))return;
		//Recherche du produit
		$P = Sys::getData('Boutique','Produit/Reference/'.$this->Id,false,0,1);
		//Recherche des declinaisons
		$D = Sys::getData('Boutique','Declinaison/Reference/'.$this->Id);
		$this->Reference = $P[0]->Reference;
		if (is_array($D))foreach ($D as $De){
			$this->Reference.="-".$De->Code;
		}
	}

	/**
	 * Création d'une référence si c'est une nouvelle référence unique
	 * @return	void
	 */
	private function SaveRefUnique() {
		if (!empty($this->Reference))return;
		//Recherche du produit
		$P = $this->getProd();
		//Recherche des declinaisons
		$R = Sys::$Modules['Boutique']->callData('Boutique/Produit/'.$P->Id.'/Reference/tmsCreate<'.$this->tmsCreate,false,0,1,'','','COUNT(m.Id)');
		$this->Reference = $P->Reference;
		$this->Reference.="-".($R[0]['COUNT(m.Id)']+1);

	}
	/**
	 * Indique si la référence est encore dispo ou non
	 * @return true ou false
	 */
	public function estDisponible($Q=1) {
		return ( ($this->Quantite>=$Q || $this->StockPermanent)&& $this->Actif  );
	}
	/**
	 * Indique si la référence est encore dispo ou non
	 * @return true ou false
	 */
	public function getStockReference() {
		return $this->StockPermanent ? 1000 : $this->Quantite;
	}

	/**
	 * renvoie le prix unitaire HT de la référence hors promo
	 * @return true ou false
	 */
	public function getTarifHorsPromoHT($config=null,$qte=1){
		$prod = $this->getProd();
		$prixRef=$this->Tarif;
		//dans le cas d'un type pack ou personnalisable on ajoute les tarifs spéciaux
		if (($prod->TypeProduit==4||$prod->TypeProduit==5)&&is_array($config)){
			$prixRef=0;
			$cps = array_keys($config);
			foreach ($cps as $c){
				$cp = genericClass::createInstance('Boutique','ConfigPack');
				$cp->initFromId($c);
				// si le tarif du pack est variable
				if ($cp->TarifPack){
					$re = genericClass::createInstance('Boutique','Reference');
					$re->initFromId($config[$c]);
					$prixRef+=$re->TarifPack;
				}else $prixRef+=$cp->TarifHT;
			}
		}
		return $prixRef;
	}



	/**
	 * renvoie le prix de la référence ht payé par le client
	 * Alias de la fonction getTarifHorsPromoHT car on ne peut appliquer de reduction qu'après la tva.
	 * @return true ou false
	 */
	public function getTarifHT($Qte=1,$config=null, $prixUnitaire=false){
		$prod = $this->getProd();
		// Calcul du prix pour la ref
		$prixRef = $this->getTarifHorsPromoHT($config,$Qte);
// ajout septembre 2014 le 29 pour abonnement
		$remise = $this->getRemiseProduit($Qte);
		$prixRef*=(100-$remise)/100;
	//	klog::l("remise", $remise);
		$prixRef = $prod->applyPromo($prixRef);
	//	klog::l("getTarifHT", $prixRef);
		if ($prixUnitaire) {
			return $prixRef;
		}else{
			$prixRef *= $Qte;
			return $prixRef;

			
		}
	}

	/**
	 * renvoie le prix de la référence ttc payé par le client
	 * @return true ou false
	 */
	public function getTarif($Qte=1, $config=null, $prixUnitaire=false){
		$prod = $this->getProd();
		// Calcul du prix pour la ref
		$prixRef = $this->getTarifHorsPromoHT($config,$Qte);
		$remise = $this->getRemiseProduit($Qte);
		$prixRef = $prod->applyPromo($prixRef);
		$Montant= $prod->applyTva($prixRef,$config);
		$Montant*=(100-$remise)/100;
	//	klog::l("getTarif", $Montant);

		if ($prixUnitaire) {
			return sprintf('%.2f',$Montant) ;
		}else{
			$Montant *= $Qte;
			return sprintf('%.2f',$Montant) ;

			
		}
	}
    /**
     * getRemiseProduit
     * Retourne le montant de remise à appliquer pour ce produit et ce client connecté
     */
    public function getRemiseProduit($qte) {
        $prod = $this->getProd();
        return $prod->getRemiseProduit($qte);
    }

	/**
	 * renvoie les déclinaisons de la références
	 *
	 */
	public function getDeclinaisons(){
		if (!isset($this->Declinaisons)) {
            $this->Declinaisons = $this->getParents('Declinaison');
		}
        return $this->Declinaisons;
	}

	/**
	 * Decrementation du stock
	 * @return true ou false
	 */
	public function decrementeStock($Q=1){
		if(!$this->StockPermanent) $this->Quantite-=$Q;
		$this->QuantiteVendue+=$Q;
		$this->Save();
		$P = $this->getProd();
		$P->Ventes+=$Q;
		$P->Save();
		
	}
	/**
	 * Incrementation du stock
	 * @return true ou false
	 */
	public function incrementeStock($Q=1){
		if(!$this->StockPermanent) $this->Quantite+=$Q;
		if ($this->Quantite>0)$this->Actif=true;
		$this->QuantiteVendue-=$Q;
		$this->Save();
		$P = $this->getProd();
		$P->Ventes-=$Q;
		$P->Save();
	}
}