<?php
$info = Info::getInfos($vars['Chemin']);

$vars['CurrentObj'] = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['identifier'] = $vars['Url'];

$vars["ObjectClass"] = $vars["CurrentObj"]->getObjectClass();
//$vars["ChildrenElements"] = $vars["ObjectClass"]->getChildElements();
//$vars['Interfaces'] = $vars["ObjectClass"]->getInterfaces();

$vars['description'] = $vars['CurrentObj']->getDescription();

//Gestion des composants
$comps = Component::getAll();
//print_r($comps);
$vars['Components'] = array();
foreach($comps as $a){
    $compo = Component::getInstance($a);
    if($compo->Module == 'Cms')
        $vars['Components'][$compo->Module.$compo->Title] = $compo;
}
$vars['Components'] = json_encode($vars['Components']);
?>