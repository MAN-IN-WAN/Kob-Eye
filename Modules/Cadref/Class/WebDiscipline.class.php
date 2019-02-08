<?php
class WebDiscipline extends genericClass {
	
	function Save() {
		$id = $this->Id;
		$p = $this->getOneParent('WebSection');
		$this->CodeDiscipline = $p->WebSection.$this->WebDiscipline;
		return parent::Save();
	}
	
}
