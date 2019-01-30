<?php
class Reservation extends genericClass {
	
	function Save() {
		Cadref::$Annee;
		if(!empty($this->Annee) && $this->Annee = $annee) {
			$this->addError(array("Message" => "Cette fiche ne peut être modifiée ($this->Annee)", "Prop" => ""));
			return false;			
		}
		$vis = $this->getOneParent('Visite');
		$this->Visite = $vis->Visite;
		$adh = $this->getOneParent('Adherent');
		$this->Numero = $adh->Numero;
		$this->Annee = $annee;
		$this->Utilisateur = Sys::$User->Initiales;
		
		$ret = parent::Save();
		if($ret) {
			$vis->Save(true);
		}
		return $ret;
	}
	

}

