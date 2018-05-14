<?php
$cs = Sys::getData('Reservations','Court');
$b = new BashColors();
echo $b->getColoredString("Mise à jour des réservations journées\r\n", 'yellow');
foreach ($cs as $c){
    echo $b->getColoredString('-> Emplacement '.$c->Titre."\r\n", 'green');
    //mise àjour des resajour
    $c->checkResaJours();
}

//Mise à jour dispos
$cs = Sys::getData('Reservations','Disponibilite');
echo $b->getColoredString('Mise à jour des disponibilité'."\r\n", 'yellow');
foreach ($cs as $c){
    if (!$c->RecurrenceHebdo&&$c->Debut<time()-86400) continue;
    echo $b->getColoredString('-> Disponibilité '.$c->Titre."\r\n", 'green');
    //mise àjour des dispos
    $c->Save();
}

//Mise à jour résas
$cs = Sys::getData('Reservations','Reservation/DateDebut>'.time());
echo $b->getColoredString('Mise à jour des réservations'."\r\n", 'yellow');
foreach ($cs as $c){
    echo $b->getColoredString('-> Réservation '.$c->Nom."\r\n", 'green');
    //mise àjour des resas
    $c->Save();
}
