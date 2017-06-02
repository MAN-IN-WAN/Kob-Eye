<?php
$query = isset($vars['Path']) ? $vars['Path']: $vars['Query'];
$info = Info::getInfos($query);
if ($info["TypeSearch"]=="Direct"){
    //alors modification
    $o = Sys::getOneData($info['Module'],$query);
    $o->Delete();
}
?>