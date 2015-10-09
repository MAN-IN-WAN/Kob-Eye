<?php
class TypeTva extends genericClass {
    function getTaux() {
        $tx = $this->getChildren('TauxTva/Debut<'.time().'&Fin>'.time().'&Actif=1');
        return $tx[0]->Taux;
    }
	
}

class ObjetTva {
	var $Debut;
	var $Fin;
	var $Type;
	var $Taux;
	private $defaut;
	var $min;
	
	function ObjetTva($date=null,$pays=null) {
		if(!$date) $date = time();
		$ty = array();
		$tx = array();
		$max = 9999999999;
		$taux = Sys::$Modules['Fiscalite']->callData('TauxTva:ListTauxTva');
		foreach($taux as $x) {
			$d = $x['Debut'];
			$f = $x['Fin'];
			if($x['TypeTvaActif'] && $x['Actif'] && $d<=$date && $f>=$date) {
				if($d > $min) $min = $d;
				if($f < $max) $max = $f;
				$ty[$x['TypeTvaId']] = array('Taux'=>$x['Taux'], 'Compte'=>$x['CompteComptable']);
				$tx[$x['Taux']] = array('Type'=>$x['TypeTvaId'], 'Compte'=>$x['CompteComptable']);
				if($x['TypeTvaDefaut']) $this->defaut = $x['TypeTvaId'];
			}
		}
		$this->Debut = $min;
		$this->Fin = $max;
		$this->Type = $ty;
		$this->Taux = $tx;
	}
	
	function checkDate($date) {
		return $this->Debut<=$date && $this->Fin>=$date;
	}
	
	function getTaux($type, $defaut=false) {
		if(!isset($this->Type[$type])) {
			klog::l("********* FISCALITE getTaux ERREUR : Type TVA '$type' non trouvé ********");
			return $defaut ? $this->Type[$this->defaut]['Taux'] : false;
		}
		return $this->Type[$type]['Taux']; 
	}

	function getCompte($key, $type=true, $defaut=false) {
		if($type) $tb = $this->Type;
		else $tb = $this->Taux;
		if(!isset($tb[$key])) {
			klog::l("********* FISCALITE getCompte ERREUR : Type TVA '$type' non trouvé ********");
			return $defaut ? $this->Type[$this->defaut]['Compte'] : false;
		}
		return $tb[$key]['Compte']; 
	}
}
