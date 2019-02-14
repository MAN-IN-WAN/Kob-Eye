<?php
$info= Info::getInfos($vars['Query']);
$obj = Sys::getOneData($info['Module'],$vars['Query']);
if (!strpos($obj->Adresse,'abtel.fr')) {
    $vars['authorized'] = true;
    $vars['url'] = $obj->deletegateAccess();
}else $vars['authorized'] = false;