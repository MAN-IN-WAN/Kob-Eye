<?php

$user = Sys::$User;
$menus = $user->getMenus();

foreach ($menus as &$m){
    if(strpos($vars['Lien'],$m->Url) === 0){
        $m->current = true;
    } else {
        $m->current = false;
    }
    if ( empty($m->Url) && empty($vars['Lien']) ){
        $m->current = true;
    }
    if ($m->Url == "urgence") {
       continue;
    }
    else {
        $menu2 []=$m;
    }
}
$vars['Menu'] = $menu2;

$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$LeClient = Sys::getOneData("Parc","Client/MiniSite/".$Minisite->Id);

//$lAdherent = $LeClient->getOneChild("Adherent");
$lAdherent = Sys::getOneData("Vetoccitan","Adherent/9");
$vars['mentions'] = Sys::getOneData("Vetoccitan","Divers");
//print_r($vars['mentions']);
$lAdherent->Module="Vetoccitan";

// Recupération des publicités

$CategsAdherent= array();
$PubsAdherent= array();

// recherche Activités de l'adhérent
//$ActivitesAdherent = Sys::getData('Vetoccitan',"Activite/Adherent/".$lAdherent->Id);
$ActivitesAdherent = Sys::getData('Vetoccitan',"Activite/Adherent/9");


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
$vars['men'] = Sys::$CurrentMenu->Titre;

$vars['Filtres']=$CategsAdherent;
$vars['Tel'] = $lAdherent->Tel;
$vars['EmailContact'] = $lAdherent->EmailContact;
$vars['Nom'] = $lAdherent->Nom;
$vars['TelUrgence'] = $lAdherent->TelUrgence;
$vars['Adresse'] = $lAdherent->Adresse;
$vars['CodePostal'] = $lAdherent->CodePostal;
$vars['Ville'] = $lAdherent->Ville;
$vars['LienFacebook'] = $lAdherent->LienFacebook;
$vars['LienInstagram'] = $lAdherent->LienInstagram;

$params = $Minisite->getParamsValues();

$vars["mainColor"] = $params['Couleur_Principale']->vms;
