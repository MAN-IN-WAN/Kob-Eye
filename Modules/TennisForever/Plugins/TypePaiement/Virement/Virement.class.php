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

	/**
	* initStatePaiement
	* Initiliase le paiement avec ses propriétés particulières
	* Dans le cas virement définit le statut en attente par defaut (etat=4)
	*/
	public function initStatePaiement() {
		return 4;
	}


	public function getCodeHTML( $paiement ) {
		$cde= $paiement->getCommande();
		//On met le paiement en attente
		$cde->setPending();
		header('Location: '.Sys::getMenu('Boutique/Commande/Etape5'));
	}

	public function serveurAutoResponse( $paiement, $commande ) {
	}

	public function affichageEtape5( $paiement, $commande ) {
		$Tp = $paiement->getTypePaiement();
		$Cli = $commande->getClient();
		$Mag = $commande->getMagasin();
		$Site = $commande->getSiteMagasin();
		


		if($commande->Paye) {
				return 'Le paiment de votre commande ' . $commande->RefCommande . 'a bien été reçu ';
		} else {
			// envoie de mail au client avec rib
			require_once("Class/Lib/Mail.class.php");
			$Mail= new Mail();
			$Mail->Subject("Votre commande ".$commande->RefCommande." vient d'etre validée");
			$Mail->From($Mag ->EmailContact);
//			$Mail->ReplyTo($GLOBALS['Systeme']->Conf->get('MODULE::SYSTEME::NOREPLY'));
			$Mail->ReplyTo($Mag->EmailContact);
			$Mail->To($Cli->Mail);
			$bloc = new Bloc();
			$mailContent = "
				Bonjour ".$Cli->Civilite." ".$Cli->Prenom." ".$Cli->Nom.",<br />
				Nous vous informons que votre commande N° ".$commande->RefCommande." a bien été prise en compte. <br />  Vous avez choisi de payer par virement bancaire. Votre commande sera expédiée à confirmation de ce paiement par notre banque, les délais d'expédition courants seront donc appliqués à partir de cette confirmation.<br />
				Veuillez trouver ci-dessous le RIB qui vous permettra d'effectuer ce paiement :<br /><br />" . $Mag->Rib . "<br /><br />
				Vous recevrez un mail de confirmation d'expédition comprenant le Numéro de suivi de votre colis.<br />
				<br />			
				Toute l'équipe de LOVE PAPER  vous remercie de votre confiance,<br />
				<br />			
				Pour nous contacter : " . $Mag -> EmailContact;
	

			$bloc->setFromVar("Mail",$mailContent,
				array("BEACON"=>"BLOC"));
			$Pr = new Process();
			$bloc->init($Pr);
			$bloc->generate($Pr);
			$Mail->Body($bloc->Affich());
			$Mail->Send();
			//MODIF OCTOBRE 2014 POUR GERER LE BBCODE
			$U = new charUtils();
			$U->Beacon="UTIL";
			$U->Vars="BBCODE";
			$U->Data=$Tp->Description;

			return  $U->Affich();
		}
	}

	public function retrouvePaiementEtape4s() {
		return false;
	}

}