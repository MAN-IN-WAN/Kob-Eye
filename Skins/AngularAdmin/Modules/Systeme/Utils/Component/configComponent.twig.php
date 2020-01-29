<?php
$Module = $GLOBALS['Systeme']->getGetVars('Module');
$Component =  $GLOBALS['Systeme']->getGetVars('Component');

$comp = Component::getInstance($Module.'/'.$Component);
//print_r($comp);


$vars['formfields'] = $comp->Proprietes;

array_walk($vars['formfields'] ,function(&$a){
        $a['name'] = $a['Nom'];
        $a['type'] = $a['Type'];
});
//var_dump($vars['formfields']);
?>