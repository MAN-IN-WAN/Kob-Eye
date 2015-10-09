<?php
class TypeLogement extends genericClass {
	function Delete (){
		$tl = Sys::getData('ParcImmobilier','TypeLogement/'.$this->Id.'/Lot');
		foreach ($tl as $t) {
			$t->Delete();
		}
		parent::Delete();
	}


	function explodeCSV( $content ) {
		return explode(PHP_EOL, $content);
	}

	function sendHeader() {
		header("Content-type: application/vnd.ms-excel"); 
		header("Content-disposition: attachment; filename=\"pragmaDenonciation.csv\"");
	}

  	function addLigne($ctc) {
   		echo $ctc . ";"  . "\r\n";
  	}


	function rc() {
		echo "\r\n";
	}
	



}
?>