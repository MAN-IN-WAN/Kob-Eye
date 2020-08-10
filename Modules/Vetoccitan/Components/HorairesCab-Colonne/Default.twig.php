<?php
// Récupération de l'adherent
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$LeClient = Sys::getOneData("Parc","Client/MiniSite/".$Minisite->Id);

$lAdherent = $LeClient->getOneChild("Adherent");
$lAdherent->Module="Vetoccitan";

$vars['Adherent']=$lAdherent;

// Recupération des horaires de l'adhérent
$HorairesAdherent = $lAdherent->getOneChild("Horaire");
$vars['Horaires']=$HorairesAdherent;

$jourd="";
$jourf="";

if ($HorairesAdherent->Lundi) $jourd = 'Lundi';
if ($HorairesAdherent->Mardi && $jourd == "") $jourd = 'Mardi';
if ($HorairesAdherent->Mercredi && $jourd == "") $jourd = 'Mercredi';
if ($HorairesAdherent->Jeudi && $jourd == "") $jourd = 'Jeudi';
if ($HorairesAdherent->Vendredi && $jourd == "") $jourd = 'Vendredi';
if ($HorairesAdherent->Samedi && $jourd == "") $jourd = 'Samedi';
if ($HorairesAdherent->Dimanche && $jourd == "") $jourd = 'Dimanche';

if ($HorairesAdherent->Dimanche && $jourf == "") $jourf = 'Dimanche';
if ($HorairesAdherent->Samedi && $jourf == "") $jourf = 'Samedi';
if ($HorairesAdherent->Vendredi && $jourf == "") $jourf = 'Vendredi';
if ($HorairesAdherent->Jeudi && $jourf == "") $jourf = 'Jeudi';
if ($HorairesAdherent->Mercredi && $jourf == "") $jourf = 'Mercredi';
if ($HorairesAdherent->Mardi && $jourf == "") $jourf = 'Mardi';
if ($HorairesAdherent->Lundi && $jourf == "") $jourf = 'Lundi';
// notre liste des Horaires

$vars['Debut']=$jourd;
$vars['Fin']=$jourf;
