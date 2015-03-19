<?php
class Reglement extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function Save() {
		genericClass::Save();
$GLOBALS["Systeme"]->Log->log("FactureTete/ClientId=$id",$this);

		foreach ($this->faclist as $f) {
			if($f->Solde) {
				$rec = Sys::$Modules['Devis']->callData("FactureTete/$f->Id", false, 0, 1);
				$fac = genericClass::createInstance('Devis', $rec[0]);
				$fac->Solde = 1;
				$fac->Save();
			}
		}
		return $this->GetReglements($this->TiersId);
	}
	
	function GetReglements($id) {
		$reg = Sys::$Modules['Repertoire']->callData("Tiers/$id/Reglement", false, 0,9999);	
		$fac = Sys::$Modules['Devis']->callData("FactureTete/ClientId=$id&Solde=0", false, 0, 9999);
		$tot = 0;
		foreach($reg as $r)
			$tot += $r['Montant'];
		foreach($fac as $r) {
			$m = $r['MontantTTC'];
			$tot -= $r['Type'] == 'A' ? -$m : $m;
		}
		$data = array(soldeClient=>$tot,reglist=>$reg,faclist=>$fac);
		$res = array(dataValues=>$data);
		return WebService::WSStatus('method', 1, '', '', '', '', '', array(), $res);
	}
}
?>