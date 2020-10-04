<?php

class Message extends genericClass {
	
	// cron function
	// read all messages older than 15' and send a mail to each users
	// then flag the messages. 
	public static function SendMails() {
		// notifications
		$sql = "select Id from `##_Show-Message` where !(Status&5) ";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$ids = '';
		foreach($rs as $r) $ids .= ($ids ? ',' : '').$r['Id'];

		if($ids) {
			$sql = "select distinct u.Informations "
				."from `##_Show-Message` m "
				."inner join `##_Systeme-User` u on u.Id=m.ToId "
				."where m.Id in ($ids) and u.Informations<>''";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
			foreach($rs as $r) {
				$inf = json_decode($r['Informations']);
				if($inf->fcmToken) {
					switch($inf->language) {
						case 'ES': $t = 'Tienes mensajes'; break;
						case 'FR': $t = 'Vous avez des messages'; break;
						default: $t = 'You have messages';
					}
					Show::SendFCM($inf->fcmToken, $t, '');
				}
			}

			$sql1 = "update `##_Show-Message` set Status=(Status|4) where Id in ($ids)";
			$sql1 = str_replace('##_', MAIN_DB_PREFIX, $sql1);
			$rs = $GLOBALS['Systeme']->Db[0]->exec($sql1);
		}
		
		// mails
		$dt = time() - 60*15;

		$sql = "select Id from `##_Show-Message` where !(Status&3) and MessageDate<$dt ";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$ids = '';
		foreach($rs as $r) $ids .= ($ids ? ',' : '').$r['Id'];

		if($ids) {
			$sql = "select distinct m.ToId,u.Prenom,u.Mail,u.Informations "
				."from `##_Show-Message` m "
				."inner join `##_Systeme-User` u on u.Id=m.ToId "
				."where m.Id in ($ids) ";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$rs = $GLOBALS['Systeme']->Db[0]->query($sql);

			foreach($rs as $r) {
				$inf = $r['Informations'];
				if($inf) $inf = json_decode($inf);
				$lang = isset($inf->language) && $inf->language ? $inf->language : 'EN';
				switch($lang) {
					case 'EN': 
						$s = 'You have some messages';
						$b = 'Hello '.$r['Prenom'].",\n\n$s on https://shows.zone\n";
						break;
					case 'ES': 
						$s = 'Tienes algunos mensajes'; 
						$b = 'Hola '.$r['Prenom'].",\n\n$s en https://shows.zone\n";
						break;
					case 'FR': 
						$s = 'Vous avez des messages';
						$b = 'Bonjour '.$r['Prenom'].",\n\n$s sur https://shows.zone\n";
						break;
				}
				$params = ['Subject'=>"shows.zone: $s", 'To'=>array($r['Mail']), 'Boddy'=>$b];
				Show::SendMessage($params);
			}

			$sql1 = "update `##_Show-Message` set Status=(Status|2) where Id in ($ids)";
			$sql1 = str_replace('##_', MAIN_DB_PREFIX, $sql1);
			$rs = $GLOBALS['Systeme']->Db[0]->exec($sql1);
		}
		
		return true;
	}
		
	// get all discussions
	public static function Messages($args) {
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		if(!$logged) return ['success'=>false, 'logged'=>false, 'msgs'=>[]];
		$id = $usr->Id;
		$sql = "select distinct p.Id,p.userCreate,p.Title,if(m.FromId=$id,m.ToId,m.FromId) as tfid,u.Prenom,u.Nom,u.Informations,count(*) as cnt,max(m1.MessageDate) as dt, min(m1.Status) as st "
			."from `##_Show-Message` m "
			."inner join `##_Show-Message` m1 on m1.PerformanceId=m.PerformanceId "
			//."and ((m1.FromId=m.FromId and m1.ToId=$usr->Id) or (m1.FromId=$usr->Id and m1.ToId=m.FromId)) "
			."inner join `##_Show-Performance` p on p.Id=m.PerformanceId "
			."inner join `##_Systeme-User` u on u.Id=if(m.FromId=$id,m.ToId,m.FromId) "
			."where m.ToId=$id or m.FromId=$id "
			."group by p.Id,tfid order by dt desc";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		
		$id = $usr->Id;
		$msgs = [];
		foreach($rs as $r) {
			$inf = json_decode($r['Informations']);
			
			$d = new stdClass();
			$d->id = $r['Id'];
			$d->title = $r['Title'];
			//$d->uid = $r['tfid'];
			$d->user = ['id'=>$r['tfid'], 'nickname'=>$r['Prenom']]; //$inf && $inf->displayName && $r['Nom'] ? $r['Nom'] : $r['Prenom'];
			$d->count = $r['cnt'];
			$d->time = $r['dt'];
			$d->status = $r['st'];
			$d->mine = $r['userCreate'] == $id;
			$msgs[] = $d;
		}
		return ['success'=>true, 'logged'=>true, 'msgs'=>$msgs, 'sql'=>$sql];
	}
	
	// get one dialog
	public static function Dialog($args) {
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		if(!$logged) return ['success'=>false, 'logged'=>false];

		$p = $args['show'];
		$u = $args['user'];
		$id = $usr->Id;
		$sql = "select Id,Message,FromId,MessageDate,Status "
			."from `##_Show-Message` "
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
			$d->status = $r['Status'];
			$msgs[] = $d;
		}
		
		$sql1 = "update `##_Show-Message` set Status=(Status|1) where PerformanceId=$p and ((FromId=$u and ToId=$id) or (FromId=$id and ToId=$u)) and !(Status&1)";
		$sql1 = str_replace('##_', MAIN_DB_PREFIX, $sql1);
		$rs = $GLOBALS['Systeme']->Db[0]->exec($sql1);
		
		return ['success'=>true, 'logged'=>true, 'msgs'=>$msgs, 'sql'=>$sql, 'sql1'=>$sql1];
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
		$m->MessageDate = floor($msg->time);
		$m->Status = 0;
		$m->addParent($p);
		$m->addParent($f, 'FromId');
		$m->addParent($t, 'ToId');
		$m->Save();
		
		return ['success'=>true, 'logged'=>true, 'msgId'=>$m->Id];
	}
	
	
	public static function DelDialog($args) {
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		if(!$logged) return ['success'=>false, 'logged'=>false];

		$p = $args['perfId'];
		$u = $args['user'];
		$id = $usr->Id;
		$sql1 = "delete from `##_Show-Message` set status=1 where PerformanceId=$p and ((FromId=$u and ToId=$id) or (FromId=$id and ToId=$u))";
		$sql1 = str_replace('##_', MAIN_DB_PREFIX, $sql1);
		$rs = $GLOBALS['Systeme']->Db[0]->exec($sql1);
		
		return self::Messages(null);
	}
	
	
}
