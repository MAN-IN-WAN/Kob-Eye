<?php


$vars['User'] = Sys::$User;
$vars['Client'] = $vars['User']->getOneChild('Client');
if(!$vars['Client']) {
    $vars['Client'] = new stdClass();
    $vars['Client']->Id = 0;
}
$vars['forceDisplayList'] = ($vars['User']->isRole('PARC_TECHNICIEN') || $vars['User']->Admin) ? 1 : 0;

/*$vars['Domaines'] = $vars['Client']->getChildren('Domain');
$vars['Sites'] = array();
foreach ($vars['Domaines'] as $dom){
    $site = Sys::getOneData('Systeme','Site/Url~'.$dom->Url);
    if(!$site) $site = array();
    array_push($vars['Sites'],$site);
}*/


