<?php
$vars['identifier'] = $vars['Url'];
$vars['Genre'] = Sys::getData('Reservation','Genre');

$tabCouleur = array();
$tabNom = array();

$tab = array(
    $tabNom,
    $tabCouleur
);

foreach($vars['Genre'] as $items){
    $tab[0][] = $items->Nom;
    $tab[1][] = $items->Couleur;
}


$vars['Genre']= json_encode($tab[0]);
$vars['GenreCoul']= json_encode($tab[1]);