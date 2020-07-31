<?php

class Message extends genericClass {
	
	public static function Messages($args) {
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		if(!$logged) return ['success'=>false, 'logged'=>false, 'msgs'=>[]];
		
		$sql = "select p.Id,p.Title,m.FromId,u.Initiales,count(*) as cnt,max(m.MessageDate) as dt "
			."from `kob-Show-Message` m "
			."inner join `kob-Show-Performance` p on p.Id=m.PerformanceId "
			."inner join `kob-Systeme-User`u on u.Id=p.userCreate "
			."where m.ToId=$usr->Id "
			."group by p.Id order by dt";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		
		$msgs = [];
		foreach($rs as $r) {
			$dt = $r['dt'];
			$gap = time() - $dt;
			if($gap >= 15768000) $tim = date('M Y', $dt);
			elseif($gap >= 518400) $tim = date('d M', $dt);
			else $tim = date('D H:i', $dt);
			
			$d = new stdClass();
			$d->id = $r['Id'];
			$d->title = $r['Title'];
			$d->uid = $r['FromId'];
			$d->user = $r['Initiales'];
			$d->count = $r['cnt'];
			$d->time = $tim;
			$msgs[] = $d;
		}
		return ['success'=>true, 'logged'=>true, 'msgs'=>$msgs];
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
		
		$sql = "update `kob-Show-Message` set status=1 where PerformanceId=$p and ((FromId=$u and ToId=$id) or (FromId=$id and ToId=$u)) and status=0";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->exec($sql);
		
		return ['success'=>true, 'logged'=>true, 'msgs'=>$msgs];
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
		$m->MessageDate = $msg->time / 1000;
		$m->Status = 0;
		$m->addParent($p);
		$m->addParent($f, 'FromId');
		$m->addParent($t, 'ToId');
		$m->Save();
		return ['success'=>true, 'logged'=>true, 'msgId'=>$m->Id];
	}
}