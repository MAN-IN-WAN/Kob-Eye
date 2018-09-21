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
     * postInit
     * Initialisation du plugin
     */
    public function postInit();
    /**
     * Delete
     * Suppression des éléments spécifiques
     */
    public function Delete();
    /**
     * createInstallTask
     * Creation de la tach d'installation du logiciel
     */
    public function createInstallTask();
    /**
     * installSoftware
     * Fonction d'installation ou de mise à jour de l'applicatif
     * @param Object Tache
     */
    public function installSoftware($task = null);
    /**
     * createUpdateTask
     * Creation de la tache de mise à jour du logiciel
     */
    public function createUpdateTask();
    /**
     * updateSoftware
     * Fonction de mise à jour de l'applicatif
     * @param Object Tache
     */
    public function updateSoftware($task = null);
    /**
     * checkState
     * Vérifie l'état d'une instance
     */
    public function checkState();
    /**
     * rewriteConfig
     * Réécrire la configuration
     */
    public function rewriteConfig();
}