<?php

class ToTonalli extends genericClass {
	
	static public function InitApp($args) {
		$lang = $args['lang'];
		
		$sql = "select Id,Tonalli,Maya,Divinite,$lang from `##_CEN-ToTonalli` order by Id";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$ton = array();
		foreach($rs as $r) $ton[] = ['id'=>$r['Id'],'tonalli'=>$r['Tonalli'],'maya'=>$r['Maya'],'divinite'=>$r['Divinite'],'trad'=>$r[$lang]];
		$sql = "select Id,Meztli,Maya,$lang from `##_CEN-ToMeztli` order by Id";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$mez = array();
		foreach($rs as $r) $mez[] = ['id'=>$r['Id'],'meztli'=>$r['Meztli'],'maya'=>$r['Maya'],'trad'=>$r[$lang]];
		$sql = "select Id,Xihuitl,MoisInitial,Parametres from `##_CEN-ToXihuitl` order by Id";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$xih = array();
		foreach($rs as $r) $xih[] = ['id'=>$r['Id'],'xihuitl'=>$r['Xihuitl'],'mois'=>$r['MoisInitial'],'params'=>$r['Parametres']];
		$sql = "select Id,Codex from `##_CEN-ToCodex` order by Id";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$cod = array();
		foreach($rs as $r) $cod[] = ['id'=>$r['Id'],'codex'=>$r['Codex']];
		
		return array('success'=>true,data=>['tonalli'=>$ton,'meztli'=>$mez,'xihuitl'=>$xih,'codex'=>$cod,'translation'=>[]]);
	}
}