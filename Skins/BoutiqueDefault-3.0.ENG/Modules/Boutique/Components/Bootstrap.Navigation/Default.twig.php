<?php
if (!isset($vars['Niveau']))
    $vars['Niveau'] = 1;
$cats = Sys::getData('Boutique','Magasin/'.$vars['CurrentMagasin']->Id.'/Categorie');
$out = array();
foreach ($cats as $c){
    $mens = Sys::searchInMenus('Alias','Boutique/Categorie/'.$c->Id);
    $out = array_merge($out,$mens);
}
$vars['Menus'] = $out;
?>