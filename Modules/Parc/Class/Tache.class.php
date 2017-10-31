<?php
class Tache extends genericClass{
    var $_apache = null;
    public function Execute() {
        try {
            $serv = $this->getParents('Server');
            $serv = $serv[0];
            $this->Demarre = true;
            $this->Retour = "";
            parent::Save();

            /*set_include_path('Class/Lib/phpseclib');
            include_once('Net/SSH2.php');
            include_once('File/ANSI.php');
            $ansi = new File_ANSI();

            $ssh = new Net_SSH2($serv->DNSNom);
            $ssh->enableQuietMode();
            if (!$ssh->login($serv->SshUser, $serv->SshPassword)) {
                $this->Erreur = true;
                $this->addRetour(' - Connexion refusée. Identifiants incorrects.');
                parent::Save();
                return false;
            }
            $cmds = explode("\n", $this->Contenu);
            print_r($cmds);
            die();
            $ansi->appendString($ssh->read());
            foreach ($cmds as $cmd) {
                $ssh->write($cmd . "\n");
                $ssh->setTimeout(5);
                $ansi->appendString($ssh->read());
            }
            $this->Retour = $ansi->getHistory();*/
            $this->Retour = '<pre width="80" style="color: white; background: black">';
            $connection = ssh2_connect($serv->DNSNom, 22);
            ssh2_auth_password($connection,$serv->SshUser, $serv->SshPassword);
            $cmds = explode("\n", $this->Contenu);
            foreach ($cmds as $cmd) {
                $this->addRetour('==> '.$cmd);
                $stream1= ssh2_exec($connection, trim($cmd)."\n");
                stream_set_blocking($stream1, true);
                $this->addRetour(stream_get_contents($stream1));
            }
            $this->addRetour('</pre>');
        }catch (Exception $e){
            $this->Erreur=true;
            $this->Retour = print_r($e,true);
        }
        $this->Termine = true;

        //déclenche la mise à jour Apache
        $ap = $this->getApache();
        if ($ap){
            $ap->Save(true);
        }

        parent::Save();
        return true;
    }
    private function addRetour($msg){
        //recherche du apache pour callback
        $ap = $this->getApache();
        if ($ap){
            $ap->callBackTask($msg);
        }
        $this->Retour.=$msg."\n";
    }
    private function getApache(){
        if (!$this->_apache) {
            $ap = Sys::getOneData('Parc', 'Apache/Tache/' . $this->Id);
            $this->_apache = $ap;
        }
        return $this->_apache;
    }
}