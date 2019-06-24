<?php

/*********************************************
*
* Class pour plugin
* LightBox / Instance
* Abtel
* 
*********************************************/


class LightBoxApnCanon extends Plugin implements LightBoxApnPlugin {
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
        //récupération de la photosession
        $ps = PhotoSession::getCurrent();
        return array(
            "connected" => $this->_apn->Connecte,
            "api" => $this->_apn->Api,
            "recmode" => $this->_apn->RecMode,
            "background" => 'http://'.LightBox::getMyIp().'/'.$ps->Fond,
            "liveview" => $this->_apn->LiveView,
            "liveviewenable" => $ps->LiveView,
            "liveviewproxy" => $this->_apn->LiveViewProxy,
            /*            "liveview_url"=> $this->LiveViewUrl*/
            "liveview_url"=> 'http://'.LightBox::getMyIp().':8888/liveview',
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
        return false;
    }

    /**
     * addWifi
     * Ajout la connexion Wifi
     */
    function addWifi(){
        return false;
    }

    /**
     * checkConnected
     * Vérifie si l'usb est bien connecté
     * Active automatiquement l'appareil photo concerné
     */
    function checkConnected(){
        $out = LightBox::localExec('gphoto2 --auto-detect | grep Canon | wc -l');
        if (intval($out) == 1){
            //recherche de l'appareil correspondant
            $apn = Sys::getOneData('LightBox','Apn/Plugin=Canon');
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
        $act = $this->_apn->addLog('Deconnexion de l\'appareil photo');
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
        if (!$silent){
            $act = $this->_apn->addLog('Deconnexion API de l\'appareil photo');
            $act->Terminate(true);
        }
        $this->_apn->Busy = false;
        $this->_apn->Api = false;
        $this->_apn->RecMode = false;
        $this->_apn->LiveView = false;
        $this->_apn->LiveViewProxy = false;
        $this->_apn->ApiUrl = 'http://127.0.0.1:8888/';
        $this->_apn->stopLiveView();
        sleep(1);
        LightBox::localExec('gphoto2 --reset');
        $this->_apn->RawSave();
    }

    /**
     * connectApi
     * Connecte à l'api de l'appareil photo
     */
    function connectApi(){
        $act = $this->_apn->addLog('Connexion Api');
        $this->_apn->Busy = true;
        $this->_apn->RawSave();
        try {
            $out = LightBox::localExec('/usr/bin/nohup /home/lightbox/app/CameraControllerApi/bin/CameraControllerApi >/dev/null 2>&1 &');
        }catch (Exception $e){
            $act->addDetails('error: '.$e->getMessage());
            $act->Terminate(false);
            $this->_apn->Busy = false;
            $this->_apn->RawSave();
            return false;
        }
        $act->addDetails('result: '.$out);
        $this->_apn->Busy = false;
        $this->_apn->Api = true;
        $this->_apn->ApiUrl = 'http://127.0.0.1:8888/';
        $this->_apn->addLog('Connection API OK');
        $act->Terminate(true);
        return true;
    }

    /**
     * checkApiConnected
     * Vérifie si l'api est bien connecté et que l'on a bien l'url
     */
    function checkApiConnected(){
        //on vérifie si le processus est lancé
        $u = LightBox::localExec('ps auxwww | grep CameraControllerApi | wc -l');
        if (intval($u) < 2){
            $act = $this->_apn->addLog('Perte du proxy liveview => nb process '.$u);
            $act->Terminate(false);
            $this->resetApi();
            return false;
        }else return true;
    }

    /**
     * checkLiveViewProxy
     * Vérifie si le proxy est bien lancé.
     */
    function checkLiveViewProxy(){
        /*try {
            $out = $this->callApi('capture?action=autofocus');
        } catch (Exception $e) {
            $act = $this->_apn->addLog('Check LiveView Proxy');
            $act->Terminate(false);
            return false;
        }*/
        /*if (is_object($out) && $out->cca_response->state == "success") {*/
            if (!$this->_apn->LiveViewProxy){
                $this->_apn->LiveViewProxy = true;
                $this->_apn->RawSave();
            }
            return true;
        /*}else{
            if (is_object($out) && $out->cca_response->message == "Camera not found"){
                $this->resetUsb();
                return false;
            }else{
                return false;
            }
        }*/
    }
    /**
     * resetUsb
     * Reset all usb
     */
    function resetUsb() {
        $act = $this->_apn->addLog('RESET USB ');
        try {
            $out = LightBox::localExec('sudo /home/lightbox/app/usbreset.sh');
        }catch (Exception $e){
            $act->addDetails('error: '.$e->getMessage());
            $act->Terminate(false);
            $this->_apn->Busy = false;
            $this->_apn->RawSave();
            return false;
        }
        $act->Terminate(true);
        return true;
    }

    /**
     * deleteConnexion
     * Supprime la connexion enregistrée
     */
    function deleteWifi(){
        /*try {
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
        return true;*/
        $this->resetApi();
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
        while ($m<3){
            echo "-----> c parti pour un checkState CANON \r\n";
            Sys::$Modules['LightBox']->Db->clearLiteCache();
            $apn = Sys::getOneData('LightBox','Apn/Actif=1');
            if ($apn->Busy&&$apn->tmsEdit<time()-60){
                //operation a du echouée on supprime le mode busy
                $apn->Busy = false;
                $apn->Save();
            }
            if (!$apn->Busy) {
                //teste si l'appareil est bien connecté
                if ($apn->checkConnected()) {
                    echo "$m | OK c'est bien connecté \r\n";
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
                                echo "--> checkLiveViewProxy  ";
                                if (!$apn->checkLiveViewProxy()){
                                    echo "--> FAILED \r\n";
                                    $apn->resetApi();
                                }else echo "--> OK \r\n";
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
            sleep(20);
            $m++;
        }
    }

    /**
     * setConfig
     * Configure l'appareil
     */
    function setConfigApn(){
        $act = $this->_apn->addLog('Configuration de l\'appareil');
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
        $this->_apn->LiveViewProxy = true;
        $this->_apn->RawSave();
        //on vérifie d'abord si il n'est pas lancé
        return true;
    }
    /**
     * stopLiveView
     * Stoppe le liveview
     * En mode python pour l'instant
     */
    function stopLiveView($silent = false){
        $act = $this->_apn->addLog('Arret du proxy liveview');
        echo 'STOP LIVE VIEW'."\r\n";
        $act->addDetails('killall -9 CameraControllerApi');
        try {
            LightBox::localExec('killall -9 CameraControllerApi');
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
        $this->_apn->RecMode = false;
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
        $result = $this->callApi('capture?action=shot');
        if (!is_object($result)||$result->cca_response->state!="success") {
            $act->addDetails('Impossible de prendre une photo.');
            $this->_apn->addError(Array('Message'=> 'Impossible de prendre une photo.'));
            $act->Terminate(false);
            return false;
        }
        $this->_apn->Busy = true;
        $this->_apn->RawSave();
        /*LightBox::localExec('killall -9 CameraControllerApi');
        sleep(1);
        try {
            //$this->resetUsb();
            //LightBox::localExec('gphoto2 --reset');
            sleep(1);
            LightBox::localExec('/usr/bin/nohup /home/lightbox/app/CameraControllerApi/bin/CameraControllerApi >/dev/null 2>&1 &');
        }catch (Exception $e){
            sleep(1);
            //$this->resetUsb();
            //LightBox::localExec('gphoto2 --reset');
            sleep(1);
            LightBox::localExec('/usr/bin/nohup /home/lightbox/app/CameraControllerApi/bin/CameraControllerApi >/dev/null 2>&1 &');
        }*/
        //création de la photo
        $url = Photo::download(base64_decode($result->cca_response->data->image));
        $act->Terminate(true);
        $this->_apn->Busy = false;
        $this->_apn->RawSave();
        return $url;
    }
    /**
     * callApi
     * Appelle l'api Sony
     * @return mixed
     */
    function callApi($f,$params = array()) {
        $url = $this->_apn->ApiUrl.$f;
        $data_string = json_encode(array(
        ));
        //$this->addLog('API request: '.$data_string);
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($ch);
        //$this->addLog('API response: '.$result);
        if (empty(trim($result)))$this->_apn->addLog('API error: '.curl_error($ch).' api '.$f);
        curl_close($ch);
        $result = json_decode($result);
        return $result;
    }
}