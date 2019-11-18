<?php
class MailQueue extends genericClass{
	
	function Save() {
		if(!$this->Id) $this->CreationTime = time();
		return parent::Save();
	}

	public static function SendMails() {
		$ms = Sys::getData('Systeme', 'MailQueue/Status=0');
		foreach($ms as $m) {
			try {
				$m->SendTime = time();
				self::SendMessage($m);
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
		$ret = $Mail->Send();
	}
}

