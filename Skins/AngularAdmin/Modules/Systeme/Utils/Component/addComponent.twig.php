<?php

//Gestion des composants
$comps = Component::getAll();
//print_r($comps);
$vars['Components'] = array();
foreach($comps as $a){
    $compo = Component::getInstance($a);
    if($compo->Module == 'Cms')
        $vars['Components'][$compo->Module.$compo->Title] = $compo;
}

