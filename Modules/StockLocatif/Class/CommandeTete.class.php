<?php
class CommandeTete extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	/*
	 * 
	 */
	function Save() {
		if($this->Reference == '') {
			$rec = Sys::$Modules['StockLogistique']->callData('CommandeTete', false, 0, 1, 'DESC', 'Reference', 'Reference');
			if(! $rec) $this->Reference = '1'; 
			else $this->Reference = $rec[0]['Reference'] + 1;
		}
		genericClass::Save();
	}
	
	
}
?>