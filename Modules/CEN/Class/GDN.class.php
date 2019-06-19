<?php

class GDN extends genericClass {
	
	// liste de mots du GDN 
	function GetList($args) {
		$word = $args['word'];
		$field = $args['nah'] == 'true' ? 'Norma_1' : 'Trad_2';

		$sql = "select distinct $field as word from `##_CEN-GDN` where $field like '$word%' and substring($field,-1,1)<>'+' order by word limit 15";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$list = array();
		foreach($pdo as $p)	$list[] = $p['word'];
		return array('words'=>$list, 'aaa'=>'aaa');
	}

	function GetGDN($args) {
		$field = $args['nah'] == 'true' ? 'Norma_1' : 'Trad_2';
		$word = $args['word'];
		
		$sql = "select Id,Paleo,Norma_1,if(Trad_2='',Trad_1,Trad_2) as Trad_2,Commentaires,DictionnaireId from `##_CEN-GDN` where $field like '$word%' order by $field,Paleo";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$gdn = array();
		$rup = '';
		foreach($pdo as $r) {
			$n = $r['Norma_1'];
			if($n != $rup) {
				if(!empty($rup)) $gdn[] = array('norma'=>$rup, 'gdn'=>$list);
				$list = array();
				$rup = $n;
			}
			$list[] = array('id'=>$r['Id'],'paleo'=>$r['Paleo'],'trad'=>$r['Trad_2'],'comm'=>!empty($r['Commentaires']),'dic'=>$r['DictionnaireId']);
		}
		if(!empty($rup)) $gdn[] = array('norma'=>$rup, 'gdn'=>$list);
		return array('gdn'=>$gdn, 'sql'=>$sql);
	}
	
}