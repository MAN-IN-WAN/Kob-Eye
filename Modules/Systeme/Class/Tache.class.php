<?php
class Tache extends genericClass{
    var $_apache = null;

    public function Execute($force=false) {
        Sys::autocommitTransaction();

        if(!empty($this->ThreadId) && $this->ThreadId != getmypid()) {
            //on vérifie que le thread est toujours la
            if (Systeme::isProcessAlive($this->ThreadId))
                return true;
            $this->Reset();
            return $this->Execute();
        }

        //on rafraichit les infos
        if ($this->Demarre&&!$force) return true;

        /*if ($force){
            $this->Demarre = false;
            $this->Termine = false;
            $this->Erreur = false;
            $this->ThreadId = '';
        }*/

        $this->ThreadId = getmypid();
        $this->DateDebut = time();
        parent::Save();

        sleep(1);

        Sys::$Modules['Systeme'] -> Db -> clearLiteCache();
        $upd = Sys::getOneData('Systeme','Tache/'.$this->Id);
        if($upd->ThreadId != $this->ThreadId) return true;


        switch ($this->Type) {

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
                        if ($out) {
                            $this->Progression = 100;
                            $this->Termine = true;
                        }else
                            $this->Erreur = true;
                        parent::Save();
                    }catch (Throwable $e){
                        $this->addRetour('ERROR: '.$e->getMessage().' ligne: '.$e->getLine().' code: '.$e->getCode().' file: '.$e->getFile().' trace: '.$e->getTraceAsString());
                        $this->Erreur = true;
                        parent::Save();
                    }
                }else{
                    //execution statique
                    try {
                        call_user_func($this->TaskObject.'::'.$this->TaskFunction,$this);
                        $this->Progression = 100;
                    }catch (Throwable $e){
                        $this->addRetour('ERROR: '.$e->getMessage().' ligne: '.$e->getLine().' code: '.$e->getCode().' file: '.$e->getFile().' trace: '.$e->getTraceAsString());
                        $this->Erreur = true;
                    }
                    $this->Termine = true;
                    parent::Save();
                }
                break;

/*            default:
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
                break;*/
        }
        $this->DateFin=time();
        parent::Save();
        return true;
    }


    public function addRetour($msg){

        if($this->TaskModule == 'Parc') {
            //recherche du apache pour callback
            $ap = $this->getApache();
            if ($ap) {
                $ap->callBackTask($msg);
            }
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




    public function Delete(){
        $acts = $this->getChildren('Activity');
        foreach ($acts as $act) {
            $act->Delete();
        }
        return parent::Delete();
    }
    /**
     * Reset
     * Reset all datas
     */
    public function Reset() {
        $this->Termine=0;
        $this->Demarre=0;
        $this->Erreur=0;
        $this->ThreadId='';
        $this->DateDebut = 0;
        return $this->Save();
    }

    /**
     * Terminate
     */
    public function Terminate($end = false){
        $this->Termine=$end;
        $this->Erreur=!$end;
        $this->Progression = 100;
        return $this->Save();
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
    public function createActivity($title, $Type = 'Exec',$jPSpan=0){
        $act = genericClass::createInstance('Systeme', 'Activity');
        $act->addParent($this);
        $act->Titre = $this->tag . date('d/m/Y H:i:s') . ' > ' . $this->Titre . ' > ' . $title;
        $act->Started = true;
        $act->Type = $Type;
        $act->Progression = 0;
        $act->ProgressStart = $this->Progression;
        $act->ProgressSpan = $jPSpan;
        $act->Save();
        return $act;
    }
}