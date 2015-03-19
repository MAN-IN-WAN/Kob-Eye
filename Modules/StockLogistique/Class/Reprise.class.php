<?php
class Reprise extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	// remise en stock
	function GetReprise() {
	 	$rec = Sys::$Modules['StockLogistique']->callData('Reprise/Stock=0&Panne=0&NonRepris=0',false,0,999);
		if(! is_array($rec)) $rec = array();
		$c = count($rec);
		return WebService::WSData('',0,$c,$c,'','','','','',$rec);
	}
	
	// remise en stock
	function SaveReprise($date, $reps) {
		foreach($reps as $rep) {
			$rec = Sys::$Modules['StockLogistique']->callData('Reprise/'.$rep->Id,false,0,1);
			$rp = genericClass::createInstance('StockLogistique',$rec[0]);
			$ref = null;
			if($rep->ReferenceId) {
				$rec = Sys::$Modules['StockLocatif']->callData('Reference/'.$rep->ReferenceId,false,0,1);
				$ref = genericClass::createInstance('StockLocatif',$rec[0]);
			}
			if($rep->Panne) {
				$rp->Panne = 1;
				if($ref) {
					$pan = genericClass::createInstance('StockLocatif','Panne');
					$pan->addParent($ref);
					$pan->Date = time();
					$pan->DateFin = null;
					$pan->Libelle = 'Retour en panne';
					$pan->Save();
					$ref->Sorti = 0;
					$ref->Panne = 1;
					$ref->Save();
				}
				$rp->Save();
			}
			elseif($rep->Stock) {
				$rp->Stock = 1;
				if($ref) {
					$ref->Sorti = 0;
					$ref->Save();
				}
				$rp->Save();
			}
			if($rp->ElementId) {
				$rec = Sys::$Modules['StockLogistique']->callData('Element/'.$rp->ElementId,false,0,1);
				$elm = genericClass::createInstance('StockLogistique',$rec[0]);
				$elm->DateStock = $date;
				$elm->Etat = 4;
				$elm->Save();
			}
		}
		return WebService::WSStatus('edit',1,0,'StockLogistique','Reprise','','',null,null);
	}
}