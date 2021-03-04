<?php

//$tasks = Sys::getData('Systeme','Tache/Nom~%Dns%',0,200000);
$tasks = Sys::getData('Systeme','Tache/Termine=1',0,200000);
foreach($tasks as $task){
    print_r($task->Id.' : '.$task->Nom.PHP_EOL);
    $task->Delete();
}