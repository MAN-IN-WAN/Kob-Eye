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

	if($info['ObjectType'] == 'Classe') {
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
//$vars['Utilisateur'] = "XXX";

?>