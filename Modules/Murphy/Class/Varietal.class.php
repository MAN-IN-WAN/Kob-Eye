<?php
class Varietal extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function Save() {
		$col = '';
		if($this->Red) $col = '1';
		if($this->White) {
			if($col) $col .= ',';
			$col .= '2';
		}
		if($this->Rose) {
			if($col) $col .= ',';
			$col .= '3';
		}
		$this->Colours = $col;
		parent::Save();
	}
}