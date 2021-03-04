<?php

$Devices = Sys::getData('Parc','Device/tmsCreate>1589793000');
print_r(count($Devices));
echo PHP_EOL;
foreach ($Devices as $d){
    $ds = Sys::getData('Parc','Device/Uuid='.$d->Uuid);
    if(count($ds)>1){
        echo $d->Uuid .' --- '. $d->Nom .' --- '. $d->Id.PHP_EOL;
        $d->Delete();
    }
}