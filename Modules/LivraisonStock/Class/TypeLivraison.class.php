<?php
 
class TypeLivraison extends genericClass {

	/**
	 * Retourne un plugin LivraisonStock / TypeLivraison
	 * @return	Implémentation d'interface
	 */
	public function getPlugin() {
		$plugin = Plugin::createInstance('LivraisonStock','TypeLivraison', $this->Plugin);
		$plugin->setConfig( $this->PluginConfig );
		$plugin->setTypeLivraison( $this );
		return $plugin;
	}

	/**
	 * Récupère le tarif pour ce Type Livraison
	 * @param	object	Commande KE
	 * @param	object	Adresse Livraison KE
	 * @return	Prix ou -1 si le type de livraison n'est pas conforme
	 */
	public function recupereTarif( $commande, $adresseLivraison ) {
		$plugin = $this->getPlugin();
		$tarif = $plugin->getTarif($commande, $adresseLivraison);
		return $tarif;
	}

	/**
	 * Récupère la zone KE à partir du pays + code postal
	 * @param	string	Code pays
	 * @param	string	Code postal
	 * @return	Zone KE
	 */
	public function GetZone($Pays, $CodePostal="") {
		// Zone définie au niveau du pays
		$Zp = $this->storproc("ZoneLivraison/Pays.ZonePays(Nom=".$Pays.")&TypeLivraison.TypeLivraisonId(".$this->Id. ")",false,0,1,"Id","DESC","m.*,j11p.Nom as Pays");
		if (is_array($Zp)) return genericClass::createInstance('LivraisonStock',$Zp[0]);


		// Zone définie par Code postal
		if(!empty($CodePostal)) {
			$P = $this->storproc("Pays/Nom=".$Pays);
			if (is_array($P)){
				// Récupération Id du code postal
				$Cp = $this->storproc("Pays/".$P[0]["Code"]."/Departement/*/Ville/*/CodePostal/Code=".$CodePostal,false,0,100,"Id","DESC","m.*,j0.Nom as Pays,j0.Code as PaysCode, j1.Id as DepartementCode, j2.Id as VilleCode");
				// Recherche de la zone correspondante avec le departement
				$Zd = $this->storproc("ZoneLivraison/Departement.ZoneDepartement(".$Cp[0]["DepartementCode"].")&TypeLivraison.TypeLivraisonId(".$this->Id. ")",false,0,1,"Id","DESC","m.*,j11p.Nom as Pays");
				if (is_array($Zd)) return genericClass::createInstance('LivraisonStock',$Zd[0]);
				// Recherche de la zone correspondante avec la ville
				$Zv = $this->storproc("ZoneLivraison/Ville.ZoneVille(".$Cp[0]["VilleCode"] . ")&TypeLivraison.TypeLivraisonId(".$this->Id. ")",false,0,1,"Id","DESC","m.*,j11p.Nom as Pays");
				if (is_array($Zv)) return genericClass::createInstance('LivraisonStock',$Zv[0]);
			}
		}

		// Zone définie par défaut
		$Zd = $this->storproc("ZoneLivraison/Default=1",false,0,1);
		if (is_array($Zd)) return genericClass::createInstance('LivraisonStock',$Zd[0]);

		// Pas de zone trouvée
		return false;
	}

	/**
	 * Applique la TVA du TypeLivraison à un tarif
	 * @param	float	Tarif de base
	 * @return	float	Tarif TTC
	 */
	public function getTTC( $tarif ) {
		return $tarif * (1 + $this->TvaLivr / 100);
	}

	/**
	 * Vérifie si le choix réalisé est correct
	 * @param	object	Commande KE
	 * @param	object	Adresse Livraison KE
	 * @param	string	Uid du choix
	 * @return	VRAI si choix OK, FALSE sinon
	 */
	public function verifierChoix( $commande, $adresseLivraison, $c ) {
		$choix = $this->getPlugin()->getChoix($commande, $adresseLivraison);
		if(empty($choix)) return empty($c);
		foreach($choix as $ch) if($c == $ch['Uid']) return true;
		return false;
	}

	/**
	 * Raccourci vers callData
	 * @return	Résultat de la requete
	 */
	public function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
		return Sys::$Modules['LivraisonStock']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}

}