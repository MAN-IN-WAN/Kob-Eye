<?php
class Device extends genericClass{
    /*
     *
     * @override
     */
    function save(){
        $this->checkGuacamoleConnection();

        parent::save();
    }

    function getConfig($uuid) {
        $exists = Sys::getOneData('Parc','Device/Uuid='.$uuid);
        if ($exists){
            $port_rdp = 12000+$exists->Id;
            $port_vnc = 22000+$exists->Id;
            $exists->Nom = $_GET["name"];
            $exists->Description = $_GET["os"];
            $exists->ConnectionType = 'R'.$port_rdp.'=localhost:3389,R'.$port_vnc.'=localhost:5900';
            $exists->Save();
            return $exists->ConnectionType;
        }else{
            //creation du device
            $obj = genericClass::createInstance('Parc','Device');
            $obj->Nom = $_GET["name"];
            $obj->Description = $_GET["os"];
            $obj->Uuid = $uuid;
            $obj->Save();
            $port_rdp = 12000+$obj->Id;
            $port_vnc = 22000+$obj->Id;
            $obj->ConnectionType = 'R'.$port_rdp.'=localhost:3389,R'.$port_vnc.'=localhost:5900';
            $obj->Save();
            return $obj->ConnectionType;
        }
    }


    private function checkGuacamoleConnection(){
        if($this->GuacamoleUrl!='') return true;

        $dbGuac = new PDO('mysql:host=10.0.97.5;dbname=guacamole','root','RsL5pfky',array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $dbGuac->query("SET AUTOCOMMIT=1");
        $dbGuac->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        //Connection RDP
        $query = "INSERT INTO `guacamole_connection` (connection_name,protocol,parent_id,max_connections,max_connections_per_user) VALUES ('".$this->Nom."_rdp','rdp',NULL,NULL,NULL)";
        $q = $dbGuac->query($query);
        $lid = $dbGuac->lastInsertId();

        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'hostname','127.0.0.1')";
        $q = $dbGuac->query($query);
        $port = 12000+$this->Id;
        $query = "INSERT INTO `guacamole_connection_parameter` (connection_id,parameter_name,parameter_value) VALUES ($lid,'port','$port')";
        $q = $dbGuac->query($query);





//        //Connection VNC
//        $query = "INSERT INTO `guacamole_connection` ('connection_name','protocol','parent_id','max_connections','max_connections_per_user') VALUES ($this->Nom.'_vnc','vnc',NULL,NULL,NULL)";
//        try{
//            $q = $this->dbAbtel->query($query);
//
//        }catch (Exception $e){
//            $this->error .= print_r($e,true);
//        }




    }

    private function removeGuacamoleConnection(){

    }

}