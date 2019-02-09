<?php
class Absence extends genericClass {
	
	function Save() {
		$this->Annee = Cadref::$Annee;
		return parent::Save();		
klog::l("aaaaaaaaaaaaaaaaaaaaaaaaaaaaa",$this);
	}


}


