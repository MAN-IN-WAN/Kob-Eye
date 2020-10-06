<?php

class Show extends Module {
	
	public static $Lang = '';
	
	public static function GetShow($args) {
		if(isset($args['lang'])) self::$Lang = $args['lang'];
		
		$mode = $args['mode'];

klog::l("GETSHOW >>>>>",$args);
		switch($mode) {			
			//case 'test': return self::checkMsg(Message::SendMails());
			case 'del-account': return self::DelAccount($args);
			case 'del-dialog': return self::checkMsg(Message::DelDialog($args));
			case 'show-status': return self::checkMsg(Performance::SetStatus($args));
			case 'contact': return self::checkMsg(Contact::SaveContact($args));
			case 'vote': return self::checkMsg(Performance::SetVote($args));
			case 'get-vote': return self::checkMsg(Performance::GetVote($args));
			case 'comments': return self::checkMsg(Performance::GetComments($args));
			case 'ratings': return self::checkMsg(Performance::GetRatings($args));
			case 'document': return self::checkMsg(self::document($args));	
			case 'account': return self::checkMsg(self::saveUser($args));	
			case 'nickname': return self::checkMsg(self::checkNickname($args));	
			case 'email': return self::checkMsg(self::checkEMail($args));			
			case 'change-pwd': return self::checkMsg(self::changePwd($args));			
			case 'lost-pwd': return self::lostPwd($args);			
			case 'add-role': return self::checkMsg(Performance::AddRole($args));
			case 'del-role': return self::checkMsg(Performance::DelRole($args));
			case 'status': return self::Status();
			case 'messages': return self::checkMsg(Message::Messages($args));
			case 'dialog': return self::checkMsg(Message::Dialog($args));
			case 'msg': return self::checkMsg(Message::AddMsg($args));
			case 'image': return self::checkMsg(Performance::LoadImage($args));
			case 'del-pict': return self::checkMsg(Performance::DelPict($args));
			case 'main-pict': return self::checkMsg(Performance::MainPict($args));
			case 'add-link': return self::checkMsg(Performance::AddLink($args));
			case 'del-link': return self::checkMsg(Performance::DelLink($args));
			case 'save-perf': return self::checkMsg(Performance::SavePerf($args));
			case 'del-perf': return self::checkMsg(Performance::DeletePerf($args));
			case 'login': return self::logUser($args);
			case 'confirm': return self::registerConfirm($args);
			case 'register': return self::registerUser($args);
			case 'logout': return self::logout($args);
			case 'init': return self::initShow($args);
			case 'lang': return self::loadLang($args);			
			case 'param': return self::checkMsg(self::param($args));
			case 'perf': return self::checkMsg(Performance::GetPerf($args));
			case 'favourite': return self::checkMsg(Performance::SetFavourite($args));
		}
		return array('err'=>'mode unknown');
	}
	
	private static function checkMsg($args) {
		if(isset($args['logged']) && $args['logged']) {
			$args['msgCount'] = self::newMessages(Sys::$User->Id);
		}
		return $args;
	}
	
	
	public static function checkLang() {
		$usr = Sys::$User;
		if($usr->Public) return;
		$inf = '';
		if($usr->Informations) $inf = json_decode($usr->Informations);
		if(!$inf) $inf = new stdClass();
		if(!isset($inf->language) || $inf->language!=self::$Lang) {
			$inf->language = self::$Lang;
			$usr->Informations = json_encode($inf);
			$usr->Save();
		}
	}
	
	public static function userLang($id) {
		$lang = 'EN';
		$usr = Sys::getOneData('Systeme', "User/$id");
		$inf = '';
		if($usr->Informations) $inf = json_decode($usr->Informations);
		if($inf && isset($inf->language)) $lang = $inf->language;
		return $lang;
	}
	
	private static function delAccount($args) {
		$usr = Sys::$User;
		if($usr->Public) return array('success'=>false, 'logged'=>false);
		
		$id = $usr->Id;
		$phase = $args['phase'];
		
		$ps = $usr->getChildren('Performance');
		
		return array('success'=>true, 'logged'=>false);
	}
	
	private static function document($args) {
		$doc = $args['doc'];
		$lang = $args['lang'];
		
		$set = Sys::getOneData('Show', "Setting/Domain=DOCUMENT&SubDomain=$doc&Setting=$lang");
		if(!$set && $lang != 'EN') $set = Sys::getOneData('Show', "Setting/Domain=DOCUMENT&SubDomain=$doc&Setting=EN");
		return ['success'=>true, 'title'=>$set->Value, 'html'=>$set->Html];
	}
	
	private static function logout($args) {
		$GLOBALS['Systeme']->Connection->Disconnect();
		return array('success'=>true, 'logged'=>false, 'token'=>'', 'pseudo'=>'');
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
		
		$ret = self::getUserInfo($args['fcm']);
		$ret['country'] = $cry;
		return $ret;
	}
	
	private static function getUserInfo($fcmToken) {
		$usr = Sys::$User;
		$logged = !$usr->Public;
		$user = null;
		$msg = 0;
		$fav = 0;
		if($logged) {
			$inf = null;
			if($usr->Informations) $inf = json_decode($usr->Informations);
			if($fcmToken && (!isset($inf->fcmToken) || $inf->fcmToken != $fcmToken)) {
				$inf->fcmToken = $fcmToken;
				$usr->Informations = json_encode($inf);
				$usr->Save();
			}
			$user = ['id'=>$usr->Id, 'nickname'=>$usr->Prenom, 'name'=>$usr->Nom, 'email'=>$usr->Mail, 'phone'=>$usr->Tel, 'info'=>$inf];
			if($usr->Privilege) $user['privilege'] = $usr->Privilege;
			$msg = self::newMessages($usr->Id);
			$fav = Sys::getCount('Show', 'FavPerformance/UserId='.$usr->Id);
		}
		$ret = ['success'=>true, 'logged'=>$logged];
		if($logged) {
			$ret['token'] = session_id();
			$ret['user'] = $user;
			$ret['msgCount'] = $msg;
			$ret['favCount'] = $fav;
		}
		return $ret;
	}
	
	private static function logUser($args) {
		$usr = Sys::$User;
		if($usr->Public) return array('success'=>false, 'logged'=>false);
		
		$ret = self::getUserInfo($args['fcm']);
		return $ret;
	}
	
	public static function Status() {
		$usr = Sys::$User;
		if($usr->Public) return array('success'=>true, 'logged'=>false, 'msgCount'=>0);
		return array('success'=>true, 'logged'=>true, 'msgCount'=>self::newMessages($usr->Id));
	}
	
	
	private static function loadLang($args) {
		$lang = $args['lang'];
		$usr = Sys::$User;
		$logged = !$usr->Public;
		$msg = $logged ? self::newMessages($usr->Id) : 0;
		if($args['first']) $trn = self::getTranslation($lang);
		$cat = self::getObjsArray('Category', '', false, $lang);
		$mat = self::getObjsArray('Maturity', '', false, '');
		$lng = self::getObjsArray('Language', '', false, $lang);
		return array('success'=>true, 'logged'=>$logged, 'categories'=>$cat, 'maturities'=>$mat, 'languages'=>$lng, 
				'translation'=>$trn);
	}
	
	private static function getTranslation($lang) {
		$trn = [];
		$rs = Sys::getData('Show', "Translation");
		foreach($rs as $r) $trn[] = ['EN'=>$r->TextEN, 'FR'=>$r->TextFR, 'ES'=>$r->TextES ? $r->TextES : $r->TextEN];
		return $trn;
	}
	
	private static function saveUser($args) {
		$acc = $args['account'];
		$ret = self::checkNickname(['nickname'=>$acc->nickname, 'id'=>$acc->id]);
		if(!$ret['success']) return $ret;
		$ret = self::checkEMail(['email'=>$acc->email, 'id'=>$acc->id]);
		if(!$ret['success']) return $ret;
		$usr = Sys::$User;
		$usr->Prenom = $acc->nickname;
		$usr->Mail = $acc->email;
		$usr->Nom = $acc->name;
		$usr->Tel = $acc->phone;
//		$inf = new stdClass();
//		$inf->displayName = $acc->displayName;
//		$inf->sendInfo = $acc->sendInfo;
//		$inf->showFavourites = $acc->showFavourites;
		$usr->Informations = json_encode($acc->info);
		$usr->Save();
		return ['success'=>true];
	}

	private static function checkNickname($args) {
		$usr = Sys::getOneData('Systeme', 'User/Prenom='.$args['nickname'].'&Id!='.$args['id']);
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
			case 'motives': $data = self::getObjsArray('Motive', "Type='$flt'", true, $lang); break;
		}
		return array('success'=>true, 'logged'=>!Sys::$User->Public, 'data'=>$data);
	}

	public static function getObjsArray($name, $query, $obj, $lang) {
		$en = 'EN';
		$fld = $name;
		if($lang) $fld = "if(ifnull($name$lang,'')='', $name$en, $name$lang) as $name$lang"; 
		$sql = "select Id,$fld from `##_Show-$name`";
		if($query) $sql .= " where $query";
		$sql .= " order by $name$lang";
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
			
	private static function newMessages($id) {
		$sql = "select count(*) as cnt from `##_Show-Message` where ToId=$id and !(Status&1)";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$r = $rs->fetch(PDO::FETCH_ASSOC);
		return $r['cnt'];
	}
	
	private static function registerUser($args) {
		$c = $args['credentials'];
		$n = Sys::getCount('Systeme', 'User/Prenom='.$c->nickname);
		if($n > 0) return array('success'=>false, 'msg'=>'Nickname already in use');
		$n = Sys::getCount('Systeme', 'User/Mail='.$c->email);
		if($n > 0) return array('success'=>false, 'msg'=>'Email address already in use');
		
		$g = Sys::getOneData('Systeme', 'Group/Nom=SHOW');
		$u = genericClass::createInstance('Systeme', 'User');
		$u->addParent($g);
		$u->Login = $c->email;
		$u->Mail = $c->email;
		$u->Prenom = $c->nickname;
		$u->Actif = 0;
		$u->Pass = '[md5]'.md5($c->pass);
		$inf = new stdClass();
		$inf->language = self::$Lang;
		$u->Informations = json_encode($inf);
		$u->Save();
		
		$host = $_SERVER['HTTP_ORIGIN'];
		$info = base64_encode($u->Id.','.$c->email.','.time());
		switch(self::$Lang) {
			case 'EN':
				$s = 'shows.zone: Confirm registration.';
				$b = "Hello ".$u->Prenom.",<br /><br /><br />";
				$b .= 'Click on the link below to confirm your registration :<br /><br />';
				$b .= "<strong><a href=\"$host/s/confirm?info=$info\">Confirm registration</a></strong><br /><br />";
				$b .= "This link will be active for 48 hours.<br /><br />";
				$b .= "You can complete user information in Menu/My account.<br /><br />";
				break;
			case 'FR':
				$s = "shows.zone: Confirmer l'enregistrement";
				$b = "Bonjour ".$u->Prenom.",<br /><br /><br />";
				$b .= 'Cliquer sur le lien ci-dessous pour confirmer votre enregistrement :<br /><br />';
				$b .= "<strong><a href=\"$host/s/confirm?info=$info\">Confirmer l'enregistrement</a></strong><br /><br />";
				$b .= "Ce lien restera actif pendant 48 heures.<br /><br />";
				$b .= "Vous pouvez complèter vos informations dans Menu/Mon compte.<br /><br />";
				break;
			case 'ES':
				$s = 'shows.zone: Confirmar registro.';
				$b = "Hola ".$u->Prenom.",<br /><br /><br />";
				$b .= 'Haga clic en el enlace de abajo para confirmar su registro :<br /><br />';
				$b .= "<strong><a href=\"$host/s/confirm?info=$info\">Confirmación de registro</a></strong><br /><br />";
				$b .= "Este enlace estará activo durante 48 horas..<br /><br />";
				$b .= "Puede completar la información del usuario en Menú/Mi cuenta.<br /><br />";
				break;
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
				$b = "Hello ".$u->Prenom.",<br /><br /><br />";
				$b .= 'Click on the link below to change your password :<br /><br />';
				$b .= "<strong><a href=\"$host/s/password?info=$info\">Change password</a></strong><br /><br />";
				$b .= "This link will be active for 24 hours.<br /><br />";
				break;
			case 'FR':
				$s = 'shows.zone : Mot de passe oublié.';
				$b = "Bonjour ".$u->Prenom.",<br /><br /><br />";
				$b .= 'Cliquer sur le lien ci dessous pour changer de mot de passe :<br /><br />';
				$b .= "<strong><a href=\"$host/s/password?info=$info\">Changer le mot de passe</a></strong><br /><br />";
				$b .= "Ce lien restera actif pendant 24 heures.<br /><br />";
				break;
			case 'ES':
				$s = 'shows.zone : Change password.';
				$b = "Hello ".$u->Prenom.",<br /><br /><br />";
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
		$lang = $args['lang'];

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
			$data['message'] = "Your registration has already been confirmed";
			return $data;
		}

		$u->Actif = 1;
		$u->Save();
//		$data['success'] = 1;
		$data['message'] = 'Your registration has been confirmed';
		return $data;
	}

	
	public static function MailSignature() {
		return '';
	}
	
	public static function SendMessage($params) {
		require_once('Class/Lib/Mail.class.php');

		$Mail = new Mail();
		if(isset($params['From']) && !empty($params['From'])) $Mail->From($params['From']);
		else $Mail->From('noreply@shows.zone');
		$Mail->Subject($params['Subject']);
		if(isset($params['To'])) {
			foreach($params['To'] as $to)
				$Mail->To($to);
		}
		if(isset($params['ReplyTo'])) {
			foreach($params['ReplyTo'] as $to)
				$Mail->ReplyTo($to);
		}
		$Mail->Cc('contact@shows.zone');
		if(isset($params['Cc'])) {
			foreach($params['Cc'] as $to)
				$Mail->Cc($to);
		}
		if(isset($params['Bcc'])) {
			foreach($params['Bcc'] as $to)
				$Mail->Bcc($to);
		}
		$body = $params['Body'];
		$set = Sys::getOneData('Show', 'Setting/Domain=MAIL&SubDomain=DEFAULT&Setting=SIGN');
		$body .= $set->Html;
		$a = explode('|', $set->Value);
		$Mail->EmbeddedImage($a[0], $a[1]);
		
		$bloc = new Bloc();
		$bloc->setFromVar("Mail", $body, array("BEACON"=>"BLOC"));
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
	
	static function SendFCM($token, $title, $body) {
		$key = Sys::getOneData('Show', 'Setting/Domain=NOTIFICATION&SubDomain=FCM&Setting=KEY');
		
		$registrationIds = array($token);
		$msg = array
		(
			'title'=>$title,
			'body'=>$body,
			'vibrate'=>1,
			'sound'=>1
		);
		$fields = array
		(
			'registration_ids'=>$registrationIds,
			'notification'=>$msg
		);
		$headers = array
		(
			'Authorization: key='.$key->Value,
			'Content-Type: application/json'
		);
		
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		curl_close( $ch );
		klog::l('FCM:', $result);
	}
}
