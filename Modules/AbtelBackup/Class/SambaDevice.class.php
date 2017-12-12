<?php
class SambaDevice extends genericClass {
    public function Save() {
        $new = false;
        if (!$this->Id){
            //nouvelle machine
            $new = true;
        }
        if ($this->getShares(true)) {
            parent::Save();
            $this->getShares();
            return true;
        }
        if (parent::Verify()) $this->checkBorgRepo();
        return false;
    }
    public function Delete() {
        //Suppresion des partages
        $shares = $this->getChildren('SambaShare');
        foreach ($shares as $s) $s->Delete();
        //suppression des dépots borg
        $borg = $this->getOneParent('BorgRepo');
        if ($borg)
            $borg->Delete();
        parent::Delete();
    }
    public function Verify($new = false) {
        //test existence du dépo borg
        if (!$new&&!$this->getOneParent('BorgRepo')){
            $this->addWarning(array('Message'=> 'Le dépôt Borg est manquant, veuillez utiliser la fonction de vérification du dépôt afin de le créer.'));
        }
        return parent::Verify();
    }

    /**
     * @return bool
     * checkBorgRepo
     * Vzérifie l'existence d'un depot borg pour cette machine.
     */
    public function checkBorgRepo() {
        //test existence d'un borg repo correspondant
        $borg = $this->getOneParent('BorgRepo');
        if (!$borg) {
            $borg = genericClass::createInstance('AbtelBackup','BorgRepo');
            $borg->Titre = "BORG: ".$this->Titre;
            $borg->Path = "/backup/borg/SambaDevice/".Utils::checkSyntaxe($this->Titre);
            if ($borg->Save()){
                $this->addSuccess(array('Message' => 'Le dépôt Borg a été créé avec succès'));
            }
            else {
                $this->Error = array_merge($this->Error,$borg->Error);
                $this->addError(array('Message' => 'Impossible de créer le dépot borg'));
                return false;
            }
            $this->addParent($borg);
            $this->Save();
        }
        return true;
    }
    /**
     * createRestorePoint
     * Creation d'un point de restauration
     */
    public function createRestorePoint($time,$det) {
        $borg = $this->getOneParent('BorgRepo');
        $rp = genericClass::createInstance('AbtelBackup','RestorePoint');
        $rp->addParent($this);
        $rp->addParent($borg);
        $rp->Titre= "[VM] ".$this->Titre." > ".date('d/m/Y H:i:s',$time)." >  ".$time;
        $rp->Name= $time;
        $rp->Details = $det;
        $rp->Save();
    }
    /**
     * getShares
     * Rafraichit les partages disponib le sur cette machine
     */
    public function getShares($test=false) {
        if (empty($this->IP)) {
            return false;
        }
        $cmd = 'smbclient -L '.$this->IP;
        if (!empty($this->Login)&&!empty($this->Password)){
            if (!empty($this->Domain))
                $cmd.=' -U "'.$this->Domain.'\\'.$this->Login;
            else $cmd.=' -U "'.$this->Login;
            if (!empty($this->Password)) $cmd.='%'.$this->Password.'"';
            else $cmd.='"';
        }
        $cmd.=' | grep "Disk" | grep -v \'\$\'';
        try {
            $list = AbtelBackup::localExec($cmd);
        }catch (Exception $e){
            $this->AddError(Array('Message'=>'Impossible de se connecter au partage. Cmd: '.$cmd.'  Détails: '.$e->getMessage()));
            $this->Status = false;
            parent::Save();
            return false;
        }
        $this->Status = true;
        parent::Save();

        //On n'ajoute les partages qu'en dehors du mode test
        if (!$test) {
            //analyse du retour de la liste
            $list = explode("\n", $list);
            foreach ($list as $l) {
                if (preg_match("#^(.+)[\ ]+Disk[\ ]+$#", $l, $out)) {
                    $share = trim($out[1]);
                    $this->addShare($share,$this->Titre." -> ".$share);
                }
            }
        }
        return true;
    }
    private function addShare($share,$title){
        //test existence
        if (!Sys::getCount('AbtelBackup','SambaDevice/'.$this->Id.'/SambaShare/Partage='.$share)){
            $s= genericClass::createInstance('AbtelBackup','SambaShare');
            $s->Titre = $title;
            $s->Partage = $share;
            $s->addParent($this);
            $s->Save();
        }
        return true;
    }
}