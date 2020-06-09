<?php

class ModeleMiniSite extends genericClass{
    public function Delete() {
        $ms = $this->getOneChild('MiniSite');
        if($ms){
             return false;
        } else {
            $pages = $this->getChildren('PageMiniSite');
            foreach ($pages as $p){
                $p->Delete();
            }
            $params = $this->getChildren('ParametreMiniSite');
            foreach ($params as $pa){
                $pa->Delete();
            }
            return parent::Delete();
        }
    }
}
