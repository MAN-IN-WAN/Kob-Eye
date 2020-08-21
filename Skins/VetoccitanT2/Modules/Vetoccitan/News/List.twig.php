<?php

// Récupération de l'adherent
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$LeClient = Sys::getOneData("Parc","Client/MiniSite/".$Minisite->Id);

$lAdherent = $LeClient->getOneChild("Adherent");
$lAdherent->Module="Vetoccitan";

// Recupération des News
$CategsAdherent= array();
$NewsAdherent= array();

// recherche Activités de l'adhérent
if ($_GET["C"]) {
    // CAS DU FILTRE DEMANDÉ
    $NewsAdherent = Sys::getData('Vetoccitan', "Categorie/".$_GET["C"]."/News/Display=1");
    // RESTE À EXCLURE LES INFOS QUI SONT POUR D'AUTRES ADHÉRENTS !!
} else {
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

    // retrait des news liés à un autre adhérent
    $NewsExcluAutreAdherent = array();
    $NewsToutAdherent = array();

    foreach ($NewsAdherent as $New) {
        $NewParent = Sys::getData('Vetoccitan', "Adherent/News/" . $New->Id);
        foreach ($NewParent as $Nads) {
            $exclu = 0 ;
            if ($Nads->Id != $lAdherent->Id) {
                $NewsExcluAutreAdherent[]=$Nads;
                $exclu = 1 ;
            }
        }
        if ($exclu == 0 ) {
            $NewsToutAdherent[]=$New;
        }
    }

    $NewsAdherent =$NewsToutAdherent ;
}



// Recherche les Médias de nos news
foreach ($NewsAdherent as &$NEWS) {
    $NewsImg = $NEWS->getOneChild("Media");
    $NEWS->Media =$NewsImg;
}
// notre liste de services avec leurs médias

$vars['News']=$NewsAdherent;
//var_dump($vars['Services']);
