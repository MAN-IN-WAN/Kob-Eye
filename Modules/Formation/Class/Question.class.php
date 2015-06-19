<?php
class Question extends genericClass{
    /**
     * getCategoriebloquante
     * Recherche dela categorie bloquante d'une question
     */
    function getCategorieBloquante() {
        $c = Sys::getOneData('Formation','Categorie/Question/'.$this->Id);
        if (is_object($c))
            return $c->getCategorieBloquante();
        else return false;
    }
}