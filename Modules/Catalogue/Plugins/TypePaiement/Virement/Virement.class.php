<?php

/*********************************************
*
* Module de paiement
* Crédit Mutuel
* Abtel
* 
*********************************************/

require_once( dirname(dirname(__FILE__)).'/TypePaiement.interface.php' );

class BoutiqueTypePaiementVirement extends Plugin implements BoutiqueTypePaiementPlugin {


	public function getCodeHTML( $paiement ) {
		// il n'y a rien de special à faire pour le paiment par cheque
		header('Location: /Boutique/Commande/Etape5');
	}

	public function serveurAutoResponse( $paiement, $commande ) {
	}

	public function affichageEtape5( $paiement, $commande ) {
		$Tp = $paiement->getTypePaiement();
		$Cli = $commande->getClient();
		$Mag = $commande->getMagasin();

		if($commande->Paye) {
				return 'Le paiment de votre commande ' . $commande->RefCommande . 'a bien été reçu ';
		} else {
			// envoie de mail au client avec rib
			require_once("Class/Lib/Mail.class.php");
			$Mail= new Mail();
			$Mail->Subject("Votre commande ".$commande->RefCommande." vient d'etre validée");
			$Mail->From($GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::CONTACT'));
			$Mail->ReplyTo($GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::NOREPLY'));
			$Mail->To($Cli->Mail);
			$bloc = new Bloc();
			$mailContent = "
				Bonjour ".$Cli->Civilite." ".$Cli->Prenom." ".$Cli->Nom.",<br />
				Nous vous informons que votre commande N° ".$commande->RefCommande." a bien été prise en compte. <br />  Vous avez choisi de payer par virement bancaire. Votre commande sera expédiée à confirmation de ce paiement par notre banque, les délais d'expédition courrent donc à partir de cette confirmation.<br />
				Veuillez trouver ci-dessous le RIB qui vous permettra d'effectuer ce paiement :<br /><br />" . $Mag->Rib . "<br /><br />
				Vous recevrez un mail de confirmation d'expédition comprenant le Numéro de suivi de votre colis.<br />
				<br />			
				Toute l'équipe de [!Domaine!] vous remercie de votre confiance,<br />
				<br />			
				Pour nous contacter : " . $GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::CONTACT');
	

			$bloc->setFromVar("Mail",$mailContent,
				array("BEACON"=>"BLOC"));
			$Pr = new Process();
			$bloc->init($Pr);
			$bloc->generate($Pr);
			$Mail->Body($bloc->Affich());
			$Mail->Send();

			return  $Tp->Description;
		}
	}

	public function retrouvePaiementEtape4s() {
		return false;
	}

}