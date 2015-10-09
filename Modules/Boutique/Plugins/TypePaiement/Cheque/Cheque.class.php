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

	/**
	* initStatePaiement
	* Initiliase le paiement avec ses propriétés particulières
	* Dans le cas cheque définit le statut en attente par defaut (etat=4)
	*/
	public function initStatePaiement() {
		return 4;
	}

	public function getCodeHTML( $paiement ) {
		$cde= $paiement->getCommande();
//		$cde->sendMailAcheteurAttentePaiement($cde, 'par Chèque');
		//$cde->sendMailAcheteurAttentePaiement();
		$cde->setPending();
		// il n'y a rien de special à faire pour le paiment par cheque
		header('Location: '.Sys::getMenu('Boutique/Commande/Etape5'));
	}

	public function serveurAutoResponse( $paiement, $commande ) {
	}

	public function affichageEtape5( $paiement, $commande ) {
		
		if($commande->Paye) {
			return 'Le paiment de votre commande ' . $commande->RefCommande . 'a bien été reçu.';
		} else {

			// envoie de mail au client avec attente paiement
			$Tp = $paiement->getTypePaiement();
			$Cli = $commande->getClient();
			$Mag = $commande->getMagasin();
			$Site = $commande->getSiteMagasin();
			$Bonlivr =$commande -> getBonLivraison();
			$livr = $commande -> getAdresseLivraison();
			
			$Civilite = $Cli -> Civilite . " " . $Cli ->  Prenom . ' <span style="text-transform:uppercase">' . $Cli  -> Nom . '</span>';
			$Lacommande = "";
			$commande -> getLignesCommande();
			if (!sizeof($commande -> LignesCommandes)) {
				$Lacommande = "";
			} else {
				$Lacommande = "<br /><br />Recapitulatif de votre commande  : <br /><br /><table style=\"font-family: arial,helvetica,sans-serif; font-size: 10pt; color: rgb(0, 0, 0);\">";
				foreach ($commande->LignesCommandes as $l) :
					$Lacommande .= "<tr><td>" . $l -> Quantite . "</td>";
					$Lacommande .= "<td>" . $l -> Titre . "</td></tr>";
				endforeach;
				$Lacommande .= "</table>";
			}
			if (is_object($Bonlivr)&&$Bonlivr -> AdresseLivraisonAlternative) {
				$AdressLiv = "<br />Pour " . $Civilite . " à <br /> " . $Bonlivr -> ChoixLivraison . "<br /> ";
			} else {
				$AdressLiv = "<br />" . $Civilite . "<br />" . $livr-> Adresse . " <br /> " . $livr-> CodePostal . "  " . $livr -> Ville . " " . $livr -> Pays;
			}

			require_once("Class/Lib/Mail.class.php");
			$Mail= new Mail();
			$Mail->Subject("Votre commande ".$commande->RefCommande." vient d'etre valid&eacute;e");
			$Mail->From($Mag -> EmailContact);
			$Mail->ReplyTo($Mag -> EmailContact);
			$Mail->To($Cli->Mail);
			$bloc = new Bloc();
			$mailContent = "
			Bonjour " . $Civilite . ",<br /><br />
			Nous vous informons que votre commande N° " . $commande -> RefCommande . " a bien été prise en compte.<br />
			Vous avez choisi de payer par chèque bancaire. Votre commande sera expédiée à confirmation de ce paiement par notre banque, les délais d'expédition courants seront donc appliqués à partir de cette confirmation.<br />
			Vous pouvez d'ores et déjà vous rendre sur <a style='text-decoration:underline' href='" . $Site -> Domaine . "/" . $GLOBALS['Systeme'] ->  getMenu('Boutique/Mon-compte') . "' > votre espace client </a> et suivre l'évolution de votre commande.<br /><br />
			Adresse de livraison de votre commande " . $AdressLiv . "<br /><br /> " . $Lacommande . "<br /><br />
			Toute l'équipe de " . $Mag -> Nom . " vous remercie de votre confiance,<br />
			<br />Pour nous contacter : " .  $Mag -> EmailContact  . " .";
			$bloc->setFromVar("Mail",$mailContent,
				array("BEACON"=>"BLOC"));
			$Pr = new Process();
			$bloc->init($Pr);
			$bloc->generate($Pr);
			$Mail->Body($bloc->Affich());
			$Mail->Send();


			return  nl2br($Tp->Description);
		}
	}

	public function retrouvePaiementEtape4s() {
		return false;
	}
	
}
