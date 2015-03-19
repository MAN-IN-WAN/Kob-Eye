<?php
class Webservice_Axenergie extends Root implements IWebservice {
	
	private $wsdl = 'Modules/Axenergie/Class/Axenergie.xml';
	
	function getwsdl($sys) {
		$w = file_get_contents($this->wsdl);
		return str_replace($sys->Lien.'.soap', 'http://'.$_SERVER['SERVER_NAME'].'/Axenergie.soap', $w);
	}

	function soapServer($sys) {
		try { 
			$srv = new SoapServer($this->wsdl,  array('cache_wsdl'=>'WSDL_CACHE_NONE','trace'=>1,'encoding'=>'UTF-8')); //,'features'=>SOAP_SINGLE_ELEMENT_ARRAYS));
			$srv->setclass('Webservice_Axenergie');
		} catch (Exception $e) {
			echo $e;
		}
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$srv->handle();
		}
	}
	

	function getRange($id) {
		$rtype = array('Unknown','Regular','Promo','Service');
		$msg = array();
		$rngs = array();
		$prds = array();
		$rng = new ReturnRange();
		
		if($id > 0) {
			$cat = Sys::$Modules["Axenergie"]->callData("Categorie/$id",false,0,1);
			if(count($cat)) {
				$c = $cat[0];
				$rng->id = $id;
				$rng->order = $c['Ordre'];
				$rng->type = $rtype[$c['Type']];
				$rng->title = $c['Nom'];
				$rng->subTitle = $c['SousTitre'];
				$rng->colour = $c['Couleur'];
				$rng->headerPicture = $c['ImageEntete'];
				$rng->webHeaderPicture = $c['ImageEnteteWeb'];
				$rng->webTitle = $c['TitreWeb'];
				$rng->webDescription = $c['DescriptionWeb'];
				$rng->logo = $c['Logo'];
				$cat = Sys::$Modules["Axenergie"]->callData("Categorie/$id/Categorie");
				foreach($cat as $c) {
					$r = new Range();
					$r->id = $c['Id'];
					$r->order = $c['Ordre'];
					$r->type = $rtype[$c['Type']];
					$r->title = $c['Nom'];
					$r->subTitle = $c['SousTitre'];
					$r->colour = $c['Couleur'];
					$r->headerPicture = $c['ImageEntete'];
					$r->webHeaderPicture = $c['ImageEnteteWeb'];
					$r->webTitle = $c['TitreWeb'];
					$r->webDescription = $c['DescriptionWeb'];
					$r->logo = $c['Logo'];
					$rec = Sys::$Modules["Axenergie"]->callData("Categorie/$r->id/Categorie",false,0,1,'','','COUNT(*)');
					$r->rangeCount = $rec[0]['COUNT(*)'];
					$rec = Sys::$Modules["Axenergie"]->callData("Categorie/$r->id/Produit",false,0,1,'','','COUNT(*)');
					$r->productCount = $rec[0]['COUNT(*)'];
					$rngs[] = $r;
				}
				$rng->products = array();
				$cat = Sys::$Modules["Axenergie"]->callData("Categorie/$id/Produit");
				foreach($cat as $c) 
					$rng->products[] = $this->loadProduct($c['Id']);
			}
		}
		else {
			$rng->id = 0;
			$cat = Sys::$Modules["Axenergie"]->callData('Categorie');
			foreach($cat as $c) {
				$r = new Range();
				$r->id = $c['Id'];
				$r->order = $c['Ordre'];
				$r->type = $rtype[$c['Type']];
				$r->title = $c['Nom'];
				$r->subTitle = $c['SousTitre'];
				$r->colour = $c['Couleur'];
				$r->headerPicture = $c['ImageEntete'];
				$r->webHeaderPicture = $c['ImageEnteteWeb'];
				$r->webTitle = $c['TitreWeb'];
				$r->webDescription = $c['DescriptionWeb'];
				$r->logo = $c['Logo'];
				$rec = Sys::$Modules["Axenergie"]->callData("Categorie/$r->id/Categorie",false,0,1,'','','COUNT(*)');
				$r->rangeCount = $rec[0]['COUNT(*)'];
				$rec = Sys::$Modules["Axenergie"]->callData("Categorie/$r->id/Produit",false,0,1,'','','COUNT(*)');
				$r->productCount = $rec[0]['COUNT(*)'];
				$rngs[] = $r;
			}
		}
		$rng->ranges = $rngs;
		$rng->products = $prds;
		return array('getRangeReturn'=>$rng);
	}


	function getProduct($id) {
		$r = $this->loadProduct($id);
		return array('getProductReturn'=>$r);
	}

	private function loadProduct($id) {
		$r = new Product();
		if($id > 0) {
			$rec = Sys::$Modules["Axenergie"]->callData("Produit/$id",false,0,1);
			if(is_array($rec) && count($rec)) {
				$p = genericClass::createInstance("Axenergie",$rec[0]);
				$r->id = $p->Id;
				$r->title = $p->Nom;
				$r->description = $p->Description;
				$r->productPicture = $p->ImageProduit;
				$r->inSitusPicture = $p->ImageAmbiance;
				$r->webDescription = $p->DescriptionWeb;
				$r->startingPrice = $p->PrixHT;
				$r->vatCode = $p->CodeTVA;
				$brd = $p->getParents('Marque');
				if(is_array($brd) && count($brd)) {
					$r->brandName = $brd[0]->Nom;
					$r->brandLogo = $brd[0]->Logo;
				}
				$r->descriptions = array();
				$des = Sys::$Modules["Axenergie"]->callData("Produit/".$id.'/ProDescription');
				foreach($des as $ds) {
					$d = new Description();
					$d->id = $ds['Id'];
					$d->order = $ds['Ordre'];
					$d->label = $ds['Libelle'];
					$d->text = $ds['Texte'];
					$r->descriptions[] = $d;
				}
				
				$r->models = array();
				$mod = Sys::$Modules["Axenergie"]->callData("Produit/".$id."/Modele");
				foreach($mod as $m) $r->models[] = $this->loadModel($m['Id']);
			}
		}
		return $r;
	}	

	function getModel($id) {
		$r = $this->loadModel($id);
		return array('getModelReturn'=>$r);
	}
	
	private function loadModel($id) {
		$r = new Model();
		if($id > 0) {
			$rec = Sys::$Modules["Axenergie"]->callData("Modele/$id",false,0,1);
			if(is_array($rec) && count($rec)) {
				$p = genericClass::createInstance("Axenergie",$rec[0]);
				$r->id = $p->Id;
				$r->title = $p->Nom;
				$r->description = $p->Description;
				$r->productPicture = $p->ImageProduit;
				$r->inSitusPicture = $p->ImageAmbiance;
				$r->cataloguePicture = $p->ImageCatalogue;
				$r->pictureOnly = $p->ImageSeule;
				$r->service = $p->Service;
				$r->webDescription = $p->DescriptionWeb;
				$r->price = $p->PrixHT;
				$r->reference = $p->Reference;
				$r->height = $p->Hauteur;
				$r->width = $p->Largeur;
				$r->depth = $p->Profondeur;
				$r->power = $p->Puissance;
				$r->efficiency = $p->Rendement;
				$r->output = $p->Debit;
				$r->descriptions = array();
				$des = Sys::$Modules["Axenergie"]->callData("Modele/".$id.'/ModDescription');
				foreach($des as $ds) {
					$d = new Description();
					$d->id = $ds['Id'];
					$d->order = $ds['Ordre'];
					$d->label = $ds['Libelle'];
					$d->text = $ds['Texte'];
					$r->descriptions[] = $d;
				}
			}
		}
		return $r;
	}
}




class ReturnRange {
	var $id;
	var $order;
	var $type;
	var $title;
	var $subTitle;
	var $colour;
	var $headerPicture;
	var $webHeaderPicture;
	var $webTitle;
	var $webDescription;
	var $logo;
	var $ranges;
	var $products;
}

class Range {
	var $id;
	var $order;
	var $type;
	var $title;
	var $subTitle;
	var $colour;
	var $headerPicture;
	var $webHeaderPicture;
	var $webTitle;
	var $webDescription;
	var $logo;
	var $rangeCount;
	var $productCount;
}



class Product {
	var $id;
	var $title;
	var $description;
	var $productPicture;
	var $inSitusPicture;
	var $webDescription;
	var $startingPrice;
	var $vatCode;
	var $brandName;
	var $brandLogo;
	var $descriptions;
	var $models;
}

class Model {
	var $id;
	var $title;
	var $description;
	var $productPicture;
	var $inSitusPicture;
	var $cataloguePicture;
	var $pictureOnly;
	var $service;
	var $webDescription;
	var $price;
	var $vatCode;
	var $reference;
	var $height;
	var $width;
	var $depth;
	var $power;
	var $efficiency;
	var $output;
	var $descriptions;
}

class Description {
	var $id;
	var $order;
	var $label;
	var $text;
}


