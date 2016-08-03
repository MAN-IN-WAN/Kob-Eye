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
        $GLOBALS["Systeme"]->registerVar("CurrentClient",TennisForever::getCurrentClient());
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
    /**
     * isDispoPiscine
     * Vérifie la disponibilité de la piscine
     */
    static function isDispoPiscine($date) {
        $co = 20;
        $res = Sys::getCount('TennisForever','Court/'.$co.'/Disponibilite/Debut>='.$date);
        return $res;
    }
    /**
     * getResaPiscine
     * Recupere le sinformations  de reservation de piscine.
     */
    static function getResaPiscine() {
        
        $co = 20;
        $res = Sys::getData('TennisForever','Court/'.$co.'/Reservation/DateDebut>='.Utils::getTodayMorning(array()).'&DateDebut<'.Utils::getTodayEvening(array()).'/LigneFacture');
        $ser = Sys::getData('TennisForever','Court/'.$co.'/Service');
        $out='';
        foreach ($ser as $s){
            $nbres = 0;
            foreach ($res as $r){
                if ($r->Id = $s->Id) $nbres++;
            }
            //on compte le nombre d'occurence
            $out.='<div style="overflow:hidden;"><span class="label label-warning" style="font-size:16px;font-weight: bold;text-align:center;float:right;background-color:darkred;width: 50px;height: 50px; display: block;line-height:50px;">'.$nbres.'</span>'.$s->Titre.'</div>';
        }
        return addslashes($out);
    }
    /**
     * getServices
     * Récupère tous les services par type de court et par court;
     */
    static function getServices() {
        $ser = Sys::getData('TennisForever','Service');
        $out = array();
        foreach ($ser as $s){
            //recherche des types de court
            $tcs = Sys::getData('TennisForever','TypeCourt/Service/'.$s->Id);
            $cs = Sys::getData('TennisForever','Court/Service/'.$s->Id);
            for ($i=0;$i<sizeof($tcs);$i++){
                $st = unserialize(serialize($s));
                $st->TypeCourtId = $tcs[$i]->Id;
                $out[] = $st;
            }
            //recherche des court
            for ($j=0;$j<sizeof($cs);$j++){
                $st = unserialize(serialize($s));
                $st->CourtId = $cs[$j]->Id;
                $out[] = $st;
            }
        }
        return $out;
    }
}