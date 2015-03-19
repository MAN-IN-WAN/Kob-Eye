<?php
class Boutique extends Module {
	/**
	 * Surcharge de la fonction init
	 * Avant l'authentification de l'utilisateur
	 * @void 
	 */
	static $_Client;

	function init (){
		parent::init();
	}
	/**
	 * Surcharge de la fonction postInit
	 * Après l'authentification de l'utilisateur
	 * Toutes les fonctionnalités sont disponibles
	 * @void 
	 */
	function postInit (){
		parent::postInit();
		//chargement des variables globales par défaut pour le module boutique
		$this->initGlobalVars();
		//modification des valeurs skins si nécessaire
		$this->editSkinsValues();
	}
	/**
	 * Initilisation des variables globales disponibles pour la boutique
	 */
	function initGlobalVars(){
		//initialisation magasin si disponible
		$this->_Magasin = Magasin::getCurrentMagasin();
		$GLOBALS["Systeme"]->registerVar("CurrentMagasin",$this->_Magasin);
		
		//initilisation devise
		$T= Sys::getData('Boutique','Devise/Defaut=1');
		$this->_Devise=$T[0];
		$GLOBALS["Systeme"]->registerVar("CurrentDevise",$this->_Devise);
		
		//initialisation client si disponible
		if (Boutique::$_Client = Client::getCurrentClient()){
			$GLOBALS["Systeme"]->registerVar("CurrentClient",Boutique::$_Client);
			//Récupération de l'utilisateur par défaut du magasin
			$sit = $this->_Magasin->getParents('Site');
			if (isset($sit[0])){
				$sit = $sit[0];
				//récupération de l'utilisateur
				$usr = $sit->getParents('User');
				$usr = $usr[0];
				//on force la skin du magasin en cours
				$usr = Connection::initUser($usr);
				Sys::setSkin($usr->Skin);
				Sys::$User->Menus = $usr->Menus;
				$GLOBALS["Systeme"]->Menus = $usr->Menus;
			}
		}

		Boutique::initTableauTva();


	}
	/**
	 * Modification des données de la skin dans le cas ou un utilisateur se connecte sur un magasin
	 * Avec une url différente du magasin d'origine
	 */
	 function editSkinsValues(){
	 	//si la skin est différente à cause de la connexion d'un utilisateur
	 	
	 }

	static function initTableauTva() {
		if (!Sys::$User->Public&&is_object(Boutique::$_Client)){
			// on a un client on va chercher les taux correspondants
			$tabarray=Boutique::$_Client->clientTableauTva();
			$GLOBALS["Systeme"]->registerVar("TX_TVA", $tabarray);
		} else {
			// zone fiscale par defaut
			$tauxtva= Sys::getData('Fiscalite','ZoneFiscale/Default=1/TauxTva/Actif=1&Debut<='. time().'&Fin>='.time() );
			if (sizeof($tauxtva)) {
				foreach ($tauxtva as $t) {
					$type = $t->getParents('TypeTva');
					if (sizeof($type)) {
						$tabarray[$type[0]->Id] = $t->Taux;
					} 
				}
				$GLOBALS["Systeme"]->registerVar("TX_TVA", $tabarray);
			}
		}
	}

	function Check() {
		parent::Check();
		// Vérification de l'existence d'une devise par Defaut
		if (!Sys::getCount('Boutique','Devise/Defaut=1')) {
			$D=genericClass::createInstance('Boutique','Devise');
			$D->Nom = 'Euro';
			$D->Taux = '1';	
			$D->Sigle= '€';
			$D->Default= '1';
			$D->Save();
		}
		
		//Maj des lignes de commande pour les commandes dont la facture n'a pas encore été créée pour coller au nouveau systeme.
		$commSansFac = Sys::getData('Boutique','Commande/Valide=1&Paye=0'); //Recup des commandes sensées n'avoir aucune facture
		foreach($commSansFac as $comm){
			//On vérifie qu'il n'y a bien aucune facture
			$fac = $comm->getChildren('Facture');
			if(count($fac)) continue;
			
			//On recup les lignes de commande
			$lcs = $comm->getChildren('LigneCommande');
			//On les recalcule
			foreach($lcs as $lc){
				$lc->Recalculer();
				$lc->Save();
			}
		}

	}
}
?>
