<?php
/**
 * Class Reservation
 */
class Reservation extends Module{
    private $_Organisation = null;

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

    private function initGlobalVars(){
        $grps = Sys::$User->Groups;
        foreach($grps as $grp){
            $struct = Sys::getOneData('Reservation','Client/NumeroGroupe='.$grp->Id);
            if(!empty($struct)){
                $this->_Organisation = $struct;
                $GLOBALS["Systeme"]->registerVar("ReservationClient",$this->_Organisation);
                break;
            }
        }

        //Redirection pour l'acces structures sociales
        if(!empty(Process::$TempVar['spectacle'])){
            header('Location:: /#/'.Sys::getMenu('Reservation/Spectacle').'/'.Process::$TempVar['spectacle'],true,302);
            die();
        }
    }
}