<?php
$info= Info::getInfos($vars['Query']);
$obj = Sys::getOneData($info['Module'],$vars['Query']);
if ($obj->Proxy||$obj->Web||$obj->Dns)
    $out = $obj->callLdap2Service();
else{
    $out = false;
    $out->addError(array("Message"=>"Ce serveur n'est pas un serveur Proxy/Web/Dns."));
}
$vars['obj'] = $obj;
$vars['success'] = $out;