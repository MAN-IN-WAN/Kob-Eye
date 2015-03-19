<?php
class Participation extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function Delete() {
		$res = $this->getChildren('Resultat');
		foreach($res as $r) $r->Delete();
		return parent::Delete();
	}

	function SaveResultat() {
		$note = 0;
		foreach($_POST as $k=>$v) {
			if($k == 'submit') {
				$valid = $v == 'valider' ? 1 : 0;
				continue;
			}
			$q = explode('-', $k);
			$qest = Sys::getData('QCM', 'Question/'.$q[1]);
			$qest = $qest[0];
			$rs = Sys::getData('QCM', 'Participation/'.$this->Id.'/Resultat/QuestionId='.$q[1]);
			switch($q[0]) {
				case 'u':
					if($qest->DictionnaireId) $rps = Sys::getData('QCM', 'Dictionnaire/'.$qest->DictionnaireId.'/Entree/BonneReponse=1');
					else $rps = Sys::getData('QCM', 'Question/'.$q[1].'/Reponse/BonneReponse=1');
					if($v == $rps[0]->Id) $note++;
					if(sizeof($rs)) {
						if($rs[0]->ReponseId == $v) continue;
						$rs[0]->ReponseId = $v;
						$rs[0]->Save();
					}
					else $this->newResultat($q[1], $v, $q[0]);
					break;
				case 'v':
					if($qest->TypeQuestionId != 4 && $qest->TypeQuestionId != 7 && $v == $qest->Reponse) $note++;
					if(sizeof($rs)) {
						if($rs[0]->Reponse == $v) continue;
						$rs[0]->Reponse = $v;
						$rs[0]->Save();
					}
					else $this->newResultat($q[1], $v, $q[0]);
					break;
				case 'm':
					if($qest->DictionnaireId) $rps = Sys::getData('QCM', 'Dictionnaire/'.$qest->DictionnaireId.'/Entree/BonneReponse=1');
					else $rps = Sys::getData('QCM', 'Question/'.$q[1].'/Reponse/BonneReponse=1');
					$n = 1 / count($rps);
					$n0 = $n1 = 0;
					foreach($v as $w) {
						$ok = false;
						foreach($rps as $rp) {
							if($w== $rp->Id) {
								$n0 += $n;
								$ok = true;
							}
						}
						if(! $ok) $n1++;
						$ok = false;
						foreach($rs as $r) {
							if($r->ReponseId == $w) {
								$r->ok = $ok = true;
								break;
							}
						}
						if(! $ok) $this->newResultat($q[1], $w, $q[0]);
					}
					foreach($rs as $r) {
						if(! $r->ok) $r->Delete();
					}
					if(!$n1) $note += $n0;
					break;
			}
		}
		if($valid) {
			$this->Valide = 1;
			$this->DateValidation = time();
			$this->Note = $note;
			$this->Save();
			$p = $this->getParents('Projet');
			$p = $p[0];
			if($p->GestionnaireId) {
				$u = genericClass::createInstance('Systeme', 'User');
				$u->initFromId($p->GestionnaireId);
				$h = $this->getParents('Host');
				$txt = $h[0]->Prenom.' '.$h[0]->NomFamille.' à validé l\'enquête '.$p->Nom;
				
				AlertUser::addAlert($txt,'PR'.$p->Id,'QCM','Projet',$p->Id,array($u->Id),null);

				$m = new PHPMailer();
				$m->SetFrom('noreply@unibio.fr','');
				$m->AddAddress($u->Mail, '');
				$m->Subject = $txt;
				$m->IsHTML(false);
				$m->Body = $txt;
				$res = $m->Send();
			}
		}
		return $valid ? 'valide' : '';
	}
	
	private function newResultat($q, $v, $t) {
		$r = genericClass::createInstance('QCM', 'Resultat');
		$r->addParent($this);
		$r->QuestionId = $q;
		if($t == 'v') $r->Reponse = $v;
		else $r->ReponseId = $v;
		$r->Save();
	}
	

}
