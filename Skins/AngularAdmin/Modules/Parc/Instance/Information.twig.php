<?php
$query = $vars['Query'];
//recupération de l'instance
$inst = Sys::getOneData('Parc',$query);
$vars['instance'] = $inst;
$pref='';
$infra = $inst->getInfra();
if($infra){
    $pref= 'Infra/'.$infra->Id.'/';
}
$mysqlsrv = Sys::getOneData('Parc', $pref.'Server/Sql=1&defaultSqlServer=1', null, null, null, null, null, null, true);
$vars['sql'] = $mysqlsrv;

//récupération des hosts
$hosts = $inst->getChildren('Host');
foreach ($hosts as $k=>$host){
    $host->apacheServer = Server::getServer($host->getMasterServer());
    $host->bdds = $host->getChildren('Bdd');
    $host->ftps = $host->getChildren('Ftpuser');
    $hosts[$k] = $host;
}
$vars['hosts'] = $hosts;
$vars['domains'] = $inst->getChildren('InstanceDomain');
