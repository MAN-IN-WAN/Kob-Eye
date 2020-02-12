<?php
session_write_close();

if(connection_aborted()){
    endPacket();
    exit;
}

$args = (array)json_decode(file_get_contents('php://input'));
$vars['data'] = json_encode(Show::GetShow($args), 1);
?>