<?php
class FactureLigne extends genericClass{

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
	
	
	function Famille($fam,$remise,$quant) {
		$rec = Sys::$Modules['StockLocatif']->callData("Famille/$fam");
		$f = $rec[0];
		$rec = Sys::$Modules['StockLocatif']->callData("Famille/$fam/Tarif/1");
		$brut = $rec[0]['Prix'];
		$net = $brut - round($brut * $remise / 100);
		if(! $quant) $quant = 1;
		$data = array('Designation'=>$f['Designation'],'CodeTVA'=>$f['CodeTVA'],'PrixUnitaire'=>$brut,'PrixNet'=>$net,'Quantite'=>$quant,'TexteId'=>'0');
		$res = array('dataValues'=>$data);
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), $res);
	}
	
	function TexteLibre($txt) {
		$rec = Sys::$Modules['Devis']->callData("TexteLibre/$txt",false,0,1);
		$f = $rec[0];
		$data = array('Designation'=>$f['Texte'],'CodeTVA'=>'','PrixUnitaire'=>'','PrixNet'=>'','Quantite'=>'','FamilleId'=>'0');
		$res = array('dataValues'=>$data);
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), $res);
	}
	
	
	function TotalLigne($brut, $remise) {
		$net = $brut - round($brut * $remise / 100);
		$data = array('PrixNet'=>$net);
		$res = array('dataValues'=>$data);
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), $res);
	}
	
}
