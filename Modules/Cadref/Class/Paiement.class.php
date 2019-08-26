<?php
class Paiement extends genericClass
{
    function getTypePaiement()
    {
        return Sys::getOneData('Cadref', 'TypePaiement/Paiement/' . $this->Id);
    }

    public function CheckPaiement()
    {
        if (($this->Etat != 0 && $this->Etat < 3) or $this->Status != 0) {
            return;
        }

        // Récupération type de paiement
        $type = $this->getTypePaiement();
        if ($type == null) {
            mail("paul@abtel.fr", "CADREF : Le type de paiement n'est pas défini.", print_r($_POST, true));
            die("Le type de paiement n'est pas défini.");
        }

        // Chargement du plugin
        $plugin = $type->getPlugin();
        if ($plugin == null) {
            mail("paul@abtel.fr", "CADREF : Ce type de paiement ne peut être pris en charge.", print_r($_POST, true));
            die("Ce type de paiement ne peut être pris en charge.");
        }

        // Résultats de l'analyse par le plugin
        $results = $plugin->serveurAutoResponse($this, $commande);
        if ($results == null) {
            mail("paul@abtel.fr", "CADREF : Le paiement n'a pas pu être contrôlé.", print_r($_POST, true));
            die("Le paiement n'a pas pu être contrôlé.");
        }

        // Mise à jour de l'objet paiement
        $this->Set('Detail', $results['detail']);
        $this->Set('Reference', $results['ref']);
        $this->Set('Etat', $results['etat']);
        $this->Set('Status', 1);
        $this->Save();

        if ($results['etat']=='1') {
			$adh = $this->getOneParent('Adherent');
			$adh->WebInscription($this);
        }
    }
}