<?php

/**
 * Ajout d'un utilisateur automatique pour un Client
 *
 */
class Client extends genericClass {

  var $Pass;
  var $Panier;

	/**
	* Créé un mot de passe aléatoire pour l'utilisateur.
	*
	* @return string
	*/
	function makePassword($i=0){
		if ($i==8) return "";
		else{
			$Caracteres = "azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";
			$Nombres = "0123456789";
			$CharOuNbr = rand(0,1);
			if ($CharOuNbr == 0){
				$Char = rand(0,strlen($Caracteres)-1);
				return $Caracteres[$Char] . $this->makePassword($i+1);
			}else{
				$Nbr = rand(0,strlen($Nombres)-1);
				return $Nombres[$Nbr] . $this->makePassword($i+1);
			}
		}
	}

	function changePassword(){
		$Pass = $this->makePassword();
		$Utilisateur=$this->getUser();
		$Utilisateur->Set("Pass",$Pass);
		$Utilisateur->Save();
		return $Pass;
	}

	/**
	* Renvoie le mot de passe précédemment affecté à $this->Pass.
	*
	* @return string
	*/
	function getPass(){
		return $this->Pass;
	}
	/**
	* Créé l'utilisateur correspondant au client
	*
	* @return Objet:Systeme/Utilisateur
	*/
	function makeUser() {
		// on enleve la création du pass car il est saisi
		//$this->Pass = $this->makePassword();
		$Utilisateur = genericClass::createInstance("Systeme","User");
		$Utilisateur->Set("Nom",$this->Get("Nom")); 
		$Utilisateur->Set("Prenom",$this->Get("Prenom")); 
		$Utilisateur->Set("Mail",$this->Get("Mail"));
		$Utilisateur->Set("Login",$this->Get("Mail"));
		$Utilisateur->Set("Adresse",$this->Get("Adresse"));
		$Utilisateur->Set("Tel",$this->Get("Tel"));
		$Utilisateur->Set("CodPos",$this->Get("CodePostal"));
		$Utilisateur->Set("Ville",$this->Get("Ville"));
		$Utilisateur->Set("Adresse",$this->Get("Adresse"));
		$Utilisateur->Set("Pays",$this->Get("Pays"));
//		$Utilisateur->Set("Pass",$this->Get("Pass"));
		$Utilisateur->Set("Pass",$this->Pass);
		$Utilisateur->Set("Admin","0");
//		$Utilisateur->AddParent("Systeme/Group/3");
		//$GrpUser = $this->storproc('Boutique/Magasin/1',false,0,1);
		$mag = Magasin::getCurrentMagasin();
		$GrpUser = $this->storproc('Boutique/Group/Magasin/'. $mag->Id,false,0,1);
		$Utilisateur->AddParent("Systeme/Group/".$GrpUser[0]['Id']);
		return $Utilisateur;
	}

	function updateUser($Utilisateur=null) {
		if($Utilisateur == null) $Utilisateur = $this->getUser();
		if($Utilisateur == null) return;
		$Utilisateur->Set("Nom",$this->Get("Nom")); 
		$Utilisateur->Set("Prenom",$this->Get("Prenom")); 
		$Utilisateur->Set("Mail",$this->Get("Mail"));
		$Utilisateur->Set("Login",$this->Get("Mail"));
		$Utilisateur->Set("Adresse",$this->Get("Adresse"));
		$Utilisateur->Set("Tel",$this->Get("Tel"));
		$Utilisateur->Set("CodPos",$this->Get("CodePostal"));
		$Utilisateur->Set("Ville",$this->Get("Ville"));
		$Utilisateur->Set("Adresse",$this->Get("Adresse"));
		$Utilisateur->Set("Pays",$this->Get("Pays"));
// ajout du pass à la modification et du coup on se reconnecte à chaque modification du user
		if($this->Pass != ''){
		  $Utilisateur->Set("Pass",$this->Pass);
		}
		$Utilisateur->Set("Admin","0");
//		$Utilisateur->AddParent("Systeme/Group/3");
		$mag = Magasin::getCurrentMagasin();
		$GrpUser = $this->storproc('Boutique/Group/Magasin/'. $mag->Id,false,0,1);
		$Utilisateur->AddParent("Systeme/Group/".$GrpUser[0]['Id']);
		$Utilisateur->Save();
		return $Utilisateur;
	}
	/**
	* Créé l'utilisateur correspondant à la personne
	*
	* @return Objet:Systeme/Utilisateur
	*/
	function initFromUser() {
		$U = Sys::$User;
		$this->Nom = $U->Nom;
		$this->Prenom = $U->Prenom;
		$this->Mail = $U->Mail;
		$this->Login = $U->Login;
		$this->Adresse = $U->Adresse;
		$this->Tel = $U->Tel;
		$this->CodePostal = $U->CodePos;
		$this->Ville = $U->Ville;
		$this->Pays = $U->Pays;
	}

	/**
	* Ajoute la vérification appdes paramètres utilisateur (surtout les mails)
	*
	* @return bool
	*/
	function Verify($need_user=0) {
		if ($need_user==1) {
			if (!empty($this->Utilisateur))$Utilisateur = $this->getUser();
			else $Utilisateur = $this->makeUser();
			$Utilisateur->Verify();
			genericClass::Verify();
			//$this->Error = $Utilisateur->Error;
			$Errors = Array();
			if (isset($Utilisateur->Error) && is_array($Utilisateur->Error))foreach ($Utilisateur->Error as $E){

				$f= false;
				foreach ($Errors as $e)if ($e["Prop"]==$E["Prop"])$f=true;
				if (!$f)$Errors[] = $E;
			}
			if (isset($this->Error)&&is_array($this->Error))foreach ($this->Error as $E){
				$f= false;
				foreach ($Errors as $e)if ($e["Prop"]==$E["Prop"])$f=true;
				if (!$f)$Errors[] = $E;
			}
			$this->Error = $Errors;
			return !sizeof($this->Error);
		}
		return genericClass::Verify();
	}
	
	function getUser(){
		$U = Sys::$Modules["Systeme"]->callData("Systeme/User/".$this->UserId);
		return genericClass::createInstance('Systeme',$U[0]);
	}	
	/**
	* Verification supplementaire pour validation du dossier 
	*
	* @return boolean
	*/
	function isCorrect() {
		$Result = true;
		$Name = $this->Prenom . " " . $this->Nom;
		if ( empty($this->Pays) ) {
			$Result = false;
			$this->addError(array("Message" => "Le pays de ". $Name . " n'est pas renseign&eacute;") );
		}
		if ( empty($this->DateNaissance) ) {
			$Result = false;
			$this->addError(array("Message" => "La date de naissance de ". $Name . " n'est pas renseign&eacute;") );
		}
				return $Result;
	}
	/**
	* Surcharge de l'enregistrement pour ajouter un utilisateur 
	*
	* @return bool
	*/
	function Save($need_user=0) {
		if($this->Id!="") {
			if($need_user==1 && $this->Verify(1) && $this->UserId!="") {
				$Utilisateur = $this->getUser();
				$Utilisateur = $this->updateUser($Utilisateur);
				$Utilisateur->Save();
			}
			elseif($need_user==1 && $this->Verify(1) && $this->UserId=="") {
				$createUser = 1;
				$Utilisateur = $this->makeUser();
				$Utilisateur->Save();
				$this->Set("UserId",$Utilisateur->Get("Id"));
			}
		}
		else {
			if ($need_user==1 && $this->Verify(1)) {
				$createUser = 1;
				$Utilisateur = $this->makeUser();
				$Utilisateur->Save();
			}
			if ( $need_user==1 ) {
				$this->Set("UserId",$Utilisateur->Get("Id"));
			}
		}
		genericClass::Save();
		return true;
	}





	/***************************************** GESTION PANIER ***************************************/


	/**
	 * Permet de récupérer le panier en cours
	 * @return	Panier en cours
	 */
	public function getPanier() {
		if(!is_object($this->Panier)) $this->initPanier();
		//$this->Panier->recalculer();
		return $this->Panier;
	}

	/**
	 * Initialise le panier quand nécessaire
	 * -> Si client connecté et commande brouillon on prend celle ci
	 * -> Si client connecté et pas de commande brouillon on en créé une dans la BDD
	 * -> Si client non connecté on créé une commande brouillon dans le cookie
	 * @return	void
	 */
	private function initPanier() {
		if($this->Id) {
			//Client connecté
			if (isset($_SESSION['KEBoutiquePanier'])){
				//Le client vient de se connecter
				//récupération du panier dans le cookie
				$this->Panier = @unserialize($_SESSION['KEBoutiquePanier']);
				$this->Panier->Current=true;
				$GLOBALS["Systeme"]->Connection->rmSessionVar('KEBoutiquePanier');
				if (!isset($this->Panier->LignesCommandes)||!is_array($this->Panier->LignesCommandes)||!sizeof($this->Panier->LignesCommandes)){
					//si panier vide alors on supprime le panier
					$this->Panier=null;
				}else $this->savePanier();
			}
			if (!is_object($this->Panier)){
				//Par défaut on récupère la commande par defaut
				$B = $this->storproc('Boutique/Client/'.$this->Id.'/Commande/Current=1&Paye=0&PaymentPending=0',false,0,1,'DESC','tmsCreate');
				if(isset($B[0])) {
					$this->Panier = genericClass::createInstance('Boutique',$B[0]);
					$this->Panier->initFromBDD();
				}else{
					//Si pas de commande par defaut on recherche la commande non paye, non valide et on la définit par defaut
					$C = $this->storproc('Boutique/Client/'.$this->Id.'/Commande/Paye=0&Valide=0',false,0,1,'DESC','tmsCreate');
					if(isset($C[0])) {
						$this->Panier = genericClass::createInstance('Boutique',$C[0]);
						$this->Panier->initFromBDD();
						$this->Panier->Current=true;
					}else {
						//sinon on en crée une nouvelle
						$this->Panier = genericClass::createInstance('Boutique','Commande');
						$this->Panier->Current=1;
						$this->Panier->addParent($this);
					}
				}
			}
		}
		else {
			//client non connecté
			if(isset($_SESSION['KEBoutiquePanier'])) {
				$this->Panier = @unserialize($_SESSION['KEBoutiquePanier']);
			}
			else {
				$this->Panier = genericClass::createInstance('Boutique','Commande');
				$GLOBALS["Systeme"]->Connection->addSessionVar('KEBoutiquePanier', $this->Panier);
			}
		}
	}

	/**
	 * Supprime toute trace de panier pour cet utilisateur 
	 * et en créé un nouveau
	 * @return	void
	 */
	public function	viderPanier() {
		if($this->Id) {
			if (!isset($this->Panier)) $this->getPanier();
			// Si connecté : supprime le panier existant
			$this->Panier->Delete();
			// Créé un panier vide
			$this->Panier = genericClass::createInstance('Boutique','Commande');
			// ... puis enregistre le nouveau
			$this->Panier->AddParent($this);
			$this->Panier->Save();
		}else {
			// Créé un panier vide
			$this->Panier = genericClass::createInstance('Boutique','Commande');
			// Sinon : panier vide dans la session
			$GLOBALS["Systeme"]->Connection->addSessionVar('KEBoutiquePanier', $this->Panier);
		}
		return true;
	}
	
	/**
	 * Ajoute un produit au panier
	 * Si client connecté on enregistre dans la BDD
	 * @param	Id	Référence produit
	 * @param	int		Quantité à ajouter
	 * @return	void
	 */
	public function ajouterAuPanier( $refReference, $qte , $config=null,$options=null) {
		$lc = genericClass::createInstance('Boutique','LigneCommande');
		$lc->InitFromReference($refReference, $qte, $config,$options);
		$this->getPanier();
		$o = $this->Panier->ajouterLigneCommande( $lc );
		$this->savePanier();
		if ($o===true)return true;
		else {
		  return $this->Panier->Error;
		}
	}

	/**
	 * Définit une quantité précise pour une ligne de commande
	 * @param	string	Référence du produit que l'on update
	 * @param	int		Nouvelle quantité à attribuer
	 * @return	void
	 */
	public function ajusterQtePanier( $refReference, $nouvelleQte ) {
		$o = $this->getPanier()->ajusterQtePanier( $refReference, $nouvelleQte );
		$this->savePanier();
		return $o;
	}

	/**
	 * Retire un produit au panier
	 * Si client connecté on enregistre dans la BDD
	 * @param	object	Référence produit
	 * @return	void
	 */
	public function enleverDuPanier( $refReference ) {
		$o = $this->getPanier()->enleverLigneCommande( $refReference );
		$this->savePanier();
		return $o;
	}

	/**
	 * Enregistre l'état du panier
	 * -> Soit dans la BDD si connecté
	 * -> Soit dans la session si non connecté
	 * @return void
	 */
	public function savePanier() {
		//enregistrement du panier
		if($this->Id) {
			$this->Panier->AddParent($this);
			$this->Panier->Save();
		}
		else {
			$GLOBALS["Systeme"]->Connection->addSessionVar('KEBoutiquePanier', $this->Panier);
		}
	}

	/**
	 * Renvoie la derniere commande en cours de validation
	 * @return	Commande valide ou false
	 */
	public function getCurrentCommande() {
		if (!$this->Id) return;
		$C = $this->storproc('Boutique/Client/'.$this->Id.'/Commande/Valide=1&&Paye=0',false,0,1,'DESC','Id');
		if (is_array($C)&&is_array($C[0])){
			$com = genericClass::createInstance('Boutique',$C[0]);
			$com->initFromBDD();
			return $com;
		}else return false;
	}
	/**
	 * switchCommande
	 * Modifie la commande en cours pour une autre
	 * @param reference commande
	 * @return void 
	 */
	 public function switchCommande($com) {
	 	//recherche commande
		$C = $this->storproc('Boutique/Client/'.$this->Id.'/Commande/RefCommande='.$com,false,0,1,'DESC','Id');
		if (is_array($C)&&is_array($C[0])){
			$com = genericClass::createInstance('Boutique',$C[0]);
			$com->initFromBDD();
			if ($com->getStatus()<4){
				//reinitialisation du statut
			 	$com->PaymentPending = 0;
			 	$com->EchecPayment = 0;
			 	$com->Current = 1;
				$com->setUnValid();
				$com->Save();
			}
		}
	 }
	 
	/**
	 * cancelCommande
	 * Annule une commande
	 * @param reference commande
	 * @return void 
	 */
	 public function cancelCommande($com) {
	 	//recherche commande
		$C = $this->storproc('Boutique/Client/'.$this->Id.'/Commande/RefCommande='.$com,false,0,1,'DESC','Id');
		if (is_array($C)&&is_array($C[0])){
			$com = genericClass::createInstance('Boutique',$C[0]);
			$com->initFromBDD();
			if ($com->getStatus()<4){
				$com->Delete();
			}
		}
	 }

	/**
	 * Renvoie toutes les commandes valides mais non cloturées
	 * @return	Commande valide ou false
	 */
	public function getPendingCommandes() {
		if (!$this->Id) return;
		$out = Array();
		$C = $this->storproc('Boutique/Client/'.$this->Id.'/Commande/Valide=1&Cloture=0&Current=0',false,0,10,'DESC','Id');
		if (is_array($C)&&is_array($C)){
			foreach ($C as $co){
				$com = genericClass::createInstance('Boutique',$co);
				$com->initFromBDD();
				$out[]=$com;
			}
			return $out;
		}else return false;
	}
	
	
	/**
	 * Renvoie toutes les commandes non valides
	 * @return	Commande valide ou false
	 */
	public function getOtherPanier() {
		if (!$this->Id) return;
		$out = Array();
		$C = $this->storproc('Boutique/Client/'.$this->Id.'/Commande/Valide=0&Current=0',false,0,10,'DESC','Id');
		if (is_array($C)&&is_array($C)){
			foreach ($C as $co){
				$com = genericClass::createInstance('Boutique',$co);
				$com->initFromBDD();
				$out[]=$com;
			}
			return $out;
		}else return false;
	}
	
	/**
	 * Renvoie la derniere commande payée
	 * @return	Commande payée ou false
	 */
	public function getLastCommande() {
		$C = $this->storproc('Boutique/Client/'.$this->Id.'/Commande/Valide=1',false,0,1,'DESC','DateCommande');
		if (is_array($C)&&is_array($C[0])) return genericClass::createInstance('Boutique',$C[0]);
		else return false;
	}

	/**
	 * Renvoie toutes les commandes
	 * @return	tableau des commandes
	 */
	public function getAllCommandes() {
		$C = $this->storproc('Boutique/Client/'.$this->Id.'/Commande',false,0,1000,'DESC','Id');
		$out = Array();
		if (is_array($C)&&is_array($C[0])){
			foreach ($C as $co)
				array_push($out, genericClass::createInstance('Boutique',$co));
		} 
		return $out;
	}

	/**
	 * Retourne le nombre de ventes pour ce client
	 * @return	Nombre
	 */
	public function getNbVentes() {
		$ventes = $this->storproc('Boutique/Client/'.$this->Id.'/LigneCommande/Expedie=1',false,0,1,'','','COUNT(DISTINCT(m.Id))');
		return $ventes[0]['COUNT(DISTINCT(m.Id))'];
	}
	
	/**
	 * Retourne les offres spéciales pour une commande
	 * @return	array
	 */
	public function getOffreSpeciale() {
		$offrespec = $this->storproc('Boutique/Client/'.$this->Id.'/CodePromo/TypeOffre=2&Actif=1&DateDebut<'. time() .'&DateFin>' .time(),false,0,1000);
		if (is_array($offrespec)) foreach ($offrespec as $K => $Os ) {
			$offrespec[$K] = genericClass::createInstance('Boutique',$Os);
		}
		return $offrespec;
	}
	
	
	
	
	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return Sys::$Modules['Boutique']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}
  
	/**
	 * getCurrentClient
	 * Fonction statique qui récupère le client connecté */
	public static function getCurrentClient($leclient=false) {
	      //si utilisateur public alors pas de client courrant
	      if (Sys::$User->Public) return false;
	      if ($leclient) {
				$cl = Sys::getData('Boutique','Client/UserId='.$leclient);
	      } else {
	          $cl = Sys::getData('Boutique','Client/UserId='.Sys::$User->Id);
	      }

	      if (isset($cl[0])){
		  $cl = $cl[0];
		  $cl->_Remises = array();
		  //remise Abonnement
		  $m = Magasin::getCurrentMagasin();
		  $abo = $cl->getChildren('Service/Service.MagasinId('.$m->Id.')');
	 	  // JANVIER 2105 : on teste la validité de l'abonnement pour affecter la remise client d'un abonnement...
		  if (sizeof($abo)){
			foreach($abo as $abonn){
				if($abonn->DateFin > time()){
					$cl->_Remises["Abonnement ".$abonn->Nom." ".$cl->Nom." ".$cl->Prenom] = $abonn->Remise;
				}
			}
		  } 
		  //remise client relative client
		  if ($cl->Remise>0){
			  $cl->_Remises["Client ".$cl->Nom." ".$cl->Prenom] = $cl->Remise;
		  }
		  //Récupération de ses groupes de clients
		  $cl->_GroupClients = $cl->getParents('GroupClient');
		  foreach ($cl->_GroupClients as $gr){
			  //récupération des remises relative aux groupes
			  if ($gr->Remise>0){
				  $cl->_Remises["Groupe ".$gr->Nom] = $gr->Remise;
			  }
		  }
 		  //Récupération de ses règles relatives
		  $cl->_RegleRemises = $cl->getChildren('RegleRemise');
		  foreach ($cl->_GroupClients as $gr){
	    		  $cl->_RegleRemises = array_merge($cl->_RegleRemises,$gr->getChildren('RegleRemise'));
		  }
		  //initilisation des règles
		  foreach ($cl->_RegleRemises as $k=>$rr){
			  $rr->initCategoryProducts();
			  $cl->_RegleRemises[$k] = $rr;
		  }
		  return $cl;
	      }else return false;
        }

	/**
	 * checkRemiseProduit
	 * vérification d'une remise éventuelle pour le produit fournit en paramètre
	 */
	public function checkRemiseProduit($prod,$qte=1) {
	      $remises = array();

        //TODO PERFORMANCE HIT !!
	      //pour chaque règle on teste le produit
	      foreach ($this->_RegleRemises as $rr){
		    if ($rr->checkProduct($prod,$qte)){
			$remises  = array_merge($remises,array("Règle Produit ".$prod->Nom => $rr->Remise));
		    }
	      }
	      //pour chaque catégorie du produit on teste les regles
	      $cats = $prod->getParents('Categorie/*/Categorie');
	      foreach ($cats as $c){
		    foreach ($this->_RegleRemises as $rr){
			  if ($rr->checkCategory($c,$qte)){
			      $remises  = array_merge($remises,array("Règle Catégorie ".$c->Nom => $rr->Remise));
			  }
		    }	
      	      }
	      return $remises;
	}

	// Mars 2015 function qui permet d'initialiser un tableau de taux de tva à utiliser
	// dans le contexte	
	function clientTableauTva() {

		//INIT DES TAUX UTILISABLES 
		// ATTENTION IL FAUDRA AJUSTÉ CE SYSTEME POUR LES PROFESSIONNELS !!!!!!!!!!
		// recherche la zone fiscale du client connecté
		$adrclient= Sys::getData('Boutique','Client/' . $this->Id .'/Adresse/Type=Livraison&Default=1');
		// on recherche adresse de Livraison
		if (sizeof($adrclient)) {
			$lazone=ZoneFiscale::getZone($adrclient[0]->Pays,$adrclient[0]->CodePostal);
		} else {
			// si pas d'adresse trouvée on prend celle du client
			$lazone=ZoneFiscale::getZone($this->Pays,$this->CodePostal);
		}
        if (!is_object($lazone)) return;
		$tauxtva= Sys::getData('Fiscalite','ZoneFiscale/' .$lazone->Id .'/TauxTva/Actif=1&Debut<='. time().'&Fin>='.time() );
		$tabarray=array();
		if (sizeof($tauxtva)) {
			foreach ($tauxtva as $t) {
				$type = $t->getParents('TypeTva');
				if (sizeof($type)) {
					$tabarray[$type[0]->Id] = $t->Taux;
				} 
				// prévoir le renvoie d'une erreur
			}
			//$GLOBALS["Systeme"]->registerVar("TX_TVA", $tabarray);
		}
		return($tabarray);

	}

}
