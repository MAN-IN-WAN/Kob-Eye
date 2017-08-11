<?php
class Esx extends genericClass {
    private $_connection;
    public function Save() {
        parent::Save();
        //installation de la clef
        if (!$this->Status){
            if (!$this->installSshKey()) return false;
            parent::Save();
        }
        //inventaire
        if (!$this->getInventory()) return false;
        return true;
    }
    public function Connect() {
        //test connectivite ssh
        try {
            $connection = ssh2_connect($this->IP, 22);
            if (!$connection){
                $this->addError(array("Message"=>"Impossible de contacter l'hôte ".$this->IP));
                return false;
            }
            if (!$this->Status) {
                if (!ssh2_auth_password($connection, $this->Login, $this->Password)) {
                    $this->addError(array("Message" => "Impossible de s'authentifier sur l'hôte " . $this->IP . ". Veuillez vérifier vos identifiants."));
                    return false;
                }
            }else{
                //connexion avec clef ssh
//                if (!ssh2_auth_pubkey_file($connection,$this->Login,$this->PublicKey,$this->PrivateKey)){
                if (!ssh2_auth_pubkey_file($connection,$this->Login,'.ssh/id_'.$this->IP.'.pub','.ssh/id_'.$this->IP)){
                    $this->addError(array("Message" => "Impossible de s'authentifier sur l'hôte " . $this->IP . ". Veuillez vérifier vos clefs publique / privée ou les regénérer."));
                    return false;
                }else{
                    $this->addSuccess(array("Message" => "Connexion réussie avec les clefs publique / privée ."));
                }
            }
            /*$stream1= ssh2_exec($connection, trim("hostname")."\n");*/
            //stream_set_blocking($stream1, true);
            $this->_connection= $connection;
            return $connection;
        }catch (Exception $e){
            $this->addError(array("Message"=>"Une erreur interne s'est produite lors de la tentative de connexion à l'hôte ".$this->IP));
            return false;
        }
        return true;
    }
    public function Verify() {
        $this->Connect();
        return parent::Verify();
    }
    public function installSshKey() {
        //connexion par login/pass
        if (!$this->_connection)$this->Connect();
        //génération des clefs publiques / privées
        try {
            AbtelBackup::localExec("if [ ! -d '.ssh' ]; then mkdir .ssh; fi && cd .ssh && rm -f id_". $this->IP."* && /usr/bin/ssh-keygen  -N \"\" -f id_" . $this->IP);
            //récupération et stockage des clefs
            $stream2 =  AbtelBackup::localExec("cd .ssh && cat id_" . $this->IP);
            $this->PrivateKey = $stream2;
            $stream2 =  AbtelBackup::localExec("cd .ssh && cat id_" . $this->IP . ".pub");
            $this->PublicKey = $stream2;
            //publication de la clef
            $stream3 = $this->remoteExec("echo ".$this->PublicKey." >>/etc/ssh/keys-root/authorized_keys");
        }catch (Exception $e){
            $this->addError(array("Message"=>"Une erreur interne s'est produite lors de la tentative de création des clefs SSH. Détails: ".$e->getMessage()));
            return false;
        }
        //tout initialisé
        $this->Status = true;
        return true;
    }
    public function getInventory(){
        if (!$this->_connection)$this->Connect();
        try {
            $stream3 = $this->remoteExec("vim-cmd vmsvc/getallvms");
            //traitement du tableau
            $stream3 = explode("\n",$stream3);
            array_shift($stream3);
            array_pop($stream3);
            foreach ($stream3 as $s){
                $s = preg_match("#([0-9]+)[ ]{2,30}(.*?)[ ]{2,30}(.*?)[ ]{2,30}(.*?)[ ]{2,30}(.*?)$#",$s,$out);
                //création des vmsen vérifiant qu'elle n'existe pas déjà , sinon mise à jour.
                $vm = Sys::getOneData('AbtelBackup','Esx/'.$this->Id.'/EsxVm/RemoteId='.$out[1]);
                if (!$vm){
                    //alors creation de la vm
                    $vm  = genericClass::createInstance('AbtelBackup','EsxVm');
                    $vm->AddParent($this);
                    $vm->RemoteId = $out[1];
                    $this->addSuccess(array('Message'=>'Ajout de la vm '.$out[2]));
                }else{
                    $this->addWarning(array('Message'=>'Modification de la vm '.$out[2]));
                }
                $vm->Titre = $out[2];
                $vm->FileName = $out[3];
                $vm->VmType = $out[4];
                $vm->VmVersion = $out[5];
                $vm->Save();
            }
        }catch (Exception $e){
            $this->addError(array("Message"=>"Une erreur interne s'est produite lors de la tentative de récupération de la liste des vms. Détails: ".$e->getMessage()));
            return false;
        }
        //
        return true;
    }

    private function remoteExec( $command ){
        $result = $this->rawExec( $command.';echo -en "\n$?"' );
        if( ! preg_match( "/^(.*)\n(0|-?[1-9][0-9]*)$/s", $result[0], $matches ) ) {
            throw new RuntimeException( "Le retour de la commande ne contenait pas le status. commande : ".$command );
        }
        if( $matches[2] !== "0" ) {
            throw new RuntimeException( $result[1], (int)$matches[2] );
        }
        return $matches[1];
    }

    private function rawExec( $command ){
        $stream = ssh2_exec( $this->_connection, $command );
        $error_stream = ssh2_fetch_stream( $stream, SSH2_STREAM_STDERR );
        stream_set_blocking( $stream, TRUE );
        stream_set_blocking( $error_stream, TRUE );
        $output = stream_get_contents( $stream );
        $error_output = stream_get_contents( $error_stream );
        fclose( $stream );
        fclose( $error_stream );
        return array( $output, $error_output );
    }
}