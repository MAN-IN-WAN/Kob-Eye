<?php

class MailMurphy {
	
	static function addMailContact(&$msg, $adr) {
		genericClass::createInstance('Murphy', 'Contact')->AddMailContact($msg, $adr);
	}
	

	static function getData($idCreator, $object) {
		if($idCreator) {
			if($object == 'Enquiry' || $object == 'Contract' || $object == 'Third') {
				$data = array();
				$par = genericClass::createInstance('Murphy', $object);
				$par->initFromId($idCreator);
				if($object == 'Enquiry' || $object == 'Contract') {
					$tmp = $object.'Id'.$object;
					$data[$tmp] = $idCreator;
					$data['Subject'] = $object.' '.$par->Reference.' ';
					$data['Body'] = $object.' '.$par->Reference."\n\n";
					if($object == 'Enquiry') {
						$thd = $par->getParents('Third.EnquiryBuyerId');
						if(count($thd)) $par = $thd[0];
						else $par = null;
					}
					else $par = null;
				}
				if($par) {
					$ctc = $par->getChildren('Contact/CAContact=1');
					if(count($ctc) && $ctc[0]->Email) {
						$to = $ctc[0]->FullName.' <'.$ctc[0]->Email.'>';
						$data['ToAddress'] = $to;
						if($object == 'Third') $data['ContactIdMessage'] = $ctc[0]->Id;
					}
				}
			}
		}
		return $data;
	}
	
	static function replyParents(&$msg, $org) {
		$par = $org->getParents('Enquiry.EnquiryIdEnquiry');
		if(count($par)) $msg->addParent($par[0]);
		$par = $org->getParents('Contract.ContractIdContract');
		if(count($par)) $msg->addParent($par[0]);
	}
	
	static function checkMurphy(&$msg) {
		$msk = array("#(E[0-9\-]{9,13})#","#(P[0-9\-]{16})#","#(C[0-9\-]{12,16})#","#([0-9]{6}/\-[0-9]{2})#");
		$obj = array('Enquiry','Enquiry','Contract','Contract');

		for($i = 0; $i < 4; $i++) {
			if(preg_match($msk[$i], $msg->Subject, $out) || preg_match($msk[$i], $msg->Body, $out) || preg_match($msk[$i], $msg->HtmlBody, $out)) 
				MailMurphy::checkMurphy1($msg, $obj[$i], $out[0]);
		}
	}
	
	static private function checkMurphy1(&$msg, $obj, $ref) {
		if(substr($ref, 0, 1) == 'P') $ref = 'E'.substr($ref, 1, 13);
		$rec = Sys::$Modules['Murphy']->callData($obj.'/Reference='.$ref, false, 0, 1, '', '', 'Id');
		if(! is_array($rec) || ! count($rec)) return;
		$dos = genericClass::createInstance('Murphy', $rec[0]);		
		$msg->addParent($dos);
	}
	
	static function getMailAddresses(&$add, $flt) {
		$add = genericClass::createInstance('Murphy', 'Contact')->GetMailAddresses($add, $flt);
	}
	
	static function getHeaders(&$msg, &$send) {
		if(count($msg->Parents)) {
			$rec = Sys::$Modules['Murphy']->callData($msg->Parents[0]['Titre']."/".$msg->Parents[0]['Id'], false, 0, 1);
			$par = genericClass::createInstance('Murphy', $rec[0]);
			$send->AddCustomHeader('MURPHY_ID: '.$par->Reference);
		}
	}
	
	static function mailUserParents(&$musr, &$msg) {
		foreach($musr->EnquiryIdEnquiry as $p) {
			$msg->addParent('Murphy/Enquiry/'.$p);
			$msg->addMessageStatus('Mail','Enquiry',$p,$msg->Id);
		}
		foreach($musr->ContractIdContract as $p) {
			$msg->addParent('Murphy/Contract/'.$p);
			$msg->addMessageStatus('Mail','Contract',$p,$msg->Id);
		}
		foreach($musr->ContactIdMessage as $p) {
			$msg->addParent('Murphy/Contact/'.$p);
			$msg->addMessageStatus('Mail','Contact',$p,$msg->Id);
		}
	}

}
