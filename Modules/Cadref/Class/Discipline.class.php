<?php
class Discipline extends genericClass {
	
	function Save() {
		$p = $this->getParents('Section');
		$this->Section = $p[0]->Section;
		$this->CodeDiscipline = "$this->Section.$this->Discipline";
		parent::Save();
		$sts[] = array($id ? 'edit' : 'add', 1, $this->Id, $this->Module, $this->ObjectType, '', '', null, null);
		return $sts;
	}
	
	function Delete() {
		$rec = $this->getChildren('Niveau');
		if(count($rec)) {
			$this->addError(array("Message"=>"Cette fiche ne peut être supprimée", "Prop"=>""));
			return false;
		}

		return parent::Delete();
	}

	
}
