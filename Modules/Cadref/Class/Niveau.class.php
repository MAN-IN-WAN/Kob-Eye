<?php
class Niveau extends genericClass {
	
	function Save() {
		$ps = $this->getParents('Antenne');
		$this->Antenne = $p[0]->Antenne;
		$ps = $this->getParents('Discipline');
		$this->Section = $p[0]->Section;
		$this->Discipline = $p[0]->Discipline;
		$this->CodeNiveau = "$this->Antenne.$this->Section.$this->Discipline.$this->Niveau";
		parent::Save();
		$sts[] = array($id ? 'edit' : 'add', 1, $this->Id, $this->Module, $this->ObjectType, '', '', null, null);
		return $sts;
	}
	
	function Delete() {
		$rec = $this->getChildren('Classe');
		if(count($rec)) {
			$this->addError(array("Message"=>"Cette fiche ne peut être supprimée", "Prop"=>""));
			return false;
		}

		return parent::Delete();
	}

	
}
