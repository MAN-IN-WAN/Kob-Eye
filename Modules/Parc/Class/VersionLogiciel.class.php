<?php
class VersionLogiciel extends genericClass {
    /**
     * override Save
     * Permet de détecter un changement de version et de générer la mise à jour des instances
     */
    public function Save(){
        $old = Sys::getOneData('Parc','VersionLogiciel/'.$this->Id);
        parent::Save();
        //on compare un changement de version éventuel
        if ($this->Version>$old->Version){
            //on déclenche la mise à jour pour les instances concernées
            $this->lookForUpdate();
        }
        return true;
    }
    /**
     * lookForUpdate
     * Recherche les instances à mettre à jour
     */
    public function lookForUpdate() {
        //vérifie d'abord qu'il s'agisse de la version la plus haute pour ce type
        $nb = Sys::getCount('Parc','VersionLogiciel/Version>'.$this->Version.'&Type='.$this->Type);
        if ($nb) return;
        //détecte les instances à mettre à jour et génère les taches de mise à jour
        $insts = Sys::getData('Parc','Instance/CurrentVersion<'.$this->Version.'&Type='.$this->Type);
        foreach ($insts as $inst){
            $inst->createUpdateTask($this);
        }
    }
    /**
     * getLastVersion
     * Récupère la dernière version en fonction du type et du nom de l'applicatif
     * @param app Nom de l'applicatif
     * @param type Type de l'applicatif
     */
    public static function getLastVersion($app,$type){
        $last = Sys::getOneData('Parc','VersionLogiciel/Nom='.$app.'&Type='.$type,0,1,'DESC','Version');
        return $last;
    }
}