<?php


var_dump($_FILES);

die('blarg');

$user = Sys::$User;
$toUrl = 'Home/'.$user->Id;

if($vars['Module']) $toUrl .= $vars['Module'];
if($vars['obj']) $toUrl .= $vars['obj'];

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



