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
					$dic[] = array('id'=>$id, 'code'=>$d->Code, 'title'=>strtolower($d->Titre), 'selected'=>true, 'imgSel'=>false, 
						'dir'=>self::getDir($d->userCreate, $d->Repertoire), 'img'=>'img_01.bmp');
					$dicId[$id] = strtolower($d->Titre);
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
				$whr = "where g.CodexId=$id and substr(g.Cote,1,$ln)='$ext' order by g.Cote";
				$gly = self::getGlyphe('Glyphe', $whr);
				$per = self::getGlyphe('Personnage', $whr);
				return array('glyphes'=>$gly, 'personnes'=>$per);
				
			case 'element':
				$whr = "where CodexId=$id and Cote='$ext' order by Theme";
				$dic = self::getTlaElement($whr);
				return array('elements'=>$dic);
		}
	}
	
	private static function getGlyphe($tbl, $whr) {
		$sql = "
select g.CodexId,g.Id,g.Cote,g.Lecture,c.userCreate,c.Repertoire
from `##_CEN-$tbl` g
inner join `##_CEN-Codex` c on c.Id=g.CodexId
$whr";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$gly = array();
		foreach($pdo as $d) {
			$gly[] = array('codexId'=>$d['CodexId'], 'id'=>$d['Id'], 'cote'=>trim($d['Cote']), 'lecture'=>trim($d['Lecture']), 'dir'=>self::getDir($d['userCreate'], $d['Repertoire']));
		}
		return $gly;
	}
	
	static function getTlaElement($whr) {
		$sql = "
select e.CodexId,e.Id,e.Cote,e.Theme,e.Element,c.userCreate,c.Repertoire,c.Titre,ifnull(v0.Valeur,ifnull(v1.Valeur,'')) as Valeur
from `##_CEN-Element` e
inner join `##_CEN-Codex` c on c.Id=e.CodexId
left join `##_CEN-Valeur` v0 on v0.Cote=e.Cote
left join `##_CEN-PValeur` v1 on v1.Cote=e.Cote
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
				if($cdx) $cdx = "g.CodexId in ($cdx) and";
				$whr = "where $cdx (g.Lecture $mode or g.Cote $mode) group by g.CodexId order by c.Code";
				$gly = self::getGlyphe('Glyphe', $whr);
				$per = self::getGlyphe('Personnage', $whr);
				return array('glyphes'=>$gly, 'personnes'=>$per);

			case 'glyphe-elem':
				$el = $args['element'];
				$id = $args['id'];
				$whr = "where g.CodexId=$id and g.Element like '% $el,%' order by g.Cote";
				$gly = self::getGlyphe('Glyphe', $whr);
				$per = self::getGlyphe('Personnage', $whr);
				return array('glyphes'=>$gly, 'personnes'=>$per);

			case 'element':
				$grp = $args['elements'] == 'true' ? '' : 'group by e.CodexId';
				if($cdx) $cdx = "e.CodexId in ($cdx) and";
				$whr = "where $cdx (e.Element $mode or e.Cote $mode or e.Theme $mode) $grp order by c.Code";
				$dic = self::getTlaElement($whr);
				return array('elements'=>$dic);

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
				switch($args['text']) {
					case 'etude': $pres = 'document'; break;
					case 'dict': $pres = 'text_dic'; break;
					case 'biblio': $pres = 'biblio'; break;
					case 'varia': $pres = 'v_expose'; break;
					case 'thanx': $pres = 'remer_c'; break;
					case 'credit': $pres = 'credits'; break;
					case 'real': $pres = 'txt_reel'; $dir = '/Home/2/CEN/Codex/Aide/'; $lang = $lana; $les = '.aes'; break;
				}
		}
		$txt = '';
		if(file_exists(getcwd().$dir.$pres.$lang)) $txt = file_get_contents(getcwd().$dir.$pres.$lang);
		if(!$txt) $txt = file_get_contents(getcwd().$dir.$pres.$les);
		$txt = str_replace("\n", '<br />', str_replace("\r", '', utf8_encode($txt)));
		return array('text'=>$txt, 'xxx'=>getcwd().$dir.$pres.$lang);		
	}
	
}