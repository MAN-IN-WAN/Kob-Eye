<?php

class cadrefStat {
	var $Type;
	var $Code;
	var $Libelle;
	var $Valeurs;
	var $Total;
	
	function cadrefStat($type, $code, $lib, $cols) {
		$this->Type = $type;
		$this->Code = $code;
		$this->Libelle = $lib;
		$this->Valeurs = array();
		for($i = 0; $i < $cols; $i++) $this->Valeurs[$i] = 0;
		$this->Total = 0;
	}
}

class cadrefStatList {
	private $cols;
	var $Stats = array();
	
	function cadrefStatList($cols) {
		$this->cols = $cols;
	}
	
	function Find($type, $code) {
		$n = count($this->Stats);
		foreach($this->Stats as $s) {
			if($s->Type == $type && $s->Code == $code) return $s;
		}
		return null;
	}
	
	function Sum($type, $code, $lib, $col, $val) {
		$s = $this->Find($type, $code);
		if($s == null) {
			$s = new cadrefStat($type, $code, $lib, $this->cols);
			$this->Stats[] = $s;
		}
		$s->Total += $val;
		$s->Valeurs[$col] += $val;
		return $s;
	}
}
