<?php
class EsxVm extends genericClass {
    public function Save () {
        $new = false;
        if (!$this->Id){
            //nouvelle machine
            $new = true;
        }
        parent::Save();
        if ($new) $this->checkBorgRepo();
        return true;
    }
    public function Verify() {
        parent::Verify();
        //test existence du dépo borg
        if (!$this->getOneParent('BorgRepo')){
            $this->addWarning(array('Message'=> 'Le dépôt Borg est manquant, veuillez utiliser la fonction de vérification du dépôt afin de le créer.'));
        }
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
                $this->addParent($borg);
                $this->Save();
            }
            else {
                $this->Error = array_merge($this->Error,$borg->Error);
                return false;
            }

        }
        return true;
    }
}