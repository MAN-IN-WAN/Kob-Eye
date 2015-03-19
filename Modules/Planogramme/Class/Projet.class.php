<?php 
class Projet extends genericClass{
	function unSerialize() {
		if (empty($this->Data)) return;
		$o = unserialize($this->Data);
		print_r($o);
		$out = new stdClass();
		$out->projectName = $o["NomProjet"];
		$out->stands = Array();
		if (is_array($o["Lineaires"]))foreach ($o["Lineaires"] as $l){
			$li = new stdClass();
			$li->standName = $l["Name"];
			//$li->depth = $l->
			$li->height = $l["HauteurCm"];
			$li->width = $l["LargeurCm"];
			$li->range = $this->Gamme=="11"?"VET":"POS";
			$li->levels = Array();
			if (is_array($l["Etages"]))foreach ($l["Etages"] as $e){
				$et = new stdClass();
				$et->numero = $e["Num"];
				$et->depth = $e["Profondeur"];
				$et->height = $e["Hauteur"];
				$et->packs = Array();
				if (is_array($l["Packs"]))foreach ($l["Packs"] as $p){
					if ($p["EtageNum"]==$e["Num"]){
						$te = explode("-",$p["Values"]["Nom"]);
						$pa = new stdClass();
						$pa->productname = $te[0];
						$pa->modelname = $te[1];
						$pa->name = $p["Values"]["label"];
						$pa->unitperbox = $p["Values"]["UcPerBox"];
						$pa->weight = $p["Values"]["Poids"];
						$pa->depth = $p["Values"]["Profondeur"];
						$pa->x = $p["Values"]["X"]+($p["Values"]["Largeur"]/2);
						$pa->model3d = $p["Values"]["Hauteur"]>$p["Values"]["Largeur"]?"StandardPack":"BottomPack";
						$pa->y = $p["Values"]["Y"];
						$pa->height = $p["Values"]["Hauteur"];
						$pa->texture = $p["Values"]["Photo"].".texture.jpg";
						//verification de l'existence de la texture
						if (!file_exists($pa->texture))
							Produit::createTexture($p["Values"]["Photo"]);
						$pa->image = $p["Values"]["Photo"];
						$pa->width = $p["Values"]["Largeur"];
						$pa->gencode = $p["Values"]["GenCode"];
						$pa->packs = Array();
						$et->packs[] = $pa;
					}
				}
				$li->levels[] = $et;
			} 
			$out->stands[] = $li;
		}
		$this->Donnee = json_encode($out);
		parent::Save();
	}

	function Delete(){
		//Verification du propriétaire
		if (($this->userCreate==Sys::$User->Id)){
			parent::Delete();
			return true;
		}else throw new Exception("You cant delete this project.");

	}

	function reset() {
		//Si l'utilisateur n'est pas le propriétaire alors on crée une copie du projet
		$this->Id=NULL;
		$this->userCreate=NULL;
		$this->groupCreate=NULL;
		$this->uid=NULL;
		$this->gid=NULL;
	}

	function Save() {
		//Verification du propriétaire et/ou de l'option saveAs
		if (($this->userCreate!=Sys::$User->Id)){
			$this->reset();
		}
		//Verification projet vérolé
		if (empty($this->Donnee)&&empty($this->Data))
			if (!$this->View&&$this->Id)
				return $this->Delete();
		//Vérification adaptation du projet
		if (!$this->POS&&!$this->VET&&empty($this->Donnee)){
			$this->unSerialize();
		}
		//verification Données
		if (!empty($this->Donnee)){
			$o = json_decode($this->Donnee);
			//verification de l'option saveas
			if (isset($o->saveAs)&&$o->saveAs){
				$this->reset();
				$o->saveAs = false;
			}
			$this->Nom = $o->projectName;
			//enregistrement type
			if ($o->projectType=="hidden"){
				$this->Modele = false;
				$this->Hidden = true;
			}else if ($o->projectType=="model"){
				$this->Modele = true;
				$this->Hidden = false;
			}else{
				$this->Modele = false;
				$this->Hidden = false;
			}
			//enregistrement images
			$this->ScreenShotFirst = $o->firstImage;
			$this->ScreenShot = $o->secondImage;
						//verification de l'affectation à une base de donnée
			$Bdds = null;
			if ($this->Id){
				$Bdds = $this->getParents("Database");
			}
			if (!is_array($Bdds)||!sizeof($Bdds)){
				$Bd = genericClass::createInstance('Vitrine','Database');
				$Bd = $Bd->getDatabaseFromUser();
				$this->addParent($Bd);
			}
			$details = "";
			$totalwidth=0;
			$totalheight=0;
			$totaldepth=0;
			//deifnition des ranges
			if (is_array($o->stands))foreach ($o->stands as $st){
				if ($st->range=="POS")$this->POS = true;
				if ($st->range=="VET")$this->VET = true;
				//constitution du detail
				$details.="- Shelf $st->width x $st->height x $st->depth $st->range levels: ".sizeof($st->levels)."\r\n";
				$totalwidth += $st->width;
				$totalheight = ($st->height>$totalheight)?$st->height:$totalheight;
				$totaldepth = ($st->depth>$totaldepth)?$st->depth:$totaldepth;
			}
			$details = "Total project dimensions: $totalwidth x $totalheight x $totaldepth stands: ".sizeof($o->stands)."\r\n".$details;
			$this->TotalWidth = $totalwidth+" cm";
			$this->Details = $details;
			$this->Donnee = json_encode($o);
		}
		parent::Save();
	}
	function getModels($i="") {
		//recuperation de la base de donnée de l'utilisateur
		$db = genericClass::createInstance('Vitrine','Database');
		$db = $db->getDatabaseFromUser();

		$o=  Array();
		$o[] = Array(
			"Nom"=>"Blank",
			"ScreenShot"=>"Skins/RoyalCanin/Img/Blank.jpg",
			"ScreenShotFirst"=>"Skins/RoyalCanin/Img/Blank.jpg",
			"Details"=> "New blank project"
		);
		$out = Sys::$Modules["Vitrine"]->callData("Database/".$db->Id."/Projet/Modele=1");
		if (is_array($out))
			$o = array_merge($o,$out);
		return WebService::WSData("Nom", 0, sizeof($o), sizeof($o),"Database/".$db->Id."/Projet/Modele=1", "Projet", "", "Planogramme", "Projet", $o);
	}
	function getModel($id) {
		$o = Sys::$Modules["Planogramme"]->callData("Projet/".$id,false,0,1);
		return WebService::WSData("Nom", 0, sizeof($o), sizeof($o), "Projet/Modele=1", "Projet", "", "Planogramme", "Projet", $o);
	}

}
?>
