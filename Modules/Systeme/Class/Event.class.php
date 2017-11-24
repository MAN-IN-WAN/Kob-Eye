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


	function pollEvents($module,$object=null,$lastAlert=0,$interval = 1000,$maxDuration=15){
        Connection::CloseSession();
        if($lastAlert == '' || $lastAlert == null || $lastAlert == 'NULL')
            $lastAlert=time();
        if($interval == '' || $interval == null || $interval == 'NULL' || $interval == 0)
            $interval=1000;
        if($maxDuration == '' || $maxDuration == null || $maxDuration == 'NULL' || $interval == 0)
            $maxDuration =15;

        $GLOBALS['Systeme']->Db[0]->query("COMMIT");
        $GLOBALS['Systeme']->Db[0]->query("SET AUTOCOMMIT=1");
	    $i = 0;
	    $nbIt = ceil($maxDuration*1000 /$interval);
	    $delay = $interval*1000;

        $query = 'Event/tmsCreate>'.$lastAlert.'&EventModule='.$module;
        if($object != '' && $object != null && $object != 'NULL')
            $query.='&EventObjectClass='.$object;

	    while($i<$nbIt){
            $rec = Sys::$Modules['Systeme']->callData($query, false, 0, 30);
            Sys::$Modules['Systeme']->Db->clearLiteCache();
            if(is_array($rec) && count($rec)) {
                return $rec;
            }
            $i++;
            usleep($delay);
	    }
    }

    function pollAll($lastAlert=0,$interval = 1000,$maxDuration=15){
        Connection::CloseSession();
        if($lastAlert == '' || $lastAlert == null || $lastAlert == 'NULL')
            $lastAlert=time();
        if($interval == '' || $interval == null || $interval == 'NULL' || $interval == 0)
            $interval=1000;
        if($maxDuration == '' || $maxDuration == null || $maxDuration == 'NULL' || $interval == 0)
            $maxDuration =15;

        $GLOBALS['Systeme']->Db[0]->query("COMMIT");
        $GLOBALS['Systeme']->Db[0]->query("SET AUTOCOMMIT=1");

        $i = 0;
        $nbIt = ceil($maxDuration*1000 /$interval);
        $delay = $interval*1000;

        $queryEv = 'Event/tmsCreate>'.$lastAlert;
        $queryAu = 'AlertUser::AlertUserList/tmsCreate>'.$lastAlert;

        $res=array('Ev' => Array(),'Au' => Array());
        $ret =false;

        while($i<$nbIt){
            $recEv = Sys::$Modules['Systeme']->callData($queryEv, false, 0, 30);
            $recAu = Sys::$Modules['Systeme']->callData($queryAu, false, 0, 30);
            Sys::$Modules['Systeme']->Db->clearLiteCache();
            if(is_array($recEv) && count($recEv)) {
                $res['Ev']=$recEv;
                $ret =true;
            }
            if(is_array($recAu) && count($recAu)) {
                $res['Au']=$recAu;
                $ret =true;
            }

            if($ret) {
                foreach ($res['Ev'] as $k=>$ev){
                    $o = unserialize($res['Ev'][$k]['Data']);
                    $res['Ev'][$k]['Data'] = json_encode($o->getWebServiceData());
                }
                return $res;
            }

            $i++;
            usleep($delay);
        }
    }

}