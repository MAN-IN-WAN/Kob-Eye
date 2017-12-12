<?php

$vars['identifier'] = 'AbtelBackupActivity';

$vars['User'] = Sys::$User;

$vars['Urls'] = array(
    'Activity'=>Sys::getMenu('AbtelBackup/Activity'),
    'VmJob'=>Sys::getMenu('AbtelBackup/VmJob'),
    'SambaJob'=>Sys::getMenu('AbtelBackup/SambaJob'),
    'RemoteJob'=>Sys::getMenu('AbtelBackup/RemoteJob')
);
