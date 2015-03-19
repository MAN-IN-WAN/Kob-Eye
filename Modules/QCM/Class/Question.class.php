<?php
class Question extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function Delete() {
		$res = $this->getChildren('Reponse');
		foreach($res as $r) $r->Delete();
		return parent::Delete();
	}
}