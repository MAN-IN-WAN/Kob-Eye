<?php

class AlertTask extends genericClass {

	function Save() {
		$this->addParent(Sys::$User);
		$this->RemindDate = $this->StartDate - $this->Reminder;
		$this->Alert = $this->RemindDate > microtime(true) ? 0 : 1;
		parent::Save();
	}
	
	static public function addTask($title,$detail,$startdate,$enddate,$module,$object,$id,$user,$role,$reminder=86400,$type=1,$private=0) {
		$a = genericClass::createInstance('Systeme', 'AlertTask');
		$a->Title = $title;
		$a->Detail = $detail;
		$a->StartDate = $startdate;
		$a->EndDate = $enddate;
		$a->TaskModule = $module;
		$a->TaskObject = $object;
		$a->ObjectId = $id;
		$a->Reminder = $reminder;
		$a->TypeId = $type;
		$a->Private = $private;
		if($user)$a->addParent('Systeme/User/'.$user);
		if($role)$a->addParent('Systeme/Role/'.$role);
		$a->Save();
	}
	public function GetUserTask($id, $offset, $limit, $sort, $order, $filter, $start, $end) {
		$url = '';
		if($start) $url .= "EndDate<$start";
		if($end) {
			if($url) $url .= '&';
			$url .= "StartDate>$end";
		}
		$url = 'AlertTask' . ($url ? "/$url" : '');
		$usr = Sys::$User;
		$ut = $usr->getChildren($url);
		$rol = $usr->getRole();
		if(is_array($rol)) {
			foreach($rol as $r) {
				$rt = $r->getChildren($url);
				if(is_array($rt)) $ut = array_merge($ut, $rt);
			}
		}
		$t = array();
		foreach($ut as $u)
			$t[$u->Id] = $u;
		$c = count($t);
		return WebService::WSData('',0,$c,$c,'','','','','',$t);
	}

	public function GetObjectTask($id, $offset, $limit, $sort, $order, $filter, $start, $end, $module, $object, $objectId) {
		$url = '';
		if($start) $url .= "EndDate<$start";
		if($end) {
			if($url) $url .= '&';
			$url .= "StartDate>$end";
		}
		if($module) {
			if($url) $url .= '&';
			$url .= "TaskModule=$module&TaskObject=$object";
			if($objectId) $url .= "&ObjectId=$objectId";
		}
		$url = 'AlertTask' . ($url ? "/$url" : '');
		$ts = Sys::getData('Systeme', $url, $offset, $limit, $sort, $order);
		$c = count($ts);
		foreach($ts as $t) unset($t->Triggers);
//klog::l(">>>>>>>>><<<<<<<<<<<<<<".print_r($ts[0],1));
		return WebService::WSData('',0,$c,$c,'','','','','',$ts);
	}
	
	public function createAlerts($time) {
		$tsks = Sys::getData('Systeme', "AlertTask/Alert=0&RemindDate<$time");
		foreach($tsks as $tsk) {
			$usr = null;
			$rol = null;
			$usrs = $tsk->getParents('User');
			if(count($usrs)) $usr = $usrs[0];
			$rols = $tsk->getParents('Role');
			if(count($rols)) $rol = $rols[0];
			$tsk->Alert = 1;
			$tsk->Save();
			$tag = $tsk->TaskModule.'/'.$tsk->TaskObject.'/'.$tsk->ObjectId;
			AlertUser::addAlert($tsk->Title,$tag,$tsk->TaskModule,$tsk->TaskObject,$tsk->ObjectId,$usr,$rol,null);
		}
	}
	
	public function StartDate($start, $end) {
		if($end == null || $end < $start) $end = strtotime("+1 hour", $start);
		$data = array('EndDate'=>$end);
		$res = array('dataValues'=>$data);
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}

	public function EndDate($start, $end) {
		if($end < $start) $end = strtotime("+1 day", $end);
		$data = array('EndDate'=>$end);
		$res = array('dataValues'=>$data);
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}

	public function GetTaskCalendar($id, $first, $last) {
		Connection::CloseSession();
		if($id) $flt = 'Id='.$id.'&';
		$flt .= 'StartDate>='.$first.'&StartDate<='.$last;
		$uid = Sys::$User->Id;
		$flt .= "&(!Private=0+UserId=$uid!)";
		$tsk = Sys::getData('Systeme', 'AlertTask/'.$flt, 0, 999);
		$items = array();
		foreach($tsk as $t) {
			$evt = 'Title : '.$t->Title."\n";
			$evt .= 'Start : '.date('d/m/Y', $t->StartDate)."\nEnd : ".date('d/m/Y', $t->EndDate)."\n";
			$evt .= $t->Detail."\n";
			if($t->ObjectId) $tag = $t->TaskModule.'/'.$t->TaskObject.'/'.$t->ObjectId;
			else $tag = '';
			$items[] = array('Id'=>$t->Id,'label'=>$t->Title,'date'=>$t->StartDate,'end'=>$t->EndDate,'event'=>$evt,'color'=>$t->Color,'Tag'=>$tag,'TypeId'=>$t->TypeId);
		}
		$c = count($items);
		return WebService::WSData('',0,$c,$c,'','','','','',$items);
	}

}