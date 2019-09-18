<?php
$user = Sys::$User;
$alerts =  Sys::getData('Systeme','AlertUser/Read=0',0,10000);

foreach($alerts as $a){
    $a->Read = 1;
    $a->Save();
}
echo json_encode(array('Success'=>true,'Message'=>count($alerts).' alerte(s) traitée(s) avec succès.'));
