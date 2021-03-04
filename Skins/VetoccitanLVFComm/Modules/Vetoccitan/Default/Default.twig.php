<?php
// Récupération des valeurs du minisite
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$Modele = $Minisite->getOneParent("ModeleMiniSite");
$Pages = $Modele->getChildren("PageMiniSite/14");
$Menu = Sys::$CurrentMenu->Url;

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
$params = $PageEncours->getParamsValues($Minisite->Id);
foreach($params as $p){
    $vars[$p->Nom] = $p->vms;
}

