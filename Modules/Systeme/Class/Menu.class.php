<?php
class Menu extends genericClass {
	var $Image = null;
	/**
	 * Save
	 * Surcharge de la fonction Save
	 */
	 function Save(){
		if (empty($this->Alias))$this->Alias = $this->Lien;
		//sauvegarde du menu
		parent::Save();
		//------------------------------------------------------
		//génération des pages associées (sitemap + recherche)
		//------------------------------------------------------
		$sites = $this->getSites();
		if (!empty($this->Alias))
			$pages = Array();//$this->getSubPagesFromQuery($this->FullUrl,$this->Alias);
		else $pages = Array();
		//Pour chaque site on ajoute la page correpondante
		foreach ($sites as $s){
		     if ($this->Affiche){
			$s->addPage($this->FullUrl,$this->Alias,$this);
			foreach ($pages as $k=>$p) $s->addPage($k,$p->Module.'/'.$p->ObjectType.'/'.$p->Id,null,$p->FromUrl);
		     }else {
			foreach ($pages as $k=>$p)
			    $s->delPage($k);
		     }
		}
		parent::Save();
		 //enregistrement des metas pour les menus
		 if (isset($this->Affiche)&&$this->Affiche){
			 $this->SaveMenuKeywords();
		 }else{
			 $this->deletePages();
		 }
	 }

	/**
	 * SaveMenuKeywords
	 * Enregistrement des metas pour les menus
	 */
	function SaveMenuKeywords()
	{
		//On recherche les pages
		$tls = $this->getPages();

		//mise à jour des pages
		for ($i = 0; $i < sizeof($tls); $i++) {
			$tls[$i]->Title = $this->Title;
			$tls[$i]->Description = $this->Description;
			$tls[$i]->Keywords = $this->Keywords;
			$tls[$i]->Image = $this->Image;
			foreach ($GLOBALS["Systeme"]->Conf->get("GENERAL::LANGUAGE") as $Cod => $Lang) {
				if (!isset($Lang["DEFAULT"]) || !$Lang["DEFAULT"]) {
					$tls[$i]->{$Cod . "-Title"} = $this->{$Cod . "-Title"};
					$tls[$i]->{$Cod . "-Description"} = $this->{$Cod . "-Description"};
					$tls[$i]->{$Cod . "-Keywords"} = $this->{$Cod . "-Keywords"};
				}
			}
			$url = $tls[$i]->Url;
			$last = explode('/', $url);
			if ($last[sizeof($last) - 1] != $this->Url) {
				//mise à jour url
				$tls[$i]->Url = preg_replace('#' . $last[sizeof($last) - 1] . '$#', $this->Url, $url);
				//TODO requete recursive dans le cas ou on modifie une url contenant d'autres pages.
			}
		}
		for ($i = 0; $i < sizeof($tls); $i++){
			$tls[$i]->Save();
		}
	}

	/**
	  * retournes les sites concernées par le menu courant.
	  * @return Array[Sites]
	  */
	 public function getSites() {
		if (!$this->Id) return;
		//recherche des menus parents
		$mens = $this->getParents('Menu/*/Menu');
		$url = $this->Url;
		//on recherche tous les groupes et on construit l'url
		foreach ($mens as $men){
			$url=$men->Url.'/'.$url;
		}
		$this->FullUrl = $url;
		if (!sizeof($mens))$men = $this;
		//recherche des groupes
		$grps = $men->getParents('Group/*/Group');
		//recherche des sites
		$sites = Array();
		foreach ($grps as $g){
			$usrs = Sys::getData('Systeme','Group/'.$g->Id.'/User',0,10000);
			foreach ($usrs as $u){
				$sites = array_merge($sites,Sys::getData('Systeme','User/'.$u->Id.'/Site'));
			}
		}
		return $sites;
	 }
	 
	 /**
	  * getSubPagesFromQuery
	  * Recherche de manière recursive l'ensemble des pages à partir d'un menu
	  */
	 private function getSubPagesFromQuery($url,$alias) {
		$suffixe='';
		$pages = Array();
		$inf = Info::getInfos($alias);
		//if module ok
		if (!isset($inf["Module"])||!is_object(Sys::$Modules[$inf["Module"]]) || !isset($inf["TypeChild"])) return $pages;
		$obj = Sys::$Modules[$inf["Module"]]->Db->getObjectClass($inf["TypeChild"]);
		//if objest has generateUrl browseable enabled
		if (isset($obj->browseable)&&$obj->browseable||isset($obj->generateUrl)&&$obj->generateUrl){
			$ch = Sys::getData($inf["Module"],$alias);
			foreach ($ch as $o){
			        if (!$o->Display) continue;
				if ($inf['TypeSearch']!="Direct")$suffixe = '/'.$o->Url;
				else $suffixe = '';
				//looking for children
				$pages[$url.$suffixe] = new stdClass();
				$pages[$url.$suffixe]->Module = $o->Module;
				$pages[$url.$suffixe]->ObjectType = $o->ObjectType;
				$pages[$url.$suffixe]->Id = $o->Id;
				$pages[$url.$suffixe]->FromUrl = $url;
				$pages = array_merge($this->getSubPages($o,$url.$suffixe),$pages);
			}
		}
		return $pages;
	 }
	 /**
	  * getSubPages
	  * Recherche de manière recursive l'ensemble des pages à partir d'un menu
	  * @param genericClass objet racine
	  */
	 private function getSubPages($o,$url) {
		$suffixe='/';
		$pages = Array();
		//recuperation des objects enfants
		$cht = $o->getChildTypes();
		//si stopPage, alors on sort
		if ($o->getObjectClass()->stopPage) return $pages;
		foreach ($cht as $c){
			if ($c['browseable']&&!$c['noRecursivity']){
				if ($o->ObjectType!=$c['Titre'])$suffixe = '/'.$c['Titre'].'/';
				else $suffixe='/';
				$oc = $o->getChildren($c['Titre'].'/Display=1');
				foreach ($oc as $ob){
				        $pages[$url.$suffixe.$ob->Url] = new stdClass();
					$pages[$url.$suffixe.$ob->Url]->Module = $ob->Module;
					$pages[$url.$suffixe.$ob->Url]->ObjectType = $ob->ObjectType;
					$pages[$url.$suffixe.$ob->Url]->Id = $ob->Id;
					$pages[$url.$suffixe.$ob->Url]->FromUrl = $url;
					$pages = array_merge($pages,$this->getSubPages($ob,$url.$suffixe.$ob->Url));
				}
			}
		}
		return $pages;
	 }
	 /**
	 * Delete
	 * Override default delete function 
	 * Delete recursively
	 */
	function Delete () {
	 	//suppression des menus
	 	$mpd = $this->getChilds("Menu");
		if (is_array($mpd)&&sizeof($mpd))foreach ($mpd as $mp){
			$mp->Delete();
		}
	 	return parent::Delete();
	}
	/**
	 * getSubMenu
	 * return the children menus of this menu or children by alias from another module
	 * @return Array( genericClass ) 
	 */
	 public function getSubMenus($limit=1000){
	 	$out = Array();
		//local storage ( session cookie )
	 	if (isset($this->Menus)&&is_array($this->Menus)){
	 		$out = array_merge($this->Menus,$out);
	 	}else {
			//children menus
			$chds = $this->getChildren('Menu/Affiche=1');
		 	if (isset($chds)&&is_array($chds)){
		 		$out = array_merge($chds,$out);
		 	}
		}
		//If AutoSubGen try to get children from target module
		if ($this->AutoSubGen&&!empty($this->Alias)){
			$inf = Info::getInfos($this->Alias);
			//if module ok
			if (isset($inf["Module"])&&isset(Sys::$Modules[$inf["Module"]])&&is_object(Sys::$Modules[$inf["Module"]])){
				$obj = Sys::$Modules[$inf["Module"]]->Db->getObjectClass($inf["TypeChild"]);
				//if objest has generateUrl enabled
				if (isset($obj->generateUrl)&&$obj->generateUrl){
					$ch = Sys::$Modules[$inf["Module"]]->callData($this->Alias,false,0,$limit);
					if (is_array($ch)){
						foreach ($ch as $c){
							$o = genericClass::createInstance($inf["Module"],$c);
							if ($inf['TypeSearch']=='Direct'){
								//looking for childs
								$out = array_merge($out,$o->getChildren($o->ObjectType."/Display=1",0,$limit));
								//normalisation des elements
								foreach ($out as $o){
									$o->Titre = $o->getFirstSearchOrder();
								}
							}else{
								$o->Titre = $o->getFirstSearchOrder();
                                if ($o->Display)
								    array_push($out,$o);
							}
						}
					}
				}
			}
		}
		return $out;
	 } 
	/*
	 * getMainMenus
	 * return the main menu list
	 * @return Array ( genericClass ) 
	 */
	 public function getMainMenus (){
	 	$out = Array();
	 	//Recherche parmis les menus systeme
	 	if (is_array($GLOBALS["Systeme"]->Menus)){
	 		foreach ($GLOBALS["Systeme"]->Menus as $m){
	 			if ($m->Affiche && $m->MenuPrincipal){
	 				array_push($out,$m);
	 			}
	 		}
	 	}
		return $out;
	 }
	/*
	 * getTopMenus
	 * return the top menu list
	 * @return Array ( genericClass ) 
	 */
	 public function getTopMenus (){
	 	$out = Array();
	 	//Recherche parmis les menus systeme
	 	if (is_array($GLOBALS["Systeme"]->Menus)){
	 		foreach ($GLOBALS["Systeme"]->Menus as $m){
	 			if ($m->Affiche && $m->MenuHaut){
	 				array_push($out,$m);
	 			}
	 		}
	 	}
		return $out;
	 }
	/*
	 * getBottomMenus
	 * return the bottom menu list
	 * @return Array ( genericClass ) 
	 */
	 public function getBottomMenus (){
	 	$out = Array();
	 	//Recherche parmis les menus systeme
	 	if (is_array($GLOBALS["Systeme"]->Menus)){
	 		foreach ($GLOBALS["Systeme"]->Menus as $m){
	 			if ($m->Affiche && $m->MenuBas){
	 				array_push($out,$m);
	 			}
	 		}
	 	}
		return $out;
	 }
}
