<?php

/**
 * 
 *   A la création d une nouvelle adresse le defaut est modifié pour etre l'unique adresse par défaut pour un client 
 */
class Adresse extends genericClass {
	function Save() {
		parent::Save();
		if ($this->Default==1) {
			$cli = $this->getParents('Client');
			if (sizeof($cli)) {
				$liv = $cli[0]->getChildren('Adresse/Type='.$this->Type.'&Default=1&Id!='.$this->Id);
				foreach ($liv as $l) {
					$l->Default=0;
					$l->Save();
				}
			}
			Boutique::initTableauTva();
		}

	}
}
