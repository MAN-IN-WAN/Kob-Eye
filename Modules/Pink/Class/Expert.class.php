<?php
class Expert extends genericClass {
	
	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}

	function Save() {
		$id = $this->Id;
		if($id) {
			$old = Sys::getData('Pink','Expert/Id='.$id,0,1);
			$old = $old[0];
		}
		else {
			$old = new stdClass;
			$old->Mail = '';
		}
		if($old->Mail != $this->Mail && ! $this->checkLogin($this->Mail)) {
			$err = array(array('message'=>'Cet expert existe déjà.'));
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
			$rec = Sys::getData('Systeme','Group/Nom=PINK_EXPERT',false,0,1);
			$usr->addParent($rec[0]);
		}
		$usr->Login = $this->Mail;
		if($this->Pass != '*******') $usr->Pass = md5($this->Pass);
		$usr->Nom = $this->Nom;
		$usr->Prenom = $this->Prenom;
		$usr->Initiales = $this->Initiales;
		$usr->Informations = $this->Informations;
		$usr->Avatar = $this->Avatar;
		$usr->Mobile = $this->Mobile;
		$usr->Mail = $this->Mail;
		$usr->Tel = $this->Tel;
		$usr->Actif = $this->Actif;
//		$usr->Skin = 'PinkUser';
//		$usr->Style = 'kobeye.swf';
//		$usr->Langue = 'FR';
		$usr->Verify();
		if($this->Error) return array(array('edit',0,'','','','','',$this->Error,null));
		$usr->Save();
		$this->UserId = $usr->Id;
		if(!$id) $this->Id = null;
		parent::Save();
		if($this->Error) return array(array('edit',0,'','','','','',$this->Error,null));
		$ret = array('UserId'=>$usr->Id);
		return array(array($id ? 'edit' : 'add',1,$this->Id,'Pink','Expert','','',null,array('dataValues'=>$ret)));
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
		if($ch) {
			$err = "Cet utilisateur ne peut être effacé.";
			throw new Exception($err);
		}
		return parent::Delete();
	}
	
	function GetExpert() {
		$rec = Sys::$Modules['Pink']->callData("Expert/Id=".(!$this->Id ? '0' : $this->Id),false,0,1);
		$c = count($rec);
		return WebService::WSData('',0,$c,$c,'','','','','',$rec);
	}
	

	function ExpertStatus() {
		$sts = '{"data":[';
		$rec = Sys::$Modules['Pink']->callData("Expert:NOVIEW",false,0,9999,'','','Id,Available,OnLine');
		$comma = false;
		foreach($rec as $r) {
			if($comma) $sts .= ',';
			$comma = true;
			$sts .= '{"id":'.$r['Id'].',"available":'.$r['Available'].',"online":'.$r['OnLine'].'}';
		}
		$sts .= ']}';
		$fp = fopen('Home/tmp/status.htm','w');
		fwrite($fp, $sts);
		fclose($fp);
	}

	function CallMe() {
		return 'call me '.$_POST['expert'];
	}
}