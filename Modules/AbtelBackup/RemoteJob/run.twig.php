<?php
$info= Info::getInfos($vars['Query']);
$obj = Sys::getOneData($info['Module'],$vars['Query']);

$task = genericClass::createInstance('Systeme', 'Tache');
$task->Type = 'Fonction';
$task->Nom = 'Job Remote :' . $obj->Titre;
$task->TaskModule = 'AbtelBackup';
$task->TaskObject = 'RemoteJob';
$task->TaskId = $obj->Id;
$task->TaskFunction = 'run';
$task->addParent($obj);
$out = $task->Save();

$vars['obj'] = $obj;
$vars['success'] = $out;