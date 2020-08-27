<?php

$args = $_SERVER["argv"];

if(count($args) < 5) die('0');

$job = Sys::getOneData('AbtelBackup',$args[4]);
$task = $job->getOneChild('Tache');

if($task->Erreur)
    echo 1;
else
    echo 0;

