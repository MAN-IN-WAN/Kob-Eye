<?php
class Paiement extends genericClass
{
    function getTypePaiement()
    {
        return Sys::getOneData('Reservations', 'TypePaiement/Paiement/' . $this->Id);
    }
    function getFacture()
    {
        return Sys::getOneData('Reservations', 'Facture/Paiement/' . $this->Id);
    }

    public function CheckPaiement()
    {
        /*if (($this->Etat != 0 && $this->Etat < 3) or $this->Status != 0) {
            return;
        }*/

        // Récupération commande
        $facture = $this->getFacture();
        if ($facture == null) {
            die("Impossible de trouver la commande correspondante.");
        }

        // Récupération type de paiement
        $type = $this->getTypePaiement();
        if ($type == null) {
            mail("enguer@enguer.com", "Le type de paiement n'est pas défini.", print_r($_POST, true));
            die("Le type de paiement n'est pas défini.");
        }

        // Chargement du plugin
        $plugin = $type->getPlugin();
        if ($plugin == null) {
            mail("enguer@enguer.com", "Ce type de paiement ne peut être pris en charge.", print_r($_POST, true));
            die("Ce type de paiement ne peut être pris en charge.");
        }

        // Résultats de l'analyse par le plugin
        $results = $plugin->serveurAutoResponse($this, $facture);
        if ($results == null) {
            mail("enguer@enguer.com", "Le paiement n'a pas pu être controle.", print_r($_POST, true));
            die("Le paiement n'a pas pu être controle.");
        }

        // Mise à jour de l'objet paiement
        $this->Set('Detail', $results['detail']);
        $this->Set('Reference', $results['ref']);
        $this->Set('Etat', $results['etat']);
        $this->Set('Status', 1);
        $this->Save();

        if ($results['etat']=='1') {
            $status = $this->getOneParent('StatusReservation');
            if($status){
                $partenaire = $status->getOneChild('Partenaire');

                //Maj du status + du paiement principal
                $status->Paye = 1;
                $status->Save();

                $mainPaiement = $facture->getOneChild('Paiement/PaiementFractionne=1');
                $mainPaiement->Montant -= $this->Montant;
                $mainPaiement->Detail .= PHP_EOL.'Participation de '.$this->Montant.'€ payé par '.$partenaire->Prenom.' '.$partenaire->Nom.' le '.date('d/m/Y à H:i:s');
                $mainPaiement->Save();
            } else {


                //Mise à jour de la facture
                $facture->Valide = 1;
                $facture->Paye = 1;
                $facture->Save();
            }


        }
    }
}