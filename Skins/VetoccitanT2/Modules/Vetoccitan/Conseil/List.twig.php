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
if ($_GET["C"]) {

    // CAS DU FILTRE DEMANDÉ
    $ConseilsAdherent = Sys::getData('Vetoccitan', "Categorie/".$_GET["C"]."/Conseil/Display=1");
    // RESTE À EXCLURE LES INFOS QUI SONT POUR D'AUTRES ADHÉRENTS !!

} else {
    $ActivitesAdherent = Sys::getData('Vetoccitan', "Activite/Adherent/" . $lAdherent->Id);

    // Recupération des conseils
    $CategsAdherent = array();
    $ConseilsAdherent = array();

    // recherche Activités de l'adhérent
    $ActivitesAdherent = $lAdherent->getParents("Activite");

    // recherche Catégories pour chque Activités de l'adhérent
    foreach ($ActivitesAdherent as $ACP) {
        $CategsActivite = $ACP->getChildren("Categorie");
        foreach ($CategsActivite as $Cats) {
            foreach ($CategsAdherent as $Cads) {
                if ($Cats->Id == $Cads->Id) {
                    continue 2;
                }
            }
            $CategsAdherent[] = $Cats;
        }
    }

    // Recherche des Conseil  liées aux catégories
    foreach ($CategsAdherent as $CATAD) {
        $ConseilsCat = $CATAD->getChildren("Conseil/Display=1");
        foreach ($ConseilsCat as $CatCons) {
            foreach ($ConseilsAdherent as $Cads) {
                if ($CatCons->Id == $Cads->Id) {
                    continue 2;
                }
            }
            $ConseilsAdherent[] = $CatCons;
        }
    }

    // recherche les conseils liés directement à l'adhérent
    $ConsAdherentDirect = $lAdherent->getChildren("Conseil/Display=1");

    // ajout aux autres conseils
    foreach ($ConsAdherentDirect as $Cons) {
        foreach ($ConseilsAdherent as $Cads) {
            if ($Cons->Id==$Cads->Id) {continue 2;}
        }
        $ConsAdherentDirect[]= $Cons;
    }
    $ConseilsAdherent = $ConsAdherentDirect;

    // retrait des conseils liés à un autre adhérent
    $ConsExcluAutreAdherent = array();
    $ConsToutAdherent = array();

    foreach ($ConseilsAdherent as $Cons) {
        $ConsParent = Sys::getData('Vetoccitan', "Adherent/Conseil/" . $Cons->Id);
        foreach ($ConsParent as $Cads) {
            $exclu = 0 ;
            if ($Cads->Id != $lAdherent->Id) {
                $exclu = 1 ;
            }
        }
        if ($exclu == 0 ) {
            $ConsToutAdherent[]=$Cons;
        }
    }

    $ConseilsAdherent =$ConsToutAdherent ;


}


// Recherche les Médias de nos conseils
foreach ($ConseilsAdherent as &$CONS) {
    $ConsImg = $CONS->getOneChild("Media");
    $CONS->Media =$ConsImg;
}
// notre liste de conseils avec leurs médias
$vars['Conseils']=$ConseilsAdherent;
