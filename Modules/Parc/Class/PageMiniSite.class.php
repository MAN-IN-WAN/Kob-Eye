<?php

class PageMiniSite extends genericClass{
    public function Save() {
        $modele = $this->getOneParent('ModeleMiniSite');
        $ms = $modele->getChildren('MiniSite');
        foreach ($ms as $m){
           $m->Save(true);
        }

        return parent::save();
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
            // remplacé pour gérer les valeurs avec du html
            $p->vms = $val->Valeur;
            $p->valms = $val;
        }
        return $params;
    }
}
