<?php

class StatusReservation extends genericClass {

    function getPaiement() {
        $paiement = Sys::getOneData('Reservations','StatusReservation/'.$this->Id.'/Paiement');
        $partenaire = $this->getOneChild('Partenaire');
        $reservation = $this->getOneParent('Reservation');
        $facture = $reservation->getOneChild('Facture');

        if ($paiement) return $paiement;
        else{
            //recherche du type de paiement actif
            $tp = Sys::getOneData('Reservations','Instance/Actif=1');

            //création du paiement
            $paiement = genericClass::createInstance('Reservations','Paiement');
            $paiement->Montant = $this->MontantPaye;
            $paiement->Mail = $partenaire->Email;
            $paiement->addParent($this);
            $paiement->addParent($facture);
            $paiement->addParent($tp);
            $paiement->Save();
            return $paiement;
        }
    }
}