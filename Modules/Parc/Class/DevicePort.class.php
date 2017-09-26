<?php
class DevicePort extends genericClass{
    public function Save() {
        parent::Save();
        if (!$this->PortRedirectLocal){
            //generation d'un portlocal incrémental
            $this->PortRedirectLocal = $this->getNextLocalPort();
            parent::Save();
        }
        return true;
    }
    private function getNextLocalPort() {
        try {
            $sql = 'SELECT MAX(PortRedirectLocal) FROM `' . MAIN_DB_PREFIX . 'Parc-DevicePort` ';
            $q = $GLOBALS['Systeme']->Db[0]->query($sql);
            $result = $q->fetchALL(PDO::FETCH_ASSOC);
        } catch (Exception $e){
        }
        return intval($result[0]['MAX(PortRedirectLocal)'])+1;
    }
}