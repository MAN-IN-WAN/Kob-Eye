<?php

class Show extends Module {
	
//	public static function CheckLogged() {
//		$usr = Sys::$User;
//		return($usr->Public);
//	}
	
	public static function GetShow($args) {
		$mode = $args['mode'];



klog::l("GETSHOW >>>>>",$args);
		switch($mode) {
			
			case 'change-pwd': return self::changePwd($args);			
			case 'lost-pwd': return self::lostPwd($args);			
			case 'add-role': return Performance::AddRole($args);
			case 'del-role': return Performance::DelRole($args);
			case 'status': return self::Status();
			case 'messages': return Message::Messages($args);
			case 'dialog': return Message::Dialog($args);
			case 'msg': return Message::AddMsg($args);
			case 'image': return Performance::LoadImage($args);
			case 'del-pict': return Performance::DelPict($args);
			case 'add-link': return Performance::AddLink($args);
			case 'del-link': return Performance::DelLink($args);
			case 'save-perf': return Performance::SavePerf($args);
			case 'del-perf': return Performance::DeletePerf($args);
			case 'login': return self::logUser();
			case 'confirm': return self::registerConfirm($args);
			case 'register': return self::registerUser($args);
			case 'logout': return self::logout($args);
			case 'init': return self::initShow($args);			
			case 'param': return self::param($args);
			case 'perf': return Performance::GetPerf($args);
			case 'favourite': return Performance::SetFavourite($args);
		}
		return array('error'=>'mode unknown');
	}
	
	private static function logout($args) {
		$GLOBALS['Systeme']->Connection->Disconnect();
		return array('success'=>true, 'logged'=>false, 'token'=>'', 'pseudo'=>'');
	}

	private static function param($args) {
		$id = $args['id'];
		$flt = $args['filter'];
		switch($args['type']) {
			case 'translation': $data = self::getTranslation($id); break;
			case 'countries': $data = self::getObjsArray('Country', "Country like '$flt%'", true); break;
			case 'states': $data = self::getObjsArray('State', "CountryId=$id and State like '$flt%'", true); break;
			case 'cities': $data = self::getObjsArray('City', "StateId=$id", true); break;
			//case 'domains': $data = self::getObjsArray('Domain', "CategoryId=$id", false); break;
			case 'genres': $data = self::getObjsArray('Genre', "CategoryId=$id", false); break;
		}
		return array('success'=>true, 'logged'=>!Sys::$User->Public, 'data'=>$data);
	}

	private static function initShow($args) {
		$ip = $_SERVER['REMOTE_ADDR']; 
		$geo = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));
		if($geo->geoplugin_status == 404) {
			$ip = '82.64.39.104';
			$geo = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));
		}

		$langName = 'French';
		$country = 'France';
		$cy = Sys::getOneData('Show', 'Country/Code='.$geo->geoplugin_countryCode);
		if($cy) {
			$country = $cy->Country;
			$langName = $cy->Language;
		}
		switch($langName) {
			case 'English': $lang = 'EN'; break;
			case 'French': $lang = 'FR'; break;
			case 'Spanish': $lang = 'ES'; break;
		}
		$cry = ['lang'=>$lang, 'langName'=>$langName, 'country'=>$country, 'countryId'=>$cy->Id];

		$usr = Sys::$User;
		$logged = !$usr->Public;
		$msg = $logged ? self::newMessages($usr->Id) : 0;
		$trn = self::getTranslation($args['translation']);
		$cat = self::getObjsArray('Category');
		//$dom = self::getObjsArray('Domain');
		//$gen = self::getObjsArray('Genre');
		$mat = self::getObjsArray('Maturity');
		$lng = self::getObjsArray('Language');
		//$cry = self::getObjsArray('Country', '', true);
		//$stt = self::getObjsArray('State', '/CountryId='.$args['country']);
		return array('success'=>true, 'logged'=>$logged, 'categories'=>$cat, 'country'=>$cry,
				'genres'=>$gen, 'maturities'=>$mat, 'languages'=>$lng, 
				'translation'=>$trn, 'messages'=>$msg);
	}
	
	
	private static function getTranslation($lang) {
		$trn = [];
		$rs = Sys::getData('Show', "Translation/Language=$lang+Code=");
		foreach($rs as $r) $trn[] = [$r->Original, $r->Translation];
		return $trn;
	}
	
	public static function getObjsArray($name, $query='', $obj=false) {
		$sql = "select Id,$name from `kob-Show-$name`";
		if($query) $sql .= " where $query";
		$sql .= " order by $name";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);

		$arr = array();
		if($obj) {
			foreach($rs as $r) $arr[] = ['id'=>$r['Id'], 'name'=>$r[$name]];
		}
		else {
			foreach($rs as $r) $arr[$r['Id']] = $r[$name];
		}
		return $arr; //['count'=>count($arr), 'data'=>$arr];
	}
		
	private static function logUser() {
		$usr = Sys::$User;
		if($usr->Public) return array('success'=>false, 'logged'=>false);
		
		$id = $usr->Id;
		$msg = self::newMessages($id);
		$fav = Sys::getCount('Show', 'FavPerformance/UserId='.$id);
		//$fav += Sys::getCount('Show', 'FavUser/UserId='.$id);
		return array('success'=>true, 'logged'=>true, 'token'=>session_id(), 'surname'=>$usr->Nom, 'name'=>$usr->Prenom, 
				'id'=>$usr->Id, 'nickname'=>Sys::$User->Initiales, 'messages'=>$msg, 'favourites'=>$fav);
	}
	
	public static function Status() {
		$usr = Sys::$User;
		if($usr->Public) return array('success'=>true, 'logged'=>false, 'messages'=>0);
		return array('success'=>true, 'logged'=>true, 'messages'=>self::newMessages($usr->Id));
	}
	
	private static function newMessages($id) {
		$sql = "select count(*) as cnt from `kob-Show-Message` where ToId=$id and Status=0";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$r = $rs->fetch(PDO::FETCH_ASSOC);
		return $r['cnt'];
	}
	
	private static function registerUser($args) {
		$c = $args['credentials'];
		$n = Sys::getCount('Systeme', 'User/Initiales='.$c->nickname);
		if($n > 0) return array('success'=>false, 'msg'=>'Nickname already in use');
		$n = Sys::getCount('Systeme', 'User/Mail='.$c->email);
		if($n > 0) return array('success'=>false, 'msg'=>'Email address already in use');
		
		$g = Sys::getOneData('Systeme', 'Group/Nom=SHOW');
		$u = genericClass::createInstance('Systeme', 'User');
		$u->addParent($g);
		$u->Login = $c->email;
		$u->Mail = $c->email;
		$u->Initiales = $c->nickname;
		$u->Actif = 0;
		$u->Pass = '[md5]'.md5($c->pass);
		$u->Save();

		$host = $_SERVER['HTTP_ORIGIN'];
		$info = base64_encode($u->Id.','.$c->email.','.time());
		$s = "Hello ".$u->Initiales.",<br /><br /><br />";
		$s .= 'Click on the link below to confirm your registration :<br /><br />';
		$s .= "<strong><a href=\"$host/s/confirm?info=$info\">Confirm registration</a></strong><br /><br />";
		$s .= "This link will be active for 48 hours.<br /><br />";
		$s .= self::MailSignature();
		$params = array('Subject'=>'www.shows.zone : Confirm registration.', 'To'=>array($c->email), 'Body'=>$s);
		self::SendMessage($params);

		return array('success'=>true);
	}
	

	private static function lostPwd($args) {
		$mail = $args['email'];
		if(!filter_var($mail, FILTER_VALIDATE_EMAIL)) return ['success'=>false, 'err'=>'Invalid email format'];
		$u = Sys::getOneData('Show', 'User/Login='.$mail);
		if(!$u) return ['success'=>false, 'err'=>'User not found'];
		$usr = genericClass::createInstance('Systeme', 'User');
		$usr->initFromId($u->Id);
		$ok = false;
		$gs = $usr->getParents('Group');
		foreach($gs as $g) {
			if($g->Nom == 'SHOW') $ok = true;
		}
		if(!$ok) return ['success'=>false, 'err'=>'User not found'];
		
		$host = $_SERVER['HTTP_ORIGIN'];
		$info = base64_encode($mail.','.time());
		$s = "Hello ".$u->Initiales.",<br /><br /><br />";
		$s .= 'Click on the link below to change your password :<br /><br />';
		$s .= "<strong><a href=\"$host/s/password?info=$info\">Change password</a></strong><br /><br />";
		$s .= "This link will be active for 24 hours.<br /><br />";
		$s .= self::MailSignature();
		$params = array('Subject'=>'www.shows.zone : Change password.', 'To'=>array($mail), 'Body'=>$s);
		self::SendMessage($params);		
		
		return ['success'=>true];
	}

	private static function changePwd($args) {
		$cred = $args['credentials'];
		if($cred->id) {
			$usr = Sys::$User;
			if($usr->Id != $cred->id) return ['success'=>false, 'err'=>'Wrong user'];
			if($cred->pass != $cred->pass2) return ['success'=>false, 'err'=>'Incorrect confirmation'];
			if($usr->Pass != '[md5]'.md5($cred->old)) return ['success'=>false, 'err'=>'Invalid password'];
			$usr->Pass = '[md5]'.md5($cred->pass);
			$usr->Save();
			return ['success'=>true];
		} 
				
		$data = array('success'=>false, 'err'=>"Incorrect link.");
		
		$get = isset($cred->info) ? trim($cred->info) : '';
		if($get == '') return $data;
		$info = explode(',', base64_decode($get));
		if(count($info) != 2) return $data;	
		if(($info[1]+86400) < time()) return ['success'=>false, 'err'=>'This link has expired'];
		$u = Sys::getOneData('Systeme', 'User/Login='.$info[0]);
		if(!$u || $u->Mail != $info[0]) return ['success'=>false, 'err'=>'Incorrect user'];
		if($cred->pass != $cred->pass2) return ['success'=>false, 'err'=>'Incorrect confirmation'];
		$u->Pass = '[md5]'.md5($cred->pass);
		$u->Save();
		return ['success'=>true];
	}
	
	private static function registerConfirm($args) {
		$data = array('success'=>0,'message'=>"Incorrect link.");

		$get = isset($args['info']) ? trim($args['info']) : '';
		if($get == '') return $data;
		
		
		$info = explode(',', base64_decode($get));
		if(count($info) != 3) return $data;	
		if(($info[2]+2*86400) < time()) {
			$data['message'] = "This link has expired.";
			return $data;
		}
		
		$u = Sys::getOneData('Systeme', 'User/'.$info[0]);
		if(!$u || $u->Mail != $info[1]) {
			$data['message'] = "An error has occurred. Try to register again";
			return $data;
		}
		$data['success'] = 1;
		$data['mail'] = $info[1];
		if($u->Actif) {
			$data['message'] = "Your registration has already been confirmed.<br />Welcome on show.ooo.";
			return $data;
		}

		$u->Actif = 1;
		$u->Save();
//		$data['success'] = 1;
		$data['message'] = 'Your registration has been confirmed.<br />Welcome on show.ooo.';
		return $data;
	}

	
	public static function MailSignature() {
		return '';
	}
	
//	public static function SendMessage($params) {
//		$m = genericClass::createInstance('Systeme', 'MailQueue');
//		if(isset($params['From']) && !empty($params['From'])) $m->From = $params['From'];
//		else $m->From = 'show@polgo.ooo';
//
//		if(isset($params['To'])) $m->To = implode(',', $params['To']);
//		if(isset($params['ReplyTo'])) $m->ReplyTo = implode(',', $params['ReplyTo']);
//		
//		$m->Subject = $params['Subject'];
//		$m->Body = $params['Body'];
//		if(isset($params['Attachments'])) $m->Attachments = implode(',', $params['Attachments']);
//		
//		//$m->EmbeddedImages = '';
//		$m->Save();
//		return $m->Id;
//	}
	
	public static function SendMessage($params) {
		require_once('Class/Lib/Mail.class.php');

		$Mail = new Mail();
		if(isset($params['From']) && !empty($params['From'])) $Mail->From($params['From']);
		else $Mail->From('info@shows.zone');
		$Mail->Subject($params['Subject']);
		if(isset($params['To'])) {
			foreach($params['To'] as $to)
				$Mail->To($to);
		}
		if(isset($params['ReplyTo'])) {
			foreach($params['ReplyTo'] as $to)
				$Mail->ReplyTo($to);
		}
		if(isset($params['Cc'])) {
			foreach($params['Cc'] as $to)
				$Mail->Cc($to);
		}
		if(isset($params['Bcc'])) {
			foreach($params['Bcc'] as $to)
				$Mail->Bcc($to);
		}
		$bloc = new Bloc();
		$bloc->setFromVar("Mail", $params['Body'], array("BEACON"=>"BLOC"));
		$Pr = new Process();
		$bloc->init($Pr);
		$bloc->generate($Pr);
		$Mail->Body($bloc->Affich());
		
		if($params['Attachments']) {
			foreach($params['Attachments'] as $att) {
				$a = explode('|',$att);
				$Mail->Attach($a[0], $a[1]);
			}
		}
		if($params['EmbeddedImages']) {
			foreach($params['EmbeddedImages'] as $att) {
				$a = explode('|',$att);
				$Mail->EmbeddedImage($a[0], $a[1]);
			}
		}
		$ret = $Mail->Send();
	}
}
