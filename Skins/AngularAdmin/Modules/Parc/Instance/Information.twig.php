<?php
$query = $vars['Query'];
//recupération de l'instance
$inst = Sys::getOneData('Parc',$query);
$vars['instance'] = $inst;
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
