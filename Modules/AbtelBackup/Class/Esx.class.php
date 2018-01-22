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
        if (preg_match('/(.*)\n(0|-?[1-9][0-9]*)/s',$output,$out)) {
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
    /**
     * getSnapshots
     * Récupère la liste des snapshots
     * @vm
     */
    public function getSnapshots($vm) {
        try {
            $out = $this->remoteExec('vim-cmd vmsvc/snapshot.get ' . $vm->RemoteId);

        } catch (Exception $e){
            $this->addError(Array('Message'=> 'Impossible de récupérer l\'état des snapshots. Détails: '.$e->getMessage()));
            return false;
        }
        $this->addSuccess(Array('Message'=> 'Snapshot vérifié avec succès'));
        if (preg_match("#Snapshot Name [ ]+: (.+)#",$out,$snaps)){
            return $snaps;
        }else array();
        return true;
    }
    /**
     * createSnapshot
     * créé un snapshot
     * @vm
     */
    public function createSnapshot($vm,$name='deploy') {
        try {
            $this->remoteExec('vim-cmd vmsvc/snapshot.create ' . $vm->RemoteId . ' ' . $name);
        } catch (Exception $e){
            $this->addError(Array('Message'=> 'Impossible de créer un snapshot. Détails: '.$e->getMessage()));
            return false;
        }
        $this->addSuccess(Array('Message'=> 'Snapshot créé avec succès'));
        return true;
    }
    /**
     * removeAllSnapshot
     * supprime tous les snapshots un snapshot
     * @vm
     */
    public function removeAllSnapshot($vm) {
        try {
            $this->remoteExec('vim-cmd vmsvc/snapshot.removeall ' . $vm->RemoteId);
        } catch (Exception $e){
            $this->addError(Array('Message'=> 'Impossible de supprimer les snapshots. Détails: '.$e->getMessage()));
            return false;
        }
        $this->addSuccess(Array('Message'=> 'Snapshots supprimés avec succès'));
        return true;
    }
    /**
     * registervm
     * ajoute une vm à l'inventaire
     */
    public function registerVm(){
        try {
            $this->remoteExec('vim-cmd solo/registervm /vmfs/volumes/NL-SAS/BORG/BORG.vmx');
        } catch (Exception $e){
            $this->addError(Array('Message'=> 'Impossible d\'enregistrer la vm. Détails: '.$e->getMessage()));
            return false;
        }
        $this->addSuccess(Array('Message'=> 'Vm ajoutée à l\'inventaire avec succès'));
        return true;
    }
    public function enableEsxiClient() {
        try {
            $this->remoteExec('esxcli network firewall ruleset set -e true -r sshClient');
        } catch (Exception $e){
            $this->addError(Array('Message'=> 'Impossible d\'activer le sshClient. Détails: '.$e->getMessage()));
            return false;
        }
        $this->addSuccess(Array('Message'=> 'SSh client activé avec succès avec succès'));
        return true;
    }

    /**
     * createActivity
     * créé une activité en liaison avec l'esx
     * @param $title
     * @param null $obj
     * @param int $jPSpan
     * @param string $Type
     * @return genericClass
     */
    public function createActivity($title,$Type='Exec') {
        $act = genericClass::createInstance('AbtelBackup','Activity');
        $act->addParent($this);
        $act->Titre = $this->tag.date('d/m/Y H:i:s').' > '.$this->Titre.' > '.$title;
        $act->Started = true;
        $act->Type= $Type;
        $act->Progression = 0;
        $act->Save();
        return $act;
    }
    /**
     * deployNow Wrapper
     * @param $esxid
     * @return bool
     */
    public function deployNow() {
        $cmd = 'bash -c "exec nohup setsid php cron.php backup.abtel.local AbtelBackup/Esx/'.$this->Id.'/deploy.cron > /dev/null 2>&1 &"';
        exec($cmd);
        return true;
    }
    /**
     * deploy
     * Utilisataire de déploiment.
     */
    public function deploy(){
        //vm source
        $GLOBALS['Systeme']->Db[0]->query("SET AUTOCOMMIT=1");
        $vmsrc = Sys::getOneData('AbtelBackup','EsxVm/SrcVm=1');
        if (!$vmsrc) return false;
        $esxsrc = $vmsrc->getOneParent('Esx');
        $esxsrc->enableEsxiClient();
        $esx = $this;
        //On modifie le fichier fstab
        //echo "Modification du fichier fstab\r\n";
        $act = $esx->createActivity("Modification du fichier fstab",'Info');
        $act->addDetails(AbtelBackup::localExec('sudo cp /etc/fstab.mig /etc/fstab'));
        //on crée un snapshot
        $snpas = $esxsrc->getSnapshots($vmsrc);

        if (sizeof($snpas)) {
            //echo "suppression des snapshots\r\n";
            $act = $esx->createActivity("Suppression des snapshots",'Info');
            //on supprime les snapshots
            $act->addDetails($esxsrc->removeAllSnapshot($vmsrc));
        }
        //echo "Creation du snapshot deploy\r\n";
        $act = $esx->createActivity("Creation du snapshot deploy",'Info');
        //si pas de snapshot en cours
        $act->addDetails($esxsrc->createSnapshot($vmsrc, 'deploy'));
        //echo "Reset fichier fstab\r\n";
        $act = $esx->createActivity("Reset fichier fstab",'Info');
        //on remet le fichier fstab
        $act->addDetails(AbtelBackup::localExec('sudo cp /etc/fstab.bck /etc/fstab'));
        //echo "Copie de la clef privée\r\n";
        $act = $esx->createActivity("Copie de la clef privée",'Info');
        //on copie la clef privée
        $act->addDetails(AbtelBackup::localExec('scp -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -q -i /var/www/html/.ssh/id_'.$esxsrc->IP.' /var/www/html/.ssh/id_'.$esx->IP.' root@'.$esxsrc->IP.':/tmp/id_'.$esx->IP));
        //echo "Création du dossier BORG\r\n";
        $act = $esx->createActivity("Création du dossier BORG",'Info');
        //on copie le dossier vm vers le nouvel esx
        $act->addDetails($esx->remoteExec('if [ ! -d /vmfs/volumes/NL-SAS/BORG ]; then mkdir /vmfs/volumes/NL-SAS/BORG; fi'));
        //echo "Copie du fichier BORG.vmx\r\n";
        $act = $esx->createActivity("Copie du fichier BORG.vmx",'Info');
        //echo 'scp -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -q -i /tmp/id_'.$esx->IP.' /vmfs/volumes/datastore1/BORG/BORG.vmx root@'.$esx->IP.':/vmfs/volumes/NL-SAS/BORG/'."\r\n";
        $act->addDetails($esxsrc->remoteExec('scp -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -q -i /tmp/id_'.$esx->IP.' /vmfs/volumes/datastore1/BORG/BORG.vmx root@'.$esx->IP.':/vmfs/volumes/NL-SAS/BORG/'));
        //echo "Copie du fichier BORG-thin.vmdk\r\n";
        $act = $esx->createActivity("Copie du fichier BORG.vmdk",'Info');
        $act->addDetails($esxsrc->remoteExec('scp -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -q -i /tmp/id_'.$esx->IP.' /vmfs/volumes/datastore1/BORG/BORG-thin.vmdk root@'.$esx->IP.':/vmfs/volumes/NL-SAS/BORG/'));
        //echo "Copie du fichier BORG-thin-flat.vmdk\r\n";
        $act = $esx->createActivity("Copie du fichier BORG-thin-flat.vmdk",'Info');
        $act->addDetails($esxsrc->remoteExec('scp -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -q -i /tmp/id_'.$esx->IP.' /vmfs/volumes/datastore1/BORG/BORG-thin-flat.vmdk root@'.$esx->IP.':/vmfs/volumes/NL-SAS/BORG/'));
        //on modifie le fichier vmx
        //echo "Modification duy fichier BORG.vmx\r\n";
        $act = $esx->createActivity("Modification du fichier BORG.vmx",'Info');
        $vmx = $esx->remoteExec('cat /vmfs/volumes/NL-SAS/BORG/BORG.vmx');
        $vmx = str_replace('scsi0:0.fileName = "BORG-thin-000001.vmdk','scsi0:0.fileName = "BORG-thin.vmdk',$vmx);
        $vmx = preg_replace('#scsi0:1.*$#','',$vmx);
        $esx->remoteExec('echo \''.$vmx.'\' > /vmfs/volumes/NL-SAS/BORG/BORG.vmx');
        $act->addDetails($vmx);
        //echo "Ajout de la vm à l'inventaire\r\n";
        $act = $esx->createActivity("Ajout de la vm à l'inventaire",'Info');
        //on ajoute la vm à l'inventaire
        $act->addDetails($esx->registerVm());
        //echo "suppression des snapshots\r\n";
        $act = $esx->createActivity("Suppression des snapshots",'Info');
        //on supprime les snapshots
        $act->addDetails($esx->removeAllSnapshot($vmsrc));
        $act = $esx->createActivity("Déployé avec succès",'Exec');
        $act->Terminate(true);
    }

}