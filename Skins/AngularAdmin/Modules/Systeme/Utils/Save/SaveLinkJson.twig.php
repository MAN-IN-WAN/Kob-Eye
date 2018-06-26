<?php

$vals = json_decode(file_get_contents('php://input'),true);

$info = Info::getInfos($vars['Query']);
$cur = Sys::getOneData($vals['Module'],$vals['ObjectName'].'/'.$vals['Id']);

$cur->resetChilds($info['ObjectType']);

foreach($vals['list'] as $v){
    $cur->addChild($info['ObjectType'],$v);
}
//TODO : gestion des erreurs
$vars['retour'] = '{
        "data": "",
        "success": true,
        "message": "Enfants affectés avec succès"
    }';

echo $vars['retour'];