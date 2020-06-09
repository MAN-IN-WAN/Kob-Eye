<?php

class PageMiniSite extends genericClass{
    public function Save() {
        $modele = $this->getOneParent('ModeleMiniSite');
        $ms = $modele->getChildren('MiniSite');
        foreach ($ms as $m){
           $m->Save(true);
        }

        return parent::Save();
    }

    public function Delete() {
        parent::Delete();

        $modele = $this->getOneParent('ModeleMiniSite');
        $ms = $modele->getChildren('MiniSite');
        foreach ($ms as $m){
            $m->Save(true);
        }

        return true;
    }


    public function getParams($full=false){
        $params = $this->getChildren('ParametreMiniSite');
        if($full){
            $modele = $this->getOneParent('ModeleMiniSite');
            $pms = $modele->getChildren('ParametreMiniSite');
            $params = $params + $pms;
        }
        return $params;
    }

    public function getParamsValues($msID,$full=false){
        $params = $this->getParams($full);
        foreach ($params as &$p){
            $val = $p->getOneChild('ValeurMiniSite/MiniSite.MiniSiteId('.$msID.')');
            $p->vms = $val->Valeur;
        }
        return $params;
    }
}
