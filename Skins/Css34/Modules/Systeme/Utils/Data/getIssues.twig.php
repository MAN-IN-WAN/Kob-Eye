<?php
require_once ROOT_DIR."Class/Lib/Zabbix.class.php";

if(isset($cli))
    $objectType = 'HostGroup';
if(isset($uuid))
    $objectType = 'Host';


$object = null;
switch($objectType){
    case 'HostGroup':
        $object = Zabbix::getClientGroup($uuid);
        break;
    case 'Host':
        $object = Zabbix::getHostFromUuid($uuid);
        break;
}


$var['issues'] = Zabbix::getProblems($object);
$var['total'] = count($var['issues']);
