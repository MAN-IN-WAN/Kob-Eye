<?php

$vals = json_decode(file_get_contents('php://input'),true);

$info = Info::getInfos($vars['Query']);
$cur = Sys::getOneData($vals['Module'],$vals['ObjectName'].'/'.$vals['Id']);

$childs = $cur->getChildren($info['ObjectType']);
$list = array();
foreach ($childs as $c){
    array_push($list,$c->Id);
}
//TODO : gestion des erreurs
$vars['retour'] = '{
        "data": '.json_encode($list).',
        "success": true
    }';

echo $vars['retour'];