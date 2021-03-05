<?php
$Minisite = Sys::getOneData("Parc", "MiniSite/Domaine=".Sys::$domain);
$params = $Minisite->getParamsValues();
foreach($params as $param){
    $vars[$param->Nom] = $param->vms;
}