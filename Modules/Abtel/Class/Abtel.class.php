<?php
class Abtel extends Module {
	/**
	 * Surcharge de la fonction init
	 * Avant l'authentification de l'utilisateur
	 * @void 
	 */
	static $_Entite;


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
		//chargement des variables globales par défaut pour le module entité
		//$this->initGlobalVars();
		//modification des valeurs skins si nécessaire
		$this->editSkinsValues();
	}
	
	/**
	 * Initilisation des variables globales disponibles pour l'entité
	 */
	function initGlobalVars(){
		//initialisation Entite si disponible
		Abtel::$_Entite = Entite::getCurrentEntite();
		$GLOBALS["Systeme"]->registerVar("CurrentEntite",Abtel::$_Entite);
		
		//Récupération de l'utilisateur par défaut de l'entité
		$sit = Abtel::$_Entite->getParents('Site');
		if (isset($sit[0])){
		    $sit = $sit[0];
		    //récupération de l'utilisateur
		    $usr = $sit->getParents('User');
		    $usr = $usr[0];
		    //on force la skin de l'entité en cours
		    $usr = Connection::initUser($usr);
		    Sys::setSkin($usr->Skin);
		    Sys::$User->Menus = $usr->Menus;
		    $GLOBALS["Systeme"]->Menus = $usr->Menus;
		}
	}
	
	/**
	 * Modification des données de la skin dans le cas ou un utilisateur se connecte sur une entité
	 * Avec une url différente de l'entité d'origine
	 */
	function editSkinsValues(){
	   //si la skin est différente à cause de la connexion d'un utilisateur
	   
	}

}
?>
