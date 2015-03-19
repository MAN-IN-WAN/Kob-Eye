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
			//Mise a jour du temps de reponse
			$this->TempsReponse = mktime()-$cde->tmsEdit;
		}
		parent::Save();
		//Appel de modification du produit pour mettre à jour les infos du produit
		$P = $GLOBALS["Systeme"]->Modules['Boutique']->callData('Boutique/Reference/LigneCommande/'.$this->Id,false,0,1);
		if (!empty($P)&&is_array($P[0])){
			$P = genericClass::createInstance('Boutique',$P[0]);
			$this->Titre = $P->Reference." - ".$P->Nom;
			$this->Reference = $P->Reference;
			$this->Image = $P->Image;
			$this->Montant = $P->Tarif;
			$this->Description = $P->Description;
		}
		parent::Save();

		//recuperation du vendeur
		$vend = $this->storproc('Boutique/Client/Reference/'.$P->Id);
		$cde = $this->storproc('Boutique/Commande/LigneCommande/'.$this->Id);
		$ach = $this->storproc('Boutique/Client/Commande/'.$cde[0]['Id']);

		if ($this->Expedie==1&&$this->Livre==0){
			//Envoi du mail à l'acheteur
			 $this->sendMailAcheteurL($ach,$cde,$vend);
		}
		if ($this->Livre==1) $this->sendMailVendeurL($vend,$P,$this,$ach);
		
	}


	/**
	* Envoi du mail au vendeur l'informant que son colis a été reçu
	*/
	function sendMailVendeurL($vend,$annonce,$ligcde,$ach) {
		require_once("Class/Lib/Mail.class.php");
		$Mail= new Mail();
		$Mail->Subject("[GAMES-AVENUE] Votre Expédition".$ligcde->RefEnvoi." vient d'etre reçue");
		$Mail->From("noreply@games-avenue.com");
		$Mail->ReplyTo("noreply@games-avenue.com");
		$Mail->To("bug@expressiv.net");
		$Mail->To($vend[0]['Mail']);
		$bloc = new Bloc();
		$mailContent = "Bonjour ". $vend[0]['Prenom'] ." " . $vend[0]['Nom']. " ,<br />
			Nous vous informons que votre article N° ".$annonce->Reference." ".$annonce->Nom." viens d'être reçu par le client ". $ach[0]['Prenom'] . " " . $ach[0]['Nom'] . ".<br />
			Toute l'équipe de Games-Avenue vous remercie de votre confiance,<br />
			<br />
			Avec http://www.games-avenue.com/ <br />
			Achetez ou Vendez, c'est vous qui choisissez !<br />
			<br />
			Pour nous contacter : contact@games-avenue.com";
		$bloc->setFromVar("Mail|Validation d'une annonce|http://www.games-avenue.com/Skins/gamesavenue/Images/GA_Logo_Pdf.jpg|http://www.games-avenue.com",$mailContent,
				array("BEACON"=>"BLOC"));
		$Pr = new Process();
		$bloc->init($Pr);
		$bloc->generate($Pr);
		$Mail->Body($bloc->Affich());
		$Mail->Send();
	}


	/**
	* Envoi du mail a l'acheteur l'informant que son achat a expédié
	*/
	function sendMailAcheteurL($ach,$commande,$vend) {
		require_once("Class/Lib/Mail.class.php");
		$Mail= new Mail();
		$Mail->Subject("[GAMES-AVENUE] Votre commande ".$commande->RefCommande." vient d'etre expédiée");
		$Mail->From("noreply@games-avenue.com");
		$Mail->ReplyTo("noreply@games-avenue.com");
		$Mail->To("bug@expressiv.net");
		$Mail->To($ach[0]['Mail']);
		$bloc = new Bloc();
		$mailContent = "
			Bonjour ".$ach[0]['Prenom']." ".$ach[0]['Nom'] . ",<br />
			Nous vous informons que votre commande N°".$commande[0]['RefCommande']." à bien été expédiée par le vendeur ".$vend[0]['Pseudonyme'].".<br />
			Lors de la réception de votre colis nous vous remercions de bien vouloir vous rendre sur www.games-avenue.com dans votre espace - Historique de mes commandes - pour valider la bonne réception de
			votre commande et de laisser une notation sur la qualité de service de votre vendeur.<br />
				
			<br />			
			Toute l'équipe de Games-Avenue vous remercie de votre confiance,<br />
			<br />			
			Avec http://www.games-avenue.com/ <br />
			Achetez ou Vendez, c'est vous qui choisissez !<br />
		
			Pour nous contacter : contact@games-avenue.com";
	

		$bloc->setFromVar("Mail|Validation d'une annonce|http://www.games-avenue.com/Skins/gamesavenue/Images/GA_Logo_Pdf.jpg|http://www.games-avenue.com",$mailContent,
				array("BEACON"=>"BLOC"));
		$Pr = new Process();
		$bloc->init($Pr);
		$bloc->generate($Pr);
		$Mail->Body($bloc->Affich());
		$Mail->Send();
	}

	 /**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
    	*/
   	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return $GLOBALS['Systeme']->Modules['Boutique']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
   	}

}