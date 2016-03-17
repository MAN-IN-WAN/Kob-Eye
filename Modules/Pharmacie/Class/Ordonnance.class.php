<?php
class Ordonnance extends genericClass {
    function Save () {
        $id = $this->Id;
        parent::Save();
        if (!$id){
            //A la creation on force l'état 1
            $this->Etat=1;
            $usr='';
            $rol='USER';

            //alors c'une nouvelle ordonnance on envoie une notification
 	        AlertUser::addAlert('Nouvelle ordonnance à traiter pour monsieur '.$this->Nom.' '.$this->Prenom,'OR'.$this->Id,'Pharmacie','Ordonnance',$this->Id,$usr,$rol,'icon_contract');
        }
        parent::Save();

        $new = (!$id)?true:false;
        //envoi des mails
        $this->sendMailAcheteur();

        //envoi des notifications
        $this->sendNotifications($new);
    }
    /**
     * Envoi du mail a l'acheteur l'informant que son achat a été prise en compte
     * Param  magasin string
     */
    private function sendMailAcheteur() {
        $this->Magasin = Magasin::getCurrentMagasin();


        $Civilite = $this -> Prenom . ' <span style="text-transform:uppercase">' . $this -> Nom . '</span>';

        $Lacommande = "<br /><img src='http://".Sys::$domain."/".$this->Image.".limit.800x800.jpg' width='100%'/>";

        require_once ("Class/Lib/Mail.class.php");
        $Mail = new Mail();
        if ($this->Etat==1) {
            $Mail->Subject("Confirmation de soumission d'une ordonnance sur " . $this->Magasin->Nom);
        }elseif ($this->Etat==2) {
            $Mail->Subject("Confirmation de preparation de l'ordonnance  " . $this->Magasin->Nom);
        }elseif ($this->Etat==3) {
            $Mail->Subject("Confirmation de retrait de l'ordonnance  " . $this->Magasin->Nom);
        }
        //$Mail -> From($GLOBALS['Systeme'] -> Conf -> get('MODULE::SYSTEME::CONTACT'));
        $Mail -> From( $this -> Magasin ->EmailContact );
//		$Mail -> ReplyTo($GLOBALS['Systeme'] -> Conf -> get('MODULE::SYSTEME::CONTACT'));
        $Mail -> ReplyTo($this -> Magasin ->EmailContact);
        $Mail -> To($this -> Email);
        $Mail -> Bcc($this -> Magasin ->EmailContact);
        $bloc = new Bloc();
        if ($this->Etat==1) {
            $mailContent = "
                Bonjour " . $Civilite . ",<br /><br />
                Nous vous informons que votre ordonnance a bien été prise en compte.<br />
                Vous pouvez d'ores et déjà vous rendre sur <a style='text-decoration:underline' href='http://" . Sys::$domain . "/" . $GLOBALS['Systeme']->getMenu('Boutique/Mon-compte') . "'>votre espace client</a> et suivre l'évolution de votre ordonnance.<br /><br />
                <br />Toute l'équipe de " . $this->Magasin->Nom . " vous remercie de votre confiance,<br />
                <br />Pour nous contacter : " . $this->Magasin->EmailContact . " .".$Lacommande;
        }elseif ($this->Etat==2) {
            $mailContent = "
                Bonjour " . $Civilite . ",<br /><br />
                Nous vous informons que votre commande N° " . $this->RefCommande . " a été préparée.<br />
                Vous pouvez d'ores et déjà vous rendre à l'officine ".$this->Magasin->Nom." pour retirer et payer votre commande <br /><br />
                Toute l'équipe de " . $this->Magasin->Nom . " vous remercie de votre confiance,<br />
                <br />Pour nous contacter : " . $this->Magasin->EmailContact . ". ".$Lacommande;
        }elseif ($this->Etat==3) {
            $mailContent = "
                Bonjour " . $Civilite . ",<br /><br />
                Vous avez retiré votre commande N° " . $this->RefCommande . ".
                Toute l'équipe de " . $this->Magasin->Nom . " vous remercie de votre confiance,<br />
                <br />Pour nous contacter : " . $this->Magasin->EmailContact . " .".$Lacommande;
        }

        $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
        $Pr = new Process();
        $bloc -> init($Pr);
        $bloc -> generate($Pr);
        $Mail -> Body($bloc -> Affich());
        if ($this->Etat<4)
            $Mail -> Send();
    }
    private function sendNotifications($new){
        //notification admin
        $m = Magasin::getCurrentMagasin();
        switch ($this->Etat){
            //1::Non traitée,2::En cours de traitement,3::Traitée,4::Livrée,5::Litige
            case 1:
                $message = "Une nouvelle ordonnance est disponible de Mr " . $this->Nom . " " . $this->Prenom." ";
                break;
            case 2:
                $message = "L'ordonnance est en cours de préparation";
                break;
            case 3:
                $message = "L'ordonnance est préparée";
                break;
            case 4:
                $message = "L'ordonnance est livrée";
                break;
        }

        if ($new) {
            $msg = array
            (
                'title' => 'Driveo backoffice '.$m->Nom.': nouvelle ordonnance de Mr ' . $this->Nom . ' ' . $this->Prenom,
                'store' => 'Ordonnances',
                'vibrate' => 1,
                'sound' => 1
            );
        }else{
            $msg = array
            (
                'title' => 'Driveo backoffice '.$m->Nom.': un nouvel ordonnance de Mr ' . $this->Nom . ' ' . $this->Prenom,
                'store' => 'Ordonnances',
                'vibrate' => 1,
                'sound' => 1
            );
        }
        $msg["message"] = $message;
        Systeme::sendNotification($msg,'admin');

        //notification utilisateur
        $u = Sys::getOneData('Systeme','User/'.$this->userCreate);
        if (!$new) {
            $msg = array
            (
                'title' => $m->Nom.': votre ordonnance a changé d\'état.',
                'store' => 'Ordonnances',
                'vibrate' => 1,
                'sound' => 1
            );
            $msg["message"] = $message;
            Systeme::sendNotification($msg,$u->Id);
        }
    }
}
?>