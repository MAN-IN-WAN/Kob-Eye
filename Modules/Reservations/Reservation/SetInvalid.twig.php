<?php


$query = explode('/',$vars['Query'],2);

$res = Sys::getOneData($query[0],$query[1]);

$vars['com'] = $res->Commentaire;

$vars['done'] = false;
if(isset($vars['confirmInvalid']) && $vars['confirmInvalid']=1){
    $res->Commentaire = $vars['Commentaire'];
    $res->setInvalid();
    $vars['done'] = true;
}