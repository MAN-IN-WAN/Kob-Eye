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
		foreach($cls as $c) $c->id = $c->Id;
		$vis = $this->getChildren('Visite/Annee='.Cadref::$Annee);
		foreach($vis as $v) {
			$v->id = $v->Id;
			$v->DateVisite = date('d/m/Y', $v->DateVisite);
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
		$this->Compte = 1;
		$this->Save();
		return array('msg'=>'Utilisateur créé.','success'=>1);
	}
	
	function PublicSendMessage($params) {
		$annee = Cadref::$Annee;
		$id = $this->Id;
		$mode = $params['sendMode'];
		$args = array();
		$args['Subject'] = $params['Subject'];
		$args['Body'] = $params['Sender']."<br /><br />".$params['Body'];
		$args['Attachments'] = $params['Msg']['Pieces']['data'];
		
		$to = $params['Mail'];

		if($to == 'C') {
			$args['To'] = aray('contact@cadref.com');
			Cadref::SendMessage($args);
//			$us = Sys::getData('Systeme', 'Group/Nom=CADREF_ADMIN/User');
//			$to = array();
//			foreach($us as $u) {
//				$args['To'] = array($u->Mail);
//				Cadref::SendMessage($args);
//			}
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
		foreach($pdo as $p) {
			if($p['Mail']) {
				$args['To'] = array($p['Mail']);
				$ret = Cadref::SendMessage($args);
			}
		}
//		$ret = Cadref::SendSms(array('Telephone1'=>$this->Telephone1,'Telephone2'=>$this->Telephone2,'Message'=>$params['Msg']['SMS']));
		return array('data'=>'Message envoyé','sql'=>$sql);
	}

	function PrintEtiquettes($obj) {
		$mode = $obj['mode'];
		
		if($mode != 'print') {
			$mail = $obj['Mail'];
			$sql = "select Mail,Telephone1,Telephone2 from from `##_Cadref-Enseignant` where ";
			if($mail != 0) $sql .= "Id=$mail";
			else $sql .= "Mail<>''";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			foreach($pdo as $p) {
				if($mode == 'sms') {
					$params = array('Telephone1'=>$p['Telephone1'],'Telephone2'=>$p['Telephone2'],'Message'=>$obj['SMS']);
					Cadref::SendSms($params);
				}
				else {
					$args = array('Subject'=>$obj['Sujet'], 'To'=>array($p['Mail']), 'Body'=>$obj['Corps'], 'Attachments'=>$obj['Pieces']['data']);
					if(MSG_ADH) Cadref::SendMessage($args);				
				}
			}
			return array('pdf'=>false, 'msg'=>true);
		}
				
		$sql .= "select Nom, Prenom, Adresse1, Adresse2, CP, Ville from `##_Cadref-Enseignant` order by Nom, Prenom";
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
		$obj = array('CurrentUrl'=>'impressionslisteadherents', 'Contenu'=>'A', 'Rupture'=>'C', 'Enseignant'=>$this->Id, 'Annee'=>$annee);
		$a = genericClass::createInstance('Cadref', 'Adherent');
		return $a->PrintAdherent($obj);
	}
	
	function PrintVisite($args) {
		$a = genericClass::createInstance('Cadref', 'Visite');
		return $a->PrintVisite($args);		
	}
	
}
