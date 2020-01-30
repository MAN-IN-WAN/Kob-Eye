<?php

class Adherent extends genericClass {
	
	function Save() {
		$id = $this->Id;
		$annee = Cadref::$Annee;

		if(!$id) {
			$a = Sys::getOneData('Cadref', 'Adherent', 0, 1, 'DESC', 'Numero');
			$this->Numero = sprintf('%06d', intval($a->Numero) + 1);
			$this->Inscription = $annee;
		}
		$this->Nom = strtoupper($this->Nom);
		$this->IBAN = strtoupper($this->IBAN);
		$this->BIC = strtoupper($this->BIC);
		$this->NomPrenom = $this->Nom.' '.$this->Prenom;
		$this->Utilisateur = Sys::$User->Initiales;
		$this->DateModification = time();
		return parent::Save();
		
		// valeur de AdherentAnnee sauvé par Adherent/Save.twig.php)
	}

	function GetFormInfo($annee) {
		$c = !$this->CheckCertificat();
		$s = $this->getOneChild('AdherentAnnee/Annee='.$annee);
		$cc = '';
		if($s->ClasseId) {
			$cl = Sys::getOneData('Cadref', 'Classe/'.$s->ClasseId);
			$cc = $cl->CodeClasse;
		}
		return array('Cotisation'=>$s->Cotisation, 'Cours'=>$s->Cours, 'Visites'=>$s->Visites, 'Reglement'=>$s->Reglement, 'Differe'=>$s->Differe,
			'Regularisation'=>$s->Regularisation, 'Solde'=>$s->Solde, 'NotesAnnuelles'=>$s->NotesAnnuelles, 'Adherent'=>$s->Adherent,
			'ClasseId'=>$s->ClasseId, 'AntenneId'=>$s->AntenneId, 'CotisationAnnuelle'=>Cadref::$Cotisation, 'certifInvalide'=>$c,
			'Soutien'=>$s->Soutien,'Dons'=>$s->Dons,'ClasseClasseIdlabel'=>$cc);
	}
	
	public function GetScanCount($annee) {
		
		if(Cadref::$UTL != 'CADREF') return array('scan'=>$n);
			
		$dd = strtotime('dmY','0108'.$annee);
		$df = strtotime('dmY','3107'.($annee+1));
		
		$num = $this->Numero;
		$data = array('sApiKey'=>'#ansicere68#', 'nId'=>$num);
		$ch = curl_init("https://scan.cadref.com/api/member/");
		curl_setopt_array($ch, array(
			CURLOPT_HTTPAUTH => CURLAUTH_ANY,
			CURLOPT_USERPWD => "intranet:#Yanolorp20",
		    CURLOPT_POST => TRUE,
		    CURLOPT_SSL_VERIFYHOST => FALSE,
		    CURLOPT_SSL_VERIFYPEER => FALSE,
		    CURLOPT_RETURNTRANSFER => TRUE,
		    CURLOPT_HTTPHEADER => array(
		        'Content-Type: application/json'
		    ),
		    CURLOPT_POSTFIELDS => json_encode($data)
		));
		$ret = curl_exec($ch);
		curl_close($ch);
		
		if($ret == '') return array('scan'=>0);
		$o = json_decode($ret);
		if($o->error == 'No result') return array('scan'=>0);
		$n = 0;
		if(isset($o->response)) {
			foreach($o->response->aResult as $r) {
				//if(substr($r->DTY_DT_CREATION,6,4) == $annee) $n++;
				$dc = strtotime('d/m/Y', $r->DTY_DT_CREATION);
				if($dc >= $dd && $dc <= $df) $n++;
			}
		}
		return array('scan'=>$n);
	}

	// $mode :
	// 0 => save adherent (appel par Adherent/Save.twig.php)
	// 1 => save inscription ou reservation	
	// 2 => save reglement (appel direct)
	// 3 => recalcul cumuls
	function SaveAnnee($data, $mode) {
		$annee = Cadref::$Annee;
		$cours = 0;
		$visit = 0;
		$regle = 0;
		$diffe = 0;
		$id = $this->Id;
		
		$sql = "select ifnull(sum(Prix-Reduction-Soutien),0) as total from `##_Cadref-Inscription` where AdherentId=$id and Annee='$annee' and Supprime=0 and Attente=0";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		foreach($pdo as $p) $cours = $p['total'];
		$sql = "select ifnull(sum(Prix-Reduction-Assurance),0) as total from `##_Cadref-Reservation` where AdherentId=$id and Annee='$annee' and Supprime=0 and Attente=0";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		foreach($pdo as $p) $visit = $p['total'];
		$sql = "select ifnull(sum(if(Differe=0 or Encaisse=1,Montant,0)),0) as total, ifnull(sum(if(Differe=1 and Encaisse=0,Montant,0)),0) as differe from `##_Cadref-Reglement` where AdherentId=$id and Annee='$annee' and Supprime=0";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		foreach($pdo as $p) {
			$regle = $p['total'];
			$diffe = $p['differe'];
		}
		
		$a = $this->getOneChild('AdherentAnnee/Annee='.$annee);
		if(!$a) {
			$a = genericClass::createInstance('Cadref', 'AdherentAnnee');
			$a->addParent($this);
			$a->Annee = $annee;
			$a->Numero = $this->Numero;
		}

		if($mode == 0) {
//			if($a->AntenneId != $data->AntenneId) {
//				$usr = Sys::getOneData('Systeme', 'User/Login='.$this->Numero);
//				if($usr) {
//					if(!$data->AntenneId) {
//						$grs = $usr->getParents('Group');
//						foreach($grs as $g) {
//							if($g->Nom == 'CADREF_SITE'); {
//								$usr->delParent($g);
//								break;
//							}
//						}
//					}
//					else {
//						$g = Sys::getOneData('Systeme', 'Group/Nom=CADREF_SITE');
//						$usr->addParent($g);
//					}
//					$usr->Save();
//				}
//			}
//			if($a->ClasseId != $data->ClasseId) {
//				$usr = Sys::getOneData('Systeme', 'User/Login='.$this->Numero);
//				if($usr) {
//					if(!$data->ClasseId) {
//						$grs = $usr->getParents('Group');
//						foreach($grs as $g) {
//							if($g->Nom == 'CADREF_DELEGUE'); {
//								$usr->delParent($g);
//								break;
//							}
//						}
//					}
//					else {
//						$g = Sys::getOneData('Systeme', 'Group/Nom=CADREF_DELEGUE');
//						$usr->addParent($g);
//					}
//					$usr->Save();
//				}
//			}
			$a->Adherent = $data->Adherent;
			$a->ClasseId = $data->ClasseId;
			$a->AntenneId = $data->AntenneId;
			$a->NotesAnnuelles = $data->NotesAnnuelles;
		}
		else if($mode == 1) {
			if(!$a->Cotisation && $data->Cotisation) $a->DateCotisation = time();
			$a->Cotisation = $data->Cotisation ? $data->Cotisation : 0;
			$this->Cotisation = $data->Cotisation;
			$this->Save();
		}
		if($mode == 0 || $mode == 1) {
			$a->Regularisation = $data->Regularisation ? $data->Regularisation : 0;
			$a->Dons = $data->Dons ? $data->Dons : 0;
		}
		$a->Cours = $cours;
		$a->Visites = $visit;
		$a->Reglement = $regle;
		$a->Differe = $diffe;
		$a->Solde = $a->Cotisation + $cours /*+ $visit*/ - $regle - $diffe + $a->Regularisation + $a->Dons;
		$a->Save();

		return true;
	}

	private function saveAnneeInscr($params) {
		$data = new stdClass();
		$inscr = $params['Inscr'];
		$data->Cotisation = $inscr['cotis'];
		$data->Regularisation = $inscr['regul'];
		$data->Dons = $inscr['dons'];
		$this->SaveAnnee($data, 1);
	}

	function Delete() {
		$rec = $this->getChildren('Reglement');
		if(count($rec)) {
			$this->addError(array("Message"=>"Cette fiche ne peut être supprimée", "Prop"=>""));
			return false;
		}
		$rec = $this->getChildren('AdherentAnnee');
		foreach($rec as $r)
			$r->Delete();
		$rec = $this->getChildren('Inscription');
		foreach($rec as $r)
			$r->Delete();
		$rec = $this->getChildren('Reservation');
		foreach($rec as $r)
			$r->Delete();
		
		$usr = Sys::getOneData('Systeme', 'User/Login='.$this->Numero);
		if($usr) $usr->Delete();

		return parent::Delete();
	}

	function EditInscriptions($params) {
		$annee = Cadref::$Annee;
		if(!isset($params['Numero'])) if(!isset($params['step'])) $params['step'] = 0;
		switch($params['step']) {
			case 0:
				return array(
					'step'=>1,
					'template'=>'editInscriptions',
					'callNext'=>array(
						'nom'=>'EditInscriptions',
						'title'=>'Réglement',
						'needConfirm'=>false
					)
				);
				break;
			case 1:
				unset($params['step']);
				if($params['Inscr']['solde'])
						return array(
						'step'=>2,
						'template'=>'editDiffere',
						'args'=>$params,
						'callNext'=>array(
							'nom'=>'EditInscriptions',
							'title'=>'Différé',
							'needConfirm'=>false
						)
					);
				$ret = $this->saveInscriptions($params, true);
				$this->saveAnneeInscr($params);
				if($ret) return array(
						'data'=>'Inscription enregistrée',
						'callBack'=>array(
							'nom'=>'refreshAdherent',
							'args'=>array(true)
						)
					);
				return false;
				break;
			case 2:
				$this->saveInscriptions($params, true);
				$this->saveDiffere($params);
				$this->saveAnneeInscr($params);
				return array(
					'data'=>'Inscription enregistrée',
					'callBack'=>array(
						'nom'=>'refreshAdherent',
						'args'=>array(true)
					)
				);
				break;
		}
	}
	
	function WebInscription($paiement) {
		$annee = Cadref::$Annee;

		$reg = genericClass::createInstance('Cadref', 'Reglement');
		$reg->addParent($paiement);
		$reg->addParent($this);
		$reg->Numero = $this->Numero;
		$reg->Annee = $annee;
		$reg->Montant = $paiement->Montant;
		$reg->DateReglement = time();
		$reg->ModeReglement = 'W';
		$reg->Note = $paiement->Reference;
		$reg->Encaisse = 1;
		$reg->Utilisateur = 'WEB';
		$reg->Web = 1;
		$reg->Save();
	
		$data = $this->GetPanier('inscribe', '');
		foreach($data['data'] as $ins) {
			if($ins['bloque']) continue;
			
			$attente = 0;
			$supprime = 0;

			$o = genericClass::createInstance('Cadref', 'Inscription');
			$cls = genericClass::createInstance('Cadref', 'Classe');
			$cls->initFromId($ins['clsId']);

			$o->addParent($this);
			$o->addParent($cls);
			$o->Annee = $annee;
			$o->Numero = $this->Numero;
			$o->CodeClasse = $ins['CodeClasse'];
			$o->Antenne = substr($ins['CodeClasse'], 0, 1);
			$o->Attente = 0;
			$o->Supprime = 0;
			$o->DateInscription = time();
			$o->DateAttente = 0;
			$o->DateSupprime = 0;
			$o->Prix = $ins['Prix'];
			$o->Reduction = $ins['Reduction'];
			$o->Soutien = 0;
			$o->Utilisateur = 'WEB';
			$o->Web = 1;
			$o->Save();

			$cls->Save();
		}
		// adherent
		$this->Annee = $annee;
		if($saveAdh) $this->Save();
		
		$insc = array('Inscr'=>array('cotis'=>$data['cotis'], 'regul'=>$data['regul'], 'dons'=>$data['dons']+$data['donate']));
		$this->saveAnneeInscr($insc);
		$this->Save();
		
		$pa = $this->getOneChild('Panier/Annee='.$annee);
		if($pa) $pa->Delete();
	}

	private function saveInscriptions($params, $saveAdh) {
		$annee = Cadref::$Annee;
		$inscr = $params['Inscr'];
		if(!$inscr['updated']) {
			// reglement differe
			if(!$inscr['paye'] && $inscr['solde']) return false;
			return true;
		}
		// inscriptions
		foreach($params['newInscr'] as $ins) {
			if(!$ins['updated']) continue;
			$id = $ins['id'];
			$attente = 0;
			$supprime = 0;

			$o = genericClass::createInstance('Cadref', 'Inscription');
			$cls = genericClass::createInstance('Cadref', 'Classe');
			$cls->initFromId($ins['ClasseClasseId']);

			if(!$id) {
				$o->addParent($this);
				$o->addParent($cls);
				$o->Annee = $annee;
				$o->Numero = $this->Numero;
				$o->CodeClasse = $ins['CodeClasse'];
				$o->Antenne = substr($ins['CodeClasse'], 0, 1);
			} else {
				$o->initFromId($id);
				$attente = $o->Attente;
				$supprime = $o->Supprime;
			}

			$o->Attente = $ins['Attente'];
			$o->Supprime = $ins['Supprime'];
			$o->DateInscription = $ins['DateInscription'];
			$o->DateAttente = $ins['DateAttente'];
			$o->DateSupprime = $ins['DateSupprime'];
			$o->Prix = $ins['Prix'];
			$o->Reduction = $ins['Reduction'];
			$o->Soutien = $ins['Soutien'];
			$o->Utilisateur = Sys::$User->Initiales;
			$o->Save();

			// classe : inscrits/attentes/suppmime
			if(!$id || $supprime != $o->Supprime || $attente != $o->Attente) {
				$cls->Save();
			}
		}

		// reglement
		if($inscr['paye']) {
			$r = genericClass::createInstance('Cadref', 'Reglement');
			$r->addParent($this);
			$r->Numero = $this->Numero;
			$r->Annee = $annee;
			$r->DateReglement = $inscr['date'];
			$r->Montant = $inscr['paye'];
			$r->ModeReglement = $inscr['mode'];
//			$r->Cotisation = $inscr['cotis'];
			$r->Notes = $inscr['note'];
			$r->Differe = 0;
			$r->Encaisse = 1;
			$r->Utilisateur = Sys::$User->Initiales;
			$r->Save(true);
		}

		// adherent
		$this->Annee = $annee;
		if($saveAdh) $this->Save();

		return true;
	}

	private function saveDiffere($params) {
		$annee = Cadref::$Annee;
		$reg = $params['Diff']['regl'];
		foreach($reg as $r) {
			if(!isset($r['updated']) || !$r['updated']) continue;

			$o = genericClass::createInstance('Cadref', 'Reglement');
			if($r['id']) {
				$o->initFromId($r['id']);
				if(!$r['paye']) {
					$o->Delete();
					continue;
				}
			}
			else {
				if(!$r['paye']) continue;
				$o->addParent($this);
			}

			$m = $r['mois'];
			$d = ($m >= 9 ? $annee : ($annee + 1)).(strlen($m) == 1 ? '0' : '').$m;
			$j = Cadref::GetParametre('REGLEMENT', 'DIFFERE', $d);
			$d = ($j ? $j->Valeur.'/' : '15/').(strlen($m) == 1 ? '0' : '').$m.'/'.($m >= 9 ? $annee : ($annee + 1));
			$o->Numero = $this->Numero;
			$o->Annee = $annee;
			$o->DateReglement = $d;
			$o->Montant = $r['paye'];
			$o->ModeReglement = $r['mode'];
//			$o->Cotisation = 0;
			$o->Notes = $r['note'];
			$o->Differe = 1;
			$o->Encaisse = 0;
			$o->Utilisateur = Sys::$User->Initiales;
			$o->Save(true);
		}
		$this->Save();
		return true;
	}

	function CheckCertificat() {
		$annee = Cadref::$Annee;
		$min = ($annee).'0630';
		$dat = $this->DateCertificat;
		if($dat > 0 && date('Ymd', $dat) > $min) return true;

		$cert = false;
		$ins = $this->getChildren('Inscription/Annee='.$annee);
		foreach($ins as $i) {
			if($i->Supprime || $i->Attente) continue;
			$c = $i->getOneParent('Classe');
			$d = $c->getOneParent('Discipline');
			if($d->Certificat) {
				$cert = true;
				break;
			}
		}
		if($cert && ($dat <= 0 || date('Ymd', $dat) < $min)) return false;
		return true;
	}
	
	function PrintCarte($recto = false) {
		require_once ('PrintCarte.class.php');

		$annee = Cadref::$Annee;
		$aan = $this->getOneChild('AdherentAnnee/Annee='.$annee);
		$ins = $this->getChildren('Inscription/Annee='.$annee.'&Supprime=0&Attente=0');
		if(!$aan || (!$aan->Cotisation && !count($ins))) return array('pdf'=>false);

		$pdf = new PrintCarte($this, $aan, $recto);
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle('Carte'.$this->Numero);

		$pdf->AddPage();
		$pdf->PrintLines($ins);

		$file = 'Home/tmp/Carte'.$this->Numero.'_'.date('YmdHis').'.pdf';
		$pdf->Output(getcwd().'/'.$file);
		$pdf->Close();
		
		$p = Cadref::GetParametre('IMPRIMANTE', 'CARTE', Sys::$User->Login);
		if($p && $p->Valeur) {
			$s = "lp -d ".$p->Valeur." $file";
			shell_exec($s);
			return array('pdf'=>false);
		}

		return array('pdf'=>$file);
	}

	function PrintSiteAdherents() {
		$annee = Cadref::$Annee;
		$aa = $this->getOneChild("AdherentAnnee/Annee=$annee");
		if(! $aa->AntenneId) return array('pdf'=>false);
		
		$obj = array('CurrentUrl'=>'impressionslisteadherents', 'Contenu'=>'N', 'Rupture'=>'C', 'Antenne'=>$aa->AntenneId, 'Annee'=>$annee, 'Pages'=>true);
		return $this->PrintAdherent($obj);
	}

	function PrintDelegueAdherents() {
		$annee = Cadref::$Annee;
		$aa = $this->getOneChild("AdherentAnnee/Annee=$annee");
		if(! $aa->ClasseId) return array('pdf'=>false);
		$cl = Sys::getOneData('Cadref', 'Classe/'.$aa->ClasseId);
		
		$obj = array('CurrentUrl'=>'impressionslisteadherents', 'Contenu'=>'N', 'Rupture'=>'C', 'Disc'=>array($cl->CodeClasse), 'Annee'=>$annee, 'Pages'=>true);
		return $this->PrintAdherent($obj);
	}


	
	function PrintAdherentSession($obj) {
		if(isset($_SESSION['PrintAdherent'])) $obj = $_SESSION['PrintAdherent'];
		else $obj = false;
		return $obj;
	}
	
	function PrintAdherent($obj) {
		//$menus = ['impressionslisteadherents', 'impressionscertificatesmedicaux', 'impressionsfichesincompletes'];
		//$mode = array_search($obj['CurrentUrl'], $menus);
		$mode = $obj['type'];

		$annee = $obj['Annee'];
		if(empty($annee)) $annee = Cadref::$Annee;
		$sql = '';
		$whr = '';

		// selection selon de mode
		switch($mode) {
			case 0: // liste edherents
				$_SESSION['PrintAdherent'] = $obj;

				$file = 'ListeAdherent';
				$typAdh = isset($obj['TypeAdherent']) ? $obj['TypeAdherent'] : '';
				$contenu = isset($obj['Contenu']) ? $obj['Contenu'] : '';
				$rupture = isset($obj['Rupture']) ? $obj['Rupture'] : '';
				$enseignant = isset($obj['Enseignant']) ? $obj['Enseignant'] : '';
				$visite = isset($obj['Visite']) ? $obj['Visite'] : '';
				$visiteAnnee = isset($obj['VisiteAnnee']) ? $obj['VisiteAnnee'] : '';
				$nonInscrit = isset($obj['NonInscrit']) ? $obj['NonInscrit'] : '';
				$soutien = isset($obj['Soutien']) ? $obj['Soutien'] : '';
				$pages = isset($obj['Pages']) ? $obj['Pages'] : '';
				$antenne = isset($obj['Antenne']) ? $obj['Antenne'] : '';
				$adherent = false;
				
				$noRupture =  $contenu == 'Q' || $rupture == 'S';
				$noClasse = $typAdh != '' || $visite != '' || $visiteAnnee || $nonInscrit;
				
				
/*
 select i.Antenne,s.Libelle,count(*)
from `kob-Cadref-Inscription` i
inner join `kob-Cadref-Classe` c on c.Id=i.ClasseId
inner join `kob-Cadref-Niveau` n on n.Id=c.NiveauId
inner join `kob-Cadref-Discipline` d0 on d0.Id=n.DisciplineId
inner join `kob-Cadref-WebDiscipline` d on d.Id=d0.WebDisciplineId
inner join `kob-Cadref-WebSection` s on s.Id=d.WebSectionId
where i.Numero in (
select Numero
from `kob-Cadref-Inscription`
where Soutien>0 and Supprime=0 and Attente=0 and Annee='2019'

)
and i.Annee='2019' and i.Supprime=0 and i.Attente=0
group by i.Antenne,s.Libelle
 */

				if($soutien) {
					require_once ('PrintSoutien.class.php');
					
					$sql = "
select count(*) as cnt, sum(i.Soutien) as soutien, a.Libelle
from `##_Cadref-Inscription` i
inner join `##_Cadref-Antenne` a on a.Antenne=i.Antenne
where i.Annee='$annee' and i.Soutien>0
group by i.Antenne
";
					$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
					$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
					if(! $pdo) return array('pdf'=>'', 'sql'=>$sql);

					$pdf = new PrintSoutien($annee);
					$pdf->SetAuthor("Cadref");
					$pdf->SetTitle(iconv('UTF-8','ISO-8859-15//TRANSLIT',"Liste des soutiens"));

					$pdf->AddPage();
					$pdf->PrintLines($pdo);

					$file = '/Home/tmp/Soutien_'.date('YmdHis').'.pdf';
					$pdf->Output(getcwd().$file);
					$pdf->Close();

					return array('pdf'=>$file, 'sql'=>$sql);
				}

				if($noClasse) $antenne = '';
				if($noClasse || $noRupture) {
					$sql = "select distinct ";
					$adherent = true;
					$rupture = 'S';
				}
				else $sql = "select i.CodeClasse, i.ClasseId, n.AntenneId, i.Attente, i.DateAttente, d.Libelle as LibelleD, n.Libelle as LibelleN, ";

				$sql .= "e.Sexe, e.Numero, e.Nom, e.Prenom, e.Adresse1, e.Adresse2, e.CP, e.Ville, e.Telephone1, e.Telephone2, e.Mail,";
				$sql .= $noClasse  ? "'' as Delegue " : "c0.CodeClasse as Delegue ";

				if($typAdh == 'S') {
					// adhérents sans inscription
					$sql .= "from `##_Cadref-Adherent` e left join `##_Cadref-Adherent` aa on aa.AdherentId=e.Id ad aa.Annee='$annee' ";
					$whr = "and aa.Cotisation>0 and aa.Reglement=aa.Cotisation and aa.Differe=0 and aa.Cours=0 ";
				}
				else if($typAdh != '') {
					$sql .= "
from `##_Cadref-Adherent` e
inner join `##_Cadref-AdherentAnnee` aa on aa.AdherentId=e.Id and aa.Annee='$annee'
left join `##_Cadref-Classe` c0 on c0.Id=aa.ClasseId 
left join `##_Cadref-Niveau` n on n.Id=c0.NiveauId ";
					switch($typAdh) {
						case 'B': $whr .= "and aa.Adherent='B' ";
							break;
						case 'A': $whr .= "and aa.Adherent in ('B','A') ";
							break;
						case 'D': 
							$whr .= "and aa.ClasseId<>0 ";
							if($antenne != '') $whr .= "and n.AntenneId=$antenne ";
							break;
					}
				}
				elseif($visite != '') {
					$sql .= "
from `##_Cadref-Adherent` e
inner join `##_Cadref-Reservation` r on r.AdherentId=e.Id and r.VisiteId=$visite 
left join `##_Cadref-Classe` c0 on c0.Id=e.ClasseId ";
					$rupture = 'S';
					//$contenu = 'A';
				}
				elseif($visiteAnnee != '') {
					$sql .= "
from `##_Cadref-Adherent` e
inner join `##_Cadref-Reservation` r on r.AdherentId=e.Id and r.Annee='$visiteAnnee' 
left join `##_Cadref-Classe` c0 on c0.Id=e.ClasseId ";
					$rupture = 'S';
					//$contenu = 'A';
				}
				elseif($nonInscrit) {
					$last = $annee-4;
					$sql .= "from `##_Cadref-Adherent` e ";
					$whr = "and e.Inscription<'$annee' and e.Inscription>='$last' and Inactif=0 ";
				}
				else {
					// adhérents inscrits
					$sql .= "
from `##_Cadref-Inscription` i
inner join `##_Cadref-Classe` c on c.Id=i.ClasseId
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
inner join `##_Cadref-Adherent` e on e.Id=i.AdherentId
left join `##_Cadref-AdherentAnnee` aa on aa.AdherentId=e.Id and aa.Annee='$annee'
left join `##_Cadref-Classe` c0 on c0.Id=aa.ClasseId ";
					if($enseignant) {
						$sql .= "inner join `##_Cadref-ClasseEnseignants` ce on ce.Classe=i.ClasseId ";
						$whr .= "and ce.EnseignantId=$enseignant ";
					}
				}

				$mail = (isset($obj['Mail']) && $obj['Mail'] != '') ? $obj['Mail'] : '';
				if($mail == 'A') $whr .= "and e.Mail<>'' ";
				elseif($mail == 'S') $whr .= "and e.Mail='' ";

				if(! $noClasse) {
					$whr .= "and i.Annee='$annee' and i.Supprime=0 ";

//					// type adherent
//					if($typAdh != '') {
//						$whr .= "and aa.Adherent in (";
//						switch($typAdh) {
//							case 'B': $whr .= "'B') ";
//								break;
//							case 'A': $whr .= "'B','A') ";
//								break;
//							case 'D': $whr .= "'B','A','D') ";
//								break;
//						}
//					}

					if(isset($obj['Nouveaux']) && $obj['Nouveaux']) $whr .= "and e.Inscription='$annee' ";

//					$antenne = (isset($obj['Antenne']) && $obj['Antenne'] != '') ? $obj['Antenne'] : '';
					if($antenne != '') $whr .= "and n.AntenneId='$antenne' ";

					$inscrits = (isset($obj['Inscrits']) && $obj['Inscrits'] != '') ? $obj['Inscrits'] : '';
					if($inscrits == 'I') $whr .= "and i.Attente=0 ";
					elseif($inscrits == 'A') $whr .= "and i.Attente<>0 ";

					$lieu = (isset($obj['Lieu']) && $obj['Lieu'] != '') ? $obj['Lieu'] : '';
					if($lieu != '') $whr .= "and c.Lieu='$lieu' ";

					//disciplines exclues
					if(isset($obj['Excl']) && isset($obj['Disc'])) {
						$disc = $obj['Disc'];
						$excl = $obj['Excl'];
						for($i = 0; $i < 5; $i++) {
							if(isset($excl[$i]) && $excl[$i] && isset($disc[$i]) && strlen($disc[$i]) == 11) {
								$whr .= "and i.CodeClasse<>'".$disc[$i]."' ";
							}
						}
					}
					//disciplines incluses
					if(isset($obj['Disc'])) {
						$w = '';
						$disc = $obj['Disc'];
						$excl = isset($obj['Excl']) ? $obj['Excl'] : array();
						for($i = 0; $i < 5; $i++) {
							if(isset($disc[$i]) && $disc[$i] != '' && (!isset($excl[$i]) || !$excl[$i])) {
								$w .= $w == '' ? "and (" : "or ";
								$w .= "i.CodeClasse like '".$disc[$i]."%' ";
							}
						}
						if($w != '') $whr .= $w.") ";
					}
				}

				//requete sql
				if($whr != '') $sql .= "where ".substr($whr, 4);
				if($adherent) {
					if($contenu == 'Q') $sql .= "order by e.Nom, e.Prenom ";  // e.CP, e.Ville, 
					else $sql .= "order by e.Nom, e.Prenom ";
				}
				else {
					if(isset($obj['OrdreAtt']) && $obj['OrdreAtt']) $sql .= "order by i.DateAttente ";
					else $sql .= "order by i.CodeClasse, e.Nom, e.Prenom ";
				}
				break;
				
			case 1: // certificats medicaux
				$file = 'ListeCertificat';
				$contenu = 'N';
				$rupture = 'E'; // enseignant
				$antenne = 0;
				$sql = "
select distinct a.Sexe, a.Numero, a.Nom, a.Prenom, a.Telephone1, a.Telephone2, a.Mail,
a.DateCertificat, i.CodeClasse, i.ClasseId, d.Libelle as LibelleD, n.Libelle as LibelleN
from `##_Cadref-Adherent` a
inner join `##_Cadref-Inscription` i on i.AdherentId=a.Id and i.Annee='$annee'
inner join `##_Cadref-Classe` c on c.Id=i.ClasseId
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
left join `##_Cadref-ClasseEnseignants` ce on ce.Classe=c.Id
left join `##_Cadref-Enseignant` e on e.Id=ce.EnseignantId
where a.Annee='$annee' and i.Supprime=0 and i.Attente=0 and d.Certificat<>0 
and (a.DateCertificat is null or a.DateCertificat<unix_timestamp('$annee-07-01')) ";
				if($obj['mode'] == 'print' && isset($obj['NoMail']) && $obj['NoMail'])
					$sql .= " and a.Mail not like '%@%' ";
				if($obj['mode'] == 'mail')
					$sql .= " and a.Mail like '%@%' ";

				$sql .= "order by e.Nom,i.CodeClasse,a.Nom,a.Prenom";
				break;
				
			case 2: // fiches incomplètes
				$file = 'ListeIncomplet';
				$contenu = 'N';
				$rupture = 'S';
				$antenne = 0;
				$sql = "
select a.Sexe, a.Numero, a.Nom, a.Prenom, a.Telephone1, a.Telephone2, a.Mail
from `##_Cadref-Adherent` a
where a.Annee='$annee' and (a.Origine='' or a.SituationId='' or a.ProfessionId='' or a.Sexe='' or a.Naissance='')
order by a.Nom, a.Prenom";
				break;
		}


		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(!$pdo) return array('success'=>false, 'sql'=>$sql);;

		
		if($obj['mode'] == 'mail') {

			switch($mode) {
				case 0:
					$subj = $obj['Sujet'];
					$body = $obj['Corps'];
					$att = $obj['Pieces']['data'];
					break;
				case 1:
					$subj = Cadref::$UTL.' : Certificat médical';
					$body = "À ce jour nous ne sommes toujours pas en possession de votre certificat médical.<br /><br />";
					$body .= "Merci de bien vouloir nous renvoyer l’attestation sur l’honeur ci-jointe.";
					break;
				case 2:
					$subj = '';
					$body = '';
					$att = array();
					break;
			}
			$body .= Cadref::MailSignature();

			foreach($pdo as $a) {
				if(strpos($a['Mail'], '@') === false) continue;
					
				if($mode == 1) {
					$att = array($this->imprimeCertificat(array($a)));
				}
				$args = array('Subject'=>$subj, 'To'=>array($a['Mail']), 'Body'=>Cadref::MailCivility($a).$body, 'Attachments'=>$att);
				Cadref::SendMessage($args);				
			}
			$args = array('Subject'=>$subj, 'To'=>array(Cadref::$MAIL), 'Body'=>$body, 'Attachments'=>$att);
			Cadref::SendMessage($args);				
			return true;
		}
		if($obj['mode'] == 'sms') {
			foreach($pdo as $a) {
				$params = array('Telephone1'=>$a['Telephone1'],'Telephone2'=>$a['Telephone2'],'Message'=>$obj['SMS']);
				Cadref::SendSms($params);
			}
			return true;
		}
		if($obj['mode'] == 'export') {
			$file = 'Home/tmp/ListeAdherent_'.date('YmdHis').'.csv';
			$f = fopen($file, 'w');
			$s = '"Numéro";"Nom";"Prénom";"Adresse1";"Adresse2";"CP";"Ville";"Téléphone1";"Téléphone2";"Mail";"Délégué"';
			if($obj['Rupture'] != 'S') $s .= ';"Classe";"Discipline";"Niveau";"Attente";"Date attente"';
			$s .= "\n";
			fwrite($f, $s);
			foreach($pdo as $a) {
				$s = '"'.$a['Numero'].'";';
				$s .= '"'.$a['Nom'].'";';
				$s .= '"'.$a['Prenom'].'";';
				$s .= $this->dblCotes(['Adresse1']).';';
				$s .= $this->dblCotes($a['Adresse2']).';';
				$s .= '"'.$a['CP'].'";';
				$s .= '"'.$a['Ville'].'";';
				$s .= '"'.$a['Telephone1'].'";';
				$s .= '"'.$a['Telephone2'].'";';
				$s .= '"'.$a['Mail'].'";';
				$s .= '"'.$a['Delegue'].'"';
				if($obj['Rupture'] != 'S') {
					$s .= ';';
					$s .= '"'.$a['CodeClasse'].'";';
					$s .= $this->dblCotes($a['LibelleD'].' '.$a['LibelleN']).';';
					$s .= '"'.($a['Attente'] ? 'O' : 'N').'";';
					$s .= '"'.($a['Attente'] ? date('d/m/Y H:i',$a['DateAttente']) : '').'"';
				}
				$s .= "\n";
				fwrite($f, $s);
			}
			fclose($f);
			return array('csv'=>$file, 'sql'=>$sql);
		}
		if($contenu != 'Q') {
			require_once ('PrintAdherent.class.php');

			$pdf = new PrintAdherent($mode, $contenu, $rupture, $antenne, $nonInscrit ? 'N' : $inscrits, $typAdh, $pages);
			$pdf->SetAuthor("Cadref");
			$pdf->SetTitle('Liste adherents');

			$pdf->AddPage();
			$pdf->PrintLines($pdo);

			$file = 'Home/tmp/'.$file.'_'.date('YmdHis').'.pdf';
			$pdf->Output(getcwd().'/'.$file);
			$pdf->Close();
		} else {
			require_once ('PrintLabels.class.php');
			$pdf = new PrintLabels();
			$pdf->SetAuthor("Cadref");
			$pdf->SetTitle('Etiquettes adherents');

			$pdf->AddPage();
			foreach($pdo as $l) {
				$pdf->AddLabel($l);
			}

			$file = 'Home/tmp/EtiquetteAdherent_'.date('YmdHis').'.pdf';
			$pdf->Output(getcwd().'/'.$file);
			$pdf->Close();
		}

		return array('pdf'=>$file, 'sql'=>$sql);
	}
	
	private function dblCotes($s) {
		return '"'.iconv('UTF-8','ISO-8859-15//TRANSLIT',str_replace('"', "\"", $s)).'"';
	}


	function PrintCertificat($params) {
		$mode = isset($params['mode']) ? $params['mode'] : 'print';
		$annee = Cadref::$Annee;
		$sql = "
select distinct a.Id, a.Sexe, a.Numero, a.Nom, a.Prenom, a.Mail
from `##_Cadref-Adherent` a
inner join `##_Cadref-Inscription` i on i.AdherentId=a.Id and i.Annee='$annee'
inner join `##_Cadref-Classe` c on c.Id=i.ClasseId
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
left join `##_Cadref-ClasseEnseignants` ce on ce.Classe=c.Id
left join `##_Cadref-Enseignant` e on e.Id=ce.EnseignantId";
		$where = "a.Annee='$annee' and i.Supprime=0 and i.Attente=0 and d.Certificat<>0 
and (a.DateCertificat is null or a.DateCertificat<unix_timestamp('$annee-07-01')) ";
		if($params['mode'] == 'print' && isset($params['NoMail']) && $params['NoMail'])
			$sql .= " and a.Mail not like '%@%' ";
		if($params['mode'] == 'mail')
			$sql .= " and a.Mail like '%@%' ";
		$antenne = isset($params['Antenne']) ? $params['Antenne'] : '';
		if($antenne) $sql .= " and n.AntenneId=$antenne";

		$id = $this->Id;
		if(!$id) {
			if($mode == 'mail' && (!isset($params['ExecTask']) || !$params['ExecTask'])) {
				$t = genericClass::createInstance('Systeme', 'Tache');
				$t->Nom = 'PrintCertificat';
				$t->Type = 'Fonction';
				$t->TaskType = '';
				$t->TaskModule = 'Cadref';
				$t->TaskObject = 'Adherent';
				$t->TaskFunction = 'TacheAdherent';
				$t->TaskArgs = serialize($params);
				$t->Save();
				return array('message'=>'Tache lancée en arrière plan.');
			}
			
			$sql .= " where $where order by a.Nom,a.Prenom";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			if(!$pdo) return false;

			if($mode == 'mail') {
				$sub = Cadref::$UTL." : Certificat médical";
				$bod = "À ce jour nous ne sommes toujours pas en possession de votre certificat médical.<br /><br />";
				$bod .= "Merci de bien vouloir nous renvoyer l’attestation sur l’honeur ci-jointe.";
				$bod .= Cadref::MailSignature();
				foreach($pdo as $p) {
					$file = $this->imprimeCertificat(array($p), $p['Numero']);
					$b = Cadref::MailCivility($p).$bod;
					$args = array('To'=>array($p['Mail']), 'Subject'=>$sub, 'Body'=>$b, 'Attachments'=>array($file));
					if(MSG_ADH) Cadref::SendMessage($args);
				}
				return array('message'=>$pdo->rowCount().' mails envoyés.');
			}
			else {
				$file = $this->imprimeCertificat($pdo, '');
				return array('pdf'=>$file);
			}
		}
		else {
			$sql .= " where a.Id=$id and $where";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			if(!$pdo) return array('sql'=>$sql);

			if(!$pdo->rowCount()) return array(
				'step'=>2,
				'data'=>'Pas de certificat demandé.'
			);

			$file = $this->imprimeCertificat($pdo, $mode);
			return array('pdf'=>$file);
		}
	}
	
	private function imprimeCertificat($list, $num) {
		require_once ('PrintCertificat.class.php');

		$pdf = new PrintCertificat();
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle('Certificat medical');

		foreach($list as $l)
			$pdf->PrintPage($l);

		$file = 'Home/tmp/Certificat'.$num.'_'.date('YmdHis').'.pdf';
		$pdf->Output(getcwd().'/'.$file);
		$pdf->Close();

		return $file;
	}

	
	public static function TacheAdherent($tache) {
		$args = unserialize($tache->TaskArgs);
		$args['ExecTask'] = 1;
		$adh = genericClass::createInstance('Cadref', 'Adherent');
		switch($tache->Nom) {
			case 'PrintAttestation': return $adh->PrintAttestation($args);
			case 'PrintCertificat': return $adh->PrintCertificat($args);
		}
		return false;
	}

	function PrintAttestationPublic($params) {
		$suivi = $params['AttestSuivi'] ? 1 : ($params['AttestPaiement'] ? 2 : 0);
		$annee = $params['AttestAnnee'];

		$id = $this->Id;
		$an = $this->getOneChild('AdherentAnnee/Annee='.$annee);
		$fisc = date('Y', $an->DateCotisation);
		$sql = "
select distinct h.Id,h.Sexe,h.Mail,h.Numero,h.Nom,h.Prenom,h.Adresse1,h.Adresse2,h.CP,h.Ville,a.Cotisation,a.Dons,a.Reglement,a.Differe
from `##_Cadref-AdherentAnnee` a
inner join `##_Cadref-Adherent` h on h.Id=a.AdherentId
where a.AdherentId=$id and a.Annee='$annee'
";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if($suivi) $file = $this->imprimeSuivi($suivi, $pdo, $annee);
		else $file = $this->imprimeAttestation($pdo, $annee, $fisc, $this->Numero);
		return array('pdf'=>$file, 'sql'=>$sql);
	}

	function PrintAttestation($params) {
		$sql = "
select distinct h.Id,h.Sexe,h.Mail,h.Numero,h.Nom,h.Prenom,h.Adresse1,h.Adresse2,h.CP,h.Ville,a.Cotisation,a.Dons,a.Reglement,a.Differe
from `##_Cadref-AdherentAnnee` a
inner join `##_Cadref-Adherent` h on h.Id=a.AdherentId
";

		$id = $this->Id;
		if(!$id) {
			$mode = isset($params['mode']) ? $params['mode'] : 'print';
			if($mode == 'mail' && (!isset($params['ExecTask']) || !$params['ExecTask'])) {
				$t = genericClass::createInstance('Systeme', 'Tache');
				$t->Nom = 'PrintAttestation';
				$t->Type = 'Fonction';
				$t->TaskType = '';
				$t->TaskModule = 'Cadref';
				$t->TaskObject = 'Adherent';
				$t->TaskFunction = 'TacheAdherent';
				$t->TaskArgs = serialize($params);
				$t->Save();
				return array('message'=>'Tache lancée en arrière plan.');
			}
			
			$type = $params['AttestSuivi'];
			$annee = $params['AttestAnnee'];
			$fisc = $params['AttestFiscale'];
			$where = " where a.Annee='$annee' and a.Cotisation>0 and substr(from_unixtime(a.DateCotisation),1,4)='$fisc'";
			$antenne = isset($params['Antenne']) ? $params['Antenne'] : '';
			$classe = isset($params['Classe']) ? $params['Classe'] : '';
			if($antenne || $classe) {
				$sql .= "
left join `##_Cadref-Inscription` i on i.AdherentId=h.Id and i.Annee='$annee'
left join `##_Cadref-Classe` c on c.Id=i.ClasseId
left join `##_Cadref-Niveau` n on n.Id=c.NiveauId
";
				if($antenne) $where .= " and n.AntenneId=$antenne";
				if($classe) $where .= " and c.CodeClasse like '$classe%'";
			}
			if($mode == 'print' && isset($params['NoMail']) && $params['NoMail'])
				$where .= " and h.Mail not like '%@%'";
			if($mode == 'mail')
				$where .= " and h.Mail like '%@%'";
			$sql .= $where.' order by h.Nom, h.Prenom';

			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			if(!$pdo) return false;
			if($mode == 'mail') {
				$this->sendAttestation($pdo, $annee, $fisc);
				return array('message'=>$pdo->rowCount().' mails envoyés.');
			}
			else {
				$file = $this->imprimeAttestation($pdo, $annee, $fisc, '');
				return array('pdf'=>$file,'sql'=>$sql);
			}
		}
		else {
			if(!isset($params['step'])) $params['step'] = 0;
			switch($params['step']) {
				case 0:
					return array(
						'step'=>1,
						'template'=>'printAttestation',
						'callNext'=>array(
							'nom'=>'PrintAttestation',
							'title'=>'Attestation 2',
							'needConfirm'=>false
						)
					);
					break;
				case 1:
					$suivi = $params['Attest']['AttestSuivi'] ? 1 : ($params['Attest']['AttestPaiement'] ? 2 : 0);
					$annee = $params['Attest']['AttestAnnee'];
					$fisc = $params['Attest']['AttestFiscale'];
					$mode = $params['Attest']['mode'];
					if(suivi) $where = " where a.AdherentId=$id and a.Annee='$annee'";
					else $where = " where a.AdherentId=$id and a.Annee='$annee' and a.Cotisation>0 and substr(from_unixtime(a.DateCotisation),1,4)='$fisc'";
					$sql = str_replace('##_', MAIN_DB_PREFIX, $sql.$where);
					$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
					if(!$pdo) return false;
					
					if(!$pdo->rowCount()) return array('step'=>2, 'data'=>'Pas de cotisation pour cette année.');

					if($mode == 'mail') {
						if($suivi) $this->sendSuivi($suivi, $pdo, $annee);
						else $this->sendAttestation($pdo, $annee, $fisc);
						return array('step'=>2, 'data'=>'Message envoyé.');
					}
					
					if($suivi) $file = $this->imprimeSuivi($suivi, $pdo, $annee);
					else $file = $this->imprimeAttestation($pdo, $annee, $fisc, $mode);
					return array(
						'step'=>2,
						'data'=>'<a id="displayAttestation" href="'.$file.'" target="_blank" ng-click="attestationAdherent(\''.$file.'\')">Attestation imprimée</a>',
						'callBack'=>array(
							'nom'=>'displayAttestation',
							'title'=>'Attestation 3',
							'args'=>array()
						)
					);
					break;
			}
		}
	}
	
	private function imprimeAttestation($list, $annee, $fisc, $num) {
		require_once ('PrintAttestation.class.php');

		$pdf = new PrintAttestation($annee, $fisc);
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle('Attestations fiscales');

		foreach($list as $l)
			$pdf->PrintPage($l);

		$file = 'Home/tmp/Attestation'.$num.'_'.date('YmdHis').'.pdf';
		$pdf->Output(getcwd().'/'.$file);
		$pdf->Close();

		return $file;
	}

	private function sendAttestation($pdo, $annee, $fisc) {
		$an = $annee.'-'.($annee+1);
		$sub = Cadref::$UTL." : Attestation fiscale";
		$bod = "Veuillez trouver en pièce jointe l’attestation fiscale correspondant à votre cotisation $an pour l’année fiscale $fisc.<br/><br />";
		$bod .= "Cette somme est à noter à la ligne 7UF de la déclaration 2042 RICI, case intitulée : \"Dons versés à d’autres organismes d’intérêt général\".";
		$bod .= Cadref::MailSignature();
		foreach($pdo as $p) {
			$file = $this->imprimeAttestation(array($p), $annee, $fisc, $p['Numero']);
			$b = Cadref::MailCivility($p).$bod;
			$args = array('To'=>array($p['Mail']), 'Subject'=>$sub, 'Body'=>$b, 'Attachments'=>array($file));
			if(MSG_ADH) Cadref::SendMessage($args);
		}
		$args = array('To'=>array(Cadref::$MAIL), 'Subject'=>$sub, 'Body'=>$bod);
		Cadref::SendMessage($args);
	}

	private function imprimeSuivi($suivi, $list, $annee) {
		require_once ('PrintSuivi.class.php');

		$pdf = new PrintSuivi();
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle('Attestations de suivi de cours');
		foreach($list as $l) {
			$adh = Sys::getOneData('Cadref', 'Adherent/'.$l['Id']);
			$aan = $this->getOneChild('AdherentAnnee/Annee='.$annee);
			$ins = $this->getChildren('Inscription/Annee='.$annee);
			if(!$aan || (!$aan->Cotisation && !count($ins))) continue;
			$pdf->PrintPage($suivi, $adh, $ins, $aan, $annee);
		}

		$file = 'Home/tmp/Suivi'.$num.'_'.date('YmdHis').'.pdf';
		$pdf->Output(getcwd().'/'.$file);
		$pdf->Close();

		return $file;
	}


	private function sendSuivi($suivi, $pdo, $annee) {
		$an = $annee.'-'.($annee+1);
		$sub = Cadref::$UTL." : Attestation de suivi de cours";
		$bod = "Veuillez trouver en pièce jointe l’attestation de suivi de cours $an .<br/><br />";
		$bod .= Cadref::MailSignature();
		foreach($pdo as $p) {
			$file = $this->imprimeSuivi($suivi, array($p), $annee);
			$b = Cadref::MailCivility($p).$bod;
			$args = array('To'=>array($p['Mail']), 'Subject'=>$sub, 'Body'=>$b, 'Attachments'=>array($file));
			Cadref::SendMessage($args);
		}
		$args = array('To'=>array(Cadref::$MAIL), 'Subject'=>$sub, 'Body'=>$b, 'Attachments'=>array($file));
		Cadref::SendMessage($args);
	}
	
	function CotisationList($id) {
		$sql = "select Cotisation,Annee,substr(from_unixtime(DateCotisation),1,4) as Fisc from `##_Cadref-AdherentAnnee` where AdherentId=$id and Cotisation>0";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql.$where);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$cot = array();
		foreach($pdo as $p) $cot[] = array('Cotisation'=>$p['Cotisation'],'Annee'=>$p['Annee'].'-'.($p['Annee']+1),'Fisc'=>$p['Fisc']);
		return array('cotisations'=>$cot);
	}

	function PrintCheque($params) {
		$id = $this->Id;
		if(!$id) {
			$p = $params;
			$annee = Cadref::$Annee;
			$classe = $params['CodeClasse'];
			$sql = "
select h.Nom,h.Prenom,h.Adresse1,h.Adresse2,h.CP,h.Ville
from `##_Cadref-Inscription` i
inner join `##_Cadref-Adherent` h on h.Id=i.AdherentId
where i.CodeClasse='$classe' and i.Annee='$annee'";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			if(!$pdo) return false;

			$file = $this->imprimeCheque($pdo, $params);
			return array('pdf'=>$file, 'sql'=>$sql);
		}
		else {
			if(!isset($params['step'])) $params['step'] = 0;
			switch($params['step']) {
				case 0:
					return array(
						'step'=>1,
						'template'=>'printCheque',
						'callNext'=>array(
							'nom'=>'PrintCheque',
							'title'=>'Cheque 2',
							'needConfirm'=>false
						)
					);
					break;
				case 1:
					$file = $this->imprimeCheque(null, $params['Cheque']);
					return array(
						'data'=>'<a id="displayCheque" href="'.$file.'" target="_blank" ng-click="chequeAdherent(\''.$file.'\')">Chèque imprimé</a>',
						'callBack'=>array(
							'nom'=>'displayCheque',
							'title'=>'Cheque 3',
							'args'=>array()
						)
					);
					break;
			}
		}
		return array(
			'params'=>$params,
			'template'=>'printCheque',
		);
	}

	private function imprimeCheque($list, $params) {
		require_once ('PrintCheque.class.php');

		$pdf = new PrintCheque($annee, $fisc);
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle('Cheque');

		if($list) {
			foreach($list as $l)
				$pdf->PrintPage($l, $params);
		} else $pdf->PrintPage(null, $params);

		$file = 'Home/tmp/Cheque_'.date('YmdHis').'.pdf';
		$pdf->Output(getcwd().'/'.$file);
		$pdf->Close();

		return $file;
	}

	function SendMessage($params) {
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
					$params['Msg']['To'] = array($params['Msg']['Mail'],Cadref::$MAIL);
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


	function PublicSendMessage($params) {
		$annee = Cadref::$Annee;
		$id = $this->Id;
		$mode = $params['sendMode'];
		$args = array();
		$args['Subject'] = $params['Subject'];
		$args['Body'] = $params['Sender']."<br /><br />".$params['Body'];
		$args['Attachments'] = $params['Msg']['Pieces']['data'];
		$args['Cc'] = array($this->Mail);
		$args['ReplyTo'] = array($this->Mail);
		$args['From'] = Cadref::$MAIL;
		
		$to = $params['Mail'];
		if($to == 'C') $args['To'] = array(Cadref::$MAIL);
		elseif(substr($to, 0, 2) == 'D:') {
			$sql = "
select distinct a.Mail
from `##_Cadref-Inscription` i 
inner join `##_Cadref-Adherent` a on a.Id=i.AdherentId
where i.ClasseId=".substr($to, 2)." and a.Mail like '%@%'";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql, PDO::FETCH_ASSOC);
			foreach($pdo as $r) {
				$args['To'] = array($r['Mail']);
				$ret = Cadref::SendMessage($args);
			}
			$args['To'] = array(Cadref::$MAIL);
		}
		else $args['To'] = array($to,Cadref::$MAIL);
		
		Cadref::SendMessage($args);
		return array('data'=>'Message envoyé');
	}
	
	
	// renvoi un valeur numerique pour comparaison de dates 
	private function cycleValeur($c, $debut) {
		if($c == '') return $debut ? 0 : 19999;
		$m = intval(substr($c, 3));
		return ($m < 9 ? 10000 : 0)+$m*100+intval(substr($c, 0, 2)); 
	}

	public function GetPayment($montant, $mode) {
		$p = genericClass::createInstance('Cadref', 'Paiement');
		$p->Montant = $montant;
		$tp = Sys::getOneData('Cadref', 'TypePaiement/Actif=1');
		$p->addParent($tp);
		$p->addParent($this);
		$p->Save();
		$pl = $tp->getPlugin();
		return $pl->getCodeHTML($p);
	}
	
	function GetPanier($action, $classe, $donation=-1) {
		$adhId = $this->Id;
		$annee = Cadref::$Annee;

		$pa = $this->getOneChild('Panier/Annee='.$annee);
		if(!$pa) {
			$pa = genericClass::createInstance('Cadref', 'Panier');
			$pa->Numero = $this->Numero;
			$pa->Annee = $annee;
			$pa->addParent($this);
		}
		$ids = $pa->Panier;
		$vids = $pa->Visite;
		$donate = $pa->Dons;

		$sess = isset($_SESSION['panier']);
		if($sess) $ids = unserialize($_SESSION['panier']);
		$vsess = isset($_SESSION['visite']);
		if($vsess) $vids = unserialize($_SESSION['visite']);

		$classe = trim($classe);
		if(!empty($classe)) {
			$c = "'$classe',";
			if($action == 'add') {
				$sess = true;
				if(strpos($ids, $c) === false) $ids .= $c;
				//if($sess) $_SESSION['panier'] = serialize($ids);			
			}
			if(!empty($ids) && $action == 'remove') {
				$ids = str_replace($c, '', $ids);
				//if($sess) $_SESSION['panier'] = serialize($ids);			
			}
			if($action == 'visiteAdd') {
				$vsess = true;
				if(strpos($vids, $c) === false) $vids .= $c;
				//if($vsess) $_SESSION['visite'] = serialize($vids);			
			}
			if($action == 'visiteRemove' && !empty($vids)) {
				$vids = str_replace($c, '', $vids);
				//if($vsess) $_SESSION['visite'] = serialize($vids);			
			}
		}
		
		$an = Sys::GetOneData('Cadref','Annee/Annee='.$annee);
		$co = $this->getOneChild('AdherentAnnee/Annee='.$annee);
		
		$solde = 0;
		if($co && $co->Solde > 0) $solde = $co->Solde; 

		// recheche cotisation due ou payee
		$data = array();
		$visites = array();
		if($action != 'inscribe') {
			$cot = ['clsId'=>0,'CodeClasse'=>'','LibelleD'=>'Cotisation '.Cadref::$UTL.' '.$annee.'-'.($annee+1),'LibelleN'=>'',
				'Jour'=>0,'HeureDebut'=>'','HeureFin'=>'','CycleDebut'=>'','CycleFin'=>'',
				'LibelleA'=>'','Prix'=>0,'Reduction'=>0,'Soutien'=>0,'Inscrit'=>1,'Places'=>0,
				'Disponibles'=>0,'note2'=>'','heures'=>0];
			if($co && $co->Cotisation)	{
				$cotisDue = 0;
				$cot['Prix'] = $cotis = $co->Cotisation;
				$cot['classe'] = 'label-success';
				$cot['note'] = 'Déjà réglée';
			} else {
				$cot['Prix'] = $cotis = $cotisDue = $an->Cotisation;
				$cot['classe'] = 'label-warning';
				$cot['note'] = 'A régler';
			}
			$data[] = $cot;
			$visites[] = $cot;
			$regul = $co ? $co->Regularisation : 0;
			$dons = $co ? $co->Dons : 0;
		}
		else {
			if($co) {
				$cotisDue = 0;
				$cotis = $co->Cotisation ? $co->Cotisation : $an->Cotisation;
				$regul = $co->Regularisation;
				$dons = $co->Dons;
			}
			else {
				$cotis = $cotisDue = $an->Cotisation;
				$regul = 0;
				$dons = 0;
			}
		}

		// cours deja inscrits
		$sql = "
select c.Id as clsId, c.CodeClasse, d.Libelle as LibelleD, n.Libelle as LibelleN, 
j.Jour, c.HeureDebut, c.HeureFin, c.CycleDebut, c.CycleFin,
a.LibelleCourt as LibelleA,i.Prix,i.Reduction,i.Soutien,
i.Attente,i.Supprime,1 as Inscrit,c.Places,if(c.Places-c.Inscrits-c.Attentes<=0,0,c.Places-c.Inscrits-c.Attentes) as Disponibles,
from_unixtime(i.DateAttente,'%d/%m/%Y %H:%i') as DateAttente,
from_unixtime(i.DateSupprime,'%d/%m/%Y') as DateSupprime,
from_unixtime(i.DateInscription,'%d/%m/%Y') as DateInscription, i.Supprime, c.Attachements, i.Id as insId
from `##_Cadref-Inscription` i
inner join `##_Cadref-Classe` c on c.Id=i.ClasseId
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d0 on d0.Id=n.DisciplineId
inner join `##_Cadref-WebDiscipline` d on d.Id=d0.WebDisciplineId
inner join `##_Cadref-Antenne` a on a.Id=n.AntenneId
left join `##_Cadref-Jour` j on j.Id=c.JourId
where i.AdherentId=$adhId and i.Annee='$annee'
order by d.Libelle, n.Libelle, c.JourId, c.HeureDebut";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql, PDO::FETCH_ASSOC);
		foreach($pdo as $r) {
			$r['bloque'] = 0;
			$r['classe'] = 'label-success';
			switch($r['Supprime']) {
				case 0: $r['note'] = 'Déjà inscrit'; break;
				case 1: $r['note'] = 'Cours supprimé'; $r['classe'] = 'label-danger'; break;
				case 2: $r['note'] = 'Cours échangé'; $r['classe'] = 'label-warning'; break;
			}
			
			$r['note2'] = '';
			$r['heures'] = 0;
			if($action != 'inscribe') $data[] = $r;
			// supprime si deja inscrit
			$c = "'".$r['CodeClasse']."',";
			$ids = str_replace($c, '', $ids); 
		}
			
		// cours ajoutés
		$in = substr($ids, 0, strlen($ids)-1);
		$sql = "
select distinct c.Id as clsId, c.CodeClasse, wd.Libelle as LibelleD, n.Libelle as LibelleN, 
j.Jour, c.HeureDebut, c.HeureFin, c.CycleDebut, c.CycleFin,a.LibelleCourt as LibelleA, c.Prix,
if(c.DateReduction2 is not null and c.DateReduction2<=UNIX_TIMESTAMP(),c.Reduction2,
if(c.DateReduction1 is not null and c.DateReduction1<=UNIX_TIMESTAMP(),c.Reduction1,0)) as Reduction,
0 as Soutien,0 as Attente,0 as Supprime,0 as Inscrit,c.Places,if(c.Places-c.Inscrits-c.Attentes<=0,0,c.Places-c.Inscrits-c.Attentes) as Disponibles,
c.AccesWeb as cWeb,n.AccesWeb as nWeb,0 as Attachements, 0 as InsId
from `##_Cadref-Classe` c
inner join `##_Cadref-Niveau` n on c.NiveauId=n.Id
inner join `##_Cadref-Discipline` d on n.DisciplineId=d.Id
inner join `##_Cadref-WebDiscipline` wd on d.WebDisciplineId=wd.Id
inner join `##_Cadref-Antenne` a on a.Id=n.AntenneId
left join `##_Cadref-Jour` j on j.Id=c.JourId
where c.CodeClasse in ($in) and c.Annee='$annee' 
order by d.Libelle, n.Libelle, c.JourId, c.HeureDebut";
		$sss = $sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql, PDO::FETCH_ASSOC);
		$montant = 0;
		foreach($pdo as $r) {
			$r['bloque'] = 0;
			if(!$r['cWeb'] || !$r['nWeb']) {
				$r['bloque'] = 1;
				$r['note2'] = 'Indisponible en ligne';
			}
			$r['classe'] = 'label-warning';
			$r['note'] = 'Nouveau cours';
			$jr = $r['Jour'];
			$cyd = $this->cycleValeur($r['CycleDebut'], true);
			$cyf = $this->cycleValeur($r['CycleFin'], false);
			$hrd = $r['HeureDebut'];
			$hrf = $r['HeureFin'];
			$heures = 0;
			foreach($data as $d) {
				if($d['clsId'] == 0) continue;
				$cd = $this->cycleValeur($d['CycleDebut'], true);
				$cf = $this->cycleValeur($d['CycleFin'], false);
				if($jr == $d['Jour'] && (($cf >= $cyd && $cf <= $cyf) || ($cd >= $cyd && $cd <= $cyf))) {
					$hd = $d['HeureDebut'];
					$hf = $d['HeureFin'];
					if(($hf >= $hrd && $hf <= $hrf) || ($hd >= $hrd && $hd <= $hrf)) $heures = 1;
				}			
			}
			$r['heures'] = $heures;
			if(!$r['bloque'] && $r['Disponibles'] <= 0) {
				$r['note2'] =  'Plus de place disponible';
				$r['bloque'] = 1;
			}
			elseif($heures) $r['note2'] = "Chevauchement d'horaire";
			else $r['note2'] = '';
			
			if($r['bloque'] == 0) {
				$data[] = $r;
				$montant += $r['Prix']-$r['Reduction'];
			}
			else {
				$c = "'".$r['CodeClasse']."',";
				$ids = str_replace($c, '', $ids);		
			}
		}

		if($sess) $_SESSION['panier'] = serialize($ids);
		$pa->Panier = $ids;
		if($donation >= 0) $pa->Dons = $donate = $donation;
		$pa->Save();


		$sql1 = "
select e.Nom, e.Prenom 
from `##_Cadref-ClasseEnseignants` ce
inner join `##_Cadref-Enseignant` e on e.Id=ce.EnseignantId
where ce.Classe=:cid";
		$sql1 = str_replace('##_', MAIN_DB_PREFIX, $sql1);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql1);
		foreach($data as &$d) {
			$id = $d['clsId'];
			if(!$id) continue;
			$pdo->execute(array(':cid'=>$id));
			$e = '';
			foreach($pdo as $p) {
				if($e) $e .= ', ';
				$e .= trim($p['Prenom'].' '.$p['Nom']);
			}
			$d['Enseignants'] = $e;
		}
		$total = $cotisDue+$montant+$donate+$solde;
		
		
		// visites deja inscrites
		$sql = "
select v.Id as visId, c.Visite, v.Libelle as LibelleD,v.DateVisite,v.Prix,from_unixtime(v.DateVisite,'%d/%m/%Y') as DateText,
r.Attente,r.Supprime,1 as Inscrit,v.Places,if(v.Places-v.Inscrits-v.Attentes<=0,0,v.Places-v.Inscrits-v.Attentes) as Disponibles,v.Attachements,
from_unixtime(r.DateAttente,'%d/%m/%Y %H:%i') as DateAttente,
from_unixtime(r.DateSupprime,'%d/%m/%Y') as DateSupprime,
from_unixtime(r.DateInscription,'%d/%m/%Y') as DateInscription, i.Supprime, r.Id as resId
from `##_Cadref-Reservation` r
inner join `##_Cadref-Visite` v on v.Id=r.VisiteId
where r.AdherentId=$adhId and r.Annee='$annee'
order by v.DateVisite";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql, PDO::FETCH_ASSOC);
		foreach($pdo as $r) {
			$r['bloque'] = 0;
			$r['classe'] = 'label-success';
			switch($r['Supprime']) {
				case 0: $r['note'] = 'Déjà inscrit'; break;
				case 1: $r['note'] = 'Visite supprimée'; $r['classe'] = 'label-danger'; break;
				case 2: $r['note'] = 'Visite échangée'; $r['classe'] = 'label-warning'; break;
			}
			
			$r['note2'] = '';
			if($action != 'inscribe') $visites[] = $r;
			// supprime si deja inscrit
			$c = "'".$r['Visite']."',";
			$vids = str_replace($c, '', $vids); 				
		}
			
		// visites ajoutées
		$in = substr($vids, 0, strlen($vids)-1);
		$sql = "
select distinct v.Id as clsId,v.Visite,v.Libelle as LibelleD,v.DateVisite,v.Prix,from_unixtime(v.DateVisite,'%d/%m/%Y') as DateText,
0 as Attente,0 as Supprime,0 as Inscrit,v.Places,if(v.Places-v.Inscrits-v.Attentes<=0,0,v.Places-v.Inscrits-v.Attentes) as Disponibles,v.Web,
0 as Attachements, 0 as resId
from `##_Cadref-Visite` v
where v.Visite in ($in) and v.Annee='$annee'
order by v.DateVisite";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql, PDO::FETCH_ASSOC);
		$montantVisite = 0;
		foreach($pdo as $r) {
			$r['bloque'] = 0;
			if(!$r['Web']) {
				$r['bloque'] = 1;
				$r['note2'] = 'Indisponible en ligne';
			}
			$r['classe'] = 'label-warning';
			$r['note'] = 'Nouvelle visite';
			if(!$r['bloque'] && $r['Disponibles'] <= 0) {
				$r['note2'] =  'Plus de place disponible';
				$r['bloque'] = 1;
			}
			else $r['note2'] = '';
			
			if($r['bloque'] == 0) {
				$visites[] = $r;
				$montantVisite += $r['Prix'];
			}
			else {
				$c = "'".$r['Visite']."',";
				$vids = str_replace($c, '', $vids);		
			}

		}

		if($vsess) {
			$_SESSION['visite'] = serialize($vids);
			$pa->Visite = $vids;
			if($donation >= 0) $pa->Dons = $donate = $donation;
			$pa->Save();
		}

		$sql1 = "
select e.Nom, e.Prenom 
from `##_Cadref-VisiteEnseignants` ce
inner join `##_Cadref-Enseignant` e on e.Id=ce.EnseignantId
where ce.Visite=:cid";
		$sql1 = str_replace('##_', MAIN_DB_PREFIX, $sql1);
		$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql1);
		foreach($visites as &$d) {
			$id = $d['clsId'];
			if(!$id) continue;
			$pdo->execute(array(':cid'=>$id));
			$e = '';
			foreach($pdo as $p) {
				if($e) $e .= ', ';
				$e .= trim($p['Prenom'].' '.$p['Nom']);
			}
			$d['Enseignants'] = $e;
		}
		$totalVisite = $cotisDue+$montantVisite+$donate+$solde;

		return array('data'=>$data, 'cotis'=>$cotis, 'cotisDue'=>$cotisDue, 'solde'=>$solde, 'donate'=>$donate, 'montant'=>$montant, 'total'=>$total, 
			'regul'=>$regul, 'dons'=>$dons, 'visites'=>$visites, 'montantVisite'=>$montantVisite, 'totalVisite'=>$totalVisite,
			'urlweb'=>unserialize($_SESSION['urlweb']), 'sql'=>$sss);		
	}
	
	

	function GetCours($mode, $obj) {
		$annee = Cadref::$Annee;
		$filter = str_replace('&', '', $obj['Filter']);
		$adhId = $this->Id;
		$antId = $obj['AntenneId'];
		$secId = $obj['SectionId'];
		$disId = $obj['DisciplineId'];
		switch($mode) {
			case 'antenne':
				$sql = "
select Id, Libelle
from `##_Cadref-Antenne`
where Libelle like '%$filter%'
";
				break;
			case 'section':
				$sql = "
select distinct s.Id, s.Libelle
from `##_Cadref-Discipline` d0
inner join `##_Cadref-Niveau` n on n.DisciplineId=d0.Id and n.AntenneId=$antId and n.AccesWeb=1
inner join `##_Cadref-Classe` c on c.NiveauId=n.Id and c.Annee='$annee' and c.AccesWeb=1
inner join `##_Cadref-WebDiscipline` d on d.Id=d0.WebDisciplineId
inner join `##_Cadref-WebSection` s on s.Id=d.WebSectionId
where d0.WebDisciplineId>0 and s.Libelle like '%$filter%'
order by s.Libelle";
				break;
			case 'discipline':
				$sql = "
select distinct d.Id, d.Libelle
from `##_Cadref-Discipline` d0
inner join `##_Cadref-Niveau` n on n.DisciplineId=d0.Id and n.AntenneId=$antId and n.AccesWeb=1
inner join `##_Cadref-Classe` c on c.NiveauId=n.Id and c.Annee='$annee' and c.AccesWeb=1
inner join `##_Cadref-WebDiscipline` d on d.WebSectionId=$secId and d.Id=d0.WebDisciplineId
where d0.WebDisciplineId>0 and d.Libelle like '%$filter%'
order by d.Libelle";				
				break;
			case 'classe':
				$sql = "
select distinct c.CodeClasse, c.Id as clsId, d.Libelle as LibelleD, n.Libelle as LibelleN, 
j.Jour, c.HeureDebut, c.HeureFin, c.CycleDebut, c.CycleFin,
c.Places,if(c.Places<c.Inscrits,0,c.Places-c.Inscrits) as Disponible,
a.LibelleCourt as LibelleA,c.Prix,c.Attachements,
if(c.DateReduction2 is not null and c.DateReduction2<=unix_timestamp(CURRENT_TIMESTAMP()),c.Reduction2,
if(c.DateReduction1 is not null and c.DateReduction1<=unix_timestamp(CURRENT_TIMESTAMP()),c.Reduction1,0)) as Reduction,
0 as Soutien,(n.AccesWeb and c.AccesWeb) as Web
from `##_Cadref-Discipline` d0
inner join `##_Cadref-Niveau` n on n.DisciplineId=d0.Id and n.AntenneId=$antId
inner join `##_Cadref-Classe` c on c.NiveauId=n.Id and c.Annee='$annee'
inner join `##_Cadref-WebDiscipline` d on d.Id=d0.WebDisciplineId
inner join `##_Cadref-Antenne` a on a.Id=n.AntenneId
left join `##_Cadref-Jour` j on j.Id=c.JourId
where d0.WebDisciplineId=$disId and (d0.Libelle like '%$filter%' or n.Libelle like '%$filter%')
order by d.Libelle, n.Libelle, c.JourId, c.HeureDebut";
				break;
			case 'inscription':
				$sql = "
select i.Id as insId, c.Id as clsId, d.Libelle as LibelleD, n.Libelle as LibelleN, 
j.Jour, c.HeureDebut, c.HeureFin, c.CycleDebut, c.CycleFin,
a.LibelleCourt as LibelleA,i.Prix,i.Reduction,i.Soutien,c.Attachements,
i.Attente,i.Supprime,
from_unixtime(i.DateAttente,'%d/%m/%Y %H:%i') as DateAttente,
from_unixtime(i.DateSupprime,'%d/%m/%Y') as DateSupprime,
from_unixtime(i.DateInscription,'%d/%m/%Y') as DateInscription
from `##_Cadref-Inscription` i
inner join `##_Cadref-Classe` c on c.Id=i.ClasseId
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d0 on d0.Id=n.DisciplineId
inner join `##_Cadref-WebDiscipline` d on d.Id=d0.WebDisciplineId
inner join `##_Cadref-Antenne` a on a.Id=n.AntenneId
left join `##_Cadref-Jour` j on j.Id=c.JourId
where i.AdherentId=$adhId and i.Annee='$annee' and i.Supprime=0
order by d.Libelle, n.Libelle, c.JourId, c.HeureDebut";
				break;
			case 'visite':
				$dat = time();
				$sql = "
select v.Id as clsId, v.Visite, v.Libelle, v.DateVisite, from_unixtime(v.DateVisite,'%d/%m/%Y') as DateText, 
v.Prix, v.Places, v.Inscrits, v.Attentes, v.Description,
if(Places<Inscrits,0,Places-Inscrits) as Disponible, Attachements, Web
from `##_Cadref-Visite` v
where Annee='$annee' and DateVisite>=$dat and Libelle like '%$filter%'
order by DateVisite
";
				break;
			case 'reservation':
				$sql = "
select r.Id as resId, v.Id as clsId, v.Visite, v.Libelle, v.DateVisite, from_unixtime(v.DateVisite,'%d/%m/%Y') as DateText, r.Prix, v.Places, 
v.Inscrits, v.Attentes, v.Description,v.Attachements,r.Attente,r.Supprime,
from_unixtime(r.DateAttente,'%d/%m/%Y %H:%i') as DateAttente,
from_unixtime(r.DateInscription,'%d/%m/%Y') as DateInscription,
from_unixtime(r.DateSupprime,'%d/%m/%Y') as DateSupprime,
if(v.Places<v.Inscrits,0,v.Places-v.Inscrits) as Disponible, ifnull(d.HeureDepart,'') as HeureDepart, ifnull(l.Libelle,'') as LibelleL
from `##_Cadref-Reservation` r
inner join `##_Cadref-Visite` v on v.Id=r.VisiteId
left join `##_Cadref-Depart` d on d.Id=r.DepartId
left join `##_Cadref-Lieu` l on l.Id=d.LieuId
where r.AdherentId=$adhId and r.Annee='$annee'
order by DateVisite
";
				break;
		}
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql, PDO::FETCH_ASSOC);
if(!$pdo) return array('sql'=>$sql);
		$data = $pdo->fetchAll();
		
		if($mode == 'inscription' || $mode == 'classe' || $mode == 'reservation' || $mode == 'visite') {
			if($mode == 'inscription' || $mode == 'classe')
				$sql1 = "
select e.Nom, e.Prenom 
from `##_Cadref-ClasseEnseignants` ce
inner join `##_Cadref-Enseignant` e on e.Id=ce.EnseignantId
where ce.Classe=:cid";
			else
				$sql1 = "
select e.Nom, e.Prenom 
from `##_Cadref-VisiteEnseignants` ce
inner join `##_Cadref-Enseignant` e on e.Id=ce.EnseignantId
where ce.Visite=:cid";
			
			$sql1 = str_replace('##_', MAIN_DB_PREFIX, $sql1);
			$pdo = $GLOBALS['Systeme']->Db[0]->prepare($sql1);
			foreach($data as &$d) {
				$pdo->execute(array(':cid'=>$d['clsId']));
				$e = '';
				foreach($pdo as $p) {
					if($e) $e .= ', ';
					$e .= trim($p['Prenom'].' '.$p['Nom']);
				}
				$d['Enseignants'] = $e;
			}
		}
		return array('data'=>$data, 'sql'=>$sql);
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
		$msg = Cadref::$UTL." : Changement de mot de passe.\nCode utilisateur: $this->Numero\nMote de passe: $new\n";
		$params = array('Telephone1'=>$this->Telephone1,'Telephone2'=>$this->Telephone2,'Message'=>$msg);
		Cadref::SendSms($params);

		$data['success'] = 1;
		$data['message'] = 'Mot de passe enregistré';
		return $data;
	}


	function EditReservations($params) {
		$annee = Cadref::$Annee;
		if(!isset($params['Numero'])) if(!isset($params['step'])) $params['step'] = 0;
		switch($params['step']) {
			case 0:
				return array(
					'step'=>1,
					'template'=>'editReservation',
					'callNext'=>array(
						'nom'=>'EditReservations',
						'title'=>'Réglement',
						'needConfirm'=>false
					)
				);
				break;
			case 1:
				unset($params['step']);
				$ret = $this->saveReservations($params, true);
				$this->saveAnneeReserv($params);
				if($ret) return array(
						'data'=>'Reservation enregistrée',
						'callBack'=>array(
							'nom'=>'refreshAdherent',
							'args'=>array(true)
						)
					);
				return false;
				break;
		}
	}

	private function saveReservations($params, $saveAdh) {
		$annee = Cadref::$Annee;
		$inscr = $params['Visit'];
		if(!$inscr['updated']) return true;

		// reservations
		foreach($params['newReserv'] as $ins) {
			if(!$ins['updated']) continue;
			$id = $ins['id'];
			$attente = 0;
			$supprime = 0;

			$vis = genericClass::createInstance('Cadref', 'Visite');
			$vis->initFromId($ins['VisiteVisiteId']);
			$o = genericClass::createInstance('Cadref', 'Reservation');

			if(!$id) {
				$o->addParent($this);
				$o->addParent($vis);
				$o->Annee = $annee;
				$o->Numero = $this->Numero;
				$o->Visite = $ins['Visite'];
			} else {
				$o->initFromId($id);
				$attente = $o->Attente;
				$supprime = $o->Supprime;
			}
			if($ins['DepartDepartId']) {
				$dep = genericClass::createInstance('Cadref', 'Depart');
				$dep->initFromId($ins['DepartDepartId']);
				$o->addParent($dep);
			}

			$o->Attente = $ins['Attente'];
			$o->Supprime = $ins['Supprime'];
			$o->DateInscription = $ins['DateInscription'];
			$o->DateAttente = $ins['DateAttente'];
			$o->DateSupprime = $ins['DateSupprime'];
			$o->Prix = $ins['Prix'];
			//$o->Assurance = $ins['Assurance'];
			$o->ModeReglement = $ins['ModeReglement'];
			$o->Utilisateur = Sys::$User->Initiales;
			$o->Save();

			// visite : inscrits/attentes/suppmime
			if(!$id || $supprime != $o->Supprime || $attente != $o->Attente) {
				$vis->Save();
			}
		}

//		// reglement
//		if($inscr['paye']) {
//			$r = genericClass::createInstance('Cadref', 'Reglement');
//			$r->addParent($this);
//			$r->Numero = $this->Numero;
//			$r->Annee = $annee;
//			$r->DateReglement = $inscr['date'];
//			$r->Montant = $inscr['paye'];
//			$r->ModeReglement = $inscr['mode'];
////			$r->Cotisation = $inscr['cotis'];
//			$r->Notes = $inscr['note'];
//			$r->Differe = 1;
//			$r->Encaisse = 0;
//			$r->Utilisateur = Sys::$User->Initiales;
//			$r->Save(true);
//		}

		// adherent
		$this->Annee = $annee;
		if($saveAdh) $this->Save();

		return true;
	}
	
	private function saveAnneeReserv($params) {
		$data = new stdClass();
		$inscr = $params['Visit'];
		$data->Cotisation = $inscr['cotis'];
		$data->Regularisation = $inscr['regul'];
		$data->Dons = $inscr['dons'];
		$this->SaveAnnee($data, 1);
	}
	
	function PrintRecapitulatif($params) {
		require_once ('PrintRecapitulatif.class.php');
		
		$annee = $params['Annee'];
		
		if($params['CalculSolde']) {
			$adhs = Sys::getData('Cadref','Adherent/Annee='.$annee);
			foreach($adhs as $adh) {
				//$adh = $aan->getOneParent('Adherent');
				$adh->SaveAnnee(null, 3);
			}
		}

		$nsold = (isset($params['NonSolde']) && $params['NonSolde']) ? true : false;

//		$ddeb = DateTime::createFromFormat('d/m/Y H:i:s', $obj['DateDebut'].' 00:00:00')->getTimestamp(); 
//		$dfin = DateTime::createFromFormat('d/m/Y H:i:s', $obj['DateFin'].' 23:59:59')->getTimestamp();

		$sql = "
select e.Id,e.Numero,e.Nom,e.Prenom,a.Cours,a.Reglement,a.Differe,a.Regularisation,a.Dons,a.Cotisation,a.NotesAnnuelles,
i.CodeClasse,i.Supprime,i.Prix,i.Reduction,i.Soutien,d.Libelle as LibelleD,n.Libelle as LibelleN,i.Utilisateur
from `##_Cadref-AdherentAnnee` a
left join `##_Cadref-Adherent` e on e.Id=a.AdherentId
left join `##_Cadref-Inscription` i on i.AdherentId=e.Id and i.Annee='$annee'
left join `##_Cadref-Classe` c on c.Id=i.ClasseId 
left join `##_Cadref-Niveau` n on n.Id=c.NiveauId 
left join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
where a.Annee='$annee' and (a.Cours<>0 or a.Reglement<>0 or a.Differe<>0 or a.Regularisation<>0 or a.Dons<>0 or a.Cotisation<>0 or a.Visites<>0)
";
		if($nsold)
			$sql .= " and (a.Cours+a.Cotisation-a.Reglement-a.Differe+a.Regularisation+a.Dons<>0 or a.Cotisation=0)";		
		$sql .= " order by e.Nom,e.Prenom,e.Id,i.CodeClasse";
		
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return array('sql'=>$sql);

		$pdf = new PrintRecapitulatif($nsold);
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle(iconv('UTF-8','ISO-8859-15//TRANSLIT',$title));

		$pdf->AddPage();
		$pdf->PrintLines($pdo);
		$pdf->PrintTotal();

		$file = '/Home/tmp/Recapitulatif_'.date('YmdHis').'.pdf';
		$pdf->Output(getcwd().$file);
		$pdf->Close();
		
		return array('pdf'=>$file, 'sql'=>$sql);
	}

}
