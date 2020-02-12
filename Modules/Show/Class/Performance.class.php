<?php

class Performance extends genericClass {

	
	public function GetShort() {
		$data = new stdClass();
		$data->id = $this->Id;
		$data->title = $this->Title;
		$data->teaser = $this->Teaser;
		$data->year = $this->Year;
		$data->Domain = '';
	}
}