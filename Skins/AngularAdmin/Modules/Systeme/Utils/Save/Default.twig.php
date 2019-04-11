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
if(!$o){
    $vars['retour'] = '{
        "data": "",
        "errors": [{ "Message" : "Vous n\'avez pas l\'autorisation d\'accéder à cet objet" }],
        "warning": "",
        "infos": "",
        "success": false
    }';
    return false;
}
$formfields = $o->getElementsByAttribute('form','',true);
$hiddenfields = $o->getElementsByAttribute('hidden','',true);
if(!$hiddenfields) $hiddenfields = array();
$formfields = array_merge($formfields,$hiddenfields);
$values = json_decode(file_get_contents('php://input'));
$out = array();
foreach ($formfields as $f){
    if ($f['type']=='datetime') {
        //transformation des timestamps en format js
        $a = strptime($values->{$f["name"]}, '%d/%m/%Y %H:%M');
        $o->{$f["name"]} = mktime($a['tm_hour'], $a['tm_min'], 0, $a['tm_mon'] + 1, $a['tm_mday'], $a['tm_year'] + 1900);
    }if ($f['type']=='date') {
        //transformation des timestamps en format js
        $a = strptime($values->{$f["name"]}, '%d/%m/%Y');
        $o->{$f["name"]} = mktime($a['tm_hour'], $a['tm_min'], 0, $a['tm_mon'] + 1, $a['tm_mday'], $a['tm_year'] + 1900);
    }elseif ($f['type']=='boolean'){
        if ($values->{$f["name"]}==='true'||$values->{$f["name"]}===1||$values->{$f["name"]}===TRUE||$values->{$f["name"]}==='1')
            $o->{$f["name"]} = 1;
        else $o->{$f["name"]} = 0;
    }elseif ($f['type']=='fkey' && $f['card']=='short'){
        $o->AddParent($f['objectModule'].'/'.$f['objectName'].'/'.$values->{$f["objectName"].$f["name"]});
    }elseif ($f['type']=='html'){
        $replace   = array("\r\n", "\n", "\r", "\t");
        $o->{$f["name"]} = str_replace($replace,'', $values->{$f["name"]});
    }elseif (isset($values->{$f["name"]})) $o->{$f["name"]} = $values->{$f["name"]};

}

$obj = $o->getObjectClass();
$parentelements = $obj->getParentElements();
foreach ($parentelements as $f){
    if ($f['type']=='fkey' && $f['card']=='short'){
        $o->AddParent($f['objectModule'].'/'.$f['objectName'].'/'.$values->{$f["objectName"].$f["name"]});
    }elseif ($f['type']=='fkey' && $f['card']=='long' && isset($values->{$f["objectName"].$f["name"]})){
        $o->resetParents($f['objectName']);
        foreach ($values->{$f["objectName"].$f["name"]} as $v)
            $o->AddParent($f['objectModule'].'/'.$f['objectName'].'/'.$v);
    }
}

if ($o->Verify()) {
    $success = $o->Save();

    foreach ($formfields as $f){
        $values->{$f["name"]} = $o->{$f["name"]};
        if ($f['type']=='datetime'){
            //transformation des timestamps en format js
            $values->{$f['name']} = $o->{$f['name']}>0 ? date('d/m/Y H:i',$o->{$f['name']}) : '';
		}
        elseif ($f['type']=='date'){
            //transformation des timestamps en format js
            $values->{$f['name']} = $o->{$f['name']}>0 ? date('d/m/Y',$o->{$f['name']}) : '';
        }elseif ($f['type']=='rkey') {
            $o->resetChilds($f['objectName']);
            if (is_array($values->{$f["objectName"].$f["name"]})) foreach ($values->{$f["objectName"].$f["name"]} as $v){
                $o->AddChild($f['objectName'], $v);
            }
        }
    }
    $vars['retour'] = '{
		"id": '.($o->Id ? $o->Id : 0).',
        "data": '.json_encode($values).',
        "errors": '.json_encode($o->Error).',
        "warning": '.json_encode($o->Warning).',
        "infos": '.json_encode($o->Success).',
        "success": '.(($success)?1:0).'
    }';
}else{
    $vars['retour'] = '{
		"id": '.($o->Id ? $o->Id : 0).',
        "data": '.json_encode($values).',
        "errors": '.json_encode($o->Error).',
        "warning": '.json_encode($o->Warning).',
        "infos": '.json_encode($o->Success).',
        "success": false
    }';
}


?>