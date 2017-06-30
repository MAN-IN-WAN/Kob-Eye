<?php


$vars['User'] = Sys::$User;
if(!$vars['User']->Avatar)
    $vars['User']->Avatar = 'Skins/AngularAdmin/assets/common/img/neutral_avatar.png';

$vars['Client'] = $vars['User']->getOneChild('Client');
$vars['displayName'] = '';

if (is_object($vars['Client']) && isset($vars['Client']->Nom) && $vars['Client']->Nom != ''){
    $vars['displayName'] =  'de la société '.$vars['Client']->Nom;
} else{
    if(isset($vars['User']->Nom) && $vars['User']->Nom != ''){
        $login = $vars['User']->Nom;
    } else{
        $login = $vars['User']->Login;
    }
    //TODO : prendre en compte les caractères accentués (strpos éèà....)
    if(in_array(substr($login,0,1),array('a','e','i','o','u','y')) ){
        $vars['displayName'] =  'd\''.$login;
    }else{
        $vars['displayName'] =  'de '.$login;
    }
}

if($vars['User']->Privilege){
    $vars['Tickets'] = Sys::getData('Parc','Ticket/UserNext='.$vars['User']->Initiales.'&&Etat=En cours');
    array_walk($vars['Tickets'],function(&$t){
        $cli = $t->getOneParent('Client');
        $t->CodeClient = $cli->CodeGestion;
    });

    $vars['TicketsNA'] = Sys::getData('Parc','Ticket/UserNext=00&&Etat=En cours',0,20);
    array_walk($vars['TicketsNA'],function(&$t){
        $cli = $t->getOneParent('Client');
        $t->CodeClient = $cli->CodeGestion;
    });


    $vars['TCount'] =  count($vars['Tickets']);
}

