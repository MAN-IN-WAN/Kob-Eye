<?php
    $prods = Sys::getData('Boutique','Produit/Coeur=1&Actif=1',0,9);
    for ($i=0; $i<sizeof($prods);$i++){
        $prods[$i]->Prix = $prods[$i]->getTarif();
        $prods[$i]->Promo = $prods[$i]->getPromo();
        $prods[$i]->Url = $prods[$i]->getUrl();
    }
    $vars['prods'] = $prods;
?>