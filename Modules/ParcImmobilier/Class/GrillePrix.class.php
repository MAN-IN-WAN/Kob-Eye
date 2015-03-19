<?
class GrillePrix extends genericClass{
	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function Save() {
		genericClass::Save();
		//$this->UpdateTypeLogement();
	}

	/**
	 * Maj du type logement lié au lot pour les prix mini et maxi et nombre de lots
	 * @return	Tableau d'objets
	 */
	function UpdateTypeLogement() {
		// rechercher le type de logement lié à la grille
		$Lot =$this->storproc('ParcImmobilier/Lot/GrillePrix/' . $this->Id );
		$LeLot = genericClass::createInstance('ParcImmobilier',$Lot[0]);
		if (isset($LeLot)&&is_object($LeLot)) {
			$TypL =$this->storproc('ParcImmobilier/TypeLogement/Lot/' . $Lot->Id );
			$TypeLogement = genericClass::createInstance('ParcImmobilier',$TypL[0]);
			if (isset($TypeLogement)&&is_object($TypeLogement)) {
				// si la surface saisie est mini ou maxi on met à jour type logement
				if ($this->Tarif < $TypeLogement->PrixMin) {
					$TypeLogement->PrixMin=$this->Tarif;
				}
				if ($this->Tarif > $TypeLogement->PrixMax) {
					$TypeLogement->PrixMax=$this->Tarif;
				}
				$TypeLogement->Save();
			}
		}
	}

	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return Sys::$Modules['ParcImmobilier']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}

}
?>