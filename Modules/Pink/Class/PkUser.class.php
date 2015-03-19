<?php


class PkUser extends genericClass {
	
	var $Pass;


	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
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
		$Utilisateur->Set("Nom",$this->Get("Mail")); 
		$Utilisateur->Set("Mail",$this->Get("Mail"));
		$Utilisateur->Set("Login",$this->Get("Mail"));
		$Utilisateur->Set("Tel",$this->Get("Tel"));
		$Utilisateur->Set("Pass",$this->Pass);
		$Utilisateur->Set("Admin","0");
		$GrpUser = Sys::getData('Systeme','Group/Nom=PINK_USER',false,0,1);
		$Utilisateur->AddParent($GrpUser[0]);
		return $Utilisateur;
	}
	function updateUser($Utilisateur=null) {
		if($Utilisateur == null) $Utilisateur = $this->getUser();
		if($Utilisateur == null) return;
		$Utilisateur->Set("Nom",$this->Get("Mail")); 
		$Utilisateur->Set("Mail",$this->Get("Mail"));
		$Utilisateur->Set("Login",$this->Get("Mail"));
		$Utilisateur->Set("Tel",$this->Get("Tel"));
		$Utilisateur->Set("Admin","0");
		$GrpUser = Sys::getData('Systeme','Group/Nom=PINK_USER',false,0,1);
		$Utilisateur->AddParent($GrpUser[0]);
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
		$this->Nom = $U->Mail;
		$this->Mail = $U->Mail;
		$this->Login = $U->Login;
		$this->Tel = $U->Tel;
	}

	/**
	* Ajoute la vérification appdes paramètres utilisateur (surtout les mails)
	*
	* @return bool
	*/
	function Verify($need_user=0) {
		if ($need_user==1) {
			if (!empty($this->Utilisateur))$Utilisateur = $this->getUser();
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
		return $Result;
	}
	/**
	* Surcharge de l'enregistrement pour ajouter un utilisateur 
	*
	* @return bool
	*/
	function SaveUser($need_user=0) {
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

		return true;
	}





	function Save() {
		$id = $this->Id;
		if($id) {
			$old = Sys::getData('Pink','PkUser/Id='.$id,0,1);
			$old = $old[0];
		}
		else {
			$old = new stdClass;
			$old->Mail = '';
		}
		if($old->Mail != $this->Mail && ! $this->checkLogin($this->Mail)) {
			$err = array(array('message'=>'Cet utilisateur existe déjà.'));
			return array(array('edit',0,'','','','','',$err,null));
		}
		if($this->UserId) {
			$usr = genericClass::createInstance('Systeme', 'User');
			$usr->initFromId($this->UserId);
		}
		if(! $usr) {
			if($this->Pass == '*******') {
				$err = array(array('message'=>'Le mot de passe est obligatoire.'));
				return array(array('edit',0,'','','','','',$err,null));
			}
			$usr = genericClass::createInstance('Systeme','User');
		}
		$rec = Sys::getData('Systeme','Group/Nom=PINK_USER',false,0,1);
		$usr->addParent($rec[0]);
		$usr->Login = $this->Mail;
		if($this->Pass != '*******') $usr->Pass = md5($this->Pass);
		$usr->Mail = $this->Mail;
		$usr->Tel = $this->Tel;
		$usr->Actif = $this->Actif;
//		$usr->Skin = 'PinkUser';
//		$usr->Style = 'kobeye.swf';
//		$usr->Langue = 'FR';
		
		//$usr->Verify();
		if($usr->Error) {
			return array(array('edit',0,'','','','','',$usr->Error,null));
		}
		$usr->Save();
		$this->UserId = $usr->Id;
		if(!$id) $this->Id = null;
		parent::Save();
		if($this->Error) return array(array('edit',0,'','','','','',$this->Error,null));
		$ret = array('UserId'=>$usr->Id);
		return array(array($id ? 'edit' : 'add',1,$this->Id,'Pink','PkUser','','',null,array('dataValues'=>$ret)));
	}

	private function checkLogin($login) {
		$rec = Sys::getData('Systeme','User/Mail='.$login,0,1);
		return count($rec) == 0;
	}



	function Delete() {
		$ch = false;
		$cd = $this->getChildren('Call');
		if(count($cd)) $ch = true;
		$cd = $this->getChildren('Message');
		if(count($cd)) $ch = true;
		$cd = $this->getChildren('Vote');
		if(count($cd)) $ch = true;
		$cd = $this->getChildren('Payment');
		if(count($cd)) $ch = true;
		if($ch) {
			$err = "Cet utilisateur ne peut être effacé.";
			throw new Exception($err);
		}
		return parent::Delete();
	}
	
	function GetPkUser() {
		$rec = Sys::$Modules['Pink']->callData("PkUser/Id=".(!$this->Id ? '0' : $this->Id),false,0,1);
		$c = count($rec);
		return WebService::WSData('',0,$c,$c,'','','','','',$rec);
	}
	

}