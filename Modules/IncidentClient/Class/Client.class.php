<?php

class Client extends genericClass {

    /**
     * surchage de la fonction delete pour supprimer l'utilisateur
     */
    function Delete() {
        if ($this->GroupId>0){
            $g = $this->getGroup();
            $g->Delete();
        }
        $contacts = $this->getChildren('Contact');
        foreach ($contacts as $contact){
            $contacts->Delete();
        }
        parent::Delete();
    }

    /**
     * Créé le groupe correspondant au client
     *
     * @return mixed:Systeme/Group
     */
    function makeGroup() {
        // on enleve la création du pass car il est saisi
        //$this->Pass = $this->makePassword();
        $Groupe = genericClass::createInstance("Systeme","Group");
        $Groupe->Set("Nom",$this->Get("CodeClient"));
        $grp = Sys::getOneData('Systeme','Group/Nom=[INCIDENT] Client');
        if($grp){
            $Groupe->AddParent($grp);
        }else{
            return false;
        }

        return $Groupe;
    }

    function updateGroup($Groupe=null) {
        if($Groupe == null) $Groupe = $this->getGroup();
        if($Groupe == null) return;
        $Groupe->Set("Nom",$this->Get("CodeClient"));
        $grp = Sys::getOneData('Systeme','Group/Nom=[INCIDENT] Client');
        if($grp){
            $Groupe->AddParent($grp);
        }else{
            return false;
        }
        $Groupe->Save();
        return $Groupe;
    }
    /**
     * Ajoute la vérification appdes paramètres utilisateur (surtout les mails)
     *
     * @return bool
     */
    function Verify() {
        parent::Verify();
        if ($this->Id) {
            if (intval($this->GroupId) > 0) $Groupe = $this->getGroup();
            else $Groupe = $this->makeGroup();
            $Groupe->Verify();
            array_merge($this->Error,$Groupe->Error);
        }
        return sizeof($this->Error)>0 ? 0:1;
    }

    function getGroup(){
        if(intval($this->GroupId)>0){
            $G = Sys::getOneData('Systeme',"Group/".$this->GroupId);
            return $G;
        }

        return false;
    }

    /**
     * Surcharge de l'enregistrement pour ajouter un utilisateur
     *
     * @return bool
     */
    function Save() {
        //if(!$this->Verify()) return false;
        if($this->Id) {
            if( intval($this->GroupId)>0 ) {
                $Groupe = $this->getGroup();
                $Groupe = $this->updateGroup($Groupe);
                $Groupe->Save();
            } else {
                $Groupe = $this->makeGroup();
                $Groupe->Save();
                $this->Set("GroupId",$Groupe->Id);
            }
        } else {
            $Groupe = $this->makeGroup();
            $Groupe->Save();
            $this->Set("GroupId",$Groupe->Get("Id"));

            @include_once('Class/Lib/Mail.class.php');

            $mailRecipient = $GLOBALS['Systeme']->Conf->get('GENERAL::INFO::NEWUSER_MAIL');
            //$mailRecipient = 'gcandella@abtel.fr';

            $Mail = new Mail();
            $Mail->Subject("Nouveau client".Sys::$domain);
            $Mail -> From("noreply@ocean-nimes.com");
            $Mail -> ReplyTo("noreply@ocean-nimes.com");
            $Mail -> Bcc("enguerrand@abtel.fr;myriam790@gmail.com");
            $Mail -> To($mailRecipient);
            $bloc = new Bloc();
            $mailContent = "Bonjour un nouveau client vient d'être créé :".$this->Nom.'';
            $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
            $Pr = new Process();
            $bloc -> init($Pr);
            $bloc -> generate($Pr);
            $Mail -> Body($bloc -> Affich());
            $Mail -> Send();
        }

        if(!count($Groupe->getErrors())){
            genericClass::Save();
            return true;
        } else {
            $this->addError(array("Message" => "Erreur lors de la sauvegarde du client", "Err" => $Groupe->getErrors()));
        }


        return false;
    }



}