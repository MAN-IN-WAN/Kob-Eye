<?php
class Chrono extends Root{
	var $total;
	var $compteur;
	var $occ;

	function Chrono() {
		$this->total = Array();
	}

	function start($Type="Chrono",$level=0) {
		$this->compteur[$Type] = $this->microtime_float();
	}

	function stop($Type="Chrono",$level=0) {
		$Temp = (isset($this->total[$level][$Type]))?$this->total[$level][$Type]:"";
		$Temp += $this->microtime_float() - $this->compteur[$Type];
		$this->total[$level][$Type] = $Temp;
	}

	function add($Type="Chrono") {
		$Temp = $this->occ[$Type];
		$Temp++;
		$this->occ[$Type] = $Temp;
	}

	function total() {
		$Rapport="_RAPPORT CHRONO__________________________________________________\r\n";
		if (sizeof($this->total)){
			$level = 0;
			foreach ($this->total as $Key2) {
				$Rapport.="|LEVEL ". $level."__________________________________________________\r\n";
				foreach ($Key2 as $Key=>$Value) {
					$Rapport.="| ----=> ".$Key."	= ".$Value."		\r\n";
				}
				$level++;
			}
		}
		$Rapport.="|RAPPORT DECOMPTE________________________________________________\r\n";
		if (sizeof($this->occ))foreach ($this->occ as $Key=>$Value) {
			$Rapport.="| ----=> ".$Key."	= ".$Value."		\r\n";
		}
		$Rapport.="|________________________________________________________________";
		return $Rapport;
	}

	function microtime_float() {
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}
?>