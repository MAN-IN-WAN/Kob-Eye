<?php


$query = explode('/',$vars['Query'],2);

$res = Sys::getOneData($query[0],$query[1]);

if($res->Valide) {
    $res->sendMail();
    die('<div class="alert alert-success">Le mail a été renvoyé avec succès</div>');

}

if($res->Attente){
    $res->sendPendingMail();
    die('<div class="alert alert-success">Le mail a été renvoyé avec succès</div>');
}

$res->sendInvalidMail();
die('<div class="alert alert-success">Le mail a été renvoyé avec succès</div>');

