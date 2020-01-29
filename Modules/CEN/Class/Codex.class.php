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
				$dic = self::getElementBasic($whr);
				return array('elements'=>$dic);
		}
	}
	
	static private function getGlyphe($tbl, $whr) {
		$typ = $tbl == 'Glyphe' ? 'Type' : "'person' as Type";
		$sql = "select CodexId,Id,Cote,Lecture,$typ from `##_CEN-$tbl` $whr";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);

		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$gly = array();
		foreach($pdo as $d) {
			$gly[] = array('codexId'=>$d['CodexId'], 'id'=>$d['Id'], 'cote'=>trim($d['Cote']), 'type'=>$d['Type'], 'lecture'=>trim($d['Lecture']));
		}
		return $gly;
	}
	
	static private function getElementBasic($whr) {
		$sql = "select distinct e.CodexId,e.Id,e.Cote,e.Theme,e.Element,s.Sens,s.Sens2,ifnull(v.Valeur,p.Valeur) as Valeur,f.Forme ".
			"from `##_CEN-Element` e ".
			"left join `##_CEN-Sens` s on s.CodexId=e.CodexId and s.Element=e.Element ".
			"left join `##_CEN-Valeur` v on v.CodexId=e.CodexId and v.Cote=e.Cote and v.Theme=e.Theme ".
			"left join `##_CEN-PValeur` p on p.CodexId=e.CodexId and p.Cote=e.Cote and p.Theme=e.Theme ".
			"left join `##_CEN-Forme` f on f.CodexId=e.CodexId and f.Theme=e.Theme ".
			"$whr";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);

		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$dic = array();
		foreach($pdo as $d) {
			$dic[] = array('codexId'=>$d['CodexId'], 'id'=>$d['Id'], 'cote'=>trim($d['Cote']), 'theme'=>$d['Theme'], 
				'element'=>trim($d['Element']), 'meaning'=>$d['Sens'], 'meaning2'=>$d['Sens2'], 
				'valeur'=>$d['Valeur'], 'forme'=>$d['Forme']);
		}
		return $dic;
	}	

	static private function getElementValeur($whr) {
		$sel = "select e.CodexId,e.Id,e.Cote,e.Theme,e.Element,s.Sens,s.Sens2,v.Valeur,f.Forme from ";
		$sel1 = "v inner join `##_CEN-Element` e on e.CodexId=v.CodexId and e.Cote=v.Cote and e.Theme=v.Theme ".
			"left join `##_CEN-Sens` s on s.CodexId=e.CodexId and s.Element=e.Element ".
			"left join `##_CEN-Forme` f on f.CodexId=e.CodexId and f.Theme=e.Theme ".
			"$whr";
		$sql = "$sel `##_CEN-Valeur` $sel1 union $sel `##_CEN-PValeur` $sel1 order by codexId,Valeur,Element";

		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$dic = array();
		foreach($pdo as $d) {
			$dic[] = array('codexId'=>$d['CodexId'], 'id'=>$d['Id'], 'cote'=>trim($d['Cote']), 'theme'=>$d['Theme'], 
				'element'=>trim($d['Element']), 'meaning'=>$d['Sens'], 'meaning2'=>$d['Sens2'], 
				'valeur'=>$d['Valeur'], 'forme'=>$d['Forme']);
		}
		return $dic;
	}	
	
	static function getElementForme($whr) {
		$sql = "select distinct e.CodexId,e.Id,e.Cote,e.Theme,e.Element,s.Sens,s.Sens2,ifnull(v.Valeur,p.Valeur) as Valeur,f.Forme ".
			"from `##_CEN-Forme` f ".
			"inner join `##_CEN-Element` e on e.CodexId=f.CodexId and e.Theme=f.Theme ".
			"left join `##_CEN-Sens` s on s.CodexId=e.CodexId and s.Element=e.Element ".
			"left join `##_CEN-Valeur` v on v.CodexId=e.CodexId and v.Cote=e.Cote and v.Theme=e.Theme ".
			"left join `##_CEN-PValeur` p on p.CodexId=e.CodexId and p.Cote=e.Cote and p.Theme=e.Theme ".
			"$whr order by f.CodexId,e.Cote";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);

		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$dic = array();
		foreach($pdo as $d) {
			$dic[] = array('codexId'=>$d['CodexId'], 'id'=>$d['Id'], 'cote'=>trim($d['Cote']), 'theme'=>$d['Theme'], 
				'element'=>trim($d['Element']), 'meaning'=>$d['Sens'], 'meaning2'=>$d['Sens2'], 
				'valeur'=>$d['Valeur'], 'forme'=>$d['Forme']);
		}
		return $dic;
	}	

	static function getElementSens($whr) {
		$sql = "select distinct e.CodexId,e.Id,e.Cote,e.Theme,e.Element,s.Sens,s.Sens2,ifnull(v.Valeur,p.Valeur) as Valeur,f.Forme ".
			"from `##_CEN-Sens` s ".
			"inner join `##_CEN-Element` e on e.CodexId=s.CodexId and e.Element=s.Element ".
			"left join `##_CEN-Valeur` v on v.CodexId=e.CodexId and v.Cote=e.Cote and v.Theme=e.Theme ".
			"left join `##_CEN-PValeur` p on p.CodexId=e.CodexId and p.Cote=e.Cote and p.Theme=e.Theme ".
			"left join `##_CEN-Forme` f on f.CodexId=e.CodexId and f.Theme=e.Theme ".
			"$whr order by s.CodexId,e.Cote";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);

		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$dic = array();
		foreach($pdo as $d) {
			$dic[] = array('codexId'=>$d['CodexId'], 'id'=>$d['Id'], 'cote'=>trim($d['Cote']), 'theme'=>$d['Theme'], 
				'element'=>trim($d['Element']), 'meaning'=>$d['Sens'], 'meaning2'=>$d['Sens2'], 
				'valeur'=>$d['Valeur'], 'forme'=>$d['Forme']);
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

	
	static private function wordList($sql) {
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
		$list = $args['list'] == 'true';

		$cond = json_decode($args['cond']);
		if($cdx == 'all' || $cdx == '' || $cdx == 'null') $cdx = '';
		
		$type = isset($args['type']) ? $args['type'] : $cond->type;
		$gly = $cond->glyphe;
		$elm = $cond->element;
		
		$mode = '';
		if($type == 'glyphe' && $gly == 'cote')
			$mode = 'start';
		elseif($type == 'element' && $elm == 'theme')
			$mode = 'start';
		else $mode = $cond->mode;
		
		switch($mode) {
			case 'start': $mode = "like '$word%'"; break;
			case 'all': $mode = "= '$word'"; break;
			case 'any': $mode = "like '%$word%'"; break;
		}

		switch($type) {
			case 'glyphe':
				if($cdx) $cdx = "CodexId in ($cdx) and";
				
				if($gly == 'multiple') {
					if($list) {
						$sql = "select distinct Element as word from `##_CEN-Element` where $cdx Element like '$word%'";
						return self::wordList($sql);
					}
					$mul = $cond->multiple;
					if(!count($mul)) return array('glyphes'=>[], 'personnes'=>[]);
					$whr = "";
					foreach($mul as $m) {
						$whr .= $whr ? " and" : "where $cdx";
						$whr .= " Element like '% $m,%'";
					}
					$whr .= " order by CodexId,Cote";

				}
				else {
					$fld = $gly == 'cote' ? 'Cote' : 'Lecture';
					$whr = "where $cdx $fld $mode";
					if($list) {
						$sql = "select distinct $fld as word from `##_CEN-Glyphe` $whr ".
							"union select distinct $fld as word from `##_CEN-Personnage` $whr ";
						return self::wordList($sql);
					}
					$whr .= " group by CodexId order by CodexId,Cote";
				}
				
				$gly = self::getGlyphe('Glyphe', $whr);
				$per = self::getGlyphe('Personnage', $whr);
				self::getCount($gly, 'Glyphe', '', $whr);
				self::getCount($per, 'Personnage', '', $whr);
				return array('glyphes'=>$gly, 'personnes'=>$per, 'w'=>$whr);

			case 'element':			
				if($list) {
					if($cdx) $cdx = "a.CodexId in ($cdx) and";
					switch($elm) {
						case 'designation': 
							$sql = "select distinct a.Element as word from `##_CEN-Element` a where $cdx a.Element $mode";
							return self::wordList($sql);
						case 'theme':
							$fld = $args['lang'] == 'fr' ? 'Sens2' : 'Sens';
							$sql = "select distinct concat(a.Theme,' <i>',a.Element,'</i> ',b.$fld) as word ".
								"from `##_CEN-Element` a left join `##_CEN-Sens` b on b.CodexId=a.CodexId and b.Element=a.Element ".
								"where $cdx a.Theme $mode";
							return self::wordList($sql);
						case 'valeur':
							$sel = "select distinct a.Valeur as word from";
							$sel1 = "a inner join `##_CEN-Element` e on e.CodexId=a.CodexId and e.Theme=a.Theme ";
							$sel1 .= "where $cdx a.Valeur $mode";
							$sql = "$sel `##_CEN-Valeur` $sel1 union $sel `##_CEN-PValeur` $sel1 ";
							return self::wordList($sql);
						case 'forme':
							$sql = "select distinct a.Forme as word from `##_CEN-Forme` a inner join `##_CEN-Element` e ".
								"on e.CodexId=a.CodexId and e.Theme=a.Theme where $cdx cast(a.Forme as unsigned) = cast('$word' as unsigned) ";
							return self::wordList($sql);
						case 'traduction':
							$fld = $args['lang'] == 'fr' ? 'Sens2' : 'Sens';
							$sql = "select distinct a.$fld as word from `##_CEN-Sens` a inner join `##_CEN-Element` e ".
								"on e.CodexId=a.CodexId and e.Element=a.Element where $cdx a.$fld $mode";
							return self::wordList($sql);
					}
				}

				$whr = "where ".($cdx ? "e.CodexId in ($cdx) and" : "");
				$sql = "";
				
				switch($elm) {
					case 'designation': 
						$whr .= " e.Element $mode"; break;
					case 'theme':
						$whr .= " e.Theme $mode"; break;
					case 'valeur':
						$grp = $cond->elements ? '' : "group by e.CodexId,v.Valeur,e.Element";
						$whr .= " v.Valeur $mode $grp";
						return array('elements'=>self::getElementValeur($whr));
					case 'forme':
						$grp = $cond->elements ? '' : "group by f.CodexId,f.Forme,e.Element";
						$whr .= " cast(f.Forme as unsigned) = cast('$word' as unsigned) $grp";
						return array('elements'=>self::getElementForme($whr));
					case 'traduction':
						$fld = $args['lang'] == 'fr' ? 'Sens2' : 'Sens';
						$grp = $cond->elements ? '' : "group by f.CodexId,s.$fld,e.Element";
						$whr .= " s.$fld $mode $grp";
						return array('elements'=>self::getElementSens($whr));
				}


				
				$grp = $cond->elements ? '' : 'group by e.CodexId';
				$whr .= " $grp order by e.CodexId,e.Cote";
				
				$dic = self::getElementBasic($whr);
				if($grp) self::getCount($dic, 'Element', 'e', $whr);
				return array('elements'=>$dic);

				
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
				$elm = self::getElementBasic($whr);
				return array('citations'=>$cit, 'elements'=>$elm, 'valSup'=>$valSup);

			case 'glyphe-lecture':
				$org = json_decode($args['glyphe']);
				$cid = $org->codexId;
				$gid = $org->id;
				$tbl = $org->type == 'person' ? 'Personne' : 'Glyphe';
				$whr = "where CodexId=$cid and Lecture $mode and Id<>$gid order by Cote";
				$gly = self::getGlyphe($tbl, $whr);
				return array('glyphe'=>$gly);
				
//			case 'element-detail':
//				$lang = $args['lang'];
////				$elem = $args['element'];
//				$them = $args['theme'];
//				$id = $args['id'];
//				$sql = "
//select distinct Valeur from `##_CEN-Valeur` where CodexId=$id and Theme='$them'
//union select distinct Valeur from `##_CEN-PValeur` where CodexId=$id and Theme='$them'
//";
//				$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
//				$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
//				$val = '';
//				foreach($pdo as $p)	{
//					if($val) $val .=', ';
//					$val .= $p['Valeur'];
//				}
//				return array('valeur'=>$val,'sql'=>$sql);
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
				$pres = strtolower($args['text']);
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