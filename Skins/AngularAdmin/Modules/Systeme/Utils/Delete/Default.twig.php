<?php
session_write_close();
$query = isset($vars['Path']) ? $vars['Path']: $vars['Query'];
$info = Info::getInfos($query);
if ($info["TypeSearch"]=="Direct"){
    //alors modification
    $o = Sys::getOneData($info['Module'],$query);

    $tache = Sys::getOneData('Systeme','Tache/Nom=ActivityDump');

    $vars['res'] = $o->Delete($tache);
    $vars['errors'] = json_encode($o->Error);
}
?>