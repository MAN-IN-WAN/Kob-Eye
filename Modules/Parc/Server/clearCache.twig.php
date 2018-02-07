<?php
$info= Info::getInfos($vars['Query']);
$obj = Sys::getOneData($info['Module'],$vars['Query']);
if ($obj->Proxy)
    $out = $obj->clearCache();
else{
    $out = false;
    $out->addError(array("Message"=>"Ce serveur n'est pas un serveur Proxy."));
}
$vars['obj'] = $obj;
$vars['success'] = $out;