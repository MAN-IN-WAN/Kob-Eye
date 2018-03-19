<?php

class Paiement extends genericClass {

	public function Save() {
		parent::Save();
		//Si c'est une creation on passe dans la fonction initPaiement du plugin
		if (!$this->Etat){
			$type = $this->getTypePaiement();
			$plugin = $type->getPlugin();
			$this->Etat = $plugin->initStatePaiement();
			parent::Save();
		}

		$commande = $this->getCommande();
		if($this->Etat == 1 && !$commande->Paye) {
			// Mise à jour de l'objet commande
			$commande->Set('Paye', 1);
			$commande->Set('PayeLe', time());
			// 17/11/2014 : ajout pour validation d'un panier en attente de paiement
			$commande->Set('Valide', 1);
			$commande->Set('Current', 0);
			//========================================================================
			$commande->Save();
			$commande->GenereFacture();
			//$commande->updateInfosLivraison();
		}
		if($this->Etat == 2 && $commande->Valide) {
			$commande->setUnValid();
		}
	/*	if(($this->Etat == 0 && $commande->Valide&& $this->Instance=0) {
			$commande->setUnValid();
		}*/
	}

	/**
	 * Fonction appelée pour valider un paiement
	 * directement par le serveur de la banque
	 * @return void
	 */
	public function CheckPaiement() {
		if(($this->Etat != 0 && $this->Etat < 3) or $this->Status != 0) {
			//mail($GLOBALS["Systeme"]->Conf->get("GENERAL::INFO::ADMIN_MAIL"), "Paiement déjà effectué.", print_r($_POST, true));
			//die("Paiement déjà effectué.");
			return;
		}

		// Récupération commande
		$commande = $this->getCommande();
		if($commande == null) {
			mail($GLOBALS["Systeme"]->Conf->get("GENERAL::INFO::ADMIN_MAIL"), "Impossible de trouver la commande correspondante.", print_r($_POST, true));
			die("Impossible de trouver la commande correspondante.");
		}

		// Récupération type de paiement
		$type = $this->getTypePaiement();
		if($type == null) {
			mail($GLOBALS["Systeme"]->Conf->get("GENERAL::INFO::ADMIN_MAIL"), "Le type de paiement n'est pas défini.", print_r($_POST, true));
			die("Le type de paiement n'est pas défini.");
		}

		// Chargement du plugin
		$plugin = $type->getPlugin();
		if($plugin == null) {
			mail($GLOBALS["Systeme"]->Conf->get("GENERAL::INFO::ADMIN_MAIL"), "Ce type de paiement ne peut être pris en charge.", print_r($_POST, true));
			die("Ce type de paiement ne peut être pris en charge.");
		}

		// Résultats de l'analyse par le plugin
		$results = $plugin->serveurAutoResponse( $this, $commande ); 
		if($results == null) {
			return;
			mail($GLOBALS["Systeme"]->Conf->get("GENERAL::INFO::ADMIN_MAIL"), "Le paiement n'a pas pu être contrôlé.", print_r($_POST, true));
			die("Le paiement n'a pas pu être contrôlé.");
		}

		// Mise à jour de l'objet paiement
		$this->Set('Detail', $results['detail']);
		$this->Set('Reference', $results['ref']);
		$this->Set('Etat', $results['etat']);
		$this->Set('Status', 1);
		$this->Save();

	}

	/**
	 * Récupère le type du paiement
	 * @return	Objet KE Type Paiement
	 */
	public function getTypePaiement() {
		$tps = $this->storproc('Boutique/Instance/Paiement/'.$this->Id);
		if(is_array($tps)) return genericClass::createInstance('Boutique', $tps[0]);
	}

	/**
	 * Récupère la commande
	 * @return	Objet KE Commande
	 */
	public function getCommande() {
		$c = $this->storproc('Boutique/Commande/Paiement/'.$this->Id);
		if(is_array($c)) return genericClass::createInstance('Boutique', $c[0]);
	}

	/**
	 * Définit le paiement en pending
	 * @return	void
	 */
	public function setPending() {
		$c = $this->getCommande();
		$c->PaymentPending = true;
		$c->Save();
		//
	}

	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return Sys::$Modules['Boutique']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}
}