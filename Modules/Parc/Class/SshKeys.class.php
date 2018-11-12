<?php
class SshKeys extends genericClass {
    /**
     * override Save
     * Détecte si c'est un revendeur, un client ou un technicien
     * Affectation automatique et définition des droits en 700
     */
    function Save() {
        if ($this->Id){
            $old = Sys::getOneData('Parc','SshKeys/'.$this->Id);
        }else $old = genericClass::createInstance('Parc','SshKeys');
        if (!$this->Id||(!sizeof($this->getParents('Client'))&&!sizeof($this->getParents('Revendeur'))&&!sizeof($this->getParents('Technicien')))) {
            if (Sys::$User->hasRole('PARC_CLIENT')) {
                //On associe au client connecté
                $this->addParent($GLOBALS['Systeme']->getRegVars('ParcClient'));
            } else if (Sys::$User->hasRole('PARC_REVENDEUR')) {
                $this->addParent($GLOBALS['Systeme']->getRegVars('ParcRevendeur'));
            } else if (Sys::$User->hasRole('PARC_TECHNICIEN')) {
                $this->addParent($GLOBALS['Systeme']->getRegVars('ParcTechnicien'));
            }
        }
        if (sizeof($this->getParents('Client'))){
            $this->Type = 'client';
        }
        if (sizeof($this->getParents('Revendeur'))) {
            $this->Type = 'revendeur';
            //modification des droits
        }
        if (sizeof($this->getParents('Technicien'))) {
            $this->setRights(0,Sys::$User->Id,1,7,0,0);
            $this->Type = 'technicien';
        }
        parent::Save();
        //return true;
        if ($this->Clef!=$old->Clef)
            return $this->createRefreshSshKeysTask();
    }

    /**
     * création de la tache de publication des clefs ssh
     */
    public function createRefreshSshKeysTask() {
        switch ($this->Type){
            case 'client':
                $obj = $this->getOneParent('Client');
                break;
            case 'revendeur':
                $obj = $this->getOneParent('Revendeur');
                break;
            case 'technicien':
                $obj = $this->getOneParent('Technicien');
                break;
        }
        //inventaire des hébergements
        $task = genericClass::createInstance('Systeme', 'Tache');
        $task->Type = 'Fonction';
        $task->Nom = 'Publication des clefs ssh pour le ' . $this->Type.' '.$obj->Nom;
        $task->TaskModule = 'Parc';
        $task->TaskObject = 'SshKeys';
        $task->TaskId = $this->Id;
        $task->TaskFunction = 'refreshSshKeys';
        $task->Save();
        return true;
    }
    /**
     * refreshSshKeys
     * publication des clefs ssh
     */
    public function refreshSshKeys($task) {
        $hosts = array();
        switch ($this->Type){
            case 'client':
                $client = $this->getOneParent('Client');
                $hosts = Sys::getData('Parc','Client/'.$client->Id.'/Host',0,100000);
                break;
            case 'revendeur':
                $revendeur = $this->getOneParent('Revendeur');
                $hosts = Sys::getData('Parc','Revendeur/'.$revendeur->Id.'/Client/*/Host',0,100000);
                break;
            case 'technicien':
                $hosts = Sys::getData('Parc','Host',0,100000);
                break;
        }
        foreach ($hosts as $host){
            $act = $task->createActivity('Ajout de la clef SSH sur '.$host->Nom,'Info');
            $this->addParent($host);
            $act->Terminate(true);
        }
        parent::Save();
        foreach ($hosts as $host){
            $act = $task->createActivity('Génération du fichier de clef sur  '.$host->Nom,'Info');
            $host->refreshSshKeys();
            $act->Terminate(true);
        }
        return true;
    }
}