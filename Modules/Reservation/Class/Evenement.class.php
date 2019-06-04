<?php
class Evenement extends genericClass {
	function Save() {
		genericClass::Save();
		//On recupere la ville de la salle
		$Valid=0;
		$S = Sys::$Modules["Reservation"]->callData("Reservation/Salle/Evenement/".$this->Id);
		if (is_array($S[0])){
			$Valid=1;
			$this->CodPos = $S[0]["CodPos"];
			$this->Ville = $S[0]["Ville"];
		}
		//Definition de la date de cloture
		if ($this->DateCloture==""){
			$this->DateCloture = $this->DateDebut;
			genericClass::Save();
		}
		//recupere l'objet spectacle de l'evenement
		$S = Sys::$Modules["Reservation"]->callData("Reservation/Spectacle/Evenement/".$this->Id);
		if (is_array($S[0])){
			$Valid=1;
			$S = genericClass::createInstance("Reservation",$S[0]);
			//On met a jour les données du spectacle
			$this->Nom = $S->Nom;
			$S->Save();
		}
		if ($Valid) $this->Valide=1; else $this->Valide=0;
		genericClass::Save();
	}
	
	function Delete() {
		$S=Sys::$Modules["Reservation"]->callData("Reservation/Spectacle/Evenement/".$this->Id,"",0,1);
		if (is_array($S[0])){
			$S = genericClass::createInstance("Reservation",$S[0]);
		}
		genericClass::Delete();
		if (is_object($S)){
			$S->Save();
		}
	}
	
	//Envoie un récapitulatif au responsable de la structure culturelle
	function Envoyer($Force=true){
		//récuperation du spectacle de l'évenement
		$S=Sys::$Modules["Reservation"]->callData("Reservation/Spectacle/Evenement/".$this->Id,"",0,1);
		//récupération de la structure culturelle du spectacle
		$O=Sys::$Modules["Reservation"]->callData("Reservation/Organisation/Spectacle/".$S[0]["Id"],"",0,1);
		//Creation evenement mail echec
		$Em = genericClass::createInstance("Reservation","EvenementMail");
		$Em->AddParent("Reservation/Evenement/".$this->Id);
		//recuperation de l'adresse email de la strutre culturelle
		$Em->Message = $Mail = $O[0]["Mail"];
		if ($Mail==""){
			$Em->Message="Le mail de la structure culturelle n'est pas renseigné";
			$Em->Save();
			return $Em->Message;
		}
		if (!$O[0]["EnvoiMail"]&&!$Force){
			$Em->Message="La structure culturelle n'est pas autorisée à recevoir des mails";
			$Em->Save();
			return $Em->Message;
		}
		if ($O[0]["EnvoiMail"]||$Force){
			// MD : 20190326 - Cas des spectacles qui ont plusieurs séances dans la meme journée
			$dateMail=Utils::getTodayMorning();
//echo $dateMail.PHP_EOL;
			$Semv=Sys::$Modules["Reservation"]->callData("Reservation/Spectacle/".$S[0]["Id"]."/Evenement/".$this->Id."/EvenementMail/tmsCreate>=".$dateMail);
//			var_dump($Semv);
			if ($Semv && sizeof($Semv) && $Force==false) {
//echo $S[0]["Nom"]." toto".PHP_EOL;
				// si on n'a déja envoyé le mail on ne renvoie pas
				$Em->Message=($Force)?"Mail envoyé manuellement avec succés":"Mail déjà envoyé ";
				return $Em->Message;
			}
			// MD 2019-03-26 : changement du texte du message pour ajouter qu'il n'y a pas de réservation

			$Er = Sys::$Modules["Reservation"]->callData("Reservation/Spectacle/".$S[0]["Id"]."/Evenement/".$this->Id."/Reservations");
			if (!$Er || !sizeof($Er)){
				// TRAITEMENT NORMAL
				$Mess =  " <h1>[CULTUREETSPORTSOLIDAIRES34.FR]</h1><p>Bonjour,Ci-joint le lien concernant les réservations pour l'évènement ".$S[0]["Nom"]." à la date du ".date("d/m/Y à H:i",$this->DateDebut).": PAS DE RESERVATION</p>";
			} else {
				$Mess =  "
					<h1>[CULTUREETSPORTSOLIDAIRES34.FR]</h1>
					<p>Bonjour,
					Ci-joint le lien concernant les réservations pour l'évènement ".$S[0]["Nom"]." à la date du ".date("d/m/Y à H:i",$this->DateDebut).":";
			}

			//envoi du mail contenant l'adresse du recapitulatif de l'evenement
			/*
			 * 			<h1>[CULTUREETSPORTSOLIDAIRES34.FR]</h1>
				<p>Bonjour,
				Ci-joint le lien concernant les réservations pour l'évènement ".$S[0]["Nom"]." à la date du ".date("d/m/Y à H:i",$this->DateDebut).":

			 */

			$Mess .=  "
				<a href='http://www.cultureetsportsolidaires34.fr/Reservation/Evenement/".$this->Id."/Imprimer.print'> Accéder aux réservations</a>
				<br />
				<strong>Nous vous remercions d'<span style='text-decoration:underline'>imprimer</span> et de <span style='text-decoration:underline'>conserver</span> ce document après avoir rempli la case <span style='text-decoration:underline'>PRESENCE</span> des publics (et de nous laisser un éventuel commentaire)<br />
				Nous avons besoin de vos retours sur les places proposées qui ont été effectivement utilisées.<br />
				Nous récupérerons l'ensemble des documents lors d'un contact en fin de saison que nous conviendrons ensemble.<br />
				L'équipe culture et sport solidaires 34.
				</p>
			";
			require_once("Class/Lib/Mail.class.php");
			$M = new Mail();
			$M->Subject("[CULTUREETSPORTSOLIDAIRES34.FR] Rapport des reservations ".$S[0]["Nom"]." a la date du ".date("d/m/Y à H:i",$this->DateDebut)." ");
			$M->To($Mail);
			$M->Cc("myriam@abtel.fr");
			$M->Cc("direction@cultureetsportsolidaires34.fr");
			$M->From("noreply@cultureetsportsolidaires34.fr");
			$M->Body($Mess);
			$M->Send();
			$Em->Envoye=true;
			$Em->Message=($Force)?"Mail envoyé manuellement avec succés":"Mail envoyé automatiquement avec succés";
			$Em->Save();
			return $Em->Message;
		}
		return $Em->Message;
	}
}
?>
