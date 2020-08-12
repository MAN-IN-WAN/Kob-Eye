<?php

class Message extends genericClass {
	
	public static function Messages($args) {
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		if(!$logged) return ['success'=>false, 'logged'=>false, 'msgs'=>[]];
		
		$sql = "select distinct p.Id,p.Title,m.FromId,u.Initiales,count(*) as cnt,max(m1.MessageDate) as dt "
			."from `kob-Show-Message` m "
			."inner join `kob-Show-Message` m1 on m1.PerformanceId=m.PerformanceId "
			."and ((m1.FromId=m.FromId and m1.ToId=$usr->Id) or (m1.FromId=$usr->Id and m1.ToId=m.FromId)) "
			."inner join `kob-Show-Performance` p on p.Id=m.PerformanceId "
			."inner join `kob-Systeme-User`u on u.Id=p.userCreate "
			."where m.ToId=$usr->Id "
			."group by p.Id order by dt desc";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		
		$msgs = [];
		foreach($rs as $r) {
			$d = new stdClass();
			$d->id = $r['Id'];
			$d->title = $r['Title'];
			$d->uid = $r['FromId'];
			$d->user = $r['Initiales'];
			$d->count = $r['cnt'];
			$d->time = $r['dt'];
			$msgs[] = $d;
		}
		return ['success'=>true, 'logged'=>true, 'msgs'=>$msgs, 'sql'=>$sql];
	}
	
	public static function Dialog($args) {
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		if(!$logged) return ['success'=>false, 'logged'=>false];

		$p = $args['show'];
		$u = $args['user'];
		$id = $usr->Id;
		$sql = "select Id,Message,FromId,MessageDate "
			."from `kob-Show-Message` "
			."where PerformanceId=$p and ((FromId=$u and ToId=$id) or (FromId=$id and ToId=$u)) "
			."order by MessageDate";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		
		$msgs = [];
		foreach($rs as $r) {
			$d = new stdClass();
			$d->id = $r['Id'];
			$d->msg = $r['Message'];
			$d->from = $r['FromId'];
			$d->time = $r['MessageDate'];
			$msgs[] = $d;
		}
		
		$sql1 = "update `kob-Show-Message` set status=1 where PerformanceId=$p and ((FromId=$u and ToId=$id) or (FromId=$id and ToId=$u)) and status=0";
		$sql1 = str_replace('##_', MAIN_DB_PREFIX, $sql1);
		$rs = $GLOBALS['Systeme']->Db[0]->exec($sql1);
		
		return ['success'=>true, 'logged'=>true, 'msgs'=>$msgs, 'sql'=>$sql];
	}
	
	public static function AddMsg($args) {
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		if(!$logged) return ['success'=>false, 'logged'=>false];

		$msg = $args['msg'];
		$f = Sys::getOneData('Systeme', 'User/'.$msg->from);
		$t = Sys::getOneData('Systeme', 'User/'.$msg->to);
		$p = Sys::getOneData('Show', 'Performance/'.$msg->show);
		$m = genericClass::createInstance('Show', 'Message');
		$m->Message = $msg->msg;
		$m->MessageDate = $msg->time;
		$m->Status = 0;
		$m->addParent($p);
		$m->addParent($f, 'FromId');
		$m->addParent($t, 'ToId');
		$m->Save();
		return ['success'=>true, 'logged'=>true, 'msgId'=>$m->Id];
	}
}
