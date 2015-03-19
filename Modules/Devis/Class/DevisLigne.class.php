<?php
class DevisLigne extends genericClass{

	function __construct($Mod,$Tab){
		genericClass::__construct($Mod,$Tab);
	}
	
	/*
	 * 
	 */
	function Save($flag = false){
		if(! $this->PrixUnitaire) {
			$this->Quantite;
			$this->Remise = 0;
			$this->PrixNet = 0;
			$this->CodeTVA = "";
		}
		genericClass::Save();
 	}
	
	
	function Famille($id,$fam,$tarif,$remise,$quant,$eche,$mens) {
		$rec = Sys::$Modules['StockLocatif']->callData("Famille/$fam",false,0,1);
		$f = $rec[0];
		$rec = Sys::$Modules['StockLocatif']->callData('Categorie/'.$f['CategorieId'],false,0,1);
		$cat = $rec[0];
		if($cat['ModeTarif'] == 1) {
			$rec = Sys::$Modules['Repertoire']->callData("Tiers/$id",false,0,1);
			$cod = $rec[0]['CodeTarif'];
			if($cod) 
				$prx = Sys::$Modules['StockLocatif']->callData("Famille/$fam/Tarif/CodeTarifId=$cod&DureeId>=$tarif",false,0,2,"ASC","DureeId","Prix");
			if(! is_array($prx) || ! count($prx))
				$prx = Sys::$Modules['StockLocatif']->callData("Famille/$fam/Tarif/DureeId>=$tarif",false,0,2,"ASC","DureeId","Prix");
//$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxx", $prx);
			if(is_array($prx) && count($prx))
				$brut = $this->calculPrix($prx,$eche,$mens);
		}
		if(! $brut) $brut = $f['PrixUnitaire'];
		$net = $brut - round($brut * $remise / 100, 2);
		if(! $quant) $quant = 1;
		$data = array('Famille'=>$f['Famille'],'Designation'=>$f['Designation'],'CodeTVA'=>$f['CodeTVA'],
				'PrixUnitaire'=>$brut,'PrixNet'=>$net,'Quantite'=>$quant,'TexteId'=>'0',
				'CategorieId'=>$cat['Id'],'ModeTarif'=>$cat['ModeTarif']);
		$res = array('dataValues'=>$data);
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), $res);
	}

	private function calculPrix($prx,$eche,$mens) {
		$prix = $prx[0]['Prix'];
$GLOBALS["Systeme"]->Log->log("xxxxxxxxxxxxxxxxxxxxxxxxx:$prix:$eche:$mens");
		if($eche) {
			$moi = floor($eche);
			if($moi < 6) {
				if($moi < $eche) $prix = round(($prix + $prx[1]['Prix']) / 2);
			}
			else {
				if($moi < 9) $prix = round($prix / 6);
				elseif($moi < 12) $prix = round($prix / 9);
				else $prix = round($prix / 12);
				if(! $mens) $prix = round($prix * $eche);
			}
		}
		return $prix;
	}
	
	
	function TexteLibre($txt) {
		$rec = Sys::$Modules['Devis']->callData("TexteLibre/$txt",false,0,1);
		$f = $rec[0];
		$data = array('Designation'=>$f['Texte'],'CodeTVA'=>'','PrixUnitaire'=>'','PrixNet'=>'','Quantite'=>'','FamilleId'=>'0');
		$res = array('dataValues'=>$data);
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), $res);
	}
	
	
	function TotalLigne($brut, $remise) {
		$net = $brut - round($brut * $remise / 100, 2);
		$data = array('PrixNet'=>$net);
		$res = array('dataValues'=>$data);
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), $res);
	}
	
}
