<?php
class TransportsInternationaux_Contact extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function Save($mode=false) {
		$id = $this->Id;
		$this->NomComplet = trim(trim($this->Nom).' '.trim($this->Prenom));
		genericClass::Save();
		if($mode) return;
		$res = array('FullName'=>$this->NomComplet);
		return array(array($id ? 'edit' : 'add', 1, $this->Id, 'TransportsInternationaux', 'Contact', '', '', null, array('dataValues'=>$res)));
	}
	
	
}
