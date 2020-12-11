<?php
class Lieu extends genericClass {
	
	function Save() {
		$this->Libelle = $this->Ville.', '.$this->Adresse1;
		return parent::Save();
	}
	
	function Delete() {
		$rec = $this->getChildren('Classe');
		$rec1 = $this->getChildren('Depart');
		if(count($rec) || count($rec1)) {
			$this->addError(array("Message"=>"Cette fiche ne peut être supprimée", "Prop"=>""));
			return false;
		}
		return parent::Delete();
	}

	function GetFormInfo() {
		$a = $this->getOneParent('Antenne');
		return array('LibelleA'=>$a->Libelle);
	}
	

}
