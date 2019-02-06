<?php

class Adherent extends genericClass {
	function Save() {
		$id = $this->Id;
		$annee = Cadref::$Annee;

		if(empty($this->Inscription)) $this->Inscription = $annee;
		if(empty($this->Annee)) $this->Annee = $annee;
		if(!$id) {
			$a = Sys::getOneData('Cadref', 'Adherent', 0, 1, 'DESC', 'Numero');
			$this->Numero = sprintf('%06d', intval($a->Numero) + 1);
		}
		return parent::Save();
	}

	function GetFormInfo($annee) {
		$c = !$this->CheckCertificat();
		$s = $this->getOneChild('AdherentAnnee/Annee='.$annee);
		return array('Cotisation'=>$s->Cotisation, 'Cours'=>$s->Cours, 'Visites'=>$s->Visites, 'Reglement'=>$s->Reglement, 'Differe'=>$s->Differe,
			'Regularisation'=>$s->Regularisation, 'Solde'=>$s->Solde, 'NotesAnnuelles'=>$s->NotesAnnuelles, 'Adherent'=>$s->Adherent,
			'ClasseId'=>$s->ClasseId, 'AntenneId'=>$s->AntenneId, 'CotisationAnnuelle'=>Cadref::$Cotisation, 'certifInvalide'=>$c);
	}

	// $mode :
	// true => save adherent
	// false => save inscription ou reservation	
	function SaveAnnee($data, $mode = true) {
		$annee = Cadref::$Annee;
		$cours = 0;
		$visit = 0;
		$regle = 0;
		$diffe = 0;
		$ins = $this->getChildren('Inscription/Annee='.$annee);
		foreach($ins as $in) {
			if(!$in->Attente && !$in->Supprime) $cours += $in->Prix - $in->Reduction1 - $in->Reduction2;
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

		if($mode) {
			$a->Adhrent = $data->Adherent;
			$a->CalsseId = $data->ClasseId;
			$a->AntenneId = $data->AntenneId;
			$a->NotesAnnuelles = $data->NotesAnnuelles;
		}

		if(!$a->Cotisation && $data->Cotisation) $a->DateCotisation = time();
		$a->Cotisation = $data->Cotisation ? $data->Cotisation : 0;
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
		$this->SaveAnnee($data, false);
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
				if($ret)
						return array(
						'data'=>'Inscription enregistrée',
						'callBack'=>array(
							'nom'=>'refreshAdherent',
							'args'=>array()
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
						'args'=>array()
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

			if($id == 0) {
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
			$o->Reduction1 = $ins['Reduction1'];
			$o->Redcution2 = $ins['Reduction2'];
			$o->Utilisateur = Sys::$User->Initiales;
			$o->Save();

			// classe : inscrits/attentes/suppmime
			if($supprime != $o->Supprime || $attente != $o->Attente) {
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
			$r->Encaisse = 0;
			$r->Utilisateur = Sys::$User->Initiales;
			$r->Save();
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
			$o->Save();
		}
		$this->Save();
		return true;
	}

	function CheckCertificat() {
		$annee = Cadref::$Annee;
		$max = ($annee + 1).'0630';
		$dat = $this->Certificat;
		if(!empty($dat) && date('Ymd', $dat) >= $max) return true;

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
		if($cert && (empty($dat) || date('Ymd', $dat) < $max)) return false;
		return true;
	}

	function PrintCarte($recto = false) {
		require_once ('PrintCarte.class.php');

		$annee = Cadref::$Annee;
		$ins = $this->getChildren('Inscription/Annee='.$annee);

		$pdf = new PrintCarte($this, $recto);
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle('Carte'.$this->Numero);

		$pdf->AddPage();
		$pdf->PrintLines($ins);

		$file = 'Home/tmp/Carte'.$this->Numero.'.pdf';
		$pdf->Output(getcwd().'/'.$file);
		$pdf->Close();

		return array('pdf'=>$file);
	}

	function PrintAdherent($obj) {
		$menus = ['impressionslisteadherents', 'impressionscertificatesmedicaux', 'impressionsfichesincompletes'];
		$mode = array_search($obj['CurrentUrl'], $menus);

		$annee = Cadref::$Annee;
		$sql = '';
		$whr = '';

		switch($mode) {
			case o:
				$typAdh = isset($obj['typeAdherent']) ? $obj['typeAdherent'] : '';
				$contenu = isset($obj['Contenu']) ? $obj['Contenu'] : '';
				$rupture = isset($obj['Rupture']) ? $obj['Rupture'] : '';
				$adherent = false;


				if($typAdh != '' || $contenu == 'Q' || $rupture == 'S') {
					$sql = "select distinct ";
					$adherent = true;
					$rupture = 'S';
				} else
						$sql = "select i.CodeClasse, i.ClasseId, n.AntenneId, i.Attente, i.DateAttente, d.Libelle as LibelleD, n.Libelle as LibelleN, ";

				$sql .= "e.Numero, e.Nom, e.Prenom, e.Adresse1, e.Adresse2, e.CP, e.Ville, e.Telephone1, e.Telephone2, e.Mail, e.ClasseId as Delegue";

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
			case 1:
				$contenu = 'N';
				$rupture = 'E'; // enseignant
				$antenne = 0;
				$an = $annee + 1;
				$sql = "
select distinct a.Numero, a.Nom, a.Prenom, a.Telephone1, a.Telephone2, a.Mail, 
a.Certificat, i.CodeClasse, i.ClasseId, d.Libelle as LibelleD, n.Libelle as LibelleN
from `##_Cadref-Adherent` a
inner join `##_Cadref-Inscription` i on i.AdherentId=a.Id and i.Annee='2017'
inner join `##_Cadref-Classe` c on c.Id=i.ClasseId
inner join `##_Cadref-Niveau` n on n.Id=c.NiveauId
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
left join `##_Cadref-ClasseEnseignants` ce on ce.Classe=c.Id
left join `##_Cadref-Enseignant` e on e.Id=ce.EnseignantId
where a.Annee='$annee' and i.Supprime=0 and i.Attente=0 and d.Certificat<>0 and (a.Certificat is null or a.Certificat<unix_timestamp('$an-07-01'))
order by e.Nom,i.CodeClasse,a.Nom,a.Prenom";
				break;
			case 2:
				$contenu = 'N';
				$rupture = 'S';
				$antenne = 0;
				$sql = "
select a.Numero, a.Nom, a.Prenom, a.Telephone1, a.Telephone2, a.Mail
from `##_Cadref-Adherent` a
where a.Annee='$annee' and (a.Origine='' or a.SituationId='' or a.ProfessionId='' or a.Sexe='' or a.Naissance='')
order by a.Nom, a.Prenom";
				break;
		}


		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(!$pdo) return false;

		if($contenu != 'Q') {
			require_once ('PrintAdherent.class.php');

			$pdf = new PrintAdherent($mode, $contenu, $rupture, $antenne, $attente);
			$pdf->SetAuthor("Cadref");
			$pdf->SetTitle('Liste adherents');

			$pdf->AddPage();
			$pdf->PrintLines($pdo);

			$file = 'Home/tmp/ListeAdherent_'.date('YmdHi').'.pdf';
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

			$file = 'Home/tmp/EtiquetteAdherent_'.date('YmdHi').'.pdf';
			$pdf->Output(getcwd().'/'.$file);
			$pdf->Close();
		}

		return array('pdf'=>$file, 'sql'=>$sql);
	}

	function PrintAttestation($params) {
		$sql = "
select distinct h.Numero,h.Nom,h.Prenom,h.Adresse1,h.Adresse2,h.CP,h.Ville,a.Cotisation
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
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql.$where);
			$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
			if(!$pdo) return false;

			$file = $this->imprimeAttestation($pdo, $annee, $fisc);
			return array('pdf'=>$file, 'sql'=>$sql);
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

					$file = $this->imprimeAttestation($pdo, $annee, $fisc);
					return array(
						'data'=>'<a id="displayAttestation" href="'.$file.'" target="_blank" ng-click="attestationAdherent(\''.$file.'\')">Attestation imprimée</a>',
						'callBack'=>array(
							'nom'=>'displayAttestation',
							'title'=>'Attestation 3',
							'args'=>array(),
							'sql'=>$sql
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

	private function imprimeAttestation($list, $annee, $fisc) {
		require_once ('PrintAttestation.class.php');

		$pdf = new PrintAttestation($annee, $fisc);
		$pdf->SetAuthor("Cadref");
		$pdf->SetTitle('Attestations fiscales');

		foreach($list as $l)
			$pdf->PrintPage($l);

		$file = 'Home/tmp/Attestation'.date('YmdHi').'.pdf';
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
							'args'=>array(),
							'sql'=>$sql
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

		$file = 'Home/tmp/Cheque'.date('YmdHi').'.pdf';
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
					'callNext'=>array(
						'nom'=>'SendMessage',
						'title'=>'Message suite',
						'needConfirm'=>false
					)
				);
				break;
			case 1:
				$ret = Cadref::SendMessage($params['Msg']);
				return array(
					'data'=>'Message envoyé',
					'params'=>$params['Msg'],
					'success'=>true,
					'callNext'=>false
				);
				break;
		}
	}

	function GetListClasses($mode, $obj) {
		$annee = Cadref::$Annee;
		$filter = str_replace('&', '', $obj['Filter']);
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
from `##_Cadref-Niveau` n
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
inner join `##_Cadref-Section` s on s.Id=d.SectionId
inner join `##_Cadref-Classe` c on c.NiveauId=n.Id and c.AntenneId=n.AntenneId
where n.AntenneId=$antId and c.Annee='$annee' and s.Libelle like '%$filter%'
order by s.Libelle";
				break;
			case 'discipline':
				$sql = "
select distinct d.Id, d.Libelle
from `##_Cadref-Discipline` d
inner join `##_Cadref-Niveau` n on n.DisciplineId=d.Id and n.AntenneId=$antId
inner join `##_Cadref-Classe` c on c.NiveauId=n.Id and c.AntenneId=n.AntenneId
where d.SectionId=$secId  and c.Annee='$annee'
order by d.Libelle";
				break;
			case 'classe':
				$sql = "
select distinct c.Id, concat(d.Libelle,' ',n.Libelle) as Libelle, 
j.Jour, c.HeureDebut, c.HeureFin, c.CycleDebut, c.CycleFin,
c.Places, c.Inscrits, c.Attentes
from `##_Cadref-Niveau` n
inner join `##_Cadref-Discipline` d on d.Id=n.DisciplineId
inner join `##_Cadref-Classe` c on c.NiveauId=n.Id
left join `##_Cadref-Jour` j on j.Id=c.JourId
where n.DisciplineId=$disId and n.AntenneId=$antId and c.Annee='$annee'
order by d.Libelle, n.Libelle, c.JourId, c.HeureDebut";
				break;
		}
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$data = array();
		foreach($pdo as $p) {
			$data[] = $p;
		}
		return array('data'=>$data, 'sql'=>$sql);
	}


}
