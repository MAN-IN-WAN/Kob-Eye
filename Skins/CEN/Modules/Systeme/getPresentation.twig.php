<?php
session_write_close();
//$info = Info::getInfos($vars['Query']);
//$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
////calcul offset / limit
//$filters = (isset($_GET['filters']))?$_GET['filters']:'';
//$path = explode('/',$vars['Path'],2);
//$path = $path[1];


//requete
if(connection_aborted()){
    endPacket();
    exit;
}
$vars['data'] = json_encode(CEN::GetPresentation($_GET), 1);
?>