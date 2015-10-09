<?php
class JsonP {
	static function getInput(){
		$tmp = json_decode(file_get_contents('php://input'));
		if (!$tmp)return;
		if (is_array($tmp)) return $tmp;
		else return Array($tmp);
	}
}