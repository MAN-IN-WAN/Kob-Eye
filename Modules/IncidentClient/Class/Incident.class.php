<?php

class Incident extends genericClass  {

    public function Save(){
        $new = false;
        $newState = false;
        $etat = false;

        if(!$this->Id){
            $new = true;
        } else{
            $etat = $this->getOneParent('ParametresEtat');
            $parents = $this->Parents;
            foreach($parents as $p){
                if($p['Titre'] == 'ParametresEtat'){
                    break;
                }
            }
            if($etat->Id != $p['Id']) $newState = true;
            if($etat->Cloture) $this->DateCloture = time();
        }

        //Affectation client
        parent::Save();
        if (!$new){
            $etat = $this->getOneParent('ParametresEtat');
            if($etat->Cloture) $this->DateCloture = time();
        }


        $cli = $this->getOneParent('Client');
        if(!$cli){
            $user = Sys::$User;
            $cli = Sys::getOneData('IncidentClient','Client/UserId='.$user->Id);
            if($cli){
                $this->addParent($cli);
                parent::Save();
            }
        }

        if(!$this->Numero){
            $pCli = $this->getOneParent('Client');
            if($pCli){
                $this->Numero = $pCli->CodeClient.sprintf('%05d',$this->Id);
            }else{
                $this->Numero = 'OCEAN'.sprintf('%05d',$this->Id);
            }
        }

        //Gestion de l'état
        if(!$etat){
            $defaut = Sys::getOneData('IncidentClient','ParametresEtat/Defaut=1');
            if($defaut){
                $this->addParent($defaut);
            }
        }

        parent::Save();


        if($new){
            @include_once('Class/Lib/Mail.class.php');

            $mailRecipient = $GLOBALS['Systeme']->Conf->get('GENERAL::INFO::INCIDENT_MAIL');

            $typo = $this->getOneParent('ParametresTypo');


            $Mail = new Mail();
            $Mail->Subject("Nouvel Incident ".Sys::$domain);
            $Mail -> From("noreply@ocean-nimes.com");
            $Mail -> ReplyTo("noreply@ocean-nimes.com");
            $Mail -> To($mailRecipient);
            $Mail -> Bcc("enguerrand@abtel.fr;myriam790@gmail.com");
            if($typo && isset($typo->Mail) && !empty($typo->Mail))
                $Mail -> Cc($typo->Mail);

            $Mail -> Cc('gcandella@abtel.fr');
            $bloc = new Bloc();
            $mailContent = "Bonjour un nouvel incident viens d'être renseigné :".$this->Numero;
            $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
            $Pr = new Process();
            $bloc -> init($Pr);
            $bloc -> generate($Pr);
            $Mail -> Body($bloc -> Affich());
            if (!$Mail -> Send()) {
                $this->addError(array('Message'=>'Impossible d\'envoyer le mail.'));
            }
        }
        if($newState){
            $state = Sys::getOneData('IncidentClient','ParametresEtat/'.$p['Id']);

            @include_once('Class/Lib/Mail.class.php');

            $mailRecipient = $cli->Mail;

            $Mail = new Mail();
            $Mail->Subject("Changement de status Incident ".Sys::$domain);
            $Mail -> From("noreply@ocean-nimes.com");
            $Mail -> ReplyTo("noreply@ocean-nimes.com");
            $Mail -> Bcc("enguerrand@abtel.fr;myriam790@gmail.com");
            $Mail -> To($mailRecipient);
            $Mail -> Cc('gcandella@abtel.fr');
            $bloc = new Bloc();
            $mailContent = "Bonjour un incident viens de changer de statut :".$this->Numero.' / '.date('d/m/Y H:i',$this->DateIncident).' / '.$etat->Nom.'->'.$state->Nom;
            $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
            $Pr = new Process();
            $bloc -> init($Pr);
            $bloc -> generate($Pr);
            $Mail -> Body($bloc -> Affich());
            if (!$Mail -> Send()) {
                $this->addError(array('Message'=>'Impossible d\'envoyer le mail.'));
            }
        }



        return count($this->Error) ? 0 : 1;
    }
}