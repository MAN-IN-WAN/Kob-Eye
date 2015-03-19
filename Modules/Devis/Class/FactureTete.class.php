<?php
class FactureTete extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	/*
	 * 
	 */
	function Save($mode=false) {
		$id = $this->Id;
		if($this->Valide && $this->Reference == '') $this->Reference = $this->getNumber();
		genericClass::Save();

		if(! $mode) {
			// delete old lines
			if($this->Id) {
				$lines = $this->getChilds('FactureLigne');
				foreach($lines as $line) $line->Delete();
			}
			// save new lines
			$lines = $this->FactureLigne;
			foreach($lines as $l) {
				$d = genericClass::createInstance('Devis', 'FactureLigne');
				$d->addParent($this);
				$d->FamilleId = $l->FamilleId;
				$d->Designation = $l->Designation;
				$d->Quantite = $l->Quantite;
				$d->PrixUnitaire = $l->PrixUnitaire;
				$d->Remise = $l->Remise;
				$d->PrixNet = $l->PrixNet;
				$d->CodeTVA = $l->CodeTVA;
				$d->Save();
			}
		}
		$res = array('Reference'=>$this->Reference);
		return array(array($id ? 'edit' : 'add', 1, $this->Id, 'Devis', 'FactureTete', '', '', null, array('dataValues'=>$res)));
	}
	
	
	function Delete() {
		if(! empty($this->Reference)) throw new Exception("Facture validée\nSuppression impossilble");
		$lines = $this->getChilds('FactureLigne');
		foreach($lines as $line)
			$line->Delete();
		return genericClass::Delete();
	}


	/*
	 * numerotation
	 */
	private function getNumber() {
		$code = 'FACT_'.$this->Societe;
		$rec = Sys::$Modules['Devis']->callData('Constante/Code='.$code);
		Sys::$Modules["Devis"]->Db->clearLiteCache(); 
		$cons = genericClass::createInstance('Devis', $rec[0]);
		$cons->Valeur = sprintf('%06d', $cons->Valeur + 1);
		$cons->Save();
		return $this->Societe.$cons->Valeur;
	}

	function GetEcheance($date, $regl) {
		$eche = $this->dateEcheance($date, $regl);
		$data = array(Echeance=>$eche);
		$res = array(dataValues=>$data);
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), $res);
	}
	
	/*
	 * total facture
	 */
	function TotalFacture($lines, $tauxRem,$ctva,$montRem,$mode) {
		$brut = 0;
		foreach($lines as $l) $brut += round($l->PrixNet * $l->Quantite, 2);
		switch($mode) {
			case 1: $montRem = round($brut * $tauxRem / 100, 2); break;
			case 2: $tauxRem = round($montRem / $brut * 100, 2); break;
		}
		$net = $brut - $montRem;
		$rec = Sys::$Modules['Devis']->callData("TVA/Code=$ctva", false, 0, 1, '', '', 'Taux');
		$ttva = $rec[0]['Taux'];
		$mtva = round($net * $ttva / 100, 2);
		$ttc = $net + $mtva;
		$data = array(MontantHTBrut=>$brut,MontantHTNet=>$net,MontantTVA=>$mtva,MontantTTC=>$ttc,RemiseTaux=>$tauxRem,RemiseMontant=>$montRem);
		$res = array(dataValues=>$data);
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), $res);
	}


	function TotalFactureXXX($tauxRem,$ctva,$montRem,$mode) {
		$brut = 0;
		if($this->Id) {
			$lines = Sys::$Modules['Devis']->callData("FactureTete/$this->Id/FactureLigne");
			foreach($lines as $l)
				$brut += round($l['PrixNet'] * $l['Quantite'], 2);
		}
		switch($mode) {
			case 1: $montRem = round($brut * $tauxRem / 100, 2); break;
			case 2: $tauxRem = round($montRem / $brut * 100, 2); break;
		}
		$net = $brut - $montRem;
		$rec = Sys::$Modules['Devis']->callData("TVA/Code=$ctva", false, 0, 1, '', '', 'Taux');
		$ttva = $rec[0]['Taux'];
		$mtva = round($net * $ttva / 100, 2);
		$ttc = $net + $mtva;
		$data = array(MontantHTBrut=>$brut,MontantHTNet=>$net,MontantTVA=>$mtva,MontantTTC=>$ttc,RemiseTaux=>$tauxRem,RemiseMontant=>$montRem);
		$res = array(dataValues=>$data);
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), $res);
	}

	
	/*
	 * calcul la date d'echeance selon le mode de reglement :
	 * CH60 = cheque a 60 jours
	 * TRFM = traite fin de mois
	 * VR30FM10 = Virement 30 jours fin de mois le 10
	 */
	private function dateEcheance($date, $regl) {
		$regl = strtoupper($regl);
		if(empty($regl)) return $date;
		$regl = substr($regl, 2);
		// comptant
		if(empty($regl)) return $date;
		// nombre de jours
		$nbr = '';
		$d = substr($regl, 0, 1);
		while(ctype_digit($d)) {
			$nbr .= $d;
			$regl = substr($regl, 1);
			$d = substr($regl, 0, 1);
		}
		// fin de mois ou de quinzaine
		$fm = substr($regl, 0, 2) == 'FM';
		$fq = substr($regl, 0, 2) == 'FQ';
		if($fm || $fq) {
			$regl = substr($regl, 2);
			$jour = '';
			$d = substr($regl, 0, 1);
			while(ctype_digit($d)) {
				$jour .= $d;
				$regl = substr($regl, 1);
				$d = substr($regl, 0, 1);
			}
		}
//$dd = date('d/m/y',$date);;
		if(!empty($nbr)) {
			$n = floor($nbr / 30);
			if($n) $date = strtotime("+$n month", $date);
			$n = $nbr - ($n * 30);
			if($n) $date = strtotime("+$n day", $date);
		}
//$dj = date('d/m/y',$date);
		if($fm) {
			$date = mktime(0,0,0,date('m',$date)+1,0,date('Y', $date));
			if(!empty($jour)) $date = strtotime("+$jour day", $date);
		}
		else if($fq) {
			if(date('d', $date) <= 15)
				$date = mktime(0,0,0,date('m',$date),15,date('Y', $date));
			else
				$date = mktime(0,0,0,date('m',$date)+1,0,date('Y', $date));
			if(!empty($jour)) $date = strtotime("+$jour day", $date);
		}
		return $date;
	}

/*
	//   for monthly billing
	function CreateInvoices($date, $begin, $finish) {
return WebService::WSStatus('method', 1, '', 'Devis', 'FactureTete', '', '', array(), null);
		$rec = Sys::$Modules['Devis']->callData("DevisTete/Date>=$begin&Date<=$finish&Etat=C&Facture=0",false,0,9999,'','','');
		foreach($rec as $rc) {
			// lecture devis
			$id = $rc['Id'];			
			$dev = Sys::$Modules['Devis']->callData("Devis/DevisTete/$id");
			$dv = $dev[0];
			// entetes facture
			$fact = genericClass::createInstance('Devis','FactureTete');
			$fact->Date =				$date;
			$fact->ClientId = 			$dv['ClientId'];
			$fact->ClientIntitule = 	$dv['ClientIntitule'];
			$fact->ClientAdresse1 = 	$dv['ClientAdresse1'];
			$fact->ClientAdresse2 = 	$dv['ClientAdresse2'];
			$fact->ClientAdresse3 = 	$dv['ClientAdresse3'];
			$fact->ClientCodPostal = 	$dv['ClientCodPostal'];
			$fact->ClientVille = 		$dv['ClientVille'];
			$fact->LivraisonId = 		$dv['LivraisonId'];
			$fact->LivraisonIntitule = 	$dv['LivraisonIntitule'];
			$fact->LivraisonAdresse1 = 	$dv['LivraisonAdresse1'];
			$fact->LivraisonAdresse2 = 	$dv['LivraisonAdresse2'];
			$fact->LivraisonAdresse3 = 	$dv['LivraisonAdresse3'];
			$fact->LivraisonCodPostal = $dv['LivraisonCodPostal'];
			$fact->LivraisonVille = 	$dv['LivraisonVille'];
			$fact->MontantHTBrut = 		$dv['MontantHTBrut'];
			$fact->RemiseTaux = 		$dv['RemiseTaux'];
			$fact->RemiseMontant =	 	$dv['RemiseMontant'];
			$fact->MontantHTNet = 		$dv['MontantHTNet'];
			$fact->CodeTVA = 			$dv['CodeTVA'];
			$fact->MontantTVA = 		$dv['MontantTVA'];
			$fact->MontantTTC = 		$dv['MontantTTC'];
			$fact->ModeReglement = 		$dv['ModeReglement'];
			$fact->Reglement = 			0;
			$fact->Echeance =	 		$this->Echeance($date, $fact->ModeReglement);
			$fact->CommercialId = 		$dv['CommercialId'];
			$fact->Save(true);
			// lignes facture
			$txt = 'Selon notre Devis n° '.$dv['Reference'].' du '.date('d/m/y',$dv['Date'])."\n";
			$txt .= 'Location du '.date('d/m/y',$dv['DateDebut']).' au '.date('d/m/y',$dv['DateFin'])."\n";
			$this->invoiceLine($fact, -1, $txt);
			// lignes devis
			$lines = Sys::$Modules['Devis']->callData("DevisTete/$id/DevisLigne");
			foreach($lines as $rl) {
				$line = genericClass::createInstance('Devis','FactureLigne');
				$line->addParent($fact);
				$line->Ordre =			$rl['Ordre'];
				$line->FamilleId =		$rl['FamilleId'];
				$line->Designation =	$rl['Designation'];
				$line->Quantite =		$rl['Quantite'];
				$line->CodeTVA =		$rl['CodeTVA'];
				$line->PrixUnitaire =	$rl['PrixUnitaire'];
				$line->Remise =			$rl['Ordre'];
				$line->PrixNet =		$rl['PrixNet'];
				$line->Save(true);
			}
			$devi = genericClass::createInstance('Devis',$dv);
			$devi->Facture = 1;
			$devi->Save(true);
		}
		return WebService::WSStatus('method', 1, '', 'Devis', 'FactureTete', '', '', array(), new stdClass());
	}

	private function invoiceLine($fact, $ordre, $desi) {
		$line = genericClass::createInstance('Devis', 'FactureLigne');
		$line->addParent($fact);
		$line->Ordre = $ordre;
		$line->Designation = $desi;
		$line->Quantite = 0;
		$line->CodeTva = '';
		$line->Save(true);
	}
*/

	/*
	 * print quotes
	 */
	function PrintDocuments($ids, $fond=true) {
		require_once('Class/Lib/fpdf_merge.php');
		$pdf = array();
		if(! isset($ids)) {
			$ids = array($this->Id);
			$data = array('Imprime'=>1);
		}
		foreach($ids as $id) {
			$rec = Sys::$Modules['Devis']->callData("FactureTete/$id",false,0,1,'','','');
			if(! sizeof($rec)) continue;
			$doc = genericClass::createInstance('Devis',$rec[0]);
			$pdf[] = $doc->PrintDocument($fond);
		}
		if(sizeof($pdf) > 0) {
			$file = 'Home/tmp/facture'.rand(0, 2000).'.pdf';
			$merge = new FPDF_Merge();
			foreach($pdf as $doc)
				$merge->add($doc);
			$merge->output($file);
			$res = array(printFiles=>array($file));
			if($data) {
				$this->Imprime = 1;
				$this->Save(true);
				$res['dataValues'] = $data;
			}
		}
		else $res = null;
		return WebService::WSStatus('method', 1, '', 'Devis', 'FactureTete', '', '', array(), $res);
	}

	private function PrintDocument($fond=true) {
		require_once('DevisEtat.class.php');
		$lines = Sys::$Modules['Devis']->callData("FactureTete/$this->Id/FactureLigne",false,'','','ASC','Id');
		
		$pdf = new DevisEtat($this,true,$fond,'P','mm','A4');
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle('Facture_'.$this->Reference);
		
		$pdf->AddPage();
		$pdf->PrintLines($lines, false);
		$pdf->PrintTotals();

		// echeancier
		if($this->NombreEcheance > 1) {
			$pdf->mode = 1;
			$pdf->AddPage();
		}
		// conditions de vente
		if($fond) {
			$pdf->mode = 2;
			$pdf->AddPage();
		}
		
		// save pdf
		$file = 'Home/Devis/Facture_'.$this->Reference.'.pdf';
		$pdf->Output($file);
		$pdf->Close();
		return $file;
	}
	
	
	function SalesBook($begin, $finish, $societe) {
		require_once('JournalVentes.class.php');
		$req = "FactureTete/Date>=$begin&Date<=$finish&Societe=$societe&Reference!=";
		$lines = Sys::$Modules['Devis']->callData($req,false,0,9999,'ASC','Reference','Id');
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxx $req", $lines);
		
		$pdf = new JournalVentes($begin,$finish,$societe,'P','mm','A4');
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle("JV $societe ".date('ymd_',$begin).date('ymd',$finish));
		
		$pdf->AddPage();
//for($i = 0; $i < 60; $i++) $lines[] = $lines[0]; 
		$pdf->PrintLines($lines, false);
		$pdf->PrintTotals();
		// save pdf
		$file = 'Home/tmp/facture'.rand(0, 2000).'.pdf';
		$pdf->Output($file);
		$pdf->Close();
		$res = array(printFiles=>array($file));
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), $res);
	}
	
	function NewPayment() {
		$it = new stdClass();
		$it->Id = $this->Id;
		$it->Reference = $this->Reference;
		$it->Date = $this->Date;
		$it->ClientIntitule = $this->ClientIntitule;
		$it->MontantTTC = $this->MontantTTC;
		$it->Mode = substr($this->ModeReglement, 0, 2);
		if($it->Mode === false) $it->Mode = '';
		$it->DateReglement = time();
		$it->Montant = $this->MontantTTC - $this->Reglement;
		$it->Solde = 1;
		$data = array($it);
//		return WebService::WSData($label, $start, $count, $rows, $query, $child, $filter, $module, $object, $data)
		return WebService::WSData('', 0, 1, 1, '', '', '', '', '', $data);
	}

	function SavePayment($mode,$date,$montant,$solde) {
		$p = genericClass::createInstance('Devis','Reglement');
		$p->addParent($this);
		$p->Mode = $mode;
		$p->DateReglement = $date;
		$p->Montant = $montant;
		$p->Solde = $solde;
		$ok = $p->Save(true);
		if($ok) $this->Reglements();
		return WebService::WSStatus('edit', $ok, '', 'Devis', 'FactureTete', '', '', $p->Error, new stdClass());
	}

	function Reglements() {
		$id = $this->Id;
		$rec = Sys::$Modules['Devis']->callData("FactureTete/$id/Reglement");
		$regl = 0;
		foreach($rec as $rc) {
			$regl += $rc['Montant'];
			if($rc['Solde'] == 1)  $this->Solde = 1;
		}
		$this->Reglement = $regl;
		if($regl >= $this->MontantTTC) $this->Solde = 1;
		genericClass::Save();
	}
	
	

}


?>