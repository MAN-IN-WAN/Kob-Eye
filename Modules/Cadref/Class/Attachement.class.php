<?php
class Attachement extends genericClass {
	
	
	function Save() {
		$this->DateModification = time();
		$ret = parent::Save();
		
		$c = $this->getOneParent('Classe');
		if($c) $c->Save();
		$c = $this->getOneParent('Visite');
		if($c) $c->Save();
		
		return $ret;
	}

	function Delete() {
		$c = $this->getOneParent('Classe');
		$v = $this->getOneParent('Visite');
		$ret = parent::Delete();
		if($c) $c->Save();
		if($v) $v->Save();
		return $ret;
	}
	
	function SaveAttachements($args) {
		$att = $args['attach'];
		$ids = $args['ids'];
		
		foreach($ids as $item) {
			$o = genericClass::createInstance('Cadref', 'Attachement');
			$o->FilePath = $att['FilePath'];
			$o->Titre = $att['Titre'];
			$o->LienExterne = $att['LienExterne'];
			$id = $item['attach'];
			if($id) $o->initFromId($id);
			else {
				$classe = genericClass::createInstance('Cadref', 'Classe');
				$classe->initFromId($item['classe']);
				$o->addParent($classe);
			}
			if($item['selected']) $o->Save();
			elseif($id) $o->Delete();
		}
		return true;
	}
	
	function GetAttachements($args) {
		$path = $this->FilePath;
		$sql = "select Id,ClasseId from `##_Cadref-Attachement` where FilePath='$path'";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		$ids = [];
		foreach($pdo as $p) $ids[] = ['attach'=>$p['Id'], 'classe'=>$p['ClasseId']];
		return array('ids'=>$ids);
	}


	
}
