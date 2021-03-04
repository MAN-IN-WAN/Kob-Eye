<?php

// Récupération de l'adherent
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$LeClient = Sys::getOneData("Parc","Client/MiniSite/".$Minisite->Id);

$lAdherent = $LeClient->getOneChild("Adherent");
$lAdherent->Module="Vetoccitan";

// Recupération des conseils
$CategsAdherent= array();
$ConseilsAdherent= array();

// recherche Activités de l'adhérent
$donnees = $lAdherent->Recup_InfosVeto($lAdherent,"Conseil");
// notre liste de conseils avec leurs médias
$vars['Conseils']=$donnees;

