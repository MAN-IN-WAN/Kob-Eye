<?php
$page = $_GET['page']?$_GET['page']:1;
$nbdoms = Sys::getCount('Parc','Domain');
$nbPage = ceil($nbdoms/100);
$vars['nbPage'] = $nbPage;

$doms = Sys::getData('Parc','Domain',100*($page-1),100*$page,'','','','',true);
foreach ($doms as &$dom) {
    $as = $dom->getChilds('Subdomain');
    $cli = $dom->getOneParent('Client');
    $dom->cli = $cli;
    $ips = [];
    foreach ($as as $a){
        $ips[$a->Url] = $a->IP;
    }
    $dom->ips = $ips;
}
usort($doms,function($a,$b){
    if($a->cli->Nom > $b->cli->Nom) return 1;
    if($a->cli->Nom < $b->cli->Nom) return -1;
    if($a->cli->Nom == $b->cli->Nom) {
        if($a->Url > $b->Url) return 1;
        if($a->Url < $b->Url) return -1;
    }
    return 0;
});
$vars['doms'] = $doms;
//print_r($doms);