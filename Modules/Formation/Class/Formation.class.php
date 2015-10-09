<?php
class Formation extends Module {
    /**
     * Surcharge de la fonction init
     * Avant l'authentification de l'utilisateur
     * @void
     */
    static $_Session;


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
        //chargement des variables globales par défaut pour le module formation
        $this->initGlobalVars();
    }
    /**
     * Initilisation des variables globales disponibles pour la boutique
     */
    function initGlobalVars(){
        //initialisation magasin si disponible
        $T= Sys::getOneData('Formation','Session/EnCours=1');
        Formation::$_Session = $T;
        $GLOBALS["Systeme"]->registerVar("CurrentSession",Formation::$_Session);

    }

    /**
     * Arret de l'appareil
     */
    static function Shutdown () {
        //backup d'abord
        Formation::Backup();

        //arret
        system('sudo /sbin/halt');
        klog::l('************** Systeme shutdown ***************');
    }

    /**
     * Redemarrage de l'appareil
     */
    static function Reboot () {
        //backup d'abord
        Formation::Backup();

        //reboot
        system('sudo /sbin/reboot');
        klog::l('************** Systeme reboot ***************');
    }

    /**
     * Backup mysql
     */
    static function Backup () {
        exec('/usr/bin/mysqldump -u root -pzH34Y6u5 formation > /var/www/formation.bck.sql');
        klog::l('************** Systeme reboot ***************');
    }
    /**
     * Récupère e chanel en cours
     */
    static function getCurrentChannel() {
        $channel = exec('cat /etc/hostapd/hostapd.conf | grep channel');
        $channel = explode("channel=", $channel);
        $channel = $channel[1];
        return $channel;
    }
    /**
     * setChannel
     * Définit le cannal du sifi.
     */
    static function setChannel($channel) {
        $success = file_put_contents('/tmp/hostapd.conf','interface=wlan0
interface=wlan0
driver=rtl871xdrv
country_code=FR
ctrl_interface=wlan0
ssid=DEPLOIEMENT_ERDF
hw_mode=g
channel='.$channel.'
beacon_int=100
auth_algs=3
macaddr_acl=0
wmm_enabled=1
ieee80211n=1
eap_reauth_period=360000000
ht_capab=[HT40+][SHORT-GI-40][DSSS_CCK-40]
');
        if (!$success) return false;

        //copy du fichier
        $error = system('sudo cp /tmp/hostapd.conf /etc/hostapd/hostapd.conf');
        if ($error) return false;

        //redemarrage du service
        exec('sudo service hostapd restart');
        return true;
    }

    /**
     * getCmd
     * get the result of command
     */
    static function getCmd($cmd) {
        return exec($cmd);
    }

    /**
     * getWifiChannel
     */
    static function getWifiChannels() {
        $tab = array();
        exec('sudo /sbin/iwlist wlan0 scan | grep Channel', $out);
        for ($i=1; $i<=13; $i++) $tab[$i]= 0;
        foreach ($out as $o){
            if (preg_match('#\(Channel ([0-9]+?)\)#',$o,$t)) {
                $tab[$t[1]]++;
            }
        }
        return $tab;
    }
}