<?php


$vmJobs = Sys::getData('AbtelBackup','VmJob/Enabled=1');
$sambaJobs = Sys::getData('AbtelBackup','SambaJob/Enabled=1');
$remoteJobs = Sys::getData('AbtelBackup','Remote/Enabled=1');

$jobs = array();

foreach($vmJobs as $job){
    $jobs[] = array(
        '{#JOBID}'=>'VmJob/'.$job->Id,
        '{#JOBNAME}'=>$job->Titre
    );
}

foreach($sambaJobs as $job){
    $jobs[] = array(
        '{#JOBID}'=>'SambaJob/'.$job->Id,
        '{#JOBNAME}'=>$job->Titre
    );
}

foreach($remoteJobs as $job) {
    $jobs[] = array(
        '{#JOBID}' => 'RemoteJob/'.$job->Id,
        '{#JOBNAME}' => $job->Titre
    );
}


echo  json_encode(array('data'=>$jobs));


