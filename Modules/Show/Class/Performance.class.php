<?php

class Performance extends genericClass {

	
	public static function GetPerf($args) {
		$cond = $args['cond'];
		
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		$uid = $usr->Id;

		if($cond->type == 'preview') return self::getPreview($cond, $logged, $uid);
		if($cond->type == 'details') return self::getDetails($cond, $logged, $uid);
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
			case 1: $whr .= " and s.userCreate=$uid"; break;
			case 2: $frm .= "inner join `kob-Show-FavPerformance` fp on fp.PerformanceId=s.Id and fp.UserId=$uid"; break;
			case 3:
				if($cond->cat) $whr .= " and s.CategoryId in ($cond->cat)";
				if($cond->year) $whr .= " and s.Year='$cond->year'";
				if($cond->dom) $frm .= " inner join `kob-Show-PerformanceDomains` pd on pd.PerformanceId=s.Id and pd.Domain in ($cond->dom)";
				if($cond->genre) $frm .= "inner join `kob-Show-PerformanceGenres` pg on pd.PerformanceId=s.Id and pg.Genre in ($cond->genre)";
				if($cond->crew) $frm .= " inner join `kob-Show-Crew` cw on cw.PerformanceId=s.Id and cw.PeopleId in ($cond->crew)";
		}
		
		$sql .= $frm.$whr." order by c.Category,s.tmsEdit";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$ps = $GLOBALS['Systeme']->Db[0]->query($sql);
		
		$favs = [];
		$data = [];
		$acat = [];
		$rcat = 0;
		$ncat = '';
		foreach($ps as $r) {
			$p = genericClass::createInstance('Show', 'Performance');
			$p->initFromId($r['Id']);
			$cat = $p->CategoryId;
			if($cat != $rcat) {
				if($rcat) $data[] = ['count'=>count($acat), 'name'=>$ncat, 'id'=>$rcat, 'data'=>$acat];
				$acat = [];
				$rcat = $cat;
				$ncat = $r['Category'];
			}
			$d = new stdClass();
			$d->id = $p->Id;
			$d->mine = $logged && $p->userCreate == $uid;
			$d->title = $p->Title;
			$d->subtitle = $p->Subtitle;
			$d->year = $p->Year;
			$d->category = $cat;
			$d->Country = $r['Country'];
			$d->maturity = $p->MaturityId ? $r['Maturity'].'+' : 'NR';
			$d->domains = $dom = self::getArray($p->getChildren('Domain'), 'Domain');
			$d->votes = $p->Votes;
			$d->rating = $p->Rating;
			$d->comments = $p->Comments;
			$main = '';
			$picts = self::getPictures($p, $main);
			if(empty($main) && $pict['count']) $main = $pict['data'][0]; 
			$d->picts = $picts;
			$d->pict = $main;

			$d->fav = $logged ? Sys::getCount('Show', "FavPerformance/UserId=$uid&PerformanceId=".$p->Id) : 0;

			$acat[] = $d;
			if($d->fav && $cond->mode == 0) $favs[] = $d;
		}
		if($rcat) $data[] = ['count'=>count($acat), 'name'=>$ncat, 'id'=>$rcat, 'data'=>$acat];
		if(count($favs)) {
			klog::l(count($data));
			array_splice($data, 0, 0, [['count'=>count($favs), 'name'=>'Favourites', 'id'=>0, 'data'=>$favs]]);
			klog::l(count($data));
		}
		return ['success'=>true, 'logged'=>$logged, 'count'=>count($data), 'data'=>$data, 'sql'=>$sql];
	}

	private static function getDetails($cond, $logged, $uid) {
			$id = $cond->id;

			$o = Sys::getOneData('Show', "Performance/$id");
			//$cat = self::getArray($o->getParents('Category'), 'Category');
			$dom = self::getArray($o->getChildren('Domain'), 'Domain');
			$gen = self::getArray($o->getChildren('Genre'), 'Genre');
			
			if($o->MaturityId) {
				$tmp = Sys::getOneData('Show', 'Maturity/'.$o->MaturityId);
				$mat = $tmp->Maturity.'+';
			}
			else $mat = 'NR';

			
			
			
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
				'domains'=>$dom, 'genres'=>$gen, 'duration'=>$o->Duration,
				'maturity'=>$mat, 'public'=>$pub, 'picts'=>$picts, 'pict'=>$main, 'crew'=>$crew];
			return ['success'=>true, 'logged'=>$logged, 'show'=>$d];
	}
	
	private static function getArray($rs, $field) {
		$tmp = array();
		foreach($rs as $r) $tmp[] = $r->Id; // ['id'=>$r->Id, 'name'=>$r->$field];
		return $tmp; //array('count'=>count($tmp), 'data'=>$tmp);
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
		if($usr->Public) return array('success'=>false, 'logged'=>false);
		
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
		return array('success'=>true, 'logged'=>true);
	}
}