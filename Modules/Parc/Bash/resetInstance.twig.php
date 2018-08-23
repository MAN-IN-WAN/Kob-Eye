<?php
//décoration
$bc = new BashColors();

//suppression de toutes les instances
$insts = Sys::getData('Parc','Instance');
foreach ($insts as $inst){
    echo $bc->getColoredString("-> DELETING ".$inst->Nom. "\n", 'red');
    $inst->Delete();
}