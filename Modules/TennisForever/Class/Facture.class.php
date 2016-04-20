<?php
class Facture extends genericClass{
    function Save(){
        $new = false;
        if ($this -> Id) {
            //Verification avec l'objet en base
            $old = Sys::getOneData('TennisForever','Facture/'.$this->Id);

            //Test des comportements à déclencher
            $this -> Apayer = (!$old->Paye && $this -> Paye);
            $this -> Avalider = (!$old->Valide && $this -> Valide);
            $this -> ADevalider = ($old->Valide && !$this -> Valide);
        } else {
            $new = true;
            $this -> Apayer = $this -> Paye;
            $this -> Avalider = $this -> Valide;
        }

        //Sauvegarde
        parent::Save();

        //Enregistrement de la reference
        $this -> SaveRef();


        //Execution des comportements
        if ($this -> Avalider) {
            $this->applyFacture();
            $this->Priorite = 40;
        }
        if ($this -> ADevalider) {
            $this->Priorite = 0;
        }
        if ($this -> Apayer) {
        }

        //Sauvegarde
        parent::Save();
    }

    /**
     * Création d'une référence
     * @return	void
     */
    private function SaveRef() {
        if ($this -> Valide) {
            $num = Sys::getCount('TennisForever','Facture/Valide=1')+1;
            if (substr($this->NumFac,0,4)=='BROU') $this->NumFac = sprintf("FA".Date('Y').Date('m').'-'."%05d",$num);
        }
        else {
            if($this->NumFac == '') $this->DateCommande = time();
            $this -> NumFac = sprintf("BROU%05d", $this -> Id);
        }
    }

    function getPaiement() {
        $paiement = Sys::getOneData('TennisForever','Facture/'.$this->Id.'/Paiement');
        if ($paiement) return $paiement;
        else{
            //recherch du type de paiement actif
            $tp = Sys::getOneData('TennisForever','TypePaiement/Actif=1');

            //création du paiement
            $paiement = genericClass::createInstance('TennisForever','Paiement');
            $paiement->Montant = $this->MontantTTC;
            $paiement->addParent($this);
            $paiement->addParent($tp);
            $paiement->Save();
            return $paiement;
        }
    }
    function getClient() {
        return Sys::getoneData('TennisForever','Client/Facture/'.$this->Id);
    }
    function applyFacture() {
        //recuperation de la reservation
        $res = Sys::getOneData('TennisForever','Reservation/Facture/'.$this->Id);
        if ($res){
            $res->Valide = 1;
            $res->Save();
        }
    }
}