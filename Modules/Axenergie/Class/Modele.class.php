<?php
class Modele extends genericClass {
	function Save(){
		//mise à jour de la version
		if ($this->Id>0){
			$this->Version=(int)$this->Version+1;
		}else $this->Version=1;
		if($this->ImageSeule || $this->Service) $this->ImageCatalogue = $this->ImageProduit;
		//Sauvegarde
		genericClass::Save();
		// description
		$key = 'ModDescription.ModeleDescr';
		$this->saveDescription($this->{$key});
		//si la categorie est automatique alors on propage
		$prod = $this->getParents("Produit");
		$cat = $prod[0]->getParents("Categorie");
		//oui c'est inversé !! je sais mais il y eu trop de changement
		if ($cat[0]->Prive==1&&$cat[0]->Affiche==0){
			//propagation dans les bases autoadministrées
			$status = Array();
			//ajout de la categorie sur l'ensemble des dbs auto administratées
			$dbs = Sys::$Modules["Axenergie"]->callData("Database/Auto=1");
			foreach ($dbs as $db) {
				$db = genericClass::createInstance("Axenergie",$db);
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

	private function saveDescription($descr) {
		if(! $descr) return;
		$ord = 0;
		$old = $this->getChilds('ModDescription');
		foreach($descr as $desc) {
			$id = $desc->Id;
			$d = genericClass::createInstance('Axenergie','ModDescription');
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
		$status = Array();
		//si la categorie est automatique alors on propage
		$prod = $this->getParents("Produit");
		$cat = $prod[0]->getParents("Categorie");
		//oui c'est inversé !! je sais mais il y eu trop de changement
		if ($cat[0]->Prive==1&&$cat[0]->Affiche==0){
			//suppression de la categorie dans les bases auto administratée
			$dbs = Sys::$Modules["Axenergie"]->callData("Database/Auto=1");
			foreach ($dbs as $db) {
				$db = genericClass::createInstance("Axenergie",$db);
				//suppression du modele
				$status = array_merge($status,SubModel::remove($db,$this));
			}
		}
		$des = $this->getChilds('ModDescription');
		foreach($des as $d) $d->Delete();
		$status[] = Array("delete",parent::Delete(),$this->Id,$this->Module,$this->ObjectType,null,null,null,null,null);
		//genericClass::Delete();
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


	function getImage($curr=null, $prod=null) {
		if(! $curr) {
			$promo = false;
			$prod = $this;
			$curr = $this;
			$path = 'Home/Axenergie/Images';
			$url = 'Axenergie/Modele/'.$this->Id;
			$field = 'ImageCatalogue';
		}
		else {
			$path = 'Home/'.Sys::$User->Id;
			$promo = true;
			$url = 'Axenergie/SubRange/'.$curr->Id;
			$field = 'ImagePromo';
		}
		
		$c = array();
		$o = $this->imageTete('img'.$prod->Id, $path, $url, $field, $c);
		$err = $this->imageDetail($curr, $prod, $c);
	
		if($err) {
			$err = 'ANOMALIE :'.$err;
			return WebService::WSStatus('method', 0, '', '', '', '', '', array(array('Message'=>$err)), null);
		}

		$o['commands'] = $c;
		$img = $promo ? 'ImagePromo' : 'ImageCatalogue';
		$res = array($img=>$o);
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, array('dataValues'=>$res));
	}
	
	private function imageTete($name, $path, $url, $field, &$c) {
		$o = array('unit'=>'mm','resolution'=>300,'width'=>95,'height'=>110,'bgcolor'=>'0xffffff','compression'=>100,
			'name'=>$name,'path'=>$path,'override'=>1,'updateURL'=>$url,'updateField'=>$field); 
		$c[] = array('type'=>'fontname','name'=>'DINRegular','file'=>'Skins/Axenergie/assets/DINRegular.swf');
		$c[] = array('type'=>'fontname','name'=>'DINBlack','file'=>'Skins/Axenergie/assets/DINBlack.swf');
		$c[] = array('type'=>'fontname','name'=>'DINBold','file'=>'Skins/Axenergie/assets/DINBold.swf');
		$c[] = array('type'=>'fontname','name'=>'EuroSymbol','file'=>'Skins/Axenergie/assets/EuroSymbol.swf');
		return $o;
	}
	
	private function imageDescr($label, $text, $col, &$c) {
		$c[] = array('type'=>'xy','x'=>3,'y'=>'t3');
		$c[] = array('type'=>'circle','radius'=>0.5,'color'=>$col);
		$c[] = array('type'=>'font','name'=>'emDINBold','size'=>9,'bold'=>0);
		$c[] = array('type'=>'text','x'=>'+3','y'=>'-2.5','color'=>'0x000000','text'=>$label);
		$c[] = array('type'=>'font','name'=>'emDINRegular','size'=>9,'bold'=>0);
		$c[] = array('type'=>'text','x'=>'t','text'=>$text);
	}
	
	private function imageDescrPromo($label, $text, $col, &$c) {
		$c[] = array('type'=>'xy','x'=>3,'y'=>'t3');
		$c[] = array('type'=>'circle','radius'=>0.5,'color'=>$col);
		$c[] = array('type'=>'font','name'=>'emDINBold','size'=>9,'bold'=>0);
		$c[] = array('type'=>'text','x'=>6,'y'=>'-2.5','color'=>'0x000000','text'=>$label);
		$c[] = array('type'=>'font','name'=>'emDINRegular','size'=>9);
		$c[] = array('type'=>'iftext','x'=>'t','text'=>$text,'oper'=>'maxx','value'=>48,'commands'=>array(
			array('type'=>'text','x'=>6,'y'=>'t','width'=>41,'text'=>$text),
			array('type'=>'text','text'=>$text)
		));
	}

	private function imageDetail($curr, $prod, &$c) {
		$err = '';
		$rec = $prod->getParents('Produit');
		if(! count($rec)) $err .= '\nProduit non spécifiée';
		else {
			$ppr = $rec[0];
			$cat = $ppr->getParents('Categorie');
			if(! count($cat)) $err .= '\nCatégorie non spécifiée';
			else {
				$cat1 = $cat[0];
				if($prod->Service) $col = '0x'.substr($cat1->Couleur,1);
				else {
					$cat = $cat1->getParents('Categorie');
					if(! count($cat)) $err .= '\nSuper catégorie non spécifiée';
					$cat = $cat[0];
					$col = '0x'.substr($cat->Couleur,1);
				}
			}
		}
		$mar = $ppr->getParents('Marque');
		$mar = $mar[0];
		$des = Sys::getData('Axenergie','Modele/'.$prod->Id.'/ModDescription',0,0,'ASC','Ordre');
		$tva = Sys::getData('Axenergie','CodeTVA/'.$ppr->CodeTVA,0,1);
		$ttva = $tva[0]->Taux;
		$pxht = ($curr != $prod && $curr->PrixAdherent) ? $curr->PrixAdherent : $curr->PrixHT;
		$ttc = round($pxht*(1+$ttva/100));

		$c[] = array('type'=>'rect','x'=>0,'y'=>0,'width'=>95,'height'=>110,'color'=>$col,'alpha'=>1);
		$c[] = array('type'=>'rect','x'=>0.5,'y'=>10,'width'=>94,'height'=>99.5,'color'=>'0xffffff');
		if($prod->Service) {
			$c[] = array('type'=>'font','name'=>'emDINBlack','size'=>14,'bold'=>0,'italic'=>0);
			$c[] = array('type'=>'text','x'=>3,'y'=>1.5,'color'=>'0xffffff','text'=>$prod->Nom);
			$pic = $prod->ImageProduit ? $prod->ImageProduit : $ppr->ImageProduit;
			if(! $pic) $err .= "\nImage produit manquante";
			else $c[] = array('type'=>'pict','x'=>48,'y'=>3,'width'=>42,'height'=>46,'pict'=>$pic);
		}
		elseif(! $curr->Promo) {
			$c[] = array('type'=>'font','name'=>'emDINBlack','size'=>14,'bold'=>0,'italic'=>0);
			$c[] = array('type'=>'text','x'=>3,'y'=>1.5,'color'=>'0xffffff','text'=>$prod->Nom);
			$pic = $prod->ImageProduit ? $prod->ImageProduit : $ppr->ImageProduit;
			if(! $pic) $err .= "\nImage produit manquante";
			else $c[] = array('type'=>'pict','x'=>3,'y'=>11.5,'width'=>43,'height'=>55,'pict'=>$pic);
			$pic = $prod->ImageAmbiance ? $prod->ImageAmbiance : $ppr->ImageAmbiance;
			if($pic) $c[] = array('type'=>'pict','x'=>50,'y'=>8,'width'=>42,'height'=>46,'pict'=>$pic);
			if($mar->Logo) $c[] = array('type'=>'pict','x'=>50,'y'=>54.5,'width'=>42,'height'=>12,'pict'=>$mar->Logo);
			// description
			$c[] = array('type'=>'font','name'=>'emDINBold','size'=>9,'bold'=>0);
			$c[] = array('type'=>'text','x'=>2.5,'y'=>67,'color'=>'0x000000','text'=>$prod->Description);
			// detail
			$c[] = array('type'=>'xy','x'=>3,'y'=>72,'reset'=>1);
			$desc = 0;
			if($prod->Hauteur ) $dim = 'H '.$prod->Hauteur;
			if($prod->Largeur ) $dim .= ($dim ? '/ ' : '').'L '.$prod->Largeur;
			if($prod->Profondeur ) $dim .= ($dim ? '/ ' : '').'P '.$prod->Profondeur;
			if($dim) {$this->imageDescr('Dimensions (mm) : ', $dim, $col, $c); $desc++;}
			if($prod->Puissance) {$this->imageDescr('Puissance : ', $prod->Puissance.' kW', $col, $c); $desc++;}
			if($prod->Rendement) {$this->imageDescr('Rendement sur PCI à 100% : ', $prod->Rendement.'%', $col, $c); $desc++;}
			if($prod->Debit) {$this->imageDescr('Débit sanitaire : ', $prod->Debit.' l/mn', $col, $c); $desc++;}
			foreach($des as $d) {
				$label = $d->Libelle;
				if($label && $d->Texte) $label .= ' : ';
				if($label || $d->Texte) {
					$this->imageDescr($label, $d->Texte, $col, $c);
					if(++$desc >= 7) break;
				}
			}
			if($prod->Reference) {
				$c[] = array('type'=>'xy','x'=>2.5,'y'=>104);
				$c[] = array('type'=>'font','name'=>'emDINBold','size'=>9);
				$c[] = array('type'=>'text','text'=>'REF FABRICANT : '.$prod->Reference);			
			}
		}
		else {  // promo
			$c[] = array('type'=>'font','name'=>'emDINBlack','size'=>14,'bold'=>0,'italic'=>0);
			if(! $cat->SousTitre) $err .= "\nSous-titre catégorie manquant";
			else $c[] = array('type'=>'text','x'=>3,'y'=>1.5,'color'=>'0xffffff','text'=>$cat->SousTitre);
			if($prod->ImageAmbiance) $pic = $prod->ImageAmbiance;
			elseif($prod->ImageProduit) $pic = $prod->ImageProduit;
			elseif($ppr->ImageAmbiance) $pic = $ppr->ImageAmbiance;
			elseif($ppr->ImageProduit) $pic = $ppr->ImageProduit;
			if($pic) $c[] = array('type'=>'pict','x'=>52,'y'=>3,'width'=>42,'height'=>46,'pict'=>$pic);
//			else $err .= "\nAucune image produit";
			if($mar->Logo) $c[] = array('type'=>'pict','x'=>52,'y'=>50,'width'=>42,'height'=>12,'pict'=>$mar->Logo);
			if($cat1->Logo) $c[] = array('type'=>'pict','x'=>40,'y'=>1.5,'width'=>17,'height'=>17,'pict'=>$cat1->Logo);
			// prix
			$rec = Sys::$Modules["Axenergie"]->callData("Parametre/Code=PastillePromo",false,0,1);
			if(! $rec[0]['Image']) $err .= '\nImage PastillePromo non trouvée';
			$c[] = array('type'=>'pict','x'=>54,'y'=>65,'width'=>33,'height'=>33,'pict'=>$rec[0]['Image']);
			$promo = round($curr->PrixPromo*(1+$ttva/100));
			$c[] = array('type'=>'font','name'=>'emDINBold','size'=>12,'bold'=>0);
			$c[] = array('type'=>'text','x'=>66,'y'=>69,'color'=>'0xffffff','text'=>'TTC');
			$c[] = array('type'=>'font','name'=>'emDINBold','size'=>29,'bold'=>0);
			$c[] = array('type'=>'text','x'=>55.5,'y'=>72.5,'color'=>'0xffff00','text'=>"$promo ");
			$c[] = array('type'=>'font','name'=>'emEuroSymbol','size'=>27,'bold'=>1);
			$c[] = array('type'=>'text','x'=>'t','y'=>'+2','text'=>'€');
//			$c[] = array('type'=>'font','name'=>'emDINBold','size'=>12,'bold'=>0);
//			$c[] = array('type'=>'text','x'=>66,'y'=>'t-2','color'=>'0xffffff','text'=>'TTC*');
			$c[] = array('type'=>'font','name'=>'emDINBold','size'=>9,'bold'=>0);
			$tmp = "TVA $ttva%";
			if($prod->CodeTVA == 1) $tmp .= '*';
			$c[] = array('type'=>'text','x'=>59,'y'=>'t','color'=>'0xffffff','text'=>$tmp);

			//if($prod->CodeTVA == 1)
			$c[] = array('type'=>'font','name'=>'emDINRegular','size'=>16);
			$c[] = array('type'=>'text','x'=>50,'y'=>100,'color'=>'0x000000','text'=>'au lieu de ');
			$c[] = array('type'=>'font','name'=>'emDINBold');
			$c[] = array('type'=>'text','x'=>'t','text'=>"$ttc ");
			$c[] = array('type'=>'font','name'=>'emEuroSymbol','size'=>14,'bold'=>1);
			$c[] = array('type'=>'text','x'=>'t','y'=>'+1','text'=>'€');
			// texte
			$c[] = array('type'=>'font','name'=>'emDINBold','size'=>14,'bold'=>0);
			if(! $cat1->SousTitre) $err .= "\nSous-titre sous-catégorie manquant";
			else $c[] = array('type'=>'text','x'=>2.5,'y'=>10.5,'width'=>42,'height'=>13,'align'=>'left','leading'=>'-1','color'=>'0x000000','text'=>$cat1->SousTitre);
			// description
			$c[] = array('type'=>'font','name'=>'emDINBold','size'=>9,'bold'=>0);
			$c[] = array('type'=>'text','x'=>2.5,'y'=>22.5,'width'=>46.5,'height'=>10,'align'=>'left','color'=>'0x000000','text'=>$prod->Nom);
//			$c[] = array('type'=>'text','x'=>2.5,'y'=>22.5,'width'=>46.5,'height'=>10,'align'=>'left','color'=>'0x000000','text'=>$prod->Description);
			$c[] = array('type'=>'font','name'=>'emDINRegular','size'=>9);
			if($curr->TextePromo) $c[] = array('type'=>'text','x'=>2.5,'y'=>32,'width'=>47,'height'=>20,'align'=>'justify','leading'=>'-0.3','text'=>$prod->Description);
//			if($curr->TextePromo) $c[] = array('type'=>'text','x'=>2.5,'y'=>32,'width'=>47,'height'=>20,'align'=>'justify','leading'=>'-0.3','text'=>$curr->TextePromo);
			// detail
			$c[] = array('type'=>'xy','x'=>3,'y'=>54,'reset'=>1);
			$desc = 0;
			if($prod->Hauteur ) $dim = 'H '.$prod->Hauteur;
			if($prod->Largeur ) $dim .= ($dim ? '/ ' : '').'L '.$prod->Largeur;
			if($prod->Profondeur ) $dim .= ($dim ? '/ ' : '').'P '.$prod->Profondeur;
			if($dim) {$this->imageDescrPromo('Dimensions (mm) : ', $dim, $col, $c); $desc++;}
			if($prod->Puissance) {$this->imageDescrPromo('Puissance : ', $prod->Puissance.' kW', $col, $c); $desc++;}
			if($prod->Rendement) {$this->imageDescrPromo('Rendement sur PCI à 100% : ', $prod->Rendement.'%', $col, $c); $desc++;}
			if($prod->Debit) {$this->imageDescrPromo('Débit sanitaire : ', $prod->Debit.' l/mn', $col, $c); $desc++;}
			foreach($des as $d) {
				$label = $d->Libelle;
				if($label && $d->Texte) $label .= ' : ';
				if($label || $d->Texte) {
					$this->imageDescrPromo($label, $d->Texte, $col, $c);
					if(++$desc >= 7) break;
				}
			}
			if($prod->Reference) {
				$c[] = array('type'=>'xy','x'=>2.5,'y'=>100);
				$c[] = array('type'=>'font','name'=>'emDINBold','size'=>9);
				$c[] = array('type'=>'text','text'=>'REF FABRICANT :');
				$c[] = array('type'=>'text','y'=>'t','text'=>$prod->Reference);
			}
		}
		return $err;
	}


	function GetImages($ids) {
		$c = array();
		$o = $this->imageTete('', '', '', $c);
		$path = 'Home/Axenergie/Images';
		$url = 'Axenergie/Modele/';
		$err = '';
		
		foreach($ids as $id) {
			$prd = genericClass::createInstance('Axenergie', 'Modele');
			$prd->initFromId($id);
			$err .= $prd->imageDetail($prd, $prd, $c);
			$c[] = array('type'=>'save','name'=>'img'.$this->Id,'name'=>'img'.$id,'path'=>$path,'override'=>1,'updateURL'=>$url.$id,'updateField'=>'ImageCatalogue');
		}
		$c[] = array('type'=>'close');
		if($err) {
			$err = 'ANOMALIE :'.$err;
			return WebService::WSStatus('method', 0, '', '', '', '', '', array(array('Message'=>$err)), null);
		}
		$o['commands'] = $c;
		$res = array('ImageCatalogue'=>$o);
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, array('dataValues'=>$res));
	}
}
?>