<?php
class Parc_Revendeur extends Parc_Client {
    var $Role = 'PARC_REVENDEUR';

    public function Save( $synchro = true ) {
        genericClass::Save();
        if ($this->AccesActif)
            $this->setUser();
    }
    public function Verify() {
        return genericClass::Verify();
    }
    public function Delete() {
        genericClass::Delete();
    }
    public function getClient() {
        return $this->getChildren('Client');
    }
    public function getDomain() {
        return Sys::getData('Parc','Revendeur/'.$this->Id.'/Client/*/Domain');
    }
    public function getHost() {
        return Sys::getData('Parc','Revendeur/'.$this->Id.'/Client/*/Host');
    }
    public function getClientQuery() {
        return 'Parc/Revendeur/'.$this->Id.'/Client';
    }
    public function getDomainQuery() {
        return 'Parc/Revendeur/'.$this->Id.'/Client/*/Domain';
    }
    public function getHostQuery() {
        return 'Parc/Revendeur/'.$this->Id.'/Client/*/Host';
    }
}