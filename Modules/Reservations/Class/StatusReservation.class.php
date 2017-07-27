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
            $tp = Sys::getOneData('Reservations','TypePaiement/Actif=1');

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


    function sendMail() {
        $cli = $this -> getClient();
        $paiement = $this->getPaiement();

        $Civilite = $cli -> Civilite . " " . $cli -> Prenom . ' <span style="text-transform:uppercase">' . $cli -> Nom . '</span>';

        $lf = $this->getLigneFacture();
        if (!sizeof($lf)) {
            $Lacommande = "Erreur";
        } else {
            $Lacommande .= "<h2>Récapitulatif de votre facture  $this->NumFac: </h2><br /><br /><table width='100%'>";
            $Lacommande .= "<tr bgcolor='#666' padding='5'><td><font color='#ffffff'>Quantite</font></td><td><font color='#ffffff'>Titre</font></td><td><font color='#ffffff'>Tarif TTC</font></td></tr>";
            $total = 0;
            foreach ($lf as $l) :
                //récupération du produit
                $Lacommande .= "<td><h3>" . $l->Quantite . "</h3> </td>";
                $Lacommande .= "<td><h3>" . $l->Libelle . "</h3></td>";
                $Lacommande .= "<td><h2>" . $l->MontantTTC . " € TTC</h2></td></tr>";
                $total += $l->MontantTTC;
            endforeach;
            $Lacommande .= "
                <tr bgcolor='#666' padding='5'>
                    <td colspan='2'></td>
                    <td><font color='#ffffff'>TOTAL</font></td>
                    <td><font color='#ffffff'><h2>$this->MontantTTC € TTC</h2></font></td>
                </tr>
            </table>";

            $Lacommande .= "<br /><h2>Détail du paiement</h2>";
            $Lacommande .= "<p>Le paiement a été effectué avec succès sous la référence ".$paiement->Reference." à ".date('d/m/Y H:i:s',$paiement->tmsEdit)." pour un montant de ".$paiement ->Montant." € TTC</p>";

        }

        require_once ("Class/Lib/Mail.class.php");
        $Mail = new Mail();
        $Mail->Subject("Reservations Emission de facture");
        $Mail -> From( $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $Mail -> ReplyTo($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $Mail -> To($cli -> Mail);
        //$Mail -> To('enguer@enguer.com');
        $Mail -> Cc( $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $Mail -> Bcc('enguer@enguer.com');
        $bloc = new Bloc();
        $mailContent = "
            Bonjour " . $Civilite . ",<br /><br />
            Nous vous informons que votre facture N° " . $this->NumFac. " du ".date("d/m/Y à H:i",$this->tmsCreate)." a bien été enregistrée.<br />
            <br />Toute l'équipe de TENNIS FOREVER vous remercie de votre confiance,<br />
            <br />Pour nous contacter : " . $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT') . " .".$Lacommande;

        $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
        $Pr = new Process();
        $bloc -> init($Pr);
        $bloc -> generate($Pr);
        $Mail -> Body($bloc -> Affich());
        if (!$this->Cloture)
            $Mail -> Send();
    }
}