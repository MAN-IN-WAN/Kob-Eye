<?php


$user = Sys::$User;
$toUrl = 'Home/'.$user->Id;

if($vars['Module']) $toUrl .= '/'.$vars['Module'];
if($vars['Obj']) $toUrl .= '/'.$vars['Obj'];

$File = genericClass::createInstance('Explorateur', '_Fichier');
$File->Set('Temp','FileData');
$File->Set('Url',$toUrl);

if($File->Save()){

    $vars['return']= json_encode(array(
        "status" => 1,
        "url"   => $File->Url,
        "name"  => $File->Nom,
        "error" => ''
    ));
} else{
    $vars['return']= json_encode(array(
        "status" => 0,
        "url"   => '',
        "name"  => '',
        "error" => $File->getErrors()
    ));
}



