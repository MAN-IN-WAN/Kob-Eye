<?php
$inst = Sys::getData('Parc','Instance',0,1000);
foreach ($inst as $i){
    echo $i->Id.' '.$i->Nom."\n";
    $i->checkState();
}