<?php

class Performance extends genericClass {

	
	public function GetData($args) {
		$id = $args['id'];
		if($id) return getLong($id);
		
		$cond = $args['cond'];

		$catId = 0;
		if($cond->type == 'preview') {
			$perf = array();
			$o = Sys::getOneData('Show', 'Category/Category='.$cond->cat);
			$ps = $o->getChildren('Performance');
			foreach($ps as $p) {
				$d = new stdClass();
				$d->id = $p->Id;
				$d->title = $p->Title;
				$d->teaser = $p->Teaser;
				$d->year = $p->Year;
				$d->domain = '';
				
				$ms = $p->getChildren('Medium/MediumTypeId=1');
				$pict = array();
				foreach($ms as $m) $pict[] = ['pict'=>$m->Medium, 'desc'=>$m->Description, 'year'=>$m->Year];
				$d->picts = $pict;
				
				$perf[] = $d;
	//klog::l(">>>>>>>>>",$p->getChildren('Domain'));
			}
			return array('cat'=>$cond->cat, 'count'=>count($perf), 'data'=>$perf);
		}
		return array();
		
	}
}