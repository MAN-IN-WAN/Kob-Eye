<?

/**
 * Ajout d'un utilisateur automatique pour un Client
 *
 */
class Client extends genericClass {

  var $Pass; // :: string

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
		//$this->Pass = $this->makePassword();
		$Utilisateur = genericClass::createInstance("Systeme","User");
		$Utilisateur->Set("Nom",$this->Get("Nom")); 
		$Utilisateur->Set("Prenom",$this->Get("Prenom")); 
		$Utilisateur->Set("Mail",$this->Get("Mail"));
		$Utilisateur->Set("Login",$this->Get("Pseudonyme"));
		$Utilisateur->Set("Adresse",$this->Get("Adresse"));
		$Utilisateur->Set("Tel",$this->Get("Tel"));
		$Utilisateur->Set("CodPos",$this->Get("CodPos"));
		$Utilisateur->Set("Ville",$this->Get("Ville"));
		$Utilisateur->Set("Adresse",$this->Get("Adresse"));
		$Utilisateur->Set("Pays",$this->Get("Pays"));
//		$Utilisateur->Set("Pass",$this->Get("Pass"));
		$Utilisateur->Set("Pass",$this->Pass);
		$Utilisateur->Set("Admin","0");
		$Utilisateur->AddParent("Systeme/Group/2");
		return $Utilisateur;
	}
	function updateUser($Utilisateur) {
		$Utilisateur->Set("Nom",$this->Get("Nom")); 
		$Utilisateur->Set("Prenom",$this->Get("Prenom")); 
		$Utilisateur->Set("Mail",$this->Get("Mail"));
		$Utilisateur->Set("Login",$this->Get("Login"));
		$Utilisateur->Set("Adresse",$this->Get("Adresse"));
		$Utilisateur->Set("Tel",$this->Get("Tel"));
		$Utilisateur->Set("CodPos",$this->Get("CodPos"));
		$Utilisateur->Set("Ville",$this->Get("Ville"));
		$Utilisateur->Set("Adresse",$this->Get("Adresse"));
		$Utilisateur->Set("Pays",$this->Get("Pays"));
		$Utilisateur->Set("Admin","0");
		$Utilisateur->AddParent("Systeme/Group/2");
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
		$this->CodPos = $U->CodPos;
		$this->Ville = $U->Ville;
		$this->Pays = $U->Pays;
	}

	/**
	* Ajoute la vérification appdes paramètres utilisateur (surtout les mails)
	*
	* @return bool
	*/
	function Verify($need_user=0) {
		if ($need_user==1) {
			if ($this->Utilisateur!=0)$Utilisateur = $this->getUser();
			else $Utilisateur = $this->makeUser();
			$Utilisateur->Verify();
			genericClass::Verify();
			//$this->Error = $Utilisateur->Error;
			$Errors = Array();
			if (is_array($Utilisateur->Error))foreach ($Utilisateur->Error as $E){
				$f= false;
				foreach ($Errors as $e)if ($e["Prop"]==$E["Prop"])$f=true;
				if (!$f)$Errors[] = $E;
			}
			if (is_array($this->Error))foreach ($this->Error as $E){
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
		$U = $GLOBALS["Systeme"]->Modules["Systeme"]->callData("Systeme/User/".$this->UserId);
		return genericClass::createInstance('Systeme',$U[0]);
	}	
	/**
	* Verification supplementaire pour validation du dossier H2O
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
	* Surcharge de l'enregistrement pour ajouter un utilisateur 
	*
	* @return bool
	*/
	function Save($need_user=0) {
		if($this->Id!="") {
			if($need_user==1 && $this->Verify(1) && $this->UserId!="") {
				$Utilisateur = $this->getUser();
				$Utilisateur = $this->updateUser($Utilisateur);
				$Utilisateur->Save();
			}
			elseif($need_user==1 && $this->Verify(1) && $this->UserId=="") {
				$createUser = 1;
				$Utilisateur = $this->makeUser();
				$Utilisateur->Save();
				$this->Set("UserId",$Utilisateur->Get("Id"));
			}
		}
		else {
			if ($need_user==1 && $this->Verify(1)) {
				$createUser = 1;
				$Utilisateur = $this->makeUser();
				$Utilisateur->Save();
			}
			if ( $need_user==1 ) {
				$this->Set("UserId",$Utilisateur->Get("Id"));
			}
		}
		genericClass::Save();
		//Calcul de la note moyenne
		$total=0;
		$evals = $GLOBALS['Systeme']->Modules['Boutique']->callData('Client/' . $this->Id.'/Evaluation');
		if (is_array($evals))foreach($evals as $e) $total += $e['Note'];
		// Enregistrement pour l'utilisateur
		$this->NoteMoyenne = round($total/sizeof($evals), 1);
		//MISE AJOUR TEMPS MOYEN DE REPONSE
		$evals = $GLOBALS['Systeme']->Modules['Boutique']->callData('Client/' . $this->Id.'/LigneCommande');
		if (is_array($evals))foreach($evals as $e) $total += $e['TempsReponse'];
		// Enregistrement pour l'utilisateur
		$this->TempsMoyenReponse = round($total/sizeof($evals), 1);
		genericClass::Save();
		return true;
	}

	/**
	 * Renvoie la derniere commande brouillon en cours et gere les cas de commandes en cours
	 * @param r Object reference
	 * @return	Commande brouillon ou false
	 */
	public function getCommande($r) {
		//Cas de la commande valide pour la même reference
		$C = $this->storproc('Boutique/Client/'.$this->Id.'/Commande/LigneCommande.CommandeId(ReferenceId='.$r->Id.')&Valide=1',false,0,1,'DESC','tmsCreate');
		if (is_array($C)&&is_array($C[0]))return genericClass::createInstance('Boutique',$C[0]);

		//Cas de la commande valide pour une autre reference
		$C = $this->storproc('Boutique/Client/'.$this->Id.'/Commande/Valide=1&Paye=0',false,0,1,'DESC','tmsCreate');
		if (is_array($C)&&is_array($C[0]))return genericClass::createInstance('Boutique',$C[0]);

		//On recherche une commande brouillon existante
		$C = $this->storproc('Boutique/Client/'.$this->Id.'/Commande/Valide=0&Paye=0',false,0,1,'DESC','tmsCreate');
		if (is_array($C)&&is_array($C[0])) return genericClass::createInstance('Boutique',$C[0]);

		//On cree une commande brouillon
		$c = genericClass::createInstance('Boutique','Commande');
		$c->addParent($this);
		$c->Save();
		return $c;
	}

	/**
	 * Renvoie la derniere commande en cours de validation
	 * @return	Commande valide ou false
	 */
	public function getCurrentCommande() {
		//On recherche une commande brouillon existante
		$C = $this->storproc('Boutique/Client/'.$this->Id.'/Commande/Valide=1',false,0,1,'DESC','Id');
		if (is_array($C)&&is_array($C[0])) return genericClass::createInstance('Boutique',$C[0]);
		else return false;
	}

	/**
	 * Retourne le nombre de ventes pour ce client
	 * @return	Nombre
	 */
	public function getNbVentes() {
		$ventes = $this->storproc('Boutique/Client/'.$this->Id.'/LigneCommande/Expedie=1',false,0,1,'','','COUNT(DISTINCT(m.Id))');
		return $ventes[0]['COUNT(DISTINCT(m.Id))'];
	}
	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return $GLOBALS['Systeme']->Modules['Boutique']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}

}