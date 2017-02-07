<?php
class Categorie extends genericClass {
	function Save(){
		$status = Array();
		if (empty($this->Id)){
			//CAS DE CREATION
			genericClass::Save();
			//ajout de la categorie sur l'ensemble des dbs auto administratées
			$dbs = Sys::$Modules["Axenergie"]->callData("Database/Auto=1");
			foreach ($dbs as $db) {
				$db = genericClass::createInstance("Axenergie",$db);
				//ajout de la categorie
				$status = array_merge($status,SubRange::add($db,$this,new stdClass(),true));
			}
		}else{
			//CAS DE MODIFICATION
			$this->Version++;
			genericClass::Save();
			//mise à jour des subrange des databases auto administratées
			$dbs = Sys::$Modules["Axenergie"]->callData("Database/Auto=1");
			foreach ($dbs as $db) {
				$db = genericClass::createInstance("Axenergie",$db);
				//verification de l'existence
				$c2 = SubRange::search($db,$this);
				if (is_object($c2)){
					//mise à jour de la range
					$c2->initFromObject($this);
					$c2->Save();
				}
			}
		}
		$key = 'CatDescription.CategorieDescr';
		$this->saveDescription($this->{$key});
		return $status;
	}

	private function saveDescription($descr) {
		if(! $descr) return;
		$ord = 0;
		$old = $this->getChilds('Description');
		foreach($descr as $desc) {
			$id = $desc->Id;
			$d = genericClass::createInstance('Axenergie','CatDescription');
			$d->addParent($this);
			$d->Id = $id;
			$d->Libelle = $desc->Libelle;
			$d->Texte = $desc->Texte;
			$d->Ordre = $ord++;
			$d->Save();
			if($id) {
				foreach($old as $i=>$o) {
					if($o->Id == $id) {
						unset($old[$i]);
						break;
					}
				}
			}
		}
		foreach($old as $i=>$o) $o->Delete();
	}

	
	function Delete() {
		$cat = $this->getChildren('Categorie');
		$prd = $this->getChildren('Produit');
		if((is_array($cat) && count($cat)) || (is_array($prd) && count($prd)))
			throw new Exception('Cette catégorie ne peut être supprimée');
		$status = array();
		//suppression de la categorie dans les bases auto administratée
		$dbs = Sys::$Modules["Axenergie"]->callData("Database/Auto=1");
		foreach ($dbs as $db) {
			$db = genericClass::createInstance("Axenergie",$db);
			//suppression de la categorie
			$status = array_merge($status,SubRange::remove($db,$this));
		}
		$des = $this->getChilds('CatDescription');
		foreach($des as $d) $d->Delete();
		$st = genericClass::Delete();
		$status = array("delete",1,$this->Id,$this->Module,$this->ObjectType,null,null,null,null,null,null);
		return $status;
	}
	
	function addSub($args) {
		$GLOBALS["Systeme"]->Log->log("xxxxxxADD SUB RANGE CALLxxxxxxx", $args);
		return "ADD SUB RANGE CALL";
	}
	function removeSub($args) {
		$GLOBALS["Systeme"]->Log->log("xxxxxxREMOVE SUB RANGE CALLxxxxxxx", $args);
		return "REMOVE SUB RANGE CALL";
	}

	function UploadProduct($marId, $csv, $catId) {
		if(!$marId) return WebService::WSStatus('method',0,'','','','','',array(array("Marque non renseigné")),null);
		if(!$csv) return WebService::WSStatus('method',0,'','','','','',array(array("Fichier produits non renseigné")),null);
		if(!$catId) return WebService::WSStatus('method',0,'','','','','',array(array("La catégorie doit être enregistée")),null);
		$file = file($csv);
		$n = count($file);
		if(! $n) return WebService::WSStatus('method',0,'','','','','',array(array("Fichier vide")),null);
		$line = explode(';', $file[0]);
		if($line[0] != 'Référence' || $line[1] != 'Désignation')
			return WebService::WSStatus('method',0,'','','','','',array(array("Format de fichier incorrect")),null);
			
		$cat = genericClass::createInstance("Axenergie",'Categorie');
		$cat->initFromId($catId);
		$mar = genericClass::createInstance("Axenergie",'Marque');
		$mar->initFromId($marId);
		$head = explode(';', $file[0]);
		$len = count($line);

		for($i = 1; $i < $n; $i++) {
			$line = explode(';', $file[$i]);
			$rec = Sys::$Modules['Axenergie']->callData("Produit/MarqueId=$marId&Reference=".$line[0], false, 0, 1);
			if(is_array($rec) && count($rec)) $p = genericClass::createInstance("Axenergie",$rec[0]);
			else $p = genericClass::createInstance("Axenergie",'Produit');
			
			$p->Reference = $line[0];
			$p->Nom = $line[1];
			$p->PrixHT = $line[2];
			$p->Hauteur = $line[4];
			$p->Largeur = $line[5];
			$p->Profondeur = $line[6];
			$p->addParent($cat);
			$p->addParent($mar);
			$p->Save();
			
			$des = $p->getChildren('Description');
			foreach($des as $d) $d->Delete();
			
			$ord = 0;	
			for($j = 7; $j < $len; $j++) {
				$d = genericClass::createInstance("Axenergie",'Description');
				$d->Libelle = trim($head[$j]);
				$d->Texte = trim($line[$j]);
				$d->Ordre = $ord++;
				$d->addParent($p);
				$d->Save();
			}
		}
		return WebService::WSStatus('method',1,$catId,'Axenergie','Categorie','','',null,null);
	}

}
