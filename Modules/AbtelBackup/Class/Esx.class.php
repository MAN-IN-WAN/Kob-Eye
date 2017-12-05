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
                }else{
                    $this->addWarning(array("Message" => "Connexion avec identifiant /mot de passe. Veuillez générer les clefs publique /privées."));
                }
            }else{
                //connexion avec clef ssh
//                if (!ssh2_auth_pubkey_file($connection,$this->Login,$this->PublicKey,$this->PrivateKey)){
                if (!ssh2_auth_pubkey_file($connection,$this->Login,'.ssh/id_'.$this->IP.'.pub','.ssh/id_'.$this->IP)){
                    $this->Status = false;
                    parent::Save();
                    $this->addError(array("Message" => "Impossible de s'authentifier sur l'hôte " . $this->IP . ". Veuillez vérifier vos clefs publique / privée ou les regénérer."));
                    return false;
                }else{
                    if (!$this->Status) {
                        $this->Status = true;
                        parent::Save();
                    }
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
    public function Disconnect() {
        if ($this->_connection) {
            ssh2_exec($this->_connection, 'exit');
            $this->_connection = null;
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
            $stream3 = $this->remoteExec("echo '".$this->PublicKey."' >>/etc/ssh/keys-root/authorized_keys");
        }catch (Exception $e){
            $this->addError(array("Message"=>"Une erreur interne s'est produite lors de la tentative de création des clefs SSH. Détails: ".$e->getMessage()));
            $this->Status = false;
    	    parent::Save();
            return false;
        }
        //tout initialisé
        $this->Status = true;
        parent::Save();
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
                if (!preg_match("#([0-9]+)[ ]{2,100}(.*?)[ ]{2,100}(.*?)[ \t]{2,100}?([^ ]+?)[ \t]{2,100}?([^ ]+?)[ \t]{2,100}(.*)$#",$s,$out)) continue;
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
                $this->Error = array_merge($this->Error,$vm->Error);
                $this->Success = array_merge($this->Success,$vm->Success);
            }
        }catch (Exception $e){
            $this->addError(array("Message"=>"Une erreur interne s'est produite lors de la tentative de récupération de la liste des vms. Détails: ".$e->getMessage()));
            return false;
        }
        //
        return true;
    }

    public function remoteExec( $command ,$activity = null,$noerror=false, $progData = null){
        if (!$this->_connection)$this->Connect();
        $result = $this->rawExec( $command.';echo -en "\n$?"', $activity, $progData);
        if(!$noerror&& ! preg_match( "/^(0|-?[1-9][0-9]*)$/s", $result[2], $matches ) ) {
            throw new RuntimeException( "Le retour de la commande ne contenait pas le status. commande : ".$command );
        }
        if( !$noerror&&$matches[1] !== "0" ) {
            throw new RuntimeException( $result[1].$result[0], (int)$matches[1] );
        }
        return $result[0];
    }

    public function copyFile( $file ){
        if ($this->_connection){
            $this->Disconnect();
        }
        if (!$this->_connection)$this->Connect();
        //$result = ssh2_scp_send($this->_connection,$file,'/'.$file, 0644);
        // Create SFTP session
        $sftp = ssh2_sftp($this->_connection);
        $sftpStream = @fopen('ssh2.sftp://'.$sftp.'/'.$file, 'w');
        try {
            if (!$sftpStream) {
                throw new Exception("Could not open remote file: /$file");
            }
            $data_to_send = @file_get_contents($file);
            if ($data_to_send === false) {
                throw new Exception("Could not open local file: $file.");
            }
            if (@fwrite($sftpStream, $data_to_send) === false) {
                throw new Exception("Could not send data from file: $file.");
            }
            fclose($sftpStream);

        } catch (Exception $e) {
            error_log('Exception: ' . $e->getMessage());
            fclose($sftpStream);
        }
        $this->Disconnect();
        return true;//$result;
    }
    private function rawExec( $command,$activity=null , $progData = null ){
        $stream = ssh2_exec( $this->_connection, $command );
        $error_stream = ssh2_fetch_stream( $stream, SSH2_STREAM_STDERR );
        stream_set_blocking( $stream, TRUE );
        stream_set_blocking( $error_stream, TRUE );
        $data='';
        while ($buf = fread($stream, 4096)) {
            //echo $buf;
            //tentative de récupération de la progression
            if (preg_match('# ([0-9]{1,2})% #',$buf,$out)&&$activity) {
                $progress = $out[1];
                $activity->setProgression($progress);
                if($progData){
                    $progData['job']->Progression = ($progData['init'] + $progData['span']*$progress/100);
                    $progData['job']->Save();
                }
            }
            $data.=$buf;
        }
        $output = $data;//substr($data,strlen($data)-1,1);//stream_get_contents( $stream );
        $error_output = stream_get_contents( $error_stream );
        //alors récupération sur le dernier caractère
        $exit_output = 0;
        if (preg_match('/^(.*)\n(0|-?[1-9][0-9]*)$/s',$output,$out)) {
            $output = $out[1];
            $exit_output = $out[2];
        }
        fclose( $stream );
        fclose( $error_stream );
        return array( $output, $error_output,$exit_output);
    }
    public function mountNFS(){
        return $this->remoteExec("esxcfg-nas -a ABTEL_BACKUP -o ".AbtelBackup::getMyIp()." -s /backup/nfs",null,true);
    }
    public function unmountNFS(){
        return $this->remoteExec("esxcfg-nas -d ABTEL_BACKUP",null,true);
    }
}