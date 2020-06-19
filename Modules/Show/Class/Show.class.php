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
		$info = base64_encode($u->Id.','.$mail.','.time());
		$s = "Hello ".$u->Initiales.",<br /><br /><br />";
		$s .= 'Click on the link below to confirm your registration :<br /><br />';
		$s .= "<strong><a href=\"$host/Show/Adherent/confirmRegistration?info=$info\">Confirm registration</a></strong><br /><br />";
		$s .= "This link will be active for 48 hours.<br /><br />";
		$s .= self::MailSignature();
		$params = array('Subject'=>(Cadref::$UTL.' : Confirmation d\'enregistrement.'),
			'To'=>array($mail,Cadref::$MAIL), 'Body'=>$s);
		self::SendMessage($params);

		return array('success'=>true);
	}
	
	
	public static function RegisterConfirmation() {
		$data = array('success'=>0,'message'=>"Une erreur c'est produite : Le lien est incorrect.");

		$get = isset($_GET['info']) ? trim($_GET['info']) : '';
		if($get == '') return json_encode($data);
		$info = explode(',', base64_decode($get));
		if(count($info) != 3) return json_encode($data);	
		if(($info[2]+2*86400) < time()) {
			$data['message'] = "Une erreur c'est produite : Le lien est expiré.";
			return json_encode($data);
		}
		$u = Sys::getOneData('Cadref', 'User/'.$info[0]);
		if(!$a || $a->Mail != $info[1]) {
			$data['message'] = "Une erreur c'est produite.<br />Veuillez contacter le ".Cadref::$UTL." au ".Cadref::$TEL.".";
			return json_encode($data);
		}
		if($a->Confirme) {
			$data['message'] = "Vous avez déjà confirmé votre la création de votre compte.<br />Vous avez dû recevoir un mail contenant vos identifiants.";
			return json_encode($data);
		}

		$u->Actif = 1;
		$a->Save();
		$data['success'] = 1;
		$data['message'] = 'Votre code utilisateur et votre mot de passe vous ont été envoyés par email.';
		return json_encode($data);
	}

	
	public static function MailSignature() {
		$p = self::GetParametre('MAIL', 'STANDARD', 'SIGNATURE');
		return $p->Texte;
		self::$MailLogo = $p->Valeur;
	}
	
	public static function SendMessage($params) {
		$m = genericClass::createInstance('Systeme', 'MailQueue');
		if(isset($params['From']) && !empty($params['From'])) $m->From = $params['From'];
		else $m->From = self::$MAIL_LET;

		if(isset($params['To'])) $m->To = implode(',', $params['To']);
		if(isset($params['ReplyTo'])) $m->ReplyTo = implode(',', $params['ReplyTo']);
		
		$m->Subject = $params['Subject'];
		$m->Body = $params['Body'];
		if(isset($params['Attachments'])) $m->Attachments = implode(',', $params['Attachments']);
		
		$p = self::GetParametre('MAIL', 'STANDARD', 'SIGNATURE');
		$m->EmbeddedImages = $p->Valeur;
		$m->Save();
		return $m->Id;
	}

	public static function GetParametre($dom, $sdom, $par) {
		return Sys::getOneData('Cadref', "Parametre/Domaine=$dom&SousDomaine=$sdom&Parametre=$par");
	}
	public static function SetParametre($dom, $sdom, $par, $val, $txt='') {
		$p = Sys::getOneData('Cadref', "Parametre/Domaine=$dom&SousDomaine=$sdom&Parametre=$par");
		if(!$p) {
			$p = genericClass::createInstance('Cadref', 'Parametre');
			$p->Domaine = $dom;
			$p->SousDomaine = $sdom;
			$p->Parametre = $par;
		}
		$p->Valeur = $val;
		$p->Texte = $txt;
		$p->Save();
	}

	public static function removeAccents($str) {
		static $map = [
        // single letters
        'à' => 'a',
        'á' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'ą' => 'a',
        'å' => 'a',
        'ā' => 'a',
        'ă' => 'a',
        'ǎ' => 'a',
        'ǻ' => 'a',
        'À' => 'A',
        'Á' => 'A',
        'Â' => 'A',
        'Ã' => 'A',
        'Ä' => 'A',
        'Ą' => 'A',
        'Å' => 'A',
        'Ā' => 'A',
        'Ă' => 'A',
        'Ǎ' => 'A',
        'Ǻ' => 'A',


        'ç' => 'c',
        'ć' => 'c',
        'ĉ' => 'c',
        'ċ' => 'c',
        'č' => 'c',
        'Ç' => 'C',
        'Ć' => 'C',
        'Ĉ' => 'C',
        'Ċ' => 'C',
        'Č' => 'C',

        'ď' => 'd',
        'đ' => 'd',
        'Ð' => 'D',
        'Ď' => 'D',
        'Đ' => 'D',


        'è' => 'e',
        'é' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'ę' => 'e',
        'ē' => 'e',
        'ĕ' => 'e',
        'ė' => 'e',
        'ě' => 'e',
        'È' => 'E',
        'É' => 'E',
        'Ê' => 'E',
        'Ë' => 'E',
        'Ę' => 'E',
        'Ē' => 'E',
        'Ĕ' => 'E',
        'Ė' => 'E',
        'Ě' => 'E',

        'ƒ' => 'f',


        'ĝ' => 'g',
        'ğ' => 'g',
        'ġ' => 'g',
        'ģ' => 'g',
        'Ĝ' => 'G',
        'Ğ' => 'G',
        'Ġ' => 'G',
        'Ģ' => 'G',


        'ĥ' => 'h',
        'ħ' => 'h',
        'Ĥ' => 'H',
        'Ħ' => 'H',

        'ì' => 'i',
        'í' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'ĩ' => 'i',
        'ī' => 'i',
        'ĭ' => 'i',
        'į' => 'i',
        'ſ' => 'i',
        'ǐ' => 'i',
        'Ì' => 'I',
        'Í' => 'I',
        'Î' => 'I',
        'Ï' => 'I',
        'Ĩ' => 'I',
        'Ī' => 'I',
        'Ĭ' => 'I',
        'Į' => 'I',
        'İ' => 'I',
        'Ǐ' => 'I',

        'ĵ' => 'j',
        'Ĵ' => 'J',

        'ķ' => 'k',
        'Ķ' => 'K',


        'ł' => 'l',
        'ĺ' => 'l',
        'ļ' => 'l',
        'ľ' => 'l',
        'ŀ' => 'l',
        'Ł' => 'L',
        'Ĺ' => 'L',
        'Ļ' => 'L',
        'Ľ' => 'L',
        'Ŀ' => 'L',


        'ñ' => 'n',
        'ń' => 'n',
        'ņ' => 'n',
        'ň' => 'n',
        'ŉ' => 'n',
        'Ñ' => 'N',
        'Ń' => 'N',
        'Ņ' => 'N',
        'Ň' => 'N',

        'ò' => 'o',
        'ó' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'ð' => 'o',
        'ø' => 'o',
        'ō' => 'o',
        'ŏ' => 'o',
        'ő' => 'o',
        'ơ' => 'o',
        'ǒ' => 'o',
        'ǿ' => 'o',
        'Ò' => 'O',
        'Ó' => 'O',
        'Ô' => 'O',
        'Õ' => 'O',
        'Ö' => 'O',
        'Ø' => 'O',
        'Ō' => 'O',
        'Ŏ' => 'O',
        'Ő' => 'O',
        'Ơ' => 'O',
        'Ǒ' => 'O',
        'Ǿ' => 'O',


        'ŕ' => 'r',
        'ŗ' => 'r',
        'ř' => 'r',
        'Ŕ' => 'R',
        'Ŗ' => 'R',
        'Ř' => 'R',


        'ś' => 's',
        'š' => 's',
        'ŝ' => 's',
        'ş' => 's',
        'Ś' => 'S',
        'Š' => 'S',
        'Ŝ' => 'S',
        'Ş' => 'S',

        'ţ' => 't',
        'ť' => 't',
        'ŧ' => 't',
        'Ţ' => 'T',
        'Ť' => 'T',
        'Ŧ' => 'T',


        'ù' => 'u',
        'ú' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'ũ' => 'u',
        'ū' => 'u',
        'ŭ' => 'u',
        'ů' => 'u',
        'ű' => 'u',
        'ų' => 'u',
        'ư' => 'u',
        'ǔ' => 'u',
        'ǖ' => 'u',
        'ǘ' => 'u',
        'ǚ' => 'u',
        'ǜ' => 'u',
        'Ù' => 'U',
        'Ú' => 'U',
        'Û' => 'U',
        'Ü' => 'U',
        'Ũ' => 'U',
        'Ū' => 'U',
        'Ŭ' => 'U',
        'Ů' => 'U',
        'Ű' => 'U',
        'Ų' => 'U',
        'Ư' => 'U',
        'Ǔ' => 'U',
        'Ǖ' => 'U',
        'Ǘ' => 'U',
        'Ǚ' => 'U',
        'Ǜ' => 'U',


        'ŵ' => 'w',
        'Ŵ' => 'W',

        'ý' => 'y',
        'ÿ' => 'y',
        'ŷ' => 'y',
        'Ý' => 'Y',
        'Ÿ' => 'Y',
        'Ŷ' => 'Y',

        'ż' => 'z',
        'ź' => 'z',
        'ž' => 'z',
        'Ż' => 'Z',
        'Ź' => 'Z',
        'Ž' => 'Z',


        // accentuated ligatures
        'Ǽ' => 'A',
        'ǽ' => 'a',
		];
		return strtr($str, $map);
	}

}