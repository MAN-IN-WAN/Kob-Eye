<?php

class Performance extends genericClass {
	
	
	function Save() {
		if($this->Id) $this->ComputeVotes();
		return parent::Save();
	}
	
	public static function GetComments($args) {
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		//if(!$logged) return ['success'=>false, 'logged'=>false];

		$id = $args['id'];
		$stars = $args['stars'];
		$sql = "select c.Vote,c.Comments,c.CommentsDate,u.Id,u.Initiales "
			."from `##_Show-Comments` c "
			."inner join `##_Systeme-User` u on u.Id=c.UserId "
			."where c.PerformanceId=$id "; 
		if($stars) $sql .= "and c.Vote=$stars ";
		$sql .= "order by c.CommentsDate desc";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);

		$tmp = array();
		foreach($rs as $r) {
			$tmp[] = ['vote'=>$r['Vote'], 'text'=>$r['Comments'], 'time'=>$r['CommentsDate'],
				'user'=>$r['Initiales'], 'userId'=>$r['Id']];
		}
		return ['success'=>true, 'comments'=>$tmp];
	}
	
	public static function GetVote($args) {
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		if(!$logged) return ['success'=>false, 'logged'=>false];
		
		$id = $args['show'];
		$sql = "select Id,Vote,Comments from `##_Show-Comments` where PerformanceId=$id and UserId=$usr->Id limit 1";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$r = $rs->fetch(PDO::FETCH_ASSOC);
		$vote = ['id'=>$r ? $r['Id'] : 0, 'show'=>$id, 'user'=>$usr->Id, 'vote'=>$r ? $r['Vote'] : 0, 'text'=>$r ? $r['Comments'] : ''];
		return ['success'=>true, 'vote'=>$vote];
	}
		
	public static function SetVote($args) {
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		if(!$logged) return ['success'=>false, 'logged'=>false];
			
		$vote = $args['vote'];
		$p = Sys::getOneData('Show', 'Performance/'.$vote->show);
		if($vote->id) $c = Sys::getOneData('Show', 'Comments/'.$vote->id);
		else {
			$c = genericClass::createInstance('Show', 'Comments');
			$c->addParent($p);
			$c->addParent($usr);
		}
		$c->Vote = $vote->vote;
		$c->Comments = $vote->text;
		$c->Save();
		$p->ComputeVotes();
		$p->Save();
		
		return ['success'=>true, 'votes'=>$p->Votes, 'comments'=>$p->Comments, 'rating'=>$p->Rating];
	}

	function ComputeVotes() {
		$votes = $comments = 0;
		$rating = 0;
		$cs = $this->getChildren('Comments');
		foreach($cs as $c) {
			$votes++;
			if($c->Comments) $comments++;
			$rating += $c->Vote;
		}
		$rating = round($rating/$votes,1);
		$this->Votes = $votes;
		$this->Comments = $comments;
		$this->Rating = $rating;
	}

	public static function GetRatings($args) {
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		//if(!$logged) return ['success'=>false, 'logged'=>false];
		
		$id = $args['id'];
		$sql = "select Vote,count(*) as cnt from `##_Show-Comments` where PerformanceId=$id group by Vote";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);

		$tmp = array();
		$tot = 0;
		for($n = 5; $n > 0; $n--) $tmp[$n] = 0;
		foreach($rs as $r) {
			$tot += $r['cnt'];
			$tmp[$r['Vote']] = $r['cnt'];
		}
		$arr = [];
		for($n = 5; $n > 0; $n--) {
			$v = $tmp[$n];
			$c = $tot > 0 ? $v/$tot : 0;
			$arr[] = ['stars'=>$n, 'votes'=>$v, 'coef'=>$c, 'prct'=>round($c*100)];
		}
		
		return array('success'=>1, 'ratings'=>$arr);
	}
	
	public static function SavePerf($args) {
		$lang = $args['lang'];
		$s = $args['show'];
		$id = $s->id;
		$dom = [];
		$gen = [];
		$o = genericClass::createInstance('Show', 'Performance');
		if($id) {
			$o->initFromId($id);
			$gen = self::getChildrenArray($o, 'Genre', $lang);
			$lng = self::getChildrenArray($o, 'Language', $lang);
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
		$cs = $o->getChildren('Genre');
		foreach($cs as $c) $c->Delete();
		$cs = $o->getChildren('Language');
		foreach($cs as $c) $c->Delete();
		$cs = $o->getChildren('Crew');
		foreach($cs as $c) $c->Delete();
		$cs = $o->getChildren('Medium');
		foreach($cs as $c) $c->Delete();
		$cs = $o->getChildren('Message');
		foreach($cs as $c) $c->Delete();
		$cs = $o->getChildren('Comments');
		foreach($cs as $c) $c->Delete();
		$cs = $o->getChildren('Place');
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
		$lang = $args['lang'];
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		$uid = $usr->Id;

		switch($cond->type) {
			case 'preview':
				return self::getPreview($cond, $logged, $uid, $lang);
			case 'details':
				return self::getDetails($cond, $logged, $uid, $lang, $args);
		}
		return array();
	}
	
	private static function getPreview($cond, $logged, $uid, $lang) {

		$sql = "select s.Id,c.Category$lang,mt.Maturity ";
		$frm = "from `kob-Show-Performance` s ";
		$join = "inner join `kob-Show-Category` c on c.Id=s.CategoryId "
				."left join `kob-Show-Maturity` mt on mt.Id=s.MaturityId ";
		
		$cry = $cond->country;
		$group = false;
		$name = '';
		$whr = "and countryId=$cry ";
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
				$name = 'Search';
				if($cond->category) $whr .= "and s.CategoryId=$cond->category ";
				if($cond->user) $whr .= "and s.userCreate=$cond->user ";
				if($cond->year) $whr .= "and s.Year='$cond->year' ";
				if($cond->genre) $join .= "inner join `kob-Show-PerformanceGenres` pg on pg.PerformanceId=s.Id and pg.Genre in ($cond->genre) ";
				if($cond->language) $join .= "inner join `kob-Show-PerformanceLanguages` pl on pl.PerformanceId=s.Id and pl.Language in ($cond->language) ";
				//if($cond->crew) $join .= "inner join `kob-Show-Crew` cw on cw.PerformanceId=s.Id and cw.PeopleId in ($cond->crew) ";
				if($cond->maturity) $whr .= "and s.MaturityId<>0 and s.MaturityId".($cond->more ? '>=' : '<=')."$cond->maturity ";
				if($cond->state) $whr .= "and s.StateId in ($cond->state) ";
				if($cond->city) $whr .= "and s.StateId in ($cond->city) ";
				$txt = $cond->text;
				if($txt) {
					$name .= ": $txt";
					$whr = "and s.Id in ( "
					."select Id "
					."from `kob-Show-Performance` "
					."where CountryId=$cry and MATCH (Title,Subtitle,Summary,Description) AGAINST ('$txt*' in boolean mode) "
					."union "
					."select PerformanceId "
					."from `kob-Show-Crew` "
					."where MATCH (Name) AGAINST ('$txt') "
					."union "
					."select PerformanceId "
					."from `kob-Show-Crew` "
					."where MATCH (Role) AGAINST ('$txt*' in boolean mode) "
					."union "
					."select s.Id from `kob-Show-Category` c "
					."inner join `kob-Show-Performance` s on s.CountryId=$cry and s.CategoryId=c.id "
					."where c.Category$lang like '$txt%' "
					."union "
					."select pg.PerformanceId as Id from `kob-Show-Genre` g "
					."inner join `kob-Show-PerformanceGenres` pg on pg.Genre=g.Id "
					."where MATCH (Genre$lang) AGAINST ('$txt*' in boolean mode) "
					.") ".$whr;
				}
		} 
		
		
		$whr = " where ".substr($whr, 3);
		$ord = ' order by s.tmsEdit desc, s.Id desc'; 

		$offset = $cond->offset;
		$slides = $cond->slides;
		
		$favs = [];
		$data = [];

		if($group) {
			$page = 32;
			$inf = json_decode(Sys::$User->Informations);
			if($inf && $inf->showFavourites && $cond->mode == 0 && ($slides == 0 || $slides == -1)) {
				$acat = [];
				$frm0 = $frm."inner join `kob-Show-FavPerformance` fp on fp.PerformanceId=s.Id and fp.UserId=$uid ";
				$max = self::getPerfs($uid, $logged, $lang, $sql, $frm0, $join, $whr, $ord, $offset, $page, $acat);
				if($max) $data[] = ['count'=>count($acat), 'offset'=>$offset, 'max'=>$max, 'pages'=>0, 'name'=>'Favourites', 'id'=>-1, 'data'=>$acat];
			}
		
			$cat = $slides > 0 ? "/$slides" : '';
			$cs = Sys::getData('Show', 'Category'.$cat);
			foreach($cs as $c) {
				$tmp = "Category$lang";
				$acat = [];
				$max = self::getPerfs($uid, $logged, $lang, $sql, $frm, $join, $whr." and s.CategoryId=$c->Id", $ord, $offset, $page, $acat);
				if($max) $data[] = ['count'=>count($acat), 'offset'=>$offset, 'max'=>$max, 'pages'=>0, 'name'=>$c->$tmp, 'id'=>$c->Id, 'data'=>$acat];
			}
		}
		else {
			$page = 20;
			$acat = [];
			if($offset) $offset = ($offset-1)*$page;
			$max = self::getPerfs($uid, $logged, $lang, $sql, $frm, $join, $whr, $ord, $offset, $page, $acat);
			if($max) $data[] = ['count'=>count($acat), 'offset'=>$offset, 'max'=>$max, 'pages'=>ceil($max/$page), 'name'=>$name, 'id'=>0, 'data'=>$acat];
		}

		return ['success'=>true, 'logged'=>$logged, 'count'=>count($data), 'data'=>$data, 'group'=>$group, 'sql'=>"$sql$frm$join$whr$ord"];
	}
	
	private static function getPerfs($uid, $logged, $lang, $sql, $frm, $join, $whr, $ord, $offset, $limit, &$acat) {
		$sql0 = 'select count(*) as cnt '.$frm.$join.$whr;
		$sql0 = str_replace('##_', MAIN_DB_PREFIX, $sql0);
		$ps = $GLOBALS['Systeme']->Db[0]->query($sql0);
		$r = $ps->fetch(PDO::FETCH_ASSOC);
		$count = $r['cnt'];

		$sql .= $frm.$join.$whr.$ord;
		if($limit) $sql .= " limit $limit";
		if($offset) $sql .= " offset $offset";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$ps = $GLOBALS['Systeme']->Db[0]->query($sql);

		foreach($ps as $r) {
			$p = genericClass::createInstance('Show', 'Performance');
			$p->initFromId($r['Id']);
			$d = new stdClass();
			$d->id = $p->Id;
			$d->mine = ($logged && $p->userCreate == $uid) ? 1 : 0;
			$d->title = $p->Title;
			$d->subtitle = $p->Subtitle;
			$d->year = $p->Year;
			$d->categoryId = $p->CategoryId;
			$d->category = $r["Category$lang"];
			$d->countryId = $p->CountryId;
			$d->maturity = $p->MaturityId ? $r['Maturity'] : 'NR';
			$d->votes = $p->Votes;
			$d->rating = round($p->Rating, 1);
			$d->comments = $p->Comments;
			$main = '';
			$picts = self::getPictures($p, $main);
			if(empty($main) && $pict['count']) $main = $pict['data'][0]; 
			$d->pict = $main;

			$d->fav = $logged ? Sys::getCount('Show', "FavPerformance/UserId=$uid&PerformanceId=".$p->Id) : 0;

			$acat[] = $d;
		}
		return intVal($count);
	}

	private static function getDetails($cond, $logged, $uid, $lang) {
		$id = $cond->id;
		$p = Sys::getOneData('Show', "Performance/$id");
		
		$sql = "select c.Category$lang,mt.Maturity,cr.Country$lang,st.State,cy.City,u.Initiales,u.Nom,u.Informations "
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
				
		$inf = json_decode($r['Informations']);
		
		$d = new stdClass();
		$d->id = $p->Id;
		$d->uid = $p->userCreate;
		$d->user = $inf && $inf->displayName ? $r['Nom'] : $r['Initiales'];
		$d->mine = ($logged && $p->userCreate == $uid) ? 1 : 0;
		$d->title = $p->Title;
		$d->subtitle = $p->Subtitle;
		$d->summary = $p->Summary;
		$d->description = $p->Description;
		$d->year = $p->Year;
		$d->duration = $p->Duration;
		$d->categoryId = $p->CategoryId;
		$d->category = $r["Category$lang"];		
		$d->maturityId = $p->MaturityId;
		$d->maturity = $p->MaturityId ? $r['Maturity'] : 'NR';
		$d->genres = $dom = self::getChildrenArray($p, 'Genre', $lang);
		$d->publics = $dom = self::getChildrenArray($p, 'Public', $lang);
		$d->languages = $dom = self::getChildrenArray($p, 'Language', $lang);
		$d->countryId = $p->CountryId;
		$d->country = $r["Country$lang"];
		$d->stateId = $p->StateId;
		$d->state = $r['State'];
		$d->cityId = $p->CityId;
		$d->city = $r['City'];		
		$d->votes = $p->Votes;
		$d->rating = round($p->Rating, 1);
		$d->comments = $p->Comments;	
		$main = '';
		$d->picts = self::getPictures($p, $main);
		$d->pict = $main;
		$d->links = self::getLinks($p);
		$d->crew = self::getCrew($p);
		$gnr = Show::getObjsArray('Genre', 'CategoryId='.$d->categoryId, false, $lang);
		
		return ['success'=>true, 'logged'=>$logged, 'show'=>$d, 'genres'=>$gnr, 'sql'=>$sql];
	}

	
	private static function getChildrenArray($parent, $name, $lang) {
		$table = $name.'s';
		$sql = "select $name from `kob-Show-Performance$table` where PerformanceId=$parent->Id";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);

		$tmp = array();
		foreach($rs as $r) $tmp[] = $r[$name];
		return $tmp;
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
			$tmp[] = ['id'=>$r->Id, 'pict'=>$r->Medium, 'main'=>($r->MainPicture?true:false), 'title'=>$r->Description, 'subtitle'=>'', 'year'=>$r->Year];
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

	public static function MainPict($args) {
		$usr = Sys::$User;
		$logged = ! $usr->Public;
		if(!$logged) return ['success'=>false, 'logged'=>false];
		
		$id = $args['id'];
		$p = Sys::getOneData('Show', 'Performance/'.$args['perfId']);
		$ms = $p->getChildren('Medium');
		foreach($ms as $m) {
			if($m->Id == $id) {
				if(!$m->MainPicture) {
					$m->MainPicture = 1;
					$m->Save();
				}
			}
			else if($m->MainPicture) {
				$m->MainPicture = 0;
				$m->Save();
			}
		}
		return ['success'=>true, 'logged'=>true];
	}

	private static function getCrew($parent) {
		$sql = "select Id,Name,Role from `##_Show-Crew` where PerformanceId=".$parent->Id;
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$rs = $GLOBALS['Systeme']->Db[0]->query($sql);
		$tmp = array();
		foreach($rs as $r) {
			$tmp[] = ['id'=>$r['Id'], 'name'=>$r['Name'], 'role'=>$r['Role']];
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
		return array('success'=>true, 'logged'=>true, 'favCount'=>$fav);
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