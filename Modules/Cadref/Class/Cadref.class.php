<?php

class Cadref extends Module {

	public static $Annee = null;
	public static $Cotisation = null;

	/**
	 * Surcharge de la fonction postInit
	 * Après l'authentification de l'utilisateur
	 * Toutes les fonctionnalités sont disponibles
	 * @void 
	 */
	function postInit() {
		parent::postInit();
		//chargement des variables globales par défaut pour le module boutique
		$this->initGlobalVars();
	}

	function initGlobalVars() {
		$annee = Sys::getOneData('Cadref', 'Annee/EnCours=1');
		self::$Annee = $annee->Annee;
		self::$Cotisation = $annee->Cotisation;
		$GLOBALS["Systeme"]->registerVar("AnneeEnCours", $annee->Annee);
		$GLOBALS["Systeme"]->registerVar("Cotisation", $annee->Annee);
	}

	public static function GetParametre($dom, $sdom, $par) {
		return Sys::getOneData('Cadref', "Parametre/Domaine=$dom&SousDomaine=$sdom&Parametre=$par");
	}
	
	public static function CheckAdherent() {
		$data = array();
		$data['success'] = 0;
		$data['message'] = '';
		$data['controls'] = ['close'=>0, 'save'=>1, 'cancel'=>1];
		
		// create user
		if($_POST['ValidForm'] == 2) {
			self::CreateUser($_POST['CadrefNumero1'], false);
			$data['success'] = 1;
			$data['message'] = 'Votre mot de passe vous a été envoyé par email.';
			$data['ValidForm'] = "3";
			return json_encode($data);
		}

		$num = isset($_POST['CadrefNumero']) ? trim($_POST['CadrefNumero']) : '';
		$nom = isset($_POST['CadrefNom']) ? trim($_POST['CadrefNom']) : '';
		$pnom = isset($_POST['CadrefPrenom']) ? trim($_POST['CadrefPrenom']) : '';
		$mail = isset($_POST['CadrefMail']) ? trim($_POST['CadrefMail']) : '';
		$tel = isset($_POST['CadrefTel']) ? $_POST['CadrefTel'] : '';
		if((empty($num) && (empty($nom)) || empty($pnom)) || (empty($mail) && empty($tel))) {
			$data['message'] = "Vous devez spécifier soit le numéro soit les nom et prénom<br />puis l'adresse mail ou le téléphone";
			return json_encode($data);
		}
		
		$telr = '';
		$nomr ='';
		if($num) $num = substr('000000', 0, 6 - strlen($num)).$num;
//		if($tel) $telr = preg_replace('/[^0-9]/', '([^0-9])*', $tel);
		if($tel) {
			$telr = preg_replace('/[^0-9]/', '', $tel);
			$telr = substr($telr,0,2).'.'.substr($telr,2,2).'.'.substr($telr,4,2).'.'.substr($telr,6,2).'.'.substr($telr,8,2);
		}
		if($nom) $nomr = preg_replace('/([^A-Z]){1,}/', '([^A-N])*', $nom);
		if($pnom) $pnomr = preg_replace('/([^A-Z]){1,}/', '([^A-N])*', $pnom);

		if($num) $w = "Numero='$num'";
		else $w = "(Nom regexp '$nomr' and Prenom regexp '$pnomr')";

		if($mail) $w1 .= "Mail='$mail'";
		if($telr) {
			if($w1) $w1 .= " or ";
//			$w1 .= "Telephone1 regexp '$telr' or Telephone2 regexp '$telr'";
			$w1 .= "Telephone1 = '$telr' or Telephone2 = '$telr'";
		}
		$sql = "select Numero,Nom,Prenom,Adresse1,Ville,Mail,Telephone1,Telephone2 from `##_Cadref-Adherent` where ($w) and ($w1) limit 1";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if($pdo && $pdo->rowcount()) {
			foreach($pdo as $p) {
				$r = array();
				$r['Numero'] = $p['Numero'];
				$r['Nom'] = $p['Nom'];
				$r['Prenom'] = $p['Prenom'];
				$a = $p['Adresse1'];
				$r['Adresse'] = substr($a, 0, 10);
				if(strlen($a) > 10) $r['Adresse'] .= '...';
				if(strlen($a) > 16) $r['Adresse'] .= substr($a, -3, 3);
				$r['Ville'] = $p['Ville'];
				$s = $p['Mail'];
				if($mail && $mail == $s) $r['Mail'] = $s;
				else if($s) {
					$t = explode('@', $s);
					$r[Mail] = substr($t[0], 0, 2).'...'.substr($t[0], -1, 1).'@'.substr($t[1], 0, 2).'...'.substr($t[1], -2, 2);
				}
				$r['Tel'] = '';
				if($tel) {
					$t = preg_replace('/[^0-9]/', '', $tel);
					if($t == preg_replace('/[^0-9]/', '', $p['Telephone1'])) $r['Tel'] = $p['Telephone1']; 
					elseif($t == preg_replace('/[^0-9]/', '', $p['Telephone2'])) $r['Tel'] = $p['Telephone2']; 
				}
				if(!$r['Tel']) {
					$t = !$p['Telephone1'] ? $p['Telephone2'] : $p['Telephone1'];
					if($t) $r['Tel'] = '...'.substr($t, -4, 4);
				}	
				$data['ValidForm'] = "2";
				$data['data'] = $r;
				break;
			}
			$data['success'] = 1;
			$u = Sys::getOneData('Systeme', 'User/Login='.$p['Numero']);
			if($u) $data['message'] = 'Votre espace CADREF existe déjà. Si vous avez perdu votre mot de passe appuyez sur continuer pour en recevoir un nouveau par email ou par SMS.';
			else $data['message'] = 'Si les informations suivantes vous correspondent, appuyez sur continuer pour recevoir votre mot de passe par email ou par SMS.';
		} else $data['message'] = 'Aucun adhérent ne correspond à ces critères.';

		$data['sql'] = $sql;
		$data["controls"] = ['close'=>0, 'save'=>1, 'cancel'=>1];
		return json_encode($data);
	}

		
	private static function CreateUser($num, $confirm=false) {
		$a = Sys::getOneData('Cadref', 'Adherent/Numero='.$num);
		$u = Sys::getOneData('Systeme', 'User/Login='.$num);
		$new = false;
		if(! $u) {
			$new = true;
			$g = Sys::getOneData('Systeme', 'Group/Nom=CADREF_ADH');
			$u = genericClass::createInstance('Systeme', 'User');
			$u->addParent($g);
			$u->Login = $num;
			$u->Mail = $num.'@cadref.com';
			$u->Nom = $a->Nom;
			$u->Prenom = $a->Prenom;
		}
		$p = self::GeneratePassword();
		$u->Pass = '[md5]'.md5($p);
		$u->Save();

		$s = $confirm ? 'Confirmation d\'inscription web : ' : 'Création compte utilisateur : ';
		AlertUser::addAlert('Adhérent : '.$a->Prenom.' '.$a->Nom,$s.$a->Numero,'','',0,[],'CADREF_ADMIN','icmn-user3');
		
		if(strpos($a->Mail, '@') > 0) {
			$s = self::MailCivility($a);
			$s .= $new ? "Votre espace CADREF vient d'être activé.<br /><br />" : "Votre mot de passe a été modifié.<br /><br />";
			$s .= "Vos paramètres de connection sont les suivants :<br /><br />";
			$s .= "Code utilisateur (N° adhérent) : <strong>$num</strong><br />Mot de Passe : <strong>$p</strong><br /><br />";
			if($confirm) {
				$s .= 'Avant de pouvoir vous inscrire à des cours ou à des visites guidées,<br />';
				$s .= 'vous devrez compléter les informations dans la rubrique "Info personnelles".<br /><br />';
			}
			$s .= self::MailSignature();
			$params = array('Subject'=>($new ? 'CADREF : Bienvenu dans votre nouvel espace utilisateur.' : 'CADREF : Nouveau mot de passe.'),
				'To'=>array($a->Mail),
				'Body'=>$s);
			self::SendMessage($params);
		}
		$msg = "Code utilisateur: $num\nMote de passe: $p\n";
		$params = array('Telephone1'=>$a->Telephone1,'Telephone2'=>$a->Telephone2,'Message'=>$msg);
		self::SendSms($params);
		return true;
	}
	
	public static function GeneratePassword() {	
		$lc = "abcdefghijklmnopqrstuvwxyz";
		$uc = strtoupper($lc);
		$dc = '0123456789';
		$sc = '$*+?-=';
		return str_shuffle(substr(str_shuffle($lc),0,6).substr(str_shuffle($uc),0,1).substr(str_shuffle($dc),0,1));
		//return str_shuffle(substr(str_shuffle($lc),0,3).substr(str_shuffle($uc),0,2).substr(str_shuffle($dc),0,2).substr(str_shuffle($sc),0,1));
	}

	public static function ChangePassword() {
		$data = array('success'=>0);
		$login = isset($_POST['CadrefLogin']) ? trim($_POST['CadrefLogin']) : '';
		$mail = isset($_POST['CadrefMail']) ? trim($_POST['CadrefMail']) : '';
		if(! $mail || !$login) {
			$data['message'] = 'Vous devez saisir votre code utilisateur et votre mot de passe.';
			return json_encode($data);			
		}
		
		$adh = Sys::getOneData('Cadref', "Adherent/Numero=$login&Mail=$mail");
		if(!count($adh)) {
			$usr = Sys::getOneData('Systeme', "User/Login=$login&Mail=$mail");
			if(!count($usr)) {
				$data['message'] = 'UTILISATEUR NON TROUVÉ.';
				return json_encode($data);							
			}
			$tel1 = $usr->Tel;
			$tel2 = '';
			$s = self::MailCivility($usr);
		}
		else {
			$usr = Sys::getOneData('Systeme', 'User/Login='.$adh->Numero);
			$tel1 = $adh->Telephone1;
			$tel2 = $adh->Telephone2;
			$s = self::MailCivility($adh);
		}
			
		$new = self::GeneratePassword();
		Sys::$User->Pass = '[md5]'.md5($new);
		Sys::$User->Save();

		$s .= "Votre nouveau mot de passe est : <strong>$new</strong><br /><br />";
		$s .= 'Vous pourrez le modifier dans la rubrique "Utilisateur".<br /><br />';
		$s .= self::MailSignature();
		$params = array('Subject'=>('CADREF : Changement de mot de passe.'),
			'To'=>array($mail),
			'Body'=>$s);
		self::SendMessage($params);

		$msg = "CADREF : Changement de mot de passe.\nMot de passe: $new\n";
		$params = array('Telephone1'=>$tel1,'Telephone2'=>$tel2,'Message'=>$msg);
		self::SendSms($params);
		
		$data['success'] = 1;
		$data['message'] = 'Votre nouveau mot de passe vous a été envoyé par email.';
		return json_encode($data);
	}

	
	public static function RegisterAdherent() {
		$data = array('success'=>0);
		$nom = isset($_POST['Nom']) ? trim($_POST['Nom']) : '';
		$pre = isset($_POST['Prenom']) ? trim($_POST['Prenom']) : '';
		$tel = isset($_POST['Telephone']) ? trim($_POST['Telephone']) : '';
		$mail = isset($_POST['Mail']) ? trim($_POST['Mail']) : '';
		$conf = isset($_POST['MailConfirm']) ? trim($_POST['MailConfirm']) : '';
		
		if(!$mail || !$conf || !$tel || !$nom || !$pre) {
			$data['message'] = 'Tous les champs sont obligatoires.';
			return json_encode($data);			
		}		
		if(! filter_var($mail, FILTER_VALIDATE_EMAIL)) {
			$data['message'] = "Le format de l'adresse mail est incorrect.";
			return json_encode($data);			
		}
		$adh = Sys::getOneData('Cadref', "Adherent/Mail=$mail");
		if(count($adh)) {
			$data['message'] = 'Il existe déjà un adhérent avec cette adresse mail.';
			return json_encode($data);			
		}
		if($mail != $conf) {
			$data['message'] = "L'adresse mail et la confirmation sont différentes.";
			return json_encode($data);			
		}
		$telr = preg_replace('/[^0-9]/', '([^0-9])*', $tel);
		$sql = "select Id from `##_Cadref-Adherent` where Telephone1 regexp '$telr' or Telephone2 regexp '$telr'";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if($pdo && $pdo->rowcount()) {
			$data['message'] = 'Il existe déjà un adhérent avec ce numéro de téléphone.';
			return json_encode($data);			
		}
		
		$nom = strtoupper($nom);
		$pre = strtoupper(substr($pre, 0, 1)).strtolower(substr($pre, 1));
		$adh = genericClass::createInstance('Cadref', 'Adherent');
		$adh->Nom = $nom;
		$adh->Prenom = $pre;
		$adh->Telephone1 = $tel;
		$adh->Mail = $mail;
		$adh->Web = 1;
		$adh->Save();
		AlertUser::addAlert('Adhérent : '.$pre.' '.$nom,"Nouvelle inscription web : ".$adh->Numero,'','',0,[],'CADREF_ADMIN','icmn-user3');
		
		$info = base64_encode($adh->Id.','.$mail.','.time());
		$s = "Bonjour $pre $nom,<br /><br /><br />";
		$s .= 'Appuyez sur le lien ci-dessous pour confirmer votre inscription :<br /><br />';
		$s .= "<strong><a href=\"https://gestion.cadref.com/Cadref/Adherent/confirmRegistration?info=$info\">Confirmer mon inscription</a></strong><br /><br />";
		$s .= "Ce lien sera actif pendant 48 heures.<br /><br />";
		$s .= self::MailSignature();
		$params = array('Subject'=>('CADREF : Confirmation d\'enregistrement.'),
			'To'=>array($mail),
			'Body'=>$s);
		self::SendMessage($params);

		$data['success'] = 1;
		$data['message'] = "Nous vous avons envoyé un email de confirmation.\nVeuillez l'ouvrir et cliquer sur le lien \"Confirmer mon inscription\".";
		return json_encode($data);		
	}
	
	public static function RegisterConfirmation() {
		$data = array('success'=>0,'message'=>"Une erreur c'est produite :\nLe lien est incorrect.");

		$info = isset($_GET['info']) ? trim($_GET['info']) : '';
		if($info == '') return json_encode($data);
		$info = explode(',', base64_decode($info));
		if(count($info) != 3) return json_encode($data);	
		if(($info[2]+2*86400) < time()) {
			$data['message'] = "Une erreur c'est produite :\nLe lien est expiré.";
			return json_encode($data);
		}
		$a = Sys::getOneData('Cadref', 'Adherent/'.$info[0]);
		if(!count($a) || $a->Mail != $info[1]) {
			$data['message'] = "Une erreur c'est produite :\nVeuillez contacter le CADREF au 04.66.36.99.44.";
			return json_encode($data);
		}

		$a->Web = 2;
		$a->Save();
		self::CreateUser($a->Numero, true);
		$data['success'] = 1;
		$data['message'] = 'Votre code utilisateur et votre mot de passe vous ont été envoyés par email.';
		return json_encode($data);
	}



	public static function GetStat() {
		$annee = self::$Annee;
		$data = array();
		$data['NbAdherents'] = Sys::getCount('Cadref', 'Adherent/Annee='.self::$Annee);
		$data['NbInscriptions'] = Sys::getCount('Cadref', 'Inscription/Annee='.self::$Annee.'&Attente=0&Supprime=0');
		$data['NbReservations'] = Sys::getCount('Cadref', 'Reservation/Annee='.self::$Annee.'&Attente=0&Supprime=0');
		$g = Sys::getOneData('Systeme', 'Group/Nom=CADREF_ADH');
		$u = $g->getChildren('User');
		$data['NbUsers'] = count($u);
		return $data;
	}

	public static function between($t, $start, $end) {
		return $start <= $t && $t <= $end;
	}

	public static function GetCalendar($args) {
		$args = json_decode(str_replace("\\", "", $args['args']));
		$start = strtotime(str_replace('T', ' ', $args->start));
		$end = strtotime(str_replace('T', ' ', $args->end));

		$annee = self::$Annee;
		$data = array();
		$events = array();
		$vacances = array();

		// vacances
		$sql = "select Type,Libelle,DateDebut,DateFin,JourId,Logo from `##_Cadref-Vacance` where Annee='$annee'";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		foreach($pdo as $p) {
			$t = $p['Type'];
			$d = $p['DateDebut'];
			$f = $p['DateFin'] ? $p['DateFin'] : $d;
			if($t == 'V') {
				$e = new stdClass();
				$e->title = $p['Libelle'] ?: 'VACANCES';
				$e->start = Date('Y-m-d', $d);
				$e->end = Date('Y-m-d', $f);
				$e->description = Date('d/m', $d).' au '.Date('d/m', $f);
				$e->className = 'fc-event-secondary'.($p['Logo'] ? ' cadref-cal-'.$p['Logo'] : '');
				$events[] = $e;
			}
			$v = new stdClass();
			$v->type = $t;
			$v->start = $d;
			$v->end = $f;
			$v->day = $p['JourId'];
			$vacances[] = $v;
		}

		$group = Sys::$User->getParents('Group')[0]->Nom;



		$adh = false;
		$adm = false;
		if($group == 'CADREF_ADMIN') {
			$adm = true;
			$sql1 = "
select a.DateDebut,a.DateFin,a.Description,e.Nom,e.Prenom,0 as cid,a.EnseignantId
from `##_Cadref-Absence` a
inner join `##_Cadref-Enseignant` e on e.Id=a.EnseignantId
where ((a.DateDebut>=$start and a.DateDebut<=$end) or (a.DateFin>=$start and a.DateFin<=$end))
";
		} else if($group == 'CADREF_ADH') {
			$adh = true;
			$n = Sys::$User->Login;
			$a = Sys::getOneData('Cadref', 'Adherent/Numero='.$n);
			$id = $a->Id;
			$sql = "
select i.ClasseId as cid,c.CodeClasse,c.JourId,c.HeureDebut,c.HeureFin,c.CycleDebut,c.CycleFin,
concat(ifnull(dw.Libelle,d.Libelle),' ',ifnull(n.Libelle,'')) as Libelle, l.Ville, l.Adresse1, l.Adresse2
from `##_Cadref-Inscription` i
inner join `##_Cadref-Classe` c on c.Id=i.ClasseId
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
left join `##_Cadref-WebDiscipline` dw on dw.Id=d.WebDisciplineId
left join `##_Cadref-Lieu` l on l.Id=c.LieuId
where i.AdherentId=$id and i.Annee='$annee' and c.JourId>0 and c.HeureDebut<>'' and c.Programmation=0
";
			$sql1 = "
select a.DateDebut,a.DateFin,a.Description,e.Nom,e.Prenom,i.ClasseId as cid,a.EnseignantId
from `##_Cadref-Inscription` i
left join `##_Cadref-ClasseEnseignants` ce on ce.Classe=i.ClasseId
left join `##_Cadref-Absence` a on a.EnseignantId=ce.EnseignantId
left join `##_Cadref-Enseignant` e on e.Id=ce.EnseignantId
where i.AdherentId=$id and i.Annee='$annee' and ((a.DateDebut>=$start and a.DateDebut<=$end) or (a.DateFin>=$start and a.DateFin<=$end))
";
			$sql2 = "
select cd.DateCours,i.ClasseId as cid,c.CodeClasse,c.JourId,c.HeureDebut,c.HeureFin,c.CycleDebut,c.CycleFin,
concat(ifnull(dw.Libelle,d.Libelle),' ',ifnull(n.Libelle,'')) as Libelle, l.Ville, l.Adresse1, l.Adresse2
from `##_Cadref-Inscription` i
inner join `##_Cadref-ClasseDate` cd on cd.ClasseId=i.ClasseId
inner join `##_Cadref-Classe` c on c.Id=i.ClasseId
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
left join `##_Cadref-WebDiscipline` dw on dw.Id=d.WebDisciplineId
left join `##_Cadref-Lieu` l on l.Id=c.LieuId
where i.AdherentId=$id and cd.DateCours>=$start and cd.DateCours<=$end
";
		} else if($group == 'CADREF_ENS') {
			$adh = false;
			$n = substr(Sys::$User->Login, 3, 3);
			$e = Sys::getOneData('Cadref', 'Enseignant/Code='.$n);
			$id = $e->Id;
			$sql = "
select c.Id as cid,c.CodeClasse,c.JourId,c.HeureDebut,c.HeureFin,c.CycleDebut,c.CycleFin,
concat(ifnull(dw.Libelle,d.Libelle),' ',ifnull(n.Libelle,'')) as Libelle, l.Ville, l.Adresse1, l.Adresse2
from `##_Cadref-ClasseEnseignants` ce
inner join `##_Cadref-Classe` c on c.Id=ce.Classe
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
left join `##_Cadref-WebDiscipline` dw on dw.Id=d.WebDisciplineId
left join `##_Cadref-Lieu` l on l.Id=c.LieuId
where ce.EnseignantId=$id and c.Annee='$annee' and c.JourId>0 and c.HeureDebut<>'' and c.Programmation=0
";
			$sql1 = "
select a.DateDebut,a.DateFin,a.Description,'','',0 as cid,a.EnseignantId
from `##_Cadref-Absence` a
where a.EnseignantId=$id and ((a.DateDebut>=$start and a.DateDebut<=$end) or (a.DateFin>=$start and a.DateFin<=$end))
";
			$sql2 = "			
select c.Id as cid,c.CodeClasse,c.JourId,c.HeureDebut,c.HeureFin,c.CycleDebut,c.CycleFin,
concat(ifnull(dw.Libelle,d.Libelle),' ',ifnull(n.Libelle,'')) as Libelle, l.Ville, l.Adresse1, l.Adresse2
from `##_Cadref-ClasseEnseignants` ce
inner join `##_Cadref-ClasseDate` cd on cd.ClasseId=ce.Classe
inner join `##_Cadref-Classe` c on c.Id=ce.Classe
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
left join `##_Cadref-WebDiscipline` dw on dw.Id=d.WebDisciplineId
left join `##_Cadref-Lieu` l on l.Id=c.LieuId
where ce.EnseignantId=$id and cd.DateCours>=$start and cd.DateCours<=$end
";
		}
		// absences
		$absences = array();
		$sql1 = str_replace('##_', MAIN_DB_PREFIX, $sql1);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql1);
		foreach($pdo as $p) {
			$d = $p['DateDebut'];
			$f = $p['DateFin'] ? $p['DateFin'] : $d;
			$a = new stdClass();
			$a->cid = $p['cid'];
			$a->nom = trim($p['Prenom'].' '.$p['Nom']) ?: 'ENSEIGNANT';
			$a->start = $d;
			$a->end = $f;
			$absences[] = $a;
			if(!$adh) {
				$a = new stdClass();
				$a->title = $adm ? trim($p['Prenom'].' '.$p['Nom']) : $p['Description'] ?: '';
				$a->start = Date('Y-m-d\TH:i', $d);
				$a->end = Date('Y-m-d\TH:i', $f);
				$a->description = Date('d/m H:i', $d).' au '.Date('d/m H:i', $f).'  '.($adm ? $p['Description'] : '');
				$a->className = 'fc-event-danger cadref-cal-absence';
				$events[] = $a;
			}
		}
		// cours
		if($group != 'CADREF_ADMIN') {
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			foreach($pdo as $p) {
				$cd = 0;
				$cy = $p['CycleDebut'];
				if($cy != '') {
					$m = substr($cy, 3, 2);
					$cd = strtotime(str_replace('/', '-', $cy).'-'.($m > 8 ? $annee : $annee + 1));
					$cy = $p['CycleFin'];
					$m = substr($cy, 3, 2);
					$cf = strtotime(str_replace('/', '-', $cy).'-'.($m > 8 ? $annee : $annee + 1));
					$cf += 86400 - 1;
				}
				$j = $p['JourId'] - 1;
				$d = $start + ($j * 24 * 60 * 60);
				while($d < $end) {
					//$ok = true;
					$ok = !($cd && ($d < $cd || $d > $cf));
					if($ok) {
						foreach($vacances as $v) {
							switch($v->type) {
								case 'D':
									$w = date('N', $d);
									$ok = !($v->day == $w && $d < $v->start);
									break;
								case 'F':
									$w = date('N', $d);
									$ok = !($v->day == $w && $d > $v->start);
									break;
								case 'V':
									$ok = !($d >= $v->start && $d <= $v->end);
									break;
							}
							if(!$ok) break;
						}
					}
					if($ok) {
						$events[] = self::calEvent($adh, $d, $p, $absences);
					}
					$d += 7 * 24 * 60 * 60;
				}
			}
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql2);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			foreach($pdo as $p) {
				$d = $p['DateCours'];
				$events[] = self::calEvent($adh, $d, $p, $absences);
			}
		}

		// visites
		if($group == 'CADREF_ENS')
				$sql = "
select v.Id,v.Libelle,v.DateVisite,0 as rid,v.Description,v.Prix,v.Assurance,v.Web,v.Places,v.Inscrits,v.Attentes
from `##_Cadref-Visite` v
inner join `##_Cadref-VisiteEnseignants` ve on ve.Visite=v.Id
where v.DateVisite>=$start and v.DateVisite<=$end and ve.EnseignantId=$id";
		else if($group == 'CADREF_ADH')
				$sql = "
select v.Id,v.Libelle,v.DateVisite,r.Id as rid,v.Description,v.Prix,v.Assurance,r.Supprime,r.Attente,v.Places,v.Inscrits,v.Attentes
from `##_Cadref-Visite` v
left join `##_Cadref-Reservation` r on r.AdherentId=$id and r.VisiteId=v.id
where v.DateVisite>=$start and v.DateVisite<=$end and v.Web=1";
		else
				$sql = "
select v.Id,v.Libelle,v.DateVisite,0 as rid,v.Description,v.Prix,v.Assurance,v.Web,v.Places,v.Inscrits,v.Attentes
from `##_Cadref-Visite` v
where v.DateVisite>=$start and v.DateVisite<=$end";

		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		foreach($pdo as $p) {
			if(!$args->visites) continue;  // && !$p['rid']
			$e = new stdClass();
			$e->title = $p['Libelle'];
			$e->start = Date('Y-m-d', $p['DateVisite']);
			$vid = $p['Id'];
			$sql = "
select e.Nom,e.Prenom
from `##_Cadref-VisiteEnseignants` ve
inner join `##_Cadref-Enseignant` e on e.Id=ve.EnseignantId
where ve.Visite=$vid
";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo1 = $GLOBALS['Systeme']->Db[0]->query($sql);
			$s = '';
			foreach($pdo1 as $p1) {
				$s .= (!$s ? 'Guide : ' : ', ').trim($p1['Prenom'].' '.$p1['Nom']);
			}
			if($s) $s .= "\n";
			$s .= $p['Description'] ?: '';
			if($s) $s .= "\n";
			$s .= 'Prix : <strong>€ '.$p['Prix'].'</strong>'; //.($p['Assurance'] ? ' Ass. facultative : € '.$p['Assurance'] : '');
			if($group == 'CADREF_ADH') {
				$s .= "\nPlaces disponibles : <strong>".($p['Places']<=$p['Inscrits'] ? '0' : $p['Places']-$p['Inscrits']).'</strong> / '.$p['Places'];
				if($p['rid']) {
					$s .= "\n<strong>";
					if($p['Supprime']) $s .= 'Réservation annulée';
					else if($p['Attente']) $s .= 'Réservation en attente';
					else $s .= 'Inscrit à la visite';
					$s .= '</strong>';
				}
			}
			else $s .= "\nPlaces : ".$p['Places'].' - '.$p['Inscrits'].' - '.$p['Attentes'];
			$e->description = $s;
			
			if($group == 'CADREF_ADH') {
				if($p['rid']) {
					if($p['Supprime']) $c = 'fc-event-danger';
					else if($p['Attente']) $c = 'fc-event-warning';
					else $c = 'fc-event-success';
				}
				else $c = 'fc-event-default';
			}
			else $c = $p['Web'] ? ($c = $p['Places']<=$p['Inscrits'] ? 'fc-event-danger' : 'fc-event-success') : 'fc-event-default';
			$e->className = $c.' cadref-cal-visite';
			$events[] = $e;
		}

		$data['events'] = $events;
		return $data;
	}
	
	private static function calEvent($adh, $d, $p, $absences) {
		$cid = $p['cid'];
		$e = new stdClass();
		$e->title = $p['Libelle'];
		$e->start = Date('Y-m-d', $d).'T'.$p['HeureDebut'];
		$e->end = Date('Y-m-d', $d).'T'.$p['HeureFin'];
		$e->className = 'fc-event-info';
		$e->description = $p['HeureDebut'].' à '.$p['HeureFin'].($p['CycleDebut'] ? '  du '.$p['CycleDebut'].' au '.$p['CycleFin'] : '');
		if($p['Ville']) {
			$l = $p['Ville'];
			if($p['Adresse1']) $l .= ', '.$p['Adresse1'];
			if($p['Adresse2']) $l .= "\n".$p['Adresse2'];
			$e->description .= "\n".$l;
		}
		$s = '';
		$sql = "
select e.Nom,e.Prenom,e.Id
from `##_Cadref-ClasseEnseignants` ce
inner join `##_Cadref-Enseignant` e on e.Id=ce.EnseignantId
where ce.Classe=$cid
";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo1 = $GLOBALS['Systeme']->Db[0]->query($sql);
		$s = '';
		foreach($pdo1 as $p1) {
			$s .= ($s ? "\n" : '').'Ens. : '.trim($p1['Prenom'].' '.$p1['Nom']);
			if($adh) {
				$eid = $p1['EnseignantId'];
				foreach($absences as $a) {
					if($a->cid == $cid && $a->eid == $eid) {
						$hd = strtotime($e->start);
						$hf = strtotime($e->end);
						if(self::between($hd, $a->start, $a->end) || self::between($hf, $a->start, $a->end)) {
							$e->className = 'fc-event-danger';
							$s .= " <span style=\"color:red\">(Absent)</span>";
							break;
						}
					}
				}
			}
		}
		if($s) $e->description .= "\n".$s;
		if(!$adh) $e->description .= "\n".$p['CodeClasse'];
		return $e;
	}
		
	public static function SendMessage($params) {
		$m = genericClass::createInstance('Systeme', 'MailQueue');
		$m->From = "contact@cadref.com";
		if(isset($params['To']))
			$m->To = implode(',', $params['To']);
		if(isset($params['Cc']))
			$m->Cc = implode(',', $params['Cc']);
		//if(isset($params['Bcc']))
			$m->Bcc = "contact@cadref.com"; //implode(',', $params['Bcc']);	
		$m->Subject = $params['Subject'];
		$m->Body = $params['Body'];
		if(isset($params['Attachments']))
			$m->Attachments = implode(',', $params['Attachments']);
		$p = self::GetParametre('MAIL', 'STANDARD', 'SIGNATURE');
		$m->EmbeddedImages = $p->Valeur; //"Skins/LoginCadref/Img/cadref_logo_bleu_100.png|cadref_logo";
		$m->Save();
	}
	
	public static function MailCivility($a) {
		$c = 'Bonjour ';
		if(is_object($a)) $c .= ($a->Sexe == "F" ? "Madame " : ($a->Sexe == "H" ? "Monsieur " : "")).trim($a->Prenom.' '.$a->Nom);
		elseif(is_array($a)) $c .= ($a['Sexe'] == "F" ? "Madame " : ($a['Sexe'] == "H" ? "Monsieur " : "")).trim($a['Prenom'].' '.$a['Nom']);
		return $c.",<br /><br /><br />";
	}

	public static function MailSignature() {
		$p = self::GetParametre('MAIL', 'STANDARD', 'SIGNATURE');
		return $p->Texte;
		self::$MailLogo = $p->Valeur;
//		$s = "<br /><br />A bientôt,<br />L'équipe du CADREF<br /><br />";
//		$s .= '<img alt="CADREF" src="cid:cadref_logo">';
//		return $s;
	}

    public static function SendSms($params) {
		$tel = preg_replace('/[^0-9]/', '', $params['Telephone1']);
		if(substr($tel, 0, 2) != '06' && substr($tel, 0, 2) != '07') {
			$tel = preg_replace('/[^0-9]/', '', $params['Telephone2']);
			if(substr($tel, 0, 2) != '06' && substr($tel, 0, 2) != '07')
				$tel = ''; 
		}
		if(strlen($tel) == 10) {
			require_once("Class/Lib/Isendpro/autoload.php");

			$api_instance = new Isendpro\Api\SmsApi();
			$smsrequest = new Isendpro\Model\SmsUniqueRequest();
			$smsrequest["keyid"] = SMS_API;
			$smsrequest["emetteur"] = 'CADREF';
			$smsrequest["num"] = $tel;
			$smsrequest["sms"] = $params['Message'];
			
			try {
				$api_instance->sendSms($smsrequest);
			} catch (Exception $e) {
				klog::l('CADREF ISendPro Exception :',print_r($e->getResponseObject(),1));
			}
		}

    }

	public static function SendMessageAdmin($params) {
		if(! MSG_ADMIN) return;
		$us = Sys::getData('Systeme', 'Group/Nom=CADREF_ADMIN/User');
		$to = array();
		foreach($us as $u)
			 $to[] = $u->Mail;
		$params['To'] = $to;
		self::SendMessage($params);
	}

	public static function SendSmsAdmin($params) {
		if(! MSG_ADMIN) return;
		$us = Sys::getData('Systeme', 'Group/Nom=CADREF_ADMIN/User');
		foreach($us as $u) {
			if($u->Tel) {
				$params['Telephone1'] = $u->Tel;
				$params['Telephone2'] = '';
				self::SendSms($params);
			}
		}
	}
	
	

}
