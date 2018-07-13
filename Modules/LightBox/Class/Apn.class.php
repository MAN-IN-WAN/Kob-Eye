<?php
class Apn extends genericClass{
    private $_plugin = null;
    /**
     * RawSave
     * Save sans callback
     */
    function RawSave(){
        parent::Save();
    }
    /**
     * Save
     * Surcharge de Save
     */
    function Save(){
        if ($this->Id)
            $old = Sys::getOneData('LightBox','Apn/'.$this->Id);
        else $old = genericClass::createInstance('LightBox','Apn');
        //Véirification de l'activation
        if (!$old->Actif&&$this->Actif){
            //alors activation de l'appareil et désactivation de l'autre
            $autre = Sys::getOneData('LightBox','Apn/Actif=1');
            if ($autre) {
                $autre->Actif = false;
                $autre->Connecte = false;
                $autre->Save();
            }
        }
        parent::Save();
        //vérification du wifi
        if (!$this->checkWifiExists()) $this->addWifi();
        return true;
    }
    /**
     * Retourne un plugin LightBox / Apn
     * @return	Implémentation d'interface
     */
    public function getPlugin() {
        if ($this->_plugin)return $this->_plugin;
        $this->_plugin = Plugin::createInstance('LightBox','Apn', $this->Plugin);
        $this->_plugin->setConfig( $this->PluginConfig );
        $this->_plugin->init($this);
        return $this->_plugin;
    }
    /**
     * getStatus
     * retourne l'état
     */
    function getStatus() {
        $plugin = $this->getPlugin();
        return $plugin->getStatus();
    }
    /**
     * checkWifiExists
     * @return bool
     * Vérifie si le SSID est bien enregistré en tant que connexion connue
     */
    function checkWifiExists(){
        $plugin = $this->getPlugin();
        return $plugin->checkWifiExists();
    }
    /**
     * addWifi
     * Ajout la connexion Wifi
     */
    function addWifi(){
        $plugin = $this->getPlugin();
        return $plugin->addWifi();
    }
    /**
     * checkConnected
     * Vérifie si le wifi s'est connecté automatiquement
     * Active automatiquement l'appareil photo concerné
     */
    function checkConnected() {
        $plugin = $this->getPlugin();
        return $plugin->checkConnected();
    }
    function reset() {
        $plugin = $this->getPlugin();
        return $plugin->reset();
    }
    function resetApi($silent = false) {
        $plugin = $this->getPlugin();
        return $plugin->resetApi($silent);
    }
    /**
     * connectApi
     * Connecte à l'api de l'appareil photo
     */
    function connectApi() {
        $plugin = $this->getPlugin();
        return $plugin->connectApi();
    }
    /**
     * checkApiConnected
     * Vérifie si l'api est bien connecté et que l'on a bien l'url
     */
    function checkApiConnected(){
        $plugin = $this->getPlugin();
        return $plugin->checkApiConnected();
    }
    /**
     * checkLiveViewProxy
     * Vérifie si le proxy est bien lancé.
     */
    function checkLiveViewProxy(){
        $plugin = $this->getPlugin();
        return $plugin->checkLiveViewProxy();
    }
    /**
     * deleteConnexion
     * Supprime la connexion enregistrée
     */
    function deleteWifi() {
        $plugin = $this->getPlugin();
        return $plugin->deleteWifi();
    }
    /*********************
     * checkState
     * Vérifie l'état de la connexion à l'appareil photo.
     * Un appel toutes les minutes
     * Il faut vérifier toutes les 5 secondes
     */
    static function checkState() {
        $cur = Apn::getCurrent();
        $cur->checkStateApn();
    }
    function checkStateApn() {
        $plugin = $this->getPlugin();
        return $plugin->checkStateApn();
    }

    /**
     * addLog
     * Ajoute une entrée de journal
     * @param $msg
     * @param string $det
     * @return genericClass
     */
    function addLog($msg,$det='') {
        //$this->Status .= date('m/d/Y H:i:s').' > '.$msg."\r\n";
        $act = genericClass::createInstance('LightBox','Activity');
        $act->Titre = date('m/d/Y H:i:s').' > '.$msg."\r\n";
        $act->Type = "Exec";
        $act->addParent($this);
        $act->Save();
        return $act;

    }
    /**
     * setConfig
     * Configure l'appareil
     */
    function setConfig() {
        $plugin = $this->getPlugin();
        return $plugin->setConfigApn();
    }
    /**
     * startLiveView
     * Demarre le proxy liveview
     */
    function startLiveView() {
        $plugin = $this->getPlugin();
        return $plugin->startLiveView();
    }
    /**
     * stopLiveView
     * Stoppe le liveview
     * En mode python pour l'instant
     */
    function stopLiveView() {
        $plugin = $this->getPlugin();
        return $plugin->stopLiveView();
    }
    /**
     * startRecMode
     * Met l'appareil pĥoto en mode enregistrement
     * @return mixed
     */
    function startRecMode() {
        $plugin = $this->getPlugin();
        return $plugin->startRecMode();
    }
    /**
     * stopRecMode
     * Met l'appareil pĥoto en mode veille
     * @return mixed
     */
    function stopRecMode() {
        $plugin = $this->getPlugin();
        return $plugin->stopRecMode();
    }

    /**
     * takePhoto
     * Prend une photo, la télécharge et la stocke
     * @return mixed
     */
    function takePhoto() {
        $plugin = $this->getPlugin();
        return $plugin->takePhoto();
    }

    /**
     * getCurrent
     * Retourne l'objet appareil photo en cours.
     * @return mixed
     */
    static function getCurrent() {
        $apn = Sys::getOneData('LightBox','Apn/Actif=1');
        return $apn;
    }
    /**
     * reboot
     * Redemarre
     * @return mixed
     */
    static function reboot() {
        $apn = Apn::getCurrent();
        $apn->reset();
        LightBox::localExec('sudo /sbin/reboot');
        return true;
    }
    /**
     * halt
     * Arret
     * @return mixed
     */
    static function halt() {
        LightBox::localExec('sudo /sbin/halt');
        return true;
    }
    /**
     * printLast
     * Imprime la dernière photo
     * ATTENTION DEFINIR LA PAGE SIZE EN 4x6 DANS CUPS !!!!!!!
     * @return mixed
     */
    function printLast() {
        $photos = Sys::getData('LightBox','Photo',0,1,'DESC','Id');
        foreach ($photos as $photo){
            LightBox::localExec('lp -o media=Custom.10x15cm '.$photo->Final);
        }
    }
}