<?php
$out = LightBox::emptyCache();
if ($out)
    $vars['message'] = 'Suppression du cache OK' ;
else{
    foreach ($obj->Error as $e) $vars['message'].=$e['Message'];
}
$vars['message'] = Utils::cleanJson($vars['message']);
$vars['success'] = ($out)? 1:0;