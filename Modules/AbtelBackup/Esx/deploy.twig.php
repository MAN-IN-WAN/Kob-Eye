<?php
$info= Info::getInfos($vars['Query']);
$obj = Sys::getOneData($info['Module'],$vars['Query']);

$task = genericClass::createInstance('Systeme','Tache');
$task->Type = 'Fonction';
$task->Nom = 'Deploiement ';
$task->TaskModule = 'AbtelBackup';
$task->TaskObject = 'Esx';
$task->TaskId = $obj->Id;
$task->TaskFunction = 'deploy';
$task->addParent($this);
$out = $task->Save();


$vars['obj'] = $obj;
$vars['success'] = $out;