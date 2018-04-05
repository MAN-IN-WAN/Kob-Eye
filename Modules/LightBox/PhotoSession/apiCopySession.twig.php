<?php
$info= Info::getInfos($vars['Query']);
$obj = PhotoSession::getCurrent();
$out = $obj->copySession();
$vars['message'] = '';
if ($out)
    $vars['message'] = 'Les fichiers ont été copiés avec succès';
else{
    foreach ($obj->Error as $e) $vars['message'].=$e['Message'];
}
$vars['message'] = Utils::cleanJson($vars['message']);
$vars['success'] = ($out)? 1:0;