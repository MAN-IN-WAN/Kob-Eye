<?php
class Contact extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function Save($mode=false) {
		$id = $this->Id;
		$this->FullName = trim(trim($this->FirstName).' '.trim($this->Surname));
		genericClass::Save();
		if($mode) return;
/*		
		$tmp = $this->{'Third.ContactThirdId'};
		if(count($tmp)) {
			$t = $tmp[0];
			if($this->CAContact && $t->ContactId != $this->Id) {
				$t->ContactId = $this->Id;
				$mod = true;
			}
			elseif(! $this->CAContact && $t->ContactId == $this->Id) {
				$t->ContactId = 0;
				$mod = true;
			}
			if($this->FIContact && $t->FIContactId != $this->Id) {
				$t->FIContactId = $this->Id;
				$mod = true;
			}
			elseif(! $this->FIContact && $t->FIContactId == $this->Id) {
				$t->FIContactId = 0;
				$mod = true;
			}
			if($mod) $t->Save();
		}
*/
		$res = array('FullName'=>$this->FullName);
		return array(array($id ? 'edit' : 'add', 1, $this->Id, 'Murphy', 'Contact', '', '', null, array('dataValues'=>$res)));
	}
	
	public function GetMailAddresses($add, $flt) {
		$rec = Sys::$Modules['Murphy']->callData("Contact/Email~%$flt+FullName~%$flt",false,0,30,'','','Email,FullName');
		if(is_array($rec) && count($rec)) {
			foreach($rec as $r) $add[] = array('Email'=>$r['Email'], 'Name'=>$r['FullName']);
		}
		return $add;
	}

	public function AddMailContact($msg, $add) {
		if(empty($add)) return;
		$rec = Sys::$Modules['Murphy']->callData("Contact/Email=$add",false,0,1);
		if(is_array($rec) && count($rec)) {
			$ctc = genericClass::createInstance('Murphy', $rec[0]);
			$par = $ctc->getParents('Third');
			$msg->addParent($ctc);
			$msg->addMessageStatus('Mail', 'Third', $par[0]->Id, $msg->Id);
			return true;
		}
		return false;
	}

	public function CheckMailContact() {
		$c = Sys::getCount('Mail', 'Message');
		for($i = 0; $i < $c; $i += 100) {
			echo ">>>>>>>>> $i";
			$msgs = Sys::getData('Mail', 'Message', $i, 100);
			$this->checkMailContact1($msgs);
		}
	}
	
	private function checkMailContact1(&$msgs) {
		foreach($msgs as $msg) {
			$u = false;
			$u = $this->AddMailContact($msg, $this->StripAddress($msg->FromAddress));
			$add = explode(',',$msg->ToAddress);
			foreach($add as $ad) $u |= $this->AddMailContact($msg, $this->StripAddress($ad));
			$add = explode(',',$msg->CcAddress);
			foreach($add as $ad)$u |= $this->AddMailContact($msg, $this->StripAddress($ad));
			if($u) {
				$msg->Save(true);
				echo "x\n";
			}
		}		
	}
	
	private function StripAddress($add) {
		$p = strpos($add, '<');
		$q = strpos($add, '>');
		return(substr($add, $p+1, $q-$p-1));
	}
	
}
