<?php

class Performance extends genericClass {

	
	public static function GetPerf($args) {
		$cond = $args['cond'];
		
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		$id = $usr->Id;

		if($cond->type == 'preview') {
			$perf = array();
			$o = Sys::getOneData('Show', 'Category/Category='.$cond->cat);
			$ps = $o->getChildren('Performance');
			foreach($ps as $p) {
				$d = new stdClass();
				$d->id = $p->Id;
				$d->title = $p->Title;
				$d->subtitle = $p->Subtitle;
				$d->year = $p->Year;
				if($p->MaturityId) {
					$t = Sys::getOneData('Show', 'Maturity/'.$p->MaturityId);
					$d->maturity = $t->Maturity.'+';
				}
				else $d->maturity = 'NR';
				$d->domains = $dom = self::getArray($o->getChildren('Domain'), 'Domain');
				$main = '';
				$picts = self::getPictures($p, $main);
				if(empty($main) && $pict['count']) $main = $pict['data'][0]; 
				$d->picts = $picts;
				$d->pict = $main;
				
				$d->fav = $logged ? Sys::getCount('Show', "FavPerformance/UserId=$id&PerformanceId=".$p->Id) : 0;
				
				$perf[] = $d;
			}
			return array('cat'=>$cond->cat, 'count'=>count($perf), 'data'=>$perf);
		}
		if($cond->type == 'details') {
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
			return ['show'=>$d];
		}
		return array();
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