<?php
class Tache extends genericClass{
    public function Execute() {
        $serv = $this->getParents('Server');
        $serv=$serv[0];
        $this->Demarre = true;
        $this->Retour = "";
        parent::Save();

        set_include_path('Class/Lib/phpseclib');
        include_once('Net/SSH2.php');
        include_once('File/ANSI.php');
        $ansi = new File_ANSI();

        $ssh = new Net_SSH2($serv->DNSNom);
        if (!$ssh->login($serv->SshUser, $serv->SshPassword)) {
            $this->Erreur = true;
            $this->addRetour(' - Connexion refusée. Identifiants incorrects.');
            parent::Save();
            return false;
        }
        
        $ansi->appendString($ssh->read());
        $ssh->write($this->Contenu."\n");
        $ansi->appendString($ssh->read());
        $this->Retour = $ansi->getHistory();
        $this->Termine = true;
        parent::Save();
    }
    private function addRetour($msg){
        $this->Retour.=$msg."\n";
    }
}