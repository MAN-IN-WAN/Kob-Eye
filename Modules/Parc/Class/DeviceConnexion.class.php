<?php


class DeviceConnexion extends genericClass{

    function save(){
        parent::save();
        $dev = $this->getOneParent('Device');
        if ($dev)
            $this->checkGuacamoleConnection();
        parent::save();
        return true;
    }


    private function checkGuacamoleConnection(){
        $guac_serv = Sys::getOneData('Parc','Server/Guacamole=1');

        $dbGuac = new PDO('mysql:host='.$guac_serv->InternalIP.';dbname=guacamole', $guac_serv->guacAdminUser, $guac_serv->guacAdminPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $dbGuac->query("SET AUTOCOMMIT=1");
        $dbGuac->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $device = $this->getOneParent('Device');
        if(!$device) return false;

        //Vérif l'existence du client et le crée le cas échéant
        $cli = $device->getOneParent('Client');
        $usr = array();

        //klog::l('$cli',$cli);
        if (isset($cli->AccesUser) && $cli->AccesUser != '' && $cli->AccesUser != null && isset($cli->AccesPass) && $cli->AccesPass != '' && $cli->AccesPass != null) {
            $query = "SELECT * FROM `guacamole_user` WHERE username = '" . $cli->AccesUser . "'";
            $q = $dbGuac->query($query);
            $result = $q->fetchALL(PDO::FETCH_ASSOC);
            if (sizeof($result) > 0) {
                $usr = $result;
            } else {
                $query = "INSERT INTO `guacamole_user` (username,password_hash,password_date) VALUES ('" . $cli->AccesUser . "',UNHEX(SHA2('" . $cli->AccesPass . "',256)),'" . date("Y-m-d H:i:s") . "')";
                $q = $dbGuac->query($query);

                $query = "SELECT * FROM `guacamole_user` WHERE username = '" . $cli->AccesUser . "'";
                $q = $dbGuac->query($query);
                $result = $q->fetchALL(PDO::FETCH_ASSOC);
                $usr = $result;
            }
        } elseif (isset($cli->AccesUser) && $cli->AccesUser != '' && $cli->AccesUser != null && (!isset($cli->AccesPass) || $cli->AccesPass == '' || $cli->AccesPass == null)) {
            $cli->addError(array('Message' => 'La valeur du champ AccesPass est nulle ou non définie alors que le champ AccesUser est défini.', "Prop" => 'AccesPass'));
        }


        $contacts = $this->getParents('Contact');
        foreach($contacts as $contact){
            if (isset($contact->AccesUser) && $contact->AccesUser != '' && $contact->AccesUser != null && isset($contact->AccesPass) && $contact->AccesPass != '' && $contact->AccesPass != null) {
                $query = "SELECT * FROM `guacamole_user` WHERE username = '" . $contact->AccesUser . "'";
                $q = $dbGuac->query($query);
                $result = $q->fetchALL(PDO::FETCH_ASSOC);
                if (sizeof($result) > 0) {
                    $usr = array_merge($usr,$result);
                } else {
                    $query = "INSERT INTO `guacamole_user` (username,password_hash,password_date) VALUES ('" . $contact->AccesUser . "',UNHEX(SHA2('" . $contact->AccesPass . "',256)),'" . date("Y-m-d H:i:s") . "')";
                    $q = $dbGuac->query($query);

                    $query = "SELECT * FROM `guacamole_user` WHERE username = '" . $contact->AccesUser . "'";
                    $q = $dbGuac->query($query);
                    $result = $q->fetchALL(PDO::FETCH_ASSOC);
                    $usr = array_merge($usr,$result);
                }
            } elseif (isset($cli->AccesUser) && $cli->AccesUser != '' && $cli->AccesUser != null && (!isset($cli->AccesPass) || $cli->AccesPass == '' || $cli->AccesPass == null)) {
                $contact->addError(array('Message' => 'La valeur du champ AccesPass est nulle ou non définie alors que le champ AccesUser est défini.', "Prop" => 'AccesPass'));
            }
        }


        $gid=null;
        if(isset($cli->Nom)){

            $query = "SELECT * FROM `guacamole_connection_group` WHERE connection_group_name = '" . strtoupper(str_replace('\'',' ',$cli->Nom)) . "'";
            $q = $dbGuac->query($query);
            $result = $q->fetchALL(PDO::FETCH_ASSOC);
            if (sizeof($result) > 0) {
                $grp = $result[0];
                $gid = $grp['connection_group_id'];
            } else {
                $query = "INSERT INTO `guacamole_connection_group` (connection_group_name) VALUES ('" . strtoupper(str_replace('\'',' ',$cli->Nom)) . "')";
                $q = $dbGuac->query($query);
                $gid = $dbGuac->lastInsertId();
            }
        }else $gid = 1;

        //Verif au cas ou la connection a été supprimée à l'arrache dans guacamole
        if($this->GuacamoleId){
            $query = "SELECT * FROM `guacamole_connection` WHERE connection_id = $this->GuacamoleId";
            $q = $dbGuac->query($query);
            $result = $q->fetchALL(PDO::FETCH_ASSOC);
            if(!count($result)){
                $this->GuacamoleUrl = '';
                $this->GuacamoleId = '';
            }
        }


        switch($this->Type){
            case 'RDP':
                if ($this->GuacamoleUrl == "" || $this->GuacamoleUrl == null || $this->GuacamoleId == "" || $this->GuacamoleId == null) {
                    $query = "SELECT connection_id FROM `guacamole_connection` WHERE connection_name='" . addslashes($this->Nom) . "' and protocol = 'rdp'";
                    if (!empty($gid)) $query.=" AND parent_id=$gid";
                    $q = $dbGuac->query($query);
                    $result = $q->fetchALL(PDO::FETCH_ASSOC);

                    if (!sizeof($result)) {

                        $query = "INSERT INTO `guacamole_connection` (connection_name,protocol,parent_id,max_connections,max_connections_per_user) VALUES ('" . $this->Nom . "','rdp',$gid,NULL,NULL)";
                        $q = $dbGuac->query($query);
                        $lid = $dbGuac->lastInsertId();

                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'hostname','127.0.0.1')";
                        $q = $dbGuac->query($query);

                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'port','" . $this->PortRedirectLocal . "')";
                        $q = $dbGuac->query($query);


                        //Imprimante
                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'enable-printing','true')";
                        $q = $dbGuac->query($query);
                        //Drive
                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'enable-drive','true')";
                        $q = $dbGuac->query($query);
                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'drive-path','/home/dakota')";
                        $q = $dbGuac->query($query);
                        //Clavier
                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'server-layout','fr-fr-azerty')";
                        $q = $dbGuac->query($query);

                        if (isset($this->Login) && $this->Login != '' && $this->Login != null) {
                            $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'username','" . $this->Login . "')";
                            $q = $dbGuac->query($query);
                        }
                        if (isset($this->Password) && $this->Password != '' && $this->Password != null) {
                            $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'password','" . $this->Password . "')";
                            $q = $dbGuac->query($query);
                        }
                    }else $lid = $result[0]['connection_id'];

                    $this->GuacamoleId = $lid;
                    $this->GuacamoleUrl = base64_encode($lid . "\0" . 'c' . "\0" . 'mysql');

                    $this->Save();
                } else {
                    $query = "UPDATE `guacamole_connection` SET connection_name ='" . $this->Nom . "' WHERE connection_id =$this->GuacamoleId";
                    $q = $dbGuac->query($query);

                    if(isset($this->Login) && $this->Login !='' && $this->Login != null){
                        if (strpos($this->Login,'\\')){
                            $tt = explode('\\',$this->Login);
                            $query = "REPLACE INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES (" . $this->GuacamoleId . ",'username','" . $tt[1] . "')";
                            $q = $dbGuac->query($query);
                            $query = "REPLACE INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES (" . $this->GuacamoleId . ",'domain','" . $tt[0] . "')";
                            $q = $dbGuac->query($query);
                        }else {
                            $query = "REPLACE INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES (" . $this->GuacamoleId . ",'username','" . $this->Login . "')";
                            $q = $dbGuac->query($query);
                        }
                    }
                    if(isset($this->Password) && $this->Password !='' && $this->Password != null) {
                        $query = "REPLACE INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES (".$this->GuacamoleId.",'password','".$this->Password."')";
                        $q = $dbGuac->query($query);
                    }

                    if(isset($gid)&&$gid!=null){
                        $query = "UPDATE `guacamole_connection` SET parent_id ='" . $gid . "' WHERE connection_id =$this->GuacamoleId";
                        try {
                            $q = $dbGuac->query($query);
                        }catch (Exception $e){

                        }
                    }
                    $query = "REPLACE INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($this->GuacamoleId,'enable-printing','true')";
                    $q = $dbGuac->query($query);
                    //Drive
                    $query = "REPLACE INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($this->GuacamoleId,'enable-drive','true')";
                    $q = $dbGuac->query($query);
                    $query = "REPLACE INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($this->GuacamoleId,'drive-path','/home/dakota')";
                    $q = $dbGuac->query($query);
                    //Clavier
                    $query = "REPLACE INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($this->GuacamoleId,'server-layout','fr-fr-azerty')";
                    $q = $dbGuac->query($query);


                    $query = "UPDATE `guacamole_connection_parameter` SET parameter_value = '".$this->PortRedirectLocal."' WHERE connection_id=$this->GuacamoleId AND parameter_name='port'";
                    $q = $dbGuac->query($query);
                }
                break;
            case 'VNC':
                if ($this->GuacamoleUrl == "" || $this->GuacamoleUrl == null || $this->GuacamoleId == "" || $this->GuacamoleId == null) {
                    //on vérifie l'existence en base
                    $query = "SELECT connection_id FROM `guacamole_connection` WHERE connection_name='" . addslashes($this->Nom) . "' and protocol = 'vnc'";
                    if (!empty($gid)) $query.=" AND parent_id=$gid";

                    try{
                        $q = $dbGuac->query($query);
                    }catch (Exception $e){
                        echo 'erreur '.$e->getMessage().' requete '.$query;
                    }
                    $result = $q->fetchALL(PDO::FETCH_ASSOC);

                    if (!sizeof($result)) {
                        $query = "INSERT INTO `guacamole_connection` (connection_name,protocol,parent_id,max_connections,max_connections_per_user) VALUES ('" . addslashes($this->Nom) . "','vnc',$gid,NULL,NULL)";
                        try{
                            $q = $dbGuac->query($query);
                        }catch (Exception $e){
                            echo 'erreur '.$e->getMessage().' requete '.$query;
                        }
                        $lid = $dbGuac->lastInsertId();
                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'hostname','127.0.0.1')";
                        $q = $dbGuac->query($query);
                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'port','$this->PortRedirectLocal')";
                        $q = $dbGuac->query($query);
                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'password','secret')";
                        $q = $dbGuac->query($query);
                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'color-depth','8')";
                        $q = $dbGuac->query($query);
                    }else $lid = $result[0]['connection_id'];


                    $this->GuacamoleId = $lid;
                    $this->GuacamoleUrl = base64_encode($lid . "\0" . 'c' . "\0" . 'mysql');

                    $this->Save();
                } else {
                    $query = "UPDATE `guacamole_connection` SET connection_name ='" . addslashes($this->Nom) . "' WHERE connection_id =$this->GuacamoleId";
                    $q = $dbGuac->query($query);

                    if(isset($gid)&&$gid!=null){
                        $query = "UPDATE `guacamole_connection` SET parent_id ='" . $gid . "' WHERE connection_id =$this->GuacamoleId";
                        try {
                            $q = $dbGuac->query($query);
                        }catch (Exception $e){

                        }
                    }
                    $query = "REPLACE INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($this->GuacamoleId,'color-depth','8')";
                    $q = $dbGuac->query($query);

                    $query = "UPDATE `guacamole_connection_parameter` SET parameter_value = '$this->PortRedirectLocal' WHERE connection_id=$this->GuacamoleId AND parameter_name='port'";
                    $q = $dbGuac->query($query);

                }


                break;
            case 'SSH':
                if ($this->GuacamoleUrl == "" || $this->GuacamoleUrl == null || $this->GuacamoleId == "" || $this->GuacamoleId == null) {
                    $query = "SELECT connection_id FROM `guacamole_connection` WHERE connection_name='" . addslashes($this->Nom) . "' and protocol = 'ssh'";
                    if (!empty($gid)) $query.=" AND parent_id=$gid";
                    $q = $dbGuac->query($query);
                    $result = $q->fetchALL(PDO::FETCH_ASSOC);

                    if (!sizeof($result)) {
                        $query = "INSERT INTO `guacamole_connection` (connection_name,protocol,parent_id,max_connections,max_connections_per_user) VALUES ('" . $this->Nom . "','ssh',$gid,NULL,NULL)";
                        $q = $dbGuac->query($query);
                        $lid = $dbGuac->lastInsertId();

                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'hostname','localhost')";
                        $q = $dbGuac->query($query);
                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'port','$this->PortRedirectLocal')";
                        $q = $dbGuac->query($query);
                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'username','$this->Login')";
                        $q = $dbGuac->query($query);
                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'password','$this->Password')";
                        $q = $dbGuac->query($query);
                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'color-scheme','white-black')";
                        $q = $dbGuac->query($query);
                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'enable-sftp','true')";
                        $q = $dbGuac->query($query);
                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'font-name','Terminus')";
                        $q = $dbGuac->query($query);
                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'font-size','11')";
                        $q = $dbGuac->query($query);
                    }else $lid = $result[0]['connection_id'];


                    $this->GuacamoleId = $lid;
                    $this->GuacamoleUrl = base64_encode($lid . "\0" . 'c' . "\0" . 'mysql');

                    $this->Save();
                } else {
                    $query = "UPDATE `guacamole_connection` SET connection_name ='" . $this->Nom . "' WHERE connection_id =$this->GuacamoleId";
                    $q = $dbGuac->query($query);

                    if(isset($gid)&&$gid!=null){
                        $query = "UPDATE `guacamole_connection` SET parent_id ='" . $gid . "' WHERE connection_id =$this->GuacamoleId";
                        try {
                            $q = $dbGuac->query($query);
                        }catch (Exception $e){

                        }
                    }

                    $query = "UPDATE `guacamole_connection_parameter` SET parameter_value = '$this->PortRedirectLocal' WHERE connection_id=$this->GuacamoleId AND parameter_name='port'";
                    $q = $dbGuac->query($query);

                    $query = "UPDATE `guacamole_connection_parameter` SET parameter_value = '$this->Login' WHERE connection_id=$this->GuacamoleId AND parameter_name='username'";
                    $q = $dbGuac->query($query);

                    $query = "UPDATE `guacamole_connection_parameter` SET parameter_value = '$this->Password' WHERE connection_id=$this->GuacamoleId AND parameter_name='password'";
                    $q = $dbGuac->query($query);

                    $query = "UPDATE `guacamole_connection_parameter` SET parameter_value = 'white-black' WHERE connection_id=$this->GuacamoleId AND parameter_name='color-scheme'";
                    $q = $dbGuac->query($query);

                }

                break;
            case 'Telnet':

                break;
            case 'Https':

                break;

            default:
                return false;
        }



        if (isset($usr)) {
            foreach($usr as $us){
                $query = "INSERT IGNORE INTO `guacamole_connection_permission` (user_id,connection_id,permission) VALUES ('" . $us['user_id'] . "','" . $this->GuacamoleId . "','READ')";
                $q = $dbGuac->query($query);
            }
        }

        return true;
    }



    public function Delete() {
        $this->removeGuacamoleConnection();
        parent::Delete();
    }

    private function removeGuacamoleConnection(){
        $guac_serv = Sys::getOneData('Parc','Server/Guacamole=1');

        $dbGuac = new PDO('mysql:host='.$guac_serv->InternalIP.';dbname=guacamole', $guac_serv->guacAdminUser, $guac_serv->guacAdminPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

        $dbGuac->query("SET AUTOCOMMIT=1");
        $dbGuac->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //Suppression des parametres
        $query = "DELETE FROM `guacamole_connection_parameter` WHERE connection_id=$this->GuacamoleId";
        $q = $dbGuac->query($query);

        //Suppression des permission
        $query = "DELETE FROM `guacamole_connection_permission` WHERE connection_id=$this->GuacamoleId";
        $q = $dbGuac->query($query);

        //Suppression des sharing profile + params + perms
        //TODO

        //Suppression de la connection
        $query = "DELETE FROM `guacamole_connection` WHERE connection_id=$this->GuacamoleId";
        $q = $dbGuac->query($query);
    }

}