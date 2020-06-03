<?php

class MiniSite extends genericClass{

    public function Save( $forceMenus = false ){
        Sys::startTransaction();

        $skin = 'Minisite';
        $menus = array(array('Url'=>'','Titre'=>$this->Nom,'Alias'=>''));
        $domPref = 'www';


        parent::Save();
        if(empty($this->Domaine)){
            $parD = $this->getOneParent('Domain');
            if($parD){
                $this->Domaine = $parD->Url;
            } else {
                $GLOBALS['Systeme']->Db[0]->query('ROLLBACK');
                $this->addError(Array("Message"=>'Un minisite nécéssite un domaine'));
                return false;
            }
        }

        $ds = explode('.',$this->Domaine);
        if(count($ds)  == 1  ){
            $GLOBALS['Systeme']->Db[0]->query('ROLLBACK');
            $this->addError(Array("Message"=>'Un minisite nécéssite un domaine valide'));
            return false;
        }
        if(count($ds)  == 2  ){
            $fullDomain = $domPref.'.'.$this->Domaine;
        }
        if(count($ds)  > 2  ){
            $fullDomain = $this->Domaine;
            $domPref = implode('.',array_splice($ds,-2));
        }



        //Check du domaine
        $dom = Sys::getOneData('Parc','Domain/Url='.$this->Domaine);
        if($dom) {
            $temp = $dom->getChildren('MiniSite');
            $cpt = 0;
            foreach ($temp as $m){
                if($m->Id != $this->Id) $cpt++;
            }
            if ($cpt >= 1) {
                $GLOBALS['Systeme']->Db[0]->query('ROLLBACK');
                $this->addError(array("Message" => 'Un minisite est déjà lié à ce domaine'));
                return false;
            }
        }

        //Check du site et création le cas échéant
        $sit = Sys::getOneData('Systeme','Site/Domaine='.$this->Domaine);
        if(!$sit) {
            $sit =  genericClass::createInstance('Systeme','Site');
            $sit->Domaine = $fullDomain;
            if(!$sit->Save(true)) {
                $GLOBALS['Systeme']->Db[0]->query('ROLLBACK');
                $this->addError(Array("Message"=>'Une Erreur est survenue lors de la création du site :'.$sit->Error[0]['Message']));
                return false;
            }
            $this->addParent($sit);
            parent::Save();
        }

        //Recupération du modèle
        $mod = $this->getOneParent('ModeleMiniSite');
        if($mod){
            //Skin
            $skin = $mod->Skin;

            //Menus
            $params = $mod->getChildren('ParametreMiniSite/Type=menu');
            foreach ($params as $p){
                $val = $p->getOneChild('ValeurMiniSite');
                $men = json_decode($val->Valeur,true);
                $menus[] = $men;
            }

            //Pages
            $pages = $mod->getChildren('PageMiniSite');
            foreach($pages as $page){
                if($page->MenuAffiche){
                    $titre = !empty($page->MenuTitre) ? $page->MenuTitre : $page->Titre;
                    $url = !empty($page->MenuUrl) ? $page->MenuUrl : $page->Titre;
                    $alias = !empty($page->Alias) ? $page->Alias : 'Parc/PageMiniSite/'.$page->Id;
                    $m = array('Url'=>$url,'Titre'=>$titre,'Alias'=>$alias);
                    if(!empty($page->Template)) $m['Template'] = $page->Template;
                    $menus[] = $m;
                }
            }

            //clean des url
            foreach($menus as &$menu){
                $menu['Url'] = strtolower($menu['Url']);
                $menu['Url'] = Utils::checkSyntaxe($menu['Url']);
            }
        }
        //Check du user du site creation/affectation le cas échéant
        $par = $sit->getOneParent('User');
        //recup du groupe des sites mini
        $grp = Sys::getOneData('Systeme','Group/Nom=MiniSites');

        if(!$par){
            $par = genericClass::createInstance('Systeme','User');
            $par->Login = 'mini_'.$this->Id;
            $par->Pass = str_shuffle(bin2hex(openssl_random_pseudo_bytes(4)));
            $par->Mail = 'mini_'.$this->Id.'@abtel.fr';
            $par->Actif = true;
            $par->Skin = $skin;

            if($grp)
                $par->addParent($grp);

            if(!$par->Save()) {
                $GLOBALS['Systeme']->Db[0]->query('ROLLBACK');
                $this->addError(Array("Message"=>'Une Erreur est survenue lors de la création de l\'utilisateur lié au site :'.$par->Error[0]['Message']));
                return false;
            }
            $sit->addParent($par);
            $sit->Save(true);
        } else{
            $par->Skin = $skin;
            if($grp)
                $par->addParent($grp);

            if(!$par->Save()) {
                $GLOBALS['Systeme']->Db[0]->query('ROLLBACK');
                $this->addError(Array("Message"=>'Une Erreur est survenue lors de la  mise à jour de l\'utilisateur lié au site :'.$par->Error[0]['Message']));
                return false;
            }
        }

        //Check du menu et creation le cas échéant
        $men = Sys::getData('Systeme','User/'.$par->Id.'/Menu');

        if(count($men) != count($menus) || $forceMenus){
            if(count($men)){
                foreach ($men as $me){
                    $me->Delete();
                }
            }
            foreach ($menus as $m){
                $men = genericClass::createInstance('Systeme','Menu');
                $men->Titre = $m['Titre'];
                $men->Url = $m['Url'];
                $men->Alias = $m['Alias'];
                if(!empty($m['Template']))
                    $men->Template = $m['Template'];
                $men->addParent($par);
                if(!$men->Save()) {
                    $GLOBALS['Systeme']->Db[0]->query('ROLLBACK');
                    $this->addError(Array("Message"=>'Une Erreur est survenue lors de la création du menu lié au site :'.$men->Error[0]['Message']));
                    return false;
                }
            }
        }

        //Check de la page et création le cas échéant
        /*$pag = $sit->getOneChild('Page');

        if(!$pag){
            $pag = genericClass::createInstance('Systeme','Page');
            $pag->Url = 'http://www.'.$dom->Url.'/';
            $pag->PageModule = 'Systeme';
            $pag->PageObject = 'Minisite';
            $pag->PageId = $this->Id;
            $pag->Title =$this->Titre;

            $pag->addParent($sit);
            $pag->Save();

        }*/

        if($dom) {
            $upd = false;
            $A = $dom->getOneChild('Subdomain/Nom=A:');
            if (!$A) {
                $upd = true;
            } else {
                $A->IP = '158.255.102.117';
                $A->Save();
            }
            $www = $dom->getOneChild('CNAME/Dnsdomainname='.$domPref);
            if (!$www) {
                $www = $dom->getOneChild('Subdomain/Url='.$domPref);
                if (!$www) {
                    $upd = true;
                } else {
                    $www->IP = '158.255.102.117';
                    $www->Save();
                }
            } else {
                $www->Dnscname = $dom->Url . '.';
                $www->Save();
            }

            if ($upd) {
                $dom->updateOnSave = 1;
                $template = Sys::getOneData('Parc', 'DomainTemplate/Nom=Minisites');
                $dom->addParent($template);
                $dom->Save();
            }
        }

        return parent::Save();
    }

    public function Delete(){

        $site = Sys::getOneData('Systeme','Site/Domaine='.$this->Domaine);
        if($site){
            $user = $site->getOneParent('User');
            if($user){
                $menus = $user->getChildren('Menu');
                if(!empty($menus)) {
                    foreach ($menus as $menu){
                        $menu->Delete();
                    }
                }
                $user->Delete();
            }
            $site->Delete();
        }

        parent::Delete();
    }

    public function getParams($full=true){
        $params = $this->getChildren('ParametreMiniSite');
        if($full){
            $modele = $this->getOneParent('ModeleMiniSite');
            $pms = $modele->getChildren('ParametreMiniSite');
            $params = $params + $pms;
        }
        return $params;
    }

    public function getParamsValues($full=true){
        $params = $this->getParams($full);
        foreach ($params as &$p){
            $val = $p->getOneChild('ValeurMiniSite/MiniSite.MiniSiteId('.$this->Id.')');
            $p->vms = $val->Valeur;
        }
        return $params;
    }

}