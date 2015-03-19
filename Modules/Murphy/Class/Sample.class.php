<?php
class Sample extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function Save() {
		if(! $this->Status) $this->Status = SPL_SUP_RESP;
		$p = $this->GetParents('SampleRequest.SampleSampleRequestId');
		if(count($p)) {
			$p = $p[0]->GetParents('Third.SampleRequestBuyerId');
			if(count($p)) $this->BuyerCompany = $p[0]->Company;
		}
		$p = $this->GetParents('Third.SampleSupplierId');
		if($p[0]) $this->SupplierCompany = $p[0]->Company;
		genericClass::Save();
		$res = array('BuyerCompany'=>$this->BuyerCompany,'SupplierCompany'=>$this->SupplierCompany);
		return array(array($id ? 'edit' : 'add', 1, $this->Id, 'Murphy', 'SampleRequest', '', '', null, array('dataValues'=>$res)));
	}
}