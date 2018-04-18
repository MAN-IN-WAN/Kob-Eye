<?php

/*********************************************
*
* Interface pour plugin
* Boutique / Instance
* Abtel
* 
*********************************************/


interface ParcInstancePlugin {

    /**
     * createInstallTask
     * Creation de la tach d'installation du secib web
     */
    public function createInstallTask();
    /**
     * installSecibWeb
     * Fonction d'installation ou de mise à jour de secib web
     * @param Object Tache
     */
    public function installSoftware($task = null);
}