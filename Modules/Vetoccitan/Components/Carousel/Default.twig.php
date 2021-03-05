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


// recherche Catégories pour chaque Activités de l'adhérent
foreach ($ActivitesAdherent as $ACP) {

    $CategsActivite = $ACP->getChildren("Categorie");
    foreach ($CategsActivite as $Cats) {
        foreach ($CategsAdherent as $Cads) {
           if ($Cats->Id==$Cads->Id) {continue 2;}
        }
        $CategsAdherent[]= $Cats;
    }
}
$objet = 'Bandeau';
if ($vars['POSITION'] == 'bandoHaut'){
    $objet = 'Bandeau';
}elseif ($vars['POSITION'] == 'bandoBAS'){
    $objet = 'Publicite';
}
// Recherche des Publicités liées aux catégories
foreach ($CategsAdherent as $CATAD) {
    $PubsCategs = $CATAD->getChildren($objet."/(!(!DateDebut<=". time() . "&&DateFin>=". time() ."!)++(!DateFin=0!)!)&&Publier=1");
    foreach ($PubsCategs as $Pubs) {
        foreach ($PubsAdherent as $Pads) {
            if ($Pubs->Id==$Pads->Id) {continue 2;}
        }
        $PubsAdherent[]= $Pubs;
    }



}
// recherche les Publicité liés directement à l'adhérent
$PubsAdherentDirect = $lAdherent->getChildren($objet."/(!(!DateDebut<=". time() . "&&DateFin>=". time() ."!)++(!DateFin=0!)!)&&Publier=1");
// ajout aux autres publicités

foreach ($PubsAdherentDirect as $Pubs) {
    foreach ($PubsAdherent as $Pads) {
        if ($Pubs->Id==$Pads->Id) {continue 2;}
    }
    $PubsAdherent[]= $Pubs;
}

// Recherche les Médias de nos publicités
foreach ($PubsAdherent as &$PUBS) {
    $PubsImg = $PUBS->getOneChild("Media");
    $PUBS->Media =$PubsImg;
}


shuffle($PubsAdherent);

$max = sizeof($PubsAdherent);
//var_dump($PubsAdherent[0]);

$vars['Publicites']=$PubsAdherent;
$vars['imageBas']=$PubsAdherent[rand(0,$max)];