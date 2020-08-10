<?php
// Récupération de l'adherent
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$LeClient = Sys::getOneData("Parc","Client/MiniSite/".$Minisite->Id);

$lAdherent = $LeClient->getOneChild("Adherent");
$lAdherent->Module="Vetoccitan";

// Recupération des publicités

$CategsAdherent= array();
$PubsAdherent= array();

// recherche Activités de l'adhérent
$ActivitesAdherent = Sys::getData('Vetoccitan',"Activite/Adherent/".$lAdherent->Id);


// recherche Catégories pour chque Activités de l'adhérent
foreach ($ActivitesAdherent as $ACP) {

    $CategsActivite = $ACP->getChildren("Categorie/Filtre=1");
    foreach ($CategsActivite as $Cats) {
        foreach ($CategsAdherent as $Cads) {
            if ($Cats->Id==$Cads->Id) {continue 2;}
        }
        $CategsAdherent[]= $Cats;
    }
}

$vars['Filtres']=$CategsAdherent;

$vars['ChoixCateg']=0;
if ($_GET['C'] ) {
    $vars['ChoixCateg']=$_GET['C'];
}

$vars['menuUrl'] = Sys::$CurrentMenu->Url;
