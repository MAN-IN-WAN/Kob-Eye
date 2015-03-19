<?php

class LigneCommande extends genericClass {

	/**
	 * Enregistrement d'une LigneCommande
	 * -> Check référence
	 * @return	void
	 */
	public function Save() {

		if ($this->Expedie==1&&$this->Livre==0){
			$cde = $this->storproc('Boutique/Commande/LigneCommande/'.$this->Id,false,0,1);
			$cde = genericClass::createInstance('Boutique',$cde[0]);
		}
		
		//si la configuration est un tableau alors il faut la transformer en chaine
		$this->restoreConfig();


		parent::Save();

		/*$P = Sys::$Modules['Boutique']->callData('Boutique/Reference/LigneCommande/'.$this->Id,false,0,1);
		if (!empty($P)&&is_array($P[0])){
			$P = genericClass::createInstance('Boutique',$P[0]);
			$this->Titre = $P->Reference." - ".$P->Nom;
			$this->Reference = $P->Reference;
			//$this->Quantite = ;
			$this->MontantHT = $P->Tarif;
			$this->MontantPromoHT = $P->PrixPromotion;
			$this->Taxe= $P->TypeTva;
		}
		parent::Save();*/


		
	}

	/**
	 * applyActions
	 * Applique les actions pour la ligne de commande en fonction de la nature du produit
	 */
	public function applyActions () {
//		Klog::l('----> Ligne commande apply action');
		$ref = $this->getReference();
		$prod = $ref->getProd();
		$com = $this->getCommande();
		$cli = $com->getClient();
		switch ($prod->NatureProduit){
			//Cas livrable
			case 1:
				//rien pour l'instant
			break;
			//Cas service
			case 2:
//				Klog::l('----> Ligne commande ajout service');
//				//creation d'un objet service
				$s = genericClass::createInstance('Boutique','Service');
				$m = Magasin::getCurrentMagasin();
				$s -> Nom = $prod->Nom.' - Nom ref : '.$ref->Nom;
			//	$s -> Produit = $prod->Id;
				$s -> DateDebut = time();
				$s -> DateFin = time()+$ref->Duree;
				$s -> addParent($cli);
				$s -> addParent($this);
				$s -> addParent($prod);
				$s -> addParent($m);
				$s -> Save();
			break;
			//Cas téléchargement
			case 3:
//				Klog::l('----> Ligne commande ajout telechargement');
				$s = genericClass::createInstance('Boutique','Telechargement');
				$s -> Nom = $prod->Nom.' - '.$ref->Nom;
				$s -> Url = $ref->Fichier;
				$s -> Fichier = $ref->Fichier;
				$s -> addParent($cli);
				$s -> addParent($this);
				$s -> Save();
			break;
		}
	}

	/**
	 * Fonction qui renvoie la reference
	 * 
	 */
	function getReference ($reference="") {
		if (!empty($reference)){
			$Ref = $this->storproc('Boutique/Reference/Reference='.$reference,false,0,1);
			$this->RefObject=genericClass::createInstance('Boutique',$Ref[0]);
		}
		if (!isset($this->RefObject)) {
			$Ref = $this->storproc('Boutique/Reference/LigneCommande/'.$this->Id,false,0,1);
			$this->RefObject=genericClass::createInstance('Boutique',$Ref[0]);
		}
		return $this->RefObject;
	}
	/**
	 * Fonction qui renvoie la commande
	 * 
	 */
	function getCommande () {
		if (!isset($this->Commande)) {
			$Com = $this->storproc('Boutique/Commande/LigneCommande/' . $this->Id,false,0,1);
			$this->Commande=genericClass::createInstance('Boutique',$Com[0]);
		}
		return $this->Commande;
	}

	/**
	 * Fonction qui renvoie l'url complète d'un produit présent dans le panier
	 * 
	 */
	function getUrlProduit () {
		$Ref = $this->getReference();
		if (!is_object($Ref)) return;
		$prod = $Ref->getProd();
		return $prod->getUrl();
	}

	/**
	 * Fonction qui renvoie la référence qui est acheté en fonction du produit
	 * 
	 */
	function InitFromReference ($Reference,$Quantite,$config=null,$options=null) {
		$Ref = $this->getReference($Reference);
		$prod = $Ref->getProd();
		$Colisage=1;
		$Colisage=$prod->GetColisage();
		$QuantiteLigne= $Quantite*$Colisage;
		$this->Quantite=$QuantiteLigne;
		$this->Config = $config;
		$this->Options = $options;
		$this->Reference=$Ref->Reference;
		$this->AddParent($Ref);
		$this->Recalculer();
	
	}
	
	public function restoreConfig(){
		if (is_array($this->Config)&&sizeof($this->Config)){
			$cfg = "";
			foreach ($this->Config as $k=>$c){
				if (!empty($cfg))$cfg.='::';
				$ref = genericClass::createInstance('Boutique','Reference');
				$ref->initFromId($c);
				$cfg.='cpk'.$k.'->'.$ref->Reference;
			}
			$this->Config = $cfg;
		}
	}
	
	public function initConfig(){
		if (is_string($this->Config)&&!empty($this->Config)){
			$tmp = explode("::",$this->Config);
			$this->Config = Array();
			foreach ($tmp as $t){
				if (!empty($t)){
					$pair = explode("->",$t);
					$refs = Sys::getData('Boutique','Reference/Reference='.$pair[1]);
					$this->Config[substr($pair[0],3)] = $refs[0]->Id;
				}
			}
		}
	}

	/**
	 * Recalcule des éléments à afficher dans le panier
	 * @return	void
	 */
	public function Recalculer($otva=null) {
		
		$this->initConfig();
		
		$Ref = $this->getReference();
		$prod = $Ref->getProd();

//		if($otva) $this->Taxe=$otva->getTaux($prod->TypeTvaInterne);
//		else $this->Taxe=$prod->getTauxTva();
//		$puttc = $Ref->getTarifHorsPromoTTC($this->Config, 1, $otva);

		// FAIRE UNE FONCTION QUI VA CHERCHER  LA DECLINAISON AVEC LE NOM PUBLIC : 
		//$this->Titre=$prod->Nom;
		// SI PAS D'ATTRIBUT ON MET LE NOM DE LA REFERENCE
		$this->Titre=$Ref->Nom;
		$this->MontantUnitaireHorsPromoHT=$Ref->getTarifHorsPromoHT($this->Config);
		$this->MontantHorsPromoHT=$this->MontantUnitaireHorsPromoHT*$this->Quantite;
		$this->MontantUnitaireHorsPromoTTC=$prod->applyTva($this->MontantUnitaireHorsPromoHT,$this->Config);
		$this->MontantHorsPromoTTC=$this->MontantUnitaireHorsPromoTTC*$this->Quantite;

		$this->MontantUnitaireHT=$Ref->getTarifHT($this->Quantite,$this->Config,true);
		
		//calcul taux de remise
		$remisetx = 1 - ($Ref->getRemiseProduit($this->Quantite)/100);

//septembre2014 pour config pack et carte personnalisée --> ça à l'air de fonctionner
		//$this->MontantUnitaireHT=$Ref->getTarifHT($this->Config);


		// attention meme si on veut le montant unitaire il faut mettre la quantité pour les promotions en fonction des quantités
		  //  par contre on met true dans dernier argument pour dire qu'on veut le prix unitaire
//		$this->MontantUnitaireHT=$Ref->getTarifSpeHTFloat($this->Quantite,$this->Config,true);
		$this->MontantHT=$this->MontantUnitaireHT*$this->Quantite;
		$this->MontantUnitaireTTC=$prod->applyTva($this->MontantUnitaireHT,$this->Config,$remisetx);
		$this->MontantTTC=$this->MontantUnitaireTTC*$this->Quantite;
		$this->MontantRemiseHT=$this->MontantHorsPromoHT -$this->MontantHT ;
		$this->MontantRemiseTTC=$this->MontantHorsPromoTTC -$this->MontantTTC ;

/*klog::l("MontantUnitaireHorsPromoHT", $this->MontantUnitaireHorsPromoHT);
klog::l("MontantUnitaireHorsPromoTTC", $this->MontantUnitaireHorsPromoTTC);
klog::l("MontantUnitaireHT", $this->MontantUnitaireHT);
klog::l("MontantUnitaireTTC", $this->MontantUnitaireTTC);
klog::l("MontantTTC", $this->MontantTTC);
klog::l("MontantRemiseHT", $this->MontantRemiseHT);
klog::l("MontantRemiseTTC", $this->MontantRemiseTTC);*/

		// decembre 2013 pris en compte de type de tva avec taux et zone 

		if($otva) $this->Taxe=$otva->getTaux($prod->TypeTvaInterne);
		else $this->Taxe=$prod->getTauxTva();

		$this->Image="";
		$this->RefEnvoi="";
		$this->Livre=0;
		$this->Expedie=0;
		$this->Poids=$Ref->Poids*$this->Quantite;
		if (empty($Ref->Poids))  $this->Poids = $prod->Poids*$this->Quantite;
		$this->Largeur=$Ref->Largeur*$this->Quantite;
		if (empty($Ref->Largeur))  $this->Largeur = $prod->Largeur*$this->Quantite;
		$this->Hauteur=$Ref->Hauteur*$this->Quantite;
		if (empty($Ref->Hauteur))  $this->Hauteur = $prod->Hauteur*$this->Quantite;
		$this->Profondeur=$Ref->Profondeur*$this->Quantite;
		if (empty($Ref->Profondeur))  $this->Profondeur = $prod->Profondeur*$this->Quantite;
	}
	/**
	 * Retourne le prix TTC d'un élément
	 * @return	PRIX en euros
	 */
	/*function getTTC () {
		return number_format(round($this->Montant * (1 + $this->Taxe / 100), 2), 2);
	}*/

	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
   	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return Sys::$Modules['Boutique']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
   	}

}