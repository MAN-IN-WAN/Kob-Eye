<?php

class Technicien extends genericClass {

    /**
     * Créé un mot de passe aléatoire pour l'utilisateur.
     *
     * @return string
     */
    function makePassword($i=0){
        if ($i==8) return "";
        else{
            $Caracteres = "azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";
            $Nombres = "0123456789";
            $CharOuNbr = rand(0,1);
            if ($CharOuNbr == 0){
                $Char = rand(0,strlen($Caracteres)-1);
                return $Caracteres[$Char] . $this->makePassword($i+1);
            }else{
                $Nbr = rand(0,strlen($Nombres)-1);
                return $Nombres[$Nbr] . $this->makePassword($i+1);
            }
        }
    }

    function changePassword(){
        $Pass = $this->makePassword();
        $Utilisateur=$this->getUser();
        $Utilisateur->Set("Pass",$Pass);
        $Utilisateur->Save();
        return $Pass;
    }
    /**
     * surchage de la fonction delete pour supprimer l'utilisateur
     */
    function Delete() {
        if ($this->UserId>0){
            $u = $this->getUser();
            if ($u)$u->Delete();
        }
        parent::Delete();
    }

    /**
     * Renvoie le mot de passe précédemment affecté à $this->Pass.
     *
     * @return string
     */
    function getPass(){
        return $this->Pass;
    }
    /**
     * Créé l'utilisateur correspondant au client
     *
     * @return mixed:Systeme/Utilisateur
     */
    function makeUser() {
        // on enleve la création du pass car il est saisi
        //$this->Pass = $this->makePassword();
        $Utilisateur = genericClass::createInstance("Systeme","User");
        $Utilisateur->Set("Nom",$this->Get("Nom"));
        $Utilisateur->Set("Prenom",$this->Get("Prenom"));
        $Utilisateur->Set("Mail",$this->Get("Mail"));
        $Utilisateur->Set("Login",$this->Get("Mail"));
        $Utilisateur->Set("Adresse",$this->Get("Adresse"));
        $Utilisateur->Set("Tel",$this->Get("Tel"));
        $Utilisateur->Set("CodPos",$this->Get("CodePostal"));
        $Utilisateur->Set("Ville",$this->Get("Ville"));
        $Utilisateur->Set("Adresse",$this->Get("Adresse"));
        $Utilisateur->Set("Pass",$this->Pass);
        $Utilisateur->Set("Admin","0");
        $Utilisateur->Set("Actif",$this->Actif);

        $grp = Sys::getOneData('Systeme','Group/Nom=[INCIDENT] Technicien');
        if($grp){
            $Utilisateur->AddParent($grp);
        }else{
            return false;
        }

        return $Utilisateur;
    }

    function updateUser($Utilisateur=null) {
        if($Utilisateur == null) $Utilisateur = $this->getUser();
        if($Utilisateur == null) return;
        $Utilisateur->Set("Nom",$this->Get("Nom"));
        $Utilisateur->Set("Prenom",$this->Get("Prenom"));
        $Utilisateur->Set("Mail",$this->Get("Mail"));
        $Utilisateur->Set("Login",$this->Get("Mail"));
        $Utilisateur->Set("Adresse",$this->Get("Adresse"));
        $Utilisateur->Set("Tel",$this->Get("Tel"));
        $Utilisateur->Set("CodPos",$this->Get("CodePostal"));
        $Utilisateur->Set("Ville",$this->Get("Ville"));
        $Utilisateur->Set("Adresse",$this->Get("Adresse"));
        $Utilisateur->Set("Actif",$this->Actif);
// ajout du pass à la modification et du coup on se reconnecte à chaque modification du user
        if($this->Pass != ''){
            $Utilisateur->Set("Pass",$this->Pass);
        }
        $Utilisateur->Set("Admin","0");
        $grp = Sys::getOneData('Systeme','Group/Nom=[INCIDENT] Technicien');
        if($grp){
            $Utilisateur->AddParent($grp);
        }else{
            return false;
        }
        $Utilisateur->Save();
        return $Utilisateur;
    }
    /**
     * Créé l'utilisateur correspondant à la personne
     *
     * @return Objet:Systeme/Utilisateur
     */
    function initFromUser() {
        $U = Sys::$User;
        $this->Nom = $U->Nom;
        $this->Prenom = $U->Prenom;
        $this->Mail = $U->Mail;
        $this->Login = $U->Login;
        $this->Adresse = $U->Adresse;
        $this->Tel = $U->Tel;
        $this->CodePostal = $U->CodePos;
        $this->Ville = $U->Ville;
    }

    /**
     * Ajoute la vérification appdes paramètres utilisateur (surtout les mails)
     *
     * @return bool
     */
    function Verify() {
        if ( intval($this->UserId)>0 )$Utilisateur = $this->getUser();
        if (!$Utilisateur)$Utilisateur = $this->makeUser();

        $Utilisateur->Verify();
        parent::Verify();
        array_merge($this->Error,$Utilisateur->Error);

        return sizeof($this->Error)>0 ? 0:1;
    }

    function getUser(){
        if(intval($this->UserId)>0){
            $U = Sys::getOneData('Systeme',"User/".$this->UserId);
            return $U;
        }

        return false;
    }

    /**
     * Surcharge de l'enregistrement pour ajouter un utilisateur
     *
     * @return bool
     */
    function Save() {
        if(!$this->Verify()) return false;
        if($this->Id) {
            if( intval($this->UserId)>0 ) {
                $Utilisateur = $this->getUser();
                if (!$Utilisateur) $Utilisateur = $this->makeUser();
                else $Utilisateur = $this->updateUser($Utilisateur);
                $Utilisateur->Save();
            } else {
                $Utilisateur = $this->makeUser();
                $Utilisateur->Save();
                $this->Set("UserId",$Utilisateur->Id);
            }
        } else {
            $Utilisateur = $this->makeUser();
            $Utilisateur->Save();
            $this->Set("UserId",$Utilisateur->Get("Id"));

            @include_once('Class/Lib/Mail.class.php');

            $mailRecipient = $GLOBALS['Systeme']->Conf->get('GENERAL::INFO::NEWUSER_MAIL');
            ///$mailRecipient = 'gcandella@abtel.fr';

            $Mail = new Mail();
            $Mail->Subject("Nouveau technicien ".Sys::$domain);
            $Mail -> From("noreply@ocean-nimes.com");
            $Mail -> ReplyTo("noreply@ocean-nimes.com");
            $Mail -> Bcc("enguerrand@abtel.fr");
            $Mail -> To($mailRecipient);
            $bloc = new Bloc();
            $mailContent = "Bonjour un nouveau technicien viens d'être créé :".$this->Nom.' '.$this->Prenom.''.$this->CodeClient ;
            $bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
            $Pr = new Process();
            $bloc -> init($Pr);
            $bloc -> generate($Pr);
            $Mail -> Body($bloc -> Affich());
            $Mail -> Send();
        }

        parent::Save();
        return true;
    }



}