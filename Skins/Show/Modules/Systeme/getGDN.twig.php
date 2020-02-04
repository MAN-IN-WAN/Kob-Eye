<?php
session_write_close();

if(connection_aborted()){
    endPacket();
    exit;
}

$vars['data'] = json_encode(CEN::GetGDN($_GET), 1);
?>