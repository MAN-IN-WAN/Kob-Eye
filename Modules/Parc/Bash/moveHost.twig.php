<?php
//$hosts = Sys::getData('Parc','Server/7/Host',0,1);
$hosts = Sys::getData('Parc','Host/600',0,1);
foreach ($hosts as $host) {
    echo "-> ".$host->Id." ".$host->Nom." ... \n";
    $host->resetParents('Server');
    $host->addParent('Parc/Server/8');
    $host->Save();
    $aps = $host->getChildren('Apache');
    foreach ($aps as $ap){
        echo "  -> ".$ap->ApacheServerName." ".$ap->ApacheServerAlias." ... \n";
        $ap->Save();
    }
}
sleep(5);
foreach ($hosts as $host) {
    echo "-> ".$host->Id." ".$host->Nom." ... \n";
    $host->Save();
    $aps = $host->getChildren('Apache');
    foreach ($aps as $ap){
        echo "  -> ".$ap->ApacheServerName." ".$ap->ApacheServerAlias." ... \n";
        $ap->Save();
    }
}
sleep(5);
foreach ($hosts as $host) {
    echo "-> ".$host->Id." ".$host->Nom." ... \n";
    $host->Save();
    $aps = $host->getChildren('Apache');
    foreach ($aps as $ap){
        echo "  -> ".$ap->ApacheServerName." ".$ap->ApacheServerAlias." ... \n";
        $ap->Save();
    }
    $inst = $host->getOneChild('Instance');
    $inst->createInstallTask();
    echo "OK\n";
}