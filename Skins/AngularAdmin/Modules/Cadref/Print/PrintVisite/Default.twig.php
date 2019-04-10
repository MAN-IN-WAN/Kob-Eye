<?php
// fiche printAdherent
$annee = Cadref::$Annee;
$vars['Annee'] = $annee;
$vars['CurrentMenu'] = Sys::$CurrentMenu;
if(Sys::$User->Admin && !$vars['CurrentMenu']){
    $oc = $o->getObjectClass();
    $vars['CurrentMenu'] = ['Titre' =>$oc->Description ];
}
$vars['identifier'] = 'CadrefVisite';

$vis = Sys::getData("Cadref","Visite/Annee=$annee");
$tmp = array();
foreach($vis as $v) $tmp[$v->DateVisite] = date('d/m', $v->DateVisite).' - '.$v->Libelle;
$vars['visites'] = $tmp;
