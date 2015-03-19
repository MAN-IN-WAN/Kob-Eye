<?php
class DevisEcheance extends genericClass{

	function __construct($Mod,$Tab){
		genericClass::__construct($Mod,$Tab);
	}

	function ContractInvoices($ids, $date, $force) {
		$dv = genericClass::createInstance('Devis', 'DevisTete');
		return $dv->ContractInvoices($ids, $date, $force);
	}
	
}
