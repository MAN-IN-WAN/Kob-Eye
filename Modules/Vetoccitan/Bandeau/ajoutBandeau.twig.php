<?php
// CHERCHER TOUS LES BANDEAUX VETOCCITAN TOUSSA TOUSSA
if ($vars['funcTempVars']['step'] == 1){

    $adh = $GLOBALS["Systeme"]->getRegVars("VetoAdh");

    $activites= $adh->getParents("Activite");
    $categories=array();
    foreach($activites as $items){
        $categs= $items->getChildren("Categorie");
        foreach ($categs as $categ) {
            $categories[] = $categ->Id;
        }
    }
//    print_r($categories);


    $allBandeau = Sys::getData("Vetoccitan","Bandeau/Publier=1","","","","","","",true);
    $notBandeau = Sys::getData("Vetoccitan","Adherent/*/Bandeau","","","","","","",true);
    $goodBandeau = array_filter($allBandeau,function($b) use($notBandeau,$categories,$adh){
        foreach($notBandeau as $nb){
            if ($nb->Id == $b->Id)
                return false;
        }
        $cats = $b->getParents("Categorie");
        foreach($cats as $c){
            if(in_array($c->Id,$categories)){
                return true;
            }
        }
        return false;
    });
    foreach($goodBandeau as $k => &$items){
        $items->Media = Sys::getOneData("Vetoccitan","Bandeau/".$items->Id."/Media","","","","","","",true);
        if (!$items->Media || empty($items->Media->Image)){
            unset($goodBandeau[$k]);
        }
    }
    $vars['goodBandeau'] = $goodBandeau;
}



