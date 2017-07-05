<?php
class DeviceTask extends genericClass {
    public function getTask() {
        if (isset($_GET['success'])){
            $this->Success = $_GET['success'];
            $this->Log = $_POST['log'];
            parent::Save();
        }else {
            $o = ">>DOWNLOAD\r\n";
            $f = $this->getChildren('DeviceTaskFiles');
            if (is_array($f)) foreach ($f as $fi) {
                $o .= "http://" . Sys::$domain . "/" . $fi->Fichier . "|[DIR]" . $fi->Nom . "\r\n";
            }
            $o .= $this->Commande . "\r\n>>END";
            return $o;
        }
    }
}