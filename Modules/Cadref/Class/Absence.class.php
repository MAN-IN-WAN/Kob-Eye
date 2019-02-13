<?php
class Absence extends genericClass {
	
	function Save() {
		$this->Annee = Cadref::$Annee;
		$ret = parent::Save();		
		
		$a = $this->getOneParent('Enseignant');
		$t = date('d/m H:i', $this->DateDebut).' - '.date('d/m H:i', $this->DateFin).($this->Description ? "<br />".$this->Description : '');
		AlertUser::addAlert('Absence : '.$a->Prenom.' '.$a->Nom,$t,'','',0,[],'CADREF_ADMIN','icmn-aid-kit');
		return $ret;
	}


}


