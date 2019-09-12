<?php
class RemoteServer extends genericClass {
    private $_connection;
    public function Save() {
        parent::Save();
        //installation de la clef
        if (!$this->Status){
            if (!$this->installSshKey()) return false;
            parent::Save();
        }
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
                    $this->addWarning(array("Message" => "Connexion avec identifiant /mot de passe. publication des clefs publique /privées."));
                    $this->_connection= $connection;
                    $this->installSshKey();
                    if (!$this->Status) {
                        $this->Status = true;
                        parent::Save();
                    }
                    return true;
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
//            AbtelBackup::localExec("if [ ! -d '.ssh' ]; then mkdir .ssh; fi && chmod 750 .ssh && cd .ssh && rm -f id_". $this->IP."* && /usr/bin/ssh-keygen  -N \"\" -f id_" . $this->IP);
            //test du dossier
            AbtelBackup::localExec("if [ ! -d '.ssh' ]; then mkdir .ssh; fi && chmod 750 .ssh && cd .ssh");
            //test existence du fichier
            AbtelBackup::localExec("if [ ! -f '.ssh/id_". $this->IP."' ]; then /usr/bin/ssh-keygen  -N \"\" -f id_" . $this->IP."; fi");
            //récupération et stockage des clefs
            $stream2 =  AbtelBackup::localExec("cd .ssh && cat id_" . $this->IP);
            $this->PrivateKey = $stream2;
            $stream2 =  AbtelBackup::localExec("cd .ssh && cat id_" . $this->IP . ".pub");
            $this->PublicKey = $stream2;
            //publication de la clef
            $stream4 = $this->remoteExec("if [ ! -d /home/".$this->Login."/.ssh ]; then mkdir /home/".$this->Login."/.ssh; fi");
            $stream3 = $this->remoteExec("echo '".$this->PublicKey."' >>/home/".$this->Login."/.ssh/authorized_keys");
            $stream3 = $this->remoteExec("chmod 750 /home/".$this->Login."/.ssh/authorized_keys");
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

    public function remoteExec( $command ,$activity = null,$noerror=false){
        if (!$this->_connection)$this->Connect();
        $result = $this->rawExec( $command.';echo -en "\n$?"', $activity);
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
    private function rawExec( $command,$activity=null ){
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

}