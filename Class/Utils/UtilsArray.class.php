<?php
class UtilsArray {
	static function SizeOf($P){
		return sizeof($P[0]);
	}
	static function newArray(){
		return Array();
	}
	static function push($P){
		if (sizeof($P)==2){
			array_push($P[0],$P[1]);
			return $P[0];
		}
		else {
			//push associatif
			$P[0][$P[2]] = $P[1];
			return $P[0];
		}
	}
}
?>
