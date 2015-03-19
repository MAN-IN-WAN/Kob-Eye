<?php
class Categorie extends genericClass {
	function Save(){
		$status = Array();
		if (empty($this->Id)){
			//CAS DE CREATION
			if ($this->Prive==1&&$this->Affiche==0){
				//creation
				genericClass::Save();
				//$status[] = Array("add",1,$this->Id,$this->Module,$this->ObjectType,null,null,null,null,null);
				//($type, $success, $id, $module, $object, $parent, $parentId, $errors, $result, $more)
				//ajout de la categorie sur l'ensemble des dbs auto administratées
				$dbs = Sys::$Modules["Vitrine"]->callData("Database/Auto=1");
				foreach ($dbs as $db) {
					$db = genericClass::createInstance("Vitrine",$db);
					//ajout de la categorie
					$status = array_merge($status,SubRange::add($db,$this,new stdClass(),true));
				}
			}
		}else{
			//CAS DE MODIFICATION
			$this->Version++;
			$cat = Sys::$Modules["Vitrine"]->callData("Categorie/".$this->Id);
			if (($cat[0]["Prive"]==1&&$this->Prive==0)||
				($cat[0]["Affiche"]==0&&$this->Affiche==1)){
				genericClass::Save();
				//$status[] = Array("edit",1,$this->Id,$this->Module,$this->ObjectType,null,null,null,null,null);
				//suppression de la categorie dans les bases auto administratée
				$dbs = Sys::$Modules["Vitrine"]->callData("Database/Auto=1");
				foreach ($dbs as $db) {
					$db = genericClass::createInstance("Vitrine",$db);
					//suppression de la categorie
					$status = array_merge($status,SubRange::remove($db,$this));
				}
			}else if (($cat[0]["Prive"]==0&&$this->Prive==1&&$this->Affiche==0)||
				($cat[0]["Affiche"]==1&&$this->Affiche==0&&$this->Prive==1)){
				genericClass::Save();
				//$status[] = Array("edit",1,$this->Id,$this->Module,$this->ObjectType,null,null,null,null,null);
				//ajout de la categorie sur l'ensemble des dbs auto administratées
				$dbs = Sys::$Modules["Vitrine"]->callData("Database/Auto=1");
				foreach ($dbs as $db) {
					$db = genericClass::createInstance("Vitrine",$db);
					//ajout de la categorie
					$status = array_merge($status,SubRange::add($db,$this,new stdClass(),true,true));
				}
			}else{
				genericClass::Save();
				//$status[] = Array("edit",1,$this->Id,$this->Module,$this->ObjectType,null,null,null,null,null);
				//mise à jour des subrange des databases auto administratées
				$dbs = Sys::$Modules["Vitrine"]->callData("Database/Auto=1");
				foreach ($dbs as $db) {
					$db = genericClass::createInstance("Vitrine",$db);
					//verification de l'existence
					$c2 = SubRange::search($db,$this);
					if (is_object($c2)){
						//mise à jour de la range
						$c2->initFromObject($this);
						$c2->Save();
					}
				}
			}
		}
		return $status;
	}
	function Delete() {
		$status = Array();
		//suppression de la categorie dans les bases auto administratée
		$dbs = Sys::$Modules["Vitrine"]->callData("Database/Auto=1");
		foreach ($dbs as $db) {
			$db = genericClass::createInstance("Vitrine",$db);
			//suppression de la categorie
			$status = array_merge($status,SubRange::remove($db,$this));
		}
		//Categories enfants
		$ch = $this->getChildren("Categorie");
		foreach ($ch as $c)$status = array_merge($status,$c->Delete());
		//Produits enfants
		$ph = $this->getChildren("Produit");
		foreach ($ph as $p)$status = array_merge($status,$p->Delete());
		$status[] = Array("delete",parent::Delete(),$this->Id,$this->Module,$this->ObjectType,null,null,null,null,null);
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
}
