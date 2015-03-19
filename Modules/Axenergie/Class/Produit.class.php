<?php
class Produit extends genericClass {

	function Save(){
		//init status
		$status=Array();
		//creation
		//if (sizeof(explode("texture",$this->Image))==1){
		//	$this->Texture=Produit::createTexture($this->Image,$this->ImageBottom);
		//}
		//mise à jour de la version
		if ($this->Id>0){
			$this->Version=(int)$this->Version+1;
		}else $this->Version=1;
		//sauvegarde
		genericClass::Save();
		$key = 'ProDescription.ProduitDescr';
		$this->saveDescription($this->$key);
		//si la categorie est automatique alors on propage
		$cat = $this->getParents("Categorie");
		if (isset($cat[0])){
			$cat = $cat[0];
			//oui c'est inversé !! je sais mais il y eu trop de changement
			if ($cat->Prive==1&&$cat->Affiche==0){
				//propagation dans les bases autoadministrées
				$status = Array();
				//ajout de la categorie sur l'ensemble des dbs auto administratées
				$dbs = Sys::$Modules["Axenergie"]->callData("Database/Auto=1");
				foreach ($dbs as $db) {
					$db = genericClass::createInstance("Axenergie",$db);
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

	private function saveDescription($descr) {
		if(! $descr) return;
		$ord = 0;
		$old = $this->getChilds('ProDescription');
		foreach($descr as $desc) {
			$id = $desc->Id;
			$d = genericClass::createInstance('Axenergie','ProDescription');
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
		$mod = $this->getChildren('Modele');
		if(is_array($mod) && count($mod))
			throw new Exception('Ce produit ne peut être supprimé');
		
		$status = Array();
		//si la categorie est automatique alors on propage
		$cat = $this->getParents("Categorie");
		$cat = $cat[0];
		//oui c'est inversé !! je sais mais il y eu trop de changement
		if ($cat->Prive==1&&$cat->Affiche==0){
			//suppression de la categorie dans les bases auto administratée
			$dbs = Sys::$Modules["Axenergie"]->callData("Database/Auto=1");
			foreach ($dbs as $db) {
				$db = genericClass::createInstance("Axenergie",$db);
				//suppression de la categorie
				$status = array_merge($status,SubProduct::remove($db,$this));
			}
		}
		$des = $this->getChilds('ProDescription');
		foreach($des as $d) $d->Delete();
		//Modeles enfants
		$ph = $this->getChildren("Modele");
		foreach ($ph as $p)$status = array_merge($status,$p->Delete());
		$status[] = Array("delete",parent::Delete(),$this->Id,$this->Module,$this->ObjectType,null,null,null,null,null);
		return $status;
//		return genericClass::Delete();
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
