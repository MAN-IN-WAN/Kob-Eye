<?php
class AlertUser extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}


	function Delete() {
		$id = $this->AlertId;
		$st = parent::Delete();
		Sys::$User->Admin=1;
		$rec = Sys::$Modules['Systeme']->callData('AlertUser/AlertId='.$id);
		if(! is_array($rec || ! count($rec))) {
			$al = genericClass::createInstance('Systeme', 'Alert');
			$al->initFromId($id);
			$al->Delete();
		}
		return $st;
	}	
	
	// call from web service
	function getAlerts($lastAlert, $time) {
		$uid = Sys::$User->Id;
		$rec = Sys::$Modules['Systeme']->callData('AlertTime/uid='.$uid.'&Time>'.$lastAlert);
		if(! is_array($rec) || ! count($rec)) return null;
//klog::l(">>>>>>>$uid : ".$rec[0]['uid']);
		$rec = Sys::$Modules['Systeme']->callData('AlertUser/Read=0', false, 0, 1, '', '', 'COUNT(*)');
		$cnt = $rec[0]['COUNT(*)'];
		if($cnt) {
			$rec = Sys::$Modules['Systeme']->callData('AlertUser/Read=0&Time>'.$lastAlert, false, 0, 5);
			if(is_array($rec) && count($rec)) {
				$als = array();
				foreach($rec as $r) $als[] = array($r['Title'], $r['Icon']);
			}
		}
		return array(array('type'=>'alert_alert', 'subtype'=>'Unread', 'alertCount'=>$cnt, 'alertArray'=>$als));;
	}
	
	static public function addAlert($title,$tag,$module,$object,$id,$users,$role,$icon=null) {
		$a = genericClass::createInstance('Systeme', 'Alert');
		$a->Title = $title;
		$a->Date = time();
		$a->Tag = $tag;
		$a->AlertModule = $module;
		$a->AlertObject = $object;
		$a->ObjectId = $id;
		$a->Icon = $icon;
		$a->Author = Sys::$User->Nom;
		$a->UserId = Sys::$User->Id;
		$a->Save();
		$time = microtime(true);
		if($users) foreach($users as $usr) AlertUser::addAlertUser($a->Id, $usr, $time);
		if($role && !empty($role)) {
			$grps = Group::getGroupFromRole($role);
			foreach($grps as $grp) {
				$usrs = $grp->getChilds('User');
				foreach($usrs as $usr) AlertUser::addAlertUser($a->Id, $usr->Id, $time);
			}
		}
	}
	
	public function setAlert($title,$tag,$module,$obect,$id,$users,$role,$icon=null) {
		AlertUser::addAlert($title,$tag,$module,$obect,$id,$users,$role,$icon);
	}
	
	private static function addAlertUser($aid, $uid, $time) {
		if(! SELF_NOTIFICATION && Sys::$User->Id == $uid) return;
		$u = genericClass::createInstance('Systeme', 'AlertUser');
		$u->Time = $time;
		$u->AlertId = $aid;
		$u->uid = $uid;
		$u->gid = 0;
		$u->umod = 7;
		$u->gmod = 1;
		$u->omod = 1;
		$u->Save();
		$rec = Sys::$Modules['Systeme']->callData('AlertTime/uid='.$uid);
		if(is_array($rec) && count($rec)) {
			$t = genericClass::createInstance('Systeme', $rec[0]);
		}
		else {
			$t = genericClass::createInstance('Systeme', 'AlertTime');
		}
		$t->Time = $time;
		$t->uid = $uid;
		$t->gid = 0;
//		$t->umod = 7;
//		$t->gmod = 1;
//		$t->omod = 1;
		$t->Save();
	}

	public function MarkAsRead() {
		if($this->Read) return WebService::WSStatus('method', 1, '', '', '', '', '', null, null);
		$this->Read = 1;
		$this->Time = microtime(true);
		$this->Save();
		return WebService::WSStatus('edit', 1, $this->Id, 'Systeme', 'AlertUser', '', '', null, null);
	}

	public function MarkAsDone() {
		$id = $this->AlertId;
		$time = microtime(true);
		Sys::$User->Admin=1;
		$rec = Sys::$Modules['Systeme']->callData('AlertUser/AlertId='.$id);
		foreach($rec as $rc) {
			$au = genericClass::createInstance('Systeme', $rc);
			$rec = Sys::$Modules['Systeme']->callData('AlertTime/uid='.$au->uid, false, 0, 1);
			if(is_array($rec) && count($rec)) {
				$t = genericClass::createInstance('Systeme', $rec[0]);
				$t->Time = $time;
				$t->Save();
			}
			$au->Delete();
		}
		$al = genericClass::createInstance('Systeme', 'Alert');
		$al->initFromId($id);
		$al->Delete();
		return WebService::WSStatus('delete', 1, $this->Id, 'Systeme', 'AlertUser', '', '', null, null);
	}
	
	public function MarkAllDone() {
		$rec = Sys::$Modules['Systeme']->callData('AlertUser'); //,false,0,0,'','','Id');
		foreach($rec as $rc) {
			genericClass::createInstance('Systeme', $rc)->MarkAsDone();
		}
		return WebService::WSStatus('method', 1, 0, 'Systeme', 'AlertUser', '', '', null, null);
	}
	
	public function MarkAllRead() {
		$rec = Sys::$Modules['Systeme']->callData('AlertUser'); //,false,0,0,'','','Id');
		foreach($rec as $rc) {
			genericClass::createInstance('Systeme', $rc)->MarkAsRead();
		}
		return WebService::WSStatus('delete', 1, 0, 'Systeme', 'AlertUser', '', '', null, null);
	}

	private function checkIfDone() {
		Sys::$User->Admin = 1;
		$rec = Sys::$Modules['Systeme']->callData('Alter/'.$this->AlertId, false, 0, 1);
		if(count($rec)) return null;
		$rec = Sys::$Modules['Systeme']->callData('AlterUser/AlertId='.$this->Id, false, 0, 999);
		return WebService::WSStatus('delete', 1, $this->Id, 'Systeme', 'AlertUser', '', '', null, null);
	}
	
	/*
	 * create alerts on cron
	 */
	function createAllAlerts() {
		Connection::CloseSession();
		//if(! MULTITHREAD) return;
		$time = microtime(true);
		foreach(Sys::$Modules as $mod) {
			foreach ($mod->Db->ObjectClass as $obj) {
				if($mod->Nom == $obj->Module) {
					$cls = genericClass::createInstance($obj->Module, $obj->titre);
					$cls->createAlerts($time);
				}
			}
		}
	}

	function createHourlyAlerts() {
		Connection::CloseSession();
		//if(! MULTITHREAD) return;
		$time = microtime(true);
		foreach(Sys::$Modules as $mod) {
			foreach ($mod->Db->ObjectClass as $obj) {
				if($mod->Nom == $obj->Module) {
					$cls = genericClass::createInstance($obj->Module, $obj->titre);
					$cls->createAlerts($time);
				}
			}
		}
	}

}