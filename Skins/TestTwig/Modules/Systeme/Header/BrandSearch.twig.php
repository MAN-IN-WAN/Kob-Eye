<?php
$vars['marques'] = Sys::getData('Boutique','Marque/Actif=1',0,100, 'ASC', 'Nom');
$menu = Sys::getMenu('Boutique/Marque');
for ($i = 0; $i<sizeof($vars['marques']); $i++ ){
    $vars['marques'][$i]->Url = $menu.'/'.$vars['marques'][$i]->Url;
}
?>