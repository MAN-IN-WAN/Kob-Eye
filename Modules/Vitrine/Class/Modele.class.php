<?php
class Modele extends genericClass {
	function Save(){
		//Definition du chemin
		$Chemin = 'Home/CodeBarre/CB-'.$this->GenCode.'.png';
		//Verfication de l'existence du champ CodeBarre
		if (($this->CodeBarre==""||!file_exists($this->CodeBarre)||$this->CodeBarre!=$Chemin)&&$this->GenCode!=""){
			//Generation du fichier code barre
			require_once("Class/Lib/CodeBarre.class.php");
			Connection::mk_dir('Home/CodeBarre');
			$ean13 = new debora($this->GenCode);
			$ean13-> makeImage('png',$Chemin);
			//Enregistrement dans un emplacement
			$this->CodeBarre = $Chemin;
		}
		//mise à jour de la version
		if ($this->Id>0){
			$this->Version=(int)$this->Version+1;
		}else $this->Version=1;
		//Sauvegarde
		genericClass::Save();
		//$status[] = Array($this->Version==1?"add":"edit",1,$this->Id,$this->Module,$this->ObjectType,null,null,null,null,null);
		//si la categorie est automatique alors on propage
		$prod = $this->getParents("Produit");
		$cat = $prod[0]->getParents("Categorie");
		//oui c'est inversé !! je sais mais il y eu trop de changement
		if ($cat[0]->Prive==1&&$cat[0]->Affiche==0){
			//propagation dans les bases autoadministrées
			$status = Array();
			//ajout de la categorie sur l'ensemble des dbs auto administratées
			$dbs = Sys::$Modules["Vitrine"]->callData("Database/Auto=1");
			foreach ($dbs as $db) {
				$db = genericClass::createInstance("Vitrine",$db);
				$te = SubModel::search($db,$this);
				if (is_object($te)){
					$te->initFromObject($this);
					$te->Save();
				}else{
					//ajout du modele
					$status = array_merge($status,SubModel::add($db,$this,new stdClass(),true));
				}
			}
		}
		return $status;
	}
	function Delete() {
		$status = Array();
		//si la categorie est automatique alors on propage
		$prod = $this->getParents("Produit");
		$cat = $prod[0]->getParents("Categorie");
		//oui c'est inversé !! je sais mais il y eu trop de changement
		if ($cat[0]->Prive==1&&$cat[0]->Affiche==0){
			//suppression de la categorie dans les bases auto administratée
			$dbs = Sys::$Modules["Vitrine"]->callData("Database/Auto=1");
			foreach ($dbs as $db) {
				$db = genericClass::createInstance("Vitrine",$db);
				//suppression du modele
				$status = array_merge($status,SubModel::remove($db,$this));
			}
		}
		$status[] = Array("delete",parent::Delete(),$this->Id,$this->Module,$this->ObjectType,null,null,null,null,null);
		return $status;
	}
	function addSub($args) {
		$GLOBALS["Systeme"]->Log->log("xxxxxxADD SUB MODEL CALLxxxxxxx", $ret);
		return "ADD SUB MODEL CALL";
	}
	function removeSub($args) {
		$GLOBALS["Systeme"]->Log->log("xxxxxxREMOVE SUB MODEL CALLxxxxxxx", $ret);
		return "REMOVE SUB MODEL CALL";
	}	
}
?>