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
        //if ($this->Version>$old->Version){
            //on déclenche la mise à jour pour les instances concernées
            //$this->lookForUpdate();
            $this->createUpdateTask();
        //}
        return true;
    }
    /**
     * lookForUpdate
     * Recherche les instances à mettre à jour
     */
    public function lookForUpdate($task) {
        //vérifie d'abord qu'il s'agisse de la version la plus haute pour ce type
        $nb = Sys::getCount('Parc','VersionLogiciel/Version>'.$this->Version.'&Type='.$this->Type);
        if ($nb) throw new Exception('Ce n\'est pas la version la plus récente');
        //détecte les instances à mettre à jour et génère les taches de mise à jour
        $nb = Sys::getCount('Parc','Instance/CurrentVersion<'.$this->Version.'&Type='.$this->Type,0,100000);
        $act = $task->createActivity('Inventaire des instances à mettre à jour : '.$nb, 'Info');
        $insts = Sys::getData('Parc','Instance/CurrentVersion<'.$this->Version.'&Type='.$this->Type,0,100000);
        $act->Terminate(true);
        $tms = 0;
        foreach ($insts as $inst){
            $tms+=25;
            $act = $task->createActivity('Création de la tache de mise à jour pour l\'instance '.$inst->Nom, 'Info');
            $inst->createUpdateTask($this);
            $act->Terminate(true);
        }
        $proxys = Sys::getData('Parc','Server/Proxy=1');
        foreach($proxys as $p){
            $task = genericClass::createInstance('Systeme', 'Tache');
            $task->Type = 'Fonction';
            $task->Nom = 'Nettoyage des caches du proxy : '.$p->Nom.' ('.$p->Id.')';
            $task->TaskModule = 'Parc';
            $task->TaskObject = 'Server';
            $task->TaskId = $p->Id;
            $task->TaskFunction = 'clearCache';
            $task->DateDebut = time() + $tms;
            $task->addParent($p);
            $task->Save();
        }


        return true;
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
    /**
     * createUpdateTask
     * Création de la tache de mise à jour
     */
    public function createUpdateTask() {
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Recherche des instances '.$this->Nom.' de type '.$this->Type.' à mettre à jour en version '.$this->Version.'';
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'VersionLogiciel';
        $task->TaskId = $this->Id;
        $task->TaskFunction = 'lookForUpdate';
        $task->addParent($this);
        $task->Save();
    }


}