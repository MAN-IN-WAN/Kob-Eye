<?php
class Ordonnance extends genericClass {
    function Save () {
        $id = $this->Id;
        $new = (!$id)?true:false;
        if ($id){
            //récupération de l'ancien enreigstrement pour comparer
            $old = Sys::getOneData('Pharmacie','Ordonnance/'.$id);
        }

        parent::Save();
        if ($new){
            //Si sachet dose
            if ($this->SachetDose){
                $this->Commentaire.="\r\nPREPARATION SACHET DOSE";
                if ($this->Livraison)
                    $this->Commentaire.="\r\nLIVRAISON";
            }
            //si livraison

            //A la creation on force l'état 1
            $this->Etat=1;
            $usr='';
            $rol='USER';

            $cl = Sys::getOneData('Boutique','Client/UserId='.Sys::$User->Id);
            if (!$cl) return false;
            //on remplit les champs
            $this->Nom = $cl->Nom;
            $this->Prenom = $cl->Prenom;
            $this->Email = $cl->Mail;
            $this->Telephone = $cl->Tel;
            $this->Priorite = 10;
            $this->DateCreation = time();

            //calcul de la date de retrait prévue
            //il faut au moins un décallage de 2 heures
            $d = $this->tmsCreate;
            $ch = date('H',$d+7200);
            $hs = Sys::getData('Pharmacie','PlageHoraire',0,100,'HeureDebut','ASC');
            $gp = null;
            foreach ($hs as $hss){
                if ($ch<$hss->HeureFin){
                    $gp = $hss;
                    $ch = $hss->HeureDebut;
                }
            }
            if (!$gp){
                //pour le lendemain
                $hss = Sys::getOneData('Pharmacie','PlageHoraire',0,1,'HeureDebut','ASC');
                $ch = $hss->HeureDebut;
                $d = strtotime('+1 day',$this->tmsCreate);
            }
            $this->DateRetrait = mktime($ch,0,0,date('m',$d),date('d',$d),date('Y',$d));

            //alors c'une nouvelle ordonnance on envoie une notification
 	        AlertUser::addAlert('Nouvelle ordonnance à traiter pour monsieur '.$this->Nom.' '.$this->Prenom,'OR'.$this->Id,'Pharmacie','Ordonnance',$this->Id,$usr,$rol,'icon_contract');
        }
        //mise à jour de l'état
        switch ($this->Etat){
            //1::Non traitée,2::En cours de traitement,3::Traitée,4::Livrée,5::Litige
            case 1:
                //envoi des mails
                $this->sendMailAcheteur();
                //envoi des notifications
                $this->sendNotifications($new);
                $this->DateCreation = time();
                if ($this->Priorite<50)
                    $this->Priorite = 40;
                break;
            case 2:
                if ($old->Etat!=$this->Etat||$this->Priorite>50) {
                    //envoi des mails
                    $this->sendMailAcheteur();
                    //envoi des notifications
                    $this->sendNotifications($new);
                    if ($this->Priorite<50)
                        $this->Priorite = 20;
                }
                break;
            case 3:
                if ($old->Etat!=$this->Etat||$this->Priorite>50) {
                    //envoi des mails
                    $this->sendMailAcheteur();
                    //envoi des notifications
                    $this->sendNotifications($new);
                    $this->PrepareLe = time();
                    if ($this->Priorite<50)
                        $this->Priorite = 10;
                }
                break;
            case 4:
                //if ($old->Etat!=$this->Etat) {
                    //envoi des mails
                    $this->sendMailAcheteur();
                    //envoi des notifications
                    $this->sendNotifications($new);
                    $this->RetireLe = time();
                    $this->Priorite = 0;
                //}
                break;
            default:
                $this->Priorite = 0;
            break;
        }


        parent::Save();
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
        }elseif ($this->Etat==3) {
            $Mail->Subject("Confirmation de preparation de l'ordonnance  " . $this->Magasin->Nom);
        }elseif ($this->Etat==4) {
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
                <b>Vous pouvez retirer votre commande à partir du ".date('d/m/Y H:i',$this->DateRetrait)."</b><br />
                <br />Toute l'équipe de " . $this->Magasin->Nom . " vous remercie de votre confiance,<br />
                <br />Pour nous contacter : " . $this->Magasin->EmailContact . " .".$Lacommande;
        }elseif ($this->Etat==3) {
            $mailContent = "
                Bonjour " . $Civilite . ",<br /><br />
                Nous vous informons que votre ordonnance a été préparée.<br />
                Vous pouvez d'ores et déjà vous rendre à l'officine ".$this->Magasin->Nom." pour retirer et payer votre commande <br /><br />
                Toute l'équipe de " . $this->Magasin->Nom . " vous remercie de votre confiance,<br />
                <br />Pour nous contacter : " . $this->Magasin->EmailContact . ". ".$Lacommande;
        }elseif ($this->Etat==4) {
            $mailContent = "
                Bonjour " . $Civilite . ",<br /><br />
                Vous avez retiré votre ordonnance.
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
                $message = "A retirer à partir du ".date('d/m/Y H:i',$this->DateRetrait);
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
        if ($this->Priorite>50){
            $message = 'Le client est dans l\'officine !! '.$message;
        }

        if ($new) {
            $msg = array
            (
                'title' => ''.$m->Nom.': nouvelle ordonnance de Mr ' . $this->Nom . ' ' . $this->Prenom,
                'store' => 'Ordonnances',
                'vibrate' => 1,
                'sound' => 1
            );
        }else{
            $msg = array
            (
                'title' => ''.$m->Nom.': l\'ordonnance de Mr ' . $this->Nom . ' ' . $this->Prenom,
                'store' => 'Ordonnances',
                'vibrate' => 1,
                'sound' => 1
            );
        }
        $msg["message"] = $message;
        Systeme::sendNotification($msg,'admin');

        //notification utilisateur
        $u = Sys::getOneData('Systeme','User/'.$this->userCreate);
        $msg = array
        (
            'title' => $m->Nom.': l\'ordonnance de ' . $this->Nom . ' ' . $this->Prenom.' changé d\'état.',
            'store' => 'Ordonnances',
            'vibrate' => 1,
            'sound' => 1
        );
        $msg["message"] = $message;
        Systeme::sendNotification($msg,$u->Id);
    }
    function Prioriser() {
        $this->Priorite=60;
        $this->Save();
    }
}
?>