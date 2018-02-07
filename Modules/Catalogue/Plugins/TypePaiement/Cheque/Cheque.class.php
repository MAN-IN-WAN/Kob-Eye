<?php

/*********************************************
*
* Module de paiement
* Crédit Mutuel
* Abtel
* 
*********************************************/

require_once( dirname(dirname(__FILE__)).'/TypePaiement.interface.php' );

class BoutiqueTypePaiementCheque extends Plugin implements BoutiqueTypePaiementPlugin {

	public function getCodeHTML( $paiement ) {
		$cde= $paiement->getCommande();
		$cde->sendMailAcheteurAttentePaiement($cde, 'par Chèque');
		$cde->PaymentPending = true;
		$cde->Save();
		// il n'y a rien de special à faire pour le paiment par cheque
		header('Location: /Boutique/Commande/Etape5');
	}

	public function serveurAutoResponse( $paiement, $commande ) {
	}

	public function affichageEtape5( $paiement, $commande ) {
		$Tp = $paiement->getTypePaiement();
		
		if($commande->Paye) {
			return 'Le paiment de votre commande ' . $commande->RefCommande . 'a bien été reçu.';
		} else {
			return  nl2br($Tp->Description);
		}
	}

	public function retrouvePaiementEtape4s() {
		return false;
	}
	
}