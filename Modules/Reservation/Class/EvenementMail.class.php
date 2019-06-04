<?php
class EvenementMail extends genericClass {
	function Save() {
		genericClass::Save();
		$E = Sys::$Modules["Reservation"]->callData("Reservation/Evenement/EvenementMail/".$this->Id);
		$E = genericClass::createInstance('Reservation',$E[0]);
		$this->Description = $E->Nom." - ".date('d/m/Y H:i',$E->DateDebut);
		genericClass::Save();
	}
}
?>
