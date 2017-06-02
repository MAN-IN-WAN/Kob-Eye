<?php


class DeviceConnexion extends genericClass{

    function save(){
        parent::save();
        $this->checkGuacamoleConnection();
        parent::save();
    }


    private function checkGuacamoleConnection(){
        $dbGuac = new PDO('mysql:host=10.0.97.5;dbname=guacamole', 'root', 'RsL5pfky', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $dbGuac->query("SET AUTOCOMMIT=1");
        $dbGuac->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $device = $this->getOneParent('Device');
        if(!$device) return false;

        //Vérif l'existence du client et le crée le cas échéant
        $cli = $device->getOneParent('Client');
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
                $cli->addError(array('Message' => 'La valeur du champ AccesPass est nulle ou non définie alors que le champ AccesUser est défini.', "Prop" => 'AccesPass'));
            }
        }


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
        }

        switch($this->Type){
            case 'RDP':
                if ($this->GuacamoleUrl == "" || $this->GuacamoleUrl == null || $this->GuacamoleId == "" || $this->GuacamoleId == null) {

                    $query = "INSERT INTO `guacamole_connection` (connection_name,protocol,parent_id,max_connections,max_connections_per_user) VALUES ('" . $this->Nom . "_rdp','rdp',NULL,NULL,NULL)";
                    $q = $dbGuac->query($query);
                    $lid = $dbGuac->lastInsertId();

                    $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'hostname','127.0.0.1')";
                    $q = $dbGuac->query($query);

                    $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'port','".$this->PortRedirectLocal."')";
                    $q = $dbGuac->query($query);


                    //Imprimante
                    $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'enable-printing','true')";
                    $q = $dbGuac->query($query);

                    if(isset($this->Login) && $this->Login !='' && $this->Login != null){
                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'username','".$this->Login."')";
                        $q = $dbGuac->query($query);
                    }
                    if(isset($this->Password) && $this->Password !='' && $this->Password != null) {
                        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'password','" . $this->Password . "')";
                        $q = $dbGuac->query($query);
                    }

                    $this->GuacamoleId = $lid;
                    $this->GuacamoleUrl = base64_encode($lid . "\0" . 'c' . "\0" . 'mysql');

                    $this->Save();
                } else {
                    $query = "UPDATE `guacamole_connection` SET connection_name ='" . $this->Nom . "_rdp' WHERE connection_id =$this->GuacamoleId";
                    $q = $dbGuac->query($query);

                    if(isset($this->Login) && $this->Login !='' && $this->Login != null){
                        $query = "UPDATE `guacamole_connection_parameter` SET parameter_value = '".$this->Login."' WHERE connection_id=$this->GuacamoleId AND parameter_name='username'";
                        $q = $dbGuac->query($query);
                    }
                    if(isset($this->Password) && $this->Password !='' && $this->Password != null) {
                        $query = "UPDATE `guacamole_connection_parameter` SET parameter_value = '".$this->Password."' WHERE connection_id=$this->GuacamoleId AND parameter_name='password'";
                        $q = $dbGuac->query($query);
                    }

                    if(isset($gid)){
                        $query = "UPDATE `guacamole_connection` SET parent_id ='" . $gid . "' WHERE connection_id =$this->GuacamoleId";
                        $q = $dbGuac->query($query);
                    }
                    $query = "REPLACE INTO INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($this->GuacamoleId,'enable-printing','true')";
                    $q = $dbGuac->query($query);

                    $query = "UPDATE `guacamole_connection_parameter` SET parameter_value = '".$this->PortRedirectLocal."' WHERE connection_id=$this->GuacamoleId AND parameter_name='port'";
                    $q = $dbGuac->query($query);
                }
                break;
            case 'VNC':
                if ($this->GuacamoleUrl == "" || $this->GuacamoleUrl == null || $this->GuacamoleId == "" || $this->GuacamoleId == null) {

                    $query = "INSERT INTO `guacamole_connection` (connection_name,protocol,parent_id,max_connections,max_connections_per_user) VALUES ('" . $this->Nom . "_vnc','vnc',NULL,NULL,NULL)";
                    $q = $dbGuac->query($query);
                    $lid = $dbGuac->lastInsertId();

                    $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'hostname','127.0.0.1')";
                    $q = $dbGuac->query($query);
                    $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'port','$this->PortRedirectLocal')";
                    $q = $dbGuac->query($query);
                    $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'password','secret')";
                    $q = $dbGuac->query($query);

                    $this->GuacamoleId = $lid;
                    $this->GuacamoleUrl = base64_encode($lid . "\0" . 'c' . "\0" . 'mysql');

                    $this->Save();
                } else {
                    $query = "UPDATE `guacamole_connection` SET connection_name ='" . $this->Nom . "_vnc' WHERE connection_id =$this->GuacamoleId";
                    $q = $dbGuac->query($query);

                    if(isset($gid)){
                        $query = "UPDATE `guacamole_connection` SET parent_id ='" . $gid . "' WHERE connection_id =$this->GuacamoleId";
                        $q = $dbGuac->query($query);
                    }


                    $query = "UPDATE `guacamole_connection_parameter` SET parameter_value = '$this->PortRedirectLocal' WHERE connection_id=$this->GuacamoleId AND parameter_name='port'";
                    $q = $dbGuac->query($query);

                }


                break;
            case 'SSH':

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




    private function removeGuacamoleConnection(){

    }

}