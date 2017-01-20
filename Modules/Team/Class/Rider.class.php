<?php
class Rider extends genericClass{
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
        $dons = $this->getChildren('Photo');
        foreach ($dons as $d){
            $d->addParent($o);
            $d->Save();
        }
        $dons = $this->getChildren('Post');
        foreach ($dons as $d){
            $d->addParent($o);
            $d->Save();
        }
        //CLONAGE
        //clonage des punchtext
        $dons = $this->getChildren('PunchText');
        foreach ($dons as $d){
            $pt = $d->getClone();
            $pt->addParent($o);
            $pt->Save();
        }
        //clonage des illustrations
        $dons = $this->getChildren('Caracteristique');
        foreach ($dons as $d){
            $pt = $d->getClone();
            $pt->addParent($o);
            $pt->Save();
        }
       return $o;
    }
    function Delete() {
        $dons = $this->getChildren('PunchText');
        foreach ($dons as $d){
            $d->Delete();
        }
        //clonage des illustrations
        $dons = $this->getChildren('Caracteristique');
        foreach ($dons as $d){
            $d->Delete();
        }
        parent::Delete();
    }
}