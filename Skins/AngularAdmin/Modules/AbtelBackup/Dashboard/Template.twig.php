<?php

function humanReadable($Mo){
    $units = array('Mo','Go','To','Po');

    $pow = 0;
    while($Mo > 1024){
        $Mo /= 1024;
        $pow++;
    }
    $Mo = number_format($Mo,2);
    return $Mo . $units[$pow];
}



$vars['NbVmWareJob'] = Sys::getCount('AbtelBackup','VmJob/Enabled=1');
$vars['NbSambaJob'] = Sys::getCount('AbtelBackup','SambaJob/Enabled=1');
$vars['NbHyperVJob'] = Sys::getCount('AbtelBackup','HyperJob/Enabled=1');
$vars['NbRemoteJob'] = Sys::getCount('AbtelBackup','RemoteJob/Enabled=1');



$vars['Urls'] = array(
        'Activity'=>Sys::getMenu('Systeme/Activity'),
        'EsxVm'=>Sys::getMenu('AbtelBackup/EsxVm'),
        'SambaShare'=>Sys::getMenu('AbtelBackup/SambaShare')
);
