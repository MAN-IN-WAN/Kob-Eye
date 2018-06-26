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
        $d = date('d');
        $y = date('Y');
        $m = date('m');
        $today =  mktime(0,0,0,$m,$d,$y);
        $nb = Sys::getCount('Reservations','Court/'.$this->Id.'/ResaJour/Date>='.$today);
        //die('OK '.$nb.' / '.LIMITE_RESA.' | '.$today);
        if ($nb<LIMITE_RESA) {
            for ($i=0;$i<LIMITE_RESA;$i++) {
                if (!Sys::getCount('Reservations', 'Court/' . $this->Id . '/ResaJour/Date=' . $today + $i * 86400)){
                    $jr = genericClass::createInstance('Reservations', 'ResaJour');
                    $jr->addParent($this);
                    $jr->Date = $today + $i * 86400;
                    $jr->Save();
                }
            }
        }
        return true;
    }
}