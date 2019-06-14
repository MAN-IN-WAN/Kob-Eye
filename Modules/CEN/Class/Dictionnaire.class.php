<?php

class Dictionnaire extends genericClass {
	
	// importation d'un dictionnaire
	function Save() {
		$id = $this->Id;
		$file = '';
		if($id) {
			$old = genericClass::createInstance('CEN', 'Dictionnaire');
			$old->initFromId($id);
			$file = $old->FilePath;
		}
		if($this->FilePath && $this->FilePath != $file) $this->Entrees = 0;
			
		$ret = parent::Save();
		
		if($this->FilePath && $this->FilePath != $file) {
			$t = genericClass::createInstance('Systeme', 'Tache');
			$t->Nom = 'Import';
			$t->Type = 'Fonction';
			$t->TaskType = '';
			$t->TaskModule = 'CEN';
			$t->TaskObject = 'Dictionnaire';
			$t->TaskFunction = 'TacheDictionnaire';
			$t->TaskArgs = serialize(array('Id'=>$this->Id));
			$t->Save();
		}
		return $ret;
	}
	
	public static function TacheDictionnaire($tache) {
		$args = unserialize($tache->TaskArgs);
		$args['ExecTask'] = 1;
		$dico = genericClass::createInstance('CEN', 'Dictionnaire');
		switch($tache->Nom) {
			case 'Import': return $dico->Import($args);
		}
		return false;
	}
	
	function Import($args) {
		$id = $args['Id'];
		$this->initFromId($id);

		if($this->FilePath) {
			$sql = "delete from `##_CEN-GDN` where DictionnaireId=$id";
			$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
			$GLOBALS['Systeme']->Db[0]->exec($sql);

			$e = 0;
			$f = fopen($this->FilePath, "r");
			while($l = fgets($f)) {
				$l = explode("\t", utf8_encode($l));
				if(count($l) < 17) continue;
				if(! $e) {
					$this->Nom = $l[13];  // Nom du dico
				}
				$g = genericClass::createInstance('CEN', 'GDN');
				$g->addParent($this);
				$g->Paleo = $l[0];
				$g->Norma_1 = $l[1];
				$g->Norma_2 = $l[2];
				$g->Norma_3 = $l[3];
				$g->Morpho = $l[4];
				$g->Phono = $l[5];
				$g->Prefixes = $l[6];
				$g->Type = $l[7];
				$g->Analyse = $l[8];
				$g->Formes = $l[9];
				$g->Trad_1 = $l[10];
				$g->Trad_2 = $l[11];
				$g->Commentaires = $l[12];
				//$g->Source = $l[13];
				$g->Folio = $l[14];
				$g->Colonne = $l[15];
				$g->Notes = $l[16];
				$g->Tout_ensemble = $l[17];
				$g->Save();
				$e++;
			}
			fclose($f);
			
			$this->Entrees = $e;
			$this->Save();
		}
		
	}
	
	function Delete() {
		$id = $this->Id;
		$sql = "delete from `##_CEN-GDN` where DictionnaireId=$id";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$GLOBALS['Systeme']->Db[0]->exec($sql);
		return parent::Delete();
	}
	
}