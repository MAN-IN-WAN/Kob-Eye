<?php
class Formation extends Module {
    /**
     * Surcharge de la fonction init
     * Avant l'authentification de l'utilisateur
     * @void
     */
    static $_Session;


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
        //chargement des variables globales par défaut pour le module formation
        $this->initGlobalVars();
    }
    /**
     * Initilisation des variables globales disponibles pour la boutique
     */
    function initGlobalVars(){
        //initialisation magasin si disponible
        $T= Sys::getOneData('Formation','Session/EnCours=1');
        Formation::$_Session = $T;
        $GLOBALS["Systeme"]->registerVar("CurrentSession",Formation::$_Session);

    }
}