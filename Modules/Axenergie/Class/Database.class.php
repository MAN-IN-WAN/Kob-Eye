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
			$r = Sys::$Modules["Axenergie"]->callData("Database/".$this->Id);
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
		$r = Sys::$Modules["Axenergie"]->callData("Categorie/Affiche=0&Prive=1");
		$status = Array();
		if (is_array($r)&&sizeof($r))foreach ($r as $ra){
			SubRange::add($this,genericClass::createInstance("Axenergie",$ra),new stdClass(),true,true);
		}
	}
	
	function addSub($args) {
		if ($this->Id=="")
			return "DATABASE NON INITLIALISEE";
		if (!is_object($args)||$args->objectClass==""||$args->id=="")
			return "ERREUR DE REQUETE... INFORMATIONS MANQUANTES";
		//Recuperation de l'objet à cloner
		$O = Sys::$Modules["Axenergie"]->callData($args->objectClass."/".$args->id);
		$target = genericClass::createInstance("Axenergie",$O[0]);
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
		$status[] = array("edit",1,$args->id,$args->module,$args->objectClass,null,null,null,null,null,123);
		$GLOBALS["Systeme"]->Log->log("xxxxxxADD SUB DATABASE CALLxxxxxxx");
		return WebService::WSStatusMulti($status);
	}

	
	
	function removeSub($args) {
		if ($this->Id=="")
			return "DATABASE NON INITLIALISEE";
		if (!is_object($args)||$args->objectClass==""||$args->id=="")
			return "ERREUR DE REQUETE... INFORMATIONS MANQUANTES";
		//Recuperation de l'objet d'origine
		$O = Sys::$Modules["Axenergie"]->callData($args->objectClass."/".$args->id);
		$target = genericClass::createInstance("Axenergie",$O[0]);
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
		$status[] = array("edit",1,$args->id,$args->module,$args->objectClass,null,null,null,null,null,123);
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
		$O = Sys::$Modules["Axenergie"]->callData($args->objectClass."/".$args->id);
		$target = genericClass::createInstance("Axenergie",$O[0]);
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
		$db = Sys::$Modules["Axenergie"]->callData('Database/Pays='.$grp->Nom);
		return genericClass::createInstance('Axenergie',$db[0]);
	}

		/* ___________________________________________________________________________________________
	 *																						REMOTE
	 */
	function getSubRange($g){
		$o = Sys::$Modules["Axenergie"]->callData("Database/".$this->Id."/SubRange/Nom=".$g);
		$o = Sys::$Modules["Axenergie"]->callData("Database/".$this->Id."/SubRange/".$o[0]["Id"]."/SubRange");
		//$GLOBALS["Systeme"]->Log->log("xxxxxxGET SUB RANGE Database/".$this->Id."/SubRange/Nom=$g/SubRange  xxxxxxx",$o);
		//return WebService::WSStatus('method',1,'','','','','',array(),$o);
		return WebService::WSData("Nom", 0, sizeof($o), sizeof($o), "Database/".$this->Id."/SubRange/".$o[0]["Id"]."/SubRange", "SubRange", "", "Axenergie", "SubRange", $o);
	}
	function getSubProduct($g,$g2){
		$o = Sys::$Modules["Axenergie"]->callData("Database/".$this->Id."/SubRange/Nom=".$g);
		$o2 = Sys::$Modules["Axenergie"]->callData("Database/".$this->Id."/SubRange/".$o[0]["Id"]."/SubRange/".$g2."/SubProduct");
		$o2 = array_merge($o2,Sys::$Modules["Axenergie"]->callData("Database/".$this->Id."/SubRange/".$o[0]["Id"]."/SubRange/".$g2."/*/SubProduct"));
		$out=Array();
		return WebService::WSData("Nom", 0, sizeof($o), sizeof($o), "Database/".$this->Id."/SubRange/".$o[0]["Id"]."/SubProduct", "SubProduct", "", "Axenergie", "SubProduct", $out);
	}

	function getImage($titre,$valid,$test=0) {
		$this->Titre = $titre;
		$this->Validite = $valid;
		parent::Save();
		$id = $this->Id;
		
		// controle si services uniquement
		$service = 'Serv';
		$prd = Sys::$Modules["Axenergie"]->callData("Database/$id/SubRange");
		foreach($prd as $p) {
			if($p['Type'] != 3) {
				$service = '';
				break;
			}
		}
		// chargement des photos
		$fam = Sys::getData('Axenergie', "Database/$id/SubRange/Type=4");
		if(count($fam)) {
			$pid = $fam[0]->Id;
			$pho = Sys::getData('Axenergie', "SubRange/$pid/SubProduct/*/SubModel");
			$pcnt = count($pho);
			$pnum = 0;
		}
		
		$rec = Sys::$Modules["Axenergie"]->callData("Parametre/Code=PastillePrix",false,0,1);
		if(! $rec[0]['Image']) $err .= '\nImage PastillePrix non trouvée';
		$pastille = $rec[0]['Image'];
		$rec = Sys::$Modules["Axenergie"]->callData("Parametre/Code=TexteBasPage",false,0,1);
		$tbp = $rec[0]['Texte'];
		$c = array();
		//$o = array('unit'=>'mm','resolution'=>300,'width'=>196,'height'=>283,'bgcolor'=>'0xffffff','compression'=>100);
		$o = array('unit'=>'mm','resolution'=>300,'width'=>210,'height'=>297,'leftMargin'=>7,'topMargin'=>7,'bgcolor'=>'0xffffff','compression'=>100,'test'=>$test);
		$c[] = array('type'=>'fontname','name'=>'DINRegular','file'=>'Skins/Axenergie/assets/DINRegular.swf');
		$c[] = array('type'=>'fontname','name'=>'DINBlack','file'=>'Skins/Axenergie/assets/DINBlack.swf');
		$c[] = array('type'=>'fontname','name'=>'DINBold','file'=>'Skins/Axenergie/assets/DINBold.swf');
		$c[] = array('type'=>'fontname','name'=>'EuroSymbol','file'=>'Skins/Axenergie/assets/EuroSymbol.swf');
		// 1ere de couverture
		$page = 1;
		$rec = Sys::$Modules["Axenergie"]->callData("Parametre/Code=".$service."SommaireCouleur",false,0,1);
		$color = '0x'.substr($rec[0]['Texte'],1);
		$rec = Sys::$Modules["Axenergie"]->callData("Parametre/Code=".$service."PremCouv",false,0,1);
		if(! $rec[0]['Image']) $err .= "\nPremière de couverture"; 
		else $c[] = array('type'=>'pict','x'=>0,'y'=>0,'width'=>196,'height'=>282.9,'pict'=>$rec[0]['Image']);
		//$c[] = array('type'=>'rect','x'=>0,'y'=>0,'width'=>196,'height'=>15,'color'=>$color,'alpha'=>1);
		$c[] = array('type'=>'font','name'=>'emDINRegular','size'=>32);
		$c[] = array('type'=>'text','x'=>3,'y'=>1,'width'=>190,'height'=>20,'noWrap'=>1,'align'=>'left','color'=>'0xffffff','text'=>$titre);
		$c[] = array('type'=>'font','size'=>12);
		$c[] = array('type'=>'text','x'=>3,'y'=>16,'width'=>190,'align'=>'left','color'=>$color,'text'=>$valid);
		$usr = Sys::$User;
		$adh = $usr->getChildren('Adherent');
		$usr = $adh[0];
		if($usr->Logo) $c[] = array('type'=>'pict','x'=>100,'y'=>40,'width'=>96,'height'=>35,'center'=>1,'pict'=>$usr->Logo);
		//if($usr->Logo) $c[] = array('type'=>'pict','x'=>100,'y'=>20,'width'=>96,'height'=>35,'center'=>1,'pict'=>$usr->Logo);
		//$c[] = array('type'=>'font','name'=>'emDINBold','size'=>14);
		//$c[] = array('type'=>'text','x'=>100,'y'=>58,'width'=>96,'align'=>'center','color'=>'0x000000','text'=>$usr->Enseigne);
		//$c[] = array('type'=>'text','y'=>'t','width'=>96,'align'=>'center','text'=>$usr->Adresse);
		//$c[] = array('type'=>'text','y'=>'t','width'=>96,'align'=>'center','text'=>$usr->CodPos." ".$usr->Ville);
		//$c[] = array('type'=>'text','y'=>'t','width'=>96,'align'=>'center','text'=>"Tél : ".$usr->Tel);
		//$c[] = array('type'=>'font','name'=>'emDINBlack');
		//$c[] = array('type'=>'text','y'=>'t','width'=>96,'align'=>'center','text'=>$usr->Mail);
		//$c[] = array('type'=>'text','y'=>'t','width'=>96,'align'=>'center','text'=>$usr->SiteWeb);
		$c[] = array('type'=>'save','name'=>'Catalogue'.$id.'_'.$page);

		// sommaire
		$page = 2;
		$rec = Sys::$Modules["Axenergie"]->callData("Parametre/Code=".$service."Sommaire",false,0,1);
		if(! $rec[0]['Image']) $err .= "\nSommaire"; 
		$c[] = array('type'=>'pict','x'=>0,'y'=>0,'width'=>196,'height'=>273.7,'pict'=>$rec[0]['Image']);
		$pg = 3;
		$c[] = array('type'=>'xy','y'=>50);
		$fam = Sys::$Modules["Axenergie"]->callData("Database/$id/SubRange/Type!=4");
		foreach($fam as $fm) {
			$id = $fm['Id'];
			if($fm['Type'] == 1) $prd = Sys::$Modules["Axenergie"]->callData("SubRange/$id/SubRange/*/SubProduct/*/SubModel/Promo=0&ImageSeule=0");
			elseif($fm['Type'] == 2) $prd = Sys::$Modules["Axenergie"]->callData("Database/".$this->Id."/SubRange/*/SubProduct/*/SubModel/Promo=1");
			elseif($fm['Type'] == 3) $prd = Sys::$Modules["Axenergie"]->callData("SubRange/$id/SubProduct/*/SubModel");
			else $prd = null;
			if(! is_array($prd) || ! count($prd)) continue;

			// 
			$c[] = array('type'=>'font','name'=>'emDINBold','size'=>11);
			$c[] = array('type'=>'text','x'=>8,'color'=>$color,'text'=>$fm['Nom']);
			switch($fm['Type']) {
			case 3:
				foreach($prd as $pr) {
					$c[] = array('type'=>'font','name'=>'emDINRegular','size'=>11);
					$c[] = array('type'=>'text','x'=>8,'y'=>'t0.5','color'=>'0x000000','text'=>'- '.$pr['Nom']);
					$c[] = array('type'=>'font','name'=>'emDINBold','size'=>11);
					$c[] = array('type'=>'text','x'=>72,'color'=>$color,'text'=>$pg);
					$pg++;
				}
				break;
			case 2:
				$c[] = array('type'=>'font','name'=>'emDINBold','size'=>11);
				$c[] = array('type'=>'text','x'=>72,'text'=>$pg);
				$pg += ceil(count($prd)/4);
				break;
			case 1:
				$cat = Sys::$Modules["Axenergie"]->callData("SubRange/$id/SubRange");
				foreach($cat as $ct) {
					$id = $ct['Id'];
					$prd = Sys::$Modules["Axenergie"]->callData("SubRange/$id/SubProduct/*/SubModel/Promo=0&ImageSeule=0");
					if(count($prd)) {
						$c[] = array('type'=>'font','name'=>'emDINRegular','size'=>11);
						$c[] = array('type'=>'text','x'=>8,'y'=>'t0.5','color'=>'0x000000','text'=>'- '.$ct['Nom']);
						$c[] = array('type'=>'font','name'=>'emDINBold','size'=>11);
						$c[] = array('type'=>'text','x'=>72,'color'=>$color,'text'=>$pg);
						$pg += ceil(count($prd)/4);
					}
				}
				break;
			}
			$c[] = array('type'=>'xy','y'=>'t0.5');
		}
		$c[] = array('type'=>'font','name'=>'emDINBold','size'=>12);
		$c[] = array('type'=>'text','x'=>185,'y'=>275,'width'=>10,'align'=>'right','color'=>$color,'text'=>$page);
		$c[] = array('type'=>'save','name'=>'Catalogue'.$this->Id.'_'.$page);
		// detail
		foreach($fam as $fm) {
			$col = '0x'.substr($fm['Couleur'],1);
			$id = $fm['Id'];
			switch($fm['Type']) {
			case 1:
				$cat = Sys::$Modules["Axenergie"]->callData("SubRange/$id/SubRange");
				foreach($cat as $ct) {
					$id = $ct['Id'];
					$prd = Sys::$Modules["Axenergie"]->callData("SubRange/$id/SubProduct/*/SubModel/Promo=0&ImageSeule=0",false,0,0,'ASC','PrixAdherent');
					if(! count($prd)) continue;
					$count = 0;
					foreach($prd as $pr) {
						//$pd = genericClass::createInstance("Axenergie",$pr);
						if($count == 0) {
							$page++;
							if(! $ct['ImageEntete']) $err .= "\nCatégorie : ".$ct['Nom']; 
							$c[] = array('type'=>'pict','x'=>0,'y'=>0,'width'=>196,'height'=>89.4,'pict'=>$ct['ImageEntete']);
							$c[] = array('type'=>'font','name'=>'emDINRegular','size'=>5);
							$c[] = array('type'=>'text','x'=>2,'y'=>274,'width'=>183,'height'=>10,'align'=>'justify','color'=>'0x000000','text'=>$tbp);
							$c[] = array('type'=>'font','name'=>'emDINBold','size'=>12);
							$c[] = array('type'=>'text','x'=>185,'y'=>275,'width'=>10,'align'=>'right','color'=>$col,'text'=>$page);
						}
						$x = ($count % 2) * 101;
						$y = (floor($count / 2)) * 113 + 50;
						if(! $pr['ImageCatalogue']) $err .= "\nModéle (Catalogue) : ".$ct['Nom'].' / '.$pr['Nom']; 
						else {
							$c[] = array('type'=>'pict','x'=>$x,'y'=>$y,'width'=>95,'height'=>110,'pict'=>$pr['ImageCatalogue'].'?'.$pr['Version']);
							$pxht = $pr['PrixAdherent'];
							if($pxht) {
								$ttc = round($pxht*(1+$pr['TauxTVA']/100));
								$c[] = array('type'=>'pict','x'=>$x+69,'y'=>$y+74,'width'=>23,'height'=>23,'pict'=>$pastille);

								$c[] = array('type'=>'font','name'=>'emDINBold','size'=>10,'bold'=>0);
								$c[] = array('type'=>'text','x'=>$x+77.5,'y'=>$y+75.5,'color'=>'0xffffff','text'=>'TTC');

								$c[] = array('type'=>'font','name'=>'emDINBold','size'=>19.8,'bold'=>0);
								$c[] = array('type'=>'text','x'=>$x+70,'y'=>$y+79,'color'=>'0x000000','text'=>"$ttc ");
								$c[] = array('type'=>'font','name'=>'emEuroSymbol','size'=>18,'bold'=>1);
								$c[] = array('type'=>'text','x'=>'t','y'=>'+1','text'=>'€');

								$tmp = 'TVA '.$pr['TauxTVA'].'%';
								if($pr['CodeTVA'] == 1) $tmp .= '*';
								$c[] = array('type'=>'font','name'=>'emDINBold','size'=>6,'bold'=>0);
								$c[] = array('type'=>'text','x'=>$x+72,'y'=>$y+87.5,'color'=>'0xffffff','text'=>$tmp);

//								$c[] = array('type'=>'font','name'=>'emDINBold','size'=>8,'bold'=>0);
//								$c[] = array('type'=>'text','x'=>$x+78,'y'=>$y+86.5,'color'=>'0xffffff','text'=>'TTC*');
							}
						}
						if(++$count == 4) {
							$count = 0;
							$c[] = array('type'=>'save','name'=>'Catalogue'.$this->Id.'_'.$page);
						}
					}
					if($count && $count < 4 && $pcnt) {
						while($count < 4) {
							$pr = $pho[$pnum];
							if(++$pnum == $pcnt) $pnum = 0; 
							$x = ($count % 2) * 101;
							$y = (floor($count / 2)) * 113 + 50;
							$c[] = array('type'=>'pict','x'=>$x,'y'=>$y,'width'=>95,'height'=>110,'pict'=>$pr->ImageProduit.'?'.$pr->Version);
							$count++;
						}
					}
					if($count) $c[] = array('type'=>'save','name'=>'Catalogue'.$this->Id.'_'.$page);
				}
				break;
			case 2:
				$prd = Sys::$Modules["Axenergie"]->callData("Database/".$this->Id."/SubRange/*/SubProduct/*/SubModel/Promo=1",false,0,0,'ASC','PrixPromo');
				if(! count($prd)) continue;
				$count = 0;
				foreach($prd as $pr) {
					//$pd = genericClass::createInstance("Axenergie",$pr);
					if($count == 0) {
						$page++;
						if(! $fm['ImageEntete']) $err .= "\nCatégorie : ".$fm['Nom']; 
						$c[] = array('type'=>'pict','x'=>0,'y'=>0,'width'=>196,'height'=>89.4,'pict'=>$fm['ImageEntete']);
						if($fm['Type'] != 3) {
							$c[] = array('type'=>'font','name'=>'emDINRegular','size'=>5);
							$c[] = array('type'=>'text','x'=>2,'y'=>274,'width'=>183,'height'=>10,'align'=>'justify','color'=>'0x000000','text'=>$tbp);
						}
						$c[] = array('type'=>'font','name'=>'emDINBold','size'=>12);
						$c[] = array('type'=>'text','x'=>185,'y'=>275,'width'=>10,'align'=>'right','color'=>$col,'text'=>$page);
					}
					$x = ($count % 2) * 101;
					$y = (floor($count / 2)) * 113 + 50;
					if(! $pr['ImagePromo']) $err .= "\nModèle (Promo) : ".$ct['Nom'].' / '.$pr['Nom'];
					else $c[] = array('type'=>'pict','x'=>$x,'y'=>$y,'width'=>95,'height'=>110,'pict'=>$pr['ImagePromo'].'?'.time());
					if(++$count == 4) {
						$count = 0;
						$c[] = array('type'=>'save','name'=>'Catalogue'.$this->Id.'_'.$page);
					}
				}
				if($count && $count < 4 && $pcnt) {
					while($count < 4) {
						$pr = $pho[$pnum];
						if(++$pnum == $pcnt) $pnum = 0; 
						$x = ($count % 2) * 101;
						$y = (floor($count / 2)) * 113 + 50;
						$c[] = array('type'=>'pict','x'=>$x,'y'=>$y,'width'=>95,'height'=>110,'pict'=>$pr->ImageProduit.'?'.$pr->Version);
						$count++;
					}
				}	
				if($count) $c[] = array('type'=>'save','name'=>'Catalogue'.$this->Id.'_'.$page);
				break;
			case 3:
				$prd = Sys::$Modules["Axenergie"]->callData("SubRange/$id/SubProduct/*/SubModel",false,0,0,'ASC','Nom');
				foreach($prd as $pr) {
					$page++;
					//$pd = genericClass::createInstance("Axenergie",$pr);
					if(! $pr['ImageCatalogue']) $err .= "\nProduit : ".$ct['Nom'].' / '.$pr['Nom']; 
					$c[] = array('type'=>'pict','x'=>0,'y'=>0,'width'=>196,'height'=>275,'pict'=>$pr['ImageCatalogue'].'?'.$pr['Version']);
					$c[] = array('type'=>'font','name'=>'emDINBold','size'=>12);
					$c[] = array('type'=>'text','x'=>185,'y'=>275,'width'=>10,'align'=>'right','color'=>$col,'text'=>$page);
					$c[] = array('type'=>'save','name'=>'Catalogue'.$this->Id.'_'.$page);
				}
			}
		}
		// derniere de couverture
		$page++;
		$rec = Sys::$Modules["Axenergie"]->callData("Parametre/Code=".$service."DerCouv",false,0,1);
		if(! $rec[0]['Image']) $err .= "\nDernière de couverture"; 
		$c[] = array('type'=>'pict','x'=>0,'y'=>0,'width'=>196,'height'=>282.9,'pict'=>$rec[0]['Image']);
		if($usr->Logo) $c[] = array('type'=>'pict','x'=>60,'y'=>120,'width'=>96,'height'=>35,'center'=>1,'pict'=>$usr->Logo);
		$c[] = array('type'=>'font','name'=>'emDINBold','size'=>14);
		$c[] = array('type'=>'text','x'=>60,'y'=>160,'width'=>96,'align'=>'center','color'=>'0x000000','text'=>$usr->Enseigne);
		$c[] = array('type'=>'text','y'=>'t','width'=>96,'align'=>'center','text'=>$usr->Adresse);
		$c[] = array('type'=>'text','y'=>'t','width'=>96,'align'=>'center','text'=>$usr->CodPos." ".$usr->Ville);
		$c[] = array('type'=>'text','y'=>'t','width'=>96,'align'=>'center','text'=>"Tél : ".$usr->Tel);
		$c[] = array('type'=>'font','name'=>'emDINBlack');
		$c[] = array('type'=>'text','y'=>'t','width'=>96,'align'=>'center','text'=>$usr->Mail);
		$c[] = array('type'=>'text','y'=>'t','width'=>96,'align'=>'center','text'=>$usr->SiteWeb);
		$c[] = array('type'=>'save','name'=>'Catalogue'.$this->Id.'_'.$page);
		$c[] = array('type'=>'close');
		//
		
		if($err) {
			$err = 'IMAGES MANQUANTES :'.$err;
			return WebService::WSStatus('method', 0, '', '', '', '', '', array(array('Message'=>$err)), null);
		}
		
		// traitement book
		if(! $test) {
			$adh = Sys::$User->getChilds('Adherent');
			$book = $adh[0]->getParents('Book');
			$pgs = $book[0]->getChilds('Page');
			foreach($pgs as $p) $p->Delete();
			array_map('unlink', glob('Home/'.Sys::$User->Id.'/Catalogue'.$this->Id.'_*.jpg'));
		}
		
		$o['commands'] = $c;
		$res = array('ImageCatalogue'=>$o);
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, array('dataValues'=>$res));
	}

	function CreatePDF() {
		require_once('Class/Lib/pdfb/fpdf_fpdi/fpdf.php');
		$pdf = new FPDF('P','mm','A4');
		$adh = Sys::$User->getChilds('Adherent');
		$book = $adh[0]->getParents('Book');
		//$pgs = $book[0]->getChildren('Page');
		$bid = $book[0]->Id;
		$pgs = Sys::getData('Flipbook','Book/'.$bid.'/Page',0,999,'ASC','Id');
		foreach($pgs as $p) {
			$pdf->AddPage();
			$pdf->Image($p->Image,0,0, 210, 297);
		}
		$pdf->Close();
		$file = 'Home/'.Sys::$User->Id.'/Catalogue.pdf';
		$pdf->Output($file, 'F');
		@chmod($file, 0777);
		$msg = "Lien vers le catalogue PDF :\nhttp://".$_SERVER["SERVER_NAME"].'/'.$file;
//		$msg .= "\n\nLien vers le catalogue en ligne :\nhttp://".$_SERVER["SERVER_NAME"]."/Flipbook/Book/$bid.htms\n\n";
		$msg .= "\n\nLien vers le catalogue en ligne :\nhttp://flipbook.axenergie.eu/Flipbook/Book/$bid.htms\n\n";
		return WebService::WSStatus('method', 0, '', '', '', '', '', array(array('Message'=>$msg)), null);
	}
	
	function openBook() {
		$adh = Sys::$User->getChilds('Adherent');
		$book = $adh[0]->getParents('Book');
		$bid = $book[0]->Id;
		$res = array('printFiles'=>array("http://".$_SERVER["SERVER_NAME"]."/Flipbook/Book/$bid.htms?".microtime(true)));
		return WebService::WSStatus('method', 1, '', '', '', '', '', null, $res);
	}
	
}
