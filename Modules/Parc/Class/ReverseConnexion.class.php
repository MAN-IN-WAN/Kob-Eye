<?php


class ReverseConnexion extends genericClass{

    function save(){
        //génération des informations de base
        if (empty($this->CodeConnexion)) {
            $this->CodeConnexion = sprintf("%06d", rand(100000,999999));
        }
        if (empty($this->PortEcoute)) {
            $z = 5500;
            while (Sys::getCount('Parc','ReverseConnexion/PortEcoute='.$z)) $z++;
            $this->PortEcoute= $z;
        }
        parent::save();
        $this->checkGuacamoleConnection();
        parent::save();
        return true;
    }


    private function checkGuacamoleConnection(){
        $dbGuac = new PDO('mysql:host=10.0.189.12;dbname=guacamole', 'root', 'RsL5pfky', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $dbGuac->query("SET AUTOCOMMIT=1");
        $dbGuac->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($this->GuacamoleUrl == "" || $this->GuacamoleUrl == null || $this->GuacamoleId == "" || $this->GuacamoleId == null) {

            $query = "INSERT INTO `guacamole_connection` (connection_name,protocol,max_connections,max_connections_per_user) VALUES ('" . $this->Nom . " " . $this->CodeConnexion . "','vnc',NULL,NULL)";
            $q = $dbGuac->query($query);
            $lid = $dbGuac->lastInsertId();

            $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'hostname','127.0.0.1')";
            $q = $dbGuac->query($query);
            $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'port','$this->PortEcoute')";
            $q = $dbGuac->query($query);
            $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'color-depth','8')";
            $q = $dbGuac->query($query);
            $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'reverse-connect','true')";
            $q = $dbGuac->query($query);
            $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'autoretry','0')";
            $q = $dbGuac->query($query);
            $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'listen-timeout','600000')";
            $q = $dbGuac->query($query);

            $this->GuacamoleId = $lid;
            $this->GuacamoleUrl = base64_encode($lid . "\0" . 'c' . "\0" . 'mysql');

            $this->Save();
        } else {
            $query = "UPDATE `guacamole_connection` SET connection_name ='" . $this->Nom . " " . $this->CodeConnexion . "' WHERE connection_id =$this->GuacamoleId";
            $q = $dbGuac->query($query);

            $query = "REPLACE INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($this->GuacamoleId,'color-depth','8')";
            $q = $dbGuac->query($query);
            $query = "REPLACE INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($this->GuacamoleId,'reverse-connect','true')";
            $q = $dbGuac->query($query);
            $query = "REPLACE INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($this->GuacamoleId,'autoretry','0')";
            $q = $dbGuac->query($query);
            $query = "REPLACE INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($this->GuacamoleId,'listen-timeout','600000')";
            $q = $dbGuac->query($query);

            $query = "UPDATE `guacamole_connection_parameter` SET parameter_value = '$this->PortEcoute' WHERE connection_id=$this->GuacamoleId AND parameter_name='port'";
            $q = $dbGuac->query($query);

        }


        return true;
    }




    private function removeGuacamoleConnection(){

    }

    static function getFilePath() {
        $files = Sys::getData('Parc','LogicielFichier/Application=Als-Rescue',0,100,'ASC','Id');
        $out = "";
        foreach ($files as $k=>$f){
            $out.="file".$k."=".$f->Nom.'|'.$f->Fichier."\n";
        }
        return $out;
    }
    public function setBusy() {
        //$GLOABLS["Systeme"]->Db[0]->query("SET AUTOCOMMIT=1");
        $this->Busy = true;
        parent::Save();
    }
}