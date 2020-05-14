<?php

class Performance extends genericClass {

	
	public static function GetPerf($args) {
		$cond = $args['cond'];
		
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		$uid = $usr->Id;

		if($cond->type == 'preview') return sels::getPreview($cond, $logged, $uid);
		if($cond->type == 'details') return sels::getDetails($cond, $logged, $uid);
		return array();
	}
	
	private static function getPreview($cond, $logged, $uid) {

		$sql = "select s.Id,s.Title,s.Subtitle,s.CategoryId,s.MaturityId,s.`Year`,c.Category,mt.Maturity,cy.Country";
		$frm = " from `kob-Show-Performance` s "
				."left join `kob-Show-Category` c on c.Id=s.CategoryId "
				."left join `kob-Show-Maturity` mt on mt.Id=s.MaturityId "
				."left join `kob-Show-Country` cy on cy.Id=s.CountryId ";

		$whr = ' where 1';
		switch($cond->mode) {
			case 1: $whr .= "and s.userCreate=$uid"; break;
			case 2: $frm .= "inner join `kob-Show-FavPerformance` fp on fp.PerformanceId=s.Id and fp.UserId=$id"; break;
			case 3:
				if($conf->cat) $whr .= " and s.CategoryId in ($conf->cat)";
				if($conf->year) $whr .= " and s.Year='$conf->year'";
				if($conf->dom) $frm .= " inner join `kob-Show-PerformanceDomains` pd on pd.PerformanceId=s.Id and pd.Domain in ($conf->dom)";
				if($conf->genre) $frm .= "inner join `kob-Show-PerformanceGenres` pg on pd.PerformanceId=s.Id and pg.Genre in ($conf->genre)";
				if($conf->crew) $frm .= " inner join `kob-Show-Crew` cw on cw.PerformanceId=s.Id and cw.PeopleId in ($conf->crew)";
		}
		
		$sql .= $frm.$whr." order by s.CategoryId,s.tmsEdit";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$ps = $GLOBALS['Systeme']->Db[0]->query($sql);
		
		$perf = ['count'=>0, 'data'=>[]];
		$acat = [];
		$rcat = 0;
		$ncat = '';
		foreach($ps as $p) {
			$cat = $p->CategoryId;
			if($cat != $rcat) {
				if($rcat) $perf['data'][] = ['count'=>count($acat), 'name'=>$ncat, 'id'=>$rcat, 'data'=>$acat];
				$acat = [];
				$rcat = $cat;
				$ncat = $p->Category;
			}
			$d = new stdClass();
			$d->id = $p->Id;
			$d->title = $p->Title;
			$d->subtitle = $p->Subtitle;
			$d->year = $p->Year;
			$d->maturity = $p->MaturityId ? $p->Maturity.'+' : 'NR';
			$d->domains = $dom = self::getArray($o->getChildren('Domain'), 'Domain');
			$main = '';
			$picts = self::getPictures($p, $main);
			if(empty($main) && $pict['count']) $main = $pict['data'][0]; 
			$d->picts = $picts;
			$d->pict = $main;

			$d->fav = $logged ? Sys::getCount('Show', "FavPerformance/UserId=$uid&PerformanceId=".$p->Id) : 0;

			$acat[] = $d;
		}
		if($rcat) $perf['cat'][] = ['count'=>count($acat), 'name'=>$ncat, 'id'=>$rcat, 'data'=>$acat];
		return ['success'=>true, 'perf'=>$perf];
	}

	private static function getDetails($cond, $logged, $uid) {
			$id = $cond->id;

			$o = Sys::getOneData('Show', "Performance/$id");
			$cat = self::getArray($o->getParents('Category'), 'Category');
			$dom = self::getArray($o->getChildren('Domain'), 'Domain');
			$gen = self::getArray($o->getChildren('Genre'), 'Genre');
			
			if($o->MaturityId) {
				$tmp = Sys::getOneData('Show', 'Maturity/'.$o->MaturityId);
				$mat = $tmp->Maturity.'+';
			}
			else $mat = 'NR';

//			$tmp = $o->getChildren('Maturity');
//			$mat = count($tmp) ? $tmp[0]->Maturity : 'NR';
			$pub = self::getArray($o->getChildren('Public'), 'Public');
			$plan = self::getArray($o->getChildren('Language'), 'Language');
//			$dom = self::getChildrenList($o, 'Domain');
//			$gen = self::getChildrenList($o, 'Genre');
//			$cs = $o->getChildren('Maturity');
//			$mat = count($cs) ? $cs[0]->Maturity : ''; 
//			$cs = $o->getChildren('Public');
//			$pub = count($cs) ?$cs[0]->Public : '';
			$main = '';
			$picts = self::getPictures($o, $main);
			$crew = self::getCrew($o, '');
			
			$d = ['id'=>$id, 'title'=>$o->Title, 'subtitle'=>$o->Subtitle, 'year'=>$o->Year,
				'summary'=>$o->Summary, 'descriton'=>$o->Description, 
				'category'=>$cat, 'domains'=>$dom, 'genres'=>$gen, 'duration'=>$o->Duration,
				'maturity'=>$mat, 'public'=>$pub, 'picts'=>$picts, 'pict'=>$main, 'crew'=>$crew];
			return ['success'=>true, 'show'=>$d];
	}
	
	private static function getArray($rs, $field) {
		$tmp = array();
		foreach($rs as $r) $tmp[] = ['id'=>$r->Id, 'name'=>$r->$field];
		return array('count'=>count($tmp), 'data'=>$tmp);
	}

//	private static function getChildrenList($parent, $children) {
//		$rs = $parent->getChildren($children);
//		$tmp = array();
//		foreach($rs as $r) $tmp[] = ['id'=>$r->Id, 'name'=>$r->$children];
//		return array('count'=>count($tmp), 'data'=>$tmp);
//	}
		
	private static function getPictures($parent, &$main) {
		$rs = $parent->getChildren('Medium/MediumTypeId=1');
		$main = '';
		$tmp = array();
		foreach($rs as $r) {
			if(empty($main) || $r->MainPicture) $main = $r->Medium;
			$tmp[] = ['id'=>$r->Id, 'pict'=>$r->Medium, 'title'=>$r->Description, 'subtitle'=>'', 'year'=>$r->Year];
		}
		return array('count'=>count($tmp), 'data'=>$tmp);
	}
	
	private static function getCrew($parent, $mode) {
		$sql = "select p.Id,p.FisrtName,p.MiddleName,p.Surname,c.Playing "
			."from `##_Show-Crew` c "
			."inner join `##_Show-People` p on p.Id=c.PeopleId "
			."where c.Id=".$parent->Id;
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$tmp = array();
		foreach($rs as $r) {
			$nam = trim($r['FirstName'].' '.$r['MiddleName'].' '.$r['Surname']);
			$tmp[] = ['id'=>$r[Id], 'name'=>$name, 'playing'=>$r['Playing']];
		}
	}
	
	public static function SetFavourite($args) {
		$usr = Sys::$User;
		if($usr->Public) return array('success'=>false);
		
		$uid = $usr->Id; 
		$show = $args['show'];
		
		if($show->fav) {
			$fav = genericClass::createInstance('Show', 'FavPerformance');
			$fav->addParent($usr);
			$fav->PerformanceId = $show->id;
			$fav->Save();
		}
		else {
			$fav = Sys::getOneData('Show', "FavPerformance/UserId=$uid&PerformanceId=".$show->id);
			if($fav) $fav->Delete();
		}
		return array('success'=>true);
	}
}