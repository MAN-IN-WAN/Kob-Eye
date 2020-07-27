<?php

class Performance extends genericClass {
	
	public static function SavePerf($args) {
		$s = $args['show'];
		$id = $s->id;
		$dom = [];
		$gen = [];
		$o = genericClass::createInstance('Show', 'Performance');
		if($id) {
			$o->initFromId($id);
			$dom = self::getArray($o->getChildren('Domain'), 'Domain');
			$gen = self::getArray($o->getChildren('Genre'), 'Genre');
			$lng = self::getArray($o->getChildren('Language'), 'Language');
		}
		
		$o->Title = $s->title;
		$o->Subtitle = $s->subtitle;
		$o->Summary = $s->summary;
		$o->Description = $s->description;
		$o->Year = $s->year;
		$o->Duration = $s->duration;
		$o->MaturityId = $s->maturityId;
		$o->CountryId = $s->countryId;
		$o->StateId = $s->stateId;
		$o->CityId = $s->cityId;
		self::setChild($o, 'Category', $s->categoryId);
		self::setChildren($o, 'Domain', $dom, $s->domains);
		self::setChildren($o, 'Genre', $gen, $s->genres);
		self::setChildren($o, 'Language', $lng, $s->languages);
		
		$o->Save();
		return array('success'=>1, 'id'=>$o->Id);
	}
	
	public static function DeletePerf($args) {
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		if(!$logged) return ['success'=>false, 'logged'=>false];
		
		$id = $args['id'];
		$o = genericClass::createInstance('Show', 'Performance');
		$o->initFromId($id);
		$cs = $o->getChildren('Domain');
		foreach($cs as $c) $c->Delete();
		$cs = $o->getChildren('Genre');
		foreach($cs as $c) $c->Delete();
		$cs = $o->getChildren('Language');
		foreach($cs as $c) $c->Delete();
		$cs = $o->getChildren('Crew');
		foreach($cs as $c) $c->Delete();
		$cs = $o->getChildren('Medium');
		foreach($cs as $c) $c->Delete();
		$o->Delete();
		return array('success'=>1);
	}

	
	private static function setChild($obj, $child, $id) {
		$c = Sys::getOneData('Show', "$child/$id");
		if($c) $obj->addParent($c);
	}
	
	private static function setChildren($obj, $child, $old, $new) {
		foreach($new as $k0=>$v) {
			$k1 = array_search($v, $old, true);
			if($k1 !== false) unset($old[$k0]);
			else $obj->addChild($child, $v);
		}
		foreach($old as $v) $obj->delChild($child, $v);
	}
		
	public static function LoadImage($args) {
		//klog::l('www',)
		$data = base64_decode(explode(',', $args['data'])[1]);
		$name = $args['file'];
		$id = $args['show'];
		mkdir("Home/2/Show/$id");
		$file = "Home/2/Show/$id/$name";
		file_put_contents(getcwd()."/$file", $data);
		$p = Sys::getOneData('Show', 'Performance/'.$id);
		$t = Sys::getOneData('Show', 'MediumType/1');
		$m = genericClass::createInstance('Show', 'Medium');
		$m->Medium = $file;
		$m->addParent($p);
		$m->addParent($t);
		$m->Save();

		$main = '';
		$picts = self::getPictures($p, $main);
		
		return array('success'=>true, 'logged'=>!Sys::$User->Public, 'picts'=>$picts, 'pict'=>$main);
	}
	
	
	public static function GetPerf($args) {
		$cond = $args['cond'];
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		$uid = $usr->Id;

		switch($cond->type) {
			case 'preview':
				return self::getPreview($cond, $logged, $uid);
			case 'details':
				return self::getDetails($cond, $logged, $uid);
		}
		return array();
	}
	
	private static function getPreview($cond, $logged, $uid) {

		$sql = "select s.Id,c.Category,mt.Maturity "; //,cr.Country ";
		$frm = "from `kob-Show-Performance` s ";
		$join = "left join `kob-Show-Category` c on c.Id=s.CategoryId "
				."left join `kob-Show-Maturity` mt on mt.Id=s.MaturityId ";
				//."left join `kob-Show-Country` cr on cy.Id=s.CountryId ";

		$group = false;
		$name = '';
		$whr = "where countryId=$cond->country ";
		switch($cond->mode) {
			case 0: $group = true; break;
			case 1:
				$whr .= "and s.userCreate=$uid ";
				$name = 'My shows';
				break;
			case 2: 
				$frm .= "inner join `kob-Show-FavPerformance` fp on fp.PerformanceId=s.Id and fp.UserId=$uid ";
				$name = 'Favourites';
				break;
			case 3:
				if($cond->category) $whr .= "and s.CategoryId in ($cond->category) ";
				if($cond->year) $whr .= "and s.Year='$cond->year' ";
				if($cond->domain) $join .= "inner join `kob-Show-PerformanceDomains` pd on pd.PerformanceId=s.Id and pd.Domain in ($cond->domain) ";
				if($cond->genre) $join .= "inner join `kob-Show-PerformanceGenres` pg on pd.PerformanceId=s.Id and pg.Genre in ($cond->genre) ";
				if($cond->crew) $join .= "inner join `kob-Show-Crew` cw on cw.PerformanceId=s.Id and cw.PeopleId in ($cond->crew) ";
				if($cond->maturity) $whr .= "and s.MaturityId<>0 and s.MaturityId".($cond->more ? '>=' : '<=')."$cond->maturity ";
				if($cond->state) $whr .= "and s.StateId in ($cond->state) ";
				if($cond->city) $whr .= "and s.StateId in ($cond->city) ";
				if($cond->name) {
					$frm = "from `kob-Show-Crew` w "
						."inner join `kob-Show-Performance` s on s.Id=w.PerformanceId ";
					$whr .= "and match (name) against ('$cond->name') ";
				}
				if($cond->role) {
					$frm = "from `kob-Show-Crew` w "
						."inner join `kob-Show-Performance` s on s.Id=w.PerformanceId ";
					$whr .= "and match (role) against ('$cond->role') ";
				}
				break;
			case 4:
				$txt = $cond->text;
//				$sql = "select s.Id,c.Category,mt.Maturity,s.tmsEdit "
//					."from `kob-Show-Performance` s "
//					."inner join `kob-Show-Category` c on c.Id=s.CategoryId "
//					."left join `kob-Show-Maturity` mt on mt.Id=s.MaturityId "
//					."where s.CountryId=$cond->country and MATCH (Title,Subtitle,Summary,Description) AGAINST ('$txt*' in boolean mode) "
//					."union "
//					."select s.Id,c.Category,mt.Maturity,s.tmsEdit "
//					."from `kob-Show-People` p "
//					."inner join `kob-Show-Crew` w on w.PeopleId=p.Id "
//					."inner join `kob-Show-Performance` s on s.Id=w.PerformanceId "
//					."inner join `kob-Show-Category` c on c.Id=s.CategoryId "
//					."left join `kob-Show-Maturity` mt on mt.Id=s.MaturityId "
//					."where MATCH (Name) AGAINST ('$txt') and s.CountryId=$cond->country "
//					."union "
//					."select s.Id,c.Category,mt.Maturity,s.tmsEdit "
//					."from `kob-Show-Crew` w "
//					."inner join `kob-Show-Performance` s on s.Id=w.PerformanceId "
//					."inner join `kob-Show-Category` c on c.Id=s.CategoryId "
//					."left join `kob-Show-Maturity` mt on mt.Id=s.MaturityId "
//					."where MATCH (Role) AGAINST ('$txt*' in boolean mode) and s.CountryId=$cond->country "
//					."order by tmsEdit, Id desc";
				$sql = "select s.Id,c.Category,mt.Maturity,s.tmsEdit "
					."from `kob-Show-Performance` s "
					."inner join `kob-Show-Category` c on c.Id=s.CategoryId "
					."left join `kob-Show-Maturity` mt on mt.Id=s.MaturityId "
					."where s.Id in ( "
					."select s.Id "
					."from `kob-Show-Performance` s "
					."where s.CountryId=75 and MATCH (Title,Subtitle,Summary,Description) AGAINST ('$txt*' in boolean mode) "
					."union "
					."select s.Id "
					."from `kob-Show-Crew` c "
					."inner join `kob-Show-Performance` s on s.Id=c.PerformanceId "
					."where MATCH (Name) AGAINST ('$txt') and s.CountryId=75 "
					."union "
					."select s.Id "
					."from `kob-Show-Crew` c "
					."inner join `kob-Show-Performance` s on s.Id=c.PerformanceId "
					."where MATCH (Role) AGAINST ('$txt*' in boolean mode) and s.CountryId=75 "
					.") "
					."order by s.tmsEdit ";
					break;
		} 
		
		if($cond->mode != 4) $sql .= $frm.$join.$whr.' order by '.($group ? 'c.Category,' : '').'s.tmsEdit desc, s.Id desc';
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$ps = $GLOBALS['Systeme']->Db[0]->query($sql);
		
		$favs = [];
		$data = [];
		$acat = [];
		$rcat = 0;
		$ncat = $name;
		foreach($ps as $r) {
			$p = genericClass::createInstance('Show', 'Performance');
			$p->initFromId($r['Id']);
			$cat = $p->CategoryId;
			if($group && $cat != $rcat) {
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
			//$d->year = $p->Year;
			$d->categoryId = $cat;
			$d->category = $r['Category'];
			$d->countryId = $p->CountryId;
			//$d->country = $r['Country'];
			//$d->MaturityId = $p->MaturityId;
			$d->maturity = $p->MaturityId ? $r['Maturity'] : 'NR';
			//$d->domains = $dom = self::getArray($p->getChildren('Domain'), 'Domain');
			$d->votes = $p->Votes;
			$d->rating = $p->Rating;
			$d->comments = $p->Comments;
			$main = '';
			$picts = self::getPictures($p, $main);
			if(empty($main) && $pict['count']) $main = $pict['data'][0]; 
			//$d->picts = $picts;
			$d->pict = $main;

			$d->fav = $logged ? Sys::getCount('Show', "FavPerformance/UserId=$uid&PerformanceId=".$p->Id) : 0;

			$acat[] = $d;
			if($group && $d->fav && $cond->mode == 0) $favs[] = $d;
		}
		if(count($acat)) $data[] = ['count'=>count($acat), 'name'=>$ncat, 'id'=>$rcat, 'data'=>$acat];
		if(count($favs)) array_splice($data, 0, 0, [['count'=>count($favs), 'name'=>'Favourites', 'id'=>-1, 'data'=>$favs]]);

		return ['success'=>true, 'logged'=>$logged, 'count'=>count($data), 'data'=>$data, 'group'=>$group, 'sql'=>$sql];
	}

	private static function getDetails($cond, $logged, $uid) {
		$id = $cond->id;
		$p = Sys::getOneData('Show', "Performance/$id");
		
		$sql = "select c.Category,mt.Maturity,cr.Country,st.State,cy.City,u.Initiales "
				."from `kob-Show-Performance` s "
				."left join `kob-Show-Category` c on c.Id=s.CategoryId "
				."left join `kob-Show-Maturity` mt on mt.Id=s.MaturityId "
				."left join `kob-Show-Country` cr on cr.Id=s.CountryId "
				."left join `kob-Show-State` st on st.Id=s.StateId "
				."left join `kob-Show-City` cy on cy.Id=s.CityId "
				."left join `kob-Systeme-User` u on u.Id=s.userCreate "
				."where s.Id=$id limit 1";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$r = $rs->fetch(PDO::FETCH_ASSOC);
				
		$d = new stdClass();
		$d->id = $p->Id;
		$d->uid = $p->userCreate;
		$d->user = $r['Initiales'];
		$d->mine = $logged && $p->userCreate == $uid;
		$d->title = $p->Title;
		$d->subtitle = $p->Subtitle;
		$d->summary = $p->Summary;
		$d->description = $p->Description;
		$d->year = $p->Year;
		$d->duration = $p->Duration;
		$d->categoryId = $p->CategoryId;
		$d->category = $r['Category'];		
		$d->maturityId = $p->MaturityId;
		$d->maturity = $p->MaturityId ? $r['Maturity'] : 'NR';
		$d->domains = $dom = self::getArray($p->getChildren('Domain'), 'Domain');
		$d->genres = $dom = self::getArray($p->getChildren('Genre'), 'Genre');
		$d->publics = $dom = self::getArray($p->getChildren('Public'), 'Public');
		$d->languages = $dom = self::getArray($p->getChildren('Language'), 'Language');
		$d->countryId = $p->CountryId;
		$d->country = $r['Country'];
		$d->stateId = $p->StateId;
		$d->state = $r['State'];
		$d->cityId = $p->CityId;
		$d->city = $r['City'];		
		$d->votes = $p->Votes;
		$d->rating = $p->Rating;
		$d->comments = $p->Comments;	
		$main = '';
		$d->picts = self::getPictures($p, $main);
		$d->pict = $main;
		$d->links = self::getLinks($p);
		$d->crew = self::getCrew($p);
		$dom = Show::getObjsArray('Domain', 'CategoryId='.$d->categoryId);
		
		return ['success'=>true, 'logged'=>$logged, 'show'=>$d, 'domains'=>$dom, 'sql'=>$sql];
	}
	
//	private static function getDuration($dur) {
//		$mins = explode('-', $dur);
//		$dur = '';
//		foreach($mins as $min) {
//		    $h = floor($min / 60);
//			$m = ($min % 60);
//			if($dur) $dur .= '-';
//			$dur .= sprintf('%d:%02d', $h, $m);
//		}
//		return $dur;
//	}
	
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

	private static function getLinks($parent) {
		$rs = $parent->getChildren('Medium/MediumTypeId!=1');
		$tmp = array();
		foreach($rs as $r) {
			if($r->Description) $h = $r->Description;
			else {
				$a = explode('/', $r->Medium);
				$h = $a[2];
				if(substr($h, 0, 4) == 'www.') $h = substr($h, 4);
			}
			$tmp[] = ['id'=>$r->Id, 'url'=>$r->Medium, 'title'=>$h];
		}
		return array('count'=>count($tmp), 'data'=>$tmp);
	}
	
	public static function AddLink($args) {
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		if(!$logged) return ['success'=>false, 'logged'=>false];
		
		$link = $args['link'];
		$pid = $args['perfId'];
		$p = Sys::getOneData('Show', 'Performance/'.$pid);
		if($link->id) $m = Sys::getOneData('Show', 'Medium/'.$link->id);
		else {
			$m = genericClass::createInstance('Show', 'Medium');
			$m->addParent($p);
			$t = Sys::getOneData('Show', 'MediumType/9');
			$m->addParent($t);
		}
		$m->Medium = $link->url;
		$m->Description = $link->title;
		$m->Save();
		
		return ['success'=>true, 'logged'=>true, 'links'=>self::getLinks($p)];
	}

	public static function DelLink($args) {
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		if(!$logged) return ['success'=>false, 'logged'=>false];
		
		$id = $args['id'];
		$pid = $args['perfId'];
		$p = Sys::getOneData('Show', 'Performance/'.$pid);
		$m = Sys::getOneData('Show', 'Crew/'.$id);
		$m->Delete();

		return ['success'=>true, 'logged'=>true, 'links'=>self::getLinks($p)];
	}

	public static function DelPict($args) {
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		if(!$logged) return ['success'=>false, 'logged'=>false];
		
		$m = genericClass::createInstance('Show', 'Medium');
		$m->initFromId($args['id']);
		$m->Delete();
		
		$p = Sys::getOneData('Show', 'Performance/'.$args['perfId']);
		$main = '';
		$pict = self::getPictures($p, $main);
		return ['success'=>true, 'logged'=>true, 'picts'=>$picts, 'pict'=>$main];
	}

	private static function getCrew($parent) {
//		$sql = "select p.Id,p.Name,c.Role "
//			."from `##_Show-Crew` c "
//			."inner join `##_Show-People` p on p.Id=c.PeopleId "
//			."where c.PerformanceId=".$parent->Id;
		$sql = "select Id,Name,Role from `##_Show-Crew` where PerformanceId=".$parent->Id;
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$tmp = array();
		foreach($rs as $r) {
			$tmp[] = ['id'=>$r[Id], 'name'=>$r['Name'], 'role'=>$r['Role']];
		}
		return $tmp;
	}
	
	public static function SetFavourite($args) {
		$usr = Sys::$User;
		if($usr->Public) return array('success'=>false, 'logged'=>false);
		
		$uid = $usr->Id; 
		$show = $args['show'];
		
		$fav = Sys::getData('Show', "FavPerformance/UserId=$uid&PerformanceId=".$show->id);
		if($show->fav) {
			if(!$fav || !count($fav)) {
				$fav = genericClass::createInstance('Show', 'FavPerformance');
				$fav->addParent($usr);
				$fav->PerformanceId = $show->id;
				$fav->Save();
			}
		}
		else {
			foreach($fav as $f) $f->Delete();
		}
		$fav = Sys::getCount('Show', 'FavPerformance/UserId='.$uid);
		return array('success'=>true, 'logged'=>true, 'favourites'=>$fav);
	}
	
	public static function AddRole($args) {
		$usr = Sys::$User;
		if($usr->Public) return array('success'=>false, 'logged'=>false);
		
		$role = $args['role'];
		$pid = $args['perfId'];
		$p = Sys::getOneData('Show', 'Performance/'.$pid);
		if($role->id) $cw = Sys::getOneData('Show', 'Crew/'.$role->id);
		else {
			$cw = genericClass::createInstance('Show', 'Crew');
			$cw->addParent($p);
		}
		$cw->Name = $role->name;
		$cw->Role = $role->role;
		$cw->Save();
	//klog::l("ROLE",$cw);
		return ['success'=>true, 'logged'=>true, 'crew'=>self::getCrew($p)];
	}
	
	public static function DelRole($args) {
		$usr = Sys::$User;
		if($usr->Public) return array('success'=>false, 'logged'=>false);
		
		$id = $args['id'];
		$pid = $args['perfId'];
		$p = Sys::getOneData('Show', 'Performance/'.$pid);
		$cw = Sys::getOneData('Show', 'Crew/'.$id);
		$cw->Delete();
		
		return ['success'=>true, 'logged'=>true, 'crew'=>self::getCrew($p)];
	}
}