<?php

class MiniSite extends genericClass{

public function Save(){
    parent::Save();

    switch ($this->Type){
        case 'Standard':

            break;
        case 'Easy':

            break;
        case 'Minimal':

            //Check du domaine
            $dom = $this->getOneParent('Domain');
            if(!$dom){

                $GLOBALS['Systeme']->Db[0]->query('ROLLBACK');
                $this->addError(Array("Message"=>'Un minisite doit être lié à un domaine'));
                return false;
            }
            $temp = $dom->getChildren('MiniSite/Type=Minimal');
            if(count($temp) > 1){
                $GLOBALS['Systeme']->Db[0]->query('ROLLBACK');
                $this->addError(Array("Message"=>'Un minisite est déjà lié à ce domaine'));
                return false;
            }

            //Check du site et création l cas échéant
            $sit = Sys::getOneData('Parc','Site/MiniSite/'.$this->Id);
            if(!$sit) {
                $sit =  genericClass::createInstance('Systeme','Site');
                $sit->Domaine = 'www.'.$dom->Url;
                if(!$sit->Save()) {
                    $GLOBALS['Systeme']->Db[0]->query('ROLLBACK');
                    $this->addError(Array("Message"=>'Une Erreur est survenue lors de la création du site :'.$sit->Error[0]['Message']));
                    return false;
                }
                $this->addParent($sit);
                parent::Save();
            }

            //Check du user du site creation/affectation le cas échéant
            $par = $sit->getOneParent('User');
            if(!$par){
                //recup du groupe des sites mini
                $grp = Sys::getOneData('Systeme','Group/Nom=MiniSites');

                $par = genericClass::createInstance('Systeme','User');
                $par->Login = 'mini_'.$this->Id;
                $par->Pass = str_shuffle(bin2hex(openssl_random_pseudo_bytes(4)));
                $par->Mail = 'mini_'.$this->Id.'@abtel.fr';
                $par->Actif = true;
                $par->Skin = 'Minisite';
                if($grp)
                    $par->addParent($grp);

                if(!$par->Save()) {
                    $GLOBALS['Systeme']->Db[0]->query('ROLLBACK');
                    $this->addError(Array("Message"=>'Une Erreur est survenue lors de la création de l\'utilisateur lié au site :'.$par->Error[0]['Message']));
                    return false;
                }
                $sit->addParent($par);
                $sit->Save();
            }

            //Check du menu et creation le cas échéant
            $men = Sys::getOneData('Systeme','User/'.$par->Id.'/Menu');

            if(!$men){
                $men = genericClass::createInstance('Systeme','Menu');
                $men->Titre = $this->Titre;
                $men->addParent($par);
                if(!$men->Save()) {
                    $GLOBALS['Systeme']->Db[0]->query('ROLLBACK');
                    $this->addError(Array("Message"=>'Une Erreur est survenue lors de la création du menu lié au site :'.$men->Error[0]['Message']));
                    return false;
                }
            }


            $upd = false;
            $A = $dom->getOneChild('Subdomain/Url=A:');
            if(!$A){
                $upd = true;
            } else {
                $A->IP = '158.255.102.117';
                $A->Save();
            }
            $www = $dom->getOneChild('CNAME/Dnsdomainname=www');
            if(!$www){
                $www = $dom->getOneChild('Subdomain/Url=A:www');
                if(!$www){
                    $upd = true;
                } else {
                    $www->IP = '158.255.102.117';
                    $www->Save();
                }
            } else{
                $www->Dnscname = $dom->Url.'.';
                $www->Save();
            }

            if($upd){
                $dom->updateOnSave = 1;
                $template = Sys::getOneData('Parc','DomainTemplate/Nom=Minisites');
                $dom->addParent($template);
                $dom->Save();
            }




            break;
        default:

    }

    parent::Save();
    return true;
}

public function Delete(){
    switch ($this->Type) {
        case 'Standard':

            break;
        case 'Easy':

            break;
        case 'Minimal':
            $site = Sys::getOneData('Parc','Site/MiniSite/'.$this->Id);
            if($site){
                $user = $site->getOneParent('User');
                if($user){
                    $menu = $user->getOneChild('Menu');
                    if($menu) {
                        $menu->Delete();
                    }
                    $user->Delete();
                }

                $site->Delete();
            }

            break;
    }
    parent::Delete();
}



}