<?php
class BoutiqueCategorie extends genericClass {
	/**
	 * getUrl
	 * Retourne l'url depuis la racine d'un produit donné
	 */
	/*public function getUrl() {
		//recherche des categorie
		if (!Sys::$User -> Admin) {
			$cat = $this -> storproc('Boutique/Categorie/* /Categorie/Categorie/' . $this -> Id);
			//on verifie qu'il n'y pas de menu sur chacune des categories
			$M = false;
			$U = $this->Url;
			//recherche des menus potentiels par catégories parentes
			foreach ($cat as $c) {
				if ('Boutique/Categorie/' . $c["Id"] != $GLOBALS["Systeme"] -> getMenu('Boutique/Categorie/' . $c["Id"])) {
					return $GLOBALS["Systeme"] -> getMenu('Boutique/Categorie/' . $c["Id"]) . (empty($U) ? '' : '/') . $U;
				} else
					$U = $c["Url"] . (empty($U) ? '' : '/') . $U;
			}
			$U = 'Categorie/' . $U;
			//recherche du magasin
			$mag = Magasin::getCurrentMagasin();
			if ('Boutique/Magasin/' . $mag->Id != $GLOBALS["Systeme"] -> getMenu('Boutique/Magasin/' . $mag->Id))
				return $GLOBALS["Systeme"] -> getMenu('Boutique/Magasin/' . $mag->Id) . '/' . $U;
			else
				return 'Boutique/' . $U;
		} else
			return parent::getUrl();
	}
*/
    public function Delete(){
        //suppression des catégories sous jacentes
        $cats = $this->getChildren('Categorie');
        foreach ($cats as $c){
            $c->Delete();
        }
        parent::Delete();
    }

	public function MyRecursiveUrl ($LeParent) {
		$catParent = $this->getParents(Categorie);
		if ($catParent[0]->Id!=0) {
			//klog::l('Url',$this->Url."/".$catParent[0]->RecursiveUrl());
			if ($catParent[0]->Id==$LeParent) {
				return $this->Url;
			} else {
				return $catParent[0]->MyRecursiveUrl($LeParent)  . "/" .$this->Url;
			}

		} 
		return "," ;
	}


	/**
	 * Raccourci vers callData
	 * @return      Résultat de la requete
	 */
	private function storproc($Query, $recurs = '', $Ofst = '', $Limit = '', $OrderType = '', $OrderVar = '', $Selection = '', $GroupBy = '') {
		return Sys::$Modules['Boutique'] -> callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
	}

    /**
     * parser de catégories 1001pharmacies
     */
    public function CategoryParser(){
        include "Class/Utils/htmlParser.class.php";

        //bashcolors
        $bash = new BashColors();
        $bash->getColoredString("Download File ... \r\n","yellow");
        //récupération d'une page contenant le menu total
        $html = file_get_dom('http://www.1001pharmacies.com/sante-c14');

        //Trouver tous les menus
        foreach ($html('div#category_menu_sub') as $div){
            foreach ($div('li') as $li){
                foreach ($li('a') as $a){
                    $error=0;

                    //chaine de recherche
                    $snom = html_entity_decode(trim(strip_tags($a->getPlainText())));
                    $s = Utils::KEAddSlashes(Array( $snom));

                    //extraction du remote_id
                    preg_match_all('#\/([A-z0-9-]+?)-c([0-9]+)#',$a->href,$out);
                    $level = $out[2];

                    //recherche des parents
                    $first = true;
                    foreach ($level as $k=>$l){

                        if ($first){
                            $first=false;
                            $parent = $this;
                        }

                        $r = Sys::getData('Boutique', 'Categorie/'.$parent->Id.'/Categorie/RemoteId=' .$l );
//                        echo '<li>'.'Categorie/'.$parent->Id.'/Categorie/RemoteId=' .$l.' => '.sizeof($r).' ( '.$k.' )</li>';
                        if (!sizeof($r)&&$k<sizeof($level)-1){
                            //erreur il n'y a pas d'arbo (ne devrait pas arriver)
                            $error=2;
                            break;

                        }elseif(!sizeof($r)&&$k==sizeof($level)-1){
                            //recherche des occurrences existantes avec le nom
                            $r2 = Sys::getData('Boutique', 'Categorie/'.$parent->Id.'/Categorie/Nom=' .$s );
                            if (sizeof($r2)>1) {
                                $error = 1;
                            }elseif (!sizeof($r2)){
                                //création
                                $cat  = genericClass::createInstance('Boutique','Categorie');
                                $cat->Nom = $snom;
                                $cat->Actif = 1;
                                $cat->AddParent($parent);
                                $cat->RemoteId = $l;
                                // $cat->Save();
                                $r = Array($cat);
                            }else{
                                //mise à jour du remoteId
                                $r2[0]->RemoteId = $l;
                                //$r2[0]->Save();
                            }
                        }else{
                            $parent = $r[0];
                        }
                    }

                    //affichage
                    switch ($error){
                        case 2:
                            $string = 'parents introuvables  => ' . $s . ' => ' . $a->href."\r\n";
                            echo $bash->getColoredString($string,'red');
                            /*echo '<li';
                            echo ' style="color:red;"> parents introuvables';
                            echo ' => ' . $s . ' => ' . $a->href;
                            echo "</li>";*/
                            break;
                        case 1:
                            $string = 'plusieurs catégories possibles ( ';
                            foreach ($r2 as $r1){
                                $string .=$r1->Id.' / ';
                            }
                            $string .=') => ' . $s . ' => ' . $a->href."\r\n";
                            echo $bash->getColoredString($string,'orange');
                            /*echo '<li';
                            echo ' style="color:orange;"> plusieurs catégories possibles (';
                            foreach ($r as $r1){
                                echo $r1->Id.' / ';
                            }
                            echo ') => ' . $s . ' => ' . $a->href;
                            echo "</li>";*/
                            break;
                        default:
                            $r = $r[0];
                            $string = $r->Id . ' - ' . $r->RemoteId . ' => ' . $s . ' => ' . $a->href."\r\n";
                            echo $bash->getColoredString($string,'green');
                            /*echo '<li style="color:green"';
                            echo '>' . $r->Id . ' - ' . $r->RemoteId . ' => ' . $s . ' => ' . $a->href;
                            echo "</li>";*/
                            break;
                    }

                    //if (sizeof(explode('/',$a->href))>4) die();
                }
            }
        }
    }
    /**
     * parser de produits 1001pharmacies
     */
    public function ProductParser($page=1){
        include_once "Class/Utils/htmlParser.class.php";

        //test page en cours
        if ($page=1){
            //on vérifie la page en cours
            if ($this->RemotePage>1) $page = $this->RemotePage;
        }
        //récupération d'une page contenant le menu total
        $quelquechose = false;
        $url = 'http://www.1001pharmacies.com/parapharmacie';
        if ($page>1){
            $url .= '/'.$page;
        }
        $bash = new BashColors();
        echo $bash->getColoredString("PAGE $page downloading... \r\n","yellow");
        $html = file_get_dom($url);

        //Trouver tous les produits
        foreach ($html('div#psublist') as $div){
            foreach ($div('a.pimg-box-container') as $a){
                $quelquechose = true;
                $error= 0;

                //extraction du remote_id
                preg_match('#-p([0-9]+)$#',$a->href,$out);
                $remoteid = $out[1];

                $r = Sys::getData('Boutique', 'Produit/RemoteId=' .$remoteid );
                if(!sizeof($r)){
                    //téléchargement de la fiche
                    echo $bash->getColoredString("--> Download File $a->href... \r\n","yellow");
                    $prod = file_get_dom($a->href);

                    if (!$prod) continue;

                    //on reverifie après
                    $r = Sys::getData('Boutique', 'Produit/RemoteId=' .$remoteid );
                    if(sizeof($r)){
                        continue;
                    }

                    //titre
                    if (sizeof($prod('h1'))) {
                        $tmptitle = $prod('h1');
                        $title = $tmptitle[0]->getPlainText();
                    }else continue;
                    //marque
                    $marque = $prod('a.purple');
                    $marque = $marque[0]->getPlainText();
                    //description
                    $description = $prod('div.pr_desc');
                    if (sizeof($description)){
                        $description = $description[0]->getPlainText();
                    }else $description='';
                    //image
                    $image = $prod('img#img_product');
                    if (sizeof($image)){
                        $image = $image[0]->src;
                    }else $image='';
                    //volume
                    $volume = $prod('div.auxiliar_info');
                    if (sizeof($volume)&&is_object($volume[0])) {
                        $volume = $volume[0]->getPlainText();
                    }else $volume = '';
                    //prix
                    $prix = $prod('span.product_price');
                    if (sizeof($prix)&&is_object($prix[0])){
                        $prix = $prix[0]->getPlainText();
                        $prix = str_replace(',','.',$prix);
                        $prix = floatval($prix)/1.20;
                    }
                    //content_description
                    /*  $content_description='';
                      if (sizeof($prod('div#content_description'))){

                          is_object($prod('div#content_description')[0])
                           $content_description = $prod('div#content_description')[0]->getPlainText();
                      }
                      //content_usage
                      $content_usage='';
                      if (sizeof($prod('div#content_usage'))&&is_object($prod('div#content_usage')[0]))
                          $content_usage = $prod('div#content_usage')[0]->getPlainText();
                      //content_composition
                      $content_composition='';
                      if (sizeof($prod('div#content_composition'))&&is_object($prod('div#content_composition')[0]))
                          $content_composition = $prod('div#content_composition')[0]->getPlainText();
                      //$content_notice = $prod->find('div#content_notice')[0]->plaintext;
                      //parent
                      if (sizeof($prod('div.product_breadcrumbproduct_review'))&&is_object($prod('div.product_breadcrumbproduct_review')[0])) {
                          $parents = $prod('div.product_breadcrumbproduct_review');
                          $parents = $parents[0]('a');
                          $parent = $parents[sizeof($parents) - 1];
                      }else continue;
                      //extraction du parent remote_id
                      preg_match('#-c([0-9]+)$#',$parent->href,$out);
                      $parentid = $out[1];*/
                    //$cat = Sys::getOneData('Boutique','Categorie/*/RemoteId='.$parentid);

                    //debug
                    /*echo '$titre '.$title."\r\n";
                    echo '$marque '.$marque."\r\n";
                    echo '$image '.$image."\r\n";
                    echo '$description '.$description."\r\n";
                    echo '$volume '.$volume."\r\n";
                    echo '$prix '.$prix."\r\n";
                    echo '$parentid '.$parentid."\r\n";
                    echo '$content_description '.$content_description."\r\n";
                    echo '$content_usage '.$content_usage."\r\n";
                    echo '$content_composition '.$content_composition."\r\n";*/
                    //echo '$content_notice '.$content_notice."\r\n";

                    //création du produit
                    $p  = genericClass::createInstance('Boutique','Produit');
                    $p->Nom = $title;
                    $p->Description = $description;
                    $p->Actif = 1;
                    $p->RemoteId = $remoteid;
                    $p->Save();

                    //ajout des données description
                    $d = genericClass::createInstance('Boutique','Donnee');
                    $d->Valeur = $content_description;
                    $d->Type = 'Descriptif';
                    $d->TypeCaracteristique = 'Détails';
                    $d->AddParent($p);
                    $d->Save();

                    //ajout des données utilisattion
                    $d = genericClass::createInstance('Boutique','Donnee');
                    $d->Valeur = $content_usage;
                    $d->Type = 'Descriptif';
                    $d->TypeCaracteristique = 'Utilisation';
                    $d->AddParent($p);
                    $d->Save();

                    //ajout des données composition
                    $d = genericClass::createInstance('Boutique','Donnee');
                    $d->Valeur = $content_composition;
                    $d->Type = 'Descriptif';
                    $d->TypeCaracteristique = 'Composition';
                    $d->AddParent($p);
                    $d->Save();

                    //creation de la marque
                    $ma = Sys::getOneData('Boutique','Marque/Nom='.Utils::KEAddSlashes(Array($marque)));
                    if (!$ma){
                        $ma = genericClass::createInstance('Boutique','Marque');
                        $ma->Nom = $marque;
                        $ma->addParent($p);
                        $ma->Save();
                    }

                    //creation du dossier image
                    Root::mk_dir('Home/Driveo/'.Utils::checkSyntaxe($cat->Nom));
                    //enregistrement image
                    if (!empty($image)) {
                        copy('http://www.1001pharmacies.com/' . $image, 'Home/Driveo/' . Utils::checkSyntaxe($cat->Nom) . '/' . Utils::checkSyntaxe(Array($p->Nom)) . '.jpg');
                        $p->Image = 'Home/Driveo/' . Utils::checkSyntaxe($cat->Nom) . '/' . Utils::checkSyntaxe(Array($p->Nom)) . '.jpg';
                    }

                    $p->AddParent($cat);
                    $p->Save();
                    $p->genererReferences();

                    //Mise à jour des références
                    $ref = Sys::getOneData('Boutique','Produit/'.$p->Id.'/Reference');
                    $ref->Tarif = $prix;
                    $ref->Quantite = 100;
                    $ref->Save();
                    echo $bash->getColoredString("--> Traitement produit ".$p->Reference." ".$p->Nom." [ OK ] \r\n","green");
                }else {
                    $string = $remoteid . ' - ' . $a->href . "[ OK ] \r\n";
                    echo $bash->getColoredString($string, 'green');
                }
            }
        }
        //nettoyage
        unset($html);
        unset($prod);
        unset($p);
        unset($d);
        unset($ref);
        //on enrigistre la page en cours
        $this->RemotePage = $page+1;
        $this->Save();
        //fin de la boucle il faut nettoyer kes vars et lancer les pages suivantes
        if ($quelquechose){
            //uniquement si il y avait des produits sur cette page
            $this->ProductParser();
        }
    }    /**
 * parser de catégories pharmacieLafayette
 */
    public function CategoryParserLafayette(){
        $bash = new BashColors();
        echo $bash->getColoredString("Retrieving categories $this->Id ... \r\n","yellow");
        include_once "Class/Utils/htmlParser.class.php";

        //bashcolors
        echo $bash->getColoredString("Download File ... ".$this->RemoteUrl."\r\n","yellow");

        //récupération d'une page contenant le menu total
        $html = file_get_dom($this->RemoteUrl);

        //Trouver tous les menus
        $parent = $this;
        foreach ($html('ul#menu') as $div){
            //parapohgarmacie
            foreach ($div('li.para') as $li){
                foreach ($li('a.drop') as $a) {
                    $cat = $this->addCategoryLafayette($a,$parent,0);

                    //recherche des sous-categories maitres
                    foreach ($li('div.dropdown div.sub-categories ul') as $ul) {
                        foreach ($ul('li.main a') as $m) {
                            $main = $this->addCategoryLafayette($m, $cat,1);
                        }

                        //recherche des sous-categories
                        foreach ($ul('li.sub a') as $sub) {
                            $this->addCategoryLafayette($sub, $main,2);
                        }
                    }
                }
            }

            //medicaments
            foreach ($div('li.medocs') as $li){
                foreach ($li('a.drop') as $a) {
                    $cat = $this->addCategoryLafayette($a,$parent,0);

                    //recherche des sous-categories maitres
                    foreach ($li('div.dropdown div.sub-categories ul') as $ul) {
                        foreach ($ul('li.main a') as $m) {
                            $main = $this->addCategoryLafayette($m, $cat,1);
                        }

                        //recherche des sous-categories
                        foreach ($ul('li.sub a') as $sub) {
                            $this->addCategoryLafayette($sub, $main,2);
                        }
                    }
                }
            }
        }
    }
    /**
     * addCategory
     *
     */
    public function addCategoryLafayette ($a, $parent,$level) {
        $error=0;

        //chaine de recherche
        $snom = html_entity_decode(trim(strip_tags($a->getPlainText())));
        $s = Utils::KEAddSlashes(Array( $snom));

        //extraction du remote_id
        preg_match('#\/([A-z0-9-]+?)-c-([0-9_]+)#',$a->href,$out);
        $rid = $out[2];

        //recherche des parents
        $r = Sys::getData('Boutique', 'Categorie/'.$parent->Id.'/Categorie/RemoteId=' .$rid);
        //echo '<li>'.'Categorie/'.$parent->Id.'/Categorie/RemoteId=' .$l.' => '.sizeof($r).' ( '.$k.' )</li>';
        if(!sizeof($r)){
            //echo '<li>'.'Categorie/'.$parent->Id.'/Categorie/RemoteId=' .$l.' => '.sizeof($r).' ( '.$k.' )</li>';
            //création
            $cat  = genericClass::createInstance('Boutique','Categorie');
            $cat->Nom = $snom;
            $cat->Actif = 1;
            $cat->AddParent($parent);
            $cat->RemoteId = $rid;
            $cat->RemoteUrl = $a->href;
            $cat->Save();
            $r = Array($cat);
        }

        //affichage
        switch ($error){
            case 2:
                $string = 'parents introuvables  => ' . $s . ' => ' . $a->href."\r\n";
                //echo $bash->getColoredString($string,'red');
                echo '<li';
                echo ' style="color:red;"> parents introuvables';
                echo ' => ' . $s . ' => ' . $a->href;
                echo "</li>";
                break;
            case 1:
                $string = 'plusieurs catégories possibles ( ';
                foreach ($r2 as $r1){
                    $string .=$r1->Id.' / ';
                }
                $string .=') => ' . $snom . ' => ' . $a->href."\r\n";
                //echo $bash->getColoredString($string,'orange');
                echo '<li';
                echo ' style="color:orange;"> plusieurs catégories possibles (';
                foreach ($r as $r1){
                    echo $r1->Id.' / ';
                }
                echo ') => ' . $s . ' => ' . $a->href;
                echo "</li>";
                break;
            default:
                $rc = $r[0];
                $string = $rc->Id . ' - ' . $rc->RemoteId . ' => ' . $snom . ' => ' . $a->href."\r\n";
                //echo $bash->getColoredString($string,'green');
                $tab='';
                for ($i=0; $i <= $level; $i++) {
                    $tab.=" -> ";
                }
                echo '<li style="color:green"';
                echo '>' .$tab. $rc->Id . ' - ' . $rc->RemoteId . ' => ' . $snom . ' => ' . $a->href;
                echo "</li>";
                break;
        }
        return $r[0];
    }
    /**
     * parser de produits PharmacieLafayette
     */
    public function ProductParserLafayette($page=1,$url=''){
        include_once "Class/Utils/htmlParser.class.php";

        //test page en cours
        if ($page==1){
            //on vérifie la page en cours
//            if ($this->RemotePage>1) $page = $this->RemotePage;
        }
        //récupération d'une page contenant le menu total
        $url = (empty($url))?$this->RemoteUrl:$url;
        if ($page>1){
            $url .= '?page='.$page;
        }
        $bash = new BashColors();
        echo $bash->getColoredString("PAGE $page downloading... $url\r\n","green");
        $html = file_get_dom($url);

        //nombre de page
        $nbprod = 0;
        $tmp = $html('div#paging-bas div.align-left b');
        foreach ($tmp as $t) {
            $nbprod = $t->getPlainText();
        }
        //Si il y des catégories en queue
        if ($this->isTail()) {
            foreach ($html('div#contenu_large div.categories-listing div.categorie a') as $a) {
                echo $bash->getColoredString("SOUS CAT $page downloading... $a->href\r\n","orange");
                $this->ProductParserLafayette(1,$a->href);
            }
        }
        //Trouver tous les produits medicaments
        foreach ($html('div#contenu_large div.fond_vert') as $div){

            //liste des produits parapharmacie
            foreach ($div('div.product-thumb div.add a.btn_accueil') as $a){
                $error= 0;

                //extraction du remote_id
                preg_match('#-p-([0-9]+)#',$a->href,$out);
                $remoteid = $out[1];

                $r = Sys::getData('Boutique', 'Produit/RemoteId=' .$remoteid .'&RemoteSite=PharmacieLafayette');
                if(!sizeof($r)){
                    //téléchargement de la fiche
                    echo $bash->getColoredString("--> Download File $a->href... \r\n","yellow");
                    $prod = file_get_dom($a->href);

                    if (!$prod){
                        echo $bash->getColoredString("--> ERROR $a->href... \r\n","red");
                        continue;
                    }

                    //on reverifie après
                    $r = Sys::getData('Boutique', 'Produit/RemoteId=' .$remoteid .'&RemoteSite=PharmacieLafayette');
                    if(sizeof($r)){
                        continue;
                    }

                    //titre
                    if (sizeof($prod('div.titre'))) {
                        $tmptitle = $prod('div.titre');
                        $title = $tmptitle[0]->getPlainText();
                    }
                    //marque
                    /*$marque = $prod('a.purple');
                    $marque = $marque[0]->getPlainText();*/

                    //description
                    $description = $prod('div#tabs-0');
                    if (sizeof($description)){
                        $description = $description[0]->getPlainText();
                    }else $description='';

                    //image
                    $image = $prod('a.fancybox3 img');
                    if (sizeof($image)){
                        $image = $image[0]->src;
                    }else $image='';

                    //volume et codes
                    $CIP13 = $CIP7 = $EAN = '';
                    $volume = $prod('div.grisclair-product');
                    if (sizeof($volume)&&is_object($volume[0])) {
                        $marque = $volume[0]->html();
                        if (preg_match('#<meta itemprop="brand" content="(.*?)"#',$marque,$out)){
                            $marque = $out[1];
                        }

                        $volume = $volume[0]->getPlainText();

                        //test EAN
                        if (preg_match('#EAN\/GTIN : ([0-9]+)#',$volume,$out)){
                            $EAN=$out[1];
                        }
                        //test CIP7
                        if (preg_match('#CIP7\/ACL7 : ([0-9]+)#',$volume,$out)){
                            $CIP7=$out[1];
                        }
                        //test CIP13
                        if (preg_match('#CIP13\/ACL13 : ([0-9]+)#',$volume,$out)){
                            $CIP13=$out[1];
                        }
                        //test Contenance
                        if (preg_match('#Contenance : (.+)$#',$volume,$out)){
                            $volume = $out[1];
                        }

                    }else $volume = '';

                    //prix
                    /*$prix = $prod('span.product_price');
                    if (sizeof($prix)&&is_object($prix[0])){
                        $prix = $prix[0]->getPlainText();
                        $prix = str_replace(',','.',$prix);
                        $prix = floatval($prix)/1.20;
                    }*/

                    //content_description
                    /*$content_description='';
                    if (sizeof($prod('div#content_description'))){

                        is_object($prod('div#content_description')[0])
                         $content_description = $prod('div#content_description')[0]->getPlainText();
                    }*/

                    //content_usage
                    $content_usage='';
                    $pt = $prod('div#tabs-1');
                    if (sizeof($pt)&&is_object($pt[0]))
                        $content_usage = $pt[0]->getPlainText();

                    //content_precautions
                    $content_precautions='';
                    $pt = $prod('div#tabs-2');
                    if (sizeof($pt)&&is_object($pt[0]))
                        $content_composition = $pt[0]->getPlainText();

                    //$contre_indications
                    $contre_indications='';
                    $pt = $prod('div#tabs-3');
                    if (sizeof($pt)&&is_object($pt[0]))
                        $contre_indications = $pt[0]->getPlainText();

                    //effets_indesirables
                    $effets_indesirables='';
                    $pt = $prod('div#tabs-5');
                    if (sizeof($pt)&&is_object($pt[0]))
                        $effets_indesirables = $pt[0]->getPlainText();

                    //composition
                    $composition='';
                    $pt = $prod('div#tabs-6');
                    if (sizeof($pt)&&is_object($pt[0]))
                        $composition = $pt[0]->getPlainText();

                    //content_notice
                    $content_notice='';
                    $pt = $prod('div#tabs-18');
                    if (sizeof($pt)&&is_object($pt[0]))
                        $content_notice = $pt[0]->src;

                    //$content_notice = $prod->find('div#content_notice')[0]->plaintext;
                    //parent
                    /*  if (sizeof($prod('div.product_breadcrumbproduct_review'))&&is_object($prod('div.product_breadcrumbproduct_review')[0])) {
                          $parents = $prod('div.product_breadcrumbproduct_review');
                          $parents = $parents[0]('a');
                          $parent = $parents[sizeof($parents) - 1];
                      }else continue;
                      //extraction du parent remote_id
                      preg_match('#-c([0-9]+)$#',$parent->href,$out);
                      $parentid = $out[1];

                    */
                    //$cat = Sys::getOneData('Boutique','Categorie/*/RemoteId='.$parentid);

                    //debug
                    //echo $bash->getColoredString('$titre '.$title, 'green');
                    //echo '$marque '.$marque."\r\n";
                    //echo '$image '.$image."\r\n";
                    //echo '$description '.$description."\r\n";
                    //echo '$volume '.$volume."\r\n";
                    //echo '$prix '.$prix."\r\n";
                    //echo '$parentid '.$parentid."\r\n";
                    //echo '$content_description '.$content_description."\r\n";
                    //echo '$content_usage '.$content_usage."\r\n";
                    //echo '$content_composition '.$content_composition."\r\n";
                    //echo '$content_notice '.$content_notice."\r\n";

                    //création du produit
                    $p  = genericClass::createInstance('Boutique','Produit');
                    $p->Nom = $title;
                    $p->Description = $description;
                    $p->EAN = $EAN;
                    $p->CIP7 = $CIP7;
                    $p->CIP13 = $CIP13;
                    $p->Actif = 1;
                    $p->RemoteUrl = $a->href;
                    $p->RemoteId = $remoteid;
                    $p->RemoteSite = "PharmacieLafayette";
                    $p->Save();

                    //ajout des données description
                    $d = genericClass::createInstance('Boutique','Donnee');
                    $d->Valeur = $volume;
                    $d->Type = 'Descriptif';
                    $d->TypeCaracteristique = 'Détails';
                    $d->AddParent($p);
                    $d->Save();

                    if (!empty($content_precautions)) {
                        //ajout des données utilisattion
                        $d = genericClass::createInstance('Boutique', 'Donnee');
                        $d->Valeur = $content_precautions;
                        $d->Type = 'Descriptif';
                        $d->TypeCaracteristique = 'Précautions d\'emploi';
                        $d->AddParent($p);
                        $d->Save();
                    }

                    if (!empty($contre_indications)) {
                        //ajout des données utilisattion
                        $d = genericClass::createInstance('Boutique', 'Donnee');
                        $d->Valeur = $contre_indications;
                        $d->Type = 'Descriptif';
                        $d->TypeCaracteristique = 'Contre Indication';
                        $d->AddParent($p);
                        $d->Save();
                    }

                    if (!empty($effets_indesirables)) {
                        //ajout des données utilisattion
                        $d = genericClass::createInstance('Boutique', 'Donnee');
                        $d->Valeur = $effets_indesirables;
                        $d->Type = 'Descriptif';
                        $d->TypeCaracteristique = 'Effets indésriables';
                        $d->AddParent($p);
                        $d->Save();
                    }

                    if (!empty($content_notice)) {
                        Root::mk_dir('Home/Driveo/manuals');
                        $content_notice = str_replace(' ','%20',$content_notice);
                        copy('http://www.pharmacielafayette.com/' . $content_notice, 'Home/Driveo/'.$content_notice);
                        //ajout des données utilisattion
                        $d = genericClass::createInstance('Boutique', 'Donnee');
                        $d->Valeur = '<iframe src="Home/Driveo/' . $content_notice.'" width="100%" height="500" scrolling="auto"></iframe>';
                        $d->Type = 'Descriptif';
                        $d->TypeCaracteristique = 'Notice';
                        $d->AddParent($p);
                        $d->Save();
                    }

                    if (!empty($content_usage)) {
                        //ajout des données utilisattion
                        $d = genericClass::createInstance('Boutique', 'Donnee');
                        $d->Valeur = $content_usage;
                        $d->Type = 'Descriptif';
                        $d->TypeCaracteristique = 'Utilisation';
                        $d->AddParent($p);
                        $d->Save();
                    }

                    if (!empty($composition)) {
                        //ajout des données composition
                        $d = genericClass::createInstance('Boutique', 'Donnee');
                        $d->Valeur = $composition;
                        $d->Type = 'Descriptif';
                        $d->TypeCaracteristique = 'Composition';
                        $d->AddParent($p);
                        $d->Save();
                    }

                    //creation de la marque
                    $ma = Sys::getOneData('Boutique','Marque/Nom='.Utils::KEAddSlashes(Array($marque)));
                    if (!$ma){
                        $ma = genericClass::createInstance('Boutique','Marque');
                        $ma->Nom = $marque;
                        $ma->addParent($p);
                        $ma->Save();
                    }

                    //creation du dossier image
                    Root::mk_dir('Home/Driveo/'.Utils::checkSyntaxe($this->Nom));
                    //enregistrement image
                    if (!empty($image)) {
                        $image = str_replace(' ','%20',$image);
                        copy('http://www.pharmacielafayette.com/' . $image, 'Home/Driveo/' . Utils::checkSyntaxe($this->Nom) . '/' . Utils::checkSyntaxe(Array($p->Nom)) . '.jpg');
                        $p->Image = 'Home/Driveo/' . Utils::checkSyntaxe($this->Nom) . '/' . Utils::checkSyntaxe(Array($p->Nom)) . '.jpg';
                    }

                    $p->AddParent($this);
                    $p->Save();
                    $p->genererReferences();

                    //Mise à jour des références
                    $ref = Sys::getOneData('Boutique','Produit/'.$p->Id.'/Reference');
                    $ref->Tarif = 100;
                    $ref->Quantite = 100;
                    //$ref->Save();
                    echo $bash->getColoredString("--> Traitement produit ".$p->Reference." ".$p->Nom." [ OK ] \r\n","green");
                }else {
                    /*$string = $remoteid . ' - ' . $a->href . "[ OK ] \r\n";
                    echo $bash->getColoredString($string, 'green');*/
                }
            }
        }

        //Trouver tous les produits parapharmacie
        foreach ($html('div#contenu_large div.fond_orange') as $div){

            //liste des produits parapharmacie
            foreach ($div('div.product-thumb div.add a.btn_accueil') as $a){
                $error= 0;

                //extraction du remote_id
                preg_match('#-p-([0-9]+)#',$a->href,$out);
                $remoteid = $out[1];

                $r = Sys::getData('Boutique', 'Produit/RemoteId=' .$remoteid .'&RemoteSite=PharmacieLafayette');
                if(!sizeof($r)){
                    //téléchargement de la fiche
                    echo $bash->getColoredString("--> Download File $a->href... \r\n","yellow");
                    $prod = file_get_dom($a->href);

                    if (!$prod){
                        echo $bash->getColoredString("--> ERROR $a->href... \r\n","red");
                        continue;
                    }

                    //on reverifie après
                    $r = Sys::getData('Boutique', 'Produit/RemoteId=' .$remoteid .'&RemoteSite=PharmacieLafayette');
                    if(sizeof($r)){
                        continue;
                    }

                    //titre
                    if (sizeof($prod('div.titre'))) {
                        $tmptitle = $prod('div.titre');
                        $title = $tmptitle[0]->getPlainText();
                    }
                    //marque
                    /*$marque = $prod('a.purple');
                    $marque = $marque[0]->getPlainText();*/

                    //description
                    $description = $prod('div#tabs-0');
                    if (sizeof($description)){
                        $description = $description[0]->getPlainText();
                    }else $description='';

                    //image
                    $image = $prod('a.fancybox3 img');
                    if (sizeof($image)){
                        $image = $image[0]->src;
                    }else $image='';

                    //volume et codes
                    $CIP13 = $CIP7 = $EAN = '';
                    $volume = $prod('div.grisclair-product');
                    if (sizeof($volume)&&is_object($volume[0])) {
                        $marque = $volume[0]->html();
                        if (preg_match('#<meta itemprop="brand" content="(.*?)"#',$marque,$out)){
                            $marque = $out[1];
                        }

                        $volume = $volume[0]->getPlainText();

                        //test EAN
                        if (preg_match('#EAN\/GTIN : ([0-9]+)#',$volume,$out)){
                            $EAN=$out[1];
                        }
                        //test CIP7
                        if (preg_match('#CIP7\/ACL7 : ([0-9]+)#',$volume,$out)){
                            $CIP7=$out[1];
                        }
                        //test CIP13
                        if (preg_match('#CIP13\/ACL13 : ([0-9]+)#',$volume,$out)){
                            $CIP13=$out[1];
                        }
                        //test Contenance
                        if (preg_match('#Contenance : (.+)$#',$volume,$out)){
                            $volume = $out[1];
                        }

                    }else $volume = '';

                    //prix
                    /*$prix = $prod('span.product_price');
                    if (sizeof($prix)&&is_object($prix[0])){
                        $prix = $prix[0]->getPlainText();
                        $prix = str_replace(',','.',$prix);
                        $prix = floatval($prix)/1.20;
                    }*/

                    //content_description
                      /*$content_description='';
                      if (sizeof($prod('div#content_description'))){

                          is_object($prod('div#content_description')[0])
                           $content_description = $prod('div#content_description')[0]->getPlainText();
                      }*/

                      //content_usage
                      $content_usage='';
                    $pt =  $prod('div#tabs-1');
                      if (sizeof($pt)&&is_object($pt[0]))
                          $content_usage = $pt[0]->getPlainText();

                      //content_composition
                      $content_composition='';
                    $pt = $prod('div#tabs-2');
                      if (sizeof($pt)&&is_object($pt[0]))
                          $content_composition = $pt[0]->getPlainText();

                      //$content_notice = $prod->find('div#content_notice')[0]->plaintext;
                      //parent
                    /*  if (sizeof($prod('div.product_breadcrumbproduct_review'))&&is_object($prod('div.product_breadcrumbproduct_review')[0])) {
                          $parents = $prod('div.product_breadcrumbproduct_review');
                          $parents = $parents[0]('a');
                          $parent = $parents[sizeof($parents) - 1];
                      }else continue;
                      //extraction du parent remote_id
                      preg_match('#-c([0-9]+)$#',$parent->href,$out);
                      $parentid = $out[1];

                    */
                    //$cat = Sys::getOneData('Boutique','Categorie/*/RemoteId='.$parentid);

                    //debug
                    //echo $bash->getColoredString('$titre '.$title, 'green');
                    //echo '$marque '.$marque."\r\n";
                    //echo '$image '.$image."\r\n";
                    //echo '$description '.$description."\r\n";
                    //echo '$volume '.$volume."\r\n";
                    //echo '$prix '.$prix."\r\n";
                    //echo '$parentid '.$parentid."\r\n";
                    //echo '$content_description '.$content_description."\r\n";
                    //echo '$content_usage '.$content_usage."\r\n";
                    //echo '$content_composition '.$content_composition."\r\n";
                    //echo '$content_notice '.$content_notice."\r\n";

                    //création du produit
                    $p  = genericClass::createInstance('Boutique','Produit');
                    $p->Nom = $title;
                    $p->Description = $description;
                    $p->EAN = $EAN;
                    $p->CIP7 = $CIP7;
                    $p->CIP13 = $CIP13;
                    $p->Actif = 1;
                    $p->RemoteUrl = $a->href;
                    $p->RemoteId = $remoteid;
                    $p->RemoteSite = "PharmacieLafayette";
                    $p->Save();

                    //ajout des données description
                    $d = genericClass::createInstance('Boutique','Donnee');
                    $d->Valeur = $volume;
                    $d->Type = 'Descriptif';
                    $d->TypeCaracteristique = 'Détails';
                    $d->AddParent($p);
                    $d->Save();

                    //ajout des données utilisattion
                    $d = genericClass::createInstance('Boutique','Donnee');
                    $d->Valeur = $content_usage;
                    $d->Type = 'Descriptif';
                    $d->TypeCaracteristique = 'Utilisation';
                    $d->AddParent($p);
                    $d->Save();

                    //ajout des données composition
                    $d = genericClass::createInstance('Boutique','Donnee');
                    $d->Valeur = $content_composition;
                    $d->Type = 'Descriptif';
                    $d->TypeCaracteristique = 'Composition';
                    $d->AddParent($p);
                    $d->Save();

                    //creation de la marque
                    $ma = Sys::getOneData('Boutique','Marque/Nom='.Utils::KEAddSlashes(Array($marque)));
                    if (!$ma){
                        $ma = genericClass::createInstance('Boutique','Marque');
                        $ma->Nom = $marque;
                        $ma->addParent($p);
                        $ma->Save();
                    }

                    //creation du dossier image
                    Root::mk_dir('Home/Driveo/'.Utils::checkSyntaxe($this->Nom));
                    //enregistrement image
                    if (!empty($image)) {
                        $image = str_replace(' ','%20',$image);
                        copy('http://www.pharmacielafayette.com/' . $image, 'Home/Driveo/' . Utils::checkSyntaxe($this->Nom) . '/' . Utils::checkSyntaxe(Array($p->Nom)) . '.jpg');
                        $p->Image = 'Home/Driveo/' . Utils::checkSyntaxe($this->Nom) . '/' . Utils::checkSyntaxe(Array($p->Nom)) . '.jpg';
                    }

                    $p->AddParent($this);
                    $p->Save();
                    $p->genererReferences();

                    //Mise à jour des références
                    $ref = Sys::getOneData('Boutique','Produit/'.$p->Id.'/Reference');
                    $ref->Tarif = 100;
                    $ref->Quantite = 100;
                    //$ref->Save();
                    echo $bash->getColoredString("--> Traitement produit ".$p->Reference." ".$p->Nom." [ OK ] \r\n","green");
                }else {
                    /*$string = $remoteid . ' - ' . $a->href . "[ OK ] \r\n";
                    echo $bash->getColoredString($string, 'green');*/
                }
            }
        }
        //nettoyage
        unset($html);
        unset($prod);
        unset($p);
        unset($d);
        unset($ref);
        //on enrigistre la page en cours
        //$this->RemotePage = $page+1;
        $this->Save();
        //fin de la boucle il faut nettoyer kes vars et lancer les pages suivantes
        if ($nbprod>$page*80){
            //uniquement si il y avait des produits sur cette page
            $this->ProductParserLafayette($page+1);
        }
    }

    private function analyseProduct() {

    }
}
