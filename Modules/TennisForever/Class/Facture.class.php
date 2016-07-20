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
    function getLigneFacture() {
        if ($this->Id){
            //on cherche dans les parents en base
            $this->_produits = Sys::getData('TennisForever','Facture/'.$this->Id.'/LigneFacture');
        }
        return $this->_produits;
    }

    function getClient() {
        return Sys::getoneData('TennisForever','Client/Facture/'.$this->Id);
    }
    function applyFacture() {
        //recuperation de la reservation
        $res = Sys::getOneData('TennisForever','Reservation/Facture/'.$this->Id);
        if ($res){
            $res->setValide();
            $res->Facture = 1;
            $res->Save();
        }
        $this->sendMail();
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
        $Mail->Subject("TENNISFOREVER Emission de facture");
        $Mail -> From( $GLOBALS['Systeme'] -> Conf -> get('MODULE::TENNISFOREVER::CONTACT'));
        $Mail -> ReplyTo($GLOBALS['Systeme'] -> Conf -> get('MODULE::TENNISFOREVER::CONTACT'));
        $Mail -> To($cli -> Mail);
        //$Mail -> To('enguer@enguer.com');
        $Mail -> Cc( $GLOBALS['Systeme'] -> Conf -> get('MODULE::TENNISFOREVER::CONTACT'));
        $Mail -> Bcc('enguer@enguer.com');
        $bloc = new Bloc();
        $mailContent = "
            Bonjour " . $Civilite . ",<br /><br />
            Nous vous informons que votre facture N° " . $this->NumFac. " du ".date("d/m/Y à H:i",$this->tmsCreate)." a bien été enregistrée.<br />
            <br />Toute l'équipe de TENNIS FOREVER vous remercie de votre confiance,<br />
            <br />Pour nous contacter : " . $GLOBALS['Systeme'] -> Conf -> get('MODULE::TENNISFOREVER::CONTACT') . " .".$Lacommande;

        $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
        $Pr = new Process();
        $bloc -> init($Pr);
        $bloc -> generate($Pr);
        $Mail -> Body($bloc -> Affich());
        if (!$this->Cloture)
            $Mail -> Send();
    }
}