<?php

$domain = Sys::$domain;
$site = Sys::getOneData('Systeme','Site/Domaine='.$domain);
$user = $site->getOneParent('User');
$grp = $user->getOneParent('Group');
$users = $grp->getChildren('User/Id!='.$user->Id);



$grpMenus = $grp->getChildren('Menu');
$usersMenus = array();
foreach($users as $us){
    $usersMenus = array_merge($usersMenus,$us->getChildren('Menu'));
}
$vars['menus'] = array_merge($usersMenus,$grpMenus);


$vars['entities'] = array();
foreach ($vars['menus'] as $menu){
    $info = Info::getInfos($menu->Alias);
    $gen = genericClass::createInstance($info['Module'],$info['ObjectType']);
    $props = $gen->getElementsByAttribute('','',true);
    $vars['entities'][$menu->Url] = $props;

}
