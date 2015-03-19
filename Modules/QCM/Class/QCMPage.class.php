<?php
class QCMPage extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function Delete() {
		$res = $this->getChildren('Question');
		foreach($res as $r) $r->Delete();
		return parent::Delete();
	}
}