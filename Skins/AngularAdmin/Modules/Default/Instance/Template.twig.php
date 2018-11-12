<?php

if (isset($vars['Path']))
    $Path = $vars['Path'];
else
    $vars['Path'] = $Path = $vars['Query'];


$vars['canCreate'] = false;
$user = Sys::$User;
$cli = $user->getOneChild('Client');
$doms = Sys::getCount('Parc', 'Client/'.$cli->Id.'/Domain');
$inss = Sys::getCount('Parc', 'Client/'.$cli->Id.'/Instance');


if($doms && $doms>$inss) $vars['canCreate']  = true;
