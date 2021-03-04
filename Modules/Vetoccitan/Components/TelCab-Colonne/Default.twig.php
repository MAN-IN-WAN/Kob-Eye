<?php
// Récupération de l'adherent
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$LeClient = Sys::getOneData("Parc","Client/MiniSite/".$Minisite->Id);

$lAdherent = $LeClient->getOneChild("Adherent");
$lAdherent->Module="Vetoccitan";
$vars['Adresse'] = $lAdherent->Adresse." ".$lAdherent->CodePostal." ".$lAdherent->Ville." ".$lAdherent->France;

$vars['Adherent']=$lAdherent;
