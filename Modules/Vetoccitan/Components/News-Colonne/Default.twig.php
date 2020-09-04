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

// Recherche des News liées aux catégories
foreach ($CategsAdherent as $CATAD) {
    $NewsCat = $CATAD->getChildren("News/Display=1");
    foreach ($NewsCat as $CatN) {
        foreach ($NewsAdherent as $Nads) {
            if ($CatN->Id == $Nads->Id) {
                continue 2;
            }
            // ajout de news non catégorisées
        }
        $NewsAdherent[] = $CatN;
    }
}

// recherche les NEWS liés directement à l'adhérent
$NewsAdherentDirect = $lAdherent->getChildren("News/Display=1");

// ajout aux autres NEWS
foreach ($NewsAdherentDirect as $News) {
    foreach ($NewsAdherent as $Nads) {
        if ($News->Id==$Nads->Id) {continue 2;}
    }
    $NewsAdherent[]= $News;
}

$NewsExcluAutreAdherent = array();
$NewsToutAdherent = array();
foreach ($NewsAdherent as $New) {
    $NewParent = Sys::getData('Vetoccitan', "Adherent/News/" . $New->Id);
    foreach ($NewParent as $Nads) {
        $exclu = 0 ;
        if ($Nads->Id != $lAdherent->Id) {
            $NewsExcluAutreAdherent[]=$Nads;
            $exclu = 1 ;
        } else {
            $NewsToutAdherent[]=$New;
            $exclu = 0 ;
        }
    }
    if ($exclu == 0 ) {
        $NewsToutAdherent[]=$New;
    }
}



$vars['LesNews']=$NewsAdherent;
