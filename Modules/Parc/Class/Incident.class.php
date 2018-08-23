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
    public static function createIncident($title,$detail,$parent,$code,$severity=1,$solved = false){
        //test si exsite alors on modifie
        $incident = $parent->getChildren('Incident/Code='.$code.'&Solved=0');
        if (!$incident) {
            if ($solved) return true;
            //si existe pas, alors le crée
            $incident = genericClass::createInstance('Parc', 'Incident');
        }
        $incident->Titre = $title;
        $incident->Details = $detail;
        $incident->Severity = $severity;
        $incident->Acknowledge = false;
        $incident->Code = $code;
        $incident->Solved = $solved;
        $incident->addParent($parent);
        $incident->Save();
        return $incident;
    }
}