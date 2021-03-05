<?php
// Récupération de l'adherent
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$LeClient = Sys::getOneData("Parc","Client/MiniSite/".$Minisite->Id);

$lAdherent = $LeClient->getOneChild("Adherent");
$lAdherent->Module="Vetoccitan";

$ActivitesAdherent = Sys::getData('Vetoccitan', "Activite/Adherent/" . $lAdherent->Id);

// Recupération des news
$CategsAdherent = array();
$NewsAdherent = array();

// recherche Activités de l'adhérent

$donnees = $lAdherent->Recup_InfosVeto($lAdherent,"News");

$vars['LesNews']=$donnees;
