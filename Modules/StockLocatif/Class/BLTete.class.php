<?php
class BLTete extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	/*
	 * 
	 */
	function Save() {
		if($this->Reference == '') $this->Reference = $this->getNumber();
		genericClass::Save();
	}
	
	/*
	 * numerotation
	 */
	private function getNumber() {
	 	$code = 'LIVR_'.$this->Societe;
	 	$rec = Sys::$Modules['Devis']->callData('Constante/Code='.$code);
		$cons = genericClass::createInstance('Devis', $rec[0]);
		$cons->Valeur = sprintf('%06d', $cons->Valeur + 1);
		$cons->Save();
		return $this->Societe.$cons->Valeur;
	}

	function GetLivraison($mode,$deb=0,$fin=0,$mag='',$cp='',$vil='',$cli='') {
		$req = 'BLTete/';
		if($deb) $flt = "&DateLivraison>=$deb";
		if($fin) $flt .= "&DateLivraison<=$fin";
		if($mag) $flt .= "&LivraisonId~%$mag";
		if($cp) $flt .= "&CodPostal~$cp";
		if($vil) $flt .= "&Ville~%$vil";
		if($cli) $flt .= "&ClientId~%$cli";
		// 0 : destockage, 1 : preparation, 2 : tournee, 3 : livraison
		switch($mode) {
			case 0: $req .= "Livre=0$flt"; break;
			case 1: $req .= "Livre=0&Destockage=1$flt"; break;
			case 2: $req .= "Livre=0&Tournee=0$flt"; break;
		}
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxx  $req" );
		$items = array();
		$rec = Sys::$Modules['StockLogistique']->callData($req, false, 0, 9999, 'ASC', 'DateLivraison');
		foreach($rec as $rc) {
			$id = $rc['Id'];
			$lines = array();
			$elms = Sys::$Modules['StockLogistique']->callData("BLTete/$id/CommandeLigne", false, 0, 9999, 'ASC', 'Id');
			foreach($elms as $el)
				$lines[] = array(Famille=>$el['Famille'],Designation=>$el['Designation'],Quantite=>$el['Quantite']);
			
			$items[] = array(Id=>$id,Reference=>$rc['Reference'],DateLivraison=>$rc['DateLivraison'],DateReprise=>$rc['DateReprise'],
					ClientId=>$rc['ClientId'],LivraisonId=>$rc['LivraisonId'],CodPostal=>$rc['CodPostal'],Ville=>$rc['Ville'],
					Destockage=>$rc['Destockage'],Prepare=>$rc['Prepare'],Tournee=>$rc['Tournee'],
					children=>$lines);
		}
		$c = count($items);
		return WebService::WSData('',0,$c,$c,$req,'','','','',$items);
	}

	function SaveDestockage($lines) {
		foreach($lines as $l) {
			if(! $l->_updated) continue;
			$rec = Sys::$Modules['StockLogistique']->callData('BLTete/'.$l->Id, false, 0, 1);
			$liv = genericClass::createInstance('StockLogistique', $rec[0]);
			$liv->Destockage = $l->Destockage;
			$liv->Save();
		}
		return WebService::WSStatus('method',1,'','','','','',array(),null);
	}

	function FindReference($ref) {
	 	$rec = Sys::$Modules['StockLocatif']->callData('Reference/Reference='.$ref);
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxx", $rec);
		if(count($rec)) {
			$id = $rec[0]['Id'];
			$rec = Sys::$Modules['StockLocatif']->callData('Famille/Article/'.$rec[0]["ArticleId"]);
			$fam = array();
			foreach($rec as $rc)
				$fam[] = $rc['Famille'];
			$res = array(Id=>$id,targets=>$fam);
			return WebService::WSStatus('method',1,'','','','','',array(),$res);
		}
		return WebService::WSStatus('method',0,'','','','','',array(array("Référence non trouvée : $ref")),null);
	}
	
	
	function GetPreparation() {
		$p = $this;
		// client
		$rec = Sys::$Modules['Repertoire']->callData("Tiers/".$p->ClientId);
		$cli = genericClass::createInstance('Repertoire', $rec[0]);
		$rec = Sys::$Modules['Repertoire']->callData("Tiers/".$p->LivraisonId);
		$liv = genericClass::createInstance('Repertoire', $rec[0]);
		$items = array();
		$items[] = array(Reference=>$p->Reference,DateLivraison=>$p->DateLivraison,DateReprise=>$p->DateReprise,
				Livraison=>$liv->Intitule,CPLivr=>$liv->CodPostal,VilleLivr=>$liv->Ville,
				Adr1Livr=>$liv->Adresse1,Adr2Livr=>$liv->Adresse2,Adr3Livr=>$liv->Adresse3,TelLivr=>$liv->Telephone,
				Client=>$cli->Intitule,CPClient=>$cli->CodPostal,VilleClient=>$cli->Ville,
				Adr1Client=>$cli->Adresse1,Adr2Client=>$cli->Adresse2,Adr3Client=>$cli->Adresse3,TelClient=>$cli->Telephone);
		return WebService::WSData('',0,1,1,'StockLocatif/BLTete/'.$p->Id,'','','','',$items);
	}

	function SavePreparation($lines) {
		$prep = true;
		foreach($lines as $l) {
			$rec = Sys::$Modules['StockLogistique']->callData('Element/'.$l->ReferenceId, false, 0, 1);
			$elm = genericClass::createInstance('StockLogistique', $rec[0]);
			if($l->_updated) {
				$rec = Sys::$Modules['StockLogistique']->callData('Reference/'.$l->ReferenceId, false, 0, 1);
				$ref = genericClass::createInstance('StockLogistique', $rec[0]);
				$elm->addParent($ref);
				$elm->Save();
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxx", $elm);
			}
			if(! $elm->Reference) $prep = false;
		}
		$this->Prepare = $prep ? 1 : 0;
		$this->Save();
	}
	
	
	function PrintLabel($ids,$mode,$first=1) {
		require_once('Class/Lib/pdfb/pdfb.php');
		if(! count($ids)) return WebService::WSStatus('method', 1, '', '', '', '', '', array(), null);

		$pdf = new PDFB('P','mm','A4');
		$pdf->SetAuthor("Appaloosa");
		$pdf->SetTitle('Etiquettes');
		$id = $this->Id;
		$elm = Sys::$Modules['StockLogistique']->callData("BLTete/$id/Element",false,0,9999);
		$rec = Sys::$Modules['Repertoire']->callData("Tiers/".$this->object->ClientId);
		$cli = genericClass::createInstance('Repertoire', $rec[0]);
		$rec = Sys::$Modules['Repertoire']->callData("Tiers/".$this->object->LivraisonId);
		$liv = genericClass::createInstance('Repertoire', $rec[0]);

		$i = $first;
		$pdf->AddPage();
		foreach($elm as $el) {
			if($mode == 1 && array_search($el['Id'], $ids) === false) continue;
			if($i >= 8) {
				$pdf->AddPage();
				$i = 0;
			}
			$x = ($i % 2) * 105 + 5;
			$y = floor($i / 2) * (297 / 4) + 5;
			$pdf->BarCode('$PR'.$this->Reference, "C128B", $x, $y, 145, 30, 0.5, 0.5, 2, 5, "", "PNG");
			$y += 18;
			$pdf->SetXY($x, $y);

			// delivery address
			$y = 45;
			$this->SetXY(10, $y);
			$this->SetFont('Arial','B',8);
			$this->Cell(90,5,'Adresse de Livraison',1,0,'C',true);
			$y += 5;
			$this->SetXY(10, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,8,$this->object->LivraisonIntitule,'LR');
			$y += 5;
			$this->SetXY(10,$y);
			$this->SetFont('Arial','',10);
			$this->MultiCell(90,5,$this->object->LivraisonAdresse1."\n".$this->object->LivraisonAdresse2."\n".$this->object->LivraisonAdresse3."\n",'LR');
			$y += 3*5;
			$this->SetXY(10, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,6,$this->object->LivraisonCodPostal.'  '.$this->object->LivraisonVille,'LR');
			$y += 5;
			$this->SetXY(10, $y);
			$this->SetFont('Arial','B',10);
			$this->Cell(90,6,'Tél. :'.$liv->Telephone,'LRB');


			$i++;
		}
		$file = 'Home/tmp/doc'.rand(0, 2000).'.pdf';
		$pdf->Output($file);
		$pdf->Close();
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), array(printFiles=>array($file)));
	}
}
?>