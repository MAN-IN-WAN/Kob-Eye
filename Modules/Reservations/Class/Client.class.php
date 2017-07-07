<?php

/**
 * Ajout d'un utilisateur automatique pour un Client
 *
 */
class Client extends genericClass {

  var $Pass;
  var $Panier;


	function isSubscriber(){
		return $this->Abonne;
	}
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
            $u->Delete();
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
	* @return Objet:Systeme/Utilisateur
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
		$Utilisateur->Set("Pays",$this->Get("Pays"));
		$Utilisateur->Set("Pass",$this->Pass);
		$Utilisateur->Set("Admin","0");
		$Utilisateur->Set("Actif",$this->Get("Actif"));
		$Utilisateur->AddParent("Systeme/Group/".RESERVATIONS_CLIENT_GROUP);
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
		$Utilisateur->Set("Pays",$this->Get("Pays"));
		$Utilisateur->Set("Actif",$this->Get("Actif"));
// ajout du pass à la modification et du coup on se reconnecte à chaque modification du user
		if($this->Pass != ''){
		  $Utilisateur->Set("Pass",$this->Pass);
		}
		$Utilisateur->Set("Admin","0");
		$Utilisateur->AddParent("Systeme/Group/".RESERVATIONS_CLIENT_GROUP);
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
		$this->Pays = $U->Pays;
	}

	/**
	* Ajoute la vérification appdes paramètres utilisateur (surtout les mails)
	*
	* @return bool
	*/
	function Verify($need_user=1) {
		if ($need_user==1) {
			$Utilisateur = $this->getUser();
			$Utilisateur->Verify();
			genericClass::Verify();
//			if (is_array($this->Error)&&is_array($Utilisateur->Error))
//				$this->Error = array_merge($this->Error,$Utilisateur->Error);
//			elseif (!is_array($this->Error)&&is_array($Utilisateur->Error))
//				$this->Error = $Utilisateur->Error;
            if (!is_array($this->Error))
                $this->Error = array();

			$Errors = Array();
			if (isset($this->Error)&&is_array($this->Error))foreach ($this->Error as $E){
				$f= false;
				foreach ($Errors as $e)if ($e["Prop"]==$E["Prop"])$f=true;
				if (!$f)$Errors[] = $E;
			}
			$this->Error = (array)$Errors;

			return !sizeof($this->Error);
		}


		return genericClass::Verify();
	}
	
	function getUser(){
		if ($this->UserId)
			$U = Sys::$Modules["Systeme"]->callData("Systeme/User/".$this->UserId);
		else return $this->makeUser();
		return genericClass::createInstance('Systeme',$U[0]);
	}	
	/**
	* Surcharge de l'enregistrement pour ajouter un utilisateur 
	*
	* @return bool
	*/
	function Save($need_user=1) {
		if($this->Id!="") {
			if($need_user==1 && $this->Verify(1) && $this->UserId!="") {
				$Utilisateur = $this->getUser();
				$Utilisateur = $this->updateUser($Utilisateur);
				$Utilisateur->Save();
			}
			elseif($need_user==1 && $this->Verify(1) && $this->UserId=="") {
				$Utilisateur = $this->makeUser();
				$Utilisateur->Save();
				$this->Set("UserId",$Utilisateur->Get("Id"));
			}
		}
		else {
			if ($need_user==1 && $this->Verify(1)) {
				$Utilisateur = $this->makeUser();
				$Utilisateur->Save();
			}
			if ( $need_user==1 ) {
				$this->Set("UserId",$Utilisateur->Get("Id"));
			}
			//nouvel utilisateur envoyer email de confirmation
			$this->EnvoiEmailConfirmation();
		}

		genericClass::Save();

		$partenaire = $this->getOneParent('Partenaire');
		if(!$partenaire) $partenaire = Sys::getOneData('Reservations','Partenaire/Email='.$this->Get("Mail"));
		if(!$partenaire) {
		    $partenaire = genericClass::createInstance('Reservations','Partenaire');
        }
        $partenaire->Nom = $this->Get("Nom");
        $partenaire->Prenom = $this->Get("Prenom");
        $partenaire->Email = $this->Get("Mail");
        $partenaire->Disponible = $this->Get("Disponible");
        $partenaire->Details = $this->Get("Details");
        $partenaire->Save();
        $this->addParent($partenaire);
        genericClass::Save();
		return true;
	}

	function  EnvoiEmailConfirmation() {
		require_once ("Class/Lib/Mail.class.php");
		$user = $this->getUser();
		$Mail = new Mail();
		$Mail->Subject("D.D.F: Confirmation d'inscription");
		$Mail -> From( $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
		$Mail -> ReplyTo($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
		$Mail -> To($this -> Mail);
		$bloc = new Bloc();
		$mailContent = "
			Bonjour " . $this->Civilite . " ".$this->Nom." ".$this->Prenom.",<br />
			Veuillez confirmer votre adresse email et activer votre compte en cliquant sur le lien ci-dessous: <br/>
			<a href='http://reservation.le-dome-du-foot.fr/Reservations/Client/ConfirmEmail?code=".$user->CodeVerif."'>Confirmer votre adresse maintenant</a>";
		$bloc -> setFromVar("Mail", $mailContent, array("BEACON" => "BLOC"));
		$Pr = new Process();
		$bloc -> init($Pr);
		$bloc -> generate($Pr);
		$Mail -> Body($bloc -> Affich());
		$Mail -> Send();
	}

    function  sendConfirmationPartenaireMail($present,$partenaire,$reservation) {
        require_once ("Class/Lib/Mail.class.php");
        $user = $this->getUser();

        $Mail = new Mail();
        $sub = $present ? "D.D.F: Présence Confirmée" : "D.D.F: Absence signalée";
        $Mail -> Subject($sub);
        $Mail -> From( $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $Mail -> ReplyTo($GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT'));
        $Mail -> To($this -> Mail);
        $bloc = new Bloc();
        $mailContent1 = "
			Bonjour " . $this->Civilite . " ".$this->Nom." ".$this->Prenom.",<br />
			Votre partenaire ".$partenaire->Nom." ".$partenaire->Prenom." viens juste de confirmer sa présence lors du match du ".date('d/m/Y à H:i:s',$reservation->DateDebut).".<br/>
			<br />Toute l'équipe du Dome du Foot vous remercie de votre confiance,<br />
            <br />Pour nous contacter : " . $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT') . " .";
        $mailContent2 = "
			Bonjour " . $this->Civilite . " ".$this->Nom." ".$this->Prenom.",<br />
			Votre partenaire ".$partenaire->Nom." ".$partenaire->Prenom." viens juste de nous signaler qu'il ne pourrait pas être présent lors du match du ".date('d/m/Y à H:i:s',$reservation->DateDebut).".<br/>
			<br />Toute l'équipe du Dome du Foot vous remercie de votre confiance,<br />
            <br />Pour nous contacter : " . $GLOBALS['Systeme'] -> Conf -> get('MODULE::RESERVATIONS::CONTACT') . " .";

        $bloc -> setFromVar("Mail", ($present)?$mailContent1:$mailContent2, array("BEACON" => "BLOC"));
        $Pr = new Process();
        $bloc -> init($Pr);
        $bloc -> generate($Pr);
        $Mail -> Body($bloc -> Affich());
        $Mail -> Send();
    }

	function confirmAccount() {
		$this->Actif = 1;
		$this->EmailConfirm = 1;
		$this->Save();

		return true;
	}
}
