<?php
$info = Info::getInfos($vars['Query']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['fields'] = $o->getElementsByAttribute('','',true);
$vars['row'] = Sys::getOneData($info['Module'],$vars['Query']);
$vars['row']->label = $vars['row']->getFirstSearchOrder();
$uc = Sys::getOneData('Systeme','User/'.$vars['row']->userCreate);
$ue = Sys::getOneData('Systeme','User/'.$vars['row']->userEdit);
if (is_object($uc))
    $vars['row']->userCreateName = $uc->Login;
else $vars['row']->userCreateName = 'inconnu';
if (is_object($ue))
    $vars['row']->userEditName = $ue->Login;
else $vars['row']->userEditName = 'inconnu';
?>