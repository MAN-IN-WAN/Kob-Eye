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
$last = '';
$ta = array();
foreach($aa as $a) {
	$last = $a->Annee;
	$ta[$last] = $last.'-'.($last+1);
	$tr[$last] = date('Y', $a->DateCotisation);
}
$vars['attestAnnees'] = $ta;
$vars['attestLast'] = $last;

