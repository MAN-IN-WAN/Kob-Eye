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
			$prods  = $this->storproc('Boutique/Produit/Reference/' . $this->Id);
			if(is_array($prods) && sizeof($prods)>0) $this->Prod = genericClass::createInstance('Boutique',$prods[0]);
		}		
		return $this->Prod;
	}
	

	/**
	 * Création d'une référence si c'est une nouvelle référence declinée
	 * @return	void
	 */
	private function SaveRefDecline() {
		if (!empty($this->Reference))return;
/*		if (!empty($this->Reference)) {
			$R = Sys::$Modules['Boutique']->callData('Boutique/Reference/Id!='.$this->Id.'&Reference='.$this->Reference,false,0,1);
			if(is_array($R) && sizeof($R)>0) {
				// on passe dans le cas de la recherche de la référence car cette référence existe déjà !!!

			} else {
				return;
			}
		}*/
		// --------------fin modif juillet 2014
		//Recherche du produit
		$P = Sys::$Modules['Boutique']->callData('Boutique/Produit/Reference/'.$this->Id,false,0,1);
		//Recherche des declinaisons
		$D = Sys::$Modules['Boutique']->callData('Boutique/Declinaison/Reference/'.$this->Id);
		$this->Reference = $P[0]["Reference"];
		if (is_array($D))foreach ($D as $De){
			$this->Reference.="-".$De["Code"];
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
	 * renvoie le prix unitaire HT de la référence hors promo
	 * @return true ou false
	 */
	public function getTarifHorsPromoHT($config=null,$qte=1){
			$prods  = $this->storproc('Boutique/Produit/Reference/' . $this->Id);
		$prod = $this->getProd();
		$prixRef=$this->Tarif;
		if ($prixRef == 0) {
			$prixRef =  $prod->Tarif ;
		}
		//$prixRef = $prod->applyPromo($prixRef);
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
//					$prixRef+=$re->Tarif;
					$prixRef+=$re->TarifPack;
				}else $prixRef+=$cp->TarifHT;
			}
		}
		return $prixRef;
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

//===================> MODIFICATIONS SEPTEMBRE 2014

	/**
	* renvoie le prix de la référence ttc payé par le client
	* @return true ou false
	*/
/*	public function getTarifSpe($Qte=1, $config=null, $DemUnite=false){
		$prod = $this->getProd();
		$remise=0;$Montant=0;
		// Calcul du prix pour la ref
		if ($prod->TypeProduit!=5) {
			$prixRef = $this->getTarifHorsPromoHT($config,$Qte);
		}else{
			$prixRef = $this->getTarifPrix($config,$Qte);
		}

		$remise = $this->getRemiseProduit($Qte);
		if ($remise!=0) {
			$letaux =$prod->getTauxTva();
			$prixMini= $prixRef ;
			if ($letaux>0) 	$prixRef+= (($prixMini * $letaux )/100);
			//$Montant= $prod->applyTva($prixRef,$config);
			$prixRef*=(100-$remise)/100;
		}else{
			$letaux =$prod->getTauxTva();
			$prixMini= $prixRef ;
			if ($letaux>0) 	$prixRef+= (($prixMini * $letaux )/100);
			//$Montant= $prod->applyTva($prixRef,$config);
		}
		$Montant=$prixRef;

		if ($DemUnite==1) {
			return sprintf('%.2f',$Montant) ;
		}else{
			$Montant *= $Qte;
			return sprintf('%.2f',$Montant) ;

			
		}
	}
*/
	/**
	* renvoie le prix de la référence ht payé par le client
	* @return true ou false
	*/
/*	public function getTarifSpeHT($Qte=1, $config=null, $DemUnite=false){
		$prod = $this->getProd();
		$prixRef=0;
		$Montant=0;
		// Calcul du prix pour la ref
		$prixRef = $this->getTarifPrix($config,$Qte);

		if ($prixRef == 0) {
			$prixRef =  $prod->Tarif ;
		}

		if ($DemUnite==1) {
			return sprintf('%.2f',$prixRef) ;
		}else{
			$Montant = $prixRef*$Qte;
			return sprintf('%.2f',$Montant) ;
		}
	}
*/
	/**
	* renvoie le prix de la référence ht payé par le client sans arrondi !!!!!
	* @return true ou false
	*/
/*	public function getTarifSpeHTFloat($Qte=1, $config=null, $DemUnite=false){
		$prod = $this->getProd();
		$prixRef=0;
		$Montant=0;
		// Calcul du prix pour la ref
		$prixRef = $this->getTarifPrix($config,$Qte);

		if ($prixRef == 0) {
			$prixRef =  $prod->Tarif ;
		}

		if ($DemUnite==1) {
			return $prixRef ;
		}else{
			$Montant = $prixRef*$Qte;
			return $Montant ;
		}
	}
*/
	/**
	* renvoie le prix unitaire HT de la référence  en tenant compte d'une éventuelle promo
	* @return true ou false
	*/
/*	public function getTarifPrix($config=null,$Qte=1){
		$prod = $this->getProd();
		$prixRef=$this->Tarif;
		if ($prixRef == 0) {
			$prixRef =  $prod->Tarif ;
		}
		// AJOUTÉ PAR MYRIAM SEPTEMBRE 2014 ==========================================================
		// Calcul du prix promo s il y a lieu
		$yapromo=false;
		// y a t il promo 
		$promos = $this->storproc('Boutique/Produit/' . $prod->Id . '/Promotion/DateDebutPromo<=' . time() .'&&DateFinPromo>=' . time(),'','','','DESC','APartirNbUnite');
		if (is_array($promos))foreach ($promos as $promo):
			if ($promo['APartirNbUnite']!=''&&$promo['APartirNbUnite']>$Qte) {
			}else{	
				
				if ($promo['PrixVariation']!='0') {
					if ($promo['TypeVariation']=='1')  {
						// pourcentage
						$PrixPromo= $prixRef - (($prixRef * $promo['PrixVariation'] )/100);
						$yapromo=true;
					}
					if ($promo['TypeVariation']=='2') {
						// montant fixe
						$PrixPromo= $prixRef - $promo['PrixVariation'] ;
						$yapromo=true;
					}
				} else {
					// prixforcé renseigné donc le montant remplace le tarif
					if ($promo['PrixForce']!='0') {
						$PrixPromo= $promo['PrixForce'];
						$yapromo=true;
					}
				}
			}
			if ($yapromo) break;
		endforeach;
		if ($yapromo) {
			if ($PrixPromo<$prixRef) $prixRef =$PrixPromo;
		}

		//dans le cas d'un type pack c'est pour voir si on ajoute montage 
		if (($prod->TypeProduit==4||$prod->TypeProduit==5)&&is_array($config)){
			$cps = array_keys($config);
			foreach ($cps as $c){
				$cp = genericClass::createInstance('Boutique','ConfigPack');
				$cp->initFromId($c);
				if ($cp->TarifPack){
					$re = genericClass::createInstance('Boutique','Reference');
					$re->initFromId($config[$c]);
					$prixRef+=$re->TarifPack-$cp->TarifHT;
				}
			}
		}
		//klog::l("prix avant promo", $prixRef);
		// il faudrait savoir s'il y a des règles de remises


		return $prixRef;
	}
	*/
//------------------FIN MODIFICATION SEPTEMBRE 2014



	/**
	 * renvoie le prix de la référence ttc payé par le client
	 * @return true ou false
	 */
	public function getTarifMontant($Prix, $avecTva=1, $Qte=1){

		$prod = $this->getProd();
		// Calcul du prix pour la ref
		$prixRef = $Prix;
		//$prixRef = $prod->applyPromo($prixRef);
		$prixRef *= $Qte;
		if ($avecTva==1) {
			$Montant= $prod->applyTva($prixRef) ;
		} else {
			$Montant= $prixRef ;
		}
		return sprintf('%.2f',$Montant) ;
	}
	/**
	 * renvoie les déclinaisons de la références
	 *
	 */
	public function getDeclinaisons(){
		if (!isset($this->Declinaisons)) {
			$dc     = $this->storproc('Boutique/Declinaison/Reference/' . $this->Id);
/*			if (is_array($dc)) foreach ($dc as $d) $this->Declinaisons[] = genericClass::createInstance('Boutique',$d);
			else $this->Declinaisons = array();*/

			if (is_array($dc)&&isset($dc)) foreach ($dc as $d) $this->Declinaisons[] = genericClass::createInstance('Boutique',$d);
			else $this->Declinaisons = array();

		}		
		if (isset($this->Declinaisons)) return $this->Declinaisons ;
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

	/** Moulinettes pour recalcul prix **/
	public function getTarif2013TTC($prix,$TypeTva) {
		// Calcul du prix parmi les références dispos
		$prixMini = $prix;

		$Montant= $prixMini + ($prix * $TypeTva )/100;
		return $Montant ;
	}

	public function getTarif2014TTC($prix,$taux) {
		// Calcul du prix parmi les références dispos
		$Montant= $prix + (($prix * $taux )/100);
		//return sprintf('%.2f',$Montant) ;
		return $Montant ;

	}
	public function getTarif2014HT($prix,$taux) {
		// Calcul du prix parmi les références dispos
		$letaux=  1+($taux /100);
		$Montant= $prix /$letaux;
		return $Montant ;
	}



	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return Sys::$Modules['Boutique']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}


	// PGF
	public function getTarifTTC($prod, $puttc, $qte, $otva){
		$remise = $this->getRemiseProduit($qte);
		$puttc *= (100 - $remise) / 100;
		$puttc = round($prod->applyPromo($puttc), 2);
		return $puttc * $qte;
	}

	// PGF
	public function getTarifHorsPromoTTC($config=null,$otva=null){
		$prod = $this->getProd();
		$prixRef=$this->Tarif;
		if($prixRef == 0) $prixRef =  $prod->Tarif;
		if($otva) $taux = $otva->getTaux($prod->TypeTvaInterne);
		$ttc = round($prixRef * (1 + $taux / 100), 2);
		
		//$prixRef = $prod->applyPromo($prixRef);
		//dans le cas d'un type pack ou personnalisable on ajoute les tarifs spéciaux
		if (($prod->TypeProduit==4||$prod->TypeProduit==5)&&is_array($config)){
			$ttc=0;
			$cps = array_keys($config);
			foreach ($config as $c => $r) {
				$cp = Sys::$Modules['Boutique']->callData('ConfigPack/'.$c, false, 0, 1);
				$cp = $cp[0];
				$re = Sys::$Modules['Boutique']->callData('Reference/'.$r, false, 0, 1);
				$re = $re[0];
				$pr = Sys::$Modules['Boutique']->callData('Produit/Reference/'.$re['Id'], false, 0, 1);
				$pr = $pr[0];
				$taux = $otva->getTaux($pr->TypeTvaInterne);
				if($cp['TarifPack']) $ttc += round($re['TarifPack'] * (1 + $taux / 100), 2);
				else $ttc += round($cp['TarifHT'] * (1 + $taux / 100), 2);
			}
		}
		return $ttc;
	}

}