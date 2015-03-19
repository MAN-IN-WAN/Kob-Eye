<?php
class Third extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function Save() {
		$id = $this->Id;
		//genericClass::Save();
		// CA Contact management
		if($id) {
			$upd = $this->setContact($this->ContactId, 'CAContact');
			$upd |= $this->setContact($this->FIContactId, 'FIContact');
		}
		// currency
		if(! $this->Currency && $this->Country) {
			$cty = genericClass::createInstance('Murphy', 'Country');
			$cty->initFromId($this->Country);
			$this->Currency = $cty->Currency;
		}
		genericClass::Save();
		if(! $this->Reference) {
			$this->Reference = sprintf('%05d', $this->Id);
			genericClass::Save();
		}
		$res = array('Reference'=>$this->Reference,'Currency'=>$this->Currency);
		// user
		if($this->Login) {
			$usr = $this->getParents('User.ThirdUserId');
			if(count($usr)) {
				$usr = $usr[0];
				$usr->Login = $this->Login;
				$usr->Pass = md5($this->Password);
				$usr->Save();
			}
			else {
				if(! $this->Password) {
					$this->Password = $this->generatePwd();
					$res['Password'] = $this->Password;
				}
				$usr = $this->newUser($this->ContactId);
				$this->addParent($usr);
				parent::Save();
			}
		}
		$ret = array();
		if($upd) $ret[] = Array('edit', 1, null, 'Murphy', 'Contact', null, null, null, null);
		$ret[] =array($id ? 'edit' : 'add', 1, $this->Id, 'Murphy', 'Third', '', '', null, array('dataValues'=>$res));
		return $ret;
	}

	function Delete() {
		$ok = true;
		$p = $this->getParents('Enquiry');
		$ok = count($p) == 0;
		if($ok) {
			$p = $this->getParents('Proposal');
			$ok = count($p) == 0;
		}
		if($ok) {
			$p = $this->getParents('Contract');
			$ok = count($p) == 0;
		}
		if(!$ok) {
			$err = "This thrid has documents\nIt can't be deleted";
			throw new Exception($err);
		}
		return parent::Detele();
	}

	private function setContact($id, $prop) {
		$ca = $this->getChildren("Contact/$prop=1");
		foreach($ca as $ct) {
			if($ct->Id != $id) {
				$ct->$prop = 0;
				$ct->Save(true);
				$upd = true;
			}
			else $fnd = true;
		}
		if($id && ! $fnd) {
			$ct = genericClass::createInstance('Murphy', 'Contact');
			$ct->initFromId($id);
			$ct->$prop = 1;
			$ct->Save(true);
			$upd = true;
		}
		return $upd;
	}

	private function generatePwd($length = 6) {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$count = strlen($chars);
		for ($i = 0, $result = ''; $i < $length; $i++) {
			$index = rand(0, $count - 1);
			$pwd .= mb_substr($chars, $index, 1);
		}
		return $pwd;
	}


	private function newUser($cid) {
		$rec = Sys::$Modules['Systeme']->callData('Group/Nom=MWCCLIENT', false, 0, 1);
		if(! is_array($rec) || !count($rec)) {
			$msg = 'Group MWCCLIENT not found';
			Error::AddError('User creation:'.$msg, $this);
			throw(new Exception($msg));
		}
		$g = genericClass::createInstance('Systeme', $rec[0]);
		$u = genericClass::createInstance('Systeme', 'User');
		$u->Login = $this->Login;
		$u->Pass = md5($this->Password);
		$u->Nom = $this->Company;
		$u->Mail = 'mwc'.$this->Id.'@mwc.com';
		$u->Actif = true;
		$u->addParent($g);
		$u->Save();
		//
		$m = new PHPMailer();
		$m->SetFrom('mwc@murphywinecompany.com', 'Murphy Wine Company');
		$c = genericClass::createInstance('Murphy', 'Third');
		$c->initFromId($cid);
		$m->AddAddress($c->Email, $c->FullName);
		//$m->AddAddress('paul@abtel.fr', $c->FullName);  // $c->Email
		$m->AddAddress('enguerrand@abtel.fr', $c->FullName);  // $c->Email
		//$m->AddAddress('anya@murphywinecompany.com', $c->FullName);  // $c->Email
		$m->Subject = 'Murphy Wine Company : User account information';
		$m->IsHTML(false);
		$m->Body = "Company :  ".$this->Company."\n\nLogin :  ".$this->Login."\nPassword :  ".$this->Password;
		$res = $m->Send();
		return $u;
	}

	public function GetUser() {
		$u = $this->getParents('User');
		if(! $u || ! is_array($u) || ! count($u)) return $this->CreateUser('');
		return $u[0];
	}
	
	public function CreateUser($log,$pwd) {
		try {
			$u = $this->checkUser($log,$pwd);
		} catch(Exception $e) {
			return WebService::WSStatus('method', 0, '', '', '', '', '', array(array('message'=>$e->getMessage())), null);
		}
		$res = array('User.ThirdUserId'=>array($u->Id),'Login'=>$log,'Password'=>$pwd);
		return WebService::WSStatus('edit', 1, '', '', '', '', '', null, array('dataValues'=>$res));
	}
	
	private function checkUser($log=null,$pwd=null) {
		if(!$log) $log = 'mwc'.$this->Id;
		if(!$pwd) $pwd = 'murphy';
		$rec = Sys::$Modules['Systeme']->callData('User/Login='.$log, false, 0, 1);
		if(is_array($rec) && count($rec)) 
			$u = genericClass::createInstance('Systeme', $rec[0]);
		else {
			$rec = Sys::$Modules['Systeme']->callData('Group/Nom=MWCCLIENT', false, 0, 1);
			if(! is_array($rec) || !count($rec)) {
				$msg = 'Group MWCCLIENT not found';
				Error::AddError('User creation:'.$msg, $this);
				throw(new Exception($msg));
			}
			$g = genericClass::createInstance('Systeme', $rec[0]);
			$u = genericClass::createInstance('Systeme', 'User');
			$u->Login = $log;
			$u->Pass = md5($pwd);
			$u->Nom = $this->Company;
			$u->Mail = $log.'@mwc.com';
			$u->Actif = true;
			$u->addParent($g);
			$u->Save();
			
			//
			$m = new PHPMailer();
			$m->SetFrom('mwc@murphywinecompany.com', 'Murphy Wine Company');
			$m->AddAddress($this->Email, $this->Company);
			//$m->AddAddress('paul@abtel.fr', $c->FullName);  // $c->Email
			$m->AddAddress('enguerrand@abtel.fr', $c->FullName);  // $c->Email
			//$m->AddAddress('anya@murphywinecompany.com', $c->FullName);  // $c->Email
			$m->Subject = 'Murphy Wine Company : User account information';
			$m->IsHTML(false);
			$m->Body = "Company :  ".$u->Nom."\nLogin :  ".$u->Login."\nPassword :  ".$pwd;
			$res = $m->Send();
		}
		$this->addParent($u);
		$this->Login = $log;
		$this->Password = $pwd;
		$this->Save();
		return $u;
	}
	
	public function SendMail($subject, $var, $title, $ca=true, $doc=null, $contact=null) {
		// contact
		$c = genericClass::createInstance('Murphy', 'Contact');
		if($contact) $c->initFromId($contact);
		if(! $c->Email) {
			$c = genericClass::createInstance('Murphy', 'Contact');
			if(! $ca) $id = $this->FIContactId;
			if(! $id) $id = $this->ContactId;
			$c->initFromId($id);
		}
		if(! $c->Email) {
			$msg = "Mail not sent : $this->Company\nSubject:$subject\nError:Not default contact or no email address";
			KError::AddError($msg, $this);
			return;
		}
		$usr = $this->getParents('User'); //$this->GetUser();
		if(! is_array($usr) || ! count($usr)) {
			try {
				$sur = $this->checkUser();
			} catch(Exception $e) {
				$msg = "Mail not sent : $this->Company\nSubject:$subject\nException:";
				$msg .= $e->getMessage();
				KError::AddError($msg, $this);
				return;
			}
		}
		else $usr = $usr[0];
//klog::l(">>>>>>>>".$c->Email);
		// mail		
		$url = '<a href="http://client.murphywinecompany.com?login='.$usr->Login.'&codeverif='.$usr->CodeVerif.'&passmd5='.$usr->Pass.'&'.$var.'">';
		$url .= $title.'</a>';
		$bl = new Bloc();
		$bl->setFromVar('Mail', $url, 'BLOC');
		$bl->init();
		$m = new PHPMailer();
		$m->SetFrom('mwc@murphywinecompany.com', 'Murphy Wine Company');
		$m->AddAddress($c->Email, $c->FullName);
		//$m->AddAddress('paul@abtel.fr', $c->FullName);  // $c->Email
		//$m->AddAddress('enguerrand@abtel.fr', $c->FullName);  // $c->Email
		//$m->AddAddress('myriam@abtel.fr', $c->FullName);  // $c->Email
		//$m->AddAddress('bertrand@abtel.fr', $c->FullName);  // $c->Email
		//$m->AddAddress('anya@murphywinecompany.com', $c->FullName);  // $c->Email
		$m->Subject = $subject;
		$m->IsHTML(true);
		$m->Body = $bl->Affich();
		if($doc) $m->AddAttachment($doc);
		$res = $m->Send();
//klog::l(">>>>>>>>>>EMAIL:$res:");
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxx:3:$res:",$m->ErrorInfo);	
	}

	/**
	 * getMailList
	 * Recupere la liste des mails pour un tiers tout contact confondus
	 * Permet la négociation en liste bufferisée avec les paramètres standards getData.
	 * @return Array[Array[String]]
	 */
	function GetMailList($id, $offset, $limit, $sort, $order, $filter, $thirdId) {
		//Requete
		$req = 'Third/'.$thirdId.'/Contact/*/Message';
		$items = Sys::$Modules['Murphy']->callData($req, false, $offset, $limit, $order, $ord);
		$c = is_array($items) && count($items);
		return WebService::WSData('',0,$c,$c,$req,'','','','',(!$c)?Array():$items);
	}


	function ChangeBroker() {
		$sql = 'update `##_Murphy-Enquiry` set BrokerId='.$this->BrokerId.' where EnquiryBuyerId='.$this->Id;
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$GLOBALS['Systeme']->Db[0]->exec($sql);
		$sql = 'update `##_Murphy-Proposal` set BrokerId='.$this->BrokerId.' where ProposalBuyerId='.$this->Id;
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$GLOBALS['Systeme']->Db[0]->exec($sql);
		$sql = 'update `##_Murphy-Contract` set BrokerId='.$this->BrokerId.' where ContractBuyerId='.$this->Id;
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$GLOBALS['Systeme']->Db[0]->exec($sql);
		return WebService::WSStatus('method',1,'','','','','',null,null);
	}
}
