<?php

// Récupération des valeurs du minisite
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$Modele = $Minisite->getOneParent("ModeleMiniSite");
$Pages = $Modele->getChildren("PageMiniSite");
$Menu = Sys::$CurrentMenu->Url;

foreach($Pages as $Page){
    $PageUrl = strtolower($Page->MenuUrl);
    $PageUrl = Utils::checkSyntaxe($PageUrl);
    $PageTitle = strtolower($Page->Titre);
    $PageTitle = Utils::checkSyntaxe($PageTitle);

    if($PageUrl == $Menu || $Menu == $PageTitle){
        $PageEncours = $Page;
        continue;
    }
}
$params = $PageEncours->getParamsValues($Minisite->Id);
foreach($params as $p){
    $vars[$p->Nom] = $p->vms;
}



// Récupération de l'adherent


$LeClient = Sys::getOneData("Parc","Client/MiniSite/".$Minisite->Id);

$lAdherent = $LeClient->getOneChild("Adherent");
$lAdherent->Module="Vetoccitan";


// Recuperation tous les postes pour tri d'affichage
$allPoste = Sys::getData("Vetoccitan","Poste","","","","","","",true);
$tabOrdrePost =array();
foreach ($allPoste as $p){
    $tabOrdrePost[] = array('Nom'=>$p->Nom,'Ordre'=>$p->Ordre);
}
usort($tabOrdrePost,function($a,$b){
    if ($a['Ordre'] > $b['Ordre']) return 1;
    if ($a['Ordre'] < $b['Ordre']) return -1;
    if ($a['Ordre'] == $b['Ordre']){
        if ($a['Nom'] > $b['Nom']) return 1;
        if ($a['Nom'] < $b['Nom']) return -1;
    }
    return 0;
});
//Fichier Poste
//Fichier Personnel
$EquipierAdherent = $lAdherent->getChildren("Personnel");

$post = array();

foreach($EquipierAdherent as $items){
    if (isset($post[$items->Poste])){
        $post[$items->Poste][] = $items;
    }else{
        $post[$items->Poste] = array($items);
    }
}
$tabOrdrePost = array_filter($tabOrdrePost,function($a)use($post){
    if (in_array($a['Nom'],array_keys($post))){
        return true;
    }
    return false;
});




$vars["Equipiers"]=$post;
$vars["Postes"]=$tabOrdrePost;
