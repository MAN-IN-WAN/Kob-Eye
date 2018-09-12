<?php
$doms = Sys::getData('Parc','Domain/4',0,10000);
foreach ($doms as $dom){
    echo "***".$dom->Url."***\r\n";
    $subs = Sys::getData('Parc','Domain/'.$dom->Id.'/Subdomain',0,100000);
    $exists = array();
    foreach ($subs as $sub){
        if ($sub->IP=='145.239.103.217')
            $sub->IP = '	5.196.207.219';
        if (!in_array($sub->Nom,$exists)) {
            echo "->check ".$sub->Nom."\r\n";
            array_push($exists, $sub->Nom);
        }else{
            echo "->suppression ".$sub->Nom."\r\n";
            $sub->Delete();
        }
        $sub->Save();
    }
}
