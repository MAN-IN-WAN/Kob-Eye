<?php

$vars['urls'] = array();

$vars['urls']['Parc'] = array();
$vars['urls']['Parc']['Ticket'] = Sys::getMenu('Parc/Ticket');


$vars['urls'] = json_encode($vars['urls']);
