<?php
class PetUser extends genericClass {
	
	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	

	function Delete() {
		$ch = $this->getChildren('Vehicle');
		foreach($ch as $c) $c->Delete();
		$ch = $this->getChildren('TravelAlert');
		foreach($ch as $c) $c->Delete();
		$ch = $this->getChildren('AcceptedPet');
		foreach($ch as $c) $c->Delete();
		$ch = $this->getChildren('OpinionFromPetUserId');
		foreach($ch as $c) $c->Delete();
		$ch = $this->getChildren('OpinionToPetUserId');
		foreach($ch as $c) $c->Delete();

		return parent::Delete();
	}

}