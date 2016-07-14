<?php
class ProductsProduit extends genericClass {
    function getClone()  {
        //creéation d'un clone avec clonage spécifique
        $o = parent::getClone();
        $o->Url = '';
        $o->Save();
        //ASSOCIATION
        //clonage des données
        $dons = $this->getChildren('Donnee');
        foreach ($dons as $d){
            $d->addParent($o);
            $d->Save();
        }
        $dons = $this->getChildren('Technologie');
        foreach ($dons as $d){
            $d->addParent($o);
            $d->Save();
        }
        $dons = $this->getChildren('Produit');
        foreach ($dons as $d){
            $d->addParent($o);
            $d->Save();
        }
        //CLONAGE
        //clonage des puchtext
        $dons = $this->getChildren('PunchText');
        foreach ($dons as $d){
            $pt = $d->getClone();
            $pt->addParent($o);
            $pt->Save();
        }
        //clonage des illustrations
        $dons = $this->getChildren('Illustration');
        foreach ($dons as $d){
            $pt = $d->getClone();
            $pt->addParent($o);
            $pt->Save();
        }
        //clonage des descriptions
        $dons = $this->getChildren('Description');
        foreach ($dons as $d){
            $pt = $d->getClone();
            $pt->addParent($o);
            $pt->Save();
        }
        //clonage des ranges
        $dons = $this->getChildren('Range');
        foreach ($dons as $d){
            $pt = $d->getClone();
            $pt->addParent($o);
            $pt->Save();
        }
        //clonage des sizes
        $dons = $this->getChildren('Size');
        foreach ($dons as $d){
            $pt = $d->getClone();
            $pt->addParent($o);
            $pt->Save();
        }
        return $o;
    }
    function Delete() {
        //clonage des puchtext
        $dons = $this->getChildren('PunchText');
        foreach ($dons as $d){
            $d->Delete();
        }
        //clonage des illustrations
        $dons = $this->getChildren('Illustration');
        foreach ($dons as $d){
            $d->Delete();
        }
        //clonage des descriptions
        $dons = $this->getChildren('Description');
        foreach ($dons as $d){
            $d->Delete();
        }
        //clonage des ranges
        $dons = $this->getChildren('Range');
        foreach ($dons as $d){
            $d->Delete();
        }
        //clonage des sizes
        $dons = $this->getChildren('Size');
        foreach ($dons as $d){
            $d->Delete();
        }

        parent::Delete();
    }
}