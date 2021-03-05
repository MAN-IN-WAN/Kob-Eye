<?php
// Récupération de l'adherent
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$LeClient = Sys::getOneData("Parc","Client/MiniSite/".$Minisite->Id);

//        $lAdherent = $LeClient->getOneChild("Adherent");
$lAdherent = Sys::getOneData("Vetoccitan", "Adherent/9");
$lAdherent->Module="Vetoccitan";

if ($lAdherent->MentionsLegales!='') {
    $MentionsLegales = Sys::getOneData("Vetoccitan","Divers/".$lAdherent->MentionsLegales);
} else {
    $MentionsLegales = Sys::getOneData("Vetoccitan","Divers/Defaut=1");

}

$langue = $lAdherent->getChildren("Accessibilite/Type=Langue");
$service = $lAdherent->getChildren("Accessibilite/Type!=Langue");


//print_r($accessibilite);

$vars['langue']=$langue;
$vars['service']=$service;
$vars['Mention']=$MentionsLegales;
$vars['Adherent']=$lAdherent;