<?php

class Contact extends genericClass {
	
	public static function SaveContact($args) {
		$lang = $args['lang'];
		$msg = $args['msg'];
		$c = genericClass::createInstance('Show', 'Contact');
		$c->Mail = $msg->email;
		$c->Subject = $msg->subject;
		$c->Body = $msg->body;
		$c->CategoryId = $msg->category;
		$c->MotiveId = $msg->motive;
		$c->Save();
		
		$name = ',';
		$usr = Sys::$User;
		if(! $usr->Public) $name = " $usr->Initiales,";

		switch($lang) {
			case 'EN':
				$s = 'shows.zone';
				$b = "Hello$name<br /><br /><br />";
				$b .= 'We have received your message and shall contact you very soon.<br /><br />';
			case 'FR':
				$s = 'shows.zone';
				$s = "Bonjour$name<br /><br /><br />";
				$b = 'Nous avons reçu votre message et vous contacterons très bientôt.<br /><br />';
			case 'ES':
				$s = 'shows.zone';
				$b = "Bonjour$name<br /><br /><br />";
				$b .= 'We have received your message and shall contact you very soon.<br /><br />';
		}
		$b .= Show::MailSignature();
		$params = array('Subject'=>$s, 'To'=>array($c->Mail), 'Body'=>$b);
		Show::SendMessage($params);
		
		return ['success'=>true];
	}
}