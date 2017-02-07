<?php
class Adherent extends genericClass {

	function Save() {
		// check login & password
		$pwd = Sys::getData('Systeme','User/Login='.$this->Login.'&Pass='.md5($this->Pass),0,1);
		$pwd = is_array($pwd) && count($pwd) ? $pwd[0] : null;
		$err = array(array('message'=>'Le couple utilisateur, mot de passe existe déjà.'));
		// check user
		$tmp = 'User.UserId';
		$tmp = $this->{$tmp};
		if(count($tmp)) {
			$usr = genericClass::createInstance('Systeme', 'User');
			$usr->initFromId($tmp[0]);
		}
		if($usr) {
			if($pwd && $pwd->Id != $usr->Id) return array(array('edit',0,'','','','','',$err,null));
		}
		else {
			if($pwd) return array(array('edit',0,'','','','','',$err,null));
			$usr = genericClass::createInstance('Systeme','User');
			$rec = Sys::$Modules['Systeme']->callData('Role/AXE_ADHERENT/Group',false,0,1);
			$grp = genericClass::createInstance('Systeme',$rec[0]);
			$usr->addParent($grp);
		}
		$usr->Nom = $this->Nom;
		$usr->Login = $this->Login;
		$usr->Pass = md5($this->Pass);
		$usr->Mail = $this->Mail;
		$usr->Skin = 'Axenergie';
		$usr->Style = 'axenergie.swf';
		$usr->Langue = 'FR';
		$usr->Verify();
		if($this->Error) return array(array('edit',0,'','','','','',$this->Error,null));
		$usr->Save();
		$this->addParent($usr);
		genericClass::Save();
		// check Database
		$dtb = $this->getChildren('Database');
		if(count($dtb)) {
			$dtb = $dtb[0];
			$dtb->Nom = $this->Nom;
			$dtb->Save();
		}
		else {
			$dtb = genericClass::createInstance('Axenergie','Database');
			$dtb->Nom = $this->Nom;
			$dtb->Auto = 0;
			$dtb->addParent($this);
			$dtb->Save();
		}
		// check Book
		$bks = $this->getParents('Book');
		if(sizeof($bks)) {
			$bok = $bks[0];
			$bok->Titre = $this->Nom;
			$bok->Save();
		}
		else {
			$bok = genericClass::createInstance('Flipbook','Book');
			$bok->Titre = $this->Nom;
			$bok->Save();
			$this->addParent($bok);
			parent::Save();
		}
		genericClass::Save();
		if($this->Error) return array(array('edit',0,'','','','','',$this->Error,null));
		$ret = array('User.UserId'=>array($usr->Id),'Book.BookId'=>array($bok->Id));
		return array(array('add',1,$this->Id,'Axenergie','Adherent','','',null,array('dataValues'=>$ret)));
	}

	function UploadUser($csv) {
		if(!$csv) return WebService::WSStatus('method',0,'','','','','',array(array("Fichier non renseigné")),null);
		$file = file($csv);
		$n = count($file);
		if(! $n) return WebService::WSStatus('method',0,'','','','','',array(array("Fichier vide")),null);
		for($i = 2; $i < $n; $i++) {
			$line = explode(';', $file[$i]);
			$tmp = trim($line[12]);
			if(empty($line[10]) || empty($tmp)) continue;
			$rec = Sys::$Modules['Axenergie']->callData("Adherent/Login=".$line[3], false, 0, 1);
			if(is_array($rec) && count($rec)) {
				$p = genericClass::createInstance("Axenergie",$rec[0]);
				$tmp = 'User.UserId';
				$p->{$tmp} = $p->getParents('User');
			}
			else $p = genericClass::createInstance("Axenergie",'Adherent');
			$p->Enseigne = $line[0];
			$p->Nom = $line[1].' '.$line[2];
			$p->Adresse = $line[4];
			$p->CodPos = $line[5];
			$p->Ville = $line[6];
			$p->Tel = $line[7];
			$p->Fax = $line[8];
			$p->Portable = $line[9];
			$p->Mail = $line[10];
			$p->SiteWeb = $line[11];
			$p->Login = $line[3];
			$p->Pass = trim($line[12]);
			$p->Save();
		}
		return WebService::WSStatus('method',1,'','Axenergie','Adherent','','',null,null);
	}

}