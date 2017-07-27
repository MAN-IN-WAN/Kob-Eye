<?php
class DeviceTask extends genericClass {
    public function getTask() {
        klog::l('POSTXXXXX',$_POST);

        if (isset($_GET['success'])){

            $this->Success = $_GET['success'];
            $this->Log = base64_decode($_POST['log']);
            $this->Enabled = 0;

            parent::Save();
        }else {
            if(!$this->Enabled) return false;
            $o = ">>DOWNLOAD\r\n";
            $f = $this->getChildren('DeviceTaskFiles');
            if (is_array($f)) foreach ($f as $fi) {
                $o .= "http://" . Sys::$domain . "/" . $fi->Fichier . "|[DIR]" . $fi->Nom . "\r\n";
            }
            $o .= $this->Commande . "\r\n>>END";
            $this->Enabled =0;
            parent::Save();
            return $o;
        }
    }
}