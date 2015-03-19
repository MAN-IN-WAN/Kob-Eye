<?php
class Message extends genericClass {

	public $MessageStatus = array();

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
		//*************************************
		if($GLOBALS['Systeme']->isModule('Murphy')) require_once 'Modules/Murphy/Class/MailMurphy.ext.php';
		//*************************************
	}
	
	function Save($import=false) {
		$id = $this->Id;
		
		$this->From = $this->addresses($this->FromAddress);
		$this->To = $this->addresses($this->ToAddress);
		$this->Cc = $this->addresses($this->CcAddress);
		$this->Cci = $this->addresses($this->CciAddress);
		$this->MiniBody = substr(trim( preg_replace( '/\s+/', ' ', strip_tags($this->Body) ) ),0,100);
		
		$att = 0;
		if(! $import) {
			$att = (is_array($this->attachment) && count($this->attachment)) ? 6 : 0;
			$this->Attachments = $att;
		}
		genericClass::Save();
		
		if($att) {
			foreach($this->attachment as $doc) {
				$att = genericClass::createInstance('Mail', 'Attachment');
				$att->Doc = $doc->Doc;
				$att->addParent($this); 
				$att->Save();
			}
		}

		if(! $id) {
			$usr = Sys::$User;
			if(! $import) {
				//$rec = Sys::$Modules['Mail']->callData('Folder/Name=Sent', false, 0, 1);
				//$fld = genericClass::createInstance('Mail', $rec[0]);
				$this->addUser($this->FromAddress, true, $usr, 2); // $fld);
			}
			//$rec = Sys::$Modules['Mail']->callData('Folder/Name=Inbox', false, 0, 1);
			//$fld = genericClass::createInstance('Mail', $rec[0]);
			$add = explode(',', $this->ToAddress);
			foreach($add as $ad) $this->addUser($ad, false, $usr, 1); //$fld);
			$add = explode(',', $this->CcAddress);
			foreach($add as $ad) $this->addUser($ad, false, $usr, 1); //$fld);
			$add = explode(',', $this->CciAddress);
			foreach($add as $ad) $this->addUser($ad, false, $usr, 1); //$fld);
			//*************************************
			if($GLOBALS['Systeme']->isModule('Murphy')) $this->addUser('<mwc@murphywinecompany.com>', false, $usr, 1);
			//*************************************
			parent::Save();
		}
	}


	function saveMessage() {
		$id = $this->Id;
		
		if(! $id) {
			$m = new PHPMailer();
			$this->setAddresses($m, 0, $this->FromAddress);
			$this->setAddresses($m, 1, $this->ToAddress);
			$this->setAddresses($m, 2, $this->CcAddress);
			$this->setAddresses($m, 3, $this->CciAddress);
			$m->Subject = $this->Subject;
			if($this->Body) {
				$m->IsHTML(true);
				$m->Body = $this->Body;
			}
			else $m->Body = '.';
			$m->MessageID = "AppaloosaMail_".microtime(true).'@'.$_SERVER["SERVER_NAME"];
			//*************************************
			if($GLOBALS['Systeme']->isModule('Murphy')) MailMurphy::getHeaders($msg, $m);
			//*************************************
			if(count($this->attachment)) {
				foreach($this->attachment as $doc) $m->AddAttachment($doc->Doc);
			}
			$m->Send();
			$this->IdMessage = $m->MessageID;
			$this->Date = time();
		}
		$this->Save();
	}
	
	function addMessageStatus($module,$parent,$pid,$id) {
		$this->MessageStatus[] = array('add', 1, $id, $module, 'Message', $parent, $pid, null, null);
	}
	

	private function setAddresses($msg, $mode, $add) {
		if(! add) return;
		$add = explode(',', $add);
		foreach($add as $ad) {
			$p = strpos($ad, '<');
			$name = substr($ad, 0, $p);
			$addr = substr($ad, $p+1, -1);
			switch($mode) {
				case 0: $msg->SetFrom($addr, $name); break;
				case 1: $msg->AddAddress($addr, $name); break;
				case 2: $msg->AddCC($addr, $name); break;
				case 3: $msg->AddBCC($addr, $name); break;
			}
		}
	}
	
	
	function Delete() {
		$mu = $this->getChildren('MailUser');
		foreach($mu as $m) $m->Delete();
		$mu = $this->getChildren('Attachment');
		foreach($mu as $m) $m->Delete();
		rmdir('Home/Mail/'.date('Ymd', $this->Date).'/'.$this->Id);
		return parent::Delete(true);
	}

	private function markMailUser($id, $mode) {
		$uid = Sys::$User->Id;
		$rec = Sys::$Modules['Mail']->callData("Message/$id/MailUser/User.MailUserUserId($uid)", false, 0, 1);
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxx markMailUser:".sizeof($rec), $rec);
		if(is_array($rec) && count($rec)) {
			$m = genericClass::createInstance('Mail', $rec[0]);
			switch($mode) {
				case 'read': $upd = $m->Read == 0; $m->Read = 1; break;
				case 'reply':
				case 'replyAll': $upd = $m->Answered == 0; $m->Answered = 1; break;
				case 'forward': $upd = $m->Forwarded == 0; $m->Forwarded = 1; break;
			}
			if($upd) $m->Save(true);
		}
		return $upd ? $m->Id : null;
	}


	private function addUser($add, $sent, $usr, $fld) {
		if(! $add) return;
		if($sent) {
			$mu = genericClass::createInstance('Mail', 'MailUser');
			$mu->addParent($this);
			$mu->addParent($usr);
			$mu->Folder = $fld; //$mu->addParent($fld);
			$mu->Sent = 1;
			$mu->Read = 1;
			$mu->Save(true);
			$sts = $mu->Id;
		}
		else {
			$p = strpos($add, '<');
			$q = strpos($add, '>');
			$ad = substr($add, $p+1, $q-$p-1);
			if(! $ad) return;
			$rec = Sys::$Modules['Systeme']->callData('User/Mail='.$ad, false, 0, 1);
			if(is_array($rec) && count($rec)) {
				$usr = genericClass::createInstance('Systeme', $rec[0]);
				$mu = genericClass::createInstance('Mail', 'MailUser');
				$mu->addParent($this);
				$mu->addParent($usr);
				$mu->Folder = $fld; //$mu->addParent($fld);
				$mu->Sent = 0;
				$mu->Save(true);
				$sts = $mu->Id;
			}
			//*************************************
			if($GLOBALS['Systeme']->isModule('Murphy')) MailMurphy::addMailContact($this, $ad);
			//*************************************
		}
		return $sts;
	} 
	
	private function addresses($add) {
		$s = '';
		$add = explode(',', $add);
		foreach($add as $ad) {
			$p = strpos($ad, '<');
			if($s) $s .= ',';
			$s .= $p > 0 ? substr($ad, 0, $p-1) : substr($ad, $p+1, -1);
		}
		return iconv_mime_decode($s,0,"UTF-8");
	}

	function GetMessage($idCreator, $mode, $idList, $object) {
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxx MS:$idCreator:$mode:$this->Id:$idList:$object");
		if($mode) {
			$m = genericClass::createInstance('Mail', 'Message');
			$m->initFromId($idCreator);
			$par = $m->getParents('Enquiry.EnquiryIdEnquiry');
			if(count($par)) {
				$enqid = $par[0]->Id;
				$enqrf = $par[0]->Reference;
			}
			$par = $m->getParents('Contract.ContractIdContract');
			if(count($par)) {
				$ctrid = $par[0]->Id;
				$ctrrf = $par[0]->Reference;
			} 
//			$body = 'From : '.$m->FromAddress.'\n';
			$body = $m->Body;
			$html = $m->HtmlBody;
			if(! html) $html = $body;
			$date = date('d.m.Y H:i', $m->Date);
			if($mode == "edit") {
				$data = array('FromAddress'=>$m->FromAddress,'ToAddress'=>$m->ToAddress,'CcAddress'=>$m->CcAddress,'CciAddress'=>$m->CciAddress,
							'Subject'=>$m->Subject,'Body'=>$body,'HtmlBody'=>$html,'Date'=>$date,'attachment'=>$m->GetAttachments(),
							'EnquiryIdEnquiry'=>$enqid,'ContractIdContract'=>$ctrid,
							'EnquiryReference'=>$enqrf,'ContractReference'=>$ctrrf,
							'modeReply'=>$mode);
				return WebService::WSStatus('method',1,'','','','','',null,array('javascript'=>array('function'=>'mwc_display_message', 'data'=>$data)));
			}
			if($ctrrf && strpos($ctrrf,$m->Subject)===false && strpos($ctrrf,$m->Body)===false && strpos($ctrrf,$m->HtmlBody)===false) {
				$m->Subject .= '  Contract:'.$ctrrf;
				$body .= "\nContract:$ctrrf";
				$html .= "\n<br/>Contract:$ctrrf";
			}
			if($enqrf && strpos($enqrf,$m->Subject)===false && strpos($enqrf,$m->Body)===false && strpos($enqrf,$m->HtmlBody)===false) {
				$m->Subject .= '  Enquiry:'.$enqrf;
				$body .= "\nEnquiry:$enqrf";
				$html .= "\n<br/>Enquiry:$enqrf";
			}
			if($mode == "reply") {
				$data = array('ToAddress'=>$m->FromAddress,'CcAddress'=>$m->CcAddress,'CciAddress'=>$m->CciAddress,'Subject'=>'Re: '.$m->Subject,
							'Body'=>$body,'HtmlBody'=>$html,'attachment'=>$m->GetAttachments(),
							'EnquiryIdEnquiry'=>$enq,'ContractIdContract'=>$ctr,
							'EnquiryReference'=>$enqrf,'ContractReference'=>$ctrrf,
							'modeReply'=>$mode,'idReply'=>$idCreator,'inReplyTo'=>$m->IdMessage);
//klog::l(">>>>>>>>>>>>>",$data);
				return WebService::WSStatus('method',1,'','','','','',null,array('javascript'=>array('function'=>'mwc_new_message', 'data'=>$data)));
				//return WebService::WSData('',0,'','','','','','','',array($data));
			}
			elseif($mode == "replyAll") {
				$to = array_merge(explode(',', $m->ToAddress), explode(',', $m->CcAddress));
				$usr = Sys::$User;
				$usr = $usr->Mail;
				for($i = 0; $i < sizeof($to);) {
					if(empty($to[$i]) || strpos($to[$i], $usr)) array_splice($to, $i, 1);
					else $i++;
				}
				if(array_search($m->FromAddress, $to) === false) $to[] = $m->FromAddress;
				$data = array('ToAddress'=>$to,'CcAddress'=>$m->CcAddress,'CciAddress'=>$m->CciAddress,'Subject'=>'Re: '.$m->Subject,
							'Body'=>$body,'HtmlBody'=>$html,'attachment'=>$m->GetAttachments(),
							'EnquiryIdEnquiry'=>$enq,'ContractIdContract'=>$ctr,
							'EnquiryReference'=>$enqrf,'ContractReference'=>$ctrrf,
							'modeReply'=>$mode,'idReply'=>$idCreator,'inReplyTo'=>$m->IdMessage);
//klog::l(">>>>>>>>>>>>>",$data);
				return WebService::WSStatus('method',1,'','','','','',null,array('javascript'=>array('function'=>'mwc_new_message', 'data'=>$data)));
				//return WebService::WSData('',0,'','','','','','','',array($data));
			}
			elseif($mode == "forward") {
				$data = array('Subject'=>'Fwd: '.$m->Subject,'Body'=>$body,'HtmlBody'=>$html,'attachment'=>$m->GetAttachments(),
							'EnquiryIdEnquiry'=>$enq,'ContractIdContract'=>$ctr,
							'EnquiryReference'=>$enqrf,'ContractReference'=>$ctrrf,
							'modeReply'=>$mode,'idReply'=>$idCreator,'inReplyTo'=>$m->IdMessage);
				return WebService::WSStatus('method',1,'','','','','',null,array('javascript'=>array('function'=>'mwc_new_message', 'data'=>$data)));
				//return WebService::WSData('',0,'','','','','','','',array($data));
			}
		}
		if(! $this->Id) {
			//********************************************
			if($GLOBALS['Systeme']->isModule('Murphy')) $data = MailMurphy::getData($idCreator, $object);
			//*******************************************
			$data['modeReply'] = 'new';
//klog::l(">>>>>>>>>>>>>",$data);
			return WebService::WSStatus('method',1,'','','','','',null,array('javascript'=>array('function'=>'mwc_new_message', 'data'=>$data)));
			//return WebService::WSData('',0,'','','','','','','',Array($data));
		}
		$muid = $this->markMailUser($this->Id, 'read');
		if($muid) $sts = array(array('edit', 1, $muid, 'Mail', 'MailUser', '', '', null, null));
		$m = $this;
		$par = $m->getParents('Enquiry.EnquiryIdEnquiry');
		if(count($par)) $enq = $par[0]->Id; 
		$par = $m->getParents('Contract.ContractIdContract');
		if(count($par)) $ctr = $par[0]->Id; 
		$dt = date('d/m/y H:i', $m->Date);
		$data = array('Id'=>$m->Id,'FromAddress'=>iconv_mime_decode($m->FromAddress,0,"UTF-8"),
				'ToAddress'=>iconv_mime_decode($m->ToAddress,0,"UTF-8"),
				'CcAddress'=>iconv_mime_decode($m->CcAddress,0,"UTF-8"),
				'CciAddress'=>iconv_mime_decode($m->CciAddress,0,"UTF-8"),
				'Subject'=>$m->Subject,'Body'=>$m->Body,'HtmlBody'=>$m->HtmlBody,
				'Date'=>$dt,'attachment'=>$m->GetAttachments(),'MessageId'=>$m->Id,'MailUserId'=>0,
				'EnquiryIdEnquiry'=>$enq,'ContractIdContract'=>$ctr,
				'EnquiryReference'=>$enqrf,'ContractReference'=>$ctrrf,
				'modeReply'=>'','idReply'=>'','inReplyTo'=>'');
		$data = WebService::WSDataString('',0,'','','','','','','',array($data));
		return WebService::WSStatusMulti($sts, $data);
	}

	
	public function MessageRead() {
	}
	

	function GetAttachments() {
		$att = array();
		$at = $this->getChilds('Attachment');
		foreach($at as $a)
			$att[] = array('Id'=>$a->Id,'Name'=>$a->Name,'Doc'=>$a->Doc,'module'=>'Mail','objectClass'=>'Attachment');
		return $att;
	}


	function SynchMail() {
//klog::l("SYNCHMAIL");
		fileDriver::mk_dir('Home/Mail');
		if(file_exists("Home/Mail/lock.txt")) {
//klog::l(">>>>>SYNCHMAIL Locked");
			return("Locked");
		}
		$fp = fopen("Home/Mail/lock.txt", "w");

		$acc = Sys::getData('Mail', 'Account/Active=1');
		foreach($acc as $ac) $this->synchAccount($ac);
		
		fclose($fp);
		unlink("Home/Mail/lock.txt");
//klog::l("SYNCHMAIL Unlocked");
		
		$sts = array(array('edit', 1, '', 'Mail', 'MailUser', '', '', null, null),
					array('edit', 1, '', 'Mail', 'Message', '', '', null, null));
		return WebService::WSStatusMulti($sts);
	}
	
	private function synchAccount($acc) {
		$mbox = imap_open('{'.$acc->Host.':'.$acc->Port.'/'.$acc->Method.'}', $acc->Login, $acc->Password);
		if(FALSE === $mbox) {
klog::l(">>>>>SYNCHMAIL Connexion Error: ".$acc->Login);
			return "Connexion Error";
		}
		$info = imap_check($mbox);
		if (FALSE === $info) {
klog::l(">>>>>SYNCHMAIL Read Error: ".$acc->Login);
			return "Read Error";
		}

		if($info->Nmsgs) {
			if(empty($acc->Stamp)) $this->synchMessages($mbox, 1, $info->Nmsgs);
			else {
				$set = imap_search($mbox, 'SINCE '.$acc->Stamp);
				foreach($set as $idx) $this->synchMessages($mbox, $idx, 0);
			}
		}
		$dt = explode(' ', $info->Date);
		$acc->Stamp = $dt[1].'-'.$dt[2].'-'.$dt[3];
		$acc->Save();
		imap_close($mbox);
	}

	private function synchMessages($mbox, $first, $last) {
		if($last) $idx = "$first:$last";
		else $idx = $first;
		$mails = imap_fetch_overview($mbox, $idx, 0);
		foreach($mails as $mail) {
			$mid = $mail->message_id;
			if(substr($mid, 0, 1) == '<') {
				$mid = substr($mid, 1, strlen($mid)-2);
				if(substr($mid, -1) == '>') $mid = substr($mid, 0, -1);
			}
			$mid = trim($mid);
//			$rec = Sys::$Modules['Mail']->callData("Message/IdMessage=".Utils::escape($mid),false,0,1,'','','Id');
//			if(is_array($rec) && count($rec)) continue;
			$fid = str_replace("'", "\'", $mid);
			$sql = "select count(*) as cnt from `##_Mail-Message` where IdMessage='$fid'";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			if(! $pdo) return null;
			$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
			if($rec[0]['cnt']>0) continue;
			
			$uid = $mail->uid;
			$head = imap_fetchHeader($mbox, $uid, FT_UID);
			$head = imap_rfc822_parse_headers($head);
			$struc = imap_fetchstructure($mbox, $uid, FT_UID);

			$msg = genericClass::createInstance('Mail', 'Message');
			$msg->IdMessage = $mid;
			$msg->Date = strtotime($head->date);
			$add = $head->from[0];
			$msg->From = iconv_mime_decode($add->personal ? $add->personal : $add->mailbox.'@'.$add->host, 0, "UTF-8");
			$msg->FromAddress = trim(iconv_mime_decode($add->personal,0,"UTF-8").' <'.$add->mailbox.'@'.$add->host.'>');
			$p = ''; $a = '';
			foreach($head->to as $add) {
				if($p) {
					$p .= ',';
					$a .= ',';
				}
				$p .= iconv_mime_decode($add->personal ? $add->personal : $add->mailbox.'@'.$add->host, 0, "UTF-8");
				$a .= trim(iconv_mime_decode($add->personal,0,"UTF-8").' <'.$add->mailbox.'@'.$add->host.'>');
			}
			$msg->To = $p;
			$msg->ToAddress = $a;
			$p = ''; $a = '';
			if(isset($head->cc)) {
				foreach($head->cc as $add) {
					if($p) {
						$p .= ',';
						$a .= ',';
					}
					$p .= iconv_mime_decode($add->personal ? $add->personal : $add->mailbox.'@'.$add->host, 0, "UTF-8");
					$a .= trim(iconv_mime_decode($add->personal,0,"UTF-8").' <'.$add->mailbox.'@'.$add->host.'>');
				}
			}
			$msg->Cc = $p;
			$msg->CcAddress = $a;
			$msg->Subject = iconv_mime_decode($mail->subject,0,"UTF-8");

			$mid = $mail->in_reply_to;
			if($mid) {
				if(substr($mid, 0, 1) == '<') $mid = substr($mid, 1, strlen($mid)-2);
				$rec = Sys::$Modules['Mail']->callData("Message/IdMessage=".Utils::escape($mid),false,0,1,'','','Id');
				if(is_array($rec) && count($rec)) {
					$org = genericClass::createInstance('Mail', $rec[0]);
					$msg->InReplyTo = $org->Id;
					//********************************************
					if($GLOBALS['Systeme']->isModule('Murphy')) MailMurphy::replyParents($msg, $org);
					//********************************************
				}
			}
						
			if($struc->type == 1) {
				$doc = array();
				$html = $txt = '';
				$t = $this->findPart($mbox, $uid, $struc->parts, $doc, $txt, $html);
				$idx = $t[0];
				$doc = $t[1];
				$msg->Body = $txt;
				$msg->HtmlBody = $html;
			}
			else {
				$body = imap_body($mbox, $uid, FT_UID);
				if($struc->encoding == 3) $body = base64_decode($body);
				elseif($struc->encoding == 4) $body = quoted_printable_decode($body);
				$body = $this->charset($struc->parameters, $body);
				$msg->Body = $body;
				$msg->HtmlBody = $body;
			}
			$msg->Attachments = (is_array($doc) && count($doc)) ? 6 : 0;
			//***********************************************
			if($GLOBALS['Systeme']->isModule('Murphy')) MailMurphy::checkMurphy($msg);
			//***********************************************
			$msg->Save(true);

			if($struc->type == 1) {
				$upd = false;
				foreach($doc as $d) {
					$dir = 'Home/Mail/'.date('Ymd', $msg->Date).'/'.$msg->Id;
					$doc = $dir.'/'.$d[doc];
					fileDriver::mk_dir($dir);
					file_put_contents($doc, $d[data]);
					$att = genericClass::createInstance('Mail', 'Attachment');
					$att->Name = $d[doc];
					$att->Doc = $doc;
					$att->addParent($msg); 
					$att->Save();
					$cid = $d[cid];
					if($cid) {
						$msg->HtmlBody = str_replace('cid:'.$cid, $doc, $msg->HtmlBody);
						$upd = true;
					}
				}
				if($upd) $msg->Save(true);
			}
		}		
//		imap_close($mbox);
//		$sts = array(array('edit', 1, '', 'Mail', 'MailUser', '', '', null, null),
//					array('edit', 1, '', 'Mail', 'Message', '', '', null, null));
//		return WebService::WSStatusMulti($sts);
	}

	private function charset($par, $text) {
		foreach($par as $p) {
			if(strtoupper($p->attribute) == 'CHARSET') {
				$text = iconv($p->value, 'UTF-8', $text);
				break;
			}
		}
		return $text;
	}

	private function findPart($mbox, $uid, $parts, $doc, &$txt, &$html, $idx='') {
		$count = count($parts);
		for($i = 0; $i < $count; $i++) {
			$part = $parts[$i];
			if($part->type == 1) {
				if($idx) $n = $idx.'.'.($i + 1);
				else $n = $i + 1;
				$t = $this->findPart($mbox, $uid, $part->parts, $doc, $txt, $html, $n);
				$ix = $t[0];
				$doc = $t[1];
			} 
			else {
				if($part->type == 0 && ($part->subtype == 'PLAIN' || $part->subtype == 'HTML')) {
					if($idx) $ix = $idx.'.'.($i + 1);
					else $ix = $i + 1;
//					if($part->subtype == 'PLAIN') $txt = $ix;
					$t = imap_fetchbody($mbox, $uid, $ix, FT_UID);
					if($part->encoding == 3) $t = base64_decode($t);
					else if($part->encoding == 4) $t = quoted_printable_decode($t);
					$t = $this->charset($part->parameters, $t);
					if($part->subtype == 'PLAIN') $txt = $t;
					else $html = $t;
				}
				elseif($part->ifdparameters == 1) {
					$par = $part->dparameters[0];
					if(strtoupper($par->attribute) == 'FILENAME') {
						if($idx) $n = $idx.'.'.($i + 1);
						else $n = $i + 1;
						$data = imap_fetchbody($mbox, $uid, $n, FT_UID);
						if($part->encoding == 3) $data = base64_decode($data);
//						$dir = 'Data/Mail/'.date('Ymd').'/'.$uid;
//						fileDriver::mk_dir($dir);
//						file_put_contents($dir.'/'.$par->value, $data);
						$cid = $part->id;
						$cid = substr($cid, 1, strlen($cid) - 2);
						$file = iconv_mime_decode($par->value,0,"UTF-8");
						if($part->type == 5) {
							$ar = explode('.', $file);
							$c = count($ar);
							$ext = strtolower($ar[$c-1]);
							$sub = strtolower($part->subtype);
							if($ext == $sub || ($sub == 'jpeg' && $ext == 'jpg') || ($sub == 'jpg' && $ext == 'jpeg')) {
								unset($ar[$c-1]);
								$file = implode('.', $ar);
							}
							$file .= '.'.$sub;
						}
						$file = str_replace(' ', '_', $file);
						$doc[] = array('cid'=>$cid, 'doc'=>$file, 'data'=>$data);
					}
				}
			}
		}
		return array($ix,$doc);
	}

	public function GetMailAddresses($flt) {
		$add = array();
		$rec = Sys::$Modules['Systeme']->callData("User/Mail~%$flt+Nom~%$flt",false,0,30,'','','Mail,Nom');
		if(is_array($rec) && count($rec)) {
			foreach($rec as $r) $add[] = array('Email'=>$r['Mail'], 'Name'=>$r['Nom']);
		}
		//***********************************************
		if($GLOBALS['Systeme']->isModule('Murphy')) MailMurphy::getMailAddresses($add, $flt);
		//***********************************************
		return WebService::WSData('',0,'','','','','','','',$add);
	}

}

