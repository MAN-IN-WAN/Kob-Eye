<?php
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

class Promotion extends genericClass {

  	/**
     	* Retourne le pourcentage de réduction d'un produit en promo
     	* TODO   
     	* @return    pourcentage
     	*/
    	public function GetNiveauReduction($NbUnite=1,$Tarif=0) {

		// on renvoie la variation de prix en pourcentage
               	$montant = (($Tarif-$this->PrixVariation)*100)/$Tarif;

		if ($this->PrixForce >'0'||!empty($this->PrixForce)) {
	               	$montant = (($Tarif-$this->PrixForce)*100)/$Tarif;
		}
		return number_format((double)($montant ), 2, '.', ''); 

  	}

	/**
	 * Retourne le tarif du produit en promotion
	 * TODO	
	 * @return	float tarif promotion
	 */

	public function GetTarifPromo($NbUnite=1,$Tarif=0) {

		// on a un prixvariation qui est un pourcentage
		if ($this->PrixVariation!='0') {
			if ($this->TypeVariation=='1')  {
				// pourcentage
				$montant= $Tarif - (($Tarif * $this->PrixVariation )/100);
				return $montant;
			}
			if ($this->TypeVariation=='2') {
				// montant fixe
				$montant= $Tarif - $this->PrixVariation ;
				return $montant;
			}

		} else {
			// prixforcé renseigné donc le montant remplace le tarif
			return $this->PrixForce;
		}

		
	}
	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
	private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return Sys::$Modules['Boutique']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}




}