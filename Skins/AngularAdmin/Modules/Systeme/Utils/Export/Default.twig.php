<?php
$info = Info::getInfos($vars['Path']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['fields'] = $o->getElementsByAttribute('list','',true);
//calcul offset / limit
$offset = (isset($_GET['offset']))?$_GET['offset']:0;
$limit = (isset($_GET['limit']))?$_GET['limit']:30;
$filters = (isset($_GET['filters']))?$_GET['filters']:'';

$vars['rows'] = Sys::getData($info['Module'],$vars['Path'].'/'.$filters,0,10000);
foreach ($vars['rows'] as $k=>$v){
    $v->userCreateName = 'inconnu';
    $v->userEditName = 'inconnu';
    $v->label = $v->getFirstSearchOrder();
    foreach ($vars['fields'] as $f){
        if ($f['type']=='date'){
            //transformation des timestamps en format js
            $v->{$f['name']} = date(DATE_W3C,$v->{$f['name']});
        }
    }
}
$vars['total'] = Sys::getCount($info['Module'],$vars['Path'].'/'.$filters);
$vars['lb'] = "\r\n";
