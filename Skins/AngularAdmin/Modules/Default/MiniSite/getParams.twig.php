<?php

$info = Info::getInfos($vars['Query']);
$ret = array( "common" =>array(),"pages"=>array());

$ms = Sys::getOneData($info['Module'], $info['Query']);
$modele = $ms->getOneParent('ModeleMiniSite');
$params = $ms->getParamsValues();

foreach($params as &$pa){
    $pa->obj = 'param_'.$pa->Id;
}

$ret['common'] = $params;

$pages = $modele->getChildren('PageMiniSite');

foreach($pages as &$p){
    $pparams = $p->getParamsValues($ms->Id);
    foreach ($pparams as &$ppa){
        $ppa->obj = 'param_'.$ppa->Id;
    }
    $p->msParams = $pparams;

}

$ret['pages'] =  $pages;

echo json_encode($ret);