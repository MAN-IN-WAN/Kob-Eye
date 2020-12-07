<?php
class ClasseDate extends genericClass {
	
	function Save() {
		if(! $this->Annee) {
			$cls = $this->GetOneParent('Classe');
			$this->Annee = $cls->Annee;
		}
		return parent::Save();		
	}


}


