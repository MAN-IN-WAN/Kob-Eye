<?php
class Absence extends genericClass {
	
	function Save() {
		$this->Annee = Cadref::$Annee;
		return parent::Save();		
	}


}


