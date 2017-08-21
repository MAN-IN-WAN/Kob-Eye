<?php

$vars['User'] = Sys::$User;

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