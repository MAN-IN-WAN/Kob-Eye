<?php

class GDN extends genericClass {
	
	static function GetDics() {
		$dics = Sys::getData('CEN', 'Dictionnaire', 0, 999, 'ASC', 'NOM');
		$dicId= array();
		$dic = array();
		foreach($dics as $d) {
			$id = $d->Id;
			$dic[] = array('id'=>$id, 'title'=>$d->Nom, 'selected'=>true);
			$dicId[$id] = $d->Nom;
		}
		return array('dictionariesId'=>$dicId, 'dictionaries'=>$dic);
	}

	// liste de mots du GDN 
	static function GetList($args) {
		$word = $args['word'];
		$field = $args['nah'] == 'true' ? 'Norma_1' : "if(Trad_2='',Trad_1,Trad_2)";
		$dict = $args['dic'];
		if($dict == 'all' || $dict == '' || $dict == 'null') $dict = '';
		else $dict = "and DictionnaireId in ($dict)";
		switch($args['search']) {
			case 'start': $mode = "like '$word%'"; break;
			case 'all': $mode = "= '$word'"; break;
			case 'any': $mode = "like '%$word%'"; break;
		}
		
		$sql = "select distinct $field as word from `##_CEN-GDN` where $field $mode and substring($field,-1,1)<>'+' $dict order by word limit 15";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$list = array();
		foreach($pdo as $p)	$list[] = $p['word'];
		return array('words'=>$list, 'sql'=>$sql);
	}

	static function GetGDN($args) {
		$nah = $args['nah'] == 'true';
		$field = $nah ? 'Norma_1' : 'Trad_2';
		$word = $args['word'];
		if($nah && $args['norm'] == 'true') $word = self::Normalize($word);
		$dict = $args['dic'];
		if($dict == 'all' || $dict == '' || $dict == 'null') $dict = '';
		else $dict = "and DictionnaireId in ($dict)";
		switch($args['sort']) {
			case 0: $sort = 'Norma_1,Paleo,Trad_1'; break;
			case 1: $sort = 'Trad_1,Norma_1,Paleo'; break;
			case 2: $sort = 'Nom,Norma_1,Paleo,Trad_1';
		}
		switch($args['search']) {
			case 'start': $mode = "like '$word%'"; break;
			case 'all': $mode = "= '$word'"; break;
			case 'any': $mode = "like '%$word%'"; break;
		}
		
		$sql = "select count(*) as cnt from `##_CEN-GDN` where $field $mode $dict";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		foreach($pdo as $r) $count = $r['cnt'];
		

		$sql = "
select g.Id,Paleo,Norma_1,if(Trad_1='',Trad_2,Trad_1) as Trad_2,Commentaires,DictionnaireId 
from `##_CEN-GDN` g inner join `##_CEN-Dictionnaire` d on d.Id=g.DictionnaireId
where $field $mode $dict
order by  $sort";
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
		return array('gdn'=>$gdn, 'word'=>$word, 'count'=>$count);
	}
	
	static function GetComments($args) {
		$o = Sys::getOneData('CEN', 'GDN/'.$args['id']);
		$com = $o->Commentaires;
		$com = preg_replace('/ *\/\/ */', '<br />', $com);
		$pos = strpos($com, '§ ');
		while($pos !== false) {
			$com = preg_replace('/§ /', '<i>', $com, 1);
			$com = preg_replace('/ §/', '</i>', $com, 1);
			$pos = strpos($com, '§ ');
		}
		$pos = strpos($com, '$ ');
		while($pos !== false) {
			$com = preg_replace('/\$ /', '<i>', $com, 1);
			$com = preg_replace('/ \$/', '</i>', $com, 1);
			$pos = strpos($com, '$ ');
		}
		return array('text'=>$com);				
	}
	
	static public function Normalize($word) {
		$r = Sys::getOneData('CEN', 'Regle/Code=GDN');
			
		$word = ' '.strtolower(trim($word)).' ';
		
		$os = array();
		$file = fopen(getcwd().'/'.$r->FilePath, 'r');
		if(!$file) return $word;
		while(! feof($file)) {
			$o = explode("\t", utf8_encode(fgets($file)));
			if(count($o) < 4) continue;
			$os[] = $o;
		}
		fclose($file);
		usort($os, 'self::sortOrtho');

		$ch = $word;
		foreach($os as $o) {
			$ch1 = $o[0];
			$ch2 = $o[1];
			switch($o[2]) {
				case 'début.': $ch1 = ' '.$ch1; $ch2 = ' '.$ch2; break;
				case 'fin.': $ch1 = $ch1.' '; $ch2 = $ch2.' '; break;
			}
			$numb = self::replOrtho($ch, $ch1, $ch2);
			$new = trim($ch);
		}
		return $new;
	}
	
	static private function sortOrtho($a, $b) {
		return intval($a[3]) - intval($b[3]);
	}

	private static function replOrtho(&$ch, $ch1, $ch2) {
		$nb = 0;
		$pos = strpos($ch, $ch1);
		while($pos !== false) {
			$nb++;
			$ch = substr($ch, 0, $pos).$ch2.substr($ch, $pos+strlen($ch1));
			$pos = strpos($ch, $ch1, $pos+strlen($ch2));
		}
		return $nb;
	}
	
}

