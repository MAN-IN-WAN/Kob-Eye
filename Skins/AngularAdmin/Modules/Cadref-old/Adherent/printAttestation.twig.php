<?php
// fiche printAttestation adherent
$vars['Annee'] = Cadref::$Annee;
$vars['CurrentMenu'] = Sys::$CurrentMenu;
if(Sys::$User->Admin && !$vars['CurrentMenu']){
    $oc = $o->getObjectClass();
    $vars['CurrentMenu'] = ['Titre' =>$oc->Description ];
}
$vars['identifier'] = 'CadrefAdherent';


$info = Info::getInfos($vars['Query']);
$aa = Sys::getData('Cadref','AdherentAnnee/AdherentId='.$info['LastId']);
$first = '';
$ta = array();
foreach($aa as $a) {
	if($first == '') $first = $a->Annee;
	$ta[$a->Annee] = $a->Annee.'-'.($a->Annee+1);
	$tr[$a->Annee] = date('Y', $a->DateCotisation);
}
$vars['attestAnnees'] = $ta;
$vars['attestFirst'] = $first;

