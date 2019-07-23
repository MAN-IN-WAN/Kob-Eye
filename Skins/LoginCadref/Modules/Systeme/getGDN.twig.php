<?php
session_write_close();

if(connection_aborted()){
    endPacket();
    exit;
}

$a = genericClass::createInstance('Cadref', 'Adherent');
$vars['data'] = json_encode($a->GetGDN($_GET), 1);
?>