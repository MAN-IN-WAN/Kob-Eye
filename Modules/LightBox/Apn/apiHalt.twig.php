<?php
$out = Apn::halt();
$vars['message'] = '';
if ($out)
    $vars['message'] = 'Arrety en cours' ;
else{
    foreach ($obj->Error as $e) $vars['message'].=$e['Message'];
}
$vars['message'] = Utils::cleanJson($vars['message']);
$vars['success'] = ($out)? 1:0;