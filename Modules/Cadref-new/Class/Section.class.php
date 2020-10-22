<?php
class Section extends genericClass {
	
	function Delete() {
		$rec = $this->getChildren('Discipline');
		if(count($rec)) throw new Exception('Cette section ne peut être supprimée');

		return parent::Delete();
	}

	
}
