<?php
class Reservation extends genericClass {
	
	function Save() {
		$annee = Cadref::$Annee;
		if(!empty($this->Annee) && $this->Annee != $annee) {
			$this->addError(array("Message" => "Cette fiche ne peut être modifiée ($this->Annee)", "Prop" => ""));
			return false;			
		}
		$id = $this->Id;
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
//			
//			// réglement
//			$r = $this->getOneChild('Reglement');
//			if(! $r) {
//				$r = genericClass::createInstance('Cadref', 'Reglement');
//				$r->addParent($adh);
//				$r->addParent($this);
//				$r->Numero = $adh->Numero;
//				$r->Visite = $vis->Visite;
//				$r->Annee = $annee;
//			}
//			$r->Montant = $this->Prix+$this->Assurance;
//			$r->ModeReglement = 'B';
//			$r->DateReglement = $vis->DateVisite;
//			$r->Differe = 1;
//			$r->Encaisse = 0;
//			if($this->Supprime) {
//				$r->Supprime = 1;
//				$r->DateSupprime = $this->DateSupprime;
//			}
//			else $r->Supprime = 0;
//			$r->Utilisateur = Sys::$User->Initiales;
//			$r->Save();
		}
		return $ret;
	}
	
	function Delete() {
		$vis = $this->getOneParent('Visite');
		if(! checkLimite()) {
			$this->addError(array("Message" => "Cette réservation ne peut être supprimée", "Prop" => ""));
			return false;
		}
//		$rec = $this->getChildren('Reglement');
//		foreach($rec as $r)
//			$r->Delete();
		
		return parent::Delete();
	}

	private function checkLimite() {
		$t = time();
		if($this->DateLimite) return $t < $this->DateLimite;
		$l = $this->DateVisite;
		for($i = 0; $i < 3;) {
			$l -= 86400;
			$d = date('w', $l);
			if($d != 0 && $d != 6) $i++;
		}
		return $l < $l;
	}
	

}

