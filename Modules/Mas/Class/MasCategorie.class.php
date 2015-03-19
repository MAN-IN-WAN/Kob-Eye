<?php
class MasCategorie extends genericClass {
	
	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function Save() {
		parent::Save();
	}
	
	function Delete() {
		$ch = false;
		$req = "Categorie/".$this->Id."/Document";
		$doc = Sys::getData('Mas', $req, 0, 1);
		$ch = is_array($doc) && count($doc);
		if(! $ch) {
			$req = "Categorie/".$this->Id."/Categorie";
			$doc = Sys::getData('Mas', $req, 0, 1);
			$ch = is_array($doc) && count($doc);
		} 
		if($ch) {
			$err = "Cette catégorie contient des documents ou des sous-catégories.\nElle ne peut être effacée.";
			throw new Exception($err);
		}
		return parent::Delete();
	}

	public function SaveGroup($args) {
		$grp = genericClass::createInstance('Systeme', 'Group');
		$args->Skin='Mas';
		$obj = new stdClass();
		$obj->args[] = $args;
		return $grp->saveRemote($obj);
	}

}