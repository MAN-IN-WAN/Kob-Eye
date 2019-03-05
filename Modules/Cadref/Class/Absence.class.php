<?php
class Absence extends genericClass {
	
	function Save() {
		$id = $this->Id;
		if($id) {
			$o = genericClass::createInstance('Cadref', 'Absence');
			$o->initFromId($id);
		}
		else $this->Annee = Cadref::$Annee;
		$ret = parent::Save();		

		$group = Sys::$User->getParents('Group')[0]->Nom;
		if($group != 'CADREF_ADMIN') {
			if(!$id || $this->DateDebut != $o->DateDebut || $this->DateFin != $o->DateFin || $this->Description != $o->Description) {
				$a = $this->getOneParent('Enseignant');
				$t = date('d/m H:i', $this->DateDebut).' - '.date('d/m H:i', $this->DateFin).($this->Description ? "<br />".$this->Description : '');
				$m = 'Absence : '.$a->Prenom.' '.$a->Nom;
				AlertUser::addAlert($m,$t,'','',0,[],'CADREF_ADMIN','icmn-aid-kit');
				$params = array('Message'=>$m."\n".str_replace('<br />', "\n", $t));
				if(SMS_ADMIN) Cadref::SendSmsAdmin($params);
			}
		}
		return $ret;
	}

	function SendMessage() {
		if($this->DateFin < time()) return false;
		
		$e = $this->getOneParent('Enseignant');
		$w = date("w", $this->DateDebut);
		$jd = Sys::getOneData('Cadref', "Jour/".($w > 0 ? $w : 7))->Jour;
		$w = date("w", $this->DateFin);
		$jf = Sys::getOneData('Cadref', "Jour/".($w > 0 ? $w : 7))->Jour;
		$s = "CADREF : Absence ".$e->Prenom.' '.$e->Nom;
		$b = "<br /><br />Nous somme au regret de vous informons  de l'absence de votre enseignant ".$e->Prenom.' '.$e->Nom;
		$h = "<br />du $jd ".date('d/m/Y H:i', $this->DateDebut);
		$h .= "<br />au $jf ".date('d/m/Y H:i', $this->DateFin);
		$d .= Cadref::MailSignature();

		$sent = false;
		$cs = $e->getChildren('Classe');
		foreach($cs as $c) {
			if($c->CheckAbsence($this->DateDebut, $this->DateFin)) {
				$as = Sys::getData('Cadref', "Classe/".$c->Id.'/Adherent');
				foreach($as as $a) {
					if($a->Mail) {
						$b0 = Cadref::MailCivility($a);
						$params = array('Subject'=>$s, 'To'=>array($a->Mail), 'Body'=>$b0.$b.$h.$d);
						//if(MSG_ADH) Cadref::SendMessage($params);
					}
					$params = array('Telephone1'=>$a->Telephone1,'Telephone2'=>$a->Telephone2,'Message'=>$s.str_replace('<br />',"\n",$h));
					//if(SMS_ADH) Cadref::SendSms($params);
				}				
				$sent = true;
			}
		}
		
		//if($sent) {
			$params = array('Subject'=>$s, 'Body'=>'Bonjour...'.$b.$h.$d);
			if(MSG_ADMIN) Cadref::SendMessageAdmin($params);
			if($this->Message == 0) {
				$this->Message = 1;
				$this->Save();
			}
			$params = array('Message'=>"Message envoyé aux élèves :\n".$s.str_replace('<br />',"\n",$h));
			if(SMS_ADMIN) Cadref::SendSmsAdmin($params);
		//}
		return $sent;
	}

}


