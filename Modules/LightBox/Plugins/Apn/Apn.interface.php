<?php

/*********************************************
*
* Interface pour plugin
* LightBox / Apn
* Abtel
* 
*********************************************/


interface LightBoxApnPlugin {
    /**
     * getStatus
     * retourne l'état
     */
    function getStatus();

    /**
     * checkWifiExists
     * @return bool
     * Vérifie si le SSID est bien enregistré en tant que connexion connue
     */
    function checkWifiExists();

    /**
     * addWifi
     * Ajout la connexion Wifi
     */
    function addWifi();

    /**
     * checkConnected
     * Vérifie si le wifi s'est connecté automatiquement
     * Active automatiquement l'appareil photo concerné
     */
    function checkConnected();

    /**
     * Reinitialise l'état de l'appareil
     * @return mixed
     */
    function reset();

    /**
     * Reinitialise l'état de l'API
     * @param bool $silent
     * @return mixed
     */
    function resetApi($silent = false);

    /**
     * connectApi
     * Connecte à l'api de l'appareil photo
     */
    function connectApi();

    /**
     * checkApiConnected
     * Vérifie si l'api est bien connecté et que l'on a bien l'url
     */
    function checkApiConnected();

    /**
     * checkLiveViewProxy
     * Vérifie si le proxy est bien lancé.
     */
    function checkLiveViewProxy();

    /**
     * deleteConnexion
     * Supprime la connexion enregistrée
     */
    function deleteWifi();

    /*********************
     * checkState
     * Vérifie l'état de la connexion à l'appareil photo.
     * Un appel toutes les minutes
     * Il faut vérifier toutes les 5 secondes
     */
    function checkStateApn();

    /**
     * setConfig
     * Configure l'appareil
     */
    function setConfigApn();
    /**
     * startLiveView
     * Demarre le proxy liveview
     */
    function startLiveView();
    /**
     * stopLiveView
     * Stoppe le liveview
     * En mode python pour l'instant
     */
    function stopLiveView();

    /**
     * startRecMode
     * Met l'appareil pĥoto en mode enregistrement
     * @return mixed
     */
    function startRecMode();

    /**
     * stopRecMode
     * Met l'appareil pĥoto en mode veille
     * @return mixed
     */
    function stopRecMode();

    /**
     * takePhoto
     * Prend une photo, la télécharge et la stocke
     * @return mixed
     */
    function takePhoto();

}