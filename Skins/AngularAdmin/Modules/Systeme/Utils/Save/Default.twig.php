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
    if ($f['type']=='date') {
        //transformation des timestamps en format js
        $a = strptime($values->{$f["name"]}, '%d/%m/%Y %H:%M');
        $o->{$f["name"]} = mktime($a['tm_hour'], $a['tm_min'], 0, $a['tm_mon'] + 1, $a['tm_mday'], $a['tm_year'] + 1900);
    }elseif ($f['type']=='boolean'){
        if ($values->{$f["name"]}==='true'||$values->{$f["name"]}===1||$values->{$f["name"]}===TRUE)
            $o->{$f["name"]} = 1;
        else $o->{$f["name"]} = 0;
    }elseif ($f['type']=='fkey' && $f['card']=='short'){
        $o->AddParent($f['objectModule'].'/'.$f['objectName'].'/'.$values->{$f["name"]});
    }else $o->{$f["name"]} = $values->{$f["name"]};
}
$obj = $o->getObjectClass();
$formfields = $obj->getParentElements();
foreach ($formfields as $f){
    if ($f['type']=='fkey'){
        $o->AddParent($f['objectModule'].'/'.$f['objectName'].'/'.$values->{$f["name"]});
    }
}

if ($o->Verify()) {
    $success = $o->Save();
    foreach ($formfields as $f){
        $values->{$f["name"]} = $o->{$f["name"]};
        if ($f['type']=='date'){
            //transformation des timestamps en format js
            $values->{$f['name']} = date('d/m/Y H:i',$o->{$f['name']});
        }elseif ($f['type']=='rkey') {
            $o->resetChilds($f['objectName']);
            if (is_array($values->{$f["name"]})) foreach ($values->{$f["name"]} as $v)
                $o->AddChild($f['objectName'], $v);
        }
    }
    $vars['retour'] = '{
        "data": '.json_encode($values).',
        "errors": '.json_encode($o->Error).',
        "success": '.(($success)?1:0).'
    }';
}else{
    $vars['retour'] = '{
        "data": '.json_encode($values).',
        "errors": '.json_encode($o->Error).',
        "success": false
    }';
}
?>