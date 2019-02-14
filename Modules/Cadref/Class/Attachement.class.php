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
		if($c) $c->Save();
		$c = $this->getOneParent('Visite');
		if($c) $c->Save();
		
		return parent::Delete();
	}

	
}
