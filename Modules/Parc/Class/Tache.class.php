<?php
class Tache extends genericClass{
    var $_apache = null;
    public function Execute($force=false) {
        //on rafraichit les infos
        if ($this->Demarre&&!$force) return true;
        Sys::autocommitTransaction();
        switch ($this->Type) {
            default:
                try {
                    $serv = $this->getParents('Server');
                    $serv = $serv[0];
                    $this->Demarre = true;
                    $this->Retour = "";
                    parent::Save();
                    $this->Retour = '<pre width="80" style="color: white; background: black">';
                    $connection = ssh2_connect($serv->DNSNom, 22);
                    ssh2_auth_password($connection, $serv->SshUser, $serv->SshPassword);
                    $cmds = explode("\n", $this->Contenu);
                    foreach ($cmds as $cmd) {
                        $this->addRetour('==> ' . $cmd);
                        $stream1 = ssh2_exec($connection, trim($cmd) . "\n");
                        stream_set_blocking($stream1, true);
                        $this->addRetour(stream_get_contents($stream1));
                    }
                    $this->addRetour('</pre>');
                } catch (Exception $e) {
                    $this->Erreur = true;
                    $this->Retour = print_r($e, true);
                }
                $this->Termine = true;

                //déclenche la mise à jour Apache
                $ap = $this->getApache();
                if ($ap) {
                    $ap->Save(true);
                }

                parent::Save();
            break;
            case "Fonction":
                $this->Demarre = true;
                $this->Retour = "";
                parent::Save();
                if ($this->TaskId>0){
                    //execution objet
                    $obj = Sys::getOneData($this->TaskModule,$this->TaskObject.'/'.$this->TaskId);
                    try {
                        $out = $obj->{$this->TaskFunction}($this);
                        $this->addRetour($out);
                        $this->Termine = true;
                        parent::Save();
                    }catch (Exception $e){
                        $this->addRetour('ERROR: '.$e->getMessage());
                        $this->Erreur = true;
                        parent::Save();
                    }
                }else{
                    //execution statique
                    try {
                        call_user_func($this->TaskObject.'::'.$this->TaskFunction,$this);
                    }catch (Exception $e){
                        $this->addRetour('ERROR: '.$e->getMessage());
                        $this->Erreur = true;
                    }
                    $this->Termine = true;
                    parent::Save();
                }
            break;
        }
        return true;
    }
    public function addRetour($msg){
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
    /**
     * createActivity
     * créé une activité
     * @param $title
     * @param null $obj
     * @param int $jPSpan
     * @param string $Type
     * @return genericClass
     */
    public function createActivity($title, $Type = 'Exec'){
        $act = genericClass::createInstance('Parc', 'Activity');
        $act->addParent($this);
        $act->Titre = $this->tag . date('d/m/Y H:i:s') . ' > ' . $this->Titre . ' > ' . $title;
        $act->Started = true;
        $act->Type = $Type;
        $act->Progression = 0;
        $act->Save();
        return $act;
    }

}