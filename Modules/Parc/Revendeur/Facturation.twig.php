<?php
//INIT
$vars['NbHost']=0;
$vars['NbHostExpert']=0;
$vars['NbHostHors']=0;
$vars['TotalHT']=0;
$vars['TotalHTBdd']=0;
$vars['TotalHTAp']=0;
$vars['TotalHTHd']=0;
//CONFIG
$vars['BddTarif']=3;
$vars['sizeTarif']=1;
$vars['VhostTarif']=0;
$vars['BaseTarif']=6;
$vars['ExpertTarif']=12;
$vars['HorsTarif']=24;
$vars['MaxBdd']=300;


$q = explode('/',$vars['Query'],2);
$revendeur = Sys::getOneData($q[0],$q[1]);
$clients = $revendeur->getChildren('Client');

foreach($clients as &$c){
    $hosts = $c->getChildren('Host');
//    $c->instances = 0;
    $cInstances = $c->getChildren('Instance');
    $c->cInstances = $cInstances;
    $domains = $c->getChildren('Domain');
    $c->domaines = $domains;
    $c->basic = 0;
    $c->business = 0;
    $c->expert = 0;
    $c->scVhost = 0;
    $c->siteProv = 0;
    $c->siteUtil = 0;
    $c->scSite = 0;
    $c->bddProv = 0;
    $c->bddUtil = 0;
    $c->scBdd = 0;

    foreach($hosts as  $key => &$h){
        $server = $h->getServer();
        $h->serv = $server;
        //check hébergement mutualisé
        if($server->Id == 129
        || $server->Id == 131
        || $server->Id == 130
        || $server->Id == 123
        ){

            $instance = $h->getOneParent('Instance');
            /*if($instance) {
                echo 'toto'.PHP_EOL;
                $c->instances++;
            }*/
            $h->instance = $instance;
            $apaches = $h->getChildren('Apache');
            $h->apaches = $apaches;
            $bdds = $h->getChildren('Bdd');
            $h->bdds = $bdds;

            $h->basic = false;
            $h->business = false;
            $h->expert = false;
            $h->siteProv = 0;
            $h->siteUtil = ceil($h->DiskSpace/1024);
            $c->siteUtil += ceil($h->DiskSpace/1024);
            $h->scSite = 0;
            $h->bddProv = 0;
            $h->bddUtil = 0;
            $h->scBdd = 0;


            //tarifs forfaits
            if($instance && $instance->Produit !='base'){
                if($instance->Produit == 'expert'){
                    $h->business = true;
                    $c->siteProv += 20*1024;
                    $h->siteProv = 20*1024;
                    $c->bddProv += 500;
                    $h->bddProv = 500;
                    $vars['TotalHT'] += $vars['ExpertTarif'];
                    $vars['NbHostExpert']++;
                    $c->business++;
                    $h->business = true;
                    if( round($h->DiskSpace) > (20*1024*1024) ){
                        $over = $h->DiskSpace - (20*1024*1024);
                        $overG = ceil($over/(1024*1024));
                        $sc = $vars['sizeTarif'] * $overG;
                        $h->scSite = $sc;
                        $c->scSite += $sc;
                        $vars['TotalHTHd'] += $sc;
                    }
                } else{
                    $c->siteProv += 30*1024;
                    $h->siteProv = 30*1024;
                    $c->bddProv += 600;
                    $h->bddProv = 600;
                    $vars['TotalHT'] += $vars['HorsTarif'];
                    $vars['NbHostHors']++;
                    $c->expert++;
                    $h->expert = true;
                    if( round($h->DiskSpace) > (30*1024*1024) ){
                        $over = $h->DiskSpace - (30*1024*1024);
                        $overG = ceil($over/(1024*1024));
                        $sc = $vars['sizeTarif'] * $overG;
                        $h->scSite = $sc;
                        $c->scSite += $sc;
                        $vars['TotalHTHd'] += $sc;
                    }
                }
            } else {
                $c->basic++;
                $c->siteProv += 5*1024;
                $h->siteProv = 5*1024;
                $c->bddProv += 200;
                $h->bddProv = 200;
                $vars['TotalHT'] += $vars['BaseTarif'];
                $vars['NbHost']++;
                $h->basic = true;
                if( round($h->DiskSpace) > (5*1024*1024) ){
                    $over = $h->DiskSpace - (5*1024*1024);
                    $overG = ceil($over/(1024*1024));
                    $sc = $vars['sizeTarif'] * $overG;
                    $h->scSite = $sc;
                    $c->scSite += $sc;
                    $vars['TotalHTHd'] += $sc;
                }
            }

            /*if($instance){
                if($instance->Status == 1) $h->dev = true;
                $h->plugin = $instance->Plugin;
                $h->disque =  round($instance->DiskSpace / 1024);
            } else {
                $h->disque =  round($h->DiskSpace / 1024);
            }*/


            //Tarif apaches
            $surcoutAp = (count($apaches) - 1) * $vars['VhostTarif'];
            $h->surcoutAp = $surcoutAp;
            if( $surcoutAp > 0 ){
                $vars['TotalHTAp'] += $surcoutAp;
                $c->scVhost += $surcoutAp;
            }


            //BDDS
            $totalBdd = 0;
            $surcoutBdd = 0;
            foreach($bdds as $b){
                $totalBdd += $b->Size;
            }
            if($totalBdd > ($h->bddProv * 1024)){
                $overbdd = ($totalBdd/1024) - $h->bddProv;
                $surcoutBdd = $vars['BddTarif'] * ( ceil($overbdd/$vars['MaxBdd'] )) ;
                $vars['TotalHTBdd'] += $surcoutBdd;
                $h->scBdd = $surcoutBdd;
                $c->scBdd += $surcoutBdd;
            }
            $h->bddUtil = ceil($totalBdd/1024);
            $c->bddUtil += ceil($totalBdd/1024);


            $h->domaines = array();
            foreach($apaches as $a){
                $dtcs = $a->getDomainsToCheck();
                if(!empty($dtcs)) {
                    $domaines = explode(' ', $dtcs);
                    $domaines = preg_replace('/\s+/', ' ', $domaines);
                    $h->domaines = array_merge($h->domaines, $domaines);
                }
            }
        } else {
            unset($hosts[$key]);
        }
    }
    $c->hosts = $hosts;
}
usort($clients,function($a,$b){
    if(strtolower($a->Nom) > strtolower($b->Nom)) return 1;
    if(strtolower($a->Nom) < strtolower($b->Nom)) return - 1;
    return 0;
});
$vars['clients'] = $clients;
