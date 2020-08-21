<?php

// Récupération de l'adherent
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$LeClient = Sys::getOneData("Parc","Client/MiniSite/".$Minisite->Id);

$lAdherent = $LeClient->getOneChild("Adherent");
$lAdherent->Module="Vetoccitan";

//var_dump($lAdherent->Recup_InfosVeto('Conseil'));


// Recupération des services
$CategsAdherent= array();
$ServicesAdherent= array();

// recherche Activités de l'adhérent
if ($_GET["C"]) {
    // CAS DU FILTRE DEMANDÉ
    $ServicesAdherent = Sys::getData('Vetoccitan', "Categorie/".$_GET["C"]."/Service/Display=1");
    // RESTE À EXCLURE LES INFOS QUI SONT POUR D'AUTRES ADHÉRENTS !!
    $servicesAdherent = $lAdherent->getChildren("Service");

    foreach ( $servicesAdherent as $item) {
        $recupServ[] = $item->Id;
    }
//    var_dump($recupServ);



} else {
    $ActivitesAdherent = Sys::getData('Vetoccitan', "Activite/Adherent/" . $lAdherent->Id);
    // Recupération des services

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

    // Recherche des Services liées aux catégories
    foreach ($CategsAdherent as $CATAD) {
        $ServicesCat = $CATAD->getChildren("Service/Display=1");
        foreach ($ServicesCat as $CatServ) {
            foreach ($ServicesAdherent as $Sads) {
                if ($CatServ->Id == $Sads->Id) {
                    continue 2;
                }
            }
            $ServicesAdherent[] = $CatServ;
        }
    }

    // recherche les services liés directement à l'adhérent
    $ServAdherentDirect = $lAdherent->getChildren("Service/Display=1");
    // ajout aux autres services
    foreach ($ServAdherentDirect as $Serv) {
        foreach ($ServicesAdherent as $Sads) {
            if ($Serv->Id==$Sads->Id) {continue 2;}
        }
        $ServicesAdherent[]= $Serv;
    }

    $ServicesToutAdherent = array();

    foreach ($ServicesAdherent as $Serv) {
        $ServParent = Sys::getData('Vetoccitan', "Adherent/Service/" . $Serv->Id);
        foreach ($ServParent as $Sads) {
            $exclu = 0 ;
            if ($Sads->Id != $lAdherent->Id) {
                $exclu = 1 ;
            } else {
                $ServicesToutAdherent[]=$Serv;
            }
        }
        if ($exclu == 0 ) {
            $ServicesToutAdherent[]=$Serv;
        }
    }

    $ServicesAdherent =$ServicesToutAdherent ;


}


// Recherche les Médias de nos services
foreach ($ServicesAdherent as &$SERV) {
    $ServImg = $SERV->getOneChild("Media");
    $SERV->Media =$ServImg;
}
// notre liste de services avec leurs médias

$vars['Services']=$ServicesAdherent;
//var_dump($vars['Services']);
