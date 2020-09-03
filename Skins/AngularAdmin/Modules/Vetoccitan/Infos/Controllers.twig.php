<?php
$user = Sys::$User;
$client  = Sys::getOneData("Parc","User/".$user->Id."/Client");
$adherent = Sys::getOneData("Vetoccitan","Client/".$client->Id."/Adherent");

$vars['adherent'] = $adherent;