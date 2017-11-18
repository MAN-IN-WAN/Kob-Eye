<?php
class EsxVm extends genericClass {
    public function Delete() {
        //suppression des dépots borg
        $borg = $this->getOneParent('BorgRepo');
        if ($borg)
            $borg->Delete();
        parent::Delete();
    }
    public function Save () {
        $new = false;
        if (!$this->Id){
            //nouvelle machine
            $new = true;
        }
        parent::Save();
        if (parent::Verify()) $this->checkBorgRepo();
        return true;
    }
    public function Verify($new = false) {

        //test existence du dépo borg
        if (!$new&&!$this->getOneParent('BorgRepo')){
            $this->addWarning(array('Message'=> 'Le dépôt Borg est manquant, veuillez utiliser la fonction de vérification du dépôt afin de le créer.'));
        }
        return parent::Verify();
    }
    public function checkBorgRepo() {
        //test existence d'un borg repo correspondant
        $borg = $this->getOneParent('BorgRepo');
        if (!$borg) {
            $borg = genericClass::createInstance('AbtelBackup','BorgRepo');
            $borg->Titre = "BORG: ".$this->Titre;
            $borg->Path = "/backup/borg/EsxVm/".Utils::checkSyntaxe($this->Titre);
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
    public function resetState() {
        $esx = $this->getOneParent('Esx');
        //récupération des processus qui tiennent la vm
        $out = $esx->remoteExec('vmkvsitools lsof | grep "'.$this->Titre.'"');
        //récupératio ndes proc ids
        if ($pids = preg_match("#^([0-9]+)#",$out,$pid)) {
            $esx->remoteExec('kill -9 ' . $pid[1]);
            return true;
        }else $this->AddWarning(array("Message"=>"Aucun processus trouvé."));
        return false;
    }
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
}