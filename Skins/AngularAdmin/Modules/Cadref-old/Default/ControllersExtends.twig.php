<?php
$info = Info::getInfos($vars['Chemin']);
if(isset($info['ObjectType'])){
	$vars['CurrentObj'] = genericClass::createInstance($info['Module'],$info['ObjectType']);
	$vars['identifier'] = $vars['Url'];
	$vars['ident'] = $info['Module'].$info['ObjectType'];
	$vars['module'] = $info['Module'];
	$vars['objecttype'] = $info['ObjectType'];

	$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();
	//$vars["ChildrenElements"] = $vars["ObjectClass"]->getChildElements();
	//$vars['Interfaces'] = $vars["ObjectClass"]->getInterfaces();

	$vars['description'] = $vars['CurrentObj']->getDescription();

	$vars['url'] = Sys::getMenu($info['Module'].'/'.$info['ObjectType']);

	if($info['ObjectType'] == 'Classe' || $info['ObjectType'] == 'Visite') {
		$t = array();
		$ens = Sys::getData('Cadref','Enseignant'); 
		foreach($ens as $e) $t[] = array('id'=>$e->Id, 'label'=>$e->Nom.' '.$e->Prenom);
		$vars['enseignants'] = json_encode($t);
	}
} else {
	$vars['noExtend'] = true;
}
$vars['Annee'] = Cadref::$Annee;
$vars['Cotisation'] = Cadref::$Cotisation;
$vars['Initiales'] = Sys::$User->Initiales;

$vars['benevole'] = 0;
$groups = Sys::$User->getParents('Group');
foreach($groups as $g) {
	if($g->Nom == 'CADREF_BENE') $vars['benevole'] = 1; 
	if($g->Nom == 'CADREF_ADH' || $g->Nom == 'CADREF_ENS' || $g->Nom == 'CADREF_BENE') $vars['Group'] = $g->Nom;
}

?>