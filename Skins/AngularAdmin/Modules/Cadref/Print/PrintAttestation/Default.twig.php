<?php
// fiche printAttestation
$vars['Annee'] = Cadref::$Annee;
$vars['CurrentMenu'] = Sys::$CurrentMenu;
if(Sys::$User->Admin && !$vars['CurrentMenu']){
    $oc = $o->getObjectClass();
    $vars['CurrentMenu'] = ['Titre' =>$oc->Description ];
}
$vars['identifier'] = 'CadrefAdherent';

$tmp = array(''=>'');
$as = Sys::getData('Cadref','Antenne');
foreach($as as $a) $tmp[$a->Id] = $a->Libelle;
$vars['antennes'] = $tmp;
