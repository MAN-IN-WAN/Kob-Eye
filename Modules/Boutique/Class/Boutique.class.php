<?php
class Boutique extends Module {
	/**
	 * Surcharge de la fonction init
	 * Avant l'authentification de l'utilisateur
	 * @void 
	 */
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
		
		//initialisation client si disponible
		if ($this->_Client = Client::getCurrentClient()){
			$GLOBALS["Systeme"]->registerVar("CurrentClient",$this->_Client);
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
		//initialisation taxe si disponible
	}
	/**
	 * Modification des données de la skin dans le cas ou un utilisateur se connecte sur un magasin
	 * Avec une url différente du magasin d'origine
	 */
	 function editSkinsValues(){
	 	//si la skin est différente à cause de la connexion d'un utilisateur
	 	
	 }
}
?>
