<?php
class Parc extends Module{
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
	}
        /**
         * Surcharge de la fonction Check
         * Vérifie l'existence du role PARC_CLIENT et son association à un groupe
         * Sinon génère le ROLE et créé un Group à la racine et lui affecte le ROLE
         */
        function Check () {
           parent::Check();
           //teste le role
           $r = Sys::getData('Systeme','Role/Title=PARC_CLIENT');
           if (sizeof($r)){
               $r = $r[0]; 
               //teste le groupe
               $g = $r->getChildren('Group');
               if (sizeof($g)){
                   //tout est ok 
               }else{
                    $this->createGroup($r);
               }
           }else{
                //il faut tout créer
                //création du role
                $r = genericClass::createInstance('Systeme','Role');
                $r->Title = "PARC_CLIENT";
                $r->Save();
                //création du groupe
                $this->createGroup($r);
           }
        }
        /**
         * Creation du groupe et de tout ses menus
         */
        private function createGroup($role){
            //creation du groupe 
            $g = genericClass::createInstance('Systeme','Group');
            $g->Nom = "[PARC] Accès clients";
            $g->Skin = "ParcClient";
            $g->AddParent($role);
            $g->Save();
            //création des menus
            $m = genericClass::createInstance('Systeme','Menu');
            $m->Titre = "Tableau de bord";
            $m->Alias = "Systeme/User/DashBoard";
            $m->AddParent($g);
            $m->Save();
        }

	/**
	 * Initilisation des variables globales disponibles pour la boutique
	 */
	private function initGlobalVars(){
                if (!Sys::$User->Public){
                    //initialisation client si connecté
                    $Cls = Sys::$User->getChildren('Client');
                    if (sizeof($Cls)){
                        $this->_ParcClient = $Cls[0];
                        $GLOBALS["Systeme"]->registerVar("ParcClient",$this->_ParcClient);
                    }
                }
	}
}
