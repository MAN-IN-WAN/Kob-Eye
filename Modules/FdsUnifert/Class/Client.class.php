<?

/**
 * Ajout d'un utilisateur automatique pour un Client
 *
 */
class Client extends genericClass {

  var $Pass;

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
		$this->Pass = $this->makePassword();
		$Utilisateur = genericClass::createInstance("Systeme","User");
		//$Utilisateur->Set("Nom",$this->Get("Nom")); 
		//$Utilisateur->Set("Prenom",$this->Get("Prenom")); 
		$Utilisateur->Set("Mail",$this->Get("Code").$this->Get("Mail"));
		$Utilisateur->Set("Login",$this->Get("Code"));
		//$Utilisateur->Set("Adresse",$this->Get("Adresse"));
		//$Utilisateur->Set("CodPos",$this->Get("CodePostal"));
		//$Utilisateur->Set("Ville",$this->Get("Ville"));
		//$Utilisateur->Set("Pays",$this->Get("Pays"));
		//$Utilisateur->Set("Tel",$this->Get("Tel"));
		$Utilisateur->Set("Pass",$this->Pass);
		$Utilisateur->Set("Admin","0");
		$Utilisateur->AddParent("Systeme/Group/3");
		return $Utilisateur;
	}

	function updateUser($Utilisateur=null) {
		if($Utilisateur == null) $Utilisateur = $this->getUser();
		if($Utilisateur == null) return;
		$Utilisateur->Set("Nom",$this->Get("Nom")); 
		$Utilisateur->Set("Prenom",$this->Get("Prenom")); 
		$Utilisateur->Set("Mail",$this->Get("Mail"));
		$Utilisateur->Set("Login",$this->Get("Code"));
		$Utilisateur->Set("Adresse",$this->Get("Adresse"));
		$Utilisateur->Set("Tel",$this->Get("Tel"));
		$Utilisateur->Set("CodPos",$this->Get("CodePostal"));
		$Utilisateur->Set("Ville",$this->Get("Ville"));
		$Utilisateur->Set("Adresse",$this->Get("Adresse"));
		$Utilisateur->Set("Pays",$this->Get("Pays"));
		if($this->Pass != ''){
		  $Utilisateur->Set("Pass",$this->Pass);
		}
		$Utilisateur->Set("Admin","0");
		$Utilisateur->AddParent("Systeme/Group/3");
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
	/*	$this->CodPos = $U->CodePostal;*/
		$this->Ville = $U->Ville;
		$this->Pays = $U->Pays;
	}

	/**
	* Ajoute la vérification appdes paramètres utilisateur (surtout les mails)
	*
	* @return bool
	*/
	function Verify($need_user=0) {
		//klog::l("Vérify","j y passe");
		if ($need_user==1) {
			if (!empty($this->UserId))$Utilisateur = $this->getUser();
			else $Utilisateur = $this->makeUser();
			$Utilisateur->Verify();
			genericClass::Verify();
			//$this->Error = $Utilisateur->Error;
			$Errors = Array();
			if (isset($Utilisateur->Error) && is_array($Utilisateur->Error))foreach ($Utilisateur->Error as $E){

				$f= false;
				foreach ($Errors as $e)if ($e["Prop"]==$E["Prop"])$f=true;
				if (!$f)$Errors[] = $E;
			}
			if (isset($this->Error)&&is_array($this->Error))foreach ($this->Error as $E){
				$f= false;
				foreach ($Errors as $e)if ($e["Prop"]==$E["Prop"])$f=true;
				if (!$f)$Errors[] = $E;
			}
			$this->Error = $Errors;

			return !sizeof($this->Error);
		}
		return genericClass::Verify();
	}
	
	function getUser(){
		$U = Sys::$Modules["Systeme"]->callData("Systeme/User/".$this->UserId);
		return genericClass::createInstance('Systeme',$U[0]);
	}	
	/**
	* Verification supplementaire pour validation du dossier 
	*
	* @return boolean
	*/
	function isCorrect() {
		$Result = true;
		$Name = $this->Prenom . " " . $this->Nom;
		if ( empty($this->Pays) ) {
			$Result = false;
			$this->addError(array("Message" => "Le pays de ". $Name . " n'est pas renseign&eacute;") );
		}
		if ( empty($this->DateNaissance) ) {
			$Result = false;
			$this->addError(array("Message" => "La date de naissance de ". $Name . " n'est pas renseign&eacute;") );
		}
				return $Result;
	}
	
	/**
	* Renvoi le catalogue de Fds pour un client de type Groupe 
	*
	* @return bool
	*/
	function catalogueFds() {
		$tablefds=array();
		$clis = $this->getChildren(Client);
		foreach($clis as $cli){
			$fdss = $cli->getParents('Fds');
			if(!sizeof($fdss)) continue;
			foreach($fdss as $fds){
				$tablefds[$fds->Id]= $fds;
			}
		}
		ksort($tablefds);
		return $tablefds;
	}
	
	
	
	/**
	* Surcharge de l'enregistrement pour ajouter un utilisateur 
	*
	* @return bool
	*/
	function Save($need_user=0) {

		if ($this->Verify(1)){
			if($need_user==1){
				if($this->Id!="") {
					if($this->UserId!="") {
						$Utilisateur = $this->getUser();
						$Utilisateur = $this->updateUser($Utilisateur);
						$Utilisateur->Save();
					} else {
						$Utilisateur = $this->makeUser();
						$Utilisateur->Save();
						$this->Set("UserId",$Utilisateur->Get("Id"));
					}
				} else {
					$Utilisateur = $this->makeUser();
					$Utilisateur->Save();
					$this->Set("UserId",$Utilisateur->Get("Id"));
				}
			}
			genericClass::Save();
			
			$obj = $this->getObjectClass();
			sqlCheck::CheckKeys($obj);
			
			return true;
		}
		
		if(sizeof($this->Error)){
			$adminMail = $GLOBALS['Systeme']->Conf->get('GENERAL::INFO::ADMIN_MAIL');
	
			$sujet ='Unifert : Erreur lors de l\'enregistrement du client '. $this->Societe;
			
			$content = 'Rapport d\'erreur : <br/>';
			$content .= 'Code client : '.$this->Code .'<br/>';
			$content .= 'Erreur : '.print_r($this->Error,true) .'<br/>';		
				

			
			//Prise en compte de Mail.bl
			$bloc = new Bloc();
			$bloc->setFromVar("Mail",$content,array("BEACON"=>"BLOC"));
			$Pr = new Process();
			$bloc->init($Pr);
			$bloc->generate($Pr);
			
			
			//Creation du mail.
			$mail = new Mail();
			$mail->Subject($sujet);
			$mail->From('noreply@unifert.fr');
			$mail->To($adminMail); 
			$mail->Cc('gcandella@abtel.fr');
			$mail->Cc('myriam@abtel.fr'); 
			//$mail->ReplyTo($adminMail);
			//$mail->Body($content);
			$mail->Body($bloc->Affich());
			$mail->Priority('1');
			$mail->BuildMail();
			$mail->Send();
		}
		
		klog::l('client code',$this->Code);
		klog::l('error',$this->Error);
		return !sizeof($this->Error);
	}





}
