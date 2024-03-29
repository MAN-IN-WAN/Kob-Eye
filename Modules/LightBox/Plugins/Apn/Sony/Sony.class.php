<?php

/*********************************************
*
* Class pour plugin
* LightBox / Instance
* Abtel
* 
*********************************************/


class LightBoxApnSony extends Plugin implements LightBoxApnPlugin {
    private $_apn = null;

    /**
     * init
     *
     */
    function init ($apn){
        $this->_apn = $apn;
    }
    /**
     * getStatus
     * retourne l'état
     */
    function getStatus() {

        return array(
            "connected" => $this->_apn->Connecte,
            "api" => $this->_apn->Api,
            "recmode" => $this->_apn->RecMode,
            "liveview" => $this->_apn->LiveView,
            "liveviewproxy" => $this->_apn->LiveViewProxy,
            /*            "liveview_url"=> $this->LiveViewUrl*/
            "liveview_url"=> 'http://127.0.0.1:8081/?action=stream',
            "diskused" => LightBox::getUsedSpace(),
            "freespace" => (100-LightBox::getUsedSpace()),
            "network" => LightBox::getMyIp(),
        );
    }

    /**
     * checkWifiExists
     * @return bool
     * Vérifie si le SSID est bien enregistré en tant que connexion connue
     */
    function checkWifiExists(){
        try{
            $conns = LightBox::localExec('nmcli con show | grep 802-11-wireless | grep -i "'.$this->_apn->SSID.'"');
        }catch (Exception $e){
            $this->_apn->addError(array('Message'=> 'Impossible de vérifier l\'existence du réseau wifi '.$this->_apn->SSID.'. Détails: '.$e->getMessage()));
            return false;
        }

        if (!empty($conns)) return true;
        return false;
    }

    /**
     * addWifi
     * Ajout la connexion Wifi
     */
    function addWifi(){
        try {
            $out = LightBox::localExec('nmcli dev wifi connect ' . $this->_apn->SSID . ' password ' . $this->_apn->Password);
        }catch (Exception $e){
            $this->_apn->addError(array('Message'=> 'Impossible d\'ajouter le réseau wifi '.$this->_apn->SSID.'. Détails: '.$e->getMessage().' WHOAMI '.LightBox::localExec('whoami')));
            return false;
        }
        $this->_apn->Connecte = true;
        $this->_apn->Save();
        return true;
    }

    /**
     * checkConnected
     * Vérifie si le wifi s'est connecté automatiquement
     * Active automatiquement l'appareil photo concerné
     */
    function checkConnected(){
        $out = LightBox::localExec('nmcli dev status | grep wifi | grep "connecté" | awk \'{print $4}\'');
        if (trim($out)!='--'){
            //recherche de l'appareil correspondant
            $apn = Sys::getOneData('LightBox','Apn/SSID='.Utils::KEAddSlashes($out));
            if (!$apn) return false;
            if ($apn->Id != $this->_apn->Id){
                //Alors on active l'autre appareil
                $apn->Actif = true;
                $apn->Connecte = true;
                $apn->Save();
            }else {
                $this->_apn->Connecte = true;
                $this->_apn->RawSave();
            }
            return true;
        }
        else {
            if ($this->_apn->Connecte)
                $this->_apn->reset();
            return false;
        }
    }

    /**
     * Reinitialise l'état de l'appareil
     * @return mixed
     */
    function reset(){
        $act = $this->_apn->addLog('Deconnexion réseau de l\'appareil photo');
        $this->_apn->Busy = false;
        $this->_apn->Connecte = false;
        $act->Terminate(false);
        $this->_apn->resetApi(true);
        return true;
    }

    /**
     * Reinitialise l'état de l'API
     * @param bool $silent
     * @return mixed
     */
    function resetApi($silent = false){
//        if (!$silent){
            $act = $this->_apn->addLog('Deconnexion API de l\'appareil photo');
            $act->Terminate(true);
//        }
        $this->_apn->Busy = false;
        $this->_apn->Api = false;
        $this->_apn->RecMode = false;
        $this->_apn->LiveView = false;
        $this->_apn->LiveViewProxy = false;
        $this->_apn->ApiUrl = '';
        $this->_apn->stopLiveView();
        $this->_apn->RawSave();
    }

    /**
     * connectApi
     * Connecte à l'api de l'appareil photo
     */
    function connectApi(){
        $this->_apn->Busy = true;
        $this->_apn->RawSave();
        try {
            $out = LightBox::localExec('python ~/app/sony_camera_api/src/example/scan_for_cameras.py');
            if (preg_match("#([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+):([0-9]+)#",$out,$o)){
                $this->_apn->Busy = false;
                $this->_apn->Api = true;
                $this->_apn->ApiUrl = $out;
                $this->_apn->addLog('Connection API OK');
            }else throw new Exception('Impossible de se connecter à l\'api');
        }catch (Exception $e){
            $this->_apn->addError(array('Message'=>'Impossible de se connecter à l\'api. Détails: '.$e->getMessage()));
            $this->_apn->Busy = false;
            $this->_apn->RawSave();
            $this->_apn->addLog('Connection API ERROR');
            return false;
        }
        $this->_apn->Busy = false;
        $this->_apn->RawSave();
        return true;
    }

    /**
     * checkApiConnected
     * Vérifie si l'api est bien connecté et que l'on a bien l'url
     */
    function checkApiConnected(){
        if (!empty($this->_apn->ApiUrl)&&$this->_apn->Connecte&&$this->_apn->Api){
            //on véridie que le port soit bien ouvert.
            if (preg_match("#([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+):([0-9]+)#",$this->_apn->ApiUrl,$out)) {
                if (!LightBox::checkPort($out[1],$out[2])){
                    //pas connecté
                    $act = $this->_apn->addLog('Deconnexion API détectée');
                    $this->_apn->resetApi();
                    $act->Terminate(false);
                    return false;
                }else return true;
            }else {
                echo "ERROR >> url incorrecte \r\n";
                return false;
            }
        }else return false;
    }

    /**
     * checkLiveViewProxy
     * Vérifie si le proxy est bien lancé.
     */
    function checkLiveViewProxy(){
        //on vérifie si le processus est lancé
        $u = LightBox::localExec('ps auxwww | grep mjpg_streamer | wc -l');
        if (intval($u) <= 2){
            $this->_apn->LiveViewProxy = false;
            $act = $this->_apn->addLog('Perte du proxy liveview');
            $act->Terminate(false);
            $this->_apn->RawSave();
            return false;
        }else return true;
        //on véridie que le port soit bien ouvert.
        if (!LightBox::checkPort('127.0.0.1','8081')){
            //pas connecté
            $this->_apn->LiveViewProxy = false;
            $this->_apn->RawSave();
            return false;
        }else return true;
    }

    /**
     * deleteConnexion
     * Supprime la connexion enregistrée
     */
    function deleteWifi(){
        try {
            $cmd = 'nmcli --fields NAME,UUID con | grep -i "'.$this->_apn->SSID.'" | sed -e "s/.* \(.\+\) $/\1 /"';
            $uuid = LightBox::localExec($cmd);
            $uuid = explode(' ',$uuid);
            foreach ($uuid as $u) {
                if (empty(trim($u)))continue;
                $out = LightBox::localExec("nmcli con delete ".trim($u));
                $this->_apn->addSuccess(array('Message'=> 'Suppression du réseau wifi '.$u.' OK.'));
            }
        }catch (Exception $e){
            $this->_apn->addError(array('Message'=> 'Impossible de supprimer le réseau wifi '.$this->SSID.'. Détails: '.$e->getMessage().'  '.$cmd. print_r($uuid,true) ));
            return false;
        }
        return true;
    }

    /*********************
     * checkState
     * Vérifie l'état de la connexion à l'appareil photo.
     * Un appel toutes les minutes
     * Il faut vérifier toutes les 5 secondes
     */
    function checkStateApn(){
        $GLOBALS['Systeme']->Db[0]->query("SET AUTOCOMMIT=1");
        $m = 0;
        //on vérifie que ce n'est pas le premier cycle de boot
        $apn = Sys::getOneData('LightBox','Apn/Actif=1');
        if (LightBox::getUptime()>$apn->tmsEdit) {
            $apn->Reset();
            echo "FIRST EXEC => RESET STATE\r\n";
            $act = $apn->addLog('Première execution - Reset des status');
            $act->Terminate(false);
        }
        //on vérifie l'état de la mémoire
        if (LightBox::getFreeMem()<500000){
            //il ne reste que 500 Mo => on restarte le live view
            $act = $apn->addLog('Détection de dépassement mémoire '.LightBox::getFreeMem().' < 500000');
            $act->Terminate(false);
            $apn->stopLiveView();
        }
        while ($m<12){
            echo "-----> c parti pour un checkState SONY\r\n";
            Sys::$Modules['LightBox']->Db->clearLiteCache();
            $apn = Sys::getOneData('LightBox','Apn/Actif=1');
            if ($apn->Busy&&$apn->tmsEdit<time()-60){
                //operation a du echouée on supprime le mode busy
                $apn->Busy = false;
                $apn->Save();
            }
            if (!$apn->Busy) {
                //teste si l'appareil est bien connecté en wifi
                if ($apn->checkConnected()) {
                    echo "$m | WIFI OK c'est bien connecté \r\n";
                    if ($apn->Api && !$apn->Busy) {
                        echo "--> checking API connection... ";
                        if ($apn->checkApiConnected()) {
                            echo "OK \r\n";
                            if (!$apn->RecMode) {
                                echo "--> set config ... ";
                                if ($apn->startRecMode()) {
                                    echo "OK\r\n";
                                } else {
                                    echo "NOK \r\n";
                                }
                            } elseif ($apn->RecMode&&!$apn->LiveView) {
                                echo "--> set liveview ... ";
                                if ($apn->setConfig()) {
                                    echo "OK\r\n";
                                } else {
                                    echo "NOK \r\n";
                                }
                            } elseif ($apn->RecMode&&$apn->LiveView&&!$apn->LiveViewProxy) {
                                echo "--> set liveview proxy ... ";
                                if ($apn->startLiveView()) {
                                    echo "OK\r\n";
                                } else {
                                    echo "NOK \r\n";
                                }
                            }else{
                                //on vérifie quand meme le proxy live view
                                if (!$apn->checkLiveViewProxy()) $apn->startLiveView();
                            }
                        } else {
                            echo "NOK \r\n";
                        }
                    } elseif (!$apn->Api && !$apn->Busy) {
                        //on lance la connexion
                        echo "--> connecting API ... ";
                        if ($apn->connectApi()) {
                            echo "OK \r\n";
                            echo "--> set config ... ";
                            if ($apn->startRecMode()) {
                                echo "OK\r\n";
                            } else {
                                echo "NOK \r\n";
                            }
                        } else {
                            echo "NOK \r\n";
                        }
                        $m++;
                    }

                } else {
                    echo "$m | KO c'est pas connecté \r\n";
                }
            }
            sleep(5);
            $m++;
        }
    }

    /**
     * setConfig
     * Configure l'appareil
     */
    function setConfigApn(){
        $act = $this->_apn->addLog('Configuration de l\'appareil');
        /*$result = $this->callApi('setStillSize',array(0 => "3:2",1 => "13M"));
        //$result = $this->callApi('getAvailablePostviewImageSize',array());
        if (isset($result->error)||!is_object($result)){
            $act->addDetails('Impossible de définir le setStillSize '.$result->error[1]);
            $this->addError(array('Message'=>'Impossible de définir le setStillSize '.$result->error[1]));
            $act->Terminate(false);
            return false;
        }else{
            $act->addDetails('Définition du setStillSize OK. Détails: '.print_r($result,true));
        }*/
        $result = $this->callApi('setSelfTimer',array(0 => 0));
        if (isset($result->error)||!is_object($result)){
            $act->addDetails('Impossible de définir le setSelfTimer '.$result->error[1]);
            $this->_apn->addError(array('Message'=>'Impossible de définir le setSelfTimer '.$result->error[1]));
            $act->Terminate(false);
            return false;
        }else{
            $act->addDetails('Définition du setSelfTimer OK. Détails: '.print_r($result,true));
        }
        $result = $this->callApi('startLiveviewWithSize',array("L"));
        if (isset($result->error)||!is_object($result)){
            $act->addDetails('Impossible de définir le startLiveviewSize '.$result->error[1]);
            $this->_apn->addError(array('Message'=>'Impossible de définir le startLiveviewSize '.$result->error[1]));
            $act->Terminate(false);
            return false;
        }else{
            $this->_apn->LiveViewUrl = $result->result[0];
            $act->addDetails('Démarrage du startLiveviewWithSize OK. Détails: '.print_r($result,true));
        }
        $this->_apn->LiveView = true;
        $this->_apn->RawSave();
        $act->Terminate(true);
        return true;
    }
    /**
     * startLiveView
     * Demarre le proxy liveview
     */
    function startLiveView(){
        //on vérifie d'abord si il n'est pas lancé
        if ($this->_apn->checkLiveViewProxy()) $this->_apn->stopLiveView();
        $act = $this->_apn->addLog('Démarrage du proxy liveview');
        $this->_apn->Busy = true;
        $this->_apn->RawSave();
        $act->addDetails('/usr/bin/nohup /usr/local/bin/mjpg_streamer -i "/usr/local/lib/mjpg-streamer/input_sony.so" -o "/usr/local/lib/mjpg-streamer/output_http.so -p 8081"  >/dev/null 2>&1 &');
        try {
            //$out = LightBox::localExec('/usr/bin/nohup mjpeg_stream   >/dev/null 2>&1 &');
            $out = LightBox::localExec('/usr/bin/nohup /usr/local/bin/mjpg_streamer -b -i "/usr/local/lib/mjpg-streamer/input_sony.so" -o "/usr/local/lib/mjpg-streamer/output_http.so -p 8081"  >/dev/null 2>&1 &');
            sleep(3);
        }catch (Exception $e){
            $act->addDetails('error: '.$e->getMessage());
            $act->Terminate(false);
            $this->_apn->Busy = false;
            parent::Save();
            return false;
        }
        $act->addDetails('result: '.$out);
        $this->_apn->Busy = false;
        $this->_apn->LiveViewProxy = true;
        $this->_apn->RawSave();
        $act->Terminate(true);
    }
    /**
     * stopLiveView
     * Stoppe le liveview
     * En mode python pour l'instant
     */
    function stopLiveView(){
        $act = $this->_apn->addLog('Arret du proxy liveview');
        $act->addDetails('killall -9 mjpg_streamer');
        try {
            LightBox::localExec('killall -9 mjpg_streamer');
        }catch (Exception $e){
            $act->Terminate(true);
            return true;
        }
        $this->_apn->LiveViewProxy = false;
        $this->_apn->RawSave();
        $act->Terminate(true);
        return true;
    }

    /**
     * startRecMode
     * Met l'appareil pĥoto en mode enregistrement
     * @return mixed
     */
    function startRecMode(){
        $act = $this->_apn->addLog('Démarrage du mode enregistrement');
        $result = $this->callApi('startRecMode');
        if (isset($result->error)||!is_object($result)){
            $act->addDetails('Impossible de définir le startRecMode '.$result->error[1]);
            $this->_apn->addError(array('Message'=>'Impossible de définir le startRecmMde '.$result->error[1]));
            $act->Terminate(false);
            return false;
        }
        $this->_apn->RecMode = true;
        $this->_apn->RawSave();
        $act->Terminate(true);
        return true;
    }

    /**
     * stopRecMode
     * Met l'appareil pĥoto en mode veille
     * @return mixed
     */
    function stopRecMode(){
        $act = $this->_apn->addLog('Démarrage du mode veille');
        $result = $this->callApi('stopRecMode');
        if (isset($result->error)||!is_object($result)){
            $act->addDetails('Impossible de définir le stopRecMode '.$result->error[1]);
            $this->_apn->addError(array('Message'=>'Impossible de définir le stopRecmMde '.$result->error[1]));
            $act->Terminate(false);
            return false;
        }
        //$this->RecMode = false;
        $this->_apn->RawSave();
        $act->Terminate(true);
        return true;
    }

    /**
     * takePhoto
     * Prend une photo, la télécharge et la stocke
     * @return mixed
     */
    function takePhoto(){
        $act = $this->_apn->addLog('Prendre une photo');
        $result = $this->callApi('actTakePicture',array());
        if (isset($result->error)||!is_object($result)) {
            $act->addDetails('Impossible de prendre une photo. détails: '.$result->error[1]);
            $this->_apn->addError(Array('Message'=> 'Impossible de prendre une photo. détails: '.$result->error[1]));
            $act->Terminate(false);
            return false;
        }
        //création de la photo
        $url = Photo::create($result->result[0][0]);
        $act->Terminate(true);
        return $url;
    }
    /**
     * callApi
     * Appelle l'api Sony
     * @return mixed
     */
    function callApi($f,$params = array()) {
        $url = $this->_apn->ApiUrl.'/sony/camera';
        $data_string = json_encode(array(
            "method" => $f,
            "params" => $params,
            "id" => 1,
            "version" => "1.0"
        ));
        //$this->addLog('API request: '.$data_string);
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array('Content-Type:application/json',
                'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($ch);
        //$this->addLog('API response: '.$result);
        if (empty(trim($result)))$this->_apn->addLog('API error: '.curl_error($ch));
        curl_close($ch);
        $result = json_decode($result);
        return $result;
    }
}