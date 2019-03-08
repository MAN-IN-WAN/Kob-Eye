<?php
class Visite extends genericClass {

	function Save($mode = false) {
		$annee = Cadref::$Annee;
		if(!empty($this->Annee) && $this->Annee != $annee) {
			$this->addError(array("Message" => "Cette fiche ne peut être modifiée ($this->Annee)", "Prop" => ""));
			return false;			
		}
		$id = $this->Id;
		if(! $id) { //if($mode) {
			$this->Annee = $annee;
			$this->Utilisateur = Sys::$User->Initiales;
		}
		$this->Attentes = Sys::getCount('Cadref','Visite/'.$this->Id.'/Reservation/Attente=1&Supprime=0');
		$this->Inscrits = Sys::getCount('Cadref','Visite/'.$this->Id.'/Reservation/Attente=0&Supprime=0');

		$ret = parent::Save();
		if(! $id) {
			$lx = Sys::getData('Cadref','Lieu/Type=R');
			foreach($lx as $l) {
				$d = genericClass::createInstance('Cadref', 'Depart');
				$d->addParent($this);
				$d->addParent($l);
				$d->Save();
			}
		}
		return $ret;
	}

	function Delete() {
		$res = $this->getChildren('Reservation');
		if(count($res)) {
			$this->addError(array("Message" => "Cette fiche ne peut être supprimée", "Prop" => ""));
			return false;
		}
		$rec = $this->getChildren('Lieu');
		foreach($rec as $r)
			$r->Delete();
		$rec = $this->getChildren('Enseignant');
		foreach($rec as $r)
			$r->Delete();
		
		return parent::Delete();
	}
	

}


