<?php
class ResaJour extends genericClass {
    var $_court;
    function Save(){
        parent::Save();
        //recherche des objets
        $this->_court = $this->getOneParent('Court');
        //géneration du Libelle
        $this->Libelle = date('d/m/Y',$this->Date)." pour le lieu ".$this->_court->Titre;
        parent::Save();
    }
    /**
     * update duration
     * Met les durée à jour et son booleen de disponibilité
     */
    function updateDuration() {

    }
}