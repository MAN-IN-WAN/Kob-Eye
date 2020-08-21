<?php
// Récupération de l'adherent
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$LeClient = Sys::getOneData("Parc","Client/MiniSite/".$Minisite->Id);

$lAdherent = $LeClient->getOneChild("Adherent");
$lAdherent->Module="Vetoccitan";

if ($lAdherent->MentionsLegales!='') {
    $MentionsLegales = Sys::getOneData("Vetoccitan","Divers/".$lAdherent->MentionsLegales);
} else {
    $MentionsLegales = Sys::getOneData("Vetoccitan","Divers/Defaut=1");

}

$vars['Mention']=$MentionsLegales;
$vars['Adherent']=$lAdherent;