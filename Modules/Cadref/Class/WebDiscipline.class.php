<?php
class WebDiscipline extends genericClass {
	
	function Save() {
		$id = $this->Id;
		$p = $this->getOneParent('WebSection');
		$this->CodeDiscipline = $p->WebSection.$this->WebDiscipline;
		return parent::Save();
	}

	function Delete() {
		$rec = $this->getChildren('Discipline');
		if(count($rec)) throw new Exception('Cette section ne peut être supprimée');

		return parent::Delete();
	}
	
	function GetFormInfo() {
		$s = $this->getOneParent('WebSection');
		return array('LibelleS'=>$s->Libelle);
	}
	
}
