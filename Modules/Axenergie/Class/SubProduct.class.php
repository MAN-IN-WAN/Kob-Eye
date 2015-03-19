<?php
class SubProduct extends genericClass {

/*	
	function Save() {
		$adh = Sys::$User->getChilds('Adherent');
		$dba = $adh[0]->getChildren('Database');
		$pro = $dba[0]->getChildren('SubRange/Categorie.CategorieId(Type=2)');
		if(is_array($pro) && count($pro)) {
			if($this->Promo) $this->addParent($pro[0]);
			else $this->delParent($pro[0]);
		}
		if(! $this->PrixAdherent) $this->PrixAdherent = $this->PrixHT;
		parent::Save();
	}
*/
	
	/**
	 * initFromObject
	 * Initialisation d'un subproduct depuis unproduit
	 */
	function initFromObject($obj) {
		$this->Nom = $obj->Nom;
		$this->Version = $obj->Version;
	}
	function Delete() {
		$status = Array();
		$m = $this->getChilds("SubModel");
		if (is_array($m))foreach ($m as $mo){
			$status = array_merge($status,$mo->Delete());
		}
		$p = $this->getParents("Produit");
		$status[] = Array("delete",parent::Delete(),$this->Id,$this->Module,$this->ObjectType,null,null,null,null,null,Array("ProduitId"=>$p[0]->Id));
//		$status = Array(Array("delete",parent::Delete(),$this->Id,$this->Module,$this->ObjectType));
		return $status;
	}
	static function search($db,$target){
		$p = $target->getParents("Categorie");
		if (isset($p[0])&&is_object($p[0])){
			$c = Sys::$Modules["Axenergie"]->callData("Database/".$db->Id."/SubRange/*/Categorie.CategorieId(".$p[0]->Id.")");
			if (sizeof($c)){
				$c = Sys::$Modules["Axenergie"]->callData("SubRange/".$c[0]["Id"]."/SubProduct/Produit.ProduitId(".$target->Id.")");
				return genericClass::createInstance("Axenergie",$c[0]);
			}
			return false;
		}
		return false;
	}
	
	static function add($db,$target,$args,$recurs=true){
		//verification de l'existence
		$c2 = SubProduct::search($db,$target);
		if (is_object($c2)){
			//l'objet existe deja on le met à jour
			$c2->initFromObject($target);
			$c2->Save();
			$status = Array(Array("edit",true,$c2->Id,$c2->Module,$c2->ObjectType,null,null,null,null,null,Array("ProduitId"=>$target->Id)));
		}else{
			//Creation de SubProduct
			$sr = genericClass::createInstance("Axenergie","SubProduct");
			//Recuperation de l'objet parent
			$p = $target->getParents("Categorie");
			if (isset($p[0])&&is_object($p[0])){
				$p = $p[0];
				$c = Sys::$Modules["Axenergie"]->callData("Database/".$db->Id."/SubRange/*/Categorie.CategorieId(".$p->Id.")");
				$GLOBALS["Systeme"]->Log->log("ADD SUB PRODUCT ");
				if (sizeof($c)){
					$c = genericClass::createInstance('Axenergie',$c[0]);
					$sr->AddParent($c);
				}else return SubProduct::checkParentNode($db,$target,$args,$recurs);
			}else return "CATEGORIE PARENTE INTROUVABLE FIRST LEVEL ";
			$sr->initFromObject($target);
			$sr->AddParent($target);
			$sr->Save();
			$status = Array(Array("add",true,$sr->Id,$sr->Module,$sr->ObjectType,$c->ObjectType,$c->Id,null,null,null,Array("ProduitId"=>$target->Id)));
		}
		if ($recurs){
			//Ajout des enfants models s il en fut
			$m = $target->getChilds('Modele');
			if (is_array($m))foreach ($m as $mo){
				$status = array_merge($status,SubModel::add($db,$mo,$args));
			}
		}
		
		return $status;
	}
	/**
	 * update
	 * mise à jour du produit
	 */
	function update(){
		//recupération du produit référent
		$p = $this->getParents("Produit");
		if (!is_object($p[0]))
			return "Cant find the original product. Is this a orphan sub product ?";
		$this->initFromObject($p[0]);
		$this->Save();
		return WebService::WSStatusMulti(Array(Array("edit",true,$this->Id,$this->Module,$this->ObjectType,null,null,null,null,null,Array("ProduitId"=>$p[0]->Id))));
	}
	
	/**
	 * checkParentNode
	 * Remonte recursivement pour créer les range manquantes
	 */
	static function checkParentNode($db,$target,$args,$recurs=false){
		$GLOBALS["Systeme"]->Log->log("CHECK PARENT NODE FROM PRODUCT");
		//Récupération du parent
		$p = $target->getParents("Categorie");
		//Crée le parent
		$status = SubRange::add ($db,$p[0],$args,false);
		//Recréation du courant
		$status = array_merge($status,SubProduct::add($db,$target,$args,$recurs));
		return $status;
	}
	
	static function remove($db,$target){
		$O = Sys::$Modules["Axenergie"]->callData("Database/".$db->Id."/SubRange/*/SubProduct/Produit.ProduitId(".$target->Id.")");
		$GLOBALS["Systeme"]->Log->log("REMOVE SUBPRODUCT ");
		if (is_array($O))foreach ($O as $s){
			$s = genericClass::createInstance("Axenergie",$s);
			//Verification des modeles sous-jacents
			$status = $s->Delete();
		}
		return $status;
	}
/*
	function getImage() {
		$prod = $this->getParents('Produit');
		$prod = $prod[0];
		return $prod->getImage($this, $prod);
	}
*/

}
