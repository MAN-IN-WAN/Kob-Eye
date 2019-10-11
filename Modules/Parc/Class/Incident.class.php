<?php
class Incident extends genericClass {
    /**
     * createIncident
     * Création d'un incident
     * @param $title
     * @param $detail
     * @param int $severity
     * @param null $parent genericClass
     * @return genericClass
     */
    public static function createIncident($title,$detail,$parent,$code,$identifiant,$severity=1,$solved = false){
        //test si exsite alors on modifie
        $incident = Sys::getOneData('Parc','Incident/Solved=0&Code='.$code.'&Identifiant='.$identifiant);
        if (!$incident) {
            if ($solved) return true;
            //si existe pas, alors le crée
            $incident = genericClass::createInstance('Parc', 'Incident');
        }else $incident;
        $incident->Titre = $title;
        $incident->Details = $detail;
        $incident->Severity = $severity;
        $incident->Acknowledge = false;
        $incident->Code = $code;
        $incident->Identifiant = $identifiant;
        $incident->Solved = $solved;
        $incident->addParent($parent);
        $incident->Save();
        return $incident;
    }
}