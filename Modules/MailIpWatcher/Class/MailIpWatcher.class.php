<?php
class MailIpWatcher extends Module{
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
        //$this->initGlobalVars();
    }
    /**
     * Surcharge de la fonction Check
     * Vérifie l'existence du role PARC_CLIENT et son association à un groupe
     * Sinon génère le ROLE et créé un Group à la racine et lui affecte le ROLE
     */
    function Check () {
        parent::Check();
        //creation de la tache planifiée
        $t = Sys::getCount('Systeme','ScheduledTask/TaskModule=MailIpWatcher&TaskObject=IPWatcher&TaskFunction=checkAllIp');
        if (!$t) {
            //creation du groupe public
            $t = genericClass::createInstance('Systeme', 'ScheduledTask');
            $t->Titre = 'Execution IPWatcher toutes les minutes';
            $t->Enabled = 1;
            $t->TaskModule = 'MailIpWatcher';
            $t->TaskObject = 'IPWatcher';
            $t->TaskFunction = 'checkAllIp';
            $t->Save();
        }
    }
 }