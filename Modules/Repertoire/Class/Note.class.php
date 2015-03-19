<?php
class Note extends genericClass{

	function __construct($Mod,$Tab){
		genericClass::__construct($Mod,$Tab);
	}
	

	function createAlerts($time) {
		$b = strtotime(date('Ymd',$time));
		$e = strtotime('+1 day', $b);
		$rec = Sys::getData('Repertoire',"Note/Traite=0&Rappel>=$b&Rappel<$e");
		if(! is_array($rec) || ! count($rec)) return null;
		foreach($rec as $rc) {
			$t = $rc->getParents('Tiers');
			$rc->Traite = 1;
			$rc->Save();
			$t = $t[0];
			$txt = 'RAPPEL: '.$t->Intitule.'  -  '.$n->Note;
			$tag = 'TI'.$t->Id;
			AlertUser::addAlert($txt,$tag,'Repertoire','Tiers',$t->Id,null,'COMMERCIAL',null);
		}
	}
 
}
