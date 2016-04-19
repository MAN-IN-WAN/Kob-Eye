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
    static function createReservation($date, $court, $heuredeb,$service){
        $res = genericClass::createInstance('TennisForever','Reservation');

        //récupération du client
        $cli = TennisForever::getCurrentClient();
        $res->setClient($cli);

        //vérification du court
        $court = Sys::getOneData('TennisForever','Court/'.$court);
        $res->setCourt($court);

        //définition du service
        if ($service>0) {
            $service = Sys::getOneData('TennisForever', 'Service/' . $service);
            $res->setService($service);
        }

        //definition de la date
        $res->setDate($date);

        //definition de l'heure de debut
        $res->setHeureDebut($heuredeb);

        return $res;
    }

    /**
     * getCurrentClient
     * Récupération du client
     */
    static function getCurrentClient() {
        return Sys::getOneData('TennisForever','Client/UserId='.Sys::$User->Id);
    }

}