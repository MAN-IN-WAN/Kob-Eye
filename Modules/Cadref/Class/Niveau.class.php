<?php
class Niveau extends genericClass {
	
	function Save() {
		$id = $this->Id;
		$p = $this->getOneParent('Antenne');
		$this->Antenne = $p->Antenne;
		$p = $this->getOneParent('Discipline');
		$this->Section = $p->Section;
		$this->Discipline = $p->Discipline;
		$p = $p->getOneParent('Section');
		$this->addParent($p);
		$this->CodeNiveau = $this->Antenne.$this->Section.$this->Discipline.$this->Niveau;
		return parent::Save();
	}
	
	function Delete() {
		$rec = $this->getChildren('Classe');
		if(count($rec)) {
			$this->addError(array("Message"=>"Cette fiche ne peut être supprimée", "Prop"=>""));
			return false;
		}
		return parent::Delete();
	}

	function GetFormInfo() {
		$a = $this->getOneParent('Antenne');
		$s = $this->getOneParent('Section');
		$d = $this->getOneParent('Discipline');
		return array('LibelleA'=>$a->Libelle, 'LibelleS'=>$s->Libelle, 'LibelleD'=>$d->Libelle);
	}
	

}
