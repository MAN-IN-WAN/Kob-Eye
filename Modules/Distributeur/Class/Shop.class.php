<?php
class Shop extends genericClass {

	private static $pays;
	private static $shops;


	function Save() {
		parent::Save();
	}	/**
	 * Gestion des PAYS
	 * -> Ajout si pas déjà présent
	 * -> Retour général ordonné
	 */
	public static function addPays( $p ) {
		if(!is_array(self::$pays)) self::$pays = array();
		if(!in_array($p, self::$pays)) self::$pays[] = $p;
	}

	public static function getAllPays() {
		if(!is_array(self::$pays)) self::$pays = array();
		sort(self::$pays);
		return self::$pays;
	}

	/**
	 * Gestion des SHOPS
	 * -> Ajout dans l'ordre
	 * -> Retour général
	 */
	public static function addShop( $Shop ) {
		$Shop->registerCoords();
		if(!is_array(self::$shops)) self::$shops = array();
		$k = 0;
		foreach(self::$shops as $s) {
			if(strtolower($s->Name) > strtolower($Shop->Name)) break;
			$k++;
		}
		array_splice(self::$shops, $k, 0, array($Shop));
	}

	public static function getAllShops() {
		if(!is_array(self::$shops)) self::$shops = array();
		return self::$shops;
	}

	/**
	 * Extensions de classe
	 * -> Détection des coordonnées selon adresse
	 */
	public function registerCoords() {
		if(!empty($this->Latitude) and !empty($this->Longitude)) return;
		$q = $this->Adress . ' ' . $this->Adress2 . ' ' . $this->Adress3 . ' ' . $this->PostalCode . ' ' . $this->City;
		$data = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($q).'&sensor=false'));
		if(is_array($data->results)) :
			$this->Latitude = $data->results[0]->geometry->location->lat;
			$this->Longitude = $data->results[0]->geometry->location->lng;
			$this->Save();
		endif;
	}

	public function getCoords( $adresse, $pays = '' ) {
		$q = $adresse . ' ' . $pays;
		$data = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($q).'&sensor=false'));
		$result = array('Longitude' => '', 'Latitude' => '');
		if(is_array($data->results)) :
			$result['Latitude'] = $data->results[0]->geometry->location->lat;
			$result['Longitude'] = $data->results[0]->geometry->location->lng;
			$result['Req'] = 'http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($q).'&sensor=false';
		endif;
		return $result;
	}

	public function getNearestShop($lat,$long){
		if (!$lat)$lat = 43.802145;
		if (!$long)$long = 4.435717;
		$sql="SELECT SQRT(POW(s.Latitude-$lat,2) +POW(s.Longitude-$long,2)) as DeltaHypo,s.*  FROM `".MAIN_DB_PREFIX."Distributeur-Shop` as s, `".MAIN_DB_PREFIX."Distributeur-ShopCategorieId` as cs , `".MAIN_DB_PREFIX."Distributeur-Categorie` as c WHERE s.Id=cs.Shop AND cs.CategorieId=c.Id AND c.Id='1' ORDER BY DeltaHypo  LIMIT 0,10;";
		$results = $GLOBALS["Systeme"]->Db[0]->query($sql);
		if ($results)$results = $results->fetchALL ( PDO::FETCH_ASSOC );
		return $results;
	}
}
