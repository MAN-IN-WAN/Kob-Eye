<?php
class MailUser extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}

	function Save($mode=false) {
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxx :", $this);
		if($mode) {
			$sts = 1;
			if($this->Read) $sts = 2;
			if($this->Sent) $sts = 3;
			if($this->Forwarded) $sts = 4;
			if($this->Answered) $sts = 5;
			$this->Status = $sts;
			$this->Date = time();
			genericClass::Save();
		}
	}


	function saveMessage($args) {		
		$id = $args->MessageId;
		$msg = genericClass::createInstance('Mail', 'Message');
		$msg->MessageParents = array();
		if($id) $msg->initFromId($id);
		$msg->FromAddress = $args->FromAddress;
		$msg->ToAddress = $args->ToAddress;
		$msg->CcAddress = $args->CcAddress;
		$msg->CciAddress = $args->CciAddress;
		$msg->Subject = $args->Subject;
		$msg->Body = $args->Body;
		$msg->attachment = $args->attachment;
		//*************************************
		if($GLOBALS['Systeme']->isModule('Murphy')) MailMurphy::mailUserParents($args, $msg);
		//*************************************
		$msg->saveMessage();

		$sts = array(array($id ? 'edit' : 'add', 1, $msg->Id, 'Mail', 'Message', '', '', null, null),
					array($id ? 'edit' : 'add', 1, '', 'Mail', 'MailUser', '', '', null, null));
		foreach($msg->MessageStatus as $st) {
			$st[2] = $msg->Id;
			$sts[] = $st;
		}
		return WebService::WSStatusMulti($sts);
	}


	function Delete($mode=false) {
		if($mode) {
			return parent::Delete();
		}
		$sts = array();
		//$fld = $this->getParents('Folder');
		//$fld = $fld[0];
		//if($fld->Name == 'Trash') {
		if($this->Folder == 4) {
			$msg = $this->getParents('Message');
			$msg = $msg[0];
			$sts[] = genericClass::Delete();
			$mus = $msg->getChilds('MailUser');
			if(! count($mus)) $msg->Delete();
		}
		else {
			//$this->resetParents('Folder');
			//$rec = Sys::$Modules['Mail']->callData('Folder/Name=Trash', false, 0, 1);
			//$fld = genericClass::createInstance('Mail', $rec[0]);
			//$this->addParent($fld);
			$this->Folder = 4;
			$sts[] = genericClass::Save();
		}
		return 1;
	}

 

	function GetMessage($idCreator, $mode, $idList, $object='') {
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxx MU:$idCreator:$mode:$this->Id:$idList:$object");
		if($mode) {
			$ms = genericClass::createInstance('Mail', 'Message');
			if($idList) {
				if($object == '') {
					$mu = genericClass::createInstance('Mail', 'MailUser');
					$mu->initFromId($idList);
					$par = $mu->getParents('Message');
					$idCreator = $par[0]->Id;
				}
				else $idCreator = $idList;
			}
		}
		else {
			if(! $this->Id) $ms = genericClass::createInstance('Mail', 'Message');
			else {
				$ms = $this->getParents('Message');
				$ms = $ms[0];
			}
		}
		return $ms->GetMessage($idCreator, $mode, 0, $object);
	}
	



	function GetMessageList($id, $offset, $limit, $sort, $order, $filter, $folder) {
		Connection::CloseSession();
		$uid = Sys::$User->Id;
		$sql = "select u.Id as MailUserId,s.Id as StatusId,s.Icon,m.Date,m.From,m.To,m.Subject,m.Id as MessageId
				from `##_Mail-MailUser` u
				left join `##_Mail-Message` m on m.Id=u.MailUserMessageId
				left join `##_Mail-Status` s on s.Id=u.Status
				where ";
		if($id) $sql .= "u.Id=$id";
		else {
			//$sql .= "u.MailUserUserId=$uid and u.MailUserFolderId=$folder order by ";
			$sql .= "u.MailUserUserId=$uid and u.Folder=$folder order by ";
			$sql .= $sort ? $sort.' '.$order : "m.Date desc";
		}
		if($offset || $limit) {
			$sql .= ' limit '.$offset.', '.($limit ? $limit : 1);
		}
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxx:".$sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		//$rec = Sys::$Modules['Mail']->callData("MailUser/User.MailUserUserId($uid)&Folder.MailUserFolderId($id)");
		
		$to = $folder == 2 || $folder == 3;
		$items = array();
		foreach($rec as $rc) {
			$items[] = array('Id'=>$rc['MailUserId'],'StatusId'=>$rc['StatusId'],'StatusIcon'=>$rc['Icon'],
						'Date'=>$rc['Date'],'From'=>$rc['From'],'To'=>$rc[$to ? 'To' : 'From'],'Subject'=>$rc['Subject'],
						'MessageId'=>$rc['MessageId'],'module'=>'Mail','objectClass'=>'MailUser');
		}
		$c = count($items);
		return WebService::WSData('',0,$c,$c,'','','','','',$items);
	}


	function getAlerts($lastAlert, $time) {
		$uid = Sys::$User->Id;
		//$sql = "select count(*) as cnt from `##_Mail-MailUser` u
		//		left join `##_Mail-Folder` f on f.Id=u.MailUserFolderId
		//		where u.MailUserUserId=$uid and f.Name='Inbox'";
		$sql = "select count(*) as cnt from `##_Mail-MailUser` where Folder=1 and MailUserUserId=$uid";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql." and Date>=$lastAlert");
		if(! $pdo) return null;
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		$cnt = $rec[0]['cnt'];
		if($cnt) {
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql." and `Read`=0");
			$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
			$cnt = $rec[0]['cnt'];
			return array(array('type'=>'alert_mail', 'subtype'=>'Unread', 'alertCount'=>$cnt));
		}
		return null;
	}
	
}
