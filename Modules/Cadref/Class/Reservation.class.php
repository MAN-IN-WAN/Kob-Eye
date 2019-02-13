<?php
class Reservation extends genericClass {
	
	function Save() {
		$annee = Cadref::$Annee;
		if(!empty($this->Annee) && $this->Annee != $annee) {
			$this->addError(array("Message" => "Cette fiche ne peut être modifiée ($this->Annee)", "Prop" => ""));
			return false;			
		}
		$vis = $this->getOneParent('Visite');
		$this->Visite = $vis->Visite;
		$adh = $this->getOneParent('Adherent');
		$this->Numero = $adh->Numero;
		$this->Annee = $annee;
		$this->Utilisateur = Sys::$User->Initiales;		
		if(!$this->Attente) $this->DateAttente = 0;
		if(!$this->Supprime) $this->DateSupprime = 0;
		
		$ret = parent::Save();
		if($ret) {
			$vis->Save(true);
			
			// réglement
			$r = $this->getOneChild('Reglement');
			if(! $r) {
				$r = genericClass::createInstance('Cadref', 'Reglement');
				$r->addParent($adh);
				$r->addParent($this);
				$r->Numero = $adh->Numero;
				$r->Visite = $vis->Visite;
				$r->Annee = $annee;
			}
			$r->Montant = $this->Prix+$this->Assurance;
			$r->ModeReglement = 'B';
			$r->DateReglement = $vis->DateVisite;
			$r->Differe = 1;
			$r->Encaisse = 0;
			$r->Supprime = 0;
			$r->Utilisateur = Sys::$User->Initiales;
			$r->Save();
		}
		return $ret;
	}
	

}

