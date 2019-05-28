<?php
class BorgRepo extends genericClass {
    public function Delete() {
        //suppression des points de restauration
        $rps = $this->getChildren('RestorePoint');
        foreach ($rps as $rp)$rp->Delete();
        //suppression du dépot borg physique
        if (!AbtelBackup::localExec('/usr/bin/rm -Rf "'.$this->Path.'"'))
            $this->addError(array('Message'=>'Impossible de supprimer le dossier '.$this->Path.'. Détail: '.$e->getMessage()));
        parent::Delete();
    }
    public function Save() {
        $new = false;
        if (!$this->Id){
            //nouvelle machine
            $new = true;
        }
        parent::Save();
        if ($new){
            //creation du depot
            return $this->createDepot();
        }else{
            //update size
            $this->updateSize();
        }
        return true;
    }
    public function getName() {
        $p = explode('/',$this->Path);
        return $p[sizeof($p)-1];
    }
    public function createDepot () {
        //test de l'existence du chemin
        if (!file_exists($this->Path)){
            //création du chemin
            try {
                mkdir($this->Path, 0777, true);
            }catch (Exception $e){
                $this->addError(array('Message'=>'Impossible de créer le dossier '.$this->Path.'. Détail: '.$e->getMessage()));
                return false;
            }
        }
        //intialisation du dépot
        try {
            $cmd = 'export BORG_PASSPHRASE=\''.BORG_SECRET.'\' && borg init --encryption=repokey-blake2 ' . $this->Path;
            AbtelBackup::localExec($cmd);
        }catch (Exception $e) {
            $this->addError(array('Message'=>'Impossible de créer le dépôt  Borg '.$this->Path.'. Cmd: '.$cmd.' Détail: '.$e->getMessage()));
            return false;
        }
        return true;
    }
    public function updateSize() {
        try {
            $this->Size = AbtelBackup::getSize($this->Path);
            parent::Save();
        }catch (Exception $e) {
            $this->addError(array('Message'=>'Impossible de créer le dépôt  Borg '.$this->Path.'. Cmd: '.$cmd.' Détail: '.$e->getMessage()));
            return false;
        }
    }
}