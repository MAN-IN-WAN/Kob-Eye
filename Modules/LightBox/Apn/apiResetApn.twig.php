<?php
$info= Info::getInfos($vars['Query']);
$obj = Apn::getCurrent();
$out = $obj->reset();
$vars['message'] = '';
if ($out)
    $vars['message'] = 'L\'appareil photo a été réinitialisé avec succès' ;
else{
    foreach ($obj->Error as $e) $vars['message'].=$e['Message'];
}
$vars['message'] = Utils::cleanJson($vars['message']);
$vars['success'] = ($out)? 1:0;