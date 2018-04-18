<?php
$out = LightBox::restartBrowser();
$vars['message'] = '';
if ($out)
    $vars['message'] = 'Redemarrage du navigateur' ;
else{
    foreach ($obj->Error as $e) $vars['message'].=$e['Message'];
}
$vars['message'] = Utils::cleanJson($vars['message']);
$vars['success'] = ($out)? 1:0;