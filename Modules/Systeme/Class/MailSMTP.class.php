<?php
class MailSMTP extends genericClass{
	
	function SelectSMTP($args) {
		$smtp = Sys::getOneData('Systeme', 'MailSMTP/Selected=1');
		if($smtp) {
			$smtp->Selected = 0;
			$smtp->Save();
		}

		$id = $args['SMTP'];
		$smtp = Sys::getOneData('Systeme', 'MailSMTP/'.$id);
		if($smtp) {
			$smtp->Selected = 1;
			$smtp->Save();
			return ['success'=>true, 'smtp'=>$smtp->Nom];
		}

		return array('success'=>false, 'smtp'=>'');
	}
}

