<?php
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$LeClient = Sys::getOneData("Parc","Client/MiniSite/".$Minisite->Id);

$lAdherent = $LeClient->getOneChild("Adherent");
$lAdherent->Module="Vetoccitan";

$info=Info::getInfos($vars["Query"]);

$LaNews= Sys::getOneData($info["Module"],$info["Query"]);

$LaNews->Media= $LaNews->getOneChild("Media");

$test = $lAdherent->Recup_Fiche_News($lAdherent);

foreach ($test as $items){
    if ($items->Id === $LaNews->Id){
        $vars["UneNews"]= $LaNews;
        $vars['Retour']=Sys::$CurrentMenu->Url;
    }else{
        return false;
    }
};