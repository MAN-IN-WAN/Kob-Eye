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
    $uc = Sys::getOneData('Systeme','User/'.$v->userCreate);
    $ue = Sys::getOneData('Systeme','User/'.$v->userEdit);
    if (is_object($uc))
        $v->userCreateName = $uc->Login;
    else $v->userCreateName = 'inconnu';
    if (is_object($ue))
        $v->userEditName = $ue->Login;
    else $v->userEditName = 'inconnu';
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
