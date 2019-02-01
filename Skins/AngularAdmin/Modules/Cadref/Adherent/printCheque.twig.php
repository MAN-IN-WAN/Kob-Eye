<?php
// fiche printCheque adherent
$vars['Annee'] = Cadref::$Annee;
$vars['CurrentMenu'] = Sys::$CurrentMenu;
if(Sys::$User->Admin && !$vars['CurrentMenu']){
    $oc = $o->getObjectClass();
    $vars['CurrentMenu'] = ['Titre' =>$oc->Description ];
}
$vars['identifier'] = 'CadrefAdherent';


$info = Info::getInfos($vars['Query']);
$a = Sys::getOneData('Cadref','Adherent/'.$info['LastId']);
$s = '';
switch($a->Sexe) {
	case 'H':
		$s = 'M'; break;
	case 'F':
		$s = 'Mme'; break;
}
$vars['civilite'] = $s;
$vars['nom'] = $a->Nom;
$vars['prenom'] = $a->Prenom;
$vars['adresse1'] = $a->Adresse1;
$vars['adresse2'] = $a->Adresse2;
$vars['ville'] = $a->CP.'  '.$a->Ville;
$vars['objet'] = "Remboursement de cours";