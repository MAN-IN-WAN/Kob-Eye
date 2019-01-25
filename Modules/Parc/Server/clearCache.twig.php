<?php
$info= Info::getInfos($vars['Query']);
$obj = Sys::getOneData($info['Module'],$vars['Query']);
if ($obj->Proxy){
    $task  = genericClass::createInstance('Systeme','Tache');
    $task->Nom = "Reinitialisation du cache Proxy ".$obj->Nom." ( ".$obj->Id." )";
    $task->Type = "Fonction";
    $task->TaskModule = "Parc";
    $task->TaskObject = "Server";
    $task->TaskFunction = "clearCache";
    $task->TaskId = $obj->Id;
    $task->DateDebut = time();
    $task->Save();
    $out = $task->Execute();

}else{
    $out = false;
    $out->addError(array("Message"=>"Ce serveur n'est pas un serveur Proxy."));
}
$vars['obj'] = $obj;
$vars['success'] = $out;