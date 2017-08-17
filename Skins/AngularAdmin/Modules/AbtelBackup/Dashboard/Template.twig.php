<?php

function jobsToday(){
    $weekDays =array('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');

    $curDay = $weekDays[(int)date('w')];
    $curMonthDay = (int)date('d');
    $curMonth = (int)date('m');

    $nbTodayVm = Sys::getCount('AbtelBackup','VmJob/Enabled=1&(!Mois=*+Mois='.$curMonth.'!)&(!Jour=*+Jour='.$curMonthDay.'!)&(!(!Lundi=0&Mardi=0&Mercredi=0&Jeudi=0&Vendredi=0&Samedi=0&Dimanche=0!)+(!'.$curDay.'=1!)!)');
    if(!$nbTodayVm) $nbTodayVm =0;

    $nbTodaySamba = 2; //Sys::getCount('AbtelBackup','SambaJob/Enabled=1&(!Mois=*+Mois='.$curMonth.'!)&(!Jour=*+Jour='.$curMonthDay.'!)&(!(!Lundi=0&Mardi=0&Mercredi=0&Jeudi=0&Vendredi=0&Samedi=0&Dimanche=0!)+(!'.$curDay.'=1!)!)');
    if(!$nbTodaySamba) $nbTodaySamba =0;

    $nbTodayRemote = 1; //Sys::getCount('AbtelBackup','RemoteJob/Enabled=1&(!Mois=*+Mois='.$curMonth.'!)&(!Jour=*+Jour='.$curMonthDay.'!)&(!(!Lundi=0&Mardi=0&Mercredi=0&Jeudi=0&Vendredi=0&Samedi=0&Dimanche=0!)+(!'.$curDay.'=1!)!)');
    if(!$nbTodayRemote) $nbTodayRemote =0;

    return $nbTodayVm+$nbTodayRemote+$nbTodaySamba;
}

$vars['NbRunning'] = Sys::getCount('AbtelBackup','Activity/Started=1&Success=0&Errors=0');
$vars['NbToday'] = jobsToday();
$vars['NbVm'] = Sys::getCount('AbtelBackup','EsxVm');
$vars['NbSharing'] = Sys::getCount('AbtelBackup','SambaShare');
