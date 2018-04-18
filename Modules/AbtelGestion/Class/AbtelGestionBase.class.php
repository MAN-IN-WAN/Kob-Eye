<?php

class AbtelGestionBase extends genericClass {
	protected $type;
	protected $tiers;
	protected $data;
	protected $identifiant;
	protected $quantite;
	protected $optionnel;
	protected $proprietes;
	
	
	/*
	 * vérification du code tiers
	 * param tiers // code du tiers
	 * return // id du tiers
	 */
	protected function checkTiers() {
		$id = 0;
		$sql = "select Id from tiers where Code='$this->tiers'";
		try {
			$data = AbtelGestion::getSQLData($sql);
			if(count($data)) $id = $data['Id'];
		} catch (Exception $e) {
			throw $e;
		}
		return $id;
	}

	/*
	 * recupère la liste des propriétés d'un article
	 * ainsi que le nom des propriétés identifiant et quantité
	 */
	protected function readTypeProperties() {
		$this->identifiant = '';
		$this->quantite = '';
		$this->optionnel = false;
		$sql = "select Nom,isKey,isQte,isId,isOptional,Ordre from proprietes where TypeObjet='TYPE_ARTICLE' and Cle_1='$this->type' order by Ordre";
		try {
			$result = AbtelGestion::getSQLData($sql, true);
			
			if($result !== false) {
				foreach($result as $p) {
					if(intVal($p['isId'])) $this->identifiant = $p['Nom'];
					if(intVal($p['isQty'])) $this->quantite = $p['Nom'];
					if(intVal($p['isOptional'])) $this->optionnel = true;
				}
				$this->proprietes = $result;
			}
		} catch(Exception $e) {
			throw $e;
		}
	}
	
}
