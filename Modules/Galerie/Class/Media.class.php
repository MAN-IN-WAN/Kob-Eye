<?php
class Media extends genericClass {
    function getClone()  {
        //creéation d'un clone avec clonage spécifique
        $o = parent::getClone();
        //PARENTS
        $dons = $this->getParents('Categorie');
        foreach ($dons as $d){
            $o->addParent($d);
            parent::Save();
        }
        $o->Url = '';
        $o->Save();
        //ASSOCIATION
        //clonage des données
        $dons = $this->getChildren('Donnee');
        foreach ($dons as $d){
            $d->addParent($o);
            $d->Save();
        }
        //CLONAGE
        //clonage des puchtext
        $dons = $this->getChildren('Donnees');
        foreach ($dons as $d){
            $pt = $d->getClone();
            $pt->addParent($o);
            $pt->Save();
        }
    }
    function Delete() {
        //clonage des puchtext
        $dons = $this->getChildren('Donnees');
        foreach ($dons as $d){
            $d->Delete();
        }

        parent::Delete();
    }
}