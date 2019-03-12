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
		$this->NomPrenom = $this->Nom.' '.$this->Prenom;
		return parent::Save();
	}

	function GetFormInfo($annee) {
		$c = !$this->CheckCertificat();
		$s = $this->getOneChild('AdherentAnnee/Annee='.$annee);
		return array('Cotisation'=>$s->Cotisation, 'Cours'=>$s->Cours, 'Visites'=>$s->Visites, 'Reglement'=>$s->Reglement, 'Differe'=>$s->Differe,
			'Regularisation'=>$s->Regularisation, 'Solde'=>$s->Solde, 'NotesAnnuelles'=>$s->NotesAnnuelles, 'Adherent'=>$s->Adherent,
			'ClasseId'=>$s->ClasseId, 'AntenneId'=>$s->AntenneId, 'CotisationAnnuelle'=>Cadref::$Cotisation, 'certifInvalide'=>$c,
			'Soutien'=>$s->Soutien);
	}

	// $mode :
	// 0 => save adherent (appel par Adherent/Save.twig.php)
	// 1 => save inscription ou reservation	
	// 2 => save reglement (appel direct)
	function SaveAnnee($data, $mode) {
		$annee = Cadref::$Annee;
		$cours = 0;
		$visit = 0;
		$regle = 0;
		$diffe = 0;
		$ins = $this->getChildren('Inscription/Annee='.$annee);
		foreach($ins as $in) {
			if(!$in->Attente && !$in->Supprime) $cours += $in->Prix - $in->Reduction - $in->Soutien;
		}
		$vis = $this->getChildren('Reservation/Annee='.$annee);
		foreach($vis as $vi) {
			if(!$vi->Attente && !$vi->Supprime) $visit += $vi->Prix - $vi->Reduction + $vi->Assurance;
		}
		$rgs = $this->getChildren('Reglement/Annee='.$annee);
		foreach($rgs as $rg) {
			if($rg->Differe) {
				if($rg->Encaisse) $regle += $rg->Montant;
				else $diffe += $rg->Montant;
			} else $regle += $rg->Montant;
		}

		$a = $this->getOneChild('AdherentAnnee/Annee='.$annee);
		if(!$a) {
			$a = genericClass::createInstance('Cadref', 'AdherentAnnee');
			$a->addParent($this);
			$a->Annee = $annee;
			$a->Numero = $this->Numero;
		}

		if($mode == 0) {
			$a->Adhrent = $data->Adherent;
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
		$a->Regularisation = $data->Regularisation ? $data->Regularisation : 0;
		$a->Cours = $cours;
		$a->Visites = $visit;
		$a->Reglement = $regle;
		$a->Differe = $diffe;
		$a->Solde = $a->Cotisation + $cours + $visit - $regle - $diffe + $a->Regularisation;
		$a->Save();

		return true;
	}

	private function saveAnneeInscr($params) {
		$data = new stdClass();
		$inscr = $params['Inscr'];
		$data->Cotisation = $inscr['cotis'];
		$data->Regularisation = $inscr['regul'];
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
			if(!$r['updated'] || !$r[paye]) continue;

			$o = genericClass::createInstance('Cadref', 'Reglement');
			if($r['id']) $o->initFromId($r['id']);
			else $o->addParent($this);

			$m = $r['mois'];
			$d = '15/'.(strlen($m) == 1 ? '0' : '').$m.'/'.($m >= 9 ? $annee : ($annee + 1));
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
		if(!empty($dat) && date('Ymd', $dat) < $min) return true;

		$cert = false;
		$ins = $this->getChildren('Inscription/Annee='.$annee);
		foreach($ins as $i) {
			$c = $i->getOneParent('Classe');
			$d = $c->getOneParent('Discipline');
			if($d->Certificat) {
				$cert = true;
				break;
			}
		}
		if($cert && (empty($dat) || date('Ymd', $dat) < $min)) return false;
		return true;
	}

	function PrintCarte($recto = false) {
		require_once ('PrintCarte.class.php');

		$annee = Cadref::$Annee;
		$aan = $this->getOneChild('AdherentAnnee/Annee='.$annee);
		$ins = $this->getChildren('Inscription/Annee='.$annee);

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

	function PrintAdherent($obj) {
		$menus = ['impressionslisteadherents', 'impressionscertificatesmedicaux', 'impressionsfichesincompletes'];
		$mode = array_search($obj['CurrentUrl'], $menus);

		$annee = Cadref::$Annee;
		$sql = '';
		$whr = '';

		switch($mode) {
			case 0: // liste edherents
				$file = 'ListeAdherent';
				$typAdh = isset($obj['typeAdherent']) ? $obj['typeAdherent'] : '';
				$contenu = isset($obj['Contenu']) ? $obj['Contenu'] : '';
				$rupture = isset($obj['Rupture']) ? $obj['Rupture'] : '';
				$enseignant = isset($obj['Enseignant']) ? $obj['Enseignant'] : '';
				$adherent = false;


				if($typAdh != '' || $contenu == 'Q' || $rupture == 'S') {
					$sql = "select distinct ";
					$adherent = true;
					$rupture = 'S';
				}
				else $sql = "select i.CodeClasse, i.ClasseId, n.AntenneId, i.Attente, i.DateAttente, d.Libelle as LibelleD, n.Libelle as LibelleN, ";

				$sql .= "e.Sexe, e.Numero, e.Nom, e.Prenom, e.Adresse1, e.Adresse2, e.CP, e.Ville, e.Telephone1, e.Telephone2, e.Mail, e.ClasseId as Delegue";

				if($typAdh == 'S') {
					// adhérents sans inscription
					$sql .= "from Adherent e ";
					$whr = "and e.Cotisation>0 and e.Reglement=e.Cotisation and e.Differe=0 and e.Montant=0 ";
				} else {
					// adhérents inscrits
					$sql .= "
from `##_Cadref-Inscription` i
inner join `##_Cadref-Classe` c on c.Id=i.ClasseId
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
inner join `##_Cadref-Adherent` e on e.Id=i.AdherentId ";
					if($enseignant) {
						$sql .= "inner join `##_Cadref-ClasseEnseignants` ce on ce.Classe=i.ClasseId ";
						$whr .= "and ce.EnseignantId=$enseignant ";
					}
				}

				$mail = (isset($obj['Mail']) && $obj['Mail'] != '') ? $obj['Mail'] : '';
				if($mail == 'A') $whr .= "and e.Mail<>'' ";
				elseif($mail == 'S') $whr .= "and e.Mail='' ";

				if($typAdh != 'S') {
					$whr .= "and i.Annee='$annee' and i.Supprime=0 ";

					// type adherent
					if($typAdh != '') {
						$whr .= "and TypeAdherent in (";
						switch($typAdh) {
							case 'B': $whr .= "'B') ";
								break;
							case 'A': $whr .= "'B','A') ";
								break;
							case 'D': $whr .= "'B','A','D') ";
								break;
						}
					}

					if(isset($obj['Nouveaux']) && $obj['Nouveaux']) $whr .= "and e.Inscription='$annee' ";

					$antenne = (isset($obj['Antenne']) && $obj['Antenne'] != '') ? $obj['Antenne'] : '';
					if($antenne != '') $whr .= "and n.AntenneId='$antenne' ";

					$attente = (isset($obj['Attente']) && $obj['Attente'] != '') ? $obj['Attente'] : '';
					if($attente == 'I') $whr .= "and i.Attente=0 ";
					elseif($attente == 'A') $whr .= "and i.Attente<>0 ";

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
					if($contenu == 'Q') $sql .= "order by e.CP, e.Ville, e.Nom, e.Prenom ";
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
where a.Annee='$annee' and i.Supprime=0 and i.Attente=0 and d.Certificat<>0 and (a.DateCertificat is null or a.DateCertificat<unix_timestamp('$annee-07-01'))
order by e.Nom,i.CodeClasse,a.Nom,a.Prenom";
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
			foreach($pdo as $a) {
				if(strpos($a['Mail'], '@') > 0) {
					$args = array('Subject'=>$obj['Sujet'], 'To'=>array($a['Mail']), 'Body'=>$obj['Corps'], 'Attachments'=>$obj['Pieces']['data']);
					if(MAIL_ADH) Cadref::SendMessage($args);
				}
			}
			return true;
		}
		if($obj['mode'] == 'sms') {
			foreach($pdo as $a) {
				$params = array('Telephone1'=>$a['Telephone1'],'Telephone2'=>$a['Telephone2'],'Message'=>$obj['SMS']);
				Cadref::SendSms($params);
			}
			return true;
		}
		
		if($contenu != 'Q') {
			require_once ('PrintAdherent.class.php');

			$pdf = new PrintAdherent($mode, $contenu, $rupture, $antenne, $attente);
			$pdf->SetAuthor("Cadref");
			$pdf->SetTitle('Liste adherents');

			$pdf->AddPage();
			$pdf->PrintLines($pdo);

			$file = 'Home/tmp/'.$file.'_'.date('YmdHis').'.pdf';
			$pdf->Output(getcwd().'/'.$file);
			$pdf->Close();
		} else {
			require_once('Class/Lib/pdfb/fpdf_fpdi/PDF_label.php');

			$f = array('paper-size'=>'A4',
				'metric'=>'mm',
				'marginLeft'=>0,
				'marginTop'=>8.5,
				'NX'=>2,
				'NY'=>8,
				'SpaceX'=>0,
				'SpaceY'=>0,
				'width'=>105,
				'height'=>37.125,
				'font-size'=>9);
			$pdf = new PDF_label($f);
			$pdf->SetAuthor("Cadref");
			$pdf->SetTitle('Etiquettes adherents');

			$pdf->AddPage();
			foreach($pdo as $l) {
				$s = $l['Nom'].'  '.$l['Prenom']."\n".$l['Adresse1']."\n".$l['Adresse2']."\n".$l['CP']."  ".$l['Ville'];
				$pdf->Add_Label($s);
			}

			$file = 'Home/tmp/EtiquetteAdherent_'.date('YmdHis').'.pdf';
			$pdf->Output(getcwd().'/'.$file);
			$pdf->Close();
		}

		return array('pdf'=>$file, 'sql'=>$sql);
	}

	function PrintAttestation($params) {
		$mode = isset($params['mode']) ? $params['mode'] : 'print';
		$sql = "
select distinct h.Sexe,h.Mail,h.Numero,h.Nom,h.Prenom,h.Adresse1,h.Adresse2,h.CP,h.Ville,a.Cotisation
from `##_Cadref-AdherentAnnee` a
inner join `##_Cadref-Adherent` h on h.Id=a.AdherentId";

		$id = $this->Id;
		if(!$id) {
			$annee = $params['AttestAnnee'];
			$fisc = $params['AttestFiscale'];
			$where = " where a.Annee='$annee' and a.Cotisation>0 and substr(from_unixtime(a.DateCotisation),1,4)='$fisc'";
			$antenne = isset($params['Antenne']) ? $params['Antenne'] : '';
			if($antenne) {
				$sql .= "
left join `kob-Cadref-Inscription` i on i.AdherentId=h.Id and i.Annee='$annee'
left join `kob-Cadref-Classe` c on c.Id=i.ClasseId
left join `kob-Cadref-Niveau` n on n.Id=c.NiveauId
";
				$where .= " and n.AntenneId=$antenne";
			}
			if($mode == 'print' && isset($params['NoMail']) && $params['NoMail'])
				$where .= " and h.Mail not like '%@%'";
			if($mode == 'mail')
				$where .= " and h.Mail like '%@%'";

			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql.$where);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			if(!$pdo) return false;

			if($mode == 'mail') {
				$an = $annee.'-'.($annee+1);
				$sub = "CADREF : Attestation fiscale";
				$bod = "Veuillez trouver en pièce jointe l’attestation fiscale correspondant à votre cotisation $an pour l’année fiscale $fisc.<br/><br />";
				$bod .= "Cette somme est à noter à la ligne 7UF de la déclaration 2042 RICI, case intitulée : \"Dons versés à d’autres organismes d’intérêt général\".";
				$bod .= Cadref::MailSignature();
				foreach($pdo as $p) {
					$file = $this->imprimeAttestation(array($p), $annee, $fisc, $p['Numero']);
					$b = Cadref::MailCivility($p).$bod;
					$args = array('To'=>array($p['Mail']), 'Subject'=>$sub, 'Body'=>$b, 'Attachments'=>array($file));
					if(MAIL_ADH) Cadref::SendMessage($arg);
				}
				return array('sql'=>$sql);
			}
			else {
				$file = $this->imprimeAttestation($pdo, $annee, $fisc, '');
				return array('pdf'=>$file, 'sql'=>$sql);
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
					$annee = $params['Attest']['AttestAnnee'];
					$fisc = $params['Attest']['AttestFiscale'];
					$where = " where a.AdherentId=$id and a.Annee='$annee' and a.Cotisation>0 and substr(from_unixtime(a.DateCotisation),1,4)='$fisc'";
					$sql = str_replace('##_', MAIN_DB_PREFIX, $sql.$where);
					$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
					if(!$pdo) return false;
					
					if(!$pdo->rowCount()) return array(
						'step'=>2,
						'data'=>'Pas de cotisation pour cette année'
					);

					$file = $this->imprimeAttestation($pdo, $annee, $fisc, $mode);
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
		return array(
			'params'=>$params,
			'template'=>'printAttestation',
		);
	}
	
	function CotisationList($id) {
		$sql = "select Cotisation,Annee,substr(from_unixtime(DateCotisation),1,4) as Fisc from `##_Cadref-AdherentAnnee` where AdherentId=$id and Cotisation>0";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql.$where);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$cot = array();
		foreach($pdo as $p) $cot[] = array('Cotisation'=>$p['Cotisation'],'Annee'=>$p['Annee'].'-'.($p['Annee']+1),'Fisc'=>$p['Fisc']);
		return array('cotisations'=>$cot);
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
				break;
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
				break;
		}
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
inner join `##_Cadref-Niveau` n on n.DisciplineId=d0.Id and n.AntenneId=$antId
inner join `##_Cadref-Classe` c on c.NiveauId=n.Id and c.Annee='$annee'
inner join `##_Cadref-WebDiscipline` d on d.Id=d0.WebDisciplineId
inner join `##_Cadref-WebSection` s on s.Id=d.WebSectionId
where d0.WebDisciplineId>0 and s.Libelle like '%$filter%'
order by s.Libelle";
				break;
			case 'discipline':
				$sql = "
select distinct d.Id, d.Libelle
from `##_Cadref-Discipline` d0
inner join `##_Cadref-Niveau` n on n.DisciplineId=d0.Id and n.AntenneId=$antId
inner join `##_Cadref-Classe` c on c.NiveauId=n.Id and c.Annee='$annee'
inner join `##_Cadref-WebDiscipline` d on d.WebSectionId=$secId and d.Id=d0.WebDisciplineId
where d0.WebDisciplineId>0 and d.Libelle like '%$filter%'
order by d.Libelle";				
				break;
			case 'classe':
				$sql = "
select distinct c.Id as clsId, d.Libelle as LibelleD, n.Libelle as LibelleN, 
j.Jour, c.HeureDebut, c.HeureFin, c.CycleDebut, c.CycleFin,
c.Places,if(c.Places<c.Inscrits,0,c.Places-c.Inscrits) as Disponible,
a.LibelleCourt as LibelleA,c.Prix,c.Attachements,
if(c.DateReduction1 is not null and c.DateReduction1<=unix_timestamp(Now()),c.Reduction1,0) as Reduction1,
if(c.DateReduction2 is not null and c.DateReduction2<=unix_timestamp(Now()),c.Reduction2,0) as Reduction2
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
from_unixtime(i.DateAttente,'%d/%m/%Y') as DateAttente,
from_unixtime(i.DateSupprime,'%d/%m/%Y') as DateSupprime,
from_unixtime(i.DateInscription,'%d/%m/%Y') as DateInscription
from `##_Cadref-Inscription` i
inner join `##_Cadref-Classe` c on c.Id=i.ClasseId
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d0 on d0.Id=n.DisciplineId
inner join `##_Cadref-WebDiscipline` d on d.Id=d0.WebDisciplineId
inner join `##_Cadref-Antenne` a on a.Id=n.AntenneId
left join `##_Cadref-Jour` j on j.Id=c.JourId
where i.AdherentId=$adhId and i.Annee='$annee'
order by d.Libelle, n.Libelle, c.JourId, c.HeureDebut";
				break;
		}
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql, PDO::FETCH_ASSOC);
		$data = $pdo->fetchAll();
		if($mode == 'inscription' || $mode == 'classe') {
			$sql1 = "
select e.Nom, e.Prenom 
from `##_Cadref-ClasseEnseignants` ce
inner join `##_Cadref-Enseignant` e on e.Id=ce.EnseignantId
where ce.Classe=:cid";
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
		
		if(strpos($this->Mail, '@') > 0) {
			$s = Cadref::MailCivility($this);
			$s .= "Votre nouveau mot de passe a été enregistré.<br /><br />";
			$s .= Cadref::MailSignature();
			$params = array('Subject'=>('CADREF : Changement de mot de passe.'),
				'To'=>array($this->Mail),
				'Body'=>$s);
			Cadref::SendMessage($params);
		}
		$msg = "CADREF : Changement de mot de passe.\nCode utilisateur: $this->Numero\nMote de passe: $new\n";
		$params = array('Telephone1'=>$this->Telephone1,'Telephone2'=>$this->Telephone2,'Message'=>$msg);
		Cadref::SendSms($params);

		$data['success'] = 1;
		$data['message'] = 'Mot de passe enregistré';
		return $data;
	}

}
