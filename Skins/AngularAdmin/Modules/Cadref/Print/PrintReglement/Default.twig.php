<?php
// fiche printReglement
$vars['Annee'] = Cadref::$Annee;
$vars['CurrentMenu'] = Sys::$CurrentMenu;
if(Sys::$User->Admin && !$vars['CurrentMenu']){
    $oc = $o->getObjectClass();
    $vars['CurrentMenu'] = ['Titre' =>$oc->Description ];
}
$vars['identifier'] = 'CadrefReglement';

$tmp = array();
$gs = Sys::getData('Systeme','Group/Nom=CADREF_ADMIN/*/User');
foreach($gs as $g) $tmp[] = $g->Initiales;
$tmp[] = 'WEB';
$tmp[] = "Tous";
$vars['users'] = $tmp;
$vars['initiales'] = Sys::$User->Initiales;

$menus = ['impressionslistereglements','impressionsreglementsdifferes','impressionsdifferesnonencaisses'];
$t = explode('/', Sys::$CurrentMenu->Url);
$vars['mode'] = array_search($t[0].$t[1], $menus);

$vars['modes'] = ['T'=>'Totaux', 'B'=>'Chèques','E'=>'Espèces','C'=>'Cartes','P'=>'Prélèvements','V'=>'Virements','A'=>'Chèques vacances',''=>'Non affectés'];
