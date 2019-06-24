<?php
class PhotoSession extends genericClass{
    /**
     * getCurrent
     * Retourne l'objet Session photo en cours.
     * @return mixed
     */
    static function getCurrent() {
        Sys::$Modules['LightBox']->Db->clearLiteCache();
        $sess = Sys::getOneData('LightBox','PhotoSession/Current=1');
        if (!$sess){
            //alors on créé une nouvelle session
            $sess = genericClass::createInstance('LightBox','PhotoSession');
            $sess->Titre = "Session automatique - ".date('d/m/Y H:i:s');
            $sess->Current = true;
            $sess->LiveView = true;
            $sess->Save();
        }
        return $sess;
    }
    /**
     * setMask
     * Teste si l'usb est disponible
     * Montage de l usb et récupération du fichier
     */
    function setMask() {
        if (LightBox::isUsbAvailable()){
            //montage de l'usb
            if (LightBox::mountUsb()){
                $out = LightBox::localExec('if [ -e /home/lightbox/usb/mask.png ]; then echo 1; else pwd; fi');
                if (intval($out)){
                    //ok copie du fichier mask.png et affectation sur la session
                    LightBox::localExec('cp -f /home/lightbox/usb/mask.png Home/mask.png');
                    $this->Masque = 'Home/mask.png';
                    $this->Save();
                    return true;
                }else{
                    $this->addError(array('Message' => 'Impossible de trouver un fichier mask.png sur la clef USB, details : '.$out));
                    return false;
                }
            }else{
                $this->addError(array('Message' => 'Impossible de monter le périphérique USB'));
                return false;
            }
        }else{
            $this->addError(array('Message' => 'Le fichier Masque a bien été affectée à la session en cours'));
            return false;
        }
    }
    /**
     * copySession
     * Teste si l'usb est disponible
     * Montage de l usb et copie des fichiers de la session sur la clef
     */
    function copySession() {
        if (LightBox::isUsbAvailable()){
            //montage de l'usb
            if (LightBox::mountUsb()){
                try {
                    LightBox::localExec('sudo cp -Rf Home/Sessions/'.$this->Id.' /home/lightbox/usb/'.date('Ymd').'-'.$this->Id);
                }catch (Exception $e){
                    $this->addError(array('Message' => 'Impossible de copier les fichiers sur la clef USB, details : '.$e->getMessage()));
                    return false;
                }
                return true;
            }else{
                $this->addError(array('Message' => 'Impossible de monter le périphérique USB'));
                return false;
            }
        }else{
            $this->addError(array('Message' => 'La clef usb n\'est pas détectée'));
            return false;
        }
    }
    /**
     * terminate
     * Termine la sessio nen cours en créé une nouvelle
     */
    function terminate() {
        $this->Current = false;
        $this->Save();
        self::getCurrent();
        return true;
    }
    /**
     * getStatus
     * retourne l'état
     */
    function getStatus() {
        return array(
            "title" => $this->Titre,
            "mask" => !empty($this->Masque),
            "mask_file" => $this->Masque,
            "liveviewenable" => $this->LiveView,
            "background" => 'http://'.LightBox::getMyIp().'/'.$this->Fond,
            "nb_photos" => Sys::getCount('LightBox','PhotoSession/'.$this->Id.'/Photo')
        );
    }

}