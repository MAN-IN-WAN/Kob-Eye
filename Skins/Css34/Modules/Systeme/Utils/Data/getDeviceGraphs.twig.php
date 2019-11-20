<?php
require_once ROOT_DIR."Class/Lib/Zabbix.class.php";

$query = isset($vars['Path']) ? $vars['Path']: $vars['Query'];
$info = Info::getInfos($query);
if ($info["TypeSearch"]=="Direct"){
    //alors modification
    $o = Sys::getOneData($info['Module'],$query);
}

if(!isset($o) || $o)
    return false;
//UUid dans le parc
$uuid = $o->Uuid;
//Recup du host zabbix grace à l'uuid
$devZab = Zabbix::getHostFromUuid($uuid);
if (!$devZab)
    return false;
$templates =  $devZab->parentTemplates;

//Recup des items grace au templates liés à l'host
$items = array();
foreach($templates as $template){
    switch ($template->templateid){
        case 11523: //Template OS Windows active Poste
            $search =array(
                'Free disk space',
                'Processor load (15 min average)'
            );
            $tempItems = Zabbix::getHostItems($devZab,$search);
            $items = array_merge($items,$tempItems);

            break;
        case 11431: //Template OS Windows active Serveur
            $search =array(
                'Free disk space',
                'Processor load'
            );
            $tempItems = Zabbix::getHostItems($devZab,$search);
            $items = array_merge($items,$tempItems);

            break;
        default:
            continue;
    }
}

//On assigne le tout à twig
$vars['total'] = count($items);
$vars['graphs'] = array();
foreach ( $items as $item ){
    array_push($vars['graphs'], array('label'=>$item->name,'delay'=>$item->delay,'id'=>$item->itemid));
}
