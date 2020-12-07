<?php
class Depart extends genericClass {
		
	function Save() {
		$this->Annee = Cadref::$Annee;
		return parent::Save();		
	}

}



