<?php
//config
$nb = 11;
$nbbig = 4;

//$vars ['spectacles'] = Sys::getData('Reservation','Spectacle/AlaUne=1&DateDebut>'.time());
$vars['spectacles'] = Sys::getData('Reservation','Spectacle/AlaUne=1',0,$nb);
$big = array();
for ($i=0; $i<$nbbig;$i++) {
    $new = 0;
    while ($new==0|in_array($new,$big)){
        $new = random_int(0,$nb-3);
    }
    $big[] = $new;
}
foreach ($vars['spectacles'] as $k=>$s){
    //recherche du genre
    $genre = Sys::getOneData('Reservation','Genre/Nom='.$s->Genre);
    if ($genre)
        $vars['spectacles'][$k]->Couleur = $genre->Couleur;
    else
        $vars['spectacles'][$k]->Couleur = "#fff";

    if (in_array($k,$big)){
        $vars['spectacles'][$k]->big = 'big';
    }
}
