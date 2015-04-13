<?php
class Site extends genericClass {

    var $cpt;
    var $currentUrl;

     //____________________________________________________________________________________________
    //                                                                                    STANDARD
    /**
     * addPage
     * Ajoute une page en utilisant une url absolue.
     * @param String url de la page
     * @param String alias lien interne de l'element
     * @param Menu object menu
     */
    public function addPage($url,$alias="",$men=""){
        $url = 'http://'.$this->Domaine.'/'.$url;
        //Verification de l'existence
        $p = $this->getChildren('Page/MD5=' . md5($url));
        if (!sizeof($p)){
            //creation de la page
            $p = genericClass::createInstance('Systeme','Page');
            $p->Url = $url;
            $p->MD5 = md5($p->Url);
            //$p->FromUrl = md5($fromurl);
            $p->addParent($this);
        }else $p=$p[0];
        $p->Set('LastMod', date('Y-m-d'));
        if (!empty($alias)){
            //analyse de la requete
            $i = Info::getInfos($alias);
            if ($i['TypeSearch']!='Interface'){
                //recherche de l'element correpondant
                if ($i['TypeSearch']=='Child'){
                    $el = $men;
                }else{
                    $el = Sys::getData($i["Module"],$i["ObjectType"].'/'.$i["LastId"]);
                    if (!sizeof($el)) $el = $men;
                    else $el = $el[0];
                }
            }else{
                //alors information du menu
                $el = $men;
            }
        }elseif (is_object($men)){
            $el=$men;
        }
        if (isset($el)){
            //mise à jour des donnés
            if (empty($p->Title)&&isset($el->TitleMeta))$p->Title = $el->TitleMeta;
            if (empty($p->Description)&&isset($el->DescriptionMeta))$p->Description = $el->DescriptionMeta;
            if (empty($p->Keywords)&&isset($el->KeywordsMeta))$p->Keywords = $el->KeywordsMeta;
            if (empty($p->Image)&&isset($el->ImgMeta))$p->Image = $el->ImgMeta;
            if (empty($p->PageModule))$p->PageModule = $el->Module;
            if (empty($p->PageObject))$p->PageObject = $el->ObjectType;
            if (empty($p->PageId))$p->PageId = $el->Id;
        }
        $p->Save();
        return $p;
    }
    
     
    /**
     * delPage
     * Supprime une page et ses pages descendantes
     * @param String url de la page
     * @param String alias lien interne de l'element
     * @param Menu object menu
     */
    public function delPage($url){
        $url = 'http://'.$this->Domaine.'/'.$url;
        //Verification de l'existence
        $p = $this->getChildren('Page/MD5=' . md5($url));
        foreach ($p as $p2)
            $p2->Delete();
    }
    
   
    
    //____________________________________________________________________________________________
    //                                                                                     CRAWLER
    
   public function Crawl( $url ) {
        if(is_file('videurl')) $this->VideUrl();
        if(is_file('stop')) die;
        $this->cpt = isset($this->cpt) ? $this->cpt+1 : 0;
        $this->currentUrl = $url;
        $content = file_get_contents($url);
        $pattern = '/href="(.*?)"/';
        preg_match_all($pattern, $content, $matches);
        foreach($matches[1] as $newurl) $this->RecordUrl($newurl, $url);
    }
    public function VideUrl() {
        $Pages = $this->storproc('Systeme/Site/' . $this->Id . '/Page','', 0,100000 );
        if(is_array($Pages) && sizeof($Pages)>0)  {
            foreach ($Pages as $p) {
                $page = genericClass::createInstance('Systeme', $p);
                $page->delete();
            }
        }
        die;
    }
    public function RecordUrl( $url, $from = '' ) {
        /****************** Standardisation URL ******************/

        // Complète si url relative
        if(substr($url, 0, 7) != 'http://' and substr($url, 0, 8) != 'https://') {
            if(substr($url, 0, 1) == '/') $url = 'http://' . $this->Domaine . $url;
            else $url = $this->currentUrl . '/' . $url;
        }

        // Enleve si / final
        if(substr($url, -1) == '/') $url = substr($url, 0, -1);

        // Si accueil garder /
        if($url == 'http://' . $this->Domaine) $url .= '/';

        /******************** Cas à exclure ********************/
        $keep = true;

        // Ne pas garder des URL avec paramètre ( ? )
        if(stripos($url, '?') !== FALSE) $keep = false;
        if(stripos($url, '#') !== FALSE) $keep = false;
        if(stripos($url, 'mailto:') !== FALSE) $keep = false;
        if(stripos($url, 'javascript:') !== FALSE) $keep = false;

        // Si pas une page on ignore
        $ext = strtolower(substr($url, strrpos($url, '.') + 1));
        $exclude = array('js','css','ico','jpg','jpeg','gif','swf','png','gz','f4v','mp4','avi','apk','zip','pdf', 'doc', 'print', 'xml');
        if(in_array($ext, $exclude)) $keep = false;

        // Si pas le même domaine on ignore
        if(substr($url, 0, strlen('http://' . $this->Domaine)) != 'http://' . $this->Domaine) $keep = false;

       /******************** Verification existence ********************/
        // Si déjà dans la base on ne l'ajoute pas
        $Pages = $this->storproc('Systeme/Page/MD5=' . md5($url), '', 0, $this->cpt );
        if(is_array($Pages) && sizeof($Pages)>0)  {
            $page = genericClass::createInstance('Systeme', $Pages[0]);
            if(!$page->Valid&&$keep) {
                $page->Set('Valid', true);
                $page->Set('Url', $url);
                $page->AddParent('Systeme/Site/'.$this->Id);
                $page->Save();
                if($page->Publier) $this->Crawl($url);
            }elseif ($page->Valid&&!$keep){
                $page->Set('Valid', false);
                $page->Save();
        }
            return;
        }
        /******************* Enregistrement *******************/
        if($keep) {
            $page = genericClass::createInstance('Systeme', 'Page'); 
            $page->Set('Url', $url); 
            $page->Set('FromUrl', $from); 
            $page->Set('MD5', md5($url));
            $page->Set('LastMod', date('Y-m-d'));

            $content = file_get_contents($url);
            $patterntitle = '/<title>(.*?)<\/title>/i';
            $patternkey = '/<meta name="keywords" content="(.*?)"\/>/i';
            $patterndesc = '/<meta name="description" content="(.*?)"\/>/i';
            preg_match($patterntitle, $content, $title);
            $page->Set('Title', $title[1]);
            preg_match($patternkey, $content, $keywords);
            $page->Set('Keywords', $keywords[1]);
            preg_match($patterndesc, $content, $Description);
            $page->Set('Description', $Description[1]);
            $page->AddParent('Systeme/Site/'.$this->Id);
            $page->Save();
            $this->Crawl($url);
        }
    }

    public function getRelativePath( $url ) {
        $pos = strpos( $url, $this->Domaine );
        $relative = substr($url, $pos + strlen($this->Domaine));
        if(substr($relative, 0, 1) == '/') $relative = substr($relative, 1);
        return $relative;
    }

    /*public function getQuery( $groupe, $url ) {
        $res = Sys::$Modules['Systeme']->callData("Systeme/Group/$groupe/Menu/Url=" . $url);
        if(is_array($res) && isset($res[0])) {
            // Alias du Menu
            $alias = $result[0]['Alias'];
            $mods = explode('/', $alias);
            $module = $mods[0];
            $res2 = Sys::$Modules[$module]->callData($alias);
            if(is_array($res2) && isset($res2[0]) && ( !empty($res2[0]['TitleMeta']) || !empty($res2[0]['DescriptionMeta']) || !empty($res2[0]['KeywordsMeta']) ))
                return array('Title' => $res2[0]['TitleMeta'], 'Description' => $res2[0]['DescriptionMeta'], 'Meta' => $res2[0]['KeywordsMeta']);
            // Menu direct
            return array('Title' => $res[0]['Title'], 'Description' => $res[0]['Description'], 'Meta' => $res[0]['Keywords']);
        }
        $parts = explode('/', $url);
        return array('Title' => '', 'Description' => '', 'Meta' => '');
    }*/
    public function RenvoieSite( $url ) {
        /****************** Standardisation URL ******************/
        // Complète si url relative
        if(substr($url, 0, 7) == 'http://') {
        	$lg=strlen($url)-7;
            return substr($url, 7, $lg);
        } else {
        	return $url;
        }
	}
    
        
    public static function getCurrentSite() {
        $sits = Sys::getData('Systeme','Site/Domaine='.Sys::$domain);
        if (sizeof($sits)) return $sits[0];
    }
    
    private function storproc( $Query, $recurs='', $Ofst='', $Limit='', $OrderType='', $OrderVar='', $Selection='', $GroupBy='' ) {
        return Sys::$Modules['Systeme']->callData($Query, $recurs, $Ofst, $Limit, $OrderType, $OrderVar, $Selection, $GroupBy);
    }

}