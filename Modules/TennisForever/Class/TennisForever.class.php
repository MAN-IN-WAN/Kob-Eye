<?php
class TennisForever extends Module {
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
        define('TENNISFOREVER_CLIENT_GROUP',4);
    }
    /**
     * Modification des données de la skin dans le cas ou un utilisateur se connecte sur un magasin
     * Avec une url différente du magasin d'origine
     */
    function editSkinsValues(){
        //si la skin est différente à cause de la connexion d'un utilisateur
    }


    function Check() {
        parent::Check();
        // Vérification de l'existence d'une devise par Defaut
    }

    static function confirmEmail($code){
        //recherche utilisateur
        $u = Sys::getOneData('Systeme','User/CodeVerif='.$code);
        if ($u){
            //recherche client
            $c = Sys::getOneData('TennisForever','Client/UserId='.$u->Id);
            if ($c){
                return $c->confirmAccount();
            }
		}
        return false;
    }
    static function  getCurrentClient(){
        return Sys::getOneData('TennisForever','Client/UserId='.Sys::$User->Id);
    }
}