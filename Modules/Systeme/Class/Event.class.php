<?php
class Event extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function getAlerts($lastAlert, $time) {
		if($lastAlert) {
			$rec = Sys::$Modules['Systeme']->callData('Event/tmsCreate>'.$lastAlert.'&tmsCreate<='.$time, false, 0, 30, '', '', 'EventType,EventModule,EventObjectClass,EventId,tmsCreate,uid','EventType,EventModule,EventObjectClass,EventId');
			Sys::$Modules['Systeme']->Db->clearLiteCache();
//klog::l(">>>>>>>>>>>>>>>",$rec);
			if(is_array($rec) && count($rec)) {
				$alrt = array();
				foreach($rec as $rc) {
					$alrt[] = array('type'=>'Event', 'subtype'=>$rc['EventType'], 'module'=>$rc['EventModule'], 'objectClass'=>$rc['EventObjectClass'], 'Id'=>$rc['EventId'], 'time'=>$rc['tmsCreate'], 'uid'=>$rc['uid']);
				}
				return $alrt;
			}
		}
		return null;
	}
	
	function addEvent($title,$data,$event,$module,$object,$id,$uid=0) {
		$e = genericClass::createInstance('Systeme', 'Event');
		$e->Title = $title;
		$e->Data = $data;
		$e->EventType = $event;
		$e->EventModule = $module;
		$e->EventObjectClass = $object;
		$e->EventId = $id;
		$e->UserId = $uid;
		$e->Save(); 
	}
}