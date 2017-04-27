<?php
$query = isset($vars['Path']) ? $vars['Path']: $vars['Query'];
$info = Info::getInfos($query);
if ($info["TypeSearch"]=="Direct"){
    //alors modification
    $o = Sys::getOneData($info['Module'],$query);
}else{
    //alors création
    $o = genericClass::createInstance($info['Module'],$info['ObjectType']);
}
$formfields = $o->getElementsByAttribute('form','',true);
$values = json_decode(file_get_contents('php://input'));
$out = array();
foreach ($formfields as $f){
    $o->{$f["name"]} = $values->{$f["name"]};
}
if ($o->Verify()) {
    $o->Save();
    foreach ($formfields as $f){
        $values->{$f["name"]} = $o->{$f["name"]};
    }
    $vars['retour'] = '{
        "data": '.json_encode($values).',
        "success": true
    }';
}else{
    $vars['retour'] = '{
        "data": '.json_encode($values).',
        "errors": '.json_encode($o->Error).',
        "success": false
    }';
}
?>