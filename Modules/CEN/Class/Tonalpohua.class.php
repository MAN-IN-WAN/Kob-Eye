<?php

class Tonalpohua extends genericClass {
	
	static public function InitApp($args) {
		$lang = $args['lang'];
		
		$sql = "select Id,Type,Nahuatl,Maya,$lang,Regles from `##_CEN-Tonalpohua` where Type is not null order by Id";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$ton = array();
		foreach($rs as $r) $ton[$r['Id']] = ['id'=>$r['Id'],'type'=>$r['Type'],'nahuatl'=>$r['Nahuatl'],'maya'=>$r['Maya'],'trans'=>$r[$lang],'rules'=>$r['Regles']];
		
		$sql = "select Id,Xihuitl,MoisInitial,Parametres from `##_CEN-ToXihuitl` order by Id";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$xih = array();
		foreach($rs as $r) $xih[] = ['id'=>$r['Id'],'xihuitl'=>$r['Xihuitl'],'mois'=>$r['MoisInitial'],'params'=>$r['Parametres']];
		
		$sql = "select Id,Codex,Image,Tonalli from `##_CEN-ToCodex` order by Ordre";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$cod = array();
		foreach($rs as $r) $cod[] = ['id'=>$r['Id'],'codex'=>$r['Codex'],'image'=>$r[Image],'tonalli'=>$r['Tonalli']];
		
		return array('success'=>true,data=>['tonalpohua'=>$ton,'xihuitl'=>$xih,'codex'=>$cod,'translation'=>[]]);
	}
	
}