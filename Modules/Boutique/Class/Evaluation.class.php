<?php

class Evaluation extends genericClass {

	/**
	 * Enregistrement d'une évaluation CLIENT
	 * -> Recalcule la moyenne pour le client
	 * @return	void
	 */
	public function Save() {
		parent::Save();
		// Calcul moyenne
		$total = 0;
		$client = Sys::$Modules['Boutique']->callData('Client/Evaluation/' . $this->Id,false,0,1);
		$client = genericClass::createInstance('Boutique',$client[0]);
		$client->Save();
	}

}