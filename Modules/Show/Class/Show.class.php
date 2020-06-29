<?php

class Show extends Module {
	
	public static function GetShow($args) {
		$mode = $args['mode'];

klog::l("GETSHOW >>>>>",$args);
		switch($mode) {
			case 'login':
				return self::logUser();
				
			case 'register':
				return self::registerUser($args);
				
			case 'logout':
				$cnx = genericClass::createInstance('Systeme', 'Connexion');
				return array('success'=>true, 'logged'=>false, 'token'=>'', 'pseudo'=>'');
				
			case 'lang':
				$l = array();
				$rs = Sys::getData('Show', 'Translation/Language='.$args['lang'].'+Code=');
				foreach($rs as $r) $l[] = [$r->Original, $r->Translation];
				$c = self::getObjsArray('Category');
				$d = self::getObjsArray('Domain');
				$g = self::getObjsArray('Genre');
				$m = self::getObjsArray('Maturity');
				return array('success'=>true, 'logged'=>!Sys::$User->Public, 'lang'=>$l, 'cat'=>$c, 'dom'=>$d, 'gen'=>$g, 'mat'=>$m);
				
			case 'perf':
				return Performance::GetPerf($args);
				
			case 'favourite':
				return Performance::SetFavourite($args);
		
			case 'peop': break;
			case 'any': break;
		}
		return array('error'=>'mode unknown');
	}
	
	public static function getObjsArray($name, $query='') {
		$rs = Sys::getData('Show', $query ? $query : $name);
		$arr = [];
		foreach($rs as $r) $arr[$r->Id] = $r->$name;
		return $arr; //['count'=>count($arr), 'data'=>$arr];
	}
		
	private static function logUser() {
		$usr = Sys::$User;
		if($usr->Public) return array('success'=>false, 'logged'=>false);
		
		$id = $usr->Id;
		$msg = 0; // Sys::getCount('Show', 'Message/UserId='.$id);
		$fav = Sys::getCount('Show', 'FavPerformance/UserId='.$id);
		$fav += Sys::getCount('Show', 'FavUser/UserId='.$id);
		return array('success'=>true, 'logged'=>true, 'token'=>session_id(), 'surname'=>$usr->Nom, 'name'=>$usr->Prenom, 'nickname'=>Sys::$User->Initiales, 'msg'=>$msg, 'fav'=>$fav);
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
		$s .= "<strong><a href=\"$host/Show/register/confirm?info=$info\">Confirm registration</a></strong><br /><br />";
		$s .= "This link will be active for 48 hours.<br /><br />";
		$s .= self::MailSignature();
		$params = array('Subject'=>'show.ooo : Confirm registration.', 'To'=>array($c->email), 'Body'=>$s);
		self::SendMessage($params);

		return array('success'=>true);
	}
	
	
	public static function RegisterConfirmation() {
		$data = array('success'=>0,'message'=>"Incorrect link.");

		$get = isset($_GET['info']) ? trim($_GET['info']) : '';
		if($get == '') return json_encode($data);
		$info = explode(',', base64_decode($get));
		if(count($info) != 3) return json_encode($data);	
		if(($info[2]+2*86400) < time()) {
			$data['message'] = "This link has expired.";
			return json_encode($data);
		}
		
		$u = Sys::getOneData('System', 'User/'.$info[0]);
		if(!$u || $u->Mail != $info[1]) {
			$data['message'] = "An error has occurred. Try to register again";
			return json_encode($data);
		}
		if($u->Actif) {
			$data['message'] = "Your registration has already been confirmed.";
			return json_encode($data);
		}

		$u->Actif = 1;
		$u->Save();
		$data['success'] = 1;
		$data['message'] = 'Your registration has been confirmed.<br />Welcome on show.ooo.';
		return json_encode($data);
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
		if(isset($params['From']) && !empty($params['From'])) $Mail->From = $params['From'];
		else $Mail->From = 'show@polgo.ooo';
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
