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


echo json_encode(array('data'=>$data));

