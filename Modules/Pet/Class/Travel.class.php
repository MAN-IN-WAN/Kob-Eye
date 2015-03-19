<?php
class Travel extends genericClass {
	
	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}

	function Delete() {
		$ch = $this->getChildren('Stage');
		foreach($ch as $c) $c->Delete();
		return parent::Delete();
	}


}