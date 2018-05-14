<?php
class Court extends genericClass {
    /**
     * Surcharge class court
     * vérification et synchro des resa jour
     */

    function Save() {
        parent::Save();
        $this->checkResaJours();
        return true;
    }

    /**
     * Génération des jours de réservation.
     *
     */
    function checkResaJours() {
        //on vérifie le nombre de jour
        $nb = Sys::getCount('Reservations','Court/'.$this->Id.'/ResaJour/Date>'.time());
        if ($nb<LIMITE_RESA) {
            $d = date('d');
            $y = date('Y');
            $m = date('m');
            for ($i=$nb;$i<LIMITE_RESA;$i++){
                $jr = genericClass::createInstance('Reservations','ResaJour');
                $jr->addParent($this);
                $jr->Date = mktime(0,0,0,$m,$d,$y) + $i*86400;
                $jr->Save();
            }
        }
        return true;
    }
}