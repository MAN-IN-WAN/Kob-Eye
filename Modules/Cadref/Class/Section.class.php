<?php
class Section extends genericClass {
	
	function Delete() {
		$rec = $this->getChildren('Discipline');
		if(count($rec)) throw new Exception('Cette section ne peut être supprimée');

		return parent::Delete();
	}

	function testalert() {
		AlertUser::addAlert('tttttttttttt','CA','','',0,[],'CADREF_ADMIN','');
		return true;
	}
	
}
