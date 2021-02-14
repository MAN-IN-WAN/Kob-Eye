<?php
class MailQueue extends genericClass{
	
	function Save() {
		if(!$this->Id) {
			$this->CreationTime = time();
			$smtp = Sys::getOneData('Systeme', 'MailSMTP/Selected=1');
			if($smtp) {
				$this->MailSMTPId = $smtp->Id;
				if($smtp->Domain) {
					$f = explode('@',$this->From);
					$this->From = $f[0].'@'.$smtp->Domain;
				}
			}
		}
		
		return parent::Save();
	}

	public static function SendMails() {
		$retry = time()-600;
		$ms = Sys::getData('Systeme', "MailQueue/Status=0+(!Status=2&Tries<5&SendTime<$retry!)",0,50);
		foreach($ms as $m) {
			$m->SendTime = time();
			$m->Tries++;
			try {
				self::SendMessage($m);
				$m->Error = '';
				$m->Status = 1;
			} catch(phpmailerException $e) {
				$m->Error = $e->errorMessage();
				$m->Status = 2;
			}
			$m->Save();
		}
	}
	
	private static function SendMessage($m) {
		require_once('Class/Lib/Mail.class.php');

		$Mail = new Mail();
		$Mail->Subject($m->Subject);
		$Mail->From($m->From);
		if($m->To) {
			$tos = explode(',', $m->To);
			foreach($tos as $to)
				$Mail->To($to);
		}
		if($m->ReplyTo) {
			$rtos = explode(',', $m->ReplyTo);
			foreach($rtos as $rto)
				$Mail->ReplyTo($rto);
		}
		if($m->Cc) {
			$ccs = explode(',', $m->Cc);
			foreach($ccs as $cc)
				$Mail->Cc($cc);
		}
		if($m->Bcc) {
			$bccs = explode(',', $m->Bcc);
			foreach($bccs as $bcc)
				$Mail->Bcc($bcc);
		}
		$bloc = new Bloc();
		$bloc->setFromVar("Mail", $m->Body, array("BEACON"=>"BLOC"));
		$Pr = new Process();
		$bloc->init($Pr);
		$bloc->generate($Pr);
		$Mail->Body($bloc->Affich());
		
		if($m->Attachments) {
			$atts = explode(',', $m->Attachments);
			foreach($atts as $att) {
				$a = explode('|',$att);
				$Mail->Attach($a[0], $a[1]);
			}
		}
		if($m->EmbeddedImages) {
			$atts = explode(',', $m->EmbeddedImages);
			foreach($atts as $att) {
				$a = explode('|',$att);
				$Mail->EmbeddedImage($a[0], $a[1]);
			}
		}
		
		if($m->MailSMTPId) {
			$smtp = Sys::getOneData('Systeme', 'MailSMTP/'.$m->MailSMTPId);
			if($smtp && $smtp->Mailer != '') {
				$Mail->Mailer = $smtp->Mailer;
				$Mail->Host = $smtp->Host;
				$Mail->Port = $smtp->Port;
				$Mail->Username = $smtp->Username;
				$Mail->Password = $smtp->Password;
				$Mail->SMTPSecure = $smtp->SMTPSecure;
				$Mail->SMTPAuth = $smtp->SMTPAuth;
			}
		}
		
		$ret = $Mail->Send();
	}
}

