<?php
//décoration
$bc = new BashColors();


$instance = Sys::getData('Parc','Instance');

foreach($instance as $is){

    $host = $is->getOneParent('Host');
    $is->Password = $host->Password;
    $is->Save();

    echo $bc->getColoredString($is->Nom."> OK \n", 'green');
}




