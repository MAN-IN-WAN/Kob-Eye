<?php
// fiche gestionDiffere
$vars['Annee'] = Cadref::$Annee;
$vars['CurrentMenu'] = Sys::$CurrentMenu;
if(Sys::$User->Admin && !$vars['CurrentMenu']){
    $oc = $o->getObjectClass();
    $vars['CurrentMenu'] = ['Titre' =>$oc->Description ];
}
$vars['identifier'] = 'CadrefReglement';

$tmp = array(''=>'');
$gs = Sys::getData('Systeme','Group/Nom=CADREF_ADMIN/*/User');
foreach($gs as $g) $tmp[] = $g->Initiales;
$vars['users'] = $tmp;
$vars['initiales'] = Sys::$User->Initiales;

if (is_object(Sys::$CurrentMenu))
    $vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
else $vars['CurrentUrl'] = $vars['Query'];
