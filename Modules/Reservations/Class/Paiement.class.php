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
        if (($this->Etat != 0 && $this->Etat < 3) or $this->Status != 0) {
            return;
        }

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
            mail("enguer@enguer.com", "Le paiement n'a pas pu être contrôlé.", print_r($_POST, true));
            die("Le paiement n'a pas pu être contrôlé.");
        }

        // Mise à jour de l'objet paiement
        $this->Set('Detail', $results['detail']);
        $this->Set('Reference', $results['ref']);
        $this->Set('Etat', $results['etat']);
        $this->Set('Status', 1);
        $this->Save();

        if ($results['etat']=='1') {
            //Mise à jour de la facture
            $facture->Valide = 1;
            $facture->Paye = 1;
            $facture->Save();
        }
    }
}