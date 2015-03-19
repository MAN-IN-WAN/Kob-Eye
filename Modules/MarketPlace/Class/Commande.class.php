<?php

class Commande extends genericClass {

	/**
	 * Enregistrement d'une commande
	 * -> Check référence
	 * @return	void
	 */
	public function Save() {
		parent::Save();
		$this->detectState();
		// Lignes Commandes actuellement en cache -> on les enregistre
		if(is_array($this->LignesCommandes)) :
			foreach($this->LignesCommandes as $obj) :
				$obj->AddParent( $this );
				$obj->Save();
			endforeach;
		endif;
		parent::Save();
	}

	/**
	* Suppression d'une commande
	*
	*/
	public function Delete() {
		$this->reset();
		parent::Delete();
	}

	/**
	 * Detection de l'etat d'une commande en fonction de sa reference et application des actions en fonction du changement d'etat
	 * @return	void
	 */
	private function detectState() {
		//Transformation d'une commande valide en commande payé
		if($this->Paye&&$this->Valide&&preg_match("#VAL.*#",$this->RefCommande)) {
			//Recherche du nombre de commande valide afin d'incrémenter le bon nombre de commande
			$ventes = $this->storproc('Boutique/Commande/Valide=1&&Paye=1',false,0,1,'','','COUNT(DISTINCT(m.Id))');
			$ref = $ventes[0]['COUNT(DISTINCT(m.Id))'];
			$this->RefCommande = sprintf("CDE%05d",$ref);
			$this->SetPaye();
		}
		//Transformation d'une commande brouillon en commande payé
		if($this->Paye&&!$this->Valide&&preg_match("#BRO.*#",$this->RefCommande)) {
			$this->Valide = true;
			//Recherche du nombre de comande valide afin d'incrémenter le bon nombre de commande
			$ventes = $this->storproc('Boutique/Commande/Valide=1&&Paye=1',false,0,1,'','','COUNT(DISTINCT(m.Id))');
			$ref = $ventes[0]['COUNT(DISTINCT(m.Id))'];
			$this->RefCommande = sprintf("CDE%05d",$ref);
			//Decrementation des annonces concernées
			$this->applyCommande();
		}
		//Transformation d'une commande brouillon en commande valide
		if($this->Valide&&!$this->Paye&&preg_match("#BRO.*#",$this->RefCommande)) {
			$this->RefCommande = sprintf("VAL%05d",$this->Id);
			//Decrementation des annonces concernées
			$this->applyCommande();
		}
		//Création d'une commande brouillon
		if(empty($this->RefCommande)) {
			$this->RefCommande = sprintf("BRO%05d",$this->Id);
		}
		//Devalidation d'une commande
		if ((!$this->Valide&&preg_match("#VAL.*#",$this->RefCommande))||(!$this->Paye&&!$this->Valide&&preg_match("#CDE.*#",$this->RefCommande))){
			$this->RefCommande = sprintf("BRO%05d",$this->Id);
			$this->discardCommande();
		}
		//Annulation du paiement
		if (!$this->Paye&&$this->Valide&&preg_match("#CDE.*#",$this->RefCommande)){
			$this->RefCommande = sprintf("VAL%05d",$this->Id);
		}
	}

	/**
	* reinitialisation de la commande
	*
	*/
	public function reset(){
		if (!$this->Id||$this->Paye)return;
		$this->initFromBDD();
		//On reinitialise les propriétes
		if ($this->Valide){
			$this->Valide=false;
			$this->Save();
		}
		//Suppression des tentatives de paiement
		/*if (is_array($this->Paiements))foreach($this->Paiements as $lc) :
			$lc->Delete();
		endforeach;*/
		
		$this->Paye=false;
		$this->Montant = 0;
		$this->Expedie=false;
		//Reinitialisation des lignes de la commande
		if (is_array($this->LignesCommandes))foreach($this->LignesCommandes as $lc) :
			$lc->Delete();
		endforeach;
	}

	/**
	* initFromReference initialise la commande depuis une reference un acheteur et une adresse de livraison
	* @Param r : OBJET reference 
	* @Param c : OBJET acheteur
	* @Param al : adresse livraison
	*/
	public function initFromReference($r, $c) {
		//reset
		if($this->Id) $this->reset();
		if($r->estDisponible()) {
			// On affecte le montant à cette commande
			$typeLivraison = $this->storproc('Boutique/TypeLivraison/Reference/' . $r->Id);
			$this->MontantHt = $r->Tarif + $typeLivraison[0]['Cout']+0.70;
			$this->addParent($c);
	
			// Création de la ligne de la commande à partir de la référence
			$this->ajouterLigneCommande( $r );
			$this->Save();
			return true;
		}
		return false;
	}

	/**
	* setAdresseLivraison definit l'adresse de livraison
	* @Param A : adresse livraison
	*/
	public function setAdresseLivraison($A) {
		$this->AdresseLivraison = $A;
		$this->Save();	
	}

	/**
	 * Ajoute une ligne à une commande
	 * Attention: elle ne s'enregistre pas de suite, cela se fait quand on Save la commande
	 * @param	Objet	Référence 
	 * @param	int		Quantité
	 */
	public function ajouterLigneCommande( $r, $qte = 1 ) {

		if($this->Id)  $this->initFromBDD();
		else $this->LignesCommandes = array();
		// On vérifie que la référence n'est pas déjà parmi les lignes de commandes
		// -> si c'est le cas on augmente seulement la quantité, on ne créé pas une nouvelle ligne commande
		foreach($this->LignesCommandes as $i=> $lc) :
			if($lc->Reference == $r->Id) :
				$this->LignesCommandes[$i]->Quantite += $qte;
				return;
			endif;
		endforeach;

		// -> sinon : Création de la ligne commande
		$ligneCommande = genericClass::createInstance('Boutique', 'LigneCommande');
		$ligneCommande->Titre = $r->Nom;
		$ligneCommande->Quantite = $qte;
		$ligneCommande->AddParent($r);
		$c = $this->storproc('Boutique/Client/Reference/' . $r->Id);
		$ligneCommande->AddParent('Boutique/Client/'.$c[0]['Id']);

		$this->LignesCommandes[] = $ligneCommande;
	}

	/**
	 * Récupère toutes les lignes commandes déjà existantes
	 * @return	Tableau des lignes commandes
	 */
	private function initFromBDD() {
		//Initialisation des lignes de commande
		$lignes = $this->storproc('Boutique/Commande/' . $this->Id . '/LigneCommande');
		$this->LignesCommandes = array();
		if (is_array($lignes))foreach($lignes as $l) $this->LignesCommandes[] = genericClass::createInstance('Boutique', $l);
		//Initialisation des tentatives de paiement
		$lignes = $this->storproc('Boutique/Commande/' . $this->Id . '/Paiement');
		$this->Paiements = array();
		if (is_array($lignes))foreach($lignes as $l) $this->Paiements[] = genericClass::createInstance('Boutique', $l);
	}

	/**
	 * Total des commission des lignes de la commande
	 * @return	int		Le total
	 */
	public function getTotalCommission() {
		$lignes = $this->storproc('Boutique/Commande/' . $this->Id . '/LigneCommande');
		$total = 0;
		if (is_array($lignes))foreach($lignes as $l) :
			// Référence concernée
			$ref = $this->storproc('Boutique/Reference/LigneCommande/' . $l['Id']);
			$tl = $this->storproc('Boutique/TypeLivraison/Reference/' . $ref[0]['Id']);
			$total+= $this->getCommission($l['Montant'] + $tl[0]['Cout'])+0.70; 
		endforeach;
		return round($total,2);
	}

	/**
	* Retourne le montant de la commission par rapport à un montant final
	* @param price
	* @return commission
	*/
	public function getCommission($prix){
		// TODO Calculer commission
		$comm = $prix;
		$comm = $comm* 0.15;
		$comm+=0.75;
		if($prix > 10) $comm += 0.25;
		return $comm;
	}

	/**
	 * Retourne un tableau qui contient à chaque fois 2 infos
	 * -> Email du paiement
	 * -> Montant du
	 */
	public function getVendeurPaiement() {
		$lignes = $this->storproc('Boutique/Commande/' . $this->Id . '/LigneCommande');
		$return = array();
		if (is_array($lignes))foreach($lignes as $l) :
			//On recupere le client
			$vendeur = $this->storproc('Boutique/Client/LigneCommande/'.$l['Id'],false,0,1);
			//On recupere le tarif de livraison
			$ref = $this->storproc('Boutique/Reference/LigneCommande/'.$l['Id'],false,0,1);
			$tl = $this->storproc('Boutique/TypeLivraison/Reference/'.$ref[0]['Id'],false,0,1);
			$return[] = array("Mail"=>$vendeur[0]['Mail'],"Montant"=>round($l['Montant']+$tl[0]["Cout"]-$this->getCommission($l['Montant']+$tl[0]["Cout"]),2));
		endforeach;
		return $return;
	}

	public function setValid() {
		$this->Valide=true;
		$this->Save();
	}

	public function applyCommande() {
		$lignes = $this->storproc('Boutique/Commande/' . $this->Id . '/LigneCommande');
		if (is_array($lignes))foreach($lignes as $l) :
			$r = $this->storproc('Boutique/Reference/LigneCommande/' . $l['Id']);
			$ref = genericClass::createInstance('Boutique', $r[0]);
			$ref->decrementeStock($l['Quantite']);
		endforeach;
	}

	public function discardCommande() {
		$lignes = $this->storproc('Boutique/Commande/' . $this->Id . '/LigneCommande');
		if (is_array($lignes))foreach($lignes as $l) :
			$r = $this->storproc('Boutique/Reference/LigneCommande/' . $l['Id']);
			$ref = genericClass::createInstance('Boutique', $r[0]);
			if (!is_array($ref[0]))continue;
			$ref->incrementeStock($l['Quantite']);
		endforeach;
	}

	public function setUnValid() {
		$this->Valide=false;
		$this->Save();
	}

	public function estDisponible() {
		$lignes = $this->storproc('Boutique/Commande/' . $this->Id . '/LigneCommande');
		if (!sizeof($lignes)) return false;
		if (is_array($lignes))foreach($lignes as $l) :
			$r = $this->storproc('Boutique/Reference/LigneCommande/' . $l['Id']);
			$ref = genericClass::createInstance('Boutique', $r[0]);
			if (!$ref->estDisponible()) return false;
		endforeach;
		return true;
	}

	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return $GLOBALS['Systeme']->Modules['Boutique']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}

	/**
	 * Recuperation du dernier paiement definit
	 * @return	Renvoie le dernier paiement
	 */
	public function getPaiement() {
		$lignes = $this->storproc('Boutique/Commande/' . $this->Id . '/Paiement',false,0,1,'DESC','Id');
		if (is_array($lignes)&&is_array($lignes[0]))
			$P = genericClass::createInstance('Boutique',$lignes[0]);
		else return false;
		return $P;
	}

	/**
	* Ajout des informations de paiement
	* @param p informations de paiement paypal
	* @return void
	*/
	public function addPaypal($r){
		$p = genericClass::createInstance('Boutique','Paiement');
		$p->Detail="----------------------------------\r\n";
		$p->Detail.=print_r($r,true);
		$p->Set('Reference',$r->payKey);
		$p->Set('Status',($r->paymentExecStatus=="CREATED")?true:false);
		$p->addParent($this);
		$p->Save();
	}

	/**
	* Verifie que la commande concerne cette reference
	* @param r Objet reference
	* @return void
	*/
	public function checkReference($r=false){
		if (!$r)return false;
		$lignes = $this->storproc('Boutique/Commande/' . $this->Id . '/LigneCommande');
		if (!sizeof($lignes)) return false;
		if (is_array($lignes))foreach($lignes as $l) :
			$re = $this->storproc('Boutique/Reference/LigneCommande/' . $l['Id']);
			$ref = genericClass::createInstance('Boutique', $re[0]);
			if ($ref->Id==$r->Id) return true;
		endforeach;
		return false;
	}
	/**
	* renvoie la referenc e de la commande
	* @param r Objet reference
	* @return void
	*/
	public function getReference(){
		$lignes = $this->storproc('Boutique/Commande/' . $this->Id . '/LigneCommande');
		if (!sizeof($lignes)) return false;
		if (is_array($lignes))foreach($lignes as $l) :
			$r = $this->storproc('Boutique/Reference/LigneCommande/' . $l['Id']);
			$ref = genericClass::createInstance('Boutique', $r[0]);
			return $ref;
		endforeach;
		return false;
	}

	/**
	* Conclusion de la commande
	*/
	public function SetPaye() {
		
		$this->initFromBDD();
		foreach($this->LignesCommandes as $i=> $lc) :
			//recuperation de l'annonce
			$ref = $this->storproc('Boutique/Reference/LigneCommande/'.$lc->Id);
			$ref = genericClass::createInstance('Boutique',$ref[0]);

			//recuperation du vendeur
			$vend = $this->storproc('Boutique/Client/Reference/'.$ref->Id);
			$vend = genericClass::createInstance('Boutique',$vend[0]);
			// création de la facture de commision liée à la lignedecde
			$this->creeFactureCommission($lc,$vend,$ref->Id);
			
			$fccomm = $this->storproc('Boutique/LigneCommande/'.$lc->Id .'/FactureCommission');
			//Envoi du mail pour le vendeur avec le pdf
			$this->sendMailVendeur($vend,$ref,$fccomm);

		endforeach;
			//envoi du mail à l'acheteur
			//recuperation de l'acheteur
			$cli = $this->storproc('Boutique/Client/Commande/'.$this->Id);
			$cli = genericClass::createInstance('Boutique',$cli[0]);
			$this->sendMailAcheteur($cli,$this);
		
	}

	function creeFactureCommission ($lignecde,$vendeur,$annonce) {

		$commttc= 0;
		$comm= 0;
		$livcout=0;

		//On cherche le numéro de facture à incrémenter de 1
		$startTime = mktime(0, 0, 0, 1 , 1, date('Y'));
		$endTime = mktime(23, 59, 59, 12, 31, date('Y'));
		$anneeec = Utils::getDate(array('Y',mktime()));
		$numero = $this->storproc('Boutique/FactureCommission/tmsCreate>='. $startTime . '&tmsCreate<=' . $endTime,false,0,1,'','','COUNT(DISTINCT(m.Id))');
		$cpt = $numero[0]['COUNT(DISTINCT(m.Id))']+1;
		$faccommnum= "FC" . $anneeec . sprintf("%05d", $cpt)  ;

		// calcul de la commission
		$liv = $this->storproc('Boutique/TypeLivraison/Reference/'.$annonce);
		$livcout=$liv[0]['Cout'];
		$commttc=$lignecde->Montant+$livcout;
//		$comm= $this->getCommission($lignecde->Montant +$livcout)+0.70;
// je ne met pas les 0.70 car supporté par l'acheteur
		$comm= $this->getCommission($commttc );
		$commht= $comm /1.196;
		$comm=(round($commht*100))/100;
		// enregistrements
		$fc = genericClass::createInstance('Boutique','FactureCommission');
		$fc->Set('MontantHt',$comm);
		$fc->Set('TypeTva','19.6');
		$fc->Set('NumFactComm',$faccommnum);
		$fc->AddParent("Boutique/Client/".$vendeur->Id);
		$fc->AddParent("Boutique/LigneCommande/".$lignecde->Id);
		$fc->Save();

	
		
	}

	/**
	* Envoi du mail au vendeur l'informant de l'achat d'une de ses annonces
	* on y joint le pdf de la facture des commissions
	*/
	function sendMailVendeur($vend,$annonce,$factcomm) {
		require_once("Class/Lib/Mail.class.php");
		$Mail= new Mail();
		$Mail->Subject("[GAMES-AVENUE] Votre annonce".$annonce->Reference." vient d'etre validée");
		$Mail->From("noreply@games-avenue.com");
		$Mail->ReplyTo("noreply@games-avenue.com");
		$Mail->To("bug@expressiv.net");
		$Mail->To($vend->Mail);
		$dir = 'Home/FactureCommission';
		$filename = $dir . "/FactCommissionPdf_" . $factcomm[0]['NumFactComm'] . ".pdf";
		$Mail->Attach($filename, "pdf");
		$bloc = new Bloc();
		$mailContent = "
			Bonjour ".$vend->Prenom." ".$vend->Nom.",<br />
			Nous vous informons que votre article N° ".$annonce->Reference." ".$annonce->Nom." viens d'être vendu.<br />
			Nous vous rappelons, comme indiqué dans nos conditions générales, que vous avez 72h pour confirmer votre vente et 48h pour l'expédier.<br />
			Nous vous invitons à renseigner votre numéro de colis une fois celui-ci expédié.<br />
			Pour confirmer votre vente et avoir les coordonnées de votre client, merci de vous connecter sur www.games-avenue.com \"Mes ventes\".<br />
			Vous trouverez ci-joint une facture correspondante à la commission GamesAvenue.
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
	* Envoi du mail a l'acheteur l'informant que son achat a été prise en compte
	*/
	function sendMailAcheteur($ach,$commande) {
		require_once("Class/Lib/Mail.class.php");
		$Mail= new Mail();
		$Mail->Subject("[GAMES-AVENUE] Votre commande ".$commande->RefCommande." vient d'etre validée");
		$Mail->From("noreply@games-avenue.com");
		$Mail->ReplyTo("noreply@games-avenue.com");
		$Mail->To("bug@expressiv.net");
		$Mail->To($ach->Mail);
		$bloc = new Bloc();
		$mailContent = "
			Bonjour ".$ach->Prenom." ".$ach->Nom.",<br />
			Nous vous informons que votre commande N°".$commande->RefCommande." à bien été prise en compte et en attente de confirmation de votre vendeur <br />
			Vous recevrez un mail de confirmation d'expédition comprenant le Numéro de suivi de votre colis.<br />
			Lors de la réception de votre colis nous vous remercions de bien vouloir vous rendre sur www.games-avenue.com pour valider la bonne réception de
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
}










