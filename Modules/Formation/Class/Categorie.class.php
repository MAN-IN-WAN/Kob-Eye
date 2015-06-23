<?php
class FormationCategorie extends genericClass{
    /**
     * getCategoriebloquante
     * Recherche dela categorie bloquante d'une question
     */
    function getCategorieBloquante() {
        if ($this->Bloque) return $this;
        else{
            $c = Sys::getOneData('Formation','Categorie/Categorie/'.$this->Id);
            if (is_object($c))
                return $c->getCategorieBloquante();
            else return false;
        }
    }
    function getCategoryBreadcrumb() {
        $out = array();
        $c = Sys::getOneData('Formation','Categorie/Categorie/'.$this->Id);
        if (is_object($c))
           $out = array_merge($out,$c->getCategoryBreadcrumb());
        if ($this->Afficher)
            array_push($out,$this);
        return $out;
    }
}