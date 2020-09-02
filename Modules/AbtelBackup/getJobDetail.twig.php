<?php

$args = $_SERVER["argv"];

if(count($args) < 5) die('0');

$job = Sys::getOneData('AbtelBackup',$args[4]);

$o = genericClass::createInstance($job->Module,$job->ObjectType);
$fields = $o->getElementsByAttribute('form','',true);

$data = array(
    'Id'=>$job->Id,
    'Type'=>$job->ObjectType
);

foreach($fields as $f){
    $data[$f['name']] = $job->{$f['name']};
}

$data['VmCount'] = -1;
$data['VmSize'] = -1;
$data['BackupSize'] = -1;

if($job->ObjectType == "VmJob"){
    $vms = $job->getChildren('EsxVm');
    $data['VmCount'] = sizeof($vms);
    $data['VmSize'] = 0;
    $data['BackupSize'] = 0;
    foreach ($vms as $vm){
        $data['VmSize'] += $vm->Size;
        $data['BackupSize'] += $vm->BackupSize;
    }
}
$running = $job->getOneChild('Tache/Demarre=1&&Termine=0&&Erreur=0');
$data['JobStatus'] = $running ? '1' : '0';

if(isset($args[5])){
    switch($args[5]){
        case 1:
            echo $data['BackupSize'];
            return;
            break;
        case 2:
            echo $data['JobStatus'];
            return;
            break;
        case 3:
            echo $data['Type'];
            return;
            break;
        case 4:
            echo $data['VmCount'];
            return;
            break;
        case 5:
            echo $data['VmSize'];
            return;
            break;
        default:
            echo null;
            return;
    }
} else {
    echo json_encode(array('data'=>$data));
}


