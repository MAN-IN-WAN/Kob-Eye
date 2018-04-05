<?php
$info= Info::getInfos($vars['Query']);
$obj = PhotoSession::getCurrent();
$out = $obj->setMask();
$vars['message'] = '';
if ($out)
    $vars['message'] = 'Le masque a été appliqué avec succès';
else{
    foreach ($obj->Error as $e) $vars['message'].=$e['Message'];
}
$vars['message'] = Utils::cleanJson($vars['message']);
$vars['success'] = ($out)? 1:0;