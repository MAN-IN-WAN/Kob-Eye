<?php

class DevisTete extends genericClass {

	function __construct($Mod, $Tab) {
		genericClass::__construct($Mod, $Tab);
	}

	/*
	 *
	 */
	function Save($mode=false) {
		if($this->EtatDevis == 3) $err = "Devis livré.\nModification impossible.";
		if($this->EtatDevis == 4) $err = "Devis Annulé.\nModification impossible.";
		if($err) return array(array('method', 1, '', '', '', '', '', array(array('message'=>$err)), null));
		
		$sts = array();
		$id = $this->Id;
		$confirme = $this->Confirme;
		if($this->Reference == '') $this->Reference = $this->getNumber();
		if(! $this->EtatDevis) $this->EtatDevis = 1;
		$this->Confirme = $this->EtatDevis == 2 ? 1 : 0;
		$this->Annule = $this->EtatDevis == 4 ? 1 : 0;
		if($id) {
			$bl = $this->getChildren('BLTete');
			if(count($bl)) {
				$bl = $bl[0];
				$this->Preparation = 1;
			}
			else $bl = null;
		}
		genericClass::Save();

		if(! $mode || $this->Annule) {
			// delete old lines
			if($this->Id) {
				$lines = $this->getChildren('DevisLigne');
				foreach($lines as $line) $line->Delete();
				$lines = $this->getChildren('CommandeLigne:NOVIEW');
				foreach($lines as $line) {
					$elms = $line->getChildren('Element');
					foreach($elms as $elm) $elm->Delete();
					$line->Delete();
				}
			}
		}
		if(! $mode) {
			// save new lines
			$etat = $this->EtatDevis == 2 ? 1 : 0;
			$cli = Sys::getData('Repertoire', 'Tiers/'.$this->ClientId);
			$cli = $cli[0];
			$liv = Sys::getData('Repertoire', 'Tiers/'.$this->LivraisonId);
			$liv = $liv[0];
			$lines = $this->DevisLigne;
			foreach($lines as $l) {
				$d = genericClass::createInstance('Devis', 'DevisLigne');
				$d->addParent($this);
				$d->FamilleId = $l->FamilleId;
				$d->Designation = $l->Designation;
				$d->Quantite = $l->Quantite;
				$d->PrixUnitaire = $l->PrixUnitaire;
				$d->Remise = $l->Remise;
				$d->PrixNet = $l->PrixNet;
				$d->CodeTVA = $l->CodeTVA;
				$d->Save();
				if($d->FamilleId && $d->Quantite) {
					$f = Sys::getData('StockLocatif','Famille/'.$d->FamilleId,0,1);
					$c = Sys::getData('StockLocatif','Categorie/'.$f[0]->CategorieId,0,1);
					$mt = $c[0]->ModeTarif;
					if($mt == 1 || $mt == 2) {
						$cl = genericClass::createInstance('StockLogistique', 'CommandeLigne');
						$cl->addParent($this);
						$cl->addParent($cli,'ClientId');
						$cl->addParent($liv,'LivraisonId');
						if($bl) $cl->addParent($bl);
						//$cl->addParent($f[0]);
						$cl->FamilleId = $d->FamilleId;
						$cl->Designation = $d->Designation;
						$cl->Quantite = $d->Quantite;
						$cl->DateDebut = $this->DateDebut;
						$cl->DateFin = $this->DateFin;
						$cl->DateLivraison = $this->DateLivraison;
						$cl->DateReprise = $this->DateReprise;
						$cl->ModeTarif = $c[0]->ModeTarif;
						$cl->Save(true);
						$qt = $cl->Quantite;
						if($mt == 1) {
							for($i = 0; $i < $qt; $i++) {
								$elm = genericClass::createInstance('StockLogistique', 'Element');
								$elm->Quantite = 1;
								$elm->addParent($cl);
								$elm->addParent($this);
								if($bl) $elm->addParent($bl);
								$elm->DateLivraison = $this->DateLivraison;
								$elm->DateReprise = $this->DateReprise;
								$elm->DateDepart = null;
								$elm->DateRetour = null;
								$elm->Etat = $etat;
								$elm->Save(true);
							}
						} elseif($mt == 2) {
							$elm = genericClass::createInstance('StockLogistique', 'Element');
							$elm->Quantite = $qt;
							$elm->addParent($cl);
							$elm->addParent($this);
							if($bl) $elm->addParent($bl);
							$elm->DateLivraison = $this->DateLivraison;
							$elm->DateReprise = $this->DateReprise;
							$elm->DateDepart = null;
							$elm->DateRetour = null;
							$elm->Save(true);
						}
					}
				}
			}
			if($this->Confirme) {
				if($bl) $sts[] = array('edit', 1, $bl->Id, 'StockLogistique', 'BLTete', '', '', null, null);
				else $sts[] = array('edit', 1, '', 'StockLogistique', 'CommandeLigne', '', '', null, null);
			}
		}
		if($this->Confirme && !$confirme) {
			$rol = 'LOC_LOGISTIQUE';
			$usr = null;
			AlertUser::addAlert('Devis confirmé : '.$this->ClientIntitule.' / '.$this->LivraisonIntitule,'DV'.$this->Id,'Devis','DevisTete',$this->Id,$usr,$rol,null);
		}
		$res = array('Reference'=>$this->Reference,'EtatDevis'=>$this->EtatDevis);
		$sts[] = array($id ? 'edit' : 'add', 1, $this->Id, 'Devis', 'DevisTete', '', '', null, array('dataValues'=>$res));
		return $sts;
	}

	function Delete() {
		if($this->Confirme) throw new Exception("Devis comfirmé.\nSuppression impossilble");
		$lines = $this->getChilds('DevisLigne');
		foreach($lines as $line) {
			$elms = $line->getChildren('Element');
			foreach($elms as $elm) $elm->Delete();
			$line->Delete();
		}
		return genericClass::Delete();
	}

	/*
	 * numerotation
	 */
	private function getNumber() {
		$code = 'DEVIS_'.$this->Societe;
		$rec = Sys::$Modules['Devis']->callData('Constante/Code='.$code);
		Sys::$Modules["Devis"]->Db->clearLiteCache(); 
		$cons = genericClass::createInstance('Devis', $rec[0]);
		$cons->Valeur = sprintf('%06d', $cons->Valeur + 1);
		$cons->Save();
		return $this->Societe.$cons->Valeur;
	}

	/*
	 * confirm the quote
	 */
	function DevisConfirme($chef, $tel, $notes) {
		$sts = array();
		$this->ChefRayon = $chef;
		$this->TelephoneRayon = $tel;
		$this->Commentaires = $notes;
		$this->EtatDevis = 2;
		$this->Confirme = 1;
		$this->Save(true);
		
		// echeances
		$lines = $this->getChilds('DevisEcheance');
		foreach($lines as $line) $line->Delete();
		if($this->Mensualites) {
			$eche = $this->DateDebut;
			if(date('j', $eche) >= 25) $eche = strtotime("+1 month", $eche);
			$n = $this->NombreEcheance;
			for($i = 0; $i < $n; $i++) {
				$e = genericClass::createInstance('Devis', 'DevisEcheance');
				$e->addParent($this);
				$e->Echeance = $eche;
				$e->Numero = $i + 1;
				$e->Save();
				$eche = strtotime("+1 month", $eche);
			}
			$sts[] = array('add', 1, '', 'Devis', 'DevisEcheance', '', '', null, null);
		}
		$sts[] = array('edit', 1, '', 'StockLogistique', 'CommandeLigne', '', '', null, null);
		$res = array('EtatDevis'=>2, 'Confirme'=>1, 'ChefRayon'=>$chef, 'TelephoneRayon'=>$tel, 'Commentaires'=>$notes);
		$sts[] = array('edit', 1, $this->Id, 'Devis', 'DevisTete', '', '', null, array('dataValues'=>$res));
		return WebService::WSStatusMulti($sts);
	}


	function DevisAnnule() {
		if($this->EtatDevis > 2) {
			$err = array(array('message'=>"Impossible d'annuler ce devis"));
			return WebService::WSStatus('method', 0, '', '', '', '', '', $err, null);
		}
		$this->EtatDevis = 4;
		$this->Confirme = 0;
		$this->Save(true);
		$res = array('EtatDevis'=>4, 'Confirme'=>0);
		$st = array(array('edit', 1, $this->Id, 'Devis', 'DevisTete', '', '', array(), array('dataValues'=>$res))); 
		return WebService::WSStatusMulti($st);
	}


	/*
	 * print quotes
	 */
	function PrintDocuments($ids, $fond = true) {
		require_once ('Class/Lib/fpdf_merge.php');
		$pdf = array();
		if(! isset($ids))
			$ids = array($this->Id);
		foreach($ids as $id) {
			$rec = Sys::$Modules['Devis']->callData("DevisTete/$id", false, 0, 1, '', '', '');
			if(! sizeof($rec))
				continue;
			$doc = genericClass::createInstance('Devis', $rec[0]);
			$pdf[] = $doc->PrintDocument($fond);
		}
		if(sizeof($pdf) > 0) {
			$file = 'Home/tmp/doc'.rand(0, 2000).'.pdf';
			$merge = new FPDF_Merge();
			foreach($pdf as $doc)
				$merge->add($doc);
			$merge->output($file);
			$res = array('printFiles'=> array($file));
		} else
			$res = null;
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}

	private function PrintDocument($fond = true) {
		require_once ('DevisEtat.class.php');
		$lines = Sys::$Modules['Devis']->callData("DevisTete/$this->Id/DevisLigne", false, '', '', 'ASC', 'Id');

		if($this->OperationSpeciale) {
			// TODO
		}

		$pdf = new DevisEtat($this, false, $fond, 'P', 'mm', 'A4');
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle('Devis_'.$this->Reference);

		$pdf->AddPage();
		$pdf->PrintLines($lines, false);
		$pdf->PrintTotals();
		// conditions de vente
		if($fond) {
			$pdf->mode = 2;
			$pdf->AddPage();
		}
		// save pdf
		$file = 'Home/Devis/Devis_'.$this->Reference.'.pdf';
		$pdf->Output($file);
		$pdf->Close();
		return $file;
	}

	/*
	 * date de livraison
	 */
	function DateDebut($deb, $fin) {
		$res = null;
		if($deb) {
			if($fin < $deb) $fin = null;
			$d = date('w', $deb) == 1 ? 2 : 1;
			$liv = strtotime("-$d day", $deb);
			if($fin) $td = $this->TarifDuree($deb, $fin);
			$data = array('DateFin'=>$fin, 'DateLivraison'=>$liv, 'TarifDureeId'=>$td);
			$res = array('dataValues'=>$data);
		}
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}

	/*
	 * date de reprise
	 */
	function DateFin($deb, $fin) {
		$res = null;
		if($fin) {;
			$td = $this->TarifDuree($deb, $fin);
			$d = date('w', $fin) == 6 ? 2 : 1;
			$fin = strtotime("+$d day", $fin);
			$mens = $td[1] >= 6 ? 1 : 0;
			$data = array('DateReprise'=>$fin,'TarifDureeId'=>$td[0],'NombreEcheance'=>$td[1],'Mensualites'=>$mens);
			$res = array('dataValues'=>$data);
		}
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}

	/*
	 * duree de location
	 */
	private function TarifDuree($deb, $fin) {
		if(! $deb || ! $fin) return array(0,0);
		$d = floor(($fin - $deb) / 86400);
		if($d < 0) return array(0,0);
		if($d >= 28) {
			$moi = 1;
			while($fin > strtotime("+$moi month", $deb)) $moi++;
			$moi--;
			$m = strtotime("+$moi month", $deb);
			if($fin >= strtotime('+3 week', $m)) $moi++;
			elseif($fin >= strtotime('+1 week', $m)) $demi = 0.5;
			if($moi <= 6) $tar = 6 + $moi;
			elseif($moi < 9) $tar = 12;
			elseif($moi < 12) $tar = 13;
			else $tar = 14;
			$moi += $demi;
		}
		else {
			$days = array(3,7,10,14,18,21,28);
			for($i = 0; $d >= $days[$i] && $i < 7; $i++);
			$tar = $i + 1;
		}
		return array($tar,$moi);
	}


	function Transport($cp, $lines, $client) {
		if(! $cp || ! $client) return WebService::WSStatus('method', 1, '', '', '', '', 0, null, null);
		$qte = 0;
		$trans = null;
		foreach($lines as $line) {
			if($line->Transport)
				$trans = $line;
			elseif($line->ModeTarif == '1')
				$qte += $line->Quantite;
		}
		if(! $qte) return WebService::WSStatus('method', 1, '', '', '', '', 0, null, null);
		
		if(! $trans) {
			$trans = new stdClass();
			$trans->Id = time();
			$rec = Sys::$Modules['StockLocatif']->callData('StockLocatif/Famille/Transport=1');
			$fam = genericClass::createInstance('StockLocatif', $rec[0]);
			$trans->FamilleId = $fam->Id;
			$trans->Famille = $fam->Famille;
			$trans->CategorieId = $fam->CategorieId;
			$trans->Transport = 1;
			$trans->Designation = $fam->Designation;
			$trans->CodeTVA = '1'; //$fam->CodeTVA;
			$trans->module = 'Devis';
			$trans->objectClass = 'DevisLigne';
		}
		$cp = substr($cp, 0, 2);
		$rec = Sys::$Modules['StockLocatif']->callData("StockLocatif/Transport/Departement=$cp&ClientId=$client",false,0,1);
		if(! is_array($rec) || ! count($rec))
			$rec = Sys::$Modules['StockLocatif']->callData("StockLocatif/Transport/Departement=$cp&ClientId=0",false,0,1);
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxx:$cp:$client",$rec);
		$prix = genericClass::createInstance('StockLocatif', $rec[0]);
		$trans->Quantite = 1;
		$trans->PrixNet = $trans->PrixUnitaire = $prix->Prix + ($prix->Supplement * ($qte - 1));
		$trans->objectClass = 'DevisLigne';
		return WebService::WSStatus('item', 1, '', 'Devis', 'DevisLigne', '', '', null, array(item=>$trans));
	}


	function TotalDevis($lines, $tauxRem, $ctva, $montRem, $eche, $mens, $mode) {
		$ht = $this->calculBruts($lines);
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxx1:",$ht);
		$brut = $ht[0] + $ht[1];
		switch($mode) {
			case 1:
				$montRem = round($brut * $tauxRem / 100, 2);
				break;
			case 2:
				$tauxRem = round($montRem / $brut * 100, 2);
				break;
		}
		$net = $brut - $montRem;		
		$rec = Sys::$Modules['Devis']->callData("TVA/Code=$ctva", false, 0, 1, '', '', 'Taux');
		$ttva = $rec[0]['Taux'];
		$mtva = round($net * $ttva / 100, 2);
		$ttc = $net + $mtva;
		if($mens) $ech = $this->calculEcheances($ht[0], $ht[1], $eche, $tauxRem);
		else $ech = array(0, 0, 0);
		$data = array('MontantHTBrut'=>$brut, 'MontantHTNet'=>$net, 'MontantTVA'=>$mtva, 'MontantTTC'=>$ttc,
					'RemiseTaux'=>$tauxRem, 'RemiseMontant'=>$montRem,'PremiereEcheance'=>$ech[0],
					'AutresEcheance'=>$ech[1],'DerniereEcheance'=>$ech[2]);
		$res = array('dataValues'=>$data);
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}


	private function calculBruts($lines) {
		$loc = 0;
		$div = 0;
		foreach($lines as $l) {
			$ht = round($l->PrixNet * $l->Quantite, 2);
			if($l->ModeTarif == 1)
				$loc += $ht;
			else
				$div += $ht;
		}
		return array($loc, $div);
	}



	private function calculEcheances($loc, $div, $eche, $rem) {
		if($rem) {
			$loc -= round($loc * $rem / 100, 2);
			$div -= round($div * $rem / 100, 2);
		}
		$prem = $loc + $div;
		if(floor($eche) < $eche) $der = round($loc / 2, 2);
		else $der = 0;
		return array($prem, $loc, $der);
	}


	private function calculTotaux($brut, $tauxRem, $ctva, $montRem, $mode) {
		switch($mode) {
			case 1:
				$montRem = round($brut * $tauxRem / 100, 2);
				break;
			case 2:
				$tauxRem = round($montRem / $brut * 100, 2);
				break;
		}
		$net = $brut - $montRem;
		$rec = Sys::$Modules['Devis']->callData("TVA/Code=$ctva", false, 0, 1, '', '', 'Taux');
		$ttva = $rec[0]['Taux'];
		$mtva = round($net * $ttva / 100, 2);
		$ttc = $net + $mtva;
		return array(tauxRem=>$tauxRem, montRem=>$montRem, net=>$net, tva=>$mtva, ttc=>$ttc);
	}


	function CreateInvoices($ids, $date, $force=false, $cedee=true) {
		$set = true;
		if(! isset($ids)) {
			$set = false;
			$ids = array($this->Id);
		}
		$res = null;
		foreach($ids as $id) {
			$rec = Sys::$Modules['Devis']->callData("DevisTete/$id", false, 0, 1);
			if(! sizeof($rec)) continue;
			$doc = genericClass::createInstance('Devis', $rec[0]);
			if(! $force && $doc->Facture) continue;
			$doc->CreateInvoice($date, $cedee);
			if(! $set) $res = array(dataValues=>array('Facture'=>$this->Facture,'Factures'=>$this->Factures));
		}
		$st = array( array('method', 1, '', 'Devis', 'DevisTete', '', '', null, $res), array('method', 1, '', 'Devis', 'FactureTete', '', '', null, $res));
		return WebService::WSStatusMulti($st);
		//	function WSStatus($type, $success, $id, $module, $object, $parent, $parentId, $errors, $result) {
	}

	function CreateInvoice($date, $cedee) {
		$dv = $this;
		$fact = $dv->invoiceHeader($date);

		$fact->MontantHTBrut = $dv->MontantHTBrut;
		$fact->RemiseTaux = $dv->RemiseTaux;
		$fact->RemiseMontant = $dv->RemiseMontant;
		$fact->MontantHTNet = $dv->MontantHTNet;
		$fact->CodeTVA = $dv->CodeTVA;
		$fact->MontantTVA = $dv->MontantTVA;
		$fact->MontantTTC = $dv->MontantTTC;
		$fact->ModeReglement = $dv->ModeReglement;
		$fact->Reglement = 0;
		$fact->Echeance = $fact->GetEcheance($date, $fact->ModeReglement);
		$fact->NombreEcheance = $dv->NombreEcheance;
		$fact->PremiereEcheance = $dv->PremiereEcheance;
		$fact->AutresEcheance = $dv->AutresEcheance;
		$fact->DerniereEcheance = $dv->DerniereEcheance;

		// lignes facture
		$txt = 'Selon notre Devis n° '.$dv->Reference.' du '.date('d/m/y', $dv->Date)."\n ";
		if($dv->OperationId) {
			$rec = Sys::$Modules['Devis']->callData('OperationSpeciale/'.$dv->OperationId, false, 0, 1);
			if(count($rec)) $this->invoiceLine($fact, $rec[0]['Designation']);
		}
		if($dv->DateDebut)
			$txt .= 'Location du '.date('d/m/y', $dv->DateDebut).' au '.date('d/m/y', $dv->DateFin)."\n ";
		$this->invoiceLine($fact, $txt);
		// lignes devis
		$lines = $this->getChilds('DevisLigne');
		foreach($lines as $line) {
			$l = new stdClass;
			$l->FamilleId = $line->FamilleId;
			$l->Designation = $line->Designation;
			$l->Quantite = $line->Quantite;
			$l->CodeTVA = $line->CodeTVA;
			$l->PrixUnitaire = $line->PrixUnitaire;
			$l->Remise = $line->Remise;
			$l->PrixNet = $line->PrixNet;
			$fact->FactureLigne[] = $l;
		}
		if($cedee) {
			$rec = Sys::$Modules['Devis']->callData('TexteLibre/Code=CEDEE', false, 0, 1);
			if(count($rec)) $this->invoiceLine($fact, $rec[0][Texte]);
		}
		$fact->Save();
		$rol = 'LOC_GESTION';
		$usr = null;
		AlertUser::addAlert('Devis facturé : '.$fact->ClientIntitule.' / '.$fact->LivraisonIntitule,'FA'.$fact->Id,'Devis','FactureTete',$fact->Id,$usr,$rol,null);
		if(! $dv->Facture) {
			$dv->Facture = 1;
			$dv->Factures = 1;
			$dv->Save(true);
		}
	}

	private function invoiceHeader($date, $livr=true) {
		$dv = $this;
		// client
		$cli = genericClass::createInstance('Repertoire', 'Tiers');
		$cli->initFromId($dv->ClientId);
		$liv = genericClass::createInstance('Repertoire', 'Tiers');
		$liv->initFromId($dv->LivraisonId);
		// entetes facture
		$fact = genericClass::createInstance('Devis', 'FactureTete');
		$fact->Date = $date;
		$fact->Type = 'F';
		$fact->Societe = $dv->Societe;
		$fact->CommercialId = $dv->CommercialId;
		$fact->ClientId = $dv->ClientId;
		$fact->Valide = 1;
		if(empty($cli->IntituleFac)) {
			$fact->ClientIntitule = $dv->ClientIntitule;
			$fact->ClientAdresse1 = $dv->ClientAdresse1;
			$fact->ClientAdresse2 = $dv->ClientAdresse2;
			$fact->ClientCodPostal = $dv->ClientCodPostal;
			$fact->ClientVille = $dv->ClientVille;
			$fact->ClientPays = $dv->ClientPays;
		} else {
			$fact->ClientIntitule = $cli->IntituleFac;
			$fact->ClientAdresse1 = $cli->AdresseFac1;
			$fact->ClientAdresse2 = $cli->AdresseFac2;
			$fact->ClientCodPostal = $cli->CodPostalFac;
			$fact->ClientVille = $cli->VilleFac;
			$fact->ClientPays = $cli->PaysFac;
		}
		if($livr) {
			$fact->LivraisonId = $dv->LivraisonId;
			$fact->LivraisonIntitule = $dv->LivraisonIntitule;
			$fact->LivraisonAdresse1 = $dv->LivraisonAdresse1;
			$fact->LivraisonAdresse2 = $dv->LivraisonAdresse2;
			$fact->LivraisonCodPostal = $dv->LivraisonCodPostal;
			$fact->LivraisonVille = $dv->LivraisonVille;
			$fact->LivraisonPays = $dv->LivraisonPays;
		}
		$fact->FactureLigne = array();
		return $fact;
	}

	private function invoiceLine($fact, $desi) {
		$l = new stdClass;
		$l->Designation = $desi;
		$l->Quantite = 0;
		$l->CodeTva = '';
		$fact->FactureLigne[] = $l;
	}
	
	
	// REMEMBER TO CHANGE ARGUMENTS IN DevisEcheance
	function ContractInvoices($ids, $date, $force) {
		$tb = array();
		foreach($ids as $id) {
			$ech = genericClass::createInstance('Devis', 'DevisEcheance');
			$ech->initFromId($id);
			if($ech->Facture && ! $force) continue;
			$dev = $ech->getParents('DevisTete');
			$dev = $dev[0];
			$tb[] = array('ech'=>$ech, 'dev'=>$dev);
		}
		$r = usort($tb, array('DevisTete','sortContracts'));
		$n = count($tb);
		for($i = 0; $i < $n;) {
			$t = $tb[$i];
			$dev = $t['dev'];
			$ech = $t['ech'];
			$cli = genericClass::createInstance('Repertoire', 'Tiers');
			$cli->initFromId($dev->ClientId);
			if($cli->FactureGroupee) $i = $this->groupedInvoice($date, $tb, $i, $n, $cli->Id, $cli->ModeReglement);
			else {
				$dev->contractInvoice($date, $ech->Echeance, $ech->Numero, $ech->Facture);
				$ech->Facture = 1;
				$ech->Save();
				$i++;
			}
		}
		$st = array(array('method', 1, '', 'Devis', 'FactureTete', '', '', null, null),
					array('method', 1, '', 'Devis', 'DevisEcheance', '', '', null, null));
		return WebService::WSStatusMulti($st);
	}
	
	function sortContracts($a, $b) {
		$a = $a['dev'];
		$b = $b['dev'];
		$c = strcmp($a->ClientId, $b->ClientId);
		if(! $c) $c = strcmp($a->Reference, $b->Reference);
		return $c;
	}
	
	private function contractInvoice($date, $dech, $nfac, $refac) {
		$dv = $this;
		$nech = $dv->NombreEcheance;
		$demi = $nfac > $nech;
		$fact = $dv->invoiceHeader($date);
		
		$lines = $this->getChilds('DevisLigne');
		$brut = $this->computeLines($lines, $nfac, $demi);
		$net = $brut - round($brut * $dv->RemiseTaux / 100, 2);
		$rec = Sys::$Modules['Devis']->callData('TVA/Code='.$dv->CodeTVA, false, 0, 1, '', '', 'Taux');
		$ttva = $rec[0]['Taux'];
		$mtva = round($net * $ttva / 100, 2);
		$ttc = $net + $mtva;
		$fact->MontantHTBrut = $brut;
		$fact->RemiseTaux = $dv->RemiseTaux;
		$fact->RemiseMontant = $dv->RemiseMontant;
		$fact->MontantHTNet = $net;
		$fact->CodeTVA = $dv->CodeTVA;
		$fact->MontantTVA = $mtva;
		$fact->MontantTTC = $ttc;
		$fact->ModeReglement = $dv->ModeReglement;
		$fact->Reglement = 0;
		$fact->Echeance = $fact->GetEcheance($date, $fact->ModeReglement);
		$fact->Verify();

		$txt = 'CONTRAT LONGUE DUREE N° '.$dv->Reference.' DU '.date('d/m/y', $dv->Date);
		if($dv->DateDebut)
			$txt .= "\nLOCATION DU ".date('d/m/y', $dv->DateDebut).' AU '.date('d/m/y', $dv->DateFin);
		$txt .= "\n ";
		$this->invoiceLine($fact, $txt);
		$this->contractLines($fact, $lines, $nfac, $demi);
		$this->contractMonth($fact, date('n', $dech) - 1);
		$fact->Save();
		if(! $refac) {
			$dv->Facture = 1;
			$dv->Factures += 1;
			$dv->Save(true);
		}
	}

	private function computeLines($lines, $nfac, $demi) {
		$brut = 0;
		foreach($lines as $line) {
			if(! $line->ModeTarif && ! $line->Transport) continue;
			if($nfac > 1 && $line->ModeTarif != 1) continue;
			$pu = $line->PrixUnitaire; // * $line->Quantite;
			if($demi) $pu = round($pu / 2, 2);
			$pn = $pu - round($pu * $line->Remise / 100, 2);
			$brut += $pn * $line->Quantite;
		}
		return $brut;
	}
	
	private function contractLines($fact, $lines, $nfac, $demi) {
		foreach($lines as $line) {
			if(! $line->ModeTarif && ! $line->Transport) continue;
			if($nfac > 1 && $line->ModeTarif != 1) continue;
			$pu = $line->PrixUnitaire; // * $line->Quantite;
			if($demi) $pu = round($pu / 2, 2);
			$pn = $pu - round($pu * $line->Remise / 100, 2);
			$l = new stdClass;
			$l->FamilleId = $line->FamilleId;
			$l->Designation = $line->Designation;
			$l->Quantite = $line->Quantite;
			$l->CodeTVA = $line->CodeTVA;
			$l->PrixUnitaire = $pu;
			$l->Remise = $line->Remise;
			$l->PrixNet = $pn;
			$fact->FactureLigne[] = $l;
		}
	}

	private function contractMonth($fact, $m) {
		$month = array('JANVIER','FEVRIER','MARS','AVRIL','MAI','JUIN','JUILLET','AOUT','SEPTEMBRE','OCTOBRE','NOVEMBRE','DECEMBRE');
		$txt = "\nECHEANCE DU MOIS D".(($m==3 || $m==7 || $m==9) ? "'" : "E ").$month[$m];
		$this->invoiceLine($fact, $txt);
	}

	private function groupedInvoice($date, $tb, $i, $n, $cli, $reg) {
		$brut = 0;
		$first = true;
		while($i < $n) {
			$t = $tb[$i];
			$ech = $t['ech'];
			$dv = $t['dev'];
			if($dv->ClientId != $cli) break;
			$nech = $dv->NombreEcheance;
			$nfac = $ech->Numero;
			$demi = $nfac > $nech;
			if($first) {
				$mois = date('n', $ech->Echeance) - 1;
				$fact = $dv->invoiceHeader($date, false);
				$rec = Sys::$Modules['Devis']->callData('TVA/Code='.$dv->CodeTVA,false,0,1,'','','Taux');
				$ttva = $rec[0]['Taux'];
				$this->invoiceLine($fact, 'CONTRATS DE LOCATION LONGUE DUREE');
				$first = false;
			}
			$lines = $dv->getChilds('DevisLigne');
			$brut += $dv->computeLines($lines, $nfac, $demi);
			$txt = "\nN° ".$dv->Reference.' '.$dv->LivraisonIntitule;
			$txt .= ' du '.date('d/m/y', $dv->DateDebut).' au '.date('d/m/y', $dv->DateFin);
			$dv->invoiceLine($fact, $txt);
			$dv->contractLines($fact, $lines, $nfac, $demi);
			if(! $ech->Facture) {
				$dv->Facture = 1;
				$dv->Factures += 1;
				$dv->Save(true);
				$ech->Facture = 1;
				$ech->Save();
			}
			$i++;
		}
		$dv->contractMonth($fact, $mois);
		//
		$net = $brut;
		$mtva = round($net * $ttva / 100, 2);
		$ttc = $net + $mtva;
		$fact->MontantHTBrut = $brut;
		$fact->RemiseTaux = 0;
		$fact->RemiseMontant = 0;
		$fact->MontantHTNet = $net;
		$fact->CodeTVA = $dv->CodeTVA;
		$fact->MontantTVA = $mtva;
		$fact->MontantTTC = $ttc;
		$fact->ModeReglement = $dv->ModeReglement;
		$fact->Reglement = 0;
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxx",$fact);
		$fact->Echeance = $fact->GetEcheance($date, $fact->ModeReglement);
		$fact->Verify();
		$fact->Save();
		return $i;
	}

	function createAlerts($time) {
		$d = strtotime(date('Ymd',$time));
		$b = strtotime('+1 day',$d);
		$e = strtotime('+1 day',$b);
		$rec = Sys::getData('Devis',"DevisTete/DateLivraison<$b&DateLivraison<$e&EtatDevis=2&Preparation=0");
		foreach($rec as $rc)
			AlertUser::addAlert('Devis confirmé non préparé : '.$rc->ClientIntitule.' / '.$rc->LivraisonIntitule,'DV'.$rc->Id,'Devis','DevisTete',$rc->Id,null,'LOC_LOGISTIQUE',null);

		$b = strtotime('-2 week',$d);
		$e = strtotime('+1 day', $b);
		$rec = Sys::getData('Devis',"DevisTete/Date>=$b&Date<$e&EtatDevis<2");
		foreach($rec as $rc)
			AlertUser::addAlert('Devis en attente : '.$rc->ClientIntitule.' / '.$rc->LivraisonIntitule,'DV'.$rc->Id,'Devis','DevisTete',$rc->Id,null,'LOC_GESTION',null);
	}

	/**
	 * Moulinette qui renomme les references
	 */
/*
	public function moulinette() {
		echo "<h1>MOULINETTE SET REFERENCE DEVIS</h1>";
		$loca = $bopi = 1;
		for($i = 0; $i < 100; $i++) {
			$ts = Sys::$Modules["Devis"]->callData('DevisTete', false, $i * 100, 100, 'ASC', 'Date');
			foreach($ts as $t) {
				$d = genericClass::createInstance('Devis', $t);
				if(! $d->LivraisonId)
					$d->LivraisonId = $d->ClientId;
				if($d->Societe == "L") {
					$d->Reference = "L".sprintf("%06d", $loca);
					$loca++;
				} else {
					$d->Reference = "B".sprintf("%06d", $bopi);
					$bopi++;
				}
				echo "<li>".$d->Id." ".$d->Societe." ".$d->Reference."</li>";
				$d->Save(true);
			}
		}
		//mise à jour de la constante DEVIS_L
		$cl = Sys::$Modules["Devis"]->callData('Constante/Code=DEVIS_L', false, 0, 1);
		$cl = genericClass::createInstance('Devis', $cl[0]);
		$cl->Valeur = $loca;
		$cl->Save();
		//mise à jour de la constante DEVIS_B
		$cb = Sys::$Modules["Devis"]->callData('Constante/Code=DEVIS_B', false, 0, 1);
		$cb = genericClass::createInstance('Devis', $cb[0]);
		$cb->Valeur = $bopi;
		$cb->Save();
	}
*/
}
