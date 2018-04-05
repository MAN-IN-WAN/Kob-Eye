<?php
$info= Info::getInfos($vars['Query']);
$obj = PhotoSession::getCurrent();
$out = $obj->terminate();
$vars['message'] = '';
if ($out)
    $vars['message'] = 'La session a été terminée avec succès';
else{
    foreach ($obj->Error as $e) $vars['message'].=$e['Message'];
}
$vars['message'] = Utils::cleanJson($vars['message']);
$vars['success'] = ($out)? 1:0;