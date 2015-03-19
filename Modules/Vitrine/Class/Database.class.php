<?php
class Database extends genericClass {
	/**
	 * Save override
	 */
	function Save() {
		$creation = false;
		if (!$this->Id)$creation = true;
		else{
			//recherche des valeurs d'origine
			$r = Sys::$Modules["Vitrine"]->callData("Database/".$this->Id);
			if ($r[0]["Auto"]==0&&$this->Auto==1)$creation = true;
		}
		parent::Save();
		if ($creation) $this->initDefaultDatabase();
	}
	
	/**
	 * initDefaultDatabase
	 * Initialisation des éléments par defaut de la base de donnée.
	 */
	 public function initDefaultDatabase(){
	 	$r = Sys::$Modules["Vitrine"]->callData("Categorie/Affiche=0&Prive=1");
		$status = Array();
		if (is_array($r)&&sizeof($r))foreach ($r as $ra){
			SubRange::add($this,genericClass::createInstance("Vitrine",$ra),new stdClass(),true,true);
		}
	 }
	
	function addSub($args) {
		if ($this->Id=="")
			return "DATABASE NON INITLIALISEE";
		if (!is_object($args)||$args->objectClass==""||$args->id=="")
			return "ERREUR DE REQUETE... INFORMATIONS MANQUANTES";
		//Recuperation de l'objet à cloner
		$O = Sys::$Modules["Vitrine"]->callData($args->objectClass."/".$args->id);
		$target = genericClass::createInstance("Vitrine",$O[0]);
		//Verification de l'objet
		switch ($args->objectClass){
			case "Categorie":
				$status = SubRange::add($this,$target,$args);
			break;
			case "Produit":
				$status = SubProduct::add($this, $target, $args);
			break;
			case "Modele":
				$status = SubModel::add($this, $target, $args);
			break;
		}
		$GLOBALS["Systeme"]->Log->log("xxxxxxADD SUB DATABASE CALLxxxxxxx");
		return WebService::WSStatusMulti($status);
	}

	
	
	function removeSub($args) {
		if ($this->Id=="")
			return "DATABASE NON INITLIALISEE";
		if (!is_object($args)||$args->objectClass==""||$args->id=="")
			return "ERREUR DE REQUETE... INFORMATIONS MANQUANTES";
		//Recuperation de l'objet d'origine
		$O = Sys::$Modules["Vitrine"]->callData($args->objectClass."/".$args->id);
		$target = genericClass::createInstance("Vitrine",$O[0]);
		//Verification de l'objet
		switch ($args->objectClass){
			case "Categorie":
				$status = SubRange::remove($this,$target);
			break;
			case "Produit":
				$status = SubProduct::remove($this,$target);
			break;
			case "Modele":
				$status = SubModel::remove($this,$target);
			break;
		}
		
		$GLOBALS["Systeme"]->Log->log("xxxxxxREMOVE SUB DATABASE CALLxxxxxxx");
		return WebService::WSStatusMulti($status);
	}
	
	function updateItem($args) {
		if ($this->Id==""){
			$GLOBALS["Systeme"]->Log->log("DATABASE NON INITLIALISEE");
			return "DATABASE NON INITLIALISEE";
		}
		if (!is_object($args)||$args->objectClass==""||$args->id==""){
			$GLOBALS["Systeme"]->Log->log("ERREUR DE REQUETE... INFORMATIONS MANQUANTES");
			return "ERREUR DE REQUETE... INFORMATIONS MANQUANTES";
		}
		//Recuperation de l'objet à cloner
		$O = Sys::$Modules["Vitrine"]->callData($args->objectClass."/".$args->id);
		$target = genericClass::createInstance("Vitrine",$O[0]);
		//$status = $target->update();
		$GLOBALS["Systeme"]->Log->log("xxxxxxUPDATE ITEM DATABASE xxxxxxx",$args);
		return WebService::WSStatusMulti($status);
	}

	function getDatabaseFromUser() {
		//Recherche du group du role USER
		$gro = Group::getGroupFromRole('USER');
		$gro = $gro[0];
		//recherche de la database definit pour un utilisateur
		$grp = Sys::$Modules["Systeme"]->callData('Group/'.$gro->Id.'/Group/User.GroupId('.Sys::$User->Id.')');
		$grp = genericClass::createInstance("Systeme",$grp[0]);
		//recherche de la base de donnée
		$db = Sys::$Modules["Vitrine"]->callData('Database/Pays='.$grp->Nom);
		return genericClass::createInstance('Vitrine',$db[0]);
	}

		/* ___________________________________________________________________________________________
	 *																						REMOTE
	 */
	function getSubRange($g){
		$o = Sys::$Modules["Vitrine"]->callData("Database/".$this->Id."/SubRange/Nom=".$g);
		$o = Sys::$Modules["Vitrine"]->callData("Database/".$this->Id."/SubRange/".$o[0]["Id"]."/SubRange");
		//$GLOBALS["Systeme"]->Log->log("xxxxxxGET SUB RANGE Database/".$this->Id."/SubRange/Nom=$g/SubRange  xxxxxxx",$o);
		//return WebService::WSStatus('method',1,'','','','','',array(),$o);
		return WebService::WSData("Nom", 0, sizeof($o), sizeof($o), "Database/".$this->Id."/SubRange/".$o[0]["Id"]."/SubRange", "SubRange", "", "Vitrine", "SubRange", $o);
	}
	function getSubProduct($g,$g2){
		$o = Sys::$Modules["Vitrine"]->callData("Database/".$this->Id."/SubRange/Nom=".$g);
		$o2 = Sys::$Modules["Vitrine"]->callData("Database/".$this->Id."/SubRange/".$o[0]["Id"]."/SubRange/".$g2."/SubProduct");
		$o2 = array_merge($o2,Sys::$Modules["Vitrine"]->callData("Database/".$this->Id."/SubRange/".$o[0]["Id"]."/SubRange/".$g2."/*/SubProduct"));
		$out=Array();
		if (is_array($o2))foreach ($o2 as $p){
			$r = Sys::$Modules["Vitrine"]->callData("SubProduct/".$p["Id"]."/SubModel",false,0,1000,"ASC","Packaging2.Order");
			Klog::l("xxxxxxGET SUB PRODUCT $g / $g2  xxxxxxx",$r);
			$p["references"] = $r;
			$out[] = $p;
		}
		return WebService::WSData("Nom", 0, sizeof($o), sizeof($o), "Database/".$this->Id."/SubRange/".$o[0]["Id"]."/SubProduct", "SubProduct", "", "Vitrine", "SubProduct", $out);
	}
}
