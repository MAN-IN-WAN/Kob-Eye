<?php
Class Magasin extends genericClass{
	/**
	 * Save
	 * Vérifie que le magasin est bien la magasin par défaut
	 */
	function Save(){
		$mags =  Sys::getData('Boutique','Magasin/Default=1');
		if (!isset($mags[0])||($mags[0]->Id==$this->Id&&$this->Id>0)){
			//pas de magasin par défaut donc on définit celui ci par défaut
			$this->Default = 1;
		}else $this->Default=0;
		//sauvegarde
		parent::Save();
	}
	/**
	 * initCurrent
	 * Initialise le magasin en cours
	 * @void
	 */
	 function initCurrent() {
	 	$mag = Magasin::getCurrentMagasin();
		$this->initFromId($mag->Id);
	 }
	/******************************************************
	 * 					STATIC
	 * ****************************************************/
	/**
	 * recherche du magasin en cours en fonction de du domaine par défaut
	 * @return Magasin
	 */
	static function getCurrentMagasin() {
		//récupération du domaine en cours
		$dom = Sys::$domain;
		//recherche du site correspondant
		$doms = Sys::getData('Systeme','Site/Domaine='.$dom);
		//renvoi le magasin correpondant
		if (isset($doms[0])){
			$mags = $doms[0]->getChildren('Magasin');
			if (isset($mags[0]))return $mags[0]; 
		}
		//renvoi le magasin par défaut
		$mags = Sys::getData('Boutique','Magasin/Default=1');
		if (isset($mags[0]))return $mags[0];
		
		//throw new Exception('pas de magasin disponible');
		return false;
	}
	public function getTopCategories() {
		$cat = Sys::getData("Boutique","Magasin/".$this->Id."/Categorie/Actif=1");
		return $cat;
	}

	function explodeCSV( $content ) {
		return explode(PHP_EOL, $content);
	}

	function sendHeader($fichier) {
		header("Content-type: application/vnd.ms-excel"); 
		header("Content-disposition: attachment; filename=\"" . $fichier . "\"");
	}
	function addLigne($lig) {
    		echo $lig  . "\r\n";	
  	}

}
?>