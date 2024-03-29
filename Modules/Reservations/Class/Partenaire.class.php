<?php

class Partenaire extends genericClass {


    public function sendReservationMail($reserv,$status){
        $cli = $reserv -> getClient();
        $usr = Sys::getOneData('Systeme','User/'.$cli->UserId);

        $Civilite = $cli -> Civilite . " " . $cli -> Prenom . ' <span style="text-transform:uppercase">' . $cli -> Nom . '</span>';

        require_once ("Class/Lib/Mail.class.php");
        $Mail = new Mail();
        $Mail->Subject("D.D.F: Reservation");
        $Mail -> From( $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $Mail -> ReplyTo($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $Mail -> To($this -> Email);

        klog::l('$this',print_r($this,true));
        //$Mail -> To('enguer@enguer.com');
        $Mail -> Bcc('gcandella@abtel.fr');
        //$Mail -> Cc($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $bloc = new Bloc();


        if($usr->Privilege){
            $mailContent = "
            Bonjour " . $this->Nom . " " . $this->Prenom . ",<br /><br />
            Nous vous informons que ". $Civilite ." vous a réservé une place pour le ".date("d/m/Y à H:i",$reserv->DateDebut)." (Reservation N° " . $reserv->Id . ").<br /> 
            <br />Toute l'équipe du Dome du Foot vous remercie de votre confiance,<br />
            <br />Pour nous contacter : " . $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT') . " .";
        }else{
            $mailContent = "
            Bonjour " . $this->Nom . " " . $this->Prenom . ",<br /><br />
            Nous vous informons que ". $Civilite ." vous a réservé une place pour le ".date("d/m/Y à H:i",$reserv->DateDebut)." (Reservation N° " . $reserv->Id . ").<br /> 
            Afin de confirmer ou d'infirmer votre présence".($reserv->PaiementParticipant ? " et de payer le cas échéant" : " ") .", merci de suivre le lien suivant:
            <a href=\"https://reservation.le-dome-du-foot.fr/Status/".$status->Id."\">Reservations du dome du foot.</a>
            <br />Toute l'équipe du Dome du Foot vous remercie de votre confiance,<br />
            <br />Pour nous contacter : " . $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT') . " .";
        }


        $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
        $Pr = new Process();
        $bloc -> init($Pr);
        $bloc -> generate($Pr);
        $Mail -> Body($bloc -> Affich());

        //klog::l('$Mail',print_r($Mail,true));

        $Mail -> Send();
    }


    public function sendRappelMail($reserv){
        $cli = $reserv -> getClient();

        $Civilite = $cli -> Civilite . " " . $cli -> Prenom . ' <span style="text-transform:uppercase">' . $cli -> Nom . '</span>';

        require_once ("Class/Lib/Mail.class.php");
        $Mail = new Mail();
        $Mail->Subject("D.D.F: Rappel Reservation");
        $Mail -> From( $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $Mail -> ReplyTo($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $Mail -> To($this -> Email);
        //$Mail -> To('enguer@enguer.com');
        $Mail -> Bcc('gcandella@abtel.fr');
        //$Mail -> Cc($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $bloc = new Bloc();
        $mailContent = "
            Bonjour " . $this->Nom . " " . $this->Prenom . ",<br /><br />
            Nous vous rappelons que ". $Civilite ." vous a réservé une place pour le ".date("d/m/Y à H:i",$reserv->DateDebut)." (Reservation N° " . $reserv->Id . ").<br /> 
           
            <br />Toute l'équipe du Dome du Foot vous remercie de votre confiance,<br />
            <br />Pour nous contacter : " . $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT') . " .";

        $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
        $Pr = new Process();
        $bloc -> init($Pr);
        $bloc -> generate($Pr);
        $Mail -> Body($bloc -> Affich());
        $Mail -> Send();
    }

    public function sendAnnulationMail($reserv){
        $cli = $reserv -> getClient();

        $Civilite = $cli -> Civilite . " " . $cli -> Prenom . ' <span style="text-transform:uppercase">' . $cli -> Nom . '</span>';

        require_once ("Class/Lib/Mail.class.php");
        $Mail = new Mail();
        $Mail->Subject("D.D.F: Annulation Reservation");
        $Mail -> From( $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $Mail -> ReplyTo($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $Mail -> To($this -> Email);
        //$Mail -> To('enguer@enguer.com');
        $Mail -> Bcc('gcandella@abtel.fr');
        //$Mail -> Cc($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $bloc = new Bloc();
        $mailContent = "
            Bonjour " . $this->Nom . " " . $this->Prenom . ",<br /><br />
            Nous avons le regret de vous informer que la réservation du ".date("d/m/Y à H:i",$reserv->DateDebut)." (Reservation N° " . $reserv->Id . ") par ". $Civilite ." vient d'être annulée .<br /> 
           
            <br />Toute l'équipe du Dome du Foot vous remercie de votre confiance et espère vous revoir bientôt,<br />
            <br />Pour nous contacter : " . $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT') . " .";

        $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
        $Pr = new Process();
        $bloc -> init($Pr);
        $bloc -> generate($Pr);
        $Mail -> Body($bloc -> Affich());
        $Mail -> Send();
    }

    public function sendConfirmationMail($present,$reserv){
        $cli = $reserv -> getClient();

        require_once ("Class/Lib/Mail.class.php");
        $Mail = new Mail();
        $sub = $present ? "D.D.F: Présence Confirmée" : "D.D.F: Absence signalée";
        $Mail -> Subject($sub);
        $Mail -> From( $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $Mail -> ReplyTo($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $Mail -> To($this -> Email);
        //$Mail -> To('enguer@enguer.com');
        $Mail -> Bcc('gcandella@abtel.fr');
        //$Mail -> Cc($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $bloc = new Bloc();
        $mailContent1 = "
			Bonjour ".$this->Nom." ".$this->Prenom.",<br />
			Vous venez de confirmer votre presence lors du match du ".date('d/m/Y à H:i:s',$reserv->DateDebut)." organisé par ".$cli->Nom." ".$cli->Prenom.".<br/>
			Un mail vient de lui être envoyé afin de le lui signaler.<br />
			<br />Toute l'équipe du Dome du Foot vous remercie de votre confiance,<br />
            <br />Pour nous contacter : " . $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT') . " .";
        $mailContent2 = "
			Bonjour ".$this->Nom." ".$this->Prenom.",<br />
			Vous venez de signaler que vous ne pourrez pas être présent lors du match du ".date('d/m/Y à H:i:s',$reserv->DateDebut)." organisé par ".$cli->Nom." ".$cli->Prenom.".<br/>
			Un mail vient de lui être envoyé afin de le lui signaler.<br />
			<br />Toute l'équipe du Dome du Foot vous remercie de votre confiance,<br />
            <br />Pour nous contacter : " . $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT') . " .";

        $bloc -> setFromVar("Mail", ($present)?$mailContent1:$mailContent2, array("BEACON" => "BLOC"));
        $Pr = new Process();
        $bloc -> init($Pr);
        $bloc -> generate($Pr);
        $Mail -> Body($bloc -> Affich());
        $Mail -> Send();
    }

    public function getPartenaire($email,$nom,$prenom,$cli){
        if($email != null && $email){
            $pa = Sys::getOneData('Reservations','Partenaire/Email='.$email);
        } else {
            if($prenom == null || $prenom == '' || $nom == null || $nom == '') {
                $this->addError(array("Message" => "Une erreur a été rencontrée avec le partenaire ! Vous devez au moins définir un Nom et un Prénom."));
                return $this;
            }

            $pa = Sys::getOneData('Reservations','Partenaire/Nom='.$nom.'&Prenom='.$prenom);
        }

        if($pa) {
            if(is_object($cli)){
                $pa->addParent($cli);
                $pa->Save();
            }
            return $pa;
        }

        $pa = genericClass::createInstance('Reservations','Partenaire');
        $pa->Email = $email;
        $pa->Nom = $nom;
        $pa->Prenom = $prenom;

        if(is_object($cli)){
            $pa->addParent($cli);
        }

        if($pa->Verify()){
            $pa->Save();
        }

        return $pa;
    }
}