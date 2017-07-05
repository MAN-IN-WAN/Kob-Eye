<?php

class Partenaire extends genericClass {


    public function sendReservationMail($reserv,$status){
        $cli = $reserv -> getClient();

        $Civilite = $cli -> Civilite . " " . $cli -> Prenom . ' <span style="text-transform:uppercase">' . $cli -> Nom . '</span>';

        require_once ("Class/Lib/Mail.class.php");
        $Mail = new Mail();
        $Mail->Subject("Dome du foot : Reservation");
        $Mail -> From( $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $Mail -> ReplyTo($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $Mail -> To($cli -> Mail);
        //$Mail -> To('enguer@enguer.com');
        $Mail -> Bcc('enguer@enguer.com');
        $Mail -> Cc($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $bloc = new Bloc();
        $mailContent = "
            Bonjour " . $this->Nom . " " . $this->Prenom . ",<br /><br />
            Nous vous informons que ". $Civilite ." vous a réservé une place pour le ".date("d/m/Y à H:i",$reserv->DateDebut)." (Reservation N° " . $reserv->Id . ").<br /> 
            Afin de confirmer ou d'infirmer votre présence, merci de suivre le lien suivant:
            <a href=\"reservation.le-dome-du-foot.fr/Status?s=".$status->Id."\">Reservations du dome du foot.</a>
            <br />Toute l'équipe du Dome du Foot vous remercie de votre confiance,<br />
            <br />Pour nous contacter : " . $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT') . " .";

        $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
        $Pr = new Process();
        $bloc -> init($Pr);
        $bloc -> generate($Pr);
        $Mail -> Body($bloc -> Affich());
        if (!$this->Cloture)
            $Mail -> Send();
    }

    public function sendRappelMail($reserv){
        $cli = $reserv -> getClient();

        $Civilite = $cli -> Civilite . " " . $cli -> Prenom . ' <span style="text-transform:uppercase">' . $cli -> Nom . '</span>';

        require_once ("Class/Lib/Mail.class.php");
        $Mail = new Mail();
        $Mail->Subject("Dome du foot : Rappel Reservation");
        $Mail -> From( $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $Mail -> ReplyTo($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $Mail -> To($cli -> Mail);
        //$Mail -> To('enguer@enguer.com');
        $Mail -> Bcc('enguer@enguer.com');
        $Mail -> Cc($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
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
        if (!$this->Cloture)
            $Mail -> Send();
    }


}