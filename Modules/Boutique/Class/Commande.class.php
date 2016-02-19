<?php

class Commande extends genericClass {
	var $Avalider = false;
	var $Apayer = false;
	var $ADevalider = false;
	var $ACurrent = false;

	/**
	 * @return	void
	 */
	public function Delete() {
		//restockage
		if ($this -> Valide) {
			$this -> discardCommande();
		}
		//suppression du paiement
		if ($this -> Paye) {
			$this -> getPaiement();
			if (is_object($this -> Paiement))
				$this -> Paiement -> Delete();
		}
		//suppression du bon de livraison
		//TODO Erreur de mise à jour dans la suppression
		if ($this -> getBonLivraison())
			$this -> BonLivraison -> Delete();
		//supression des lignes commandes
		if ($this -> getLignesCommande()) {
			foreach ($this->LignesCommandes as $l)
				$l -> Delete();
		}
		parent::Delete();
	}
	/**
	 * Enregistrement d'une commande en BDD
	 * @return	void
	 */
	public function Save() {
		$this -> getFacture();
		$this -> getBonLivraison();

		if ($this -> Id) {
			//Verification avec l'objet en base
			$old = Sys::getOneData('Boutique','Commande/'.$this->Id);

			//Test des comportements à déclencher
			$this -> Apayer = (!$old->Paye && $this -> Paye);
			$this -> Avalider = (!$old->Valide && $this -> Valide);
			$this -> ADevalider = ($old->Valide && !$this -> Valide);
			$this -> ACurrent = (!$old->Current && $this -> Current);

		} else {
			$this -> Apayer = $this -> Paye;
			$this -> Avalider = $this -> Valide;
		}

		//Sauvegarde
		parent::Save();

		// ==> Mars 2015
		// Recalcul de l'entete de commande par rapport aux lignes
		$this -> getLignesCommande();
		if (is_array($this -> LignesCommandes)) {
			$this->MontantTTC=0;
			$this->MontantHT =0;
			$this->MontantHorsPromoTTC=0;
			$this->MontantHorsPromoHT =0;
			foreach ($this->LignesCommandes as $obj) {
                $this->MontantTTC += $obj->MontantTTC;
                $this->MontantHT += $obj->MontantHT;
                $this->MontantHorsPromoTTC += $obj->MontantHorsPromoTTC;
                $this->MontantHorsPromoHT += $obj->MontantHorsPromoHT;
            }
		}
			
		//Definition du montant à payer avec la livraison
		if (isset($this -> BonLivraison) && is_object($this -> BonLivraison) && $this->getMontantLivrable()>0) {

			$this -> MontantPaye = round($this -> MontantTTC + $this -> BonLivraison -> MontantLivraisonTTC,2);
			$this -> MontantLivraison = round($this -> BonLivraison -> MontantLivraisonTTC,2);
			$this -> BonLivraison -> AddParent($this);
			$this -> BonLivraison -> Save();

		}else {
			 $this -> MontantPaye = $this -> MontantTTC;
		}
		if (isset($this -> LignesCommandes) && is_array($this -> LignesCommandes))
			foreach ($this->LignesCommandes as $obj) {
                $obj->AddParent($this);
                $obj->Save();
            }

		//Enregistrement de la reference
		$this -> SaveRef();

		//Verification de la commande
		$this -> checkAndBuild();

		// Definition du magasin
		//Correction Enguer 20140923
		$this->Magasin = $this->getMagasin();
		if (is_object($this->Magasin)) {
			$this -> AddParent($this->Magasin);
		} else {
			$this -> setMagasin();
			if (is_object($this -> Magasin)) {
				$this -> AddParent($this -> Magasin);
			}
		}

		//Execution des comportements
		if ($this -> Avalider) {
			$this -> applyCommande();
            $this -> sendMailAcheteur();
            $this -> sendNotification();
		}
		if ($this -> ADevalider) {
			$this -> discardCommande();
		}
		if ($this -> Apayer) {
			$this -> setMagasin();
			$this -> sendMailAcheteur();
			$this -> applyInvoice();
            $this -> sendNotification();
		}
		if ($this -> ACurrent) {
			//définit cette commande comme commande par défaut
			$this -> getClient();
			$Coms = $this -> Client -> getAllCommandes();
			foreach ($Coms as $c) {
				if ($c -> Id != $this -> Id) {
					$c -> Current = false;
					$c -> Save();
				}
			}
		}

        //generation de la table tva pour l'entete
		$this->getTableTva();

		//Sauvegarde
		parent::Save();
	}

	/**
	 * applyInvoice
	 * Termine la commande et execute l'ensemble des actions nécessaires pour chacune des lignes commandes
	 */
	private function applyInvoice() {
		$this->LignesCommandes = $this->getLignesCommande();
		foreach ($this->LignesCommandes as $l) {
			$l->applyActions();
		}
	}
	
	/**
	 * Création d'une référence
	 * @return	void
	 */
	private function SaveRef() {
		//reference en commande

		if ($this -> Valide) {
			if(substr($this->RefCommande,0,3) == 'PAN') $this->DateCommande = time();
			$this -> RefCommande = sprintf("COM%05d", $this -> Id);
		}
		else {
			if($this->RefCommande == '') $this->DateCommande = time();
			$this -> RefCommande = sprintf("PANIER%05d", $this -> Id);
		}
	}

	/**
	 * checkAndBuild
	 * Verifie les éléments nécessaires à la validation de la commande
	 * -Client
	 * -Adresse Livraison
	 * -Adresse Facturation
	 * - Bon de livraison
	 */
	public function checkAndBuild() {
		$mag = Magasin::getCurrentMagasin();
		if (!$this -> getClient() or !$this -> getAdresseLivraison() or !$this -> getAdresseFacturation() or (!$this -> getBonLivraison() && $mag->EtapeLivraison && $this->getMontantLivrable())) {
			return false;
		}
		if ($this -> getClient()) {
			$this -> AddParent($this -> Client);
		}
		if ($this -> getAdresseLivraison()) {
			$parents = $this->getParents('Adresse');
			foreach($parents as $parent){
				if ($parent->Type=='Livraison'){
					$this -> DelParent($parent);
				}
			}
			$this -> AddParent($this -> AdrLiv);
		}
		if ($this -> getAdresseFacturation()) {
			$parents = $this->getParents('Adresse');
			foreach($parents as $parent){
				if ($parent->Type=='Facturation'){
					$this -> DelParent($parent);
				}
			}
			$this -> AddParent($this -> AdrFac);
		}
		if ($this -> getOffreSpeciale()) {
			$this -> AddParent($this -> OffreSpeciale);
		}
		if ($this -> getCodePromo()) {
			$this -> AddParent($this -> CodePromo);
		}
		return true;
	}

	/****************************************************** LIGNES COMMANDE ******************************************************/

	/**
	 * Récupère toutes les lignes commandes déjà existantes
	 * @return	Tableau des lignes commandes
	 */
	public function initFromBDD() {
		//Initialisation des lignes de commande
		$this->getLignesCommande();
		$this -> recalculer();
		return $this->LignesCommandes;
	}

	/**
	 * Ajoute une ligne à une commande
	 * Attention: elle ne s'enregistre pas tout de suite, cela se fait quand on Save la commande
	 * @param	Objet	Référence
	 * @param	int		Quantité
	 */
	public function ajouterLigneCommande($lc) {
		//on vérifie que la commande en cours n'est pas dans un état valide sinon il faut d'abord la dévalider
		if ($this -> Valide) {
			$this -> Valide = 0;
		}
		// Si on a pas encore de champs pour les stocker on le créé
		if (!isset($this -> LignesCommandes) || $this -> LignesCommandes == NULL)
			$this -> LignesCommandes = $this -> InitFromBDD();
		// On vérifie si on a déjà cette référence (auquel cas on change juste la quantité)
		$found = false;
		$ref = $lc -> getReference();
		$Prod = $ref->getProd();
		$typeprod = $Prod->TypeProduit;
		//Dans le cas d'une formule on vérifie que la configuration est bien présente et complête
		$error=false;
		if ($typeprod==4||$typeprod==5){
			//cas formule donc on vérifie que les configurations sont bien présentes dans les variables POST
			$configpacks = $Prod->getChildren('ConfigPack');
			//génération de la config dans la ligne commande
			$conf = '';
			$desc='';
			foreach ($configpacks as $cp){
				if (!empty($conf))$conf.='::';
				if ((!isset($lc->Config[$cp->Id])||empty($lc->Config[$cp->Id]))&&$cp->ChoixObligatoire)$error = true;
				elseif (intval($lc->Config[$cp->Id])<1&&!$cp->ChoixObligatoire){
					$desc.=$cp->Nom."\r\n";//.': '." non défini\r\n";
				}else{
					$refs=Sys::getData('Boutique','Reference/'.$lc->Config[$cp->Id]);
					$desc.=$cp->Nom.': <br /> <strong>'.$refs[0]->Nom."</strong>\r\n";
					$conf.='cpk'.$cp->Id.'->'.$refs[0]->Reference;
				}
				//gestion des options dans les configpacks
				$optionscp = $cp->getChildren('Options');
				foreach ($optionscp as $ocp){
					if (empty($lc->Options[$cp->Id.'_'.$ocp->Id])){
						$desc.="OPTION ".$ocp->Nom.': '." non défini\r\n";
					}else{
						switch ($ocp->TypeOptions){
							case "5": /*ListeGraphique*/
							case "4": /*ListeChoix*/
								$opdetail = Sys::getData('Boutique','OptionsDetails/'.$lc->Options[$cp->Id.'_'.$ocp->Id]);
								$desc.=" - Option ".$ocp->Nom.':<br /> <strong>'.$opdetail[0]->Nom."</strong>\r\n";
							break;
							default: /*Texte ou Nombre*/
								$desc.=" - Option ".$ocp->Nom.': <br /><strong> '.$lc->Options[$cp->Id.'_'.$ocp->Id]."</strong>\r\n";
							break;
						}
					}
				}
			}
			if ($error){
				$this -> AddError(Array("Message" => "Le produit $ref->Nom n'est pas bien configuré. Veuillez reessayer."));
				return;
			}else{
				$lc->TypeProduit = $typeprod;
				$lc->Config = $conf;
				$lc->Description = $desc;
			}
		}
		
		//on vérifie l'existence de la réfénrece sauf dans le cas d'une formule
		if(isset($this->LignesCommandes)) foreach ($this->LignesCommandes as $k => $ligne) {
			if ($ligne -> Reference == $lc -> Reference&&$typeprod!=4&&$typeprod!=5) {
				// verifie stock  de cette référence si le stock est atteint on n'ajoute pas !
				$totQte = $ligne -> Quantite + $lc -> Quantite;
				if (!$ref -> estDisponible($totQte)) {
					$this -> AddError(Array("Message" => "Stock insuffisant pour le produit $ref->Nom."));
					return false;
				}
				//verifie à ne pas descendre en dessous de 1
				if ($totQte<1){
					return false;
				}
				$this -> LignesCommandes[$k] -> Quantite += $lc -> Quantite;
				$found = true;
				$this -> LignesCommandes[$k] -> Recalculer();
				break;
			}
		}


		//on verifie l'existence d'un service dans le cas d'un ajout de service
		$cli = $this->getClient();
		if ($Prod->NatureProduit==2 && is_object($cli)){
			$ser = $cli->getChildren('Service');
			if (sizeof($ser)){
				// modification 2 janvier 2015 pour tester la fin de l'abonnement
				foreach($ser as $servi){
					if($servi->DateFin > time()){ 
						//SI l'abonnement est périmé on peut en contracter un nouveau sinon on se fait refouler
						//Si il y a deja un service on refuse l'ajout de ce type de produit
						$this->AddError(Array("Message" => "Vous avez déjà un abonnement actif $ref->Nom."));
						return false;
					}
				}
			}
		}



		// Si non trouvé on ajoute la ligne commande
		if (!$found) {
			if (!$ref -> estDisponible($lc -> Quantite)) {
				$this -> AddError(Array("Message" => "Stock insuffisant pour le produit $ref->Nom."));
				return false;
			}
			// On garde une trace vers l'url du produit pour pouvoir cliquer dessus depuis le panier
			$this -> LignesCommandes[] = $lc;
		}
		//Ajout du message de success
		if ($lc->Quantite>0){
			$this -> AddSuccess(Array("Message" => "Ajout du produit $ref->Nom avec succés."));
		}else{
			$this -> AddSuccess(Array("Message" => "Diminution de la quantité du produit $ref->Nom avec succés."));
		}


		// Recalcule commande
		$this -> recalculer();
		return true;
	}

	/**
	 * Retire une ligne de la commande
	 * @param	object	La référence produit à enlever
	 * @return	void
	 */
	public function enleverLigneCommande($refReference) {
		//on vérifie que la commande en cours n'est pas dans un état valide sinon il faut d'abord la dévalider
		if ($this -> Valide) {
			$this -> Valide = 0;
		}
		if ($this -> LignesCommandes == NULL)
			$this -> LignesCommandes = $this -> InitFromBDD();
		foreach ($this->LignesCommandes as $k => $l) {
			if ($l -> Reference == $refReference) {
				$ref = $l -> getReference();
				$this -> LignesCommandes[$k] -> Delete();
				unset($this -> LignesCommandes[$k]);
				//Ajout du message de success
				$this -> AddSuccess(Array("Message" => "Suppression du produit $ref->Nom avec succés."));
				return true;
			}
		}
		$this -> recalculer();
		$this -> AddError(Array("Message" => "Impossible de supprimer le produit $refReference."));
		return false;
	}

	/**
	 * Définit une quantité précise pour une ligne de commande
	 * @param	object	Référence que l'on veut mettre à jour
	 * @param	int		Nouvelle quantité à attribuer
	 * @return	void
	 */
	public function ajusterQtePanier($refReference, $qte) {
		//on vérifie que la commande en cours n'est pas dans un état valide sinon il faut d'abord la dévalider
		if ($this -> Valide) {
			$this -> Valide = 0;
		}
		if (!isset($this -> LignesCommandes))
			$this -> getLignesCommande();
		if (!sizeof($this -> LignesCommandes))
			return false;
		foreach ($this->LignesCommandes as $k => $l) {
			if ($l -> Reference == $refReference) {
				$ref = $l -> getReference();
				if (!$ref -> estDisponible($qte)) {
					$this -> AddError(Array("Message" => "Stock insuffisants pour le produit $ref->Nom."));
					return false;
				}
				$this -> LignesCommandes[$k] -> Quantite = $qte;
				$this -> LignesCommandes[$k] -> Recalculer();
			}
		}
		$this -> recalculer();
		return true;
	}

	public function getStatus() {
		$status = "Brouillon";
		if ($this -> Valide) {
			$status = 1;
		}
		if ($this -> PaymentPending) {
			$status = 2;
		}
		if ($this -> EchecPayment) {
			$status = 3;
		}
		if ($this -> Paye) {
			$status = 4;
		}
		if ($this -> Expedie) {
			$status = 5;
		}
		if ($this -> Cloture) {
			$status = 6;
		}
		return $status;
	}

	/*************************************************** CALCUL MONTANT COMMANDE ****************************************************/

	/**
	 * Recalcule le montant de la commande avec la bonne TVA
	 * @return	void
	 */
	public function recalculer() {
		$this -> MontantHTHorsPromo = 0;
		$this -> MontantTTCHorsPromo = 0;
		$this -> MontantHT = 0;
		$this -> MontantTTC = 0;
		$this -> Remise = 0;
		$this -> Qte = 0;
		$this -> Poids = 0;
		$this -> Volume = 0;

		if (isset($this -> LignesCommandes) && is_array($this -> LignesCommandes)) {
			foreach ($this->LignesCommandes as $k=>$LC) {
				$LC->Recalculer();
				$this -> MontantHTHorsPromo += $LC->MontantHorsPromoHT;
				$this -> MontantTTCHorsPromo += $LC->MontantHorsPromoTTC;
				$this -> MontantHT += $LC->MontantHT;
				$this -> MontantTTC += $LC->MontantTTC;
				$this -> Remise += $LC->MontantRemiseTTC;
				$this -> Qte += $LC -> Quantite;
				$this -> Poids += $LC -> Poids;
				$this -> Volume += ($LC -> Largeur * $LC -> Hauteur * $LC -> Profondeur);
			}
		}
		//recuperation du bon de livraison
		$this -> getBonLivraison();
		$fraisDePortOffert = false;

		// ajouter la reduction d'offre speciale
		if (isset($this -> OffreSpeciale)) {
			if ($this -> OffreSpeciale -> TypeVariation != 3) {
				$this -> Remise += $this -> OffreSpeciale -> getReducMontant($this -> MontantTTC);
			} else {
				//soustraction du frais de port
				$fraisDePortOffert = true;
				$this -> Remise += $this -> BonLivraison -> MontantLivraisonTTC;
			}
		}
		// ajouter la reduction CodePromo
		if ($this -> getCodePromo()) {
			if ($this -> CodePromo -> TypeVariation != 3) {
				$this -> Remise += $this -> CodePromo -> getReducMontant($this -> MontantTTC);
			} else {
				if (!$fraisDePortOffert) {
					//soustraction du frais de port
					$this -> Remise += $this -> BonLivraison -> MontantLivraisonTTC;
				} else {
					unset($this -> CodePromo);
				}
			}
		}
        //mise à jour du montant TTC
        $this -> MontantTTC -= $this->Remise;

	}
	public function getClient() {
		if (isset($this -> Client) && is_object($this -> Client))
			return $this -> Client;
		if (!$this -> Id)
			return false;
		$cli = $this -> storproc('Boutique/Client/Commande/' . $this -> Id);
		$this -> Client = genericClass::createInstance('Boutique', $cli[0]);
		return $this -> Client;
	}

	/**
	 * Définition de l'adresse de livraison
	 * @return	void
	 */
	public function setAdresseLivraison($Livraison) {
		$adrliv = $this -> storproc('Boutique/Adresse/' . $Livraison);
		$this -> AdrLiv = genericClass::createInstance('Boutique', $adrliv[0]);

	}

	public function getAdresseLivraison() {

		if (!isset($this -> AdrLiv) || !is_object($this -> AdrLiv)) {
			if (!$this -> Id) return false;
			$adrliv = $this -> storproc('Boutique/Adresse/Commande/' . $this -> Id . '&m.Type=Livraison');
			if (empty($adrliv)) return false;
			$this -> AdrLiv = genericClass::createInstance('Boutique', $adrliv[0]);
		}
		return $this -> AdrLiv;

	}
	
	/**
	 * getMontantLivrable
	 * Retourne le montant total des elements livrables
	 */
	public function getMontantLivrable(){
		$totht = 0;
		$this->LignesCommandes = $this->getLignesCommande();
		foreach ($this->LignesCommandes as $l) {
			$ref = $l -> getReference();
			$prod = $ref -> getProd();
	
			//si produit livrable
			if ($prod->NatureProduit==1){
				$totht+=$ref->Tarif;
			}
		}
		return $totht;
	}

	/**
	 * Définition de l'adresse de facturation
	 * @return	void
	 */
	public function setAdresseFacturation($Facturation) {
		$adrfac = $this -> storproc('Boutique/Adresse/' . $Facturation);
		$this -> AdrFac = genericClass::createInstance('Boutique', $adrfac[0]);
	}

	public function getAdresseFacturation() {
		if (!isset($this -> AdrFac) || !is_object($this -> AdrFac)) {
			if (!$this -> Id) return false;
			$adrfac = $this -> storproc('Boutique/Adresse/Commande/' . $this -> Id . '&m.Type=Facturation');
			if (empty($adrfac)) return false;
			$this -> AdrFac = genericClass::createInstance('Boutique', $adrfac[0]);
		}
		return $this -> AdrFac;
	}

	/*------------Définition de l'offre speciale ---*/
	public function setOffreSpeciale($OffreSpeciale) {
		//echo "yo";
		$this -> OffreSpeciale = $OffreSpeciale;
		$this -> recalculer();
	}

	public function getOffreSpeciale() {
		if (isset($this -> OffreSpeciale) && is_object($this -> OffreSpeciale))
			return $this -> OffreSpeciale;
		if (!$this -> Id)
			return false;
		$Offr = $this -> storproc('Boutique/CodePromo/Commande/' . $this -> Id);
		// ici rajout du test d'existende de $Offr
		if (isset($Offr[0])) {
			$this -> OffreSpeciale = genericClass::createInstance('Boutique', $Offr[0]);
			return $this -> OffreSpeciale;
		}
		return false;
	}

	/*------------Définition de CodePromo  ---*/
	public function setCodePromo($CodePromo) {
		//on vérifie que la commande en cours n'est pas dans un état valide sinon il faut d'abord la dévalider
		if ($this -> Valide) {
			$this -> Valide = 0;
		}
		//verifier que le code promo n'est pas propre au client
		$codeProm = $this -> storproc('Boutique/CodePromo/Code=' . $CodePromo);
		$this -> CodePromo = genericClass::createInstance('Boutique', $codeProm[0]);
		$this -> recalculer();
	}

	public function getCodePromo() {
		if (isset($this -> CodePromo)) {
			return $this -> CodePromo;
		}
		return false;
	}
	/**
	 * setPending
	 * Definit la commande en attente de paiement
	 */
	public function setPending() {
		//definition du paiement en attente
		$this->PaymentPending = true;
		//suppression de l'état de panier courant
		$this->Current = false;
		$this->Save();
	}

	/**
	 * Relai l'initialisation du Bon de livraison
	 * @param  	object  Adresse KE (Livraison)
	 * @param  	object  TypeLivraison KE
	 * @param	object	TarifLivraison KE
	 * @param	object	ZoneLivraison KE
	 * @param	string	Choix complémentaire pour la livraison (unique id)
	 * @return	void
	 */
	public function setBonLivraison($AdresseLivraison, $TypeLivraison, $TarifLivraison, $ZoneLivraison, $ChoixLivraison, $TxTvaLivr) {
		//on vérifie que la commande en cours n'est pas dans un état valide sinon il faut d'abord la dévalider
		if ($this -> Valide) {
			$this -> Valide = 0;
		}
		// Si on a pas encore de champs pour les stocker on le créé
		if ($this -> BonLivraison == NULL)
			$this -> BonLivraison = genericClass::createInstance('LivraisonStock', 'BonLivraison');
		$this -> BonLivraison -> InitBL($this, $AdresseLivraison, $TypeLivraison, $TarifLivraison, $ZoneLivraison, $ChoixLivraison);

	}

	public function getBonLivraison() {
		if (isset($this -> BonLivraison) && is_object($this -> BonLivraison))
			return $this -> BonLivraison;
		if (!$this -> Id)
			return false;
		$bl = $this -> storproc('Boutique/Commande/' . $this -> Id . '/BonLivraison');
		if (is_array($bl) && sizeof($bl) > 0) {
			$this -> BonLivraison = genericClass::createInstance('Boutique', $bl[0]);
			return $this -> BonLivraison;
		}
		return false;
	}

	/**
	 * Met à jour des informations complémentaires dans le BL une fois le paiement confirmé
	 * @return void
	 */
	public function updateInfosLivraison() {
		$bonLivraison = $this -> getBonLivraison();
		$bonLivraison -> updateInfosLivraison();
	}

	/**
	 * Recuperation du paiement
	 * @return	KE object
	 */
	public function getPaiement() {
		$p = $this -> storproc('Boutique/Commande/' . $this -> Id . '/Paiement', false, 0, 1, 'DESC', 'Id');
		if (is_array($p) && sizeof($p) > 0) {
			$this->Paiement = genericClass::createInstance('Boutique', $p[0]);
			return $this->Paiement;
		}
	}

	/**
	 * Définition de la facture
	 * @Param
	 * @return	void
	 */
	public function setFacture() {
		// Si on a pas encore de champs pour les stocker on le créé
		if ($this -> Facture == NULL)
			$this -> Facture = genericClass::createInstance('Boutique', 'Facture');
		$this -> Facture -> InitFromCde($this);

	}

	public function oldgetFacture() {
		if (isset($this -> Facture) && is_object($this -> Facture))
			return $this -> Facture;
		if (!isset($this -> Id) || empty($this -> Id))
			return false;
		$fac = $this -> storproc('Boutique/Facture/' . $this -> Id);
		$this -> Facture = genericClass::createInstance('Boutique', $fac[0]);
		return $this -> Facture;
	}

	public function getFacture() {
		if (isset($this -> Facture) && is_object($this -> Facture))
			return $this -> Facture;
		if (!isset($this -> Id) || empty($this -> Id))
			return false;
		$fac = $this -> getChildren('Facture');

		if (is_array($fac)&&isset($fac[0])) {
			$this->Facture = $fac[0];
			return $this->Facture;
		}
//		if (is_array($fac) && is_object($fac[0]))
//			$this->Facture = $fac[0];
		//return $this->Facture;
	}

	/**
	 * Définition du client
	 * @return	void
	 */
	public function setClient($Client) {
		$cli = $this -> storproc('Boutique/Client/' . $Client);
		$this -> Client = genericClass::createInstance('Boutique', $cli[0]);

	}

	/**
	 * Définition du magasin
	 * @return	void
	 */
	public function setMagasin($Magasin = 1) {
		$this -> Magasin = Magasin::getCurrentMagasin();

	}

	/**
	 * Définition du magasin
	 * @return	void
	 */
	public function getMagasin() {
		$Mag = $this -> storproc('Boutique/Magasin/Commande/' . $this -> Id);
		if (is_array($Mag)&&sizeof($Mag)){
			$this->Magasin = genericClass::createInstance('Boutique', $Mag[0]);
			return $this->Magasin;
		}
	}

	/**
	 * Création du tableau de stockage des infos pour codePromo
	 * @return	array
	 */
	function getReductionCodePromo($CodeP, $ClientId) {
		$reduc_arr = Array('Desc' => '', 'Ok' => false, 'PortOffert' => false, 'Message' => '', 'Montant' => 0);
		$reduc = false;
		// on va chercher le client de la commande en cours
		//$cli=$this->getClient();
		// on recherche le code promotion en vérifiant la disponibilité
		$Prom = $this -> storproc('Boutique/CodePromo/Code=' . $CodeP);
		//Gestion des erreurs
		$Message = "";
        if (!isset($Prom[0])) $Message = "Code Promotion introuvable.";
        else {
            $PromCo = genericClass::createInstance('Boutique', $Prom[0]);
            if (!$PromCo->Actif || !sizeof($Prom))
                $Message = "Ce code promotion n'existe pas.";
            if ($PromCo->DateDebut > time())
                $Message = "Ce code promotion n'est pas encore actif.";
            if ($PromCo->DateFin < time())
                $Message = "Ce code promotion a expiré.";
            if ($PromCo->Quantite == 0 && $PromCo->GestionQuantite == 0)
                $Message = "Ce code promotion a déjà été utilisé.";
        }
		if (!empty($Message))
			return Array('Desc' => '', 'Ok' => false, 'PortOffert' => false, 'Message' => $Message, 'Montant' => 0);
		//Construction du coupon
		if (is_object($PromCo)) {
			// est ce que ce code promo est dépendant d'un minimum d'achat
			if ($PromCo -> MiniAchat > 0) {
				if ($this -> MontantTTC >= $PromCo -> MiniAchat)
					$reduc = true;
				else
					$reduc_arr['Message'] = "Le montant minimum d'achat n'a pas été atteint (" . $PromCo -> MiniAchat . " € minimum)";
			} else {
				$reduc = true;
			}
			if ($reduc) {
				$reduc = false;
				// est ce que ce code promo est limité à certains clients
				$CliProm = $this -> storproc('Boutique/CodePromo/' . $PromCo -> Id . '/Client');
				if (is_array($CliProm)&&sizeof($CliProm)) {
					// ce code promo est lié à des clients
					foreach ($CliProm as $cP) :
						if ($ClientId == $cP['Id'])
							$reduc = true;
					endforeach;
				} else {
					// c'est un code Tout client
					$reduc = true;
				}
			}
		}
		if ($reduc) {
			// c'est bon on peut utiliser la réduction de ce code promo
			$reduc_arr['Desc'] = $PromCo -> Nom;
			$reduc_arr['Ok'] = true;
			$reduc_arr['PortOffert'] = false;
			// Pourcentage de la commande
			if ($PromCo -> TypeVariation == 1) {
				$reduc_arr['Montant'] = ($this -> MontantTTC * $PromCo -> Variation) / 100;
			}

			// !!!! voir comment faire pour paiement par bon d'achat
			// Montant fixe
			if ($PromCo -> TypeVariation == 2) {
				if ($this -> MontantTTC >= $PromCo -> Variation) {
					$reduc_arr['Montant'] = number_format((double)$PromCo -> Variation, 2, '.', '');
				} else {
					$reduc_arr['Message'] = "La remise ne peut pas être supérieure au montant de la commande !";
					$reduc_arr['Montant'] = 0;
					$reduc_arr['Ok'] = false;
				}
			}
			// frais de port offert
			if ($PromCo -> TypeVariation == 3) {
				$reduc_arr['PortOffert'] = true;
				$reduc_arr['Montant'] = 0;
			}
		}
		return $reduc_arr;
	}

	/**
	 * Création du tableau de stockage des montants
	 * @return	void
	 */
	function getTableTva($o=null) {
        if (!$o)$o=$this;

        $arr = array(
            1 => array(
                'HT' => 0,
                'TTC' => 0
            ),
            2 => array(
                'HT' => 0,
                'TTC' => 0
            )
        );
        if (isset($this -> LignesCommandes) && is_array($this -> LignesCommandes))
            foreach ($this->LignesCommandes as $k=>$LC) {
                $tabletva = $LC->getTableTva();
                if (isset($tabletva['T20']))
                    $arr[1]['HT'] += $tabletva['T20']['Base'];
                if (isset($tabletva['T5.5']))
                    $arr[2]['HT'] += $tabletva['T5.5']['Base'];

                if(isset($tabletva['T20']['Taux']))
                    $o->TxTva1 = $tabletva['T20']['Taux'];
                if(isset($tabletva['T5.5']['Taux']))
                    $o->TxTva2 = $tabletva['T5.5']['Taux'];
            }
        //stockage du tableau de tva
        $o->tableTva = $arr;

        $liv = $this->getChildren('BonLivraison');

        if (sizeof($liv)&&$liv[0]->MontantLivraisonHT!=0) {

            $o->HtLivr= $liv[0]->MontantLivraisonHT;
            $o->TTCLiv= round($o->HtLivr * (1+($o->TxTva1  / 100)), 2);
            $o->MtTvaLiv =round($o->HtLivr * ($o->TxTva1  / 100), 2);
            $o->TxTvaLiv = $liv[0]->TxTvaBonLivr;
        }

        //$this->TxTva1 = 20;
        //$this->TxTva2 = 5.5;

        $o->BaseHTTx1= round($arr[1]['HT'],2) ;
        $o->MtTva1 =round($o->BaseHTTx1 * ($o->TxTva1 / 100), 2);
        $o->TTC1= $o->BaseHTTx1 + $o->MtTva1;

        $o->BaseHTTx2= round($arr[2]['HT'],2) ;
        $o->MtTva2 =round($o->BaseHTTx2 * ($o->TxTva2 / 100), 2);
        $o->TTC2= $o->BaseHTTx2+ $o->MtTva2;

	}

	/**
	 * Permet au KEML de récupérer ce tableau
	 * @return	Tableau
	 */
	function getTableTvaFacture() {

		return $this -> tabletva;
	}

	/**
	 * Permet au KEML de réaliser cette opération
	 * @return	TVA uniquement
	 */
	function getTVA($M, $tauxTva) {
		return $M * $tauxTva / 100;
	}

	/**
	 * Création de la facture
	 * @Param
	 * @return	void
	 */
	public function GenereFacture() {
		$Facture = genericClass::createInstance('Boutique', 'Facture');
		$Facture -> InitFromCde($this);
		$Facture -> Save();
	}

	/*************************************************** ENREGISTREMENT COMMANDE ****************************************************/

	public function getLignesCommande() {
		if (isset($this -> LignesCommandes) && !empty($this -> LignesCommandes))
			return $this -> LignesCommandes;
		else $this -> LignesCommandes = Array();
		if ($this -> Id) {
			$lignes = $this -> storproc('Boutique/Commande/' . $this -> Id . '/LigneCommande',false,'','','ASC','Id');
			$tabl = array();
			foreach ($lignes as $l) :
				$tabl[] = genericClass::createInstance('Boutique', $l);
			endforeach;
			$this -> LignesCommandes = $tabl;
			return $tabl;
		} else {
			if (isset($this -> LignesCommandes) && !empty($this -> LignesCommandes))
				return $this -> LignesCommandes;
			else
				return false;
		}
	}

	/**
	 * Récupération de la derniere ligne commande
	*/
	public function getLastOrderLine() {
		$this->getLignesCommande();
		if (sizeof($this->LignesCommandes))
			return end($this->LignesCommandes);
	}
	/**
	 * setValid
	 * Valide la commande
	 */
	public function setValid() {
        $li = $this->getLignesCommande();
        if (sizeof($li)) {
            $Mag = Magasin::getCurrentMagasin();
            $this->Valide = true;
            if (!$Mag->EtapePaiement) {
                $this->Current = 0;
            }
            $this->Save();
        }else{
            this.addError(Array(
                "Champ" => "None",
                "Message" => "Impossible de valider une commande vide"
            ));
        }
    }

    /**
     * setExpedie
     * Expedie la commande
     */
    public function setExpedie() {
        $Mag= Magasin::getCurrentMagasin();
        $this -> Expedie = true;
        $this -> ExpedieLe = time();
        $this -> Save();

        $this -> sendMailAcheteur();
    }

    /**
     * setPrepare
     * Prepare la commande
     */
    public function setPrepare() {
        $Mag= Magasin::getCurrentMagasin();
        $this -> Prepare = true;
        $this -> PrepareLe = time();
        $this -> Save();

        $this -> sendMailAcheteur();

    }
    /**
     * setCloture
     * Cloture la commande
     */
    public function setCloture() {
        $Mag= Magasin::getCurrentMagasin();
        $this -> Cloture = true;
        $this -> ClotureLe = time();
        $this -> Save();

    }



    /**
	 * applyCommande
	 * Applique la commande
	 * Destocke les produits
	 */
	public function applyCommande() {
		if (!isset($this -> LignesCommandes))
			$this -> getLignesCommande();
		if (!sizeof($this -> LignesCommandes))
			return false;
		foreach ($this->LignesCommandes as $l) :
			$r = $this -> storproc('Boutique/Reference/LigneCommande/' . $l -> Id);
			$ref = genericClass::createInstance('Boutique', $r[0]);
			$ref -> decrementeStock($l -> Quantite);
		endforeach;
		// AJOUTER LA MISE A JOUR DES CODES PROMOS UTILISES
		if (isset($this -> CodePromo)) {
			$this -> CodePromo -> Quantite++;
			$this -> CodePromo -> Save();
		}
		if (isset($this -> OffreSpeciale)) {
			$this -> OffreSpeciale -> Quantite++;
			$this -> OffreSpeciale -> Save();
		}

	}

	/**
	 * discardCommande
	 * Annule la commande
	 * restockes les produits
	 */
	public function discardCommande() {
		if (!isset($this -> LignesCommandes))
			$this -> getLignesCommande();
		if (!sizeof($this -> LignesCommandes))
			return false;
		if (is_array($this -> LignesCommandes))
			foreach ($this->LignesCommandes as $l) :
				$r = $this -> storproc('Boutique/Reference/LigneCommande/' . $l -> Id);
				$ref = genericClass::createInstance('Boutique', $r[0]);
				if (!is_object($ref))
					continue;
				$ref -> incrementeStock($l -> Quantite);
			endforeach
		;
		if (isset($this -> BonLivraison) && is_object($this -> BonLivraison) && $this->getMontantLivrable()>0) {
			$this -> BonLivraison->Delete();
		}

		$this -> Valide = 0;
	}

	/**
	 * setUnValid
	 * Devalide la commande
	 */
	public function setUnValid() {
		$this -> Valide = false;
		$this -> Save();
	}

	/**
	 * Verifie la disponibilité d'une commande
	 */
	public function estDisponible() {
		$lignes = $this -> getLignesCommande();
		if (!sizeof($lignes))
			return false;
		if (is_array($lignes))
			foreach ($lignes as $l) :
				$r = $this -> storproc('Boutique/Reference/LigneCommande/' . $l -> Id);
				$ref = genericClass::createInstance('Boutique', $r[0]);
				if (!$ref -> estDisponible())
					return false;
			endforeach
		;
		return true;
	}

	public function getSiteMagasin() {
		if(!isset($this->Magasin)) {
			$this-> getMagasin();
		}
		if(!isset($this->Site)) {
            $this->Site = Site::getCurrentSite();
		}
		return $this->Site;
	}

	public function getTypePaiement() {
		if(!isset($this->Paiement)) {
			$this-> getPaiement();
		}
		if(!isset($this->TypePaiement)) {
			//var_dump($this->Paiement);
			$typeP  = $this->storproc('Boutique/TypePaiement/Paiement/' . $this->Paiement->Id);
			if(is_array($typeP) && sizeof($typeP)>0) $this->TypePaiement = genericClass::createInstance('Boutique',$typeP[0]);
		}
		return $this->TypePaiement;
	}


	/**
	 * Envoi du mail a l'acheteur l'informant que son achat a été prise en compte
	 * Param  magasin string
	 */
	private function sendMailAcheteur() {
		$this -> getClient();
		$this -> getBonLivraison();
		$this->getSiteMagasin();
		$this->getTypePaiement();
		$this->getMagasin();


		$Civilite = $this -> Client -> Civilite . " " . $this -> Client -> Prenom . ' <span style="text-transform:uppercase">' . $this -> Client -> Nom . '</span>';
		$CiviliteLiv = $this -> AdrLiv -> Civilite . " " . $this -> AdrLiv -> Prenom . ' <span style="text-transform:uppercase">' . $this -> AdrLiv -> Nom . '</span>';

		$Lacommande = "";
        $this->getLignesCommande();
        if (!sizeof($this->LignesCommandes)) {
            $Lacommande = "Erreur";
        } else {
            $Lacommande = "<br /><br /><h2>Récapitulatif de votre commande  : </h2><br /><br /><table width='100%'>";
            $Lacommande .= "<tr bgcolor='#B1599e' padding='5'><td></td><td><font color='#ffffff'>Quantite</font></td><td><font color='#ffffff'>Titre</font></td><td><font color='#ffffff'>Tarif TTC</font></td></tr>";
            foreach ($this->LignesCommandes as $l) :
                //récupération du produit
                $r = $l->getParents('Reference');
                $p = $r[0]->getParents('Produit');
                $Lacommande .= "<tr height='200'><td><img src='http://" .Sys::$domain.'/'. $p[0]->Image . ".limit.200x200.jpg' /></td>";
                $Lacommande .= "<td><h3>" . $l->Quantite . "</h3> </td>";
                $Lacommande .= "<td><h3>" . $l->Titre . "</h3></td>";
                $Lacommande .= "<td><h2>" . $l->MontantTTC . " € TTC</h2></td></tr>";
            endforeach;
            $Lacommande .= "
                <tr bgcolor='#B1599e' padding='5'>
                    <td colspan='2'></td>
                    <td><font color='#ffffff'>TOTAL</font></td>
                    <td><font color='#ffffff'><h2>$this->MontantTTC € TTC</h2></font></td>
                </tr>
            </table>";
        }

		/*if (isset($this -> BonLivraison)&&is_object($this -> BonLivraison)&&$this -> BonLivraison -> AdresseLivraisonAlternative) {
			$AdressLiv = "<br />Pour " . $CiviliteLiv . " à <br /> " . $this -> BonLivraison -> ChoixLivraison . "<br /> ";
		} else {
			$this -> getAdresseLivraison();
			$AdressLiv = "<br />" . $CiviliteLiv . "<br />" . $this -> AdrLiv -> Adresse . " <br /> " . $this -> AdrLiv -> CodePostal . "  " . $this -> AdrLiv -> Ville . " " . $this -> AdrLiv -> Pays;
		}*/
		require_once ("Class/Lib/Mail.class.php");
		$Mail = new Mail();
        if ($this->Valide&&!$this->Prepare&&!$this->Expedie&&!$this->Cloture) {
            $Mail->Subject("Confirmation de commande sur " . $this->Magasin->Nom);
        }elseif ($this->Valide&&$this->Prepare&&!$this->Expedie&&!$this->Cloture) {
            $Mail->Subject("Confirmation de preparation de commande  " . $this->Magasin->Nom);
        }elseif ($this->Valide&&$this->Prepare&&$this->Expedie&&!$this->Cloture) {
            $Mail->Subject("Confirmation de retrait de commande  " . $this->Magasin->Nom);
        }
		//$Mail -> From($GLOBALS['Systeme'] -> Conf -> get('MODULE::SYSTEME::CONTACT'));
		$Mail -> From( $this -> Magasin ->EmailContact );
//		$Mail -> ReplyTo($GLOBALS['Systeme'] -> Conf -> get('MODULE::SYSTEME::CONTACT'));
		$Mail -> ReplyTo($this -> Magasin ->EmailContact);
		$Mail -> To($this -> Client -> Mail);
		$Mail -> Bcc($this -> Magasin ->EmailContact);
	//	$Mail -> Bcc($GLOBALS['Systeme'] -> Conf -> get('MODULE::SYSTEME::CONTACTALPHA'));
		$bloc = new Bloc();
        if ($this->Valide&&!$this->Prepare&&!$this->Expedie&&!$this->Cloture) {
            $mailContent = "
                Bonjour " . $Civilite . ",<br /><br />
                Nous vous informons que votre commande N° " . $this->RefCommande . " a bien été prise en compte.<br />
                Vous pouvez d'ores et déjà vous rendre sur <a style='text-decoration:underline' href='" . $this->Site->Domaine . "/" . $GLOBALS['Systeme']->getMenu('Boutique/Mon-compte') . "'>votre espace client</a> et suivre l'évolution de votre commande.<br /><br />
                <br />Toute l'équipe de " . $this->Magasin->Nom . " vous remercie de votre confiance,<br />
                <br />Pour nous contacter : " . $this->Magasin->EmailContact . " .".$Lacommande;
        }elseif ($this->Valide&&$this->Prepare&&!$this->Expedie&&!$this->Cloture) {
            $mailContent = "
                Bonjour " . $Civilite . ",<br /><br />
                Nous vous informons que votre commande N° " . $this->RefCommande . " a été préparée.<br />
                Vous pouvez d'ores et déjà vous rendre à l'officine ".$this->Magasin->Nom." pour retirer et payer votre commande <br /><br />
                Toute l'équipe de " . $this->Magasin->Nom . " vous remercie de votre confiance,<br />
                <br />Pour nous contacter : " . $this->Magasin->EmailContact . ". ".$Lacommande;
        }elseif ($this->Valide&&$this->Prepare&&$this->Expedie&&!$this->Cloture) {
            $mailContent = "
                Bonjour " . $Civilite . ",<br /><br />
                Vous avez retiré votre commande N° " . $this->RefCommande . ".
                Toute l'équipe de " . $this->Magasin->Nom . " vous remercie de votre confiance,<br />
                <br />Pour nous contacter : " . $this->Magasin->EmailContact . " .".$Lacommande;
        }

        $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
		$Pr = new Process();
		$bloc -> init($Pr);
		$bloc -> generate($Pr);
		$Mail -> Body($bloc -> Affich());
        if (!$this->Cloture)
		    $Mail -> Send();
	}


//---------------------------------------- ------------------------------------------------------//
//-------------------------- DÉPLACER DANS PLUGIN PAIEMENT PAR CHÈQUE --------------------------//
//---------------------------- ----------------------------------------------------------------//
// 	public function sendMailAcheteurAttentePaiement() {
// 		$this -> getClient();
// 		$this -> getBonLivraison();
// 		$this -> getMagasin();
// 		$this -> getAdresseLivraison();
// 		$this->getSiteMagasin();
// 		$this->getTypePaiement();
// 		
// 		$Civilite = $this -> Client -> Civilite . " " . $this -> Client -> Prenom . ' <span style="text-transform:uppercase">' . $this -> Client -> Nom . '</span>';
// 		$Lacommande = "";
// 		$this -> getLignesCommande();
// 		if (!sizeof($this -> LignesCommandes)) {
// 			$Lacommande = "";
// 		} else {
// 			$Lacommande = "<br /><br />Recapitulatif de votre commande  : <br /><br /><table style=\"font-family: arial,helvetica,sans-serif; font-size: 10pt; color: rgb(0, 0, 0);\">";
// 			foreach ($this->LignesCommandes as $l) :
// 				$Lacommande .= "<tr><td>" . $l -> Quantite . "</td>";
// 				$Lacommande .= "<td>" . $l -> Titre . "</td></tr>";
// 			endforeach;
// 			$Lacommande .= "</table>";
// 		}
// 		if ($this -> BonLivraison -> AdresseLivraisonAlternative) {
// 			$AdressLiv = "<br />Pour " . $Civilite . " à <br /> " . $this -> BonLivraison -> ChoixLivraison . "<br /> ";
// 		} else {
// 			$AdressLiv = "<br />" . $Civilite . "<br />" . $this -> AdrLiv -> Adresse . " <br /> " . $this -> AdrLiv -> CodePostal . "  " . $this -> AdrLiv -> Ville . " " . $this -> AdrLiv -> Pays;
// 		}
// 		require_once ("Class/Lib/Mail.class.php");
// 		$Mail = new Mail();
// 		$Mail -> Subject("Confirmation achat sur " . $this -> Magasin -> Nom);
// 		$Mail -> From( $this -> Magasin ->EmailContact );
// // 		$Mail -> From($GLOBALS['Systeme'] -> Conf -> get('MODULE::SYSTEME::CONTACT'));
// 		$Mail -> ReplyTo($this -> Magasin ->EmailContact);
// // 		$Mail -> ReplyTo($GLOBALS['Systeme'] -> Conf -> get('MODULE::SYSTEME::CONTACT'));
// 		$Mail -> To($this -> Client -> Mail);
// 		$Mail -> Bcc( $this -> Magasin ->EmailContact );
// // 		$Mail -> Bcc($GLOBALS['Systeme'] -> Conf -> get('MODULE::SYSTEME::CONTACT'));
// 		$Mail -> Bcc($GLOBALS['Systeme'] -> Conf -> get('MODULE::SYSTEME::CONTACTALPHA'));
// 		$bloc = new Bloc();
// 		$mailContent = "
// 			Bonjour " . $Civilite . ",<br /><br />
// 			Nous vous informons que votre commande N° " . $this -> RefCommande . " a bien été prise en compte.<br />
// 			Votre commande vous sera livrée dès réception de votre paiment " . $this->TypePaiement->Nom . ".<br />
// 			Vous pouvez d'ores et déjà vous rendre sur <a style='text-decoration:underline' href='" . $this->Site->Domaine . "/" . $GLOBALS['Systeme'] -> getMenu('Systeme/User') . "' > votre espace client </a> et suivre l'évolution de votre commande.<br /><br />
// 			Adresse de livraison de votre commande " . $AdressLiv . "<br /><br /> " . $Lacommande . "<br /><br />
// 			Toute l'équipe de " . $this -> Magasin -> Nom . " vous remercie de votre confiance,<br />
// 			<br />Pour nous contacter : " .  $this -> Magasin -> EmailContact  . " .";
// 		$bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
// 		$Pr = new Process();
// 		$bloc -> init($Pr);
// 		$bloc -> generate($Pr);
// 		$Mail -> Body($bloc -> Affich());
// 		$Mail -> Send();
// 	}

	//----------------------------------------------------------//
	//		ERREURS	SURCHARGE POUR LA GESTION EN SESSION
	//----------------------------------------------------------//
	/**
	 * addError
	 * Add an error in the error array
	 * @param Array Error
	 */
	public function addError($err) {
		$this -> Error[] = $err;
		$GLOBALS["Systeme"] -> Connection -> addSessionVar('KEMessageError', $this -> Error);
	}

	/**
	 * getErrors
	 * Add an error in the error array
	 * @param Array Error
	 */
	//public function Error() {return $this->getErrors();}
	public function getErrors() {
		if (isset($_SESSION['KEMessageError'])) {
			//récupération des erreurs dans le cookie
			$this -> Error = @unserialize($_SESSION['KEMessageError']);
		}
		return $this -> Error;
	}

	/**
	 * resetErrors
	 * reset All errors
	 */
	public function resetErrors() {
		$this -> Error = Array();
		$GLOBALS["Systeme"] -> Connection -> addSessionVar('KEMessageError', $this -> Error);
	}

	/**
	 * addSuccess
	 * Add an success message in the success array
	 * @param Array Success
	 */
	public function addSuccess($succ) {
		$this -> Success[] = $succ;
		$GLOBALS["Systeme"] -> Connection -> addSessionVar('KEMessageSuccess', $this -> Success);
	}

	/**
	 * getSuccess
	 * get all success messages
	 * @param Array Success
	 */
	//public function Success() {return $this->getSuccess();}
	public function getSuccess() {
		if (isset($_SESSION['KEMessageSuccess'])) {
			//récupération des erreurs dans le cookie
			$this -> Success = @unserialize($_SESSION['KEMessageSuccess']);
		}
		return $this -> Success;
	}

	/**
	 * resetSuccess
	 * reset All success
	 */
	public function resetSuccess() {
		$this -> Success = Array();
		$GLOBALS["Systeme"] -> Connection -> addSessionVar('KEMessageSuccess', $this -> Success);
	}

	/**
	 * detectionPack
	 * Detection des packs disponibles par rapport au contenu du panier
	 */
	public function detectionPacks() {
		$Packs = array();
		$Cpks = array();
		//Pour chaque ligne du panier, on récupère la référence et on vérifie si cette dernière ne constitue pas un pack
		$lc = $this->getLignesCommande();
		if (is_array($lc))foreach ($lc as $l){
//			echo "traitement ligne commande ".$l->Titre."\r\n";
			$ref = $l->getReference();
			$cps = $ref->getParents('ConfigPack');
			foreach ($cps as $cp){
//				if ($cp->Detection){
//					echo " - ConfigPack ".$cp->Nom." cp:".$cp->Id."\r\n";
					$Cpks[$cp->Id] = $cp;
					$prods=$cp->getParents('Produit');
					foreach ($prods as $pr){
//						echo " - -  Produit ".$pr->Nom." pr:".$pr->Id."\r\n";
						if (!isset($Packs[$pr->Id]))
							$Packs[$pr->Id] = $pr;
						else
							$pr = $Packs[$pr->Id];
						//Ajout de la ligne du panier qui permet la détection
						if (!isset($pr->LignesDetection)) $pr->LignesDetection = array();
						//vérification que cette lignecommande n'est pas déjà affectée
						$already=false;
						foreach ($pr->LignesDetection as $ld){
							if ($ld===$l)$already=true;
//							echo " - - - -  Test ligne ".$ld->Titre." cp:".$cp->Id." present:".($ld===$l)."\r\n";
						}
						if (!$already&&!isset($pr->LignesDetection[$cp->Id])){
							$pr->LignesDetection[$cp->Id] = $l;
//							echo " - - -  Ajout ligne ".$l->Titre." cp:".$cp->Id." nblignes:".sizeof($pr->LignesDetection)."\r\n";
						}
					}
//				}
			}
		}
		//Pour tout les configpacks sélectionnés on vérifie que les conditions de detection du pack soient remplis
		foreach ($Packs as $pa){
			$cps = $pa->getChildren('ConfigPack/Detection=1');
			$detect = array();
//			echo "Detection Pack ".$pa->Nom." NbConfigPack:".sizeof($cps)."\r\n";
			foreach ($cps as $cp){
				if (isset($pa->LignesDetection[$cp->Id])&&is_object($pa->LignesDetection[$cp->Id])&&!isset($detect[$cp->Id])){
//					echo " - Pack détecté ".$cp->Nom." | \r\n";
					$detect[$cp->Id]=$cp;
				}
			}
			//si pas toutes les conditions, suppression du pack
			if (sizeof($cps)!=sizeof($detect)||sizeof($cps)==0) unset($Packs[$pa->Id]);
		}
		return $Packs;
	}
	/**
	 * replacePackFromBasket
	 * Suppresion des lignes du panier constituant le pack passé en paramètre
	 * @param Int Id du pack
	 * @return String Url Produit
	 */
	public function replacePackFromBasket($pack){
		if (!(intval($pack)>0))return false;
		$p = genericClass::createInstance('Boutique','Produit');
		$p->initFromId($pack);
		$lc = $this->getLignesCommande();
		$config = array();
		$lignes = array();
		foreach ($lc as $l){
			$ref=$l->getReference();
//			echo "traitement ligne commande ".$l->Titre."\r\n";
			$cps = $ref->getParents('ConfigPack');
			foreach ($cps as $cp){
				$Cpks[$cp->Id] = $cp;
				$prods=$cp->getParents('Produit');
				foreach ($prods as $pr){
					if ($pr->Id==$p->Id){
						//vérification que cette lignecommande n'est pas déjà affectée
						$already=false;
						foreach ($lignes as $ld){
							if ($ld===$l)$already=true;
						}
						if (!$already&&!isset($lignes[$cp->Id])){
							$lignes[$cp->Id] = $l;
						}
					}
				}
			}
		}

		//on applique le tout
		foreach ($lignes as $cp=>$ld){
			$ref=$ld->getReference();
			$this->enleverLigneCommande($ld->Reference);
			$config[$cp] = $ref->Id;
		}
		
		//Sauvegarde du panier
		if (!Sys::$User->Public){
			$this->Save();
		}else {
			$GLOBALS["Systeme"]->Connection->addSessionVar('KEBoutiquePanier', $this);
		}

		//génération de l'url
		$Url='';
		foreach ($config as $k=>$c){
			if (!empty($Url)) $Url.="&";
			$Url.='packconfig['.$k.']='.$c;
		}
		$Url = $p->getUrl().'?'.$Url;
		return $Url;
	}
	
	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
	private function storproc($Query, $recurs = '', $Ofst = '', $Limit = '', $OrderType = '', $OrderVar = '', $Selection = '', $GroupBy = '') {
		return Sys::$Modules['Boutique'] -> callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}


	/**
	 * PGF 
	 *
	 * Liste pour appaloosa
	 * @return	Résultat de la requete
	 */
	function GetCommandeList($id, $offset, $limit, $sortfld, $order, $filter) {
		$etat = array('En Cours','Acceptée','Refusée','Initialisé','Attente');
		$data = array();
		$req = 'Commande:CommandeList';
		if($id) $req .= "/$id";
		if($filter) $req .= "/$filter";
		$rec = Sys::$Modules['Boutique']->callData($req,false,0,0,'','','COUNT(DISTINCT(m.Id))');
		$numRows = $rec[0]['COUNT(DISTINCT(m.Id))'];
		$rec = Sys::$Modules['Boutique']->callData($req,false,$offset,$limit,$order,$sortfld);
		foreach($rec as $r) {
			$id = $r['Id'];
			$d = array('Id'=>$id,'RefCommande'=>$r['RefCommande'],'Client1.Nom'=>$r['Client1.Nom'],'Client1.Prenom'=>$r['Client1.Prenom'],
						'ExpedieLe'=>$r['ExpedieLe'],'PayeLe'=>$r['PayeLe'],'Magasin2.Code'=>$r['Magasin2.Code'],'MontantPaye'=>$r['MontantPaye'],
						'DateCommande'=>$r['DateCommande'],'Facture3.NumFac'=>$r['Facture3.NumFac'],'TypeLivraison5.Nom'=>$r['TypeLivraison5.Nom'],
						'BonLivraison4.NumBL'=>$r['BonLivraison4.NumBL'],'BonLivraison4.NumColis'=>$r['BonLivraison4.NumColis']);
/*
			$d['Facture'] = '';
			$fac = Sys::$Modules['Boutique']->callData("Commande/$id/Facture",false,0,0,'DESC','Id');
			foreach($fac as $f) {
				$d['Facture'] = $f['NumFac'];
				break;
			}
			if(count($fac) > 1) {$d['Facture_backgroundColor'] = '0xffff00'; $d['Facture_TooTip'] = count($fac).' factures';}

			$d['ModeLivraison'] = '';
			$bon = Sys::$Modules['LivraisonStock']->callData("Commande/$id/BonLivraison",false,0,0,'DESC','Id');
			foreach($bon as $b) {
				$tpy = Sys::$Modules['LivraisonStock']->callData("TypeLivraison/BonLivraison/".$b['Id'],false,0,1);
				$d['ModeLivraison'] = $tpy[0]['Nom'];
				break;
			}
			if(count($bon) > 1) {$d['ModeLivraison_backgroundColor'] = '0xffff00'; $d['ModeLivraison_TooTip'] = count($pay).' livraisons';}
*/
			$d['TypePaiement'] = '';
			$d['EtatPaiement'] = '';
			$etatP = -1;
			$pay = Sys::$Modules['Boutique']->callData("Commande/$id/Paiement",false,0,0,'DESC','Id');
			foreach($pay as $p) {
				if($p['Etat'] == 1) {
					$etatP = $p['Etat'];
					$tpy = Sys::$Modules['Boutique']->callData("TypePaiement/Paiement/".$p['Id'],false,0,1);
					$d['TypePaiement'] = $tpy[0]['Nom'];
					$d['EtatPaiement'] = $etat[$etatP];
					break;
				}
			}
			if($etatP == -1) {
				foreach($pay as $p) {
					$etatP = $p['Etat'];
					$tpy = Sys::$Modules['Boutique']->callData("TypePaiement/Paiement/".$p['Id'],false,0,1);
					$d['TypePaiement'] = $tpy[0]['Nom'];
					$d['EtatPaiement'] = $etat[$etatP];
					break;
				}
			}
			if(count($pay) > 1) {$d['EtatPaiement_backgroundColor'] = '0xffff00'; $d['EtatPaiement_TooTip'] = count($pay).' paiements';}

			$d['Statut_backgroundColor'] = '0x08d8ff';
			$d['Statut'] = 'Nouvelle';
			if($r['Cloture']) {$d['Statut_backgroundColor'] = '0xb8b8b8'; $d['Statut'] = 'Cloturé';}
			else if($r['Expedie']) {$d['Statut_backgroundColor'] = '0x00ff7e'; $d['Statut'] = 'Expédiée';}
			else if($r['Paye']) {$d['Statut_backgroundColor'] = '0xff3636'; $d['Statut'] = 'Préparation';}
			else if($r['Valide'] && $etatP == 0) {$d['Statut_backgroundColor'] = '0xffb636'; $d['Statut'] = 'En cours';}
			else if($etatP == 2) {$d['Statut_backgroundColor'] = '0xb8b8b8'; $d['Statut'] = 'Refusée';}
			$data[] = $d;
		}
		$c = count($data);
		return WebService::WSData('',$offset,$c,$numRows,$req,'','','','',$data);
	}
	 
	/**
	 * PGF 
	 *
	 * impression commandes
	 * @return	status
	 */
	function PrintCommandes($ids) {
		$files = array();
		foreach($ids as $id) $files[] = "Boutique/Commande/$id/BonDeCommande.pdf";
		$res = array('printFiles'=> $files);
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}

	/**
	 * PGF 
	 *
	 * impression factures
	 * @return	status
	 */
	function PrintFactures($ids) {

		/*if ($_SERVER['REMOTE_ADDR']=='178.22.145.106') {

			foreach($ids as $id) {
				$liste.=$id."§";
			}
			$files = array();
			$files[] = "Boutique/Facture/FacturePdfMulti?quoi='".$liste."'";
			$res = array('printFiles'=> $files);
			return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);

		}*/

		$files = array();
		foreach($ids as $id) {
			$fac = Sys::$Modules['Boutique']->callData("Commande/$id/Facture",false,0,1,'DESC','Id');
			foreach($fac as $f) $files[] = "Boutique/Facture/".$f['Id']."/FacturePdf.pdf";
		}
		$res = array('printFiles'=> $files);
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}

	/**
	 * PGF 
	 *
	 * impression BL
	 * @return	status
	 */
	function PrintLivraisons($ids) {
		$files = array();
		foreach($ids as $id) {
			$bon = Sys::$Modules['Boutique']->callData("Commande/$id/BonLivraison",false,0,1,'DESC','Id');
			foreach($bon as $b) $files[] = "LivraisonStock/BonLivraison/".$b['Id']."/BonDeLivraison.pdf";
		}
		$res = array('printFiles'=> $files);
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}

	/**
	 * PGF 
	 *
	 * export BL
	 * @return	status
	 */
	function ExportLivraisons($ids) {
		$models = array('','SoColissimo');
		$files = array();
		$fps = array();
		foreach($ids as $id) {
			$cmd = Sys::getData('Boutique',"Commande/$id",0,1);
			$cmd = $cmd[0];
//			if($cmd->ExpedieLe) continue;
			$bon = Sys::getData('LivraisonStock',"Commande/$id/BonLivraison",0,1,'DESC','Id');
			$bon = $bon[0];
			$typ = $bon->getParents('TypeLivraison');
			$typ = $typ[0];
			$model = $typ->ModeleExport;
			if(! $model) continue;
			
			if(isset($fps[$model])) {
				$fp = $fps[$model];
			}
			else {
				$file = 'Home/tmp/export-'.$models[$model].'-'.date('Yhm-His').'.csv';
				$files[] = $file;
				$fp = fopen($file, 'w');
				$fps[$model] = $fp;
				switch($model) {
					case 1: $lig = $this->headerColissimo(); break;
					case 2: $lig = $this->headerLaPoste(); break;
				}
				fwrite($fp, $lig);
			}
			$cli = $cmd->getParents('Client');
			$cli = $cli[0];

			$liv = $cmd->getParents('Adresse');
			foreach($liv as $l) {
				if($l->Type == 'Livraison') {
					$liv = $l;
					break;
				}
			}
			if($bon->AdresseLivraisonAlternative) {
				$adr = str_replace('<br />', "\n", $bon->ChoixLivraison);
				$adr = str_replace('<strong>', "", $adr);
				$adr = str_replace('</strong>', "", $adr);
				$adr = str_replace('<br/>', "\n", $adr);
				$adr = str_replace('<br>', "\n", $adr);
				$adr = str_replace("\r", "", $adr);
				$adr = split("\n", $adr);
				$n = count($adr) - 1;
				$tmp = split(" ", $adr[$n]);
				$cp = $tmp[0];
				$vil = $tmp[1];
				$pay = "France";
				$adr[$n] = "Point relais : ".$bon->ChoixLivraisonId;
			}
			else {
				$adr = str_replace('<br />', "\n", $liv->Adresse);
				$adr = str_replace('<br/>', "\n", $adr);
				$adr = str_replace('<br>', "\n", $adr);
				$adr = str_replace("\r", "", $adr);
				$adr = split("\n", $adr);
				$cp = $liv->CodePostal;
				$vil = $liv->Ville;
				$pay = $liv->Pays;
			}

			switch($model) {
				case 1: $lig = $this->lineColissimo($bon->NumBL, $cli, $liv, $adr, $cp, $vil, $pay); break;
			}
			fwrite($fp, $lig);
		}
		foreach($fps as $fp) fclose($fp);
		
		if(!count($files)) return WebService::WSStatus('method', 0, '', '', '', '', '', array(array('message'=>'Rien à exporter')), null);
		$res = array('printFiles'=> $files);
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}

	/**
	 * PGF 
	 *
	 * export BL
	 * @return	header
	 */
	private function headerColissimo() {
		return "Code;Civilité;Nom;Code postal;Ligne 1 d'adresse;Ligne 2 d'adresse;Ligne 3 d'adresse;Ligne d'adresse 4;Commune;Code  pays;Inst. de livraison;Téléphone;Portable;Courriel;Dernière expédition;Prénom;Raison sociale;Code porte 1;Code porte 2;Interphone\r\n";
	}

	/**
	 * PGF 
	 *
	 * export BL
	 * @return	ligne
	 */
	private function lineColissimo($num, $cli, $liv, $adr, $cp, $vil, $pay) {
		$lig = $num.';'.$liv->Civilite.';'.$liv->Nom.';'.$cp.';';
		$n = count($adr);
		$c = 0;
		for($i = 0; $i < $n && $c < 4; $i++, $c++) {
			if(!empty($adr[$i])) $lig .= $adr[$i].';';
		}
		for(;$i < 4; $i++) $lig .= ';';
		$lig .= $vil.';'.$pay.';;'.$cli->Tel.';'.$cli->Portable.';';
		$lig .= $cli->Mail.';;'.$liv->Prenom.';;;;';
		$lig .= "\r\n";
		return $lig;
	}


	/**
	 * PGF 
	 *
	 * epedition
	 * @return	status
	 */
	function Expedition($id, $num, $dat) {
		$this->initFromId($id);
		$bon = $this->getChildren('BonLivraison');
		if(! count($bon)) return WebService::WSStatus('method', 0, '', '', '', '', '', array(array('message'=>"Pas de bon de livraison\nUtiliser : Commande traitée")), null);
		$bon = $bon[0];
		$mag = $this->getParents('Magasin');
		$mag = $mag[0];
		$sit = $mag->getParents('Site');
		$sit = $sit[0];
		$cli = $this->getClient();
		$liv = $this->getAdresseLivraison();

		$this->Expedie = 1;
		$this->ExpedieLe = $dat;
		$this->Save();
		$bon->NumColis = $num;
		$bon->Statut = 3;
		$bon->Save();

		$body = '<html><body><table class="table"  ><tr><td><img src="http://'.$sit->Domaine.'/Skins/LoisirsCrea/Img/bando-mail.jpg.limit.250x200.jpg" class="img-responsive"  />';
		$body .= '</td></tr><tr><td>';
		$body .= "Bonjour $cli->Civilite $cli->Prenom $cli->Nom<br />
			L'équipe de LOVEPAPER a le plaisir de vous informer que votre commande $this->RefCommande a été expédiée aux coordonnées suivantes :";
		if($bon->AdresseLivraisonAlternative)
			$body .= "<br />Pour $liv->Civilite $liv->Prenom $liv->Nom<br /><br />
					<br />$bon->ChoixLivraison<br />";
		else
			$body .= "<br />$liv->Civilite $liv->Prenom $liv->Nom<br /><br />
					$liv->Adresse <br />
					$liv->CodePostal $liv->Ville $liv->Pays<br />";
		$body .= "<hr/>";
		if($num) $body .= "Le numéro de référence de votre expédition est : $num<br />";
		$body .= "Votre commande a été expédiée par : $bon->TypeLivraison
				<br />Toute l'équipe de LOVEPAPER  vous remercie de votre confiance.<br/><br/>
				<hr/>
				Ce mail est envoyé automatiquement, merci de na pas y répondre.
				<hr/>
				Pour nous contacter : ".$mag->EmailContact."<br/><br/>";
		$body .= '</td></tr></table></body></html>';

		$m = new PHPMailer();
		$m->SetFrom($mag->EmailContact,'');
		$m->AddAddress($cli->Mail, '');
//		$m->AddAddress('paul@abtel.fr', '');
		$m->Subject = $mag->Nom.' : Votre commande a été expédiée';
		$m->IsHTML(true);
		$m->Body = $body;
		$res = $m->Send();
		
		return WebService::WSStatus('method', 1, $id, 'Boutique', 'Commande', '', '', null, null);
	}

	/**
	 * PGF 
	 *
	 * epedition
	 * @return	status
	 */
	function CommandeTraitee($id) {
		$this->initFromId($id);
		$bon = $this->getChildren('BonLivraison');
		if(count($bon)) return WebService::WSStatus('method', 0, '', '', '', '', '', array(array('message'=>"Bon de livraison présent\nUtiliser : Numéro d'expédition")), null);

		$this->Expedie = 1;
		$this->Save();
		return WebService::WSStatus('method', 1, $id, 'Boutique', 'Commande', '', '', null, null);
	}

	/**
	 * PGF 
	 * recupere l'objet tva courant
	 * @return objet tva
	 */
	function getObjetTva($otva=null) {
		if(isset($this->ObjetTva)) return $this->ObjetTva;
		if($otva && $otva->checkDate($this->DateCommande)) return $this->ObjetTva = $otva;
		return $this->ObjetTva = new ObjetTva($this->DateCommande);
	}
    private function sendNotifications($new){
// API access key from Google API's Console
        // API access key from Google API's Console
        define('API_ACCESS_KEY', 'AIzaSyCGGUR9EbkicdM7IUXp1l-Z2sHFQCnLp-A');

        //recherche des périphériques à associer.
        $dev = Sys::getData('Pharmacie','Device');
        $registrationIds = array();
        foreach ($dev as $d){
            $registrationIds[] = $d->Key;
        }

        if ($new) {
            $msg = array
            (
                'title' => 'Driveo backoffice: un nouvel évènement commande',
                'message' => 'la commande ' . $this->RefCommande,
                'store' => 'Commandes',
                'vibrate' => 1,
                'sound' => 1
            );
        }else{
            $msg = array
            (
                'title' => 'Driveo backoffice: un nouvel évènement commande',
                'message' => 'la commande ' . $this->RefCommande,
                'store' => 'Commandes',
                'vibrate' => 1,
                'sound' => 1
            );
        }
        $fields = array
        (
            'registration_ids' 	=> $registrationIds,
            'data'			=> $msg
        );

        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        //echo $result;
    }
}
