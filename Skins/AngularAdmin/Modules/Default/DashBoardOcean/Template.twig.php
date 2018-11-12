<?php

$incidents = Sys::getData('IncidentClient','Incident',0,50000);


$vars['totInci'] = 0;
$vars['runInci'] = 0;
$vars['takInci'] = 0;
$vars['doneInci'] = 0;
$vars['typoCount'] = array();
$vars['mTime'] = 0;
$totalTime = 0;
$vars['usr'] = Sys::$User;
$vars['cli'] = IncidentClient::$CurrentClient;
$vars['tech'] = IncidentClient::$CurrentTechnicien;

$mens = $vars['usr']->getMenus();
foreach($mens as $men) {
    if ($men->Alias =="IncidentClient/Incident") {
        $vars['menu'] = $men;
        $par = $vars['menu']->getOneParent('Menu');
        $vars['mUrl'] = '#/' . $par->Url . '/' . $vars['menu']->Url;
        break;
    }
    $cMens = $men->getChildren('Menu');
    foreach($cMens as $cMen) {
        if ($cMen->Alias =="IncidentClient/Incident") {
            $vars['menu'] = $cMen;
            $par = $vars['menu']->getOneParent('Menu');
            $vars['mUrl'] = '#/' . $par->Url . '/' . $vars['menu']->Url;
            break;
        }
    }
}



foreach ( $incidents as $inci) {
    $vars['totInci']++;
    $st = $inci->getOneParent('ParametresEtat');
    if ($st->Cloture == 1){
        $vars['doneInci']++;
        $start = $inci->tmsCreate;
        $end = $inci->DateCloture ? $inci->DateCloture : $inci->tmsEdit;
        $du = $end - $start;
        $totalTime += $du;
    }
    else {
        $vars['runInci']++;
        if ($st->Defaut != 1) $vars['takInci']++;
    }

    $ty = $inci->getOneParent('ParametresTypo');
    if($ty){
        if(!isset($vars['typoCount'][$ty->Nom])) $vars['typoCount'][$ty->Nom] = 0;
        $vars['typoCount'][$ty->Nom]++;
    }

}

$vars['mTime'] = floor($totalTime/$vars['doneInci']);


if(!$totalTime){
    $vars['mTime'] = 'Données insuffisantes'; 
} else {
    $time = $vars['mTime'];

    $days = floor($time / (24 * 60 * 60));
    $hours = floor(($time - ($days * 24 * 60 * 60)) / (60 * 60));
    $minutes = floor(($time - ($days * 24 * 60 * 60) - ($hours * 60 * 60)) / 60);
    $seconds = ($time - ($days * 24 * 60 * 60) - ($hours * 60 * 60) - ($minutes * 60)) % 60;

    $vars['mTime'] = $days . ' jour(s) ' . sprintf("%02d", $hours) . ' heure(s) ' . sprintf("%02d", $minutes) . ' minute(s) ' . sprintf("%02d", $seconds) . ' seconde(s)';
}