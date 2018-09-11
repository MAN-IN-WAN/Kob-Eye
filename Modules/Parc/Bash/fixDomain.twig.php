<?php
$doms = Sys::getData('Parc','Domain',0,10000);
foreach ($doms as $dom){
    echo "***".$dom->Url."***\r\n";
    $subs = Sys::getData('Parc','Domain/'.$dom->Id.'/Subdomain',0,100000);
    $exists = array();
    foreach ($subs as $sub){
        /*if (!in_array($sub->Nom,$exists)) {
            echo "->check ".$sub->Nom."\r\n";
            array_push($exists, $sub->Nom);
        }else{
            echo "->suppression ".$sub->Nom."\r\n";
            $sub->Delete();
        }*/
        $sub->Save();
    }
}
