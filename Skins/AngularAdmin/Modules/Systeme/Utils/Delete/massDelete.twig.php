<?php
$query = isset($vars['Path']) ? $vars['Path']: $vars['Query'];
$info = Info::getInfos($query);
$values = json_decode(file_get_contents('php://input'));
foreach ($values as $v){
    $o = Sys::getOneData($info['Module'],$info['ObjectType'].'/'.$v);
    $o->Delete();
}
?>