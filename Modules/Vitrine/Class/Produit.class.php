<?php
class Produit extends genericClass {
	function Save(){
		//init status
		$status=Array();
		//creation
		if (sizeof(explode("texture",$this->Image))==1){
			$this->Texture=Produit::createTexture($this->Image,$this->ImageBottom);
		}
		//mise à jour de la version
		if ($this->Id>0){
			$this->Version=(int)$this->Version+1;
		}else $this->Version=1;
		//sauvegarde
		genericClass::Save();
		//$status[] = Array($this->Version==1?"add":"edit",1,$this->Id,$this->Module,$this->ObjectType,null,null,null,null,null);
		//si la categorie est automatique alors on propage
		$cat = $this->getParents("Categorie");
		if (isset($cat[0])){
			$cat = $cat[0];
			//oui c'est inversé !! je sais mais il y eu trop de changement
			if ($cat->Prive==1&&$cat->Affiche==0){
				//propagation dans les bases autoadministrées
				$status = Array();
				//ajout de la categorie sur l'ensemble des dbs auto administratées
				$dbs = Sys::$Modules["Vitrine"]->callData("Database/Auto=1");
				foreach ($dbs as $db) {
					$db = genericClass::createInstance("Vitrine",$db);
					$te = SubProduct::search($db,$this);
					if (is_object($te)){
						$te->initFromObject($this);
						$te->Save();
					}else{
						//ajout du produit
						$status = array_merge($status,SubProduct::add($db,$this,new stdClass(),true));
					}
				}
			}
		}
		return $status;
	}
	function Delete() {
		$status = Array();
		//si la categorie est automatique alors on propage
		$cat = $this->getParents("Categorie");
		$cat = $cat[0];
		//oui c'est inversé !! je sais mais il y eu trop de changement
		if ($cat->Prive==1&&$cat->Affiche==0){
			//suppression de la categorie dans les bases auto administratée
			$dbs = Sys::$Modules["Vitrine"]->callData("Database/Auto=1");
			foreach ($dbs as $db) {
				$db = genericClass::createInstance("Vitrine",$db);
				//suppression de la categorie
				$status = array_merge($status,SubProduct::remove($db,$this));
			}
		}
		//Modeles enfants
		$ph = $this->getChildren("Modele");
		foreach ($ph as $p)$status = array_merge($status,$p->Delete());
		//
		$status[] = Array("delete",parent::Delete(),$this->Id,$this->Module,$this->ObjectType,null,null,null,null,null);
		return $status;
	}
	static function createTexture($Image,$ImageBottom=""){
			//generation de l'image texture (512x512)
			$im2 = ImageCreateTrueColor(512, 512);
			//Image
			list($w1,$h1) = getimagesize($Image); 
			$i1 = imagecreatefromjpeg($Image);
			imagecopyResampled ($im2, $i1, 0, 0, 0, 0, 512, 338, $w1, $h1);
			//ImageBottom
			if (!empty($ImageBottom)){
				list($w2,$h2) = getimagesize($ImageBottom); 
				$i2 = imagecreatefromjpeg($ImageBottom);
				imagecopyResampled ($im2, $i2, 0, 339, 0, 0, 512, 174, $w2, $h2);
			}else{
				imagecopyResampled ($im2, $i1, 0, 339, 0, 0, 512, 174, $w1, $h1);
			}
			//enregistrement de l'image
			imagejpeg($im2,$Image.".texture.jpg",90);
			return $Image.".texture.jpg";
	}
	function addSub($args) {
		$GLOBALS["Systeme"]->Log->log("xxxxxxADD SUB PRODUCT CALLxxxxxxx", $ret);
		return "ADD SUB PRODUCT CALL";
	}
	function removeSub($args) {
		$GLOBALS["Systeme"]->Log->log("xxxxxxREMOVE SUB PRODUCT CALLxxxxxxx", $args);
		return "REMOVE SUB PRODUCT CALL";
	}
}
