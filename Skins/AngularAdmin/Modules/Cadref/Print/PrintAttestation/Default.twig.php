<?php
// fiche printAttestation
$vars['Annee'] = Cadref::$Annee;
$vars['CurrentMenu'] = Sys::$CurrentMenu;
if(Sys::$User->Admin && !$vars['CurrentMenu']){
    $oc = $o->getObjectClass();
    $vars['CurrentMenu'] = ['Titre' =>$oc->Description ];
}
$vars['identifier'] = 'CertificatAdherent';

$tmp = array(''=>'');
$as = Sys::getData('Cadref','Antenne');
foreach($as as $a) $tmp[$a->Id] = $a->Libelle;
$vars['antennes'] = $tmp;

$ta = array();
$as = Sys::getData('Cadref','Annee');
foreach($as as $a) $ta[$a->Annee] = $a->Annee.'-'.($a->Annee+1);
$vars['attestAnnees'] = $ta;


