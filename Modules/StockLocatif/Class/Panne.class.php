<?php
class Panne extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	
	function Save() {
		$rf = $this->getParents('Reference.ReferenceID');
		if(count($rf)) {
			$rf = $rf[0];
			$pan = $this->DateFin ? 0 : 1;
			if($pan != $rf->Panne) {
				$rf->Panne = $pan;
				$rf->Save();
				$sts = array(array('edit', 1, $rf->Id, 'StockLocatif', 'Reference', '', '', null, null));
			}
		}
		parent::Save();
		return $sts;
	}
	
	function GetPanne($id, $offset, $limit, $sort, $order, $filter) {
		$now = floor(strtotime("now") / 86400) * 86400;
		$req = 'Panne';
		if($id) $req .= "/Id=$id";
		if($filter) $req .= "/$filter";
		$rec = Sys::$Modules['StockLocatif']->callData($req,false,$offset,$limit,$sort,$order);
		foreach($rec as &$rc) {
			$rc['Date_Color'] = !$rc['DateFin'] && ($rc['Date'] + $rc['Duree'] * 86400)<=$now ? '0xff0000' : '0x000000';
		}
		$c = count($rec);
		return WebService::WSData('',0,$c,$c,$req,'','','','',$rec);
	}

	function FinDePanne($df) {
		$sts = array();
		$rf = $this->getParents('Reference.ReferenceID');
		if(count($rf)) {
			$rf = $rf[0];
			if($rf->Panne) {
				$rf->Panne = 0;
				$rf->Save();
				$sts[] = array('edit', 1, $rf->Id, 'StockLocatif', 'Reference', '', '', null, null);
			}
		}
		$this->DateFin = $df;
		$this->Save();
		$res = array(dataValues=>array('DateFin'=>$df));
		$sts[] = array('edit', 1, $this->Id, 'StockLocatif', 'Panne', '', '', null, $res);
		return WebService::WSStatusMulti($sts);
	}

}
