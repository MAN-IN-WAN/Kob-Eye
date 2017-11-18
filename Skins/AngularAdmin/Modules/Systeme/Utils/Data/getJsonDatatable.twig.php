<?php
$info = Info::getInfos($vars['Path']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['fields'] = $o->getElementsByAttribute('list|fiche','',true);
//calcul offset / limit
$offset = (isset($_GET['offset']))?$_GET['offset']:0;
$limit = (isset($_GET['limit']))?$_GET['limit']:30;
$filters = (isset($_GET['filters']))?$_GET['filters']:'';
$path = explode('/',$vars['Path'],2);
$path = $path[1];
$vars['rows'] = Sys::getData($info['Module'],$path.'/'.$filters,$offset,$limit);
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
    if ($v->getSecondSearchOrder())
        $v->description = $v->getSecondSearchOrder();
    foreach ($vars['fields'] as $f){
        switch ($f['type']){
            case 'date':
                //transformation des timestamps en format js
                $v->{$f['name']} = date(DATE_W3C,$v->{$f['name']});
                break;
            case 'text':
            case 'varchar':
            case 'titre':
            case 'html':
            case 'raw':
                //transformation des timestamps en format js
                $v->{$f['name']} = Utils::cleanJson($v->{$f['name']});
                break;
        }
        if (isset($f['Values'])&&isset($f['Values'][$v->{$f['name']}])){
            $v->{$f['name'].'Label'} = $f['Values'][$v->{$f['name']}];
        }else if (isset($f['query'])&&$v->{$f['name']}>0){
            //recherche de sa valeur
            $str = explode('::',$f['query']);
            $qry = explode('/',$str[0],2);
            $val = Sys::getOneData($qry[0],$qry[1].'/'.$v->{$f['name']});
            $v->{$f['name'].'Label'} = $val->getFirstSearchOrder();
        }else $v->{$f['name'].'Label'} = '';
        if ($f['type']=='fkey'&&$f['card']=='short'){
            if ($v->{$f['name']} > 0) {
                $kk = Sys::getOneData($f['objectModule'], $f['objectName'] . '/' . $v->{$f['name']});
                if ($kk)
                    $v->{$f['name'].'label'} = $kk->getFirstSearchOrder();
            }else{
                $v->{$f['name'].'label'} = '';
            }
        }

    }
}
$vars['total'] = Sys::getCount($info['Module'],$vars['Path'].'/'.$filters);
?>