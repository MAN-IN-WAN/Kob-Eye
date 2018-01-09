<?php
$info= Info::getInfos($vars['Query']);
$obj = Sys::getOneData($info['Module'],$vars['Query']);
if (!strpos($obj->Adresse,'abtel.fr')&&Sys::$User->isRole('PARC_TECHNICIEN')) {
    $vars['authorized'] = true;
    $vars['url'] = $obj->deletegateAccess();
}else $vars['authorized'] = false;