<?php
class Technologie extends genericClass {
    function getClone()  {
        //creéation d'un clone avec clonage spécifique
        $o = parent::getClone();
        $o->Url = '';
        $o->Save();
        //CLONAGE
        //clonage des puchtext
        $dons = $this->getChildren('BlockTechnologie');
        foreach ($dons as $d){
            $pt = $d->getClone();
            $pt->addParent($o);
            $pt->Save();
        }
        return $o;
    }
    function Delete() {
        //clonage des puchtext
        $dons = $this->getChildren('BlockTechnologie');
        foreach ($dons as $d){
            $d->Delete();
        }
        parent::Delete();
    }
}