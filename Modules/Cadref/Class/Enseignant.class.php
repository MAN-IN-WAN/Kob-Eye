<?php
class Enseignant extends genericClass {
	
	function Save() {
		$this->Code = strtoupper($this->Code);
		$this->Nom = strtoupper($this->Nom);
		$this->Ville = strtoupper($this->Ville);
		return parent::Save();
	}
	
	function Delete() {
		$rec = $this->getChildren('Classe');
		if(count($rec)) {
			$this->addError(array("Message"=>"Cette fiche ne peut être supprimée", "Prop"=>""));
			return false;
		}
		$rec = $this->getChildren('Absence');
		foreach($rec as $r)
			$r->Delete();
		
		return parent::Delete();
	}
	
	public function GetClassesVisites() {
		$cls = $this->getChildren('Classe/Annee='.Cadref::$Annee);
		foreach($cls as $c) {
			$c->id = $c->Id;
			$c->Disponibles = $c->Inscrits >= $c->Places ? 0 : $c->Places-$c->Inscrits;
		}
		$vis = $this->getChildren('Visite/Annee='.Cadref::$Annee);
		foreach($vis as $v) {
			$v->id = $v->Id;
			$v->DateVisite = date('d/m/Y', $v->DateVisite);
			$v->Disponibles = $v->Inscrits >= $v->Places ? 0 : $v->Places-$v->Inscrits;
		}
		$a = array('classes'=>$cls, 'visites'=>$vis);
		return $a;
	}

	public function SendMessage($params) {
		if(!isset($params['step'])) $params['step'] = 0;
		switch($params['step']) {
			case 0:
				return array(
					'step'=>1,
					'template'=>'sendMessage',
					'args'=>array(),
					'callNext'=>array(
						'nom'=>'SendMessage',
						'title'=>'Message suite',
						'args'=>array('civilite'=>$s),
						'needConfirm'=>false
					)
				);
			case 1:
				if($params['Msg']['sendMode'] == 'mail') {
					$params['Msg']['To'] = array($params['Msg']['Mail']);
					$params['Msg']['Body'] .= Cadref::MailSignature();
					$params['Msg']['Attachments'] = $params['Msg']['Pieces']['data'];
					$ret = Cadref::SendMessage($params['Msg']);
					$params['Msg']['To'] = array(Cadref::$MAIL);
					$ret = Cadref::SendMessage($params['Msg']);
				}
				else {
					$ret = Cadref::SendSms(array('Telephone1'=>$this->Telephone1,'Telephone2'=>$this->Telephone2,'Message'=>$params['Msg']['SMS']));
				}
				return array(
					'data'=>'Message envoyé',
					'params'=>$params['Msg'],
					'success'=>true,
					'callNext'=>false
				);
		}
	}
	
	function CreateUser() {
		$usr = 'ens'.strtolower($this->Code);
		$o = Sys::getOneData('Systeme', 'User/Login='.$usr);
		if($o) return array('msg'=>'Utilisateur déjà existant.', 'success'=>0);
		Cadref::CreateUser($usr, false, $this->Id);
		//$this->Compte = 1;
		//$this->Save();
		return array('msg'=>'Utilisateur créé.','success'=>1);
	}
	
	function ChangePassword($params) {
		$data = array();
		$data['success'] = 0;
		$data['error'] = 0;
		$pwd = '[md5]'.md5($params['PwdOld']);
		if($pwd != Sys::$User->Pass) {
			$data['message'] = 'Mot de passe actuel incorrect';
			$data['error'] = 1;
			return $data;
		}
		$new = $params['PwdNew'];
		$cnf = $params['PwdConf'];
		$p = "/^(?=.{8,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).*$/";
		if(strlen($new) < 8 || ! preg_match($p, $new)) {
			$data['message'] = 'Nouveau mot de passe non conforme';
			$data['error'] = 2;
			return $data;
		}
		if($new != $cnf) {
			$data['message'] = 'Confirmation incorrecte';
			$data['error'] = 3;
			return $data;
		}
		Sys::$User->Pass = '[md5]'.md5($new);
		Sys::$User->Save();
		
		$this->Password = $new;
		$this->Save();
		
		if(strpos($this->Mail, '@') > 0) {
			$s = Cadref::MailCivility($this);
			$s .= "Votre nouveau mot de passe a été enregistré.<br /><br />";
			$s .= Cadref::MailSignature();
			$params = array('Subject'=>(Cadref::$UTL.' : Changement de mot de passe.'),
				'To'=>array($this->Mail,Cadref::$MAIL),
				'Body'=>$s);
			Cadref::SendMessage($params);
		}
		$msg = Cadref::$UTL." : Changement de mot de passe.\nCode utilisateur: ens".strtolower($this->Code)."\nMote de passe: $new\n";
		$params = array('Telephone1'=>$this->Telephone1,'Telephone2'=>$this->Telephone2,'Message'=>$msg);
		Cadref::SendSms($params);

		$data['success'] = 1;
		$data['message'] = 'Mot de passe enregistré';
		return $data;
	}

	function PublicSendMessage($params) {
		$annee = Cadref::$Annee;
		$id = $this->Id;
		$mode = $params['sendMode'];
		$args = array();
		$args['Subject'] = $params['Subject'];
		$args['Body'] = $params['Sender']."<br /><br />".$params['Body'];
		$args['Attachments'] = $params['Pieces']['data'];
		$args['ReplyTo'] = array($this->Mail);
		$t = explode('@', Cadref::$MAIL);
		$args['From'] = "noreply@".$t[1];
		
		$to = $params['Mail'];
		if($to == 'C') {
			$args['To'] = array(Cadref::$MAIL);
			Cadref::SendMessage($args);
			return array('data'=>'Message envoyé');
		}

		$sql = "
select Mail, Telephone1, Telephone2
from `##_Cadref-ClasseEnseignants` ce
inner join `##_Cadref-Inscription` i on i.ClasseId=ce.Classe and i.Annee='$annee'
inner join `##_Cadref-Adherent` a on a.Id=i.AdherentId
where ce.EnseignantId=$id";
		if($to != 'T') $sql .= " and ce.Classe=$to";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		
		$args['To'] = array($this->Mail);
		$ret = Cadref::SendMessage($args);
		foreach($pdo as $p) {
			if($p['Mail']) {
				$args['To'] = array($p['Mail']);
				$ret = Cadref::SendMessage($args);
			}
		}
		
		$args['To'] = array(Cadref::$MAIL);
		$ret = Cadref::SendMessage($args);
		
//		$ret = Cadref::SendSms(array('Telephone1'=>$this->Telephone1,'Telephone2'=>$this->Telephone2,'Message'=>$params['Msg']['SMS']));
		return array('data'=>'Message envoyé','sql'=>$sql);
	}

	function PrintEtiquettes($obj) {
		$mode = $obj['mode'];
		
		if($mode != 'print') {
			$mail = $obj['Mail'];
			$sql = "select Mail,Telephone1,Telephone2 from from `##_Cadref-Enseignant` where Inactif=0 and ";
			if($mail != 0) $sql .= "Id=$mail";
			else $sql .= "Mail<>''";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			if($mode == 'mail') {
				$args = array('Subject'=>$obj['Sujet'], 'To'=>array(Cadref::$MAIL), 'Body'=>$obj['Corps'], 'Attachments'=>$obj['Pieces']['data']);
				Cadref::SendMessage($args);
			}
			foreach($pdo as $p) {
				if($mode == 'sms') {
					$params = array('Telephone1'=>$p['Telephone1'],'Telephone2'=>$p['Telephone2'],'Message'=>$obj['SMS']);
					Cadref::SendSms($params);
				}
				else {
					$args = array('Subject'=>$obj['Sujet'], 'To'=>array($p['Mail']), 'Body'=>$obj['Corps'], 'Attachments'=>$obj['Pieces']['data']);
					Cadref::SendMessage($args);				
				}
			}
			return array('pdf'=>false, 'msg'=>true);
		}
				
		$sql .= "select Nom, Prenom, Adresse1, Adresse2, CP, Ville from `##_Cadref-Enseignant` where Inactif=0 order by Nom, Prenom";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return false;

		require_once ('PrintLabels.class.php');
		$pdf = new PrintLabels();
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle('Etiquettes enseignants');

		$pdf->AddPage();
		foreach($pdo as $l) {
			$pdf->AddLabel($l);
		}

		$file = 'Home/tmp/EtiquetteEnseignant_'.date('YmdHis').'.pdf';
		$pdf->Output(getcwd() . '/' . $file);
		$pdf->Close();

		return array('pdf'=>$file, 'msg'=>false);
	}
	
	function PrintPresence($obj) {
		$obj['Enseignant'] = $this->Id;
		$c = genericClass::createInstance('Cadref', 'Classe');
		return $c->PrintPresence($obj);
	}
	
	function PrintAdherents() {
		$annee = Cadref::$Annee;
		$obj = array('CurrentUrl'=>'impressionslisteadherents', 'Contenu'=>'N', 'Rupture'=>'C', 'Enseignant'=>$this->Id, 'Annee'=>$annee);
		$a = genericClass::createInstance('Cadref', 'Adherent');
		return $a->PrintAdherent($obj);
	}
	
	function PrintVisite($visite) {
		$annee = Cadref::$Annee;
		$a = Sys::getOneData('Cadref', "Visite/Visite=$visite&Annee=$annee");
		$args['Fin'] = $args['Debut'] = $a->DateVisite;
		$args['Guide'] = 1;
		$args['Chauffeur'] = 0;
		$args['Interne'] = 0;
		$a->Id = 0;
		return $a->PrintVisite($args);		
	}
	
}
