<?php

class Vetoccitan extends Module
{
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
     * Initilisation des variables globales disponibles pour la boutique
     */
    private function initGlobalVars()
    {
        if (is_object(Sys::$User) && !Sys::$User->Public) {
            //initialisation client si connecté
            $Cls = Sys::$User->getOneChild('Client');
            if ($Cls) {
                $adh  = Sys::getOneData('Vetoccitan',"Client/".$Cls->Id."/Adherent");
                if ($adh){
                    $GLOBALS["Systeme"]->registerVar("VetoAdh", $adh);
                }

            }
        }
    }


}