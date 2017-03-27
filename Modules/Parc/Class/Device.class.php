<?php
class Device extends genericClass{
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
            $port_rdp = 12000+$exists->Id;
            $port_vnc = 22000+$exists->Id;
            $obj->ConnectionType = 'R'.$port_rdp.'=localhost:3389,R'.$port_vnc.'=localhost:5900';
            $obj->Save();
            return $obj->ConnectionType;
        }
    }
}