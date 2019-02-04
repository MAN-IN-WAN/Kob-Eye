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

	public static function CheckAdherent() {
		$data = array();
		$data['success'] = 0;
		$data['message'] = '';
		$data['controls'] = ['close'=>0, 'save'=>1, 'cancel'=>1];
		
		if($_POST['validForm'] == 2) {
			// create account + password + meail
			$data['message'] = 'Votre mot de passe vous a été envoyé par email.';
		}

		$num = isset($_POST['Numero']) ? trim($_POST['Numero']) : '';
		$nom = isset($_POST['Nom']) ? trim($_POST['Nom']) : '';
		$mail = isset($_POST['Mail']) ? trim($_POST['Mail']) : '';
		$tel = isset($_POST['Tel']) ? $_POST['Tel'] : '';
		if((empty($num) && empty($nom)) || (empty($mail) && empty($tel))) {
			$data['message'] = "Vous devez spécifier le numéro ou le nom<br /> ainsi que l'adresse mail ou le téléphone";
			return json_encode($data);
		}
		
		$telr = '';
		$nomr ='';
		if($num) $num = substr('000000', 0, 6 - strlen($num)).$num;
		if($tel) $telr = preg_replace('/[^0-9]/', '([^0-9])*', $tel);
		if($nom) $nomr = preg_replace('/([^A-Z]){1,}/', '([^A-N])*', $nom);

		if($num) $w .= "Numero='$num'";
		if($nomr) {
			if($w) $w .= " or ";
			$w .= "Nom regexp '$nomr'";
		}
		if($mail) $w1 .= "Mail='$mail'";
		if($telr) {
			if($w1) $w1 .= " or ";
			$w1 .= "Telephone1 regexp '$telr' or Telephone2 regexp '$telr'";
		}
		$sql = "select Numero,Nom,Prenom,CP,Mail,Telephone1,Telephone2 from `##_Cadref-Adherent` where ($w) and ($w1) limit 1";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if($pdo && $pdo->rowcount()) {
			foreach($pdo as $p) {
				$r = array();
				$r['Numero'] = $p['Numero'];
				$r['Nom'] = $p['Nom'];
				$r['Prenom'] = $p['Prenom'];
				$r['CP'] = '...'.substr($p['CP'], -2, 2);
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
				$data['data'] = $r;
				break;
			}
			$data['success'] = 1;
			$data['message'] = 'Si les informations suivantes vous correspondent, appuyez sur continuer pour recevoir votre mot de passe par email ou par SMS.';
		} else $data['message'] = 'Aucun adhérent ne correspond à ces critères.';

		$data['sql'] = $sql;
		$data["controls"] = ['close'=>0, 'save'=>1, 'cancel'=>1];
		return json_encode($data);
	}

	public static function GeneratePassword() {	
		$lc = "abcdefghijklmnopqrstuvwxyz";
		$uc = strtoupper($lc);
		$dc = '0123456789';
		$sc = '!$*+?';
		return str_shuffle(substr(str_shuffle($lc),0,3).substr(str_shuffle($uc),0,2).substr(str_shuffle($dc),0,2).substr(str_shuffle($sc),0,1));
	}

	
	private static function checkAdher($f0, $v0, $f1, $v1) {
		$qry = "Adherent/$f0=$v0&$f1=$v1";
		$adh = Sys::getOneData('Cadref', $qry);
		return $adh;
	}

	public static function GetStat() {
		$annee = Cadref::$Annee;
		$data = array();
		$data['NbAdherents'] = Sys::getCount('Cadref', 'Adherent/Annee='.Cadref::$Annee);
		$data['NbInscriptions'] = Sys::getCount('Cadref', 'Inscription/Annee='.Cadref::$Annee.'&Attente=0&Supprime=0');

		$sql = "
select a.Libelle,sum(if(t.Sexe='H',1,0)) as homme,sum(if(t.Sexe='F',1,0)) as femme,sum(if(t.Sexe<>'H' && t.Sexe<>'F',1,0)) as autre,count(*) as total
from (
select distinct h.Id,i.Antenne,h.Sexe
from `##_Cadref-Inscription` i 
inner join `##_Cadref-Adherent` h on h.Id=i.AdherentId
where i.Annee='$annee' and i.Supprime=0 and i.Attente=0
) t 
inner join `##_Cadref-Antenne` a on a.Antenne=t.Antenne
group by t.Antenne";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(!$pdo) return $sql;

		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		$l = array();
		$h = array();
		$f = array();
		$a = array();
		$t = array();
		foreach($rec as $r) {
			$l[] = $r['Libelle'];
			$h[] = $r['homme'];
			$f[] = $r['femme'];
			$a[] = $r['autre'];
			$t[] = $r['total'];
		}
		$bars = array();
		$bars['labels'] = $l;
		$bars['series'] = array($f, $h, $a);
		$data['bars'] = $bars;

		return $data;
	}

	static function between($t, $start, $end) {
		return $start <= $t && $t <= $end;
	}

	public static function GetCalendar($args) {
		$args = json_decode(str_replace("\\", "", $args['args']));
		$start = strtotime(str_replace('T', ' ', $args->start));
		$end = strtotime(str_replace('T', ' ', $args->end));

		$annee = Cadref::$Annee;
		$data = array();
		$events = array();
		$vacances = array();

		// vacances
		$sql = "select Type,Libelle,DateDebut,DateFin,JourId from `##_Cadref-Vacance` where Annee='$annee'";
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
				$e->className = 'fc-event-secondary';
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
concat(d.Libelle,' ',n.Libelle) as Libelle, l.Ville, l.Adresse1, l.Adresse2
from `##_Cadref-Inscription` i
inner join `##_Cadref-Classe` c on c.Id=i.ClasseId
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
left join `##_Cadref-Lieu` l on l.Id=c.LieuId
where i.AdherentId=$id and i.Annee='$annee' and c.JourId>0 and c.HeureDebut<>''
";
			$sql1 = "
select a.DateDebut,a.DateFin,a.Description,e.Nom,e.Prenom,i.ClasseId as cid,a.EnseignantId
from `##_Cadref-Inscription` i
left join `##_Cadref-ClasseEnseignants` ce on ce.Classe=i.ClasseId
left join `##_Cadref-Absence` a on a.EnseignantId=ce.EnseignantId
left join `##_Cadref-Enseignant` e on e.Id=ce.EnseignantId
where i.AdherentId=$id and i.Annee='$annee' and ((a.DateDebut>=$start and a.DateDebut<=$end) or (a.DateFin>=$start and a.DateFin<=$end))
";
		} else if($group == 'CADREF_ENS') {
			$adh = false;
			$n = substr(Sys::$User->Login, 3, 3);
			$e = Sys::getOneData('Cadref', 'Enseignant/Code='.$n);
			$id = $e->Id;
			$sql = "
select c.Id as cid,c.CodeClasse,c.JourId,c.HeureDebut,c.HeureFin,c.CycleDebut,c.CycleFin,
concat(d.Libelle,' ',n.Libelle) as Libelle, l.Ville, l.Adresse1, l.Adresse2
from `##_Cadref-ClasseEnseignants` ce
inner join `##_Cadref-Classe` c on c.Id=ce.Classe
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
left join `##_Cadref-Lieu` l on l.Id=c.LieuId
where ce.EnseignantId=$id and c.Annee='$annee' and c.JourId>0 and c.HeureDebut<>''
";
			$sql1 = "
select a.DateDebut,a.DateFin,a.Description,'','',0 as cid,a.EnseignantId
from `##_Cadref-Absence` a
where a.EnseignantId=$id and ((a.DateDebut>=$start and a.DateDebut<=$end) or (a.DateFin>=$start and a.DateFin<=$end))
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
				$a->className = 'fc-event-danger';
				$events[] = $a;
			}
		}
		// cours
		if($group != 'CADREF_ADMIN') {
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			foreach($pdo as $p) {
				$cid = $p['cid'];
				$cd = 0;
				$cy = $p['CycleDebut'];
				if($cy != '') {
					$m = substr($cy, 3, 2);
					$cd = strtotime(str_replace('/', '-', $cy).'-'.($m > 8 ? $annee : $annee + 1));
					$cy = $p['CycleFin'];
					$m = substr($cy, 3, 2);
					$cf = strtotime(str_replace('/', '-', $cy).'-'.($m > 8 ? $annee : $annee + 1));
					$cf += (24 * 60 * 60) - 1;
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
						$e = new stdClass();
						$e->title = $p['Libelle'];
						$e->start = Date('Y-m-d', $d).'T'.$p['HeureDebut'];
						$e->end = Date('Y-m-d', $d).'T'.$p['HeureFin'];
						$e->className = 'fc-event-info';
						$e->description = $p['HeureDebut'].' à '.$p['HeureFin'].($cd ? '  du '.$p['CycleDebut'].' au '.$p['CycleFin'] : '');
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
						$events[] = $e;
					}
					$d += 7 * 24 * 60 * 60;
				}
			}
		}

		// visites
		if($group == 'CADREF_ENS')
				$sql = "
select v.Id,v.Libelle,v.DateVisite,0 as rid,v.Description
from `##_Cadref-Visite` v
inner join `##_Cadref-VisiteEnseignants` ve on ve.Visite=v.Id
where v.DateVisite>=$start and v.DateVisite<=$end and ve.EnseignantId=$id";
		else if($group == 'CADREF_ADH')
				$sql = "
select v.Id,v.Libelle,v.DateVisite,r.Id as rid,v.Description
from `##_Cadref-Visite` v
left join `##_Cadref-Reservation` r on r.AdherentId=$id and r.VisiteId=v.id
where v.DateVisite>=$start and v.DateVisite<=$end";
		else
				$sql = "
select v.Id,v.Libelle,v.DateVisite,0 as rid,v.Description
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
				$s .= (!$s ? 'Animation : ' : ', ').trim($p1['Prenom'].' '.$p1['Nom']);
			}
			if($s) $s .= "\n";
			$s .= $p['Description'] ?: '';
			$e->description = $s;
			$e->className = (p['rid'] ? 'fc-event-success' : 'fc-event-default').' cadref-cal-visite';
			$events[] = $e;
		}


		$data['events'] = $events;
		$data['aa'] = $aa;

		return $data;
	}

	public static function SendMessage($params) {
		@include_once('Class/Lib/Mail.class.php');

		$Mail = new Mail();
		$Mail->Subject($params['Subject']);
		$Mail->From("noreply@cadref.com");
		$Mail->To($params['Mail']);
		$bloc = new Bloc();
		$bloc->setFromVar("Mail", $params['Body'], array("BEACON"=>"BLOC"));
		$Pr = new Process();
		$bloc->init($Pr);
		$bloc->generate($Pr);
		$Mail->Body($bloc->Affich());
		$Mail->Send();
		return true;
	}
	




}
