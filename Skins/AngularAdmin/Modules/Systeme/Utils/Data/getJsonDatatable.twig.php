<?php
session_write_close();
$info = Info::getInfos($vars['Path']);
$o = genericClass::createInstance($info['Module'],$info['ObjectType']);
$vars['fields'] = $o->getElementsByAttribute('list|fiche','',true);
//calcul offset / limit
$offset = (isset($_GET['offset']))?$_GET['offset']:0;
$limit = (isset($_GET['limit']))?$_GET['limit']:30;
$filters = (isset($_GET['filters']))?$_GET['filters']:'';
$context = (isset($_GET['context']))?$_GET['context']:'default';
$path = explode('/',$vars['Path'],2);
$path = $path[1];
//souscription au push
Event::registerPush($info['Module'],$info['ObjectType'],$path,$filters,$offset,$limit,$context);
//requete
if(connection_aborted()){
    endPacket();
    exit;
}

$vars['rows'] = Sys::getData($info['Module'],$path.'/'.$filters,$offset,$limit);
if(connection_aborted()){
    endPacket();
    exit;
}
$interfaces = $o->getInterfaces();
$children = array();
foreach ($interfaces as $i){
    foreach ($i as $form) {
        if (isset($form['child'])) {
            array_push($children, $form['child']);
        }
    }
}

foreach ($vars['rows'] as $k=>$v){
    $uc = Sys::getOneData('Systeme','User/'.$v->userCreate);
    $ue = Sys::getOneData('Systeme','User/'.$v->userEdit);
    if (is_object($uc))
        $v->userCreateName = $uc->Login;
    else $v->userCreateName = 'inconnu';
    if (is_object($ue))
        $v->userEditName = $ue->Login;
    else $v->userEditName = 'inconnu';
    $v->label = Utils::cleanJson($v->getFirstSearchOrder());
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
    //cas widget
    if (sizeof($children)){
        foreach ($children as $c)
            $v->{$c} = array_reverse($v->getChildren($c));
    }
    //recursivity
    if ($o->isRecursiv()){
        $v->isTail = ($v->isTail()) ? '1':'0';
    }
}
if ($o->isRecursiv()) {
    $vars['recursiv'] = true;
}
if (sizeof($children)){
    foreach ($children as $c)
        array_push($vars['fields'],array('type'=>'children','name'=>$c));
}

$vars['total'] = Sys::getCount($info['Module'],$vars['Path'].'/'.$filters);


function endPacket(){
    echo "0\r\n\r\n";
    ob_flush();
    flush();
}
?>