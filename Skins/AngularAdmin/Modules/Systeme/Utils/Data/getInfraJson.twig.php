<?php

$proxies = Sys::getData('Parc','Server/Proxy=1');
$apaches = Sys::getData('Parc','Server/Web=1');
$sqls = Sys::getData('Parc','Server/Sql=1');

$proxList =array();
foreach($proxies as $proxy){
    array_push($proxList,array(
        'name'=>$proxy->Nom,
        'type'=>'serveur',
        'subType'=>'Proxy',
        'colors'=>array(0x99ff99),
        'status'=>$proxy->Status,
        'ip'=>$proxy->InternalIP,
        'id'=>$proxy->Id));
}

$apaList =array();
foreach($apaches as $apache){
    array_push($apaList,array(
        'name'=>$apache->Nom,
        'type'=>'serveur',
        'subType'=>'Apache',
        'colors'=>array(0x99ff99),
        'status'=>$apache->Status,
        'ip'=>$apache->InternalIP,
        'id'=>$apache->Id));
}

$sqlList =array();
foreach($sqls as $sql){
    array_push($sqlList,array(
        'name'=>$sql->Nom,
        'type'=>'serveur',
        'subType'=>'Sql',
        'colors'=>array(0x99ff99),
        'status'=>$sql->Status,
        'ip'=>$sql->InternalIP,
        'id'=>$sql->Id));
}
$devices = array_merge($apaList,$sqlList,$proxList, array(
    array(
        'name'=>'Apaches',
        'type'=>'cluster',
        'devices'=>$apaList,
        'networks'=> array(),
        'id'=>'clu_Apa'
    ),
    array(
        'name'=>'Sql',
        'type'=>'cluster',
        'devices'=>$sqlList,
        'networks'=> array(),
        'id'=>'clu_Sql'
    ),
    array(
        'name'=>'Proxies',
        'type'=>'cluster',
        'devices'=>$proxList,
        'networks'=> array(),
        'id'=>'clu_Pro'
    )

));


$vars['config']= array('devices'=>$devices);

$vars['config'] = json_encode($vars['config']);