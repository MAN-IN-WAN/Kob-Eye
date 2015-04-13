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
	
	// tableau vers chaine
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
	// Chaine vers tabelau
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
	public function Recalculer() {
		
		$this->initConfig();
		
		$Ref = $this->getReference();
		$prod = $Ref->getProd();
		$this->Titre=$Ref->Nom;

		//calcul taux de remise
		$remisetx = 1 - ($Ref->getRemiseProduit($this->Quantite)/100);
		$this->TauxRemise = $remisetx;
		$this->Taxe=$prod->getTauxTva();

		$this->TableTva = serialize($this->getTableTva($remisetx));

		// ==> mars2015

		// GESTION DES ARRONDIS !!!	

		// tarif ht Unitaire  hors promo
		$this->MontantUnitaireHorsPromoHT=$Ref->getTarifHorsPromoHT($this->Config);
		// tarif ttc Unitaire hors promo
		$this->MontantUnitaireHorsPromoTTC=$prod->applyTva($this->MontantUnitaireHorsPromoHT,$this->Config);
		// tarif ttc de la ligne
		$this->MontantHorsPromoTTC=$this->MontantUnitaireHorsPromoTTC*$this->Quantite;
		
		
		// tarif unitaire Ht réél
		$this->MontantUnitaireHT=$Ref->getTarifHT(1,$this->Config,true);
		
		// tarif  TTC  unitaire réel
		$this->MontantUnitaireTTC=$prod->applyTva($this->MontantUnitaireHT,$this->Config,$remisetx);
		
		// tarif  TTC payé
		$this->MontantTTC=round($this->MontantUnitaireTTC*$this->Quantite,2);

		// tarif  Ht payé 
//		$this->MontantHT=round($this->MontantUnitaireHT*$this->Quantite,2);



		if ($prod->TypeProduit==5||$prod->TypeProduit==4) {
			//$this->MontantHT=round($this->MontantUnitaireHT,2);
			$this->MontantHT=$this->MontantUnitaireHT;
			// tarif  Ht  de la ligne hors promo
			$this->MontantHorsPromoHT=$this->MontantUnitaireHorsPromoHT;

		} else {
			$letaux=($this->Taxe/100)+1;

			//$this->MontantHT=round($this->MontantTTC/$letaux,2);
			$this->MontantHT=$this->MontantTTC/$letaux;
			$this->MontantHorsPromoHT=$this->MontantHorsPromoTTC/$letaux;


		}

		
		
		// total ht remise
		$this->MontantRemiseHT=$this->MontantHorsPromoHT - $this->MontantHT ;
		
		//total ttc remise
		$this->MontantRemiseTTC=round($this->MontantHorsPromoTTC - $this->MontantTTC,2) ;
		

		
		//mars 2015======>

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
	
	function getTableTva ($remisetx=1) {
		$refProduitPrincipal = $this->getReference();
		$tabletva=array();
		if ($this->TypeProduit==4){
			//$this->recalculer();
			//alors il faut traiter tous les taux de tva
			//recupération du produit
			$ht=0;
			$prod = $refProduitPrincipal->getProd();
			$cps = $prod->getChildren('ConfigPack');
			if (is_array($cps))foreach ($cps as $cp){
				$re2 = genericClass::createInstance('Boutique','Reference');
				$re2->initFromId($this->Config[$cp->Id]);
				if ($cp->TarifPack){
					$Montant = $re2->TarifPack * $remisetx;
				} else {
					$Montant = $cp->TarifHT * $remisetx;
				}
				if($Montant) {
					$P = $re2->getProd();
					$TxTva = $P->getTauxTva($P->TypeTva);
					if (isset($tabletva["T".$TxTva]) ){
						 $tabletva["T".$TxTva]['Base']+=$Montant;
					}
					else $tabletva["T".$TxTva] = array( 'Base' => $Montant ,"Taux"=> $TxTva);
				}
			}
		}else{
			//$this->updateTableTvaFacture($lc->Taxe,$lc->MontantHT);
			$tabletva["T".$this->Taxe] = array( 'Base' => $this->MontantHT ,"Taux"=> $this->Taxe);
			
		}
		return $tabletva;
	}
	
	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
   	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return Sys::$Modules['Boutique']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
   	}

}