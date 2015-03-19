<?php
class Session extends Root {
	static function init(){
		if (is_array($_SESSION))foreach ($_SESSION as $k=>$v) $GLOBALS["Systeme"]->RegisterVar($k,unserialize($v));
	}
	static function add($Nom,$Valeur){
		$_SESSION[$Nom] = serialize($Valeur);
		$GLOBALS["Systeme"]->RegisterVar($Nom,$Valeur);
	}
	static function get($Nom){
		if (isset($_SESSION[$Nom]))return unserialize($_SESSION[$Nom]);
		else return false;
	}
	static function del($Nom) {
		unset($_SESSION[$Nom]);
	}
	static function reset(){
 		if (is_array($_SESSION))foreach ($_SESSION as $k=>$v) unset($_SESSION[$k]);
	}
	static function is($Nom){
		return isset($_SESSION[$Nom]);
	}
}
?>