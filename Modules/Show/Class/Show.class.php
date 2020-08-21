<?php

class Show extends Module {
	
	public static $Lang = '';
	
//	public static function CheckLogged() {
//		$usr = Sys::$User;
//		return($usr->Public);
//	}
	
	public static function GetShow($args) {
		if(isset($args['lang']) && $args['lang']) self::$Lang = $args['lang'];
		
		$mode = $args['mode'];

klog::l("GETSHOW >>>>>",$args);
		switch($mode) {			
			case 'del-dialog': return Message::DelDialog($args);
			case 'contact': return Contact::SaveContact($args);
			case 'vote': return Performance::SetVote($args);
			case 'get-vote': return Performance::GetVote($args);
			case 'comments': return Performance::GetComments($args);
			case 'ratings': return Performance::GetRatings($args);
			case 'privacy': return self::privacy($args);	
			case 'account': return self::saveUser($args);	
			case 'nickname': return self::checkNickname($args);	
			case 'email': return self::checkEMail($args);			
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
			case 'main-pict': return Performance::MainPict($args);
			case 'add-link': return Performance::AddLink($args);
			case 'del-link': return Performance::DelLink($args);
			case 'save-perf': return Performance::SavePerf($args);
			case 'del-perf': return Performance::DeletePerf($args);
			case 'login': return self::logUser();
			case 'confirm': return self::registerConfirm($args);
			case 'register': return self::registerUser($args);
			case 'logout': return self::logout($args);
			case 'init': return self::initShow($args);			
			case 'lang': return self::loadLang($args);			
			case 'param': return self::param($args);
			case 'perf': return Performance::GetPerf($args);
			case 'favourite': return Performance::SetFavourite($args);
		}
		return array('err'=>'mode unknown');
	}
	
	private static function privacy($args) {
		$a = Sys::getOneData('Redaction', 'Article/Titre=SHOW');
		return ['success'=>true, 'text'=>$a->Contenu];
	}
	
	private static function logout($args) {
		$GLOBALS['Systeme']->Connection->Disconnect();
		return array('success'=>true, 'logged'=>false, 'token'=>'', 'pseudo'=>'');
	}

	private static function param($args) {
		$id = $args['id'];
		$eqid = '';
		if(strpos($id, ',')) $eqid = " in ($id)";
		else $eqid = "=$id";
		
		$flt = $args['filter'];
		$lang = $args['lang'];
		switch($args['type']) {
			case 'countries': $data = self::getObjsArray('Country', "Country$lang like '%$flt%'", true, $lang); break;
			case 'states': $data = self::getObjsArray('State', "CountryId$eqid and State like '%$flt%'", true, ''); break;
			case 'cities': $data = self::getObjsArray('City', "StateId$eqid", true, ''); break;
			case 'genres': $data = self::getObjsArray('Genre', "CategoryId$eqid", false, $lang); break;
			case 'motives': $data = self::getObjsArray('Motive', "", true, $lang); break;
		}
		return array('success'=>true, 'logged'=>!Sys::$User->Public, 'data'=>$data);
	}

	private static function initShow($args) {
		$first = $args['first'];
		$lang = $args['lang'];
		$langName = $args['langName'];
		$country = $args['country'];
		$countryId = $args['countryId'];
		$utcOffset = "+0200";
		
		if($first) {
			$ip = $_SERVER['REMOTE_ADDR']; 
			if($ip == '127.0.0.1') $ip = "185.87.66.101";
			$geo = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));
			if($geo->geoplugin_status == 200 || $geo->geoplugin_status == 206) {
				$cy = Sys::getOneData('Show', 'Country/Code='.$geo->geoplugin_countryCode);
				if($cy) {
					$countryId = $cy->Id;
					$langName = $cy->Language;
					switch($langName) {
						case 'English': $lang = 'EN'; break;
						case 'French': $lang = 'FR'; $langName = 'Français'; break;
						case 'Spanish': $lang = 'ES'; $langName = 'Español'; break;
					}
					$fld = "Country$lang";
					$country = $cy->$fld;
				}
				$utcOffset = self::getGmtOffset($geo->geoplugin_timezone);
			}
		}
		$cry = ['lang'=>$lang, 'langName'=>$langName, 'country'=>$country, 'countryId'=>$countryId, 'utcOffset'=>$utcOffset];

		$usr = Sys::$User;
		$logged = !$usr->Public;
		$msg = $logged ? self::newMessages($usr->Id) : 0;
		return array('success'=>true, 'logged'=>$logged, 'country'=>$cry, 'msgCount'=>$msg);
	}
	
	
	
	private static function loadLang($args) {
		$lang = $args['lang'];
		$usr = Sys::$User;
		$logged = !$usr->Public;
		$msg = $logged ? self::newMessages($usr->Id) : 0;
		$trn = self::getTranslation($lang);
		$cat = self::getObjsArray('Category', '', false, $lang);
		$mat = self::getObjsArray('Maturity', '', false, '');
		$lng = self::getObjsArray('Language', '', false, $lang);
		return array('success'=>true, 'logged'=>$logged, 'categories'=>$cat, 'maturities'=>$mat, 'languages'=>$lng, 
				'translation'=>$trn);
	}
	
	private static function saveUser($args) {
		$acc = $args['account'];
		$ret = self::checkNickname(['nickname'=>$acc->nickname, 'id'=>$acc->id]);
		if(!$ret['success']) return $ret;
		$ret = self::checkEMail(['email'=>$acc->email, 'id'=>$acc->id]);
		if(!$ret['success']) return $ret;
		$usr = Sys::$User;
		$usr->Initiales = $acc->nickname;
		$usr->Mail = $acc->email;
		$usr->Nom = $acc->name;
		$usr->Tel = $acc->phone;
		$inf = new stdClass();
		$inf->displayName = $acc->displayName;
		$inf->showFavourites = $acc->showFavourites;
		$usr->Informations = json_encode($inf);
		$usr->Save();
		return ['success'=>true];
	}

	private static function checkNickname($args) {
		$usr = Sys::getOneData('Systeme', 'User/Initiales='.$args['nickname'].'&Id!='.$args['id']);
		$exists = $usr !== false && $usr !== null;
		if($exists) return ['success'=>false, 'err'=>'This nickname already exists'];
		return ['success'=>true];
	}
	
	private static function checkEMail($args) {
		$usr = Sys::getOneData('Systeme', 'User/Login='.$args['email'].'&Id!='.$args['id']);
		$exists = $usr !== false && $usr !== null;
		if($exists) return ['success'=>false, 'err'=>'This email already exists'];
		return ['success'=>true];
	}

	private static function getTranslation($lang) {
		$trn = [];
		$rs = Sys::getData('Show', "Translation/Language=$lang");
		foreach($rs as $r) $trn[] = [$r->Original, $r->Translation];
		return $trn;
	}
	
	public static function getObjsArray($name, $query, $obj, $lang) {
		$sql = "select Id,$name$lang from `kob-Show-$name`";
		if($query) $sql .= " where $query";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);

		$arr = array();
		if($obj) {
			foreach($rs as $r) $arr[] = ['id'=>$r['Id'], 'name'=>$r[$name.$lang]];
		}
		else {
			foreach($rs as $r) $arr[$r['Id']] = $r[$name.$lang];
		}
		return $arr;
	}
		
	private static function logUser() {
		$usr = Sys::$User;
		if($usr->Public) return array('success'=>false, 'logged'=>false);
		
		$id = $usr->Id;
		$inf = '';
		if($usr->Informations) $inf = json_decode($usr->Informations);
		$msg = self::newMessages($id);
		$fav = Sys::getCount('Show', 'FavPerformance/UserId='.$id);
		//$fav += Sys::getCount('Show', 'FavUser/UserId='.$id);
		return ['success'=>true, 'logged'=>true, 'msgCount'=>$msg, 'favCount'=>$fav,
				'user'=>['token'=>session_id(), 'name'=>$usr->Nom, 'phone'=>$usr->Tel, 'id'=>$usr->Id, 
				'nickname'=>$usr->Initiales, 'email'=>$usr->Mail, 'showFavourites'=>$inf ? $inf->showFavourites : false,
				'displayName'=>$inf ? $inf->displayName : false]];
	}
	
	public static function Status() {
		$usr = Sys::$User;
		if($usr->Public) return array('success'=>true, 'logged'=>false, 'msgCount'=>0);
		return array('success'=>true, 'logged'=>true, 'msgCount'=>self::newMessages($usr->Id));
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
		switch($args['lang']) {
			case 'EN':
				$s = 'shows.zone : Confirm registration.';
				$b = "Hello ".$u->Initiales.",<br /><br /><br />";
				$b .= 'Click on the link below to confirm your registration :<br /><br />';
				$b .= "<strong><a href=\"$host/s/confirm?info=$info\">Confirm registration</a></strong><br /><br />";
				$b .= "This link will be active for 48 hours.<br /><br />";
				$b .= "Please complete user information in Menu/My account.<br /><br />";
			case 'FR':
				$s = 'shows.zone : Confirm registration.';
				$b = "Bonjour ".$u->Initiales.",<br /><br /><br />";
				$b .= 'Click on the link below to confirm your registration :<br /><br />';
				$b .= "<strong><a href=\"$host/s/confirm?info=$info\">Confirm registration</a></strong><br /><br />";
				$b .= "Ce lien restera actif pendant 48 heures.<br /><br />";
				$b .= "Veuillez complèter vos informations dans Menu/Mon compte.<br /><br />";
			case 'ES':
				$s = 'shows.zone : Confirm registration.';
				$b = "Hello ".$u->Initiales.",<br /><br /><br />";
				$b .= 'Cliquer sur le lien ci dessous pour confirmer votre enregistrement :<br /><br />';
				$b .= "<strong><a href=\"$host/s/confirm?info=$info\">Confirmation d'enregistrement</a></strong><br /><br />";
				$b .= "This link will be active for 48 hours.<br /><br />";
				$b .= "Please complete user information in Menu/My account.<br /><br />";
		}
		$b .= self::MailSignature();
		$params = array('Subject'=>$s, 'To'=>array($c->email), 'Body'=>$b);
		self::SendMessage($params);

		return array('success'=>true);
	}
	
	
	private static function checkGroup($usr) {
		$gs = $usr->getParents('Group');
		foreach($gs as $g) {
			if($g->Nom == 'SHOW') return true;
		}
		return false;
	}

	private static function lostPwd($args) {
		$mail = $args['email'];
		if(!filter_var($mail, FILTER_VALIDATE_EMAIL)) return ['success'=>false, 'err'=>'Invalid email format'];
		$u = Sys::getOneData('Show', 'User/Login='.$mail);
		if(!$u) return ['success'=>false, 'err'=>'User not found'];
		$usr = genericClass::createInstance('Systeme', 'User');
		$usr->initFromId($u->Id);
		$ok = self::checkGroup($usr);
		if(!$ok) return ['success'=>false, 'err'=>'User not found'];
		
		$host = $_SERVER['HTTP_ORIGIN'];
		$info = base64_encode($mail.','.time());
		switch($lang) {
			case 'EN':
				$s = 'shows.zone : Lost password.';
				$b = "Hello ".$u->Initiales.",<br /><br /><br />";
				$b .= 'Click on the link below to change your password :<br /><br />';
				$b .= "<strong><a href=\"$host/s/password?info=$info\">Change password</a></strong><br /><br />";
				$b .= "This link will be active for 24 hours.<br /><br />";
				break;
			case 'FR':
				$s = 'shows.zone : Mot de passe oublié.';
				$b = "Bonjour ".$u->Initiales.",<br /><br /><br />";
				$b .= 'Cliquer sur le lien ci dessous pour changer de mot de passe :<br /><br />';
				$b .= "<strong><a href=\"$host/s/password?info=$info\">Changer le mot de passe</a></strong><br /><br />";
				$b .= "Ce lien restera actif pendant 24 heures.<br /><br />";
				break;
			case 'ES':
				$s = 'shows.zone : Change password.';
				$b = "Hello ".$u->Initiales.",<br /><br /><br />";
				$b .= 'Click on the link below to change your password :<br /><br />';
				$b .= "<strong><a href=\"$host/s/password?info=$info\">Change password</a></strong><br /><br />";
				$b .= "This link will be active for 24 hours.<br /><br />";
				break;
		}
		
		$s .= self::MailSignature();
		$params = array('Subject'=>$s, 'To'=>array($mail), 'Body'=>$$b);
		self::SendMessage($params);		
		
		return ['success'=>true];
	}

	private static function changePwd($args) {
		$cred = $args['credentials'];
		if($cred->id) {
			$usr = Sys::$User;
			if($usr->Id != $cred->id) return ['success'=>false, 'err'=>'Incorrect user'];
			if($cred->pass != $cred->pass2) return ['success'=>false, 'err'=>'Incorrect mail confirmation'];
			if($usr->Pass != '[md5]'.md5($cred->old)) return ['success'=>false, 'err'=>'Invalid password'];
			$usr->Pass = '[md5]'.md5($cred->pass);
			$usr->Save();
			return ['success'=>true];
		} 
				
		$data = array('success'=>false, 'err'=>"Incorrect link");
		
		$get = isset($cred->info) ? trim($cred->info) : '';
		if($get == '') return $data;
		$info = explode(',', base64_decode($get));
		if(count($info) != 2) return $data;	
		if(($info[1]+86400) < time()) return ['success'=>false, 'err'=>'This link has expired'];
		$u = Sys::getOneData('Systeme', 'User/Login='.$info[0]);
		if(!$u || $u->Mail != $info[0]) return ['success'=>false, 'err'=>'Incorrect user'];
		if($cred->pass != $cred->pass2) return ['success'=>false, 'err'=>'Incorrect mail confirmation'];
		$u->Pass = '[md5]'.md5($cred->pass);
		$u->Save();
		return ['success'=>true];
	}
	
	private static function registerConfirm($args) {
		$data = array('success'=>0,'message'=>"Incorrect link");

		$get = isset($args['info']) ? trim($args['info']) : '';
		if($get == '') return $data;
		
		
		$info = explode(',', base64_decode($get));
		if(count($info) != 3) return $data;	
		if(($info[2]+2*86400) < time()) {
			$data['message'] = "This link has expired";
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
		$Mail->Cc('paul@abtel.fr');
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

	private static function getGmtOffset($timezone){
		$userTimeZone = new DateTimeZone($timezone);
		$offset = $userTimeZone->getOffset(new DateTime("now",new DateTimeZone('GMT'))); // Offset in seconds
		$seconds = abs($offset);
		$sign = $offset > 0 ? '+' : '-';
		$hours = floor($seconds / 3600);
		$mins = floor($seconds / 60 % 60);
		$secs = floor($seconds % 60);
		return sprintf("$sign%02d:%02d", $hours, $mins);
//		return sprintf("(GMT$sign%02d:%02d)", $hours, $mins, $secs);
	}
}
