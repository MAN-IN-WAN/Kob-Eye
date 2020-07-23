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
			
			case 'add-role':
				return Performance::AddRole($args);

			case 'del-role':
				return Performance::DelRole($args);
			
			case 'status':
				return self::Status();
			
			case 'messages':
				return Message::Messages($args);
			
			case 'dialog':
				return Message::Dialog($args);
			
			case 'msg':
				return Message::AddMsg($args);
			
			case 'image':
				return Performance::LoadImage($args);
				
			case 'del-pict':
				return Performance::DelPict($args);
			
			case 'add-link':
				return Performance::AddLink($args);
			
			case 'del-link':
				return Performance::DelLink($args);
			
			case 'save-perf':
				return Performance::SavePerf($args);
				
			case 'login':
				return self::logUser();
				
			case 'confirm':
				return self::registerConfirm($args);
				
			case 'register':
				return self::registerUser($args);
				
			case 'logout':
				$GLOBALS['Systeme']->Connection->Disconnect();
				return array('success'=>true, 'logged'=>false, 'token'=>'', 'pseudo'=>'');
				
			case 'init':
				$usr = Sys::$User;
				$logged = !$usr->Public;
				$msg = $logged ? self::newMessages($usr->Id) : 0;
				$trn = self::getTranslation($args['translation']);
				$cat = self::getObjsArray('Category');
				$dom = self::getObjsArray('Domain');
				$gen = self::getObjsArray('Genre');
				$mat = self::getObjsArray('Maturity');
				$lng = self::getObjsArray('Language');
				//$cry = self::getObjsArray('Country', '', true);
				//$stt = self::getObjsArray('State', '/CountryId='.$args['country']);
				return array('success'=>true, 'logged'=>$logged, 'categories'=>$cat, 'countries'=>$cry,
						'domains'=>$dom, 'genres'=>$gen, 'maturities'=>$mat, 'languages'=>$lng, 
						'translation'=>$trn, 'messages'=>$msg);
								
			case 'param':
				$id = $args['id'];
				$flt = $args['filter'];
				switch($args['type']) {
					case 'translation': $data = self::getTranslation($id); break;
					case 'countries': $data = self::getObjsArray('Country', "/Country~$flt", true); break;
					case 'states': $data = self::getObjsArray('State', "/CountryId=$id&State~$flt", true); break;
					case 'cities': $data = self::getObjsArray('City', '/StateId='.$id, true); break;
				}
				return array('success'=>true, 'logged'=>!Sys::$User->Public, 'data'=>$data);
				
				
			case 'perf':
				return Performance::GetPerf($args);
				
			case 'favourite':
				return Performance::SetFavourite($args);
		
			case 'peop': break;
			case 'any': break;
		}
		return array('error'=>'mode unknown');
	}
	
	private static function getTranslation($lang) {
		$trn = [];
		$rs = Sys::getData('Show', "Translation/Language=$lang+Code=");
		foreach($rs as $r) $trn[] = [$r->Original, $r->Translation];
		return $trn;
	}
	
	public static function getObjsArray($name, $query='', $obj=false) {
		//sys::getData($Module, $Query, $Ofst, $Limit, $OrderType, $OrderVar)
		$rs = Sys::getData('Show', $name.$query, 0, 9999, 'ASC', $name, "Id,$name");
		$arr = array();
		if($obj) {
			foreach($rs as $r) $arr[] = ['id'=>$r->Id, 'name'=>$r->$name];
		}
		else {
			foreach($rs as $r) $arr[$r->Id] = $r->$name;
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
		if($n) return array('success'=>false, 'msg'=>'Nickname already in use');
		$n = Sys::getCount('Systeme', 'User/Mail='.$c->email);
		if($n) return array('success'=>false, 'msg'=>'Email address already in use');
		
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
		$params = array('Subject'=>'show.ooo : Confirm registration.', 'To'=>array($c->email), 'Body'=>$s);
		self::SendMessage($params);

		return array('success'=>true);
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
