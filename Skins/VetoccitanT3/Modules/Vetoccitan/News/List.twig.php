<?php

// Récupération de l'adherent
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$LeClient = Sys::getOneData("Parc","Client/MiniSite/".$Minisite->Id);

$lAdherent = $LeClient->getOneChild("Adherent");
$lAdherent->Module="Vetoccitan";

// Recupération des News
$CategsAdherent= array();
$NewsAdherent= array();

$donnees = $lAdherent->Recup_InfosVeto($lAdherent,"News");
// notre liste de services avec leurs médias

$vars['News']=$donnees;
//var_dump($vars['News']);
