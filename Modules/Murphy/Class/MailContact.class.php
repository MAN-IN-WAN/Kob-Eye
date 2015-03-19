<?php
class MailContact extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function GetMessage($id) {
		if(! $id) return WebService::WSData('',0,'','','','','','','',array());

		$ms = $this->getParents('MailContactMessageId');
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxx GetMessage ",$ms);
		$m = $ms[0];
		$att = array();
		$at = $m->getChilds('Attachment');
		foreach($at as $a)
			$att[] = array('Id'=>$a->Id,'Doc'=>$a->Doc);
		$data = array('Id'=>$m->Id,'FromAddress'=>$m->FromAddress,'ToAddress'=>$m->ToAddress,'CcAddress'=>$m->CcAddress,'Subject'=>$m->Subject,'Body'=>$m->Body,'attachment'=>$att);
		return WebService::WSData('',0,'','','','','','','',array($data));
	}

	function SynchMail() {
		$msg = genericClass::createInstance('Mail', 'Message');
		$msg->GetMail();
		return;
		
		$sql = "select m.Id from `##_Mail-Message` m left join `##_Murphy-MailContact` t on t.MailContactMessageId=m.Id where t.Id is null;";	
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		foreach($rec as $rc) {
			$rec = Sys::$Modules['Mail']->callData('Message/'.$rc['Id'], false, 0, 1);
			$msg = genericClass::createInstance('Mail', $rec[0]);
			$this->AddAddress($msg, $msg->FromAddress, 'from');
			$add = explode(',', $msg->ToAddress);
			foreach($add as $ad)
				$this->AddAddress($msg, $ad, 'to');
			$add = explode(',', $msg->CcAddress);
			foreach($add as $ad)
				$this->AddAddress($msg, $ad, 'cc');
//			$add = explode(',', $msg->Cci);
//			foreach($add as $ad)
//				$this->addAddress($msg, $ad, 'cci');
			if(preg_match("#(E[0-9]{6})\-([0-9]{2})#", $msg->Subject, $out))
				$this->checkEnquiry($msg, $out[0]);
			elseif(preg_match("#([0-9]{6})\/([0-9]{2})#", $msg->Body, $out))
				$this->checkEnquiry($msg, $out[0]);
		}
	}

	function GetMail($msg) {
		if($msg->InReplyTo) {
			$rec = Sys::$Modules['Mail']->callData('Message/'.$msg->InReplyTo,false,0,1);
			$m = genericClass::createInstance('Murphy', $rec[0]);
			$par = $m->getParents('Enquiry.EnquiryIdEnquiry');
			if(count($par)) $msg->addParent($par[0]);
			$par = $m->getParents('Contract.ContractIdContract');
			if(count($par)) $msg->addParent($par[0]);
		}
		return $msg;
	}
	
	public function AddAddress($msg, $add, $type) {
		if(! $add) return;
		$p = strpos($add, '<');
		$add = substr($add, $p+1, -1);
		$rec = Sys::$Modules['Murphy']->callData('Contact/Email='.$add, false, 0, 1);
		if(! is_array($rec) || ! count($rec)) return false;
		$ct = genericClass::createInstance('Murphy', $rec[0]);	

		$rec = Sys::$Modules['Murphy']->callData('MailContact/MailContactMessageId='.$msg->Id.'&MailContactContactId='.$ct->Id.'&Type='.$type, false, 0, 1);			
		if(is_array($rec) && count($rec)) return false;
		$m = genericClass::createInstance('Murphy', 'MailContact');
		$m->Type = $type;
		$m->addParent($msg);
		$m->addParent($ct);
		$m->Save();
	}
	
	private function checkEnquiry($msg, $ref) {
		$num = Utils::KEAddSlashes(array($ref));
		$rec = Sys::$Modules['Murphy']->callData('Enquiry/Reference='.$num, false, 0, 1);
//$GLOBALS["Systeme"]->Log->log("ref: ".$num);
		if(! is_array($rec) || ! count($rec)) return false;
		$inq = genericClass::createInstance('Murphy', $rec[0]);		
		$msg->addParent($inc);
		$msg->Save();
	}

	public function AddMailContact($msg, $add) {
		$rec = Sys::$Modules['Murphy']->callData("Contact/Email=$add",false,0,1);
		if(is_array($rec) && count($rec)) {
			$this->addParent($msg);
			$this->addParent('Murphy/Contact/'.$rec[0]['Id']);
			$this->Save();
		}
	}

}
