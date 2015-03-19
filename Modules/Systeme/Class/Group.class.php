<?php
class Group extends genericClass{
	/**
	 * Override Save function
	 * Surcharge de la fonction save pour créer les éléments nécessaire en fonction du role du groupe pere
	 */
	function Save(){
		$New=false;
		if ($this->Id==NULL) $New = true;
		parent::Save();
		//Detection de la nature du pere (ne fonctionne que pour le niveau deux)
		$ROLE=false;
		$Gr = Sys::$Modules["Systeme"]->callData("Group/*/Group/Group/".$this->Id);
		if (is_array($Gr))foreach ($Gr as $g){
			$G = genericClass::createInstance("Systeme",$g);
			$Ro = $G->getParents("Role");
			if (sizeof($Ro)&&is_object($Ro[0])){
				$ROLE = $Ro[0]->Title;
			}
		}
		//Detectin du role
		switch ($ROLE){
			case "USER":
				//Vérification de l'existence d'une base de donnée
				$bln = $this->getContryDatabase();
				//Creation d'un accés sur la base de donnée dédiée.
				$mbl = $this->getChilds("Menu");
				if (!is_array($mbl)||!sizeof($mbl)){
					//création des menus par défaut
					$this->initDefaultDatabaseMenuAccess();
				}
				//Verification de l'existence du groupe LOCAL_MANAGER
				$gr = Sys::$Modules["Systeme"]->callData("Group/Role.RoleId(Title=LOCAL_MANAGER)");
				$gr = genericClass::createInstance("Systeme",$gr[0]);
				$grlm = Sys::$Modules["Systeme"]->callData("Group/".$gr->Id."/Nom=".$this->Nom);
				if (!is_array($grlm)||!sizeof($grlm)){
					//creation du groupe local manager
					$lm = genericClass::createInstance("Systeme","Group");
					$lm->Set("Nom",$this->Nom);
					$lm->addParent($gr);
					$lm->Save();
				}
			break;
			case "LOCAL_MANAGER":
				//Vérification de l'existence d'une base de donnée
				$bln = $this->getContryDatabase();
				//Création d'un accés administrator sur la base de donnée par défaut
				$mbl = $this->getChilds("Menu");
				if (!is_array($mbl)||!sizeof($mbl)){
					//création des menus par défaut
					$this->initAdminDatabaseMenuAccess($bln);
				}
				//Verification de l'existence du groupe USER
				$gr = Sys::$Modules["Systeme"]->callData("Group/Role.RoleId(Title=USER)");
				$gr = genericClass::createInstance("Systeme",$gr[0]);
				$grlm = Sys::$Modules["Systeme"]->callData("Group/".$gr->Id."/Nom=".$this->Nom);
				if (!is_array($grlm)||!sizeof($grlm)){
					//creation du groupe local manager
					$lm = genericClass::createInstance("Systeme","Group");
					$lm->Set("Nom",$this->Nom);
					$lm->addParent($gr);
					$lm->Save();
				}
			break;
		}
	}

	/**
	 * getGroupFromRole
	 */
	 static function getGroupFromRole($Role){
	 	$out=Array();
	 	$r = explode(',',$Role);
		foreach ($r as $ro){  
		 	$bl = Sys::$Modules["Systeme"]->callData("Role/Title=".$ro."/Group");
			if (is_array($bl))foreach ($bl as $b) $out[]= genericClass::createInstance('Systeme',$b);
		}
		return $out;
	 } 
	/**
	 * getContryDatabase
	 * Retourne et / ou Creation de la base de donnée pour le pays
	 */
	 public function getContryDatabase() {
		$bl = Sys::$Modules["Vitrine"]->callData("Database/Pays=".$this->Nom);
		if (!is_array($bl)||!sizeof($bl)){
			//creation de la base de donnée
			$bln = genericClass::createInstance("Vitrine","Database");
			$bln->Pays = $this->Nom;
			$bln->Nom = $this->Nom." Database";
			$bln->Save();
		}else $bln = genericClass::createInstance("Vitrine",$bl[0]);
		return $bln;
	 }

	/**
	 * initDefaultDatabaseMenuAccess
	 * Creation des menus d'accès par défaut à la base de donnée.
	 */
	 public function initDefaultDatabaseMenuAccess() {
		//creation du menu layout management
		$bln = genericClass::createInstance("Systeme","Menu");
		$bln->Titre = "My Layouts management";
		$bln->Url = "Layout_management";
		$bln->Alias = "Planogramme/Application";
		$bln->addParent($this);
		$bln->Save();
		//creation du menu Project list
		$bln2 = genericClass::createInstance("Systeme","Menu");
		$bln2->Titre = "My layouts";
		$bln2->Url = "Project_list";
		$bln2->Alias = "Planogramme/Projet/FormList";
		$bln2->addParent($bln);
		$bln2->Save();
	 }
	
	/**
	 * initDefaultDatabaseMenuAccess
	 * Creation des menus d'accès par défaut à la base de donnée.
	 */
	 public function initAdminDatabaseMenuAccess($db) {
		//creation du menu layout management
		$bln = genericClass::createInstance("Systeme","Menu");
		$bln->Titre = "My local database";
		$bln->Url = "Local_administrator_tools";
		$bln->Alias = "Vitrine/Application";
		$bln->addParent($this);
		$bln->Save();
		//creation du menu Events list
		$bln2 = genericClass::createInstance("Systeme","Menu");
		$bln2->Titre = "Lst changes in global database";
		$bln2->Url = "database_events_list";
		$bln2->Alias = "Systeme/Event/FormList";
		$bln2->Filters = "EventModule=Vitrine&EventObjectClass=Produit";
		$bln2->Ordre = 1;
		$bln2->addParent($bln);
		$bln2->Save();
		//creation du menu MY_DATABASE admin access
		$bln3 = genericClass::createInstance("Systeme","Menu");
		$bln3->Titre = $this->Nom." database";
		$bln3->Url = "my_local_database_administration";
		$bln3->Alias = "Vitrine/Database/".$db->Id."/DatabaseManagement";
		$bln3->Ordre = 0;
		$bln3->addParent($bln);
		$bln3->Save();
	 }

	/**
	 * Override Delete function
	 * Suppression de tous les éléments sous jacents (sauf user)
	 */
	 function Delete(){
	 	//suppression des menus
	 	$mpd = $this->getChilds("Menu");
		if (is_array($mpd)&&sizeof($mpd))foreach ($mpd as $mp){
			$mp->Delete();
		}
	 	return parent::Delete();
	 }
	 
	 /**
	  * exportMenus
	  *export menus as xml
	  */
	  public function exportMenus(){
		$Mt = Sys::$Modules["Systeme"]->callData("Systeme/Group/".$this->Id."/Menu/*");
		if (is_array($Mt)&&sizeof($Mt)) {
			//On concatene les menus dans un seul tableau
			foreach ($Mt as $M) {
				//unset($M["Id"]);
				$Menus[] = $M;
			}
		}
		//Maintenant on reorganise les menus afin qu ils soient exploitables
		$Menus = StorProc::sortRecursivResult($Menus,"Menus");
		$Menus = StorProc::cleanRecursivArrays($Menus,"Menus");
		$Menus = $this->quickSort($Menus,"Ordre");
 /* 		require_once 'Class/Utils/Serializer.class.php';
	  	$xml = '';
		$options = array(
                    XML_SERIALIZER_OPTION_INDENT      => '    ',
                    XML_SERIALIZER_OPTION_LINEBREAKS  => "\n",
                    XML_SERIALIZER_OPTION_DEFAULT_TAG => 'unnamedItem',
                    XML_SERIALIZER_OPTION_TYPEHINTS   => true
                );
		$serializer = new XML_Serializer($options);
		$result = $serializer->serialize($Menus);
		if( $result === true ) {
			$xml = $serializer->getSerializedData();
		}
		
		return htmlspecialchars($xml);*/
		
		return base64_encode(serialize($Menus));
	  }
	  public function importMenus($xml){
		/*require_once 'Class/Utils/Unserializer.class.php';
		$options = array(
                    XML_SERIALIZER_OPTION_INDENT      => '    ',
                    XML_SERIALIZER_OPTION_LINEBREAKS  => "\n",
                    XML_SERIALIZER_OPTION_DEFAULT_TAG => 'unnamedItem',
                    XML_SERIALIZER_OPTION_TYPEHINTS   => true
                );
	  	$unserializer = new XML_Unserializer();
		$status = $unserializer->unserialize($xml);
		$data = $unserializer->getUnserializedData();
		print_r($data);*/
		$data = unserialize(base64_decode(trim($xml)));
		return $this->importRecursivMenu($data,$this); 
	  }
	  private function importRecursivMenu($data,$parent){
	  	$out="";
	  	foreach ($data as $d){
	  		$out.="-> Creation du menu \r\n";
	  		$t = genericClass::createInstance('Systeme',$d);
			$t->addParent($parent);
			$t->Save();
			if (is_array($d["Menus"]))$out.=$this->importRecursivMenu($d["Menus"], $t);
	  	}
		return $out;
	  }
}
