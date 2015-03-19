<?php
class SubRange extends genericClass {
	/**
	 * initFromObject
	 * Initialisation d'"une subrange depuis une categorie"
	 */
	function initFromObject($obj) {
		$this->Nom = $obj->Nom;
		$this->Version = $obj->Version;
	}
	function Delete() {
		$status = Array();
		//suppression des subranges
		$m = $this->getChilds("SubRange");
		if (is_array($m))foreach ($m as $mo){
			$status = array_merge($status,$mo->Delete());
		}
		//suppression des subproducts
		$m = $this->getChilds("SubProduct");
		if (is_array($m))foreach ($m as $mo){
			$status = array_merge($status,$mo->Delete());
		}
		$p = $this->getParents("Categorie");
		$result=parent::Delete();
		if (sizeof($p))
			$status[] = Array("delete",$result,$this->Id,$this->Module,$this->ObjectType,null,null,null,null,null,Array("CategorieId"=>$p[0]->Id));
//		$status = Array(Array("delete",parent::Delete(),$this->Id,$this->Module,$this->ObjectType));
		return $status;
	}
	static function search($db,$target){
		$c = Sys::$Modules["Vitrine"]->callData("Database/".$db->Id."/SubRange/*/Categorie.CategorieId(".$target->Id.")");
		if (sizeof($c)){
			return genericClass::createInstance("Vitrine",$c[0]);
		}
		return false;
	}
	static function add($db,$target,$args,$recurs=true,$auto = false){
		//verification de l'existence
		$c2 = SubRange::search($db,$target);
		if (is_object($c2)){
			//l'objet existe deja on le met à jour
			$c2->initFromObject($target);
			$c2->Save();
			$status = Array(Array("edit",true,$c2->Id,$c2->Module,$c2->ObjectType,null,null,null,null,null,Array("CategorieId"=>$target->Id)));
		}else{
			//l'objet n'existe pas , on doit le créer
			$sr = genericClass::createInstance("Vitrine","SubRange");
			//Recuperation de l'objet parent
			if (!is_object($target))
				return;
			$p = $target->getParents("Categorie");
			if (isset($p[0])&&is_object($p[0])){
				$GLOBALS["Systeme"]->Log->log("RECHERCHE DE LA SUBRANGE CORRESPONDANTE A LA CATEGORIE ".$p->Id);
				$p = $p[0];
				$c = Sys::$Modules["Vitrine"]->callData("Database/".$db->Id."/SubRange/*/Categorie.CategorieId(".$p->Id.")");
				$GLOBALS["Systeme"]->Log->log("ADD SUB RANGE ");
				if (sizeof($c)){
					$c = genericClass::createInstance('Vitrine',$c[0]);
					$sr->AddParent($c);
				}else return SubRange::checkParentNode($db,$target,$args,$recurs);
			}else{
				$GLOBALS["Systeme"]->Log->log("IMPOSSIBLE DE TROUVER DE SUBRANGE CORREPSONDANTE DONC ON APPLIQUE LA SUBRANGE A LA RACINE",$p);
				//Categorie racine
				$c = genericClass::createInstance('Vitrine','SubRange');
				$c->Id = 0;
				$sr->AddParent($db);
			}
			$sr->initFromObject($target);
			$sr->AddParent($target);
			$sr->Save();
			$status = Array(Array("add",true,$sr->Id,$sr->Module,$sr->ObjectType,$c->ObjectType,$c->Id,null,null,null,Array("CategorieId"=>$target->Id)));
		}
		if ($recurs){
			//Ajout des enfants range s il en fut
			$m = Sys::$Modules["Vitrine"]->callData("Categorie/".$target->Id."/Categorie/Affiche=0".(($auto)?"&Prive=1":""));
			if (is_array($m))foreach ($m as $mo){
				$mo = genericClass::createInstance("Vitrine",$mo);
				$status = array_merge($status,SubRange::add($db,$mo,$args));
			}
			//Ajout des enfants produits s il en fut
			$m = $target->getChilds('Produit');
			if (is_array($m))foreach ($m as $mo){
				$status = array_merge($status,SubProduct::add($db,$mo,$args));
			}
		}
		return $status;
	}
	/**
	 * update
	 * mise à jour de la categorie
	 */
	function update(){
		//recupération de la categorie référent
		$p = $this->getParents("Categorie");
		if (!is_object($p[0]))
			return "Cant find the original range. Is this a orphan sub range ?";
		$this->initFromObject($p[0]);
		$this->Save();
		return WebService::WSStatusMulti(Array(Array("edit",true,$this->Id,$this->Module,$this->ObjectType,null,null,null,null,null,Array("CategorieId"=>$p[0]->Id))));
	}
	/**
	 * checkParentNode
	 *
	 * Remonte recursivement pour créer les range manquantes
	 */
	static function checkParentNode($db,$target,$args,$recurs=false){
		//Récupération du parent
		$p = $target->getParents("Categorie");
		//Crée le parent
		if (is_array($p)&&is_object($p[0]))
			$status = SubRange::add ($db,$p[0],$args,false);
		//Recréation du courant
		$status = array_merge($status,SubRange::add($db,$target,$args,$recurs));
		return $status;
	}


	static function remove($db,$target){
		//Dans ce cas on peut linker la categorie direct comme SubRange
		$O = Sys::$Modules["Vitrine"]->callData("Database/".$db->Id."/SubRange/*/Categorie.CategorieId(".$target->Id.")");
		if (is_array($O))foreach ($O as $s){
			$s = genericClass::createInstance("Vitrine",$s);
			$status = $s->Delete();
		}
		return $status;
	}
}
