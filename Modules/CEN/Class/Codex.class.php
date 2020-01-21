<?php

class Codex extends genericClass {
	

	static function GetCodex($args) {
		$type = $args['type'];
		$id = isset($args['id']) ? $args['id'] : '';
		$ext = isset($args['ext']) ? $args['ext'] : '';
		$ln = strlen($ext);

		$dic = array();
		$dicId = array();		
		switch($type) {
			case 'codex':
				$dics = Sys::getData('CEN', 'Codex', 0, 999, 'ASC', 'Code');
				foreach($dics as $d) {
					$id = $d->Id;
					$dir = self::getDir($d->userCreate, $d->Repertoire);
					$dic[] = array('id'=>$id, 'code'=>$d->Code, 'title'=>strtolower($d->Titre), 'selected'=>true, 'imgSel'=>false, 
						'dir'=>$dir, 'img'=>'img_01.bmp');
					$dicId[$id] = array('title'=>strtolower($d->Titre), 'dir'=>$dir);
				}
				return array('codex'=>$dic, 'codexId'=>$dicId); 
				
			case 'planche':
				$sql = "select pCodexId,Id,Cote from `##_CEN-Zone` where CodexId=$id and substr(Cote,1,$ln)='$ext' order by Cote";
				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
				$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);

				$dics = Sys::getData('CEN', 'Planche/CodexId='.$id, 0, 999, 'ASC', 'Cote');
				foreach($dics as $d) {
					$dic[] = array('codexId'=>$d->CodexId, 'id'=>$d->Id, 'cote'=>$d->Cote, 'img'=>self::getImg($d->cote, 'jpg'));
				}
				return array('planches'=>$dic);
				
			case 'zone':
				$sql = "select CodexId,Id,Cote from `##_CEN-Zone` where CodexId=$id and substr(Cote,1,$ln)='$ext' order by Cote";
				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
				$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
				$dic = array();
				foreach($pdo as $d) {
					$dic[] = array('codexId'=>$d['CodexId'], 'id'=>$d['Id'], 'cote'=>trim($d['Cote']), 'img'=>self::getImg($d->cote, 'jpg'));
				}
				return array('zones'=>$dic);

			case 'glyphe':
				$whr = "where CodexId=$id and substr(Cote,1,$ln)='$ext' order by Cote";
				$gly = self::getGlyphe('Glyphe', $whr);
				$per = self::getGlyphe('Personnage', $whr);
				return array('glyphes'=>$gly, 'personnes'=>$per);
				
			case 'element':
				$whr = "where e.CodexId=$id and e.Cote='$ext' order by e.Theme";
				$dic = self::getTlaElement($whr);
				return array('elements'=>$dic);
		}
	}
	
	static private function getGlyphe($tbl, $whr) {
		$ty = $tbl == 'Glyphe' ? ',Type' : '';
		$sql = "select CodexId,Id,Cote,Lecture $ty from `##_CEN-$tbl` $whr";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);

		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$gly = array();
		foreach($pdo as $d) {
			$gly[] = array('codexId'=>$d['CodexId'], 'id'=>$d['Id'], 'cote'=>trim($d['Cote']), 'type'=>$d['Type'], 'lecture'=>trim($d['Lecture']));
		}
		return $gly;
	}
	
	static function getTlaElement($whr) {
		$sql = "
select distinct e.CodexId,e.Id,e.Cote,e.Theme,e.Element,s.Sens,s.Sens2
from `##_CEN-Element` e
left join `##_CEN-Sens` s on s.CodexId=e.CodexId and s.Element=e.Element
$whr";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$dic = array();
		foreach($pdo as $d) {
			$dic[] = array('codexId'=>$d['CodexId'], 'id'=>$d['Id'], 'cote'=>trim($d['Cote']), 'theme'=>$d['Theme'], 
				'element'=>trim($d['Element']), 'meaning'=>$d['Sens'], 'meaning2'=>$d['Sens2']);
		}
		return $dic;
	}	

	static function getValeur($whr) {
		$sql = "
select e.CodexId,e.Id,e.Cote,e.Theme,e.Element,c.userCreate,c.Repertoire,c.Titre,v.Valeur
from `##_CEN-Valeur` v
inner join `##_CEN-Element` e on e.Cote=v.Cote
inner join `##_CEN-Codex` c on c.Id=e.CodexId
$whr";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$dic = array();
		foreach($pdo as $d) {
			$dic[] = array('codexId'=>$d['CodexId'], 'id'=>$d['Id'], 'cote'=>trim($d['Cote']), 'theme'=>$d['Theme'], 
				'element'=>trim($d['Element']), 'dir'=>self::getDir($d['userCreate'], $d['Repertoire']), 'title'=>strtolower($d['Titre']));
		}
		return $dic;
	}	
	
	static private function getCount(&$array, $table, $alias, $where) {
		$sql = "select CodexId,count(*) as cnt from `##_CEN-$table` $alias $where";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$cnt = array();
		foreach($pdo as $d) $cnt[$d['CodexId']] = $d['cnt'];
		foreach($array as &$a) $a['count'] = $cnt[$a['codexId']]; 
	}

	private static function getDir($usr, $dir) {
		return "/Home/$usr/CEN/Codex/$dir/";
	}
	private static function getImg($code, $ext) {
		$img = str_replace('+', '-', strtolower($code)).'.'.$ext;
	}

	// word list
	static function GetList($args) {
		$word = $args['word'];
		$cdx = $args['codex'];
		if($cdx == 'all' || $cdx == '' || $cdx == 'null') $cdx = '';
		//else $cdx = "CodexId in ($cdx) and";

		switch($args['search']) {
			case 'start': $mode = "like '$word%'"; break;
			case 'all': $mode = "= '$word'"; break;
			case 'any': $mode = "like '%$word%'"; break;
		}

		$type = $args['type'];
		switch($type) {
			case 'glyphe':
				if($cdx) $cdx = "CodexId in ($cdx) and";
				$sql = "
select distinct Lecture as word from `##_CEN-Glyphe` where $cdx Lecture $mode
union select distinct Cote as word from `##_CEN-Glyphe` where $cdx Cote $mode
union select distinct Lecture as word from `##_CEN-Personnage` where $cdx Lecture $mode
union select distinct Cote as word from `##_CEN-Personnage` where $cdx Cote $mode";
				break;
				
			case 'element':
				if($cdx) $cdx = "CodexId in ($cdx) and";
				$sql = "
select distinct Element as word from `##_CEN-Element` where $cdx Element $mode
union select distinct Cote as word from `##_CEN-Element` where $cdx Cote $mode
union select distinct Theme as word from `##_CEN-Element` where $cdx Theme $mode";
				break;
				
			case 'valeur':
				if($cdx) $cdx = "CodexId in ($cdx) and";
				$sql = "
select distinct Lecture as word from `##_CEN-Valeur` where $cdx Valeur $mode
union select distinct Theme as word from `##_CEN-Valeur` where $cdx Theme $mode
union select distinct Lecture as word from `##_CEN-PValeur` where $cdx Valeur $mode
union select distinct Theme as word from `##_CEN-PValeur` where $cdx Theme $mode";
				break;
		}

		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql)." order by word limit 15";
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$list = array();
		foreach($pdo as $p)	$list[] = $p['word'];
		return array('words'=>$list, 'sql'=>$sql);		
	}

	// result
	static function GetTlachia($args) {
		$word = $args['word'];
		$cdx = $args['codex'];
		if($cdx == 'all' || $cdx == '' || $cdx == 'null') $cdx = '';
		//else $cdx = "CodexId in ($cdx) and";

		switch($args['search']) {
			case 'start': $mode = "like '$word%'"; break;
			case 'all': $mode = "= '$word'"; break;
			case 'any': $mode = "like '%$word%'"; break;
		}

		$type = $args['type'];
		switch($type) {
			case 'glyphe':
				if($cdx) $cdx = "CodexId in ($cdx) and";
				$whr = "where $cdx (Lecture $mode or Cote $mode) group by CodexId order by CodexId,Cote";
				$gly = self::getGlyphe('Glyphe', $whr);
				$per = self::getGlyphe('Personnage', $whr);
				self::getCount($gly, 'Glyphe', '', $whr);
				self::getCount($per, 'Personnage', '', $whr);
				return array('glyphes'=>$gly, 'personnes'=>$per);

			case 'glyphe-elem':
				$el = $args['element'];
				if($cdx) {
					$whr = "where CodexId in ($cdx) and Element like '% $el,%' order by CodexId,Cote";
				}
				else {
					$id = $args['id'];
					$whr = "where CodexId=$id and Element like '% $el,%' order by CodexId,Cote";
				}
				$gly = self::getGlyphe('Glyphe', $whr);
				$per = self::getGlyphe('Personnage', $whr);
				//self::getCount($dic, 'Element', 'e.', $whr);
				return array('glyphes'=>$gly, 'personnes'=>$per);

			case 'glyphe-detail':
				$cote = $args['cote'];
				$id = $args['id'];
				$them = $args['theme'];
				$sql = "
select Citation,Source,Pages from `##_CEN-Citation` where CodexId=$id and Cote='$cote'
union all select Citation,Source,Pages from `##_CEN-PCitation` where CodexId=$id and Cote='$cote'
";
				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
				$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
				$cit = array();
				foreach($pdo as $p)	$cit[] = array('citation'=>$p['Citation'],'source'=>$p['Source'],'page'=>$p['Pages']);

				$sql = "
select ValSupl from `##_CEN-ValSupl` where CodexId=$id and Cote='$cote'
union select ValSupl from `##_CEN-PValSupl` where CodexId=$id and Cote='$cote'
";
				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
				$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
				$valSup = '';
				foreach($pdo as $p)	{
					if($valSup) $valSup .=', ';
					$valSup .= $p['ValSupl'];
				}


				$whr = "where e.CodexId=$id and e.Cote='$cote' order by e.Theme";
				$elm = self::getTlaElement($whr);
				foreach($elm as &$l) {
					$c = $l['cote'];
					$t = $l['theme'];
					$sql = "
select Valeur from `##_CEN-Valeur` where CodexId=$id and Cote='$c' and Theme='$t'
union select Valeur from `##_CEN-PValeur` where CodexId=$id and Cote='$c' and Theme='$t'
";
					$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
					$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
					$val = '';
					foreach($pdo as $p)	{
						if($val) $val .=', ';
						$val .= $p['Valeur'];
					}
					$l['valeur'] = $val;
				}
				
				return array('citations'=>$cit, 'elements'=>$elm, 'valSup'=>$valSup);
				
			case 'element':
				$grp = $args['elements'] == 'true' ? '' : 'group by e.CodexId';
				if($cdx) $cdx = "e.CodexId in ($cdx) and";
				$whr = "where $cdx (e.Element $mode or e.Cote $mode or e.Theme $mode) $grp order by e.CodexId,e.Cote";
				$dic = self::getTlaElement($whr);
				if($grp) self::getCount($dic, 'Element', 'e', $whr);
				return array('elements'=>$dic);

			case 'element-detail':
				$lang = $args['lang'];
//				$elem = $args['element'];
				$them = $args['theme'];
				$id = $args['id'];
				$sql = "
select distinct Valeur from `##_CEN-Valeur` where CodexId=$id and Theme='$them'
union select distinct Valeur from `##_CEN-PValeur` where CodexId=$id and Theme='$them'
";
				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
				$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
				$val = '';
				foreach($pdo as $p)	{
					if($val) $val .=', ';
					$val .= $p['Valeur'];
				}
				return array('valeur'=>$val,'sql'=>$sql);

			case 'valeur':
				if($cdx) $cdx = "CodexId in ($cdx) and";
				$sql = "
select distinct Lecture as word from `##_CEN-Valeur` where $cdx Lecture $mode
union select distinct Cote as word from `##_CEN-Valeur` where $cdx Lecture $mode
union select distinct Lecture as word from `##_CEN-PValeur` where $cdx Lecture $mode
union select distinct Cote as word from `##_CEN-PValeur` where $cdx Lecture $mode";
				break;
		}

		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql)." order by word limit 15";
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$list = array();
		foreach($pdo as $p)	$list[] = $p['word'];
		return array('words'=>$list, 'sql'=>$sql);		
	}
	
	function GetDescr($args) {
		$dir = '/Home/'.$this->userCreate.'/CEN/Codex/'.$this->Repertoire.'/textes/';
		$lgs = ['es','fr','en'];
		$lix = array_search($args['lang'], $lgs);
		$lang = ['.esp','.fra','.ang'][$lix];
		$lana = ['.aes','.afr','.aan'][$lix];
		$les = '.esp';

		$type = $args['type'];
		switch($type) {
			case 'codex':		
			case 'glyphe':		
			case 'personne':		
				$pres = $args['text'];
				break;
			default:
				$pres = str_replace('.', '_', strtolower($args['text']));
				break;
		}
		$txt = '';
		if(file_exists(getcwd().$dir.$pres.$lang)) $txt = file_get_contents(getcwd().$dir.$pres.$lang);
		if(!$txt) $txt = file_get_contents(getcwd().$dir.$pres.$les);
		$txt = utf8_encode(nl2br($txt));
		return array('text'=>$txt, 'xxx'=>getcwd().$dir.$pres.$lang);		
	}
	
	static public function GetAnal($args) {
		$lang = $args['lang'];
		$word = strtolower($args['word']);
		if($args['norma']) $word = GDN::normalize($word);
		$id = $args['id'];
		$cid = intval($id) ? "CodexId=$id and" : "";
		
		
		$sql = "select Trans from `##_CEN-Mot` where $cid (Paleo='$word' or Trans='$word') limit 1";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(!$pdo->rowCount()) return array('success'=>false, 'nahuatl'=>$word);		
		foreach($pdo as $p) $word = $p['Trans'];
		
		$tran = self::getTrans($cid, $word, $lang);
		
		$sql = "select Decomposition,Racine1,Racine2,Racine3,Racine4,Racine5 from `##_CEN-Racine` where $cid Mot2='$word' limit 1";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(!$pdo->rowCount()) return array('success'=>false, 'nahuatl'=>$word);		
		$rs = array();
		foreach($pdo as $p) {
			$anal = $p['Decomposition'];
			for($i = 1; $i < 6; $i++) {
				$t = 'Racine'.$i;
				$r = trim($p[$t]);
				if($r) $rs[] = ['root'=>$r, 'trans'=>self::getTrans($cid, $r, $lang)];
			}
		}

		$cx = Sys::getOneData('CEN', 'Codex/'.$id);
		$dir = self::getDir($d->userCreate, $d->Repertoire);
		

		return array('success'=>true, 'title'=>$cx->Titre, 'nahuatl'=>$word, 'trans'=>$tran, 'anal'=>$anal, 'roots'=>$rs);
	}
	
	static private function getTrans($cid, $word, $lang) {
		$sql = "select Sens,Sens2,Sens3 from `##_CEN-Sens` where $cid Element='$word' limit 1";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
//klog::l($sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(!$pdo->rowCount()) return '';
		
		switch($lang) {
			case 'es': $t = 'Sens'; break;
			case 'fr': $t = 'Sens2'; break;
			case 'en': $t = 'Sens3'; break;
		}
		foreach($pdo as $p) return $p[$t];
	}
	
	static private function getPicts($cid, $word) {
		$sql = "
select Cote from `kob-CEN-Glyphe` where $cid Lecture='xolotl' group by CodexId
union select Cote from `kob-CEN-Personnage` where $cid Lecture='xolotl' group by CodexId		
";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(!$pdo->rowCount()) return '';
		
	}
	
	
	
}