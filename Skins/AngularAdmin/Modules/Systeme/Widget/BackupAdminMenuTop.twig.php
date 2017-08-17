<?php

$vars['identifier'] = 'AbtelBackupActivity';

$vars['User'] = Sys::$User;
/*
$vars['ECount'] = 0;

$vmErrors = array();
$sambaErrors = array();
$remoteErrors = array();*/


/*$jobsErrors = Sys::getData('Abtelbackup/Activite/Erreur>0&Resolved=0&Acknowledge=0');
foreach($jobsErrors as $jobError){
    $vmJob = $jobError->getOneParent('VmJob');
    if($vmJob){
        $vm= $vmjob;
        $vmErrors[] = array('texte'=>$jobError->Erreur,'code'=>$jobError->CodeErreur,'severite'=>$jobError->SeveriteErreur, 'vm'=>$vm);
        continue;
    }

    $sambaJob = $jobError->getOneParent('SambaJob');
    if($sambaJob){
        $samba= $sambaJob;
        $sambaErrors[] = array('texte'=>$jobError->Erreur,'code'=>$jobError->CodeErreur,'severite'=>$jobError->SeveriteErreur, 'vm'=>$sambaJob);
        continue;
    }

    $remoteJob = $jobError->getOneParent('RemoteJob');
    if($remoteJob){
        $remote= $remoteJob;
        $remoteErrors[] = array('texte'=>$jobError->Erreur,'code'=>$jobError->CodeErreur,'severite'=>$jobError->SeveriteErreur, 'vm'=>$remoteJob);
        continue;
    }
}*/

/*$vars['ECount'] += sizeof($vmErrors) + sizeof($sambaErrors) + sizeof($remoteErrors);

if(!sizeof($vmErrors)) $vmErrors[] = array('texte'=>'Aucune Erreur','code'=>'0000','severite'=>null, 'vm'=>null);
if(!sizeof($sambaErrors)) $sambaErrors[] = array('texte'=>'Aucune Erreur','code'=>'0000','severite'=>null, 'samba'=>null);
if(!sizeof($remoteErrors)) $remoteErrors[] = array('texte'=>'Aucune Erreur','code'=>'0000','severite'=>null, 'vremotem'=>null);

$vars['Errors'] = array(
                        'vmErrors'=> $vmErrors,
                        'sambaErrors' => $sambaErrors,
                        'remoteErrors' => $remoteErrors
                    );*/