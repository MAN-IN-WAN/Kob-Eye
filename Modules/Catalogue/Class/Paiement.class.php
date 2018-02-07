<?php

class Paiement extends genericClass {

	public function Save() {
		parent::Save();
	/*	$commande = $this->getCommande();
		if($this->Etat == 1 && !$commande->Paye) {
			// Mise à jour de l'objet commande
			$commande->Set('Paye', 1);
			$commande->Set('PayeLe', time());
			$commande->Save();
			$commande->GenereFacture();
			//$commande->updateInfosLivraison();
		}
		if($this->Etat == 2 && $commande->Valide) {
			$commande->setUnValide();
		}*/
	}

	/**
	 * Fonction appelée pour valider un paiement
	 * directement par le serveur de la banque
	 * @return void
	 */
	public function CheckPaiement() {
		if(($this->Etat != 0 && $this->Etat != 3) or $this->Status != 0) {
			$Mail= new Mail();
			$Mail->Subject("GazService : Paiement déjà effectué");
			$Mail->From($GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::CONTACTEXP'));
			$Mail->ReplyTo($GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::CONTACTEXP'));
			$Mail->To("myriam@abtel.fr");
			$bloc->setFromVar("Mail",$_POST,array("BEACON"=>"BLOC"));
			$Pr = new Process();
			$bloc->init($Pr);
			$bloc->generate($Pr);
			$Mail->Body($bloc->Affich());
			$Mail->Send();
			die;
		}

		// Récupération type de paiement
		$type = $this->getTypePaiement();
		if($type == null) {
			$Mail= new Mail();
			$Mail->Subject("GazService : Le type de paiement non défini");
			$Mail->From($GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::CONTACTEXP'));
			$Mail->ReplyTo($GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::CONTACTEXP'));
			$Mail->To("myriam@abtel.fr");
			$bloc->setFromVar("Mail",$_POST,array("BEACON"=>"BLOC"));
			$Pr = new Process();
			$bloc->init($Pr);
			$bloc->generate($Pr);
			$Mail->Body($bloc->Affich());
			$Mail->Send();
			die;
		}

		// Chargement du plugin
		$plugin = $type->getPlugin();
		if($plugin == null) {
			$Mail= new Mail();
			$Mail->Subject("GazService : Le type de paiement non pris en charge");
			$Mail->From($GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::CONTACTEXP'));
			$Mail->ReplyTo($GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::CONTACTEXP'));
			$Mail->To("myriam@abtel.fr");
			$bloc->setFromVar("Mail",$_POST,array("BEACON"=>"BLOC"));
			$Pr = new Process();
			$bloc->init($Pr);
			$bloc->generate($Pr);
			$Mail->Body($bloc->Affich());
			$Mail->Send();
			die;
		}
		// Résultats de l'analyse par le plugin
		$results = $plugin->serveurAutoResponse( $this ); 
		if($results == null) {
			$Mail= new Mail();
			$Mail->Subject("GazService : controle du paiement impossible");
			$Mail->From($GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::CONTACTEXP'));
			$Mail->ReplyTo($GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::CONTACTEXP'));
			$Mail->To("myriam@abtel.fr");
			$bloc->setFromVar("Mail",$_POST,array("BEACON"=>"BLOC"));
			$Pr = new Process();
			$bloc->init($Pr);
			$bloc->generate($Pr);
			$Mail->Body($bloc->Affich());
			$Mail->Send();
			die;
		}

		if($_POST['result']!='00') {
			$Mail= new Mail();
			$Mail->Subject("GazService : Code retour en erreur");
			$Mail->From($GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::CONTACTEXP'));
			$Mail->ReplyTo($GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::CONTACTEXP'));
			$Mail->To("myriam@abtel.fr");
			$bloc->setFromVar("Mail",$_POST,array("BEACON"=>"BLOC"));
			$Pr = new Process();
			$bloc->init($Pr);
			$bloc->generate($Pr);
			$Mail->Body($bloc->Affich());
			$Mail->Send();
			die;
		}

		// Mise à jour de l'objet paiement
		$this->Set('Detail', print_r($_POST, true));
		$this->Set('ReferenceBanque', $results['ref']);
		$this->Set('Etat', $results['etat']);
		$this->Set('Status', 1);
		$this->Save();
		// envoie des mails à utilisateur et gazservice

		$Mail= new Mail();
		$Mail->Subject("Confirmation paiment Gaz-service");
		$Mail->From($GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::CONTACTEXP'));
		$Mail->ReplyTo($GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::CONTACTEXP'));
		$Mail->To($this->Email);
		$Mail->Bcc($GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::CONTACT'));
		$Mail->Bcc($GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::CONTACTCOMPTA'));
//		$Mail->Bcc("myriam@abtel.fr");
		$bloc = new Bloc();
		$mailContent = "<h1>Réglement Facture</h1><br />";
		$mailContent .= "Client: " . $this->Nom . " <br />";
		$mailContent .= "Email: " . $this->Email . " <br />";
		$mailContent .= "Référence dossier: " . $this->Reference . " <br />";
		$mailContent .= "Montant de la facture: " . $this->Montant . " <br />";
		$mailContent .= "### Ticket de paiement à conserver ###  <br />";
		$mailContent .= "Id Authorisation: " . $_POST['auth_number'] . " <br />";
		$mailContent .= "Certificat de paiement: " . $_POST['payment_certificate'] . " <br />";
		$mailContent .= "Id Transaction: " . $_POST['trans_id'] . " <br />";
		$montant = $_POST['amount']/100;
		$mailContent .= "Montant réglé: " . number_format($montant,2) . " <br />";

//		$mailContent .= "Date du paiement: " .  $_POST['trans_date'];
		$regexp = preg_match("#([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})#",$_POST['trans_date'],$out);
		$mailContent .= "Date du paiement: " . $out[3] . "-" . $out[2] . "-" . $out[1] . "  " . $out[4] . ":" . $out[5] . ":" . $out[6] ;

		$bloc->setFromVar("Mail",$mailContent,array("BEACON"=>"BLOC"));
		$Pr = new Process();
		$bloc->init($Pr);
		$bloc->generate($Pr);
		$Mail->Body($bloc->Affich());
		$Mail->Send();


	}

	/**
	 * Récupère le type du paiement
	 * @return	Objet KE Type Paiement
	 */
	public function getTypePaiement() {
		$tps = $this->storproc('Catalogue/TypePaiement/Paiement/'.$this->Id);
		if(is_array($tps)) return genericClass::createInstance('Catalogue', $tps[0]);
	}

	/**
	 * Récupère la commande
	 * @return	Objet KE Commande
	 */
	public function getCommande() {
		$c = $this->storproc('Catalogue/Commande/Paiement/'.$this->Id);
		if(is_array($c)) return genericClass::createInstance('Catalogue', $c[0]);
	}

	/**
	 * Définit le paiement en pending
	 * @return	void
	 */
	public function setPending() {
		// Myriam ici on n'a pas de commande Mars 2013
	/*	$c = $this->getCommande();*/
		$c->PaymentPending = true;
		$c->Save();
	}

	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return Sys::$Modules['Catalogue']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}
}