<?php
class Attachment extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function Delete() {
		unlink($this->Doc);
		return parent::Delete();
	}

}

