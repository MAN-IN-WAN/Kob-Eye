<?php
// Récupération des valeurs du minisite
$params = array();
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$Modele = $Minisite->getOneParent("ModeleMiniSite");
$Pages = $Modele->getChildren("PageMiniSite/15");
$Menu = Sys::$CurrentMenu->Url;

$LeClient = Sys::getOneData("Parc","Client/MiniSite/".$Minisite->Id);
$lAdherent = $LeClient->getOneChild("Adherent");
$lAdherent->Module="Vetoccitan";

$vars['Adherent']=$lAdherent;

foreach($Pages as $Page){
    $PageUrl = strtolower($Page->MenuUrl);
    $PageUrl = Utils::checkSyntaxe($PageUrl);
    $PageTitle = strtolower($Page->Titre);
    $PageTitle = Utils::checkSyntaxe($PageTitle);

//    if($PageUrl == $Menu || $Menu == $PageTitle){
        $PageEncours = $Page;
        continue;
//    }
}
if ($PageEncours){
    $params = $PageEncours->getParamsValues($Minisite->Id);
}

foreach($params as $p){
    $vars[$p->Nom] = $p->vms;
}
